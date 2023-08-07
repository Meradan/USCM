<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
if ($user->isAdmin() || $user->isGm()) {
?>
  <h1 class="heading heading-h1">Create mission</h1>

<form method="post" action="mission.php?what=create_mission">
<input type="hidden" name="platoon_id" value="<?php echo $user->getPlatoonId(); ?>">
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
