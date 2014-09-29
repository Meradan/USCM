<?php
$currentPlayer = new Player();
$admin = $currentPlayer->isAdmin();
$gm= $currentPlayer->isGm();

if ($admin || $gm) { ?>
<form method="post" action="character.php?action=create_character">
<table width="50%"  border="0">
    <tr>
        <td>Player</td>
        <td><?php
              $players = $currentPlayer->getAllPlayers();
            ?>
          <select name="player">
          <?php foreach ($players as $player) { ?>
            <option value="<?php echo $player['id'];?>"><?php echo $player['name_short'] . ": " . $player['forname'] . " " . $player['lastname']; ?></option>
          <?php } ?>
            <option value="0">Non Player Character</option>
          </select>
        </td>
    </tr>
    <tr>
        <td>Platoon</td>
        <td><?php
            $platoons = $currentPlayer->getPlatoons(); ?>
          <select name="platoon">
          <?php foreach ($platoons as $platoon) { ?>
            <option value="<?php echo $platoon['id'];?>" <?php echo ($platoon['id']==$_SESSION['platoon_id'])?("selected"):("");?> ><?php echo $platoon['name_long']; ?></option>
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
            $specialties = getSpecialties(); ?>
          <select name="specialty">
          <?php foreach ($specialties as $specialty) { ?>
            <option value="<?php echo $specialty['id'];?>"><?php echo $specialty['specialty_name']; ?></option>
          <?php } ?>
          </select></td>
    </tr>
    <tr>
        <td>Rank</td>
        <td><?php
            $ranks = getRanks(); ?>
          <select name="rank">
          <?php foreach ($ranks as $rank) { ?>
            <option <?php echo ($rank['id']=="1")?("selected"):("");?> value="<?php echo $rank['id'];?>" >
             <?php echo $rank['rank_long']; ?></option>
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
    $attributes = getAttributes();
    foreach ($attributes as $attribute) { ?>
    <tr>
        <td><?php echo $attribute['attribute_name'];?></td>
        <td><input type="text" name="attribute[<?php echo $attribute['id'];?>]"></td>
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
    $skills = getSkills();
    foreach ($skills as $skill) { ?>
    <tr>
        <td><?php echo $skill['skill_name'];?></td>
        <td><input type="text" name="skill[<?php echo $skill['id'];?>]">
          <input type="hidden" name="optional[<?php echo $skill['id'];?>]" value="<?php echo $skill['optional'];?>">
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
