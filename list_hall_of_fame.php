<?php
$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']==2)?(TRUE):(FALSE);
//echo $_SESSION['level'];
if (array_key_exists('platoon', $_GET)) {
  $where="AND c.platoon_id={$_GET['platoon']}";
} else {
  $where = "";
}
$deadcharactersql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status_desc
          FROM {$_SESSION['table_prefix']}characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN {$_SESSION['table_prefix']}ranks
            ON {$_SESSION['table_prefix']}ranks.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}rank_names
            ON {$_SESSION['table_prefix']}rank_names.id =
              {$_SESSION['table_prefix']}ranks.rank_id
          LEFT JOIN {$_SESSION['table_prefix']}specialty
            ON {$_SESSION['table_prefix']}specialty.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}specialty_names
            ON {$_SESSION['table_prefix']}specialty_names.id =
              {$_SESSION['table_prefix']}specialty.specialty_name_id
              WHERE c.status = 'Dead' AND c.userid != '0' {$where}
              ORDER BY rank_id DESC,c.lastname,c.forname";
$retiredcharactersql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status_desc
          FROM {$_SESSION['table_prefix']}characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN {$_SESSION['table_prefix']}ranks
            ON {$_SESSION['table_prefix']}ranks.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}rank_names
            ON {$_SESSION['table_prefix']}rank_names.id =
              {$_SESSION['table_prefix']}ranks.rank_id
          LEFT JOIN {$_SESSION['table_prefix']}specialty
            ON {$_SESSION['table_prefix']}specialty.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}specialty_names
            ON {$_SESSION['table_prefix']}specialty_names.id =
              {$_SESSION['table_prefix']}specialty.specialty_name_id
              WHERE c.status = 'Retired' {$where}
              ORDER BY rank_id DESC,c.lastname,c.forname";
$platoonsql="SELECT id,name_short,name_long FROM {$_SESSION['table_prefix']}platoon_names";
$glorytopsql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status
          FROM {$_SESSION['table_prefix']}characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN {$_SESSION['table_prefix']}ranks
            ON {$_SESSION['table_prefix']}ranks.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}rank_names
            ON {$_SESSION['table_prefix']}rank_names.id =
              {$_SESSION['table_prefix']}ranks.rank_id
          LEFT JOIN {$_SESSION['table_prefix']}specialty
            ON {$_SESSION['table_prefix']}specialty.character_id = c.id
          LEFT JOIN {$_SESSION['table_prefix']}specialty_names
            ON {$_SESSION['table_prefix']}specialty_names.id =
              {$_SESSION['table_prefix']}specialty.specialty_name_id
              WHERE c.userid != '0' {$where}
              ORDER BY rank_id DESC,c.lastname,c.forname";
//echo $charactersql . "<br><br><br><br><br><br>";

?>
<div style="text-align:center;">
<a href="index.php?url=list_hall_of_fame.php">All platoons</a><br><br>
<?php
$platoons = getPlatoons();
foreach ($platoons as $platoon) { ?>
  <a href="index.php?url=list_hall_of_fame.php&platoon=<?php echo $platoon->getId(); ?>"><?php
    echo $platoon->getName(); ?> (<?php echo $platoon->getShortName(); ?>)</a>
<?php } ?>
</div>
<br/><center><img src="images/line.jpg" width="449" height="1"></center><br/>
<center>All time top 5 most glorious soldiers</center>
<br/>
<table width="700" align="center" cellspacing="3">
  <tr>
    <td class="colorfont">Glory</td>
    <td class="colorfont">Medals</td>
    <td class="colorfont">Missions</td>
    <td class="colorfont">Rank</td>
    <td class="colorfont">Name</td>
    <td class="colorfont">Specialty</td>
    <td class="colorfont">Player Name</td>
    <td class="colorfont">Status</td>
  </tr>
<?php
// 	$characterres=mysql_query($glorytopsql);
    $characterarray = listCharacters($glorytopsql, "glory");
    $i = 0;
    foreach ($characterarray as $character) {
    $i++;
    if ($i >= 7) { break; }?>
  <TR>
    <TD><?php echo $character['glory'];?></TD>
    <TD><?php echo $character['medals'];?></TD>
    <TD><?php echo $character['missions'];?></TD>
    <TD><?php echo $character['rank_short'];?></TD>
    <TD><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=modify_character.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></TD>
    <TD><?php echo $character['specialty_name'];?></TD>
    <TD><?php echo $character['forname'] . " " . $character['lastname'];?></TD>
    <TD><?php echo $character['status'];?></TD>
  </TR>
<?php } ?>
</table>
<br/><center><img src="images/line.jpg" width="449" height="1"></center><br/>
<center>They who sacrificed their lives in the line of duty</center>
<br/>
<TABLE WIDTH="950" ALIGN="center" cellspacing="3">
  <TR>
    <TD WIDTH="53" class="colorfont">Missions</TD>
    <TD WIDTH="32" class="colorfont">Rank</TD>
    <TD WIDTH="104" class="colorfont">Name</TD>
    <TD WIDTH="59" class="colorfont">Specialty</TD>
    <TD WIDTH="116" class="colorfont">Commendations</TD>
    <TD WIDTH="34" class="colorfont">Glory</TD>
    <TD WIDTH="126" class="colorfont">Player</TD>
    <TD WIDTH="71" class="colorfont">Enlisted</TD>
    <TD WIDTH="76" class="colorfont">Dead</TD>
    <TD WIDTH="51" class="colorfont">Mission</TD>
    <TD WIDTH="168" class="colorfont">Cause of death</TD>
  </TR>
  <TR>
    <TD COLSPAN="11" align="center"><IMG SRC="images/line.jpg" WIDTH="449" HEIGHT="1"></TD>
  </TR>
