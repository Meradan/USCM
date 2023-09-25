<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
if ($user->isAdmin() || $user->isGm()) {
?>
  <h1 class="heading heading-h1">Create mission</h1>

<form class="form" method="post" action="mission.php?what=create_mission">
<input type="hidden" name="platoon_id" value="<?php echo $user->getPlatoonId(); ?>">

  <label for="mission">
    Mission
    <input type="text" id="mission" name="mission">
  </label>

  <label for="name">
    Name
    <input type="text" id="name" name="name">
  </label>

  <label for="date">
    Date
    <input type="text" id="date" name="date">
  </label>

  <label for="briefing">
    Briefing
    <textarea id="briefing" name="briefing" rows="20"></textarea>
  </label>

  <label for="debriefing">
    Debriefing
    <textarea id="debriefing" name="debriefing" rows="20"></textarea>
  </label>

  <input class="button" type="submit" value="Create Mission">
</form>
<?php }
else {
include("not_allowed.php");
}?>
