<?php
$currentPlayer = new Player();
$characterController = new CharacterController();
$platoonController = new PlatoonController();
$playerController = new PlayerController();
$rankController = new RankController();
$userController = new UserController();
$user = $userController->getCurrentUser();

if ($user->isAdmin() || $user->isGm()) { ?>
  <h1 class="heading heading-h1">Create character</h1>

<form class="form" method="post" action="character.php?action=create_character">
  <div class="grid grid--1x2">
    <label for="player">
      Player
      <?php
      $players = $playerController->getActivePlayers();
      ?>
      <select id="player" name="player">
        <?php foreach ($players as $player) { ?>
          <option value="<?php echo $player->getId();?>"><?php echo $player->getNameWithNickname(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="platoon">
      Platoon
      <?php
      $platoons = $platoonController->getPlatoons(); ?>
      <select id="platoon" name="platoon">
        <?php foreach ($platoons as $platoon) { ?>
          <option value="<?php echo $platoon->getId();?>" <?php echo ($platoon->getId()==$_SESSION['platoon_id'])?("selected"):("");?> ><?php echo $platoon->getName(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="forname">
      Firstname
      <input type="text" id="forname" name="forname">
    </label>

    <label for="lastname">
      Lastname
      <input type="text" id="lastname" name="lastname">
    </label>

    <label for="specialty">
      Specialty
      <select id="specialty" name="specialty">
        <?php $specialties = $characterController->getSpecialties(); ?>
        <?php foreach ($specialties as $specialty) { ?>
          <option value="<?php echo $specialty->getId();?>"><?php echo $specialty->getName(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="rank">
      Rank
      <select id="rank" name="rank">
        <?php $ranks = $rankController->getRanks(); ?>
        <?php foreach ($ranks as $rank) { ?>
          <option <?php echo ($rank->getId()=="1")?("selected"):("");?> value="<?php echo $rank->getId();?>" >
            <?php echo $rank->getName(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="enlisted">
      Enlisted (format: YYYYMMDD)
      <input type="text" id="enlisted" name="enlisted">
    </label>

    <label for="age">
      Age
      <input type="number" id="age" name="age">
    </label>

    <label for="gender">
      Gender
      <select id="gender" name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </label>
  </div>

  <fieldset class="form--inline grid grid--small">
    <legend>Attributes</legend>
    <?php //Ta ut alla attribut
    $attributes = $characterController->getAttributes();
    foreach ($attributes as $attribute) { ?>
      <label for="attribute_<?php echo $attribute->getId();?>">
        <?php echo $attribute->getName();?>
        <input type="number" id="attribute_<?php echo $attribute->getId();?>" name="attribute[<?php echo $attribute->getId();?>]" min="0" max="10">
      </label>
    <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Points</legend>

    <label for="ap">
      Awareness Points
      <input type="number" id="ap" name="ap" value="0" min="0">
    </label>

    <label for="cp">
      Cool Points
      <input type="number" id="cp" name="cp" value="0" min="0">
    </label>

    <label for="ep">
      Exhaustion Points
      <input type="number" id="ep" name="ep" value="0" min="0">
    </label>

    <label for="fp">
      Fear Points
      <input type="number" id="fp" name="fp" value="0" min="0">
    </label>

    <label for="lp">
      Leadership Points
      <input type="number" id="lp" name="lp" value="0" min="0">
    </label>

    <label for="pp">
      Psycho Points
      <input type="number" id="pp" name="pp" value="0" min="0">
    </label>

    <label for="tp">
      Trauma Points
      <input type="number" id="tp" name="tp" value="0" min="0">
    </label>

    <label for="mp">
      Mental Points
      <input type="number" id="mp" name="mp" value="0" min="0">
    </label>

    <label for="xp">
      Unused XP
      <input type="number" id="xp" name="xp" min="0">
    </label>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Skills</legend>

    <?php //Ta ut alla skills
    $skills = $characterController->getSkills();
    foreach ($skills as $skill) { ?>
      <label for="skill_<?php echo $skill->getId();?>">
        <?php echo $skill->getName();?>
        <input type="number" id="skill_<?php echo $skill->getId();?>" name="skill[<?php echo $skill->getId();?>]" min="0" max="10">
      </label>

      <input type="hidden" name="optional[<?php echo $skill->getId();?>]" value="<?php echo $skill->getOptional();?>">
    <?php } ?>
  </fieldset>

  <input class="button" type="submit" value="Create Character">
</form>
<?php }
else {
  include("components/403.php");
}?>
