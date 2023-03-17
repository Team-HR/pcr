<?php
session_start();
require_once "assets/libs/config_class.php";
require_once "assets/libs/rsm_class.php";
require_once "assets/libs/irm_class.php";


if (isset($_SESSION['emp_id'])) {
	$user = new Employee_data();
	$year = new year();
	$step = new step();
	$user->set_emp($_SESSION['emp_id']);
	if ($_SESSION['emp_id'] == "") {
		session_destroy();
		header("location:?");
	} elseif (!isset($_GET['config'])) {
		require_once "assets/pages/header.php";
		if (isset($_GET['logout'])) {
			require_once "assets/pages/logout.php";
		} else if (isset($_GET['performanceRating'])) {
			if (!isset($_GET['form']) && !isset($_GET['error'])) {
				require_once "assets/pages/performanceRating/Index.php";
			} else if (!isset($_GET['form']) && isset($_GET['error'])) {
				require_once "assets/pages/performanceRating/matrixError.php";
			} else {
				require_once "assets/pages/performanceRating/form.php";
			}
		} else if (isset($_GET['home'])) {
			require_once "assets/pages/home.php";
		} else if (isset($_GET['RatingScale'])) {
			if (!isset($_GET['Error']) && !isset($_GET['View'])) {
				require_once "assets/pages/iMatrix/Index.php";
			} else if (isset($_GET['View']) && !isset($_GET['Error'])) {
				require_once "assets/pages/iMatrix/iMatrix.php";
			} else {
				require_once "assets/pages/iMatrix/iMatrixError.php";
			}
		} else if (isset($_GET['MotherRatingScale'])) {
			if (!isset($_GET['Edit'])) {
				// period and year selector
				require_once "assets/pages/RSM/rsm.php";
			} else {
				// the rating scale matrix editing page
				require_once "assets/pages/RSM/rsm_editor.php";
			}
		}
		// else if(isset($_GET['supportFuntion'])){
		// 	require_once "assets/pages/suppFunc/suppFunc.php";
		// }

		else if (isset($_GET['RPC'])) {
			if (isset($_GET['subordinates']) && !isset($_GET['view'])) {
				# 2. list of subs
				require_once "assets/pages/review/subordinates.php";
			} else if (isset($_GET['subordinates']) && isset($_GET['view'])) {
				# 3. formview editor for sup/dh
				require_once "assets/pages/review/view.php";
			} else {
				# 1. index
				require_once "assets/pages/review/review.php";
			}
		} else if (isset($_GET['Browse'])) {
			require_once "assets/pages/browse/browse.php";
		} else if (isset($_GET['PMT'])) {
			require_once "assets/pages/PMT/pmt.php";
		} else if (isset($_GET['showRsmView'])) {
			// require_once "assets/libs/rsm_class.php";
			require_once "assets/pages/rsmPMTview/content.php";
		} else if (isset($_GET['HR'])) {
			// require_once "assets/libs/rsm_class.php";
			if (isset($_GET['FinalNumericalRatings'])) {
				require_once "assets/pages/HR/finalNumericalRatings.php";
			} else {
				require_once "assets/pages/HR/index.php";
			}
		} else if (false) {
		} else {
			echo "<h2 style='text-align:center'>Page NOT found go to <a href='?home'><u>Homepage</u></a></h2>";
		}
		require_once "assets/pages/footer.php";
	} elseif (isset($_GET['config'])) {
		$filePath = $_GET['config'];
		if ($filePath == "rsm") {
			// require_once "assets/libs/rsm_class.php";
			require_once "assets/pages/RSM/config.php";
		} elseif ($filePath == "RSMmodalCont") {
			require_once "assets/pages/RSM/modalCont.php";
		} elseif ($filePath == "prContent") {
			require_once "assets/pages/performanceRating/content.php";
		} elseif ($filePath == "reassign") {
			require_once "assets/pages/performanceRating/reassign.php";
		} elseif ($filePath == "getAgencyForm") {
			require_once "assets/pages/performanceRating/getAgencyForm.php";
		} elseif ($filePath == "prModalL") {
			require_once "assets/pages/performanceRating/modal.php";
		} elseif ($filePath == "suggestions") {
			require_once "assets/pages/RSM/find.php";
		}
		// elseif ($filePath=="supportFunction") {
		// 	require_once "assets/pages/suppFunc/config.php";
		// }
		elseif ($filePath == "rsmPMTview") {
			require_once "assets/pages/rsmPMTview/config.php";
		} elseif ($filePath == "revContent") {
			require_once "assets/pages/review/content.php";
		} elseif ($filePath == "iMatrixConfig") {
			require_once "assets/pages/iMatrix/config.php";
		} elseif ($filePath == "BrowseConfig") {
			require_once "assets/pages/browse/config.php";
		} elseif ($filePath == "PMT") {
			require_once "assets/pages/PMT/config.php";
		} else {
			echo notFound();
			die();
		}
	}
} else {
	require_once "assets/pages/login/login.php";
}
function notFound()
{
	return "<center><h1 style='color:#888888de'>Page Not Found</h1></center>";
}
