<?php

/**
 * Duplicates SPMS Core Functions and Matrix Indicators.
 *
 * @param PDO $pdo The active database connection.
 * @param int $sourcePeriodId The mfo_periodId to copy from.
 * @param int $sourceDepId The dep_id to copy from (Source Department).
 * @param int $targetPeriodId The mfo_periodId to copy to.
 * @param int|null $targetDepId New dep_id. If null, defaults to using the $sourceDepId.
 * @param string|null $filterInCharge User ID to filter MIs. If null, copies all MIs.
 * @param int|null $specificCfId Root cf_ID to copy. If null, copies all CFs in the source period/dept.
 * @return array Summary of actions (counts of inserted rows).
 */
function duplicateSpmsData(
    PDO $pdo, 
    $sourcePeriodId, 
    $sourceDepId,       // <--- Added Source Dep ID
    $targetPeriodId, 
    $targetDepId = null, 
    $filterInCharge = null, 
    $specificCfId = null
) {
    try {
        $pdo->beginTransaction();

        // 1. Fetch Core Functions filtering by BOTH Period AND Source Department
        $sql = "SELECT * FROM spms_pcr_mfos 
                WHERE mfo_periodId = :pid 
                AND dep_id = :sdep"; // Filter by Source Dep
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pid'  => $sourcePeriodId,
            ':sdep' => $sourceDepId
        ]);
        $allCfs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($allCfs)) {
            // No data found for this period and department
            $pdo->rollBack();
            return ['status' => 'error', 'message' => 'No Core Functions found for this Period and Department.'];
        }

        // 2. Build Tree and Index
        $cfById = [];
        $childrenMap = [];
        
        foreach ($allCfs as $row) {
            $id = $row['cf_ID'];
            $parentId = $row['parent_id'];
            
            $cfById[$id] = $row;
            
            if (!isset($childrenMap[$parentId])) {
                $childrenMap[$parentId] = [];
            }
            $childrenMap[$parentId][] = $id;
        }

        // 3. Determine Starting Points (Roots)
        $rootsToProcess = [];

        if ($specificCfId !== null) {
            // Specific Root requested
            if (isset($cfById[$specificCfId])) {
                $rootsToProcess[] = $specificCfId;
            } else {
                // If specific ID exists but belongs to a different department, it won't be in $cfById
                throw new Exception("Source cf_ID $specificCfId not found in Period $sourcePeriodId and Dept $sourceDepId");
            }
        } else {
            // Find roots: Items where parent is NULL or parent is NOT in the fetched result set
            // (The latter handles cases where a sub-unit might be linked to a parent from a different dept/period, strictly keeping the copy within the source dep scope)
            foreach ($allCfs as $row) {
                $pid = $row['parent_id'];
                if (empty($pid) || !isset($cfById[$pid])) {
                    $rootsToProcess[] = $row['cf_ID'];
                }
            }
        }

        // Map: Old_ID => New_ID
        $idMap = [];
        $insertedCfCount = 0;
        $insertedMiCount = 0;

        // 4. Recursive Processing Function
        $processNode = function($currentId, $newParentId) use (
            &$processNode, &$idMap, &$insertedCfCount, &$insertedMiCount, 
            $pdo, $cfById, $childrenMap, $targetPeriodId, $targetDepId, $sourceDepId, $filterInCharge
        ) {
            $rowData = $cfById[$currentId];

            // Determine which Department ID to save
            // If targetDepId is provided, use it; otherwise use the original sourceDepId
            $finalDepId = $targetDepId ?? $sourceDepId;

            // A. Insert Core Function
            $insertCfSql = "INSERT INTO spms_pcr_mfos 
                (mfo_periodId, parent_id, dep_id, cf_count, cf_title, corrections) 
                VALUES (:mfo, :pid, :dep, :cnt, :title, :cor)";
            
            $stmtCf = $pdo->prepare($insertCfSql);
            $stmtCf->execute([
                ':mfo'   => $targetPeriodId,
                ':pid'   => $newParentId ?? '', 
                ':dep'   => $finalDepId, 
                ':cnt'   => $rowData['cf_count'],
                ':title' => $rowData['cf_title'],
                ':cor'   => $rowData['corrections']
            ]);

            $newCfId = $pdo->lastInsertId();
            $idMap[$currentId] = $newCfId;
            $insertedCfCount++;

            // B. Duplicate Matrix Indicators
            $miSql = "SELECT * FROM spms_pcr_indicators WHERE cf_ID = :old_cf_id";
            $stmtMiGet = $pdo->prepare($miSql);
            $stmtMiGet->execute([':old_cf_id' => $currentId]);
            $indicators = $stmtMiGet->fetchAll(PDO::FETCH_ASSOC);

            foreach ($indicators as $mi) {
                // Filter In-Charge
                if ($filterInCharge !== null) {
                    $stmtFilter = $pdo->prepare(
                        "SELECT id FROM pms_ipcr_si_assignments
                         WHERE success_indicator_id = :si_id AND user_id = :uid LIMIT 1"
                    );
                    $stmtFilter->execute([':si_id' => $mi['mi_id'], ':uid' => $filterInCharge]);
                    if (!$stmtFilter->fetch()) {
                        continue;
                    }
                }

                $insertMiSql = "INSERT INTO spms_pcr_indicators 
                    (cf_ID, mi_succIn, corrections) 
                    VALUES (:new_cf, :succ, :cor)";
                
                $stmtMiIns = $pdo->prepare($insertMiSql);
                $stmtMiIns->execute([
                    ':new_cf' => $newCfId,
                    ':succ'   => $mi['mi_succIn'],
                    ':cor'    => $mi['corrections']
                ]);
                $newMiId = $pdo->lastInsertId();
                $stmtIncharge = $pdo->prepare(
                    "SELECT user_id FROM pms_ipcr_si_assignments WHERE success_indicator_id = :src"
                );
                $stmtIncharge->execute([':src' => $mi['mi_id']]);
                $inChargeArr = array_column($stmtIncharge->fetchAll(PDO::FETCH_ASSOC), 'user_id');
                foreach ($inChargeArr as $empId) {
                    if (!is_numeric($empId)) continue;
                    $stmtAssign = $pdo->prepare(
                        "INSERT INTO pms_ipcr_si_assignments
                         (success_indicator_id, user_id, period_id, assigned_by, created_at, updated_at)
                         VALUES (:si_id, :uid, :pid, 9, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())"
                    );
                    $stmtAssign->execute([
                        ':si_id' => $newMiId,
                        ':uid'   => $empId,
                        ':pid'   => $targetPeriodId,
                    ]);
                }
                $stmtQet = $pdo->prepare(
                    "SELECT measure_type, score, descriptor FROM pms_si_qet_descriptors
                     WHERE success_indicator_id = :src"
                );
                $stmtQet->execute([':src' => $mi['mi_id']]);
                $stmtQetIns = $pdo->prepare(
                    "INSERT IGNORE INTO pms_si_qet_descriptors
                     (success_indicator_id, measure_type, score, descriptor, created_at, updated_at)
                     VALUES (:si_id, :mtype, :score, :desc, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())"
                );
                foreach ($stmtQet->fetchAll(PDO::FETCH_ASSOC) as $qrow) {
                    $stmtQetIns->execute([
                        ':si_id' => $newMiId,
                        ':mtype' => $qrow['measure_type'],
                        ':score' => $qrow['score'],
                        ':desc'  => $qrow['descriptor'],
                    ]);
                }
                $insertedMiCount++;
            }

            // C. Recurse Children
            if (isset($childrenMap[$currentId])) {
                foreach ($childrenMap[$currentId] as $childId) {
                    $processNode($childId, $newCfId);
                }
            }
        };

        // 5. Execute
        foreach ($rootsToProcess as $rootId) {
            $processNode($rootId, '');
        }

        $pdo->commit();
        return [
            'status' => 'success',
            'cf_inserted' => $insertedCfCount,
            'mi_inserted' => $insertedMiCount
        ];

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
?>