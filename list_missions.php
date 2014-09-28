<?php
myconnect();
mysql_select_db("skynet");
$missionssql="SELECT mission_name_short,mission_name,mn.id,pn.name_short FROM {$_SESSION['table_prefix']}mission_names mn LEFT JOIN {$_SESSION['table_prefix']}platoon_names pn ON pn.id=mn.platoon_id ORDER BY date DESC,mission_name_short DESC";
//echo $missionssql;
//echo "<br><br>";
$missionsres=mysql_query($missionssql);
while ($mission=mysql_fetch_array($missionsres)) { ?>
<a href="index.php?url=show_mission.php&id=<?php echo $mission['id'];?>"><?php echo $mission['name_short'];?>: <?php echo $mission['mission_name_short'];?></a> <?php echo $mission['mission_name'];?><br><br>
<?php } ?>
