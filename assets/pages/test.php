<button class="ui button green" onclick="copy_to()">Copy RSM (part of rsm) to other Department</button>
<!-- 

problem:
sir alvaran wants their rsm separated from mayor's office 
to avoid issues of editing conflicts

solution:
change department of sir alvaran and constituents and
transfer the IASS part of the mayor's office rsm to a 
the IASS department

Steps:
1. change departments of sir alvaran of iass
2. change configuration of id numbers from ...elseif (isset($_POST['copy_to'])) {... 
    inside assets/pages/RSM/config.php
3. delete the rsm of the target department if not empty
4. press the button from this file

recommendation:
 make a solution on how to also automatically update the accomplished pcrs' 
 data to the newly transfered rsm

-->