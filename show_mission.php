<?php 
$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']>=2)?(TRUE):(FALSE);
myconnect();
mysql_select_db("skynet");
$missionssql="SELECT mission_name_short,mission_name,forname,lastname,date,{$_SESSION['table_prefix']}mission_names.id,briefing,debriefing 
					FROM {$_SESSION['table_prefix']}mission_names 
					LEFT JOIN Users on Users.id=gm
					WHERE {$_SESSION['table_prefix']}mission_names.id='{$_GET['id']}'";
$missionres=mysql_query($missionssql);
$mission=mysql_fetch_array($missionres);
?>

<table width="100%"  border="0" cellpadding="5">
		<tr>
				<td class="colorfont">Mission</td>
				<td colspan="3"><?php echo $mission['mission_name_short'];?></td>
				<td rowspan="2"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=names" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
		<tr>
				<td class="colorfont">Name</td>
				<td colspan="3"><?php echo $mission['mission_name'];?></td>
		</tr>
		<tr>
				<td class="colorfont">Date</td>
				<td colspan="3"><?php echo $mission['date'];?></td>
		</tr>
		<tr>
				<td class="colorfont">GM</td>
				<td colspan="3"><?php echo $mission['forname']." ".$mission['lastname'];?></td>
				<td><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=gm" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>

		</tr>
		<tr>
				<td valign="top" class="colorfont">Briefing</td>
				<td colspan="3"><?php echo $mission['briefing'];?></td>
				<td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=briefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
		<tr>
				<td valign="top" class="colorfont">Debriefing</td>
				<td colspan="3"><?php echo $mission['debriefing'];?></td>
				<td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=debriefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
		<tr>
			<td valign="top" class="colorfont">Characters</td>
			<td colspan="3"><?php 
				$charactersql="SELECT c.forname,c.lastname,p.forname as pforname,p.lastname as plastname
									FROM {$_SESSION['table_prefix']}missions m
									LEFT JOIN {$_SESSION['table_prefix']}mission_names mn ON m.mission_id=mn.id
									LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=m.character_id
									LEFT JOIN Users p ON p.id=c.userid
									WHERE m.mission_id='{$_GET['id']}' ORDER BY c.lastname,c.forname";
				//echo $charactersql;
				$characterres=mysql_query($charactersql);
				while ($character=mysql_fetch_array($characterres)) {
					echo $character['forname'] . " " . $character['lastname'] . " - " .  $character['pforname'] . " " . $character['plastname'] . "<br>";
				} ?>
			</td>
			<td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=characters" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
		<tr>
			<td valign="top" class="colorfont">Commendations</td>
			<td colspan="3"><?php 
				$commendationssql="SELECT c.forname,c.lastname,medal_short
									FROM {$_SESSION['table_prefix']}medal_names mn
									LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.medal_id=mn.id
									LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=m.character_id
									WHERE m.mission_id='{$_GET['id']}'";
//									echo $charactersql;
				$commendationsres=mysql_query($commendationssql);
				while ($commendations=mysql_fetch_array($commendationsres)) {
					echo $commendations['forname'] . " " . $commendations['lastname'] . " - " .  $commendations['medal_short'] . "<br>";
				} ?>
			</td>
			<td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=commendations" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
		<tr>
			<td valign="top" class="colorfont">Promotions</td>
			<td colspan="3"><?php 
				$promotionsql="SELECT c.forname,c.lastname,rank_short
									FROM {$_SESSION['table_prefix']}rank_names rn
									LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.rank_id=rn.id
									LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=m.character_id
									WHERE m.mission_id='{$_GET['id']}'";
//									echo $charactersql;
				$promotionres=mysql_query($promotionsql);
				while ($promotion=mysql_fetch_array($promotionres)) {
					echo $promotion['forname'] . " " . $promotion['lastname'] . " - " .  $promotion['rank_short'] . "<br>";
				} ?>
			</td>
			<td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission['id'];?>&what=promotions" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
		</tr>
</table>
