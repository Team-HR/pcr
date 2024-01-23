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
			$sql = "DELETE FROM `spms_corefunctions` WHERE `cf_ID` = '$cf_ID'";
			$this->mysqli->query($sql);
			# delete success indicators
			// call function destroy_si($mi_id)
			foreach ($mfo['success_indicators'] as $success_indicator) {
				$mi_id = $success_indicator['mi_id'];
				$sql = "DELETE FROM `spms_matrixindicators` WHERE `mi_id` = '$mi_id'";
				$this->mysqli->query($sql);
				# delete mfo data
				// call function destroy_cfd($cfd_id)
				foreach ($success_indicator['cfd_ids'] as $cfd_id) {
					$sql = "DELETE FROM `spms_corefucndata` WHERE `cfd_id` = '$cfd_id'";
					$this->mysqli->query($sql);
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
		$sql = "SELECT * from spms_corefunctions where parent_id='' and mfo_periodId='$period_id' and dep_id='$department_id' ORDER BY `spms_corefunctions`.`cf_count` ASC ";
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
		$sql = "SELECT * from spms_corefunctions where parent_id='$parent_id' ORDER BY `spms_corefunctions`.`cf_count` ASC ";
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
		$sql = "SELECT * from spms_matrixindicators where cf_ID='$parent_id'";
		$res = $this->mysqli->query($sql);
		while ($row = $res->fetch_assoc()) {

			$data[] = [
				"mi_id" => $row["mi_id"],
				"cfd_ids" => $this->get_spms_corefucndata($row["mi_id"])
			];
		}
		return $data;
	}

	private function get_spms_corefucndata($p_id)
	{
		$data = [];
		$sql = "SELECT * FROM `spms_corefucndata` WHERE `p_id` = '$p_id'";
		$res = $this->mysqli->query($sql);

		while ($row = $res->fetch_assoc()) {
			$data[] = $row['cfd_id'];
		}

		return $data;
	}
}
