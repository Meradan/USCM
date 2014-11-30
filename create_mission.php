<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
if ($user->isAdmin() || $user->isGm()) {
?>
<br><br>
<form method="post" action="mission.php?what=create_mission">
<table width="50%"  border="0">
  <tr>
    <td>Mission</td>
    <td><input type="text" name="mission"></td>
  </tr>
  <tr>
    <td>Name</td>
    <td><input type="text" name="name"></td>
  </tr>
  <tr>
    <td>Date</td>
    <td><input type="text" name="date"></td>
  </tr>
  <tr>
    <td>Briefing</td>
    <td><textarea name="briefing" cols="80" rows="40"></textarea></td>
  </tr>
  <tr>
    <td>Debriefing</td>
    <td><textarea name="debriefing" cols="80" rows="40"></textarea></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" value="Create mission"></td>
  </tr>
</table>
</form>
<?php }
else {
include("not_allowed.php");
}?>
