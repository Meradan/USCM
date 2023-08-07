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

<form method="post" action="character.php?action=create_character">
<table width="50%"  border="0">
    <tr>
        <td>Player</td>
        <td><?php
              $players = $playerController->getActivePlayers();
            ?>
          <select name="player">
          <?php foreach ($players as $player) { ?>
            <option value="<?php echo $player->getId();?>"><?php echo $player->getNameWithNickname(); ?></option>
          <?php } ?>
          </select>
        </td>
    </tr>
    <tr>
        <td>Platoon</td>
        <td><?php
            $platoons = $platoonController->getPlatoons(); ?>
          <select name="platoon">
          <?php foreach ($platoons as $platoon) { ?>
            <option value="<?php echo $platoon->getId();?>" <?php echo ($platoon->getId()==$_SESSION['platoon_id'])?("selected"):("");?> ><?php echo $platoon->getName(); ?></option>
          <?php } ?>
          </select>
        </td>
    </tr>
    <tr>
        <td>Forname</td>
        <td><input type="text" name="forname"></td>
    </tr>
    <tr>
        <td>Lastname</td>
        <td><input type="text" name="lastname"></td>
    </tr>
    <tr>
        <td>Specialty</td>
        <td><?php
            $specialties = $characterController->getSpecialties(); ?>
          <select name="specialty">
          <?php foreach ($specialties as $specialty) { ?>
            <option value="<?php echo $specialty->getId();?>"><?php echo $specialty->getName(); ?></option>
          <?php } ?>
          </select></td>
    </tr>
    <tr>
        <td>Rank</td>
        <td><?php
            $ranks = $rankController->getRanks(); ?>
          <select name="rank">
          <?php foreach ($ranks as $rank) { ?>
            <option <?php echo ($rank->getId()=="1")?("selected"):("");?> value="<?php echo $rank->getId();?>" >
             <?php echo $rank->getName(); ?></option>
            <?php } ?>
          </select></td>
    </tr>
    <tr>
        <td>Enlisted</td>
        <td><input type="text" name="enlisted">  format: YYYYMMDD</td>
    </tr>
    <tr>
        <td>Age</td>
        <td><input type="text" name="age"></td>
    </tr>
    <tr>
        <td>Gender</td>
        <td>
          <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </td>
    </tr>
    <?php //Ta ut alla attribut
    $attributes = $characterController->getAttributes();
    foreach ($attributes as $attribute) { ?>
    <tr>
        <td><?php echo $attribute->getName();?></td>
        <td><input type="text" name="attribute[<?php echo $attribute->getId();?>]"></td>
    </tr>
    <?php } ?>
    <tr>
        <td>Unused XP</td>
        <td><input type="text" name="xp"></td>
    </tr>
        <tr>
          <td>Awareness Points</td>
          <td><input type="text" name="ap" value="0"></td>
          <td>Cool Points</td>
          <td><input type="text" name="cp" value="0"></td>
        </tr>
        <tr>
          <td>Exhaustion Points</td>
          <td><input type="text" name="ep" value="0"></td>
          <td>Fear Points</td>
          <td><input type="text" name="fp" value="0"></td>
        </tr>
        <tr>
          <td>Leadership Points</td>
          <td><input type="text" name="lp" value="0"></td>
          <td>Psycho Points</td>
          <td><input type="text" name="pp" value="0"></td>
        </tr>
        <tr>
          <td>Trauma Points</td>
          <td><input type="text" name="tp" value="0"></td>
          <td>Mental Points</td>
          <td><input type="text" name="mp" value="0"></td>
        </tr>

    <?php //Ta ut alla skills
    $skills = $characterController->getSkills();
    foreach ($skills as $skill) { ?>
    <tr>
        <td><?php echo $skill->getName();?></td>
        <td><input type="text" name="skill[<?php echo $skill->getId();?>]">
          <input type="hidden" name="optional[<?php echo $skill->getId();?>]" value="<?php echo $skill->getOptional();?>">
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="2"><input type="submit" value="Submit"></td>
    </tr>
</table>
</form>
<?php }
else {
include("not_allowed.php");
}?>
