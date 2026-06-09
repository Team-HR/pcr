<?php

class RatingScaleMatrixDestroyer
{
	private $mysqli;
	private $period_id;
	private $department_id;
	private $mfos;

	public function __construct($mysqli)
	{
		$this->mysqli = $mysqli;
	}


	public function set_period_id($period_id)
	{
		$this->period_id = $period_id;
	}

	public function set_department_id($department_id)
	{
		$this->department_id = $department_id;
	}

	public function delete_rating_scale_matrix()
	{
		$data = [];
		$data = $this->get_mfos();

		foreach ($data as $mfo) {
			# delete mfo
			$cf_ID = $mfo['cf_ID'];
			// call function destroy_mfo($cf_ID)
			$stmt = $this->mysqli->prepare("DELETE FROM spms_pcr_mfos WHERE cf_ID = ?");
			$stmt->bind_param("i", $cf_ID);
			$stmt->execute();
			$stmt->close();
			# delete success indicators
			// call function destroy_si($mi_id)
			foreach ($mfo['success_indicators'] as $success_indicator) {
				$mi_id = $success_indicator['mi_id'];
				$stmt = $this->mysqli->prepare("DELETE FROM spms_pcr_indicators WHERE mi_id = ?");
				$stmt->bind_param("i", $mi_id);
				$stmt->execute();
				$stmt->close();
				$stmt = $this->mysqli->prepare("DELETE FROM spms_pcr_si_assignments WHERE success_indicator_id = ?");
				$stmt->bind_param("i", $mi_id);
				$stmt->execute();
				$stmt->close();
				$stmt = $this->mysqli->prepare("DELETE FROM spms_pcr_si_qet_descriptors WHERE success_indicator_id = ?");
				$stmt->bind_param("i", $mi_id);
				$stmt->execute();
				$stmt->close();
				# delete mfo data
				// call function destroy_cfd($cfd_id)
				foreach ($success_indicator['cfd_ids'] as $cfd_id) {
					$stmt = $this->mysqli->prepare("DELETE FROM spms_pcr_indicator_accomplishments WHERE cfd_id = ?");
					$stmt->bind_param("i", $cfd_id);
					$stmt->execute();
					$stmt->close();
				}
			}
		}

		return $data;
	}


	private function get_mfos()
	{
		$period_id = $this->period_id;
		$department_id = $this->department_id;
		$data = [];
		# first get all mfos with no parent (this will be the top-most parent of the rsm, where department_id and period_id)
		$sql = "SELECT * from spms_pcr_mfos where parent_id='' and mfo_periodId='$period_id' and dep_id='$department_id' ORDER BY spms_pcr_mfos.cf_count ASC ";
		$res = $this->mysqli->query($sql);
		while ($row = $res->fetch_assoc()) {
			$id = $row['cf_ID'];
			// $row["children"] = $this->get_children($data, $id);
			$row["success_indicators"] = $this->get_success_indicators($id);
			$data[] = [
				"cf_ID" => $row["cf_ID"],
				"success_indicators" => $row["success_indicators"]
			];
			$this->get_children($data, $id);
		}

		return $data;
	}

	private function get_children(&$data, $parent_id)
	{
		$sql = "SELECT * from spms_pcr_mfos where parent_id='$parent_id' ORDER BY spms_pcr_mfos.cf_count ASC ";
		$res = $this->mysqli->query($sql);
		while ($row = $res->fetch_assoc()) {
			$id = $row['cf_ID'];
			$row["success_indicators"] = $this->get_success_indicators($id);
			$data[] = [
				"cf_ID" => $row["cf_ID"],
				"success_indicators" => $row["success_indicators"]
			];
			$this->get_children($data, $id);
		}
		return $data;
	}
	private function get_success_indicators($parent_id)
	{
		$data = [];
		$sql = "SELECT * from spms_pcr_indicators where cf_ID='$parent_id'";
		$res = $this->mysqli->query($sql);
		while ($row = $res->fetch_assoc()) {

			$data[] = [
				"mi_id" => $row["mi_id"],
				"cfd_ids" => $this->get_spms_pcr_indicator_accomplishments($row["mi_id"])
			];
		}
		return $data;
	}

	private function get_spms_pcr_indicator_accomplishments($p_id)
	{
		$data = [];
		$sql = "SELECT * FROM spms_pcr_indicator_accomplishments WHERE p_id = '$p_id'";
		$res = $this->mysqli->query($sql);

		while ($row = $res->fetch_assoc()) {
			$data[] = $row['cfd_id'];
		}

		return $data;
	}
}