<?php
// 	$characterres=mysql_query($deadcharactersql);
    $characterarray = listCharacters($deadcharactersql, "dead");
    foreach ($characterarray as $character) { ?>
  <TR>
    <TD><?php echo $character['missions'];?></TD>
    <TD><?php echo $character['rank_short'];?></TD>
    <TD><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=modify_character.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></TD>
    <TD><?php echo $character['specialty_name'];?></TD>
    <TD><?php echo $character['medals'];?></TD>
    <TD><?php echo $character['glory'];?></TD>
    <TD><?php echo $character['forname'] . " " . $character['lastname'];?></TD>
    <TD><?php echo $character['enlisted'];?></TD>
<?php
// $lastmissionsql="SELECT DATE_FORMAT(date,'%y-%m-%d') as date,mission_name_short FROM {$_SESSION['table_prefix']}mission_names LEFT JOIN {$_SESSION['table_prefix']}missions as m on m.mission_id = {$_SESSION['table_prefix']}mission_names.id WHERE character_id = '{$character['cid']}' ORDER BY date DESC LIMIT 1";
//     $lastmission=mysql_fetch_array(mysql_query($lastmissionsql));
    $lastMission = lastMissionForCharacter($character['cid'])?>
    <TD><?php echo $lastMission['date'];?></TD>
    <TD><?php echo $lastMission['mission_name_short'];?></TD>
    <TD><?php echo $character['status_desc'];?></TD>


  </TR>
<?php unset($medals,$glory);
  } ?>
</TABLE>

<br/><center><img src="images/line.jpg" width="449" height="1"></center><br/>
<center>Retirements</center>
<br/>
<TABLE WIDTH="950" ALIGN="center" cellspacing="3">
  <TR>
    <TD WIDTH="53" class="colorfont">Missions</TD>
    <TD WIDTH="32" class="colorfont">Rank</TD>
    <TD WIDTH="104" class="colorfont">Name</TD>
    <TD WIDTH="59" class="colorfont">Specialty</TD>
    <TD WIDTH="116" class="colorfont">Commendations</TD>
    <TD WIDTH="34" class="colorfont">Glory</TD>
    <TD WIDTH="126" class="colorfont">Player</TD>
    <TD WIDTH="71" class="colorfont">Enlisted</TD>
    <TD WIDTH="76" class="colorfont">Retired</TD>
    <TD WIDTH="51" class="colorfont">Mission</TD>
    <TD WIDTH="168" class="colorfont">Cause of retirement</TD>
  </TR>
  <TR>
    <TD COLSPAN="11" align="center"><IMG SRC="images/line.jpg" WIDTH="449" HEIGHT="1"></TD>
  </TR>
<?php
//$characterres=mysql_query($retiredcharactersql);
    $characterarray = listCharacters($retiredcharactersql,"retired");
    foreach ($characterarray as $character) { ?>
  <TR>
    <TD><?php echo $character['missions'];?></TD>
    <TD><?php echo $character['rank_short'];?></TD>
    <TD><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=modify_character.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></TD>
    <TD><?php echo $character['specialty_name'];?></TD>
    <TD><?php echo $character['medals'];?></TD>
    <TD><?php echo $character['glory'];?></TD>
    <TD><?php echo $character['forname'] . " " . $character['lastname'];?></TD>
    <TD><?php echo $character['enlisted'];?></TD>
<?php
// $lastmissionsql="SELECT DATE_FORMAT(date,'%y-%m-%d') as date,mission_name_short FROM {$_SESSION['table_prefix']}mission_names LEFT JOIN {$_SESSION['table_prefix']}missions as m on m.mission_id = {$_SESSION['table_prefix']}mission_names.id WHERE character_id = '{$character['cid']}' ORDER BY date DESC LIMIT 1";
// 		$lastMission=mysql_fetch_array(mysql_query($lastmissionsql));
    $lastMission = lastMissionForCharacter($character['cid']) ?>
    <TD><?php echo $lastMission['date'];?></TD>
    <TD><?php echo $lastMission['mission_name_short'];?></TD>
    <TD><?php echo $character['status_desc'];?></TD>


  </TR>
<?php unset($medals,$glory);
  } ?>
</TABLE>
