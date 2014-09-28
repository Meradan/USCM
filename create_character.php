<?php $admin=($_SESSION['level']==3)?(TRUE):(FALSE);
$gm=($_SESSION['level']==2)?(TRUE):(FALSE);
if ($admin || $gm) {
myconnect(); 
mysql_select_db("skynet");
$whereplayer=($admin)?(""):("WHERE platoon_id='{$_SESSION['platoon_id']}'");
$whereplatoon=($admin)?(""):("WHERE id='{$_SESSION['platoon_id']}'");
?>
<form method="post" action="character.php?action=create_character">
<table width="50%"  border="0">
		<tr>
				<td>Player</td>
				<td><?php $playersql="SELECT Users.id,forname,lastname,name_short FROM Users
									LEFT JOIN {$_SESSION['table_prefix']}platoon_names pn ON pn.id=Users.platoon_id {$whereplayer}
									ORDER BY platoon_id,lastname,forname";
						$playerres=mysql_query($playersql);?>
					<select name="player">
					<?php while ($player=mysql_fetch_array($playerres)) { ?>
						<option value="<?php echo $player['id'];?>"><?php echo $player['name_short'] . ": " . $player['forname'] . " " . $player['lastname']; ?></option>
					<?php } ?>
						<option value="0">Non Player Character</option>
					</select>
				</td>
		</tr>
		<tr>
				<td>Platoon</td>
				<td><?php $platoonsql="SELECT id,name_long FROM {$_SESSION['table_prefix']}platoon_names {$whereplatoon}";
						$platoonres=mysql_query($platoonsql);?>
					<select name="platoon">
					<?php while ($platoon=mysql_fetch_array($platoonres)) { ?>
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
				<td><?php $specialtysql="SELECT id, specialty_name FROM {$_SESSION['table_prefix']}specialty_names ORDER BY specialty_name";
						$specialtyres=mysql_query($specialtysql);?>
					<select name="specialty">
					<?php while ($specialty=mysql_fetch_array($specialtyres)) { ?>
						<option value="<?php echo $specialty['id'];?>"><?php echo $specialty['specialty_name']; ?></option>
					<?php } ?>
					</select></td>
		</tr>
		<tr>
        <td>Rank</td>
        <td><?php $ranksql="SELECT id, rank_long FROM {$_SESSION['table_prefix']}rank_names";
                 $rankres=mysql_query($ranksql);?>
        	<select name="rank">
          <?php while ($rank=mysql_fetch_array($rankres)) { ?>
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
		$attributesql="SELECT id, attribute_name FROM {$_SESSION['table_prefix']}attribute_names";
		$attributeres=mysql_query($attributesql);
		while ($attribute=mysql_fetch_array($attributeres)) { ?>
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
        	<td><input type="text" name="ap" value="<?php echo $character['awarenesspoints'];?>"></td>
        	<td>Cool Points</td>
        	<td><input type="text" name="cp" value="<?php echo $character['coolpoints'];?>"></td>
				</tr>
				<tr>
					<td>Exhaustion Points</td>
        	<td><input type="text" name="ep" value="<?php echo $character['exhaustionpoints'];?>"></td>
        	<td>Fear Points</td>
        	<td><input type="text" name="fp" value="<?php echo $character['fearpoints'];?>"></td>
				</tr>
				<tr>
					<td>Leadership Points</td>
        	<td><input type="text" name="lp" value="<?php echo $character['leadershippoints'];?>"></td>
        	<td>Psycho Points</td>
        	<td><input type="text" name="pp" value="<?php echo $character['psychopoints'];?>"></td>
				</tr>
				<tr>
					<td>Trauma Points</td>
        	<td><input type="text" name="tp" value="<?php echo $character['traumapoints'];?>"></td>
        	<td>Mental Points</td>
        	<td><input type="text" name="mp" value="<?php echo $character['mentalpoints'];?>"></td>
				</tr>
		
		<?php //Ta ut alla skills
		$skillsql="SELECT id, skill_name,optional FROM {$_SESSION['table_prefix']}skill_names";
		$skillres=mysql_query($skillsql);
		while ($skill=mysql_fetch_array($skillres)) { ?>
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
