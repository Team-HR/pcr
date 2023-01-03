<?php
require_once "../../libs/config_class.php";
$empData = new Employee_data();
$empData->set_emp($_POST['personalData']);
$fullname = $empData->get_emp('firstName')." ".$empData->get_emp('middleName')." ".$empData->get_emp('lastName')." ".$empData->get_emp('extName');
?>
<div class="ui segments">
  <div class="ui segment">
    <a class="ui blue ribbon label">Personal Information</a>
    <h2 class="ui center aligned icon header">
      <i class="circular user icon"></i>
      My Profile
    </h2>
  </div>
  <div class="ui horizontal segments">
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Fullname</h4>
        <center class="description"><?=$fullname?></center>
      </div>
    </div>
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Gender</h4>
        <center class="ui center aligned description"><?=$empData->get_emp('gender')?></center>
      </div>
    </div>
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Employment Status</h4>
        <center class="ui center aligned description"><?=$empData->get_emp('employmentStatus')?></center>
      </div>
    </div>
  </div>
  <div class="ui horizontal segments">
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Department</h4>
        <center class="description"><?=$empData->get_emp('department')?></center>
      </div>
    </div>
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Position</h4>
        <center class="ui center aligned description"><?=$empData->get_emp('position')?></center>
      </div>
    </div>
  </div>
  <div class="ui segment">
    <div class="content">
      <h4 class="ui header">Function</h4>
      <center class="ui center aligned description"><?=$empData->get_emp('functional')?></center>
    </div>
  </div>
</div>
<?php
  $sql = "SELECT * FROM `spms_accounts` where employees_id='$_POST[personalData]'";
  $sql = $mysqli->query($sql);
  $sql = $sql->fetch_assoc();
?>
<div class="ui segments">
  <div class="ui segment">
    <a class="ui green ribbon label">Account Information</a>
    <h2 class="ui center aligned icon header">
      <i class="circular cogs icon"></i>
      Account Settings
    </h2>
  </div>
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Username</h4>
        <center class="description" id='oldUsername' ><?=$sql['username']?></center>
      </div>
    </div>
    <div class="ui segment">
      <div class="content">
        <h4 class="ui header">Change Username</h4>
          <form class="" method="post" data-target='mgsUsernameChange' data-id='<?=$_POST['personalData']?>' onsubmit="changeUsernameForm()">
            <div class="ui form" style="width:50%;margin:auto">
              <div class="grouped fields">
                <div class="field">
                  <label>New Username</label>
                  <input type="text" name='newUsername' placeholder="Enter new Username">
                </div>
                <div class="field">
                  <label>Provide password to confirm your identity</label>
                  <input type="password" name='password' placeholder="Enter Current Password">
                </div>
                <div class="field">
                  <input type="submit" class="ui blue button" value="Change">
                </div>
              </div>
              <label id='mgsUsernameChange'></label>
            </div>
          </form>
      </div>
    </div>
  <div class="ui segment">
    <div class="content">
      <h4 class="ui header">User Privileges</h4>
      <center class="description">
        <?=$sql['type']?>
      </center>
    </div>
  </div>
  <div class="ui segment">
    <a class="ui red ribbon label">Change Password</a>
    <div class="ui segment">
      <form class="" method="post" data-id='<?=$_POST['personalData']?>' onsubmit="changePasswordForm()">
        <div class="ui form" style="width:50%;margin:auto">
          <div class="grouped fields">
            <div class="field">
              <label>Current Password</label>
              <input type="password" id="oldPass" required>
            </div>
            <div class="field">
              <label>New Password</label>
              <input type="password" id='newPassword' msgId='newPasswordValidMsg' data-target='retypePass' onkeyup="passwordCheckValid()">
              <label id='newPasswordValidMsg'></label>
            </div>
            <div class="field">
              <label>Re-type new Password</label>
              <input type="password" id="retypePass" msgId='retypePasswordValidMsg' data-target='newPassword' onkeyup="checkMatchPass()" disabled>
              <label id='retypePasswordValidMsg'></label>
            </div>
            <div class="field">
              <input type="submit" id='submitNewPass' class="ui blue button" disabled>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
