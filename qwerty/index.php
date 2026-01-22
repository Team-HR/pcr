<?php
require_once("../assets/libs/session_init.php");
session_start();

require_once("assets/libs/class/Class.php");
require_once("assets/libs/class/rsmClass.php");
require_once "assets/libs/NameFormatter.php";

$_ipcr = new IPCR();
$mysqli = $_ipcr->getMysqli();

if (isset($_SESSION['admin'])) {
  if (isset($_GET['config'])) {
    // configuration files

    $file = $_GET['config'];
    if ($file == "SupFunc") {
      require_once("assets/pages/SupFunc/config.php");
    } elseif ($file == "PMT") {
      require_once("assets/pages/PMT/config.php");
    } elseif ($file == "Users") {
      require_once("assets/pages/Users/config.php");
    } elseif ($file == "rsmStatus") {
      require_once("assets/pages/rsmStatus/config.php");
    } elseif ($file == "createUser") {
      require_once("assets/pages/createUser/config.php");
    } elseif ($file == "getSupportFunction") {
      require_once("assets/pages/SupFunc/config.php");
    } elseif ($file == "pcrForms") {
      require_once("assets/pages/PcrForms/config.php");
    }

    // ###########

  } elseif (isset($_GET['modal'])) {
    // modal views
    $modal = $_GET['modal'];
    if ($modal == "SupFunc") {
      require_once("assets/pages/SupFunc/modal.php");
    }
    // ##############
  } else {

    // main page 
    require_once("assets/pages/mainComponents/header.php");
    require_once("assets/pages/mainComponents/navigation.php");
    if (isset($_GET['PMT'])) {
      require_once("assets/pages/PMT/content.php");
    } elseif (isset($_GET['SupFunc'])) {
      require_once("assets/pages/SupFunc/content.php");
    } elseif (isset($_GET['Users'])) {
      require_once("assets/pages/Users/content.php");
    } elseif (isset($_GET['Logout'])) {
      session_destroy();
?>

      <script>
        window.location.reload()
      </script>

<?php
      // header("Refresh:0");
      // header("location:?");
    } elseif (isset($_GET['Records'])) {
      $records = $_GET['Records'];
      if ($records != "") {
        require_once("assets/pages/Records/departmentFiles.php");
      } else {
        require_once("assets/pages/Records/content.php");
      }
    } elseif (isset($_GET['RSMStatus'])) {
      require_once("assets/pages/rsmStatus/content.php");
    } elseif (isset($_GET['createUser'])) {
      require_once("assets/pages/createUser/content.php");
    } elseif (isset($_GET['viewRsm'])) {
      require_once("assets/pages/rsmStatus/viewRsm.php");
    }

    ###################
    elseif (isset($_GET['pcrForms'])) {
      require_once("assets/pages/PcrForms/content.php");
    }
    ###################

    else {
      require_once("assets/pages/Home/home.php");
    }
    require_once("assets/pages/mainComponents/footer.php");

    // #######################
  }
} else {
  require_once("assets/pages/login/wall.php");
  // echo "https://www.youtube.com/watch?v=pxxY7obcbuc";
}
