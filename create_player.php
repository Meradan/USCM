<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
$platoonController = new PlatoonController();
if ($user->isAdmin()) {
?>
  <h1 class="heading heading-h1">Create player</h1>

<form class="form" method="post" action="player.php?what=create">
  <label for="forname">
    Firstname
    <input type="text" id="forname" name="forname">
  </label>

  <label for="lastname">
    Lastname
    <input type="text" id="lastname" name="lastname">
  </label>

  <label for="nickname">
    Nickname
    <input type="text" id="nickname" name="nickname">
  </label>

  <fieldset>
    <legend>Use nickname</legend>
    <label for="use_nickname">
      <input type="radio" id="use_nickname_1" name="use_nickname" value="1"> Yes
    </label>
    <label for="use_nickname">
      <input type="radio" id="use_nickname_0" name="use_nickname" value="0"> No
    </label>
  </fieldset>

  <label for="emailadress">
    Email
    <input type="email" id="emailadress" name="emailadress">
  </label>

  <label for="password">
    Password
    <input type="password" id="password" name="password">
  </label>

  <label for="platoon_id">
    Platoon
    <?php
                $platoons = $platoonController->getPlatoons();
    ?>
        <select id="platoon_id" name="platoon_id">
    <?php foreach ($platoons as $platoon) { ?>
          <option value="<?php echo $platoon->getId(); ?>"><?php echo $platoon->getName(); ?></option>
    <?php } ?>
        </select>
  </label>

  <input class="button" type="submit" value="Create Player">
</form>
<?php }
else {
include("not_allowed.php");
}?>
