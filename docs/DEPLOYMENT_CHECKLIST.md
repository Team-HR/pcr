# Production Deployment Checklist

## Phase 1 Data Layer — Complete Changes

### All Changes Made

#### 1. Normalize mi_incharge → spms_pcr_si_assignments
- Created `spms_pcr_si_assignments` table
- Migrated incharge data from `spms_pcr_indicators.mi_incharge`
- Script: `tools/migrate_si_assignments.php`

#### 2. Normalize mi_quality, mi_eff, mi_time → spms_pcr_si_qet_descriptors
- Created `spms_pcr_si_qet_descriptors` table
- Migrated QET data from `spms_pcr_indicators` columns
- Script: `tools/migrate_si_qet_descriptors.php`

#### 3. Drop old QET columns
- Dropped `mi_quality`, `mi_eff`, `mi_time` from `spms_pcr_indicators`
- Script: `tools/drop_si_qet_columns.php`

#### 4. Drop mi_incharge column
- Dropped `mi_incharge` from `spms_pcr_indicators`
- Script: `tools/drop_mi_incharge_column.php`

#### 5. Normalize corrections
- Swapped serialize/unserialize → json_encode/json_decode
- Migrated 2,368 rows from PHP serialize to JSON
- Script: `tools/migrate_corrections_to_json.php`

#### 6. Standardize table naming
- Renamed `pms_ipcr_si_assignments` → `spms_pcr_si_assignments`
- Renamed `pms_si_qet_descriptors` → `spms_pcr_si_qet_descriptors`
- Updated 12 files with ~44 table references
- Script: `tools/rename_tables_to_spms_pcr_prefix.php`

#### 7. Add foreign key constraints
- Added 7 FK constraints to PCR tables
- Cleaned up 40,756 orphaned records
- Fixed 5 column type mismatches
- Scripts: `tools/cleanup_orphaned_data.php`, `tools/fix_schema_and_cleanup.php`, `tools/cleanup_remaining_orphans.php`, `tools/add_foreign_key_constraints.php`

---

## Deployment Steps

### Pre-Deployment
- [ ] **Backup production database** — full dump before any changes
- [ ] **Review code changes** — ensure no development-only configs
- [ ] **Test migration scripts on staging** — run all scripts in staging first
- [ ] **Notify users** — schedule maintenance window (system unavailable during migration)

### Code Deployment
- [ ] **Deploy code changes** — pull latest code to production
- [ ] **Verify file integrity** — check all migration scripts exist
- [ ] **Verify database connection** — test `_connect.db.php` configuration

### Migration Execution (Run in Order)

#### Step 1: Normalize mi_incharge
- [ ] Run `tools/migrate_si_assignments.php`
  - Creates `spms_pcr_si_assignments` table
  - Migrates incharge data

#### Step 2: Normalize mi_quality, mi_eff, mi_time
- [ ] Run `tools/migrate_si_qet_descriptors.php`
  - Creates `spms_pcr_si_qet_descriptors` table
  - Migrates QET data

#### Step 3: Drop old QET columns
- [ ] Run `tools/drop_si_qet_columns.php`
  - Drops `mi_quality`, `mi_eff`, `mi_time` from `spms_pcr_indicators`

#### Step 4: Drop mi_incharge column
- [ ] Run `tools/drop_mi_incharge_column.php`
  - Drops `mi_incharge` from `spms_pcr_indicators`

#### Step 5: Normalize corrections
- [ ] Run `tools/migrate_corrections_to_json.php`
  - Migrates serialize → JSON format (~2,368 rows)

#### Step 6: Standardize table naming
- [ ] Run `tools/rename_tables_to_spms_pcr_prefix.php`
  - Renames both tables to `spms_pcr_*` prefix

#### Step 7: Initial orphan cleanup
- [ ] **SKIPPED** — Run `tools/cleanup_orphaned_data.php`
  - Deletes initial orphaned records (~5,517)
  - **Needs future review before deployment**

#### Step 8: Fix schema and cleanup
- [ ] **SKIPPED** — Run `tools/fix_schema_and_cleanup.php`
  - Fixes column type mismatches (5 columns)
  - Deletes additional orphaned records (~35,226)
  - **Needs future review before deployment**

#### Step 9: Final cleanup
- [ ] **SKIPPED** — Run `tools/cleanup_remaining_orphans.php`
  - Deletes final orphaned records (run until 0 deleted)
  - **Needs future review before deployment**

#### Step 10: Add FK constraints
- [ ] **SKIPPED** — Run `tools/add_foreign_key_constraints.php`
  - Adds 7 foreign key constraints
  - **Needs future review before deployment**

### Verification
- [ ] **Run diagnostics** — `tools/analyze_orphaned_data.php`
  - Verify 0 orphaned records in all tables
  - Verify 7 PCR FK constraints listed
- [ ] **Test application functionality**
  - Test PCR form creation
  - Test indicator assignments
  - Test QET descriptors
  - Test MFO hierarchy
  - Test corrections JSON format
- [ ] **Check application logs** — monitor for FK constraint violations

### Post-Deployment
- [ ] **Update REDESIGN_ROADMAP.md** — mark production deployment complete
- [ ] **Document any issues** — note any production-specific issues encountered
- [ ] **Archive migration scripts** — move to `tools/archive/` after successful deployment

---

## Rollback Plan

If deployment fails:

1. **mi_incharge migration failure** — drop `spms_pcr_si_assignments` table, restore code
2. **QET migration failure** — drop `spms_pcr_si_qet_descriptors` table, restore code
3. **Column drop failure** — columns permanently removed; restore from backup
4. **Corrections migration failure** — restore from backup immediately
5. **Table rename failure** — run reverse RENAME commands, restore code
6. **Cleanup failure** — no rollback (deletion permanent); investigate and continue
7. **FK constraint failure** — `ALTER TABLE table DROP FOREIGN KEY fk_name`; fix and retry
8. **Full rollback** — restore DB backup, revert code to previous commit

---

## Critical Notes

- **Order is critical** — scripts must run in exact order listed
- **No undo for deletions** — column drops and data deletion are permanent
- **Backup required** — full database backup mandatory before starting
- **Downtime** — system unavailable during migration (approx 15-20 minutes)
- **Staging test** — test all scripts in staging before production

---

## Contact Information
- Developer: [Your Name]
- Database Admin: [DBA Contact]
- Deployment Window: [Scheduled Time]
