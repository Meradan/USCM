<?php
$platoonController = new PlatoonController();
$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']==2)?(TRUE):(FALSE);
//echo $_SESSION['level'];
if (array_key_exists('platoon', $_GET)) {
  $where="AND c.platoon_id={$_GET['platoon']}";
} else {
  $where = "";
}
$deadcharactersql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%Y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status_desc
          FROM uscm_characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN uscm_ranks
            ON uscm_ranks.character_id = c.id
          LEFT JOIN uscm_rank_names
            ON uscm_rank_names.id =
              uscm_ranks.rank_id
          LEFT JOIN uscm_specialty
            ON uscm_specialty.character_id = c.id
          LEFT JOIN uscm_specialty_names
            ON uscm_specialty_names.id =
              uscm_specialty.specialty_name_id
              WHERE c.status = 'Dead' AND c.userid != '0' {$where}
              ORDER BY rank_id DESC";
$retiredcharactersql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%Y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status_desc
          FROM uscm_characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN uscm_ranks
            ON uscm_ranks.character_id = c.id
          LEFT JOIN uscm_rank_names
            ON uscm_rank_names.id =
              uscm_ranks.rank_id
          LEFT JOIN uscm_specialty
            ON uscm_specialty.character_id = c.id
          LEFT JOIN uscm_specialty_names
            ON uscm_specialty_names.id =
              uscm_specialty.specialty_name_id
              WHERE c.status = 'Retired' {$where}
              ORDER BY rank_id DESC";
$platoonsql="SELECT id,name_short,name_long FROM uscm_platoon_names";
$glorytopsql="SELECT c.id as cid,c.forname as cfor,c.lastname as clast,DATE_FORMAT(c.enlisted,'%Y-%m-%d') as enlisted,c.status,
              rank_short,rank_id,specialty_name, p.forname,p.lastname,c.userid,c.status
          FROM uscm_characters c
          LEFT JOIN Users as p ON c.userid = p.id
          LEFT JOIN uscm_ranks
            ON uscm_ranks.character_id = c.id
          LEFT JOIN uscm_rank_names
            ON uscm_rank_names.id =
              uscm_ranks.rank_id
          LEFT JOIN uscm_specialty
            ON uscm_specialty.character_id = c.id
          LEFT JOIN uscm_specialty_names
            ON uscm_specialty_names.id =
              uscm_specialty.specialty_name_id
              WHERE c.userid != '0' {$where}
              ORDER BY rank_id DESC";
//echo $retiredcharactersql . "<br><br><br><br><br><br>";

?>

<h1 class="heading heading-h1">Hall of Fame</h1>

<label for="select-platoon" style="display: block; margin-bottom: 20px;">
  Select platoon
  <select id="select-platoon" onchange="window.location.href = this.value">
    <option value="index.php?url=fame/list.php">All platoons</option>
    <?php
    $platoons = $platoonController->getPlatoons();
    foreach ($platoons as $platoon ) {
      ?>
      <option
        <?php if (array_key_exists("platoon", $_GET) && $_GET['platoon'] == $platoon->getId()) echo "selected"; ?>
        value="index.php?url=fame/list.php&platoon=<?php echo $platoon->getId(); ?>"
      >
        <?php echo $platoon->getName(); ?> (<?php echo $platoon->getShortName(); ?>)
      </option>
      <?php
    }
    ?>
  </select>
</label>

<table class="table mt-20">
  <caption>
    All time top 5 most glorious soldiers
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Missions</th>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Glory</th>
    <th>Status</th>
    <th>Player</th>
  </tr>
  </thead>
  <tbody>
<?php
// 	$characterres=mysql_query($glorytopsql);
    $characterarray = listCharacters($glorytopsql, "glory");
    $i = 0;
    foreach ($characterarray as $character) {
    $i++;
    if ($i >= 7) { break; }

	$medals = "";
	$glory = 0;
	$commendationsArray = getCommendationsForCharacter($character['cid']);
	foreach ($commendationsArray as $key => $value) {
		if ($commendationsArray[$key]['medal_short'] != "") $medals = $medals . " " . $commendationsArray[$key]['medal_short'];
		$glory = $glory + $commendationsArray[$key]['medal_glory'];
	}
	?>
  <tr>
    <td><?php echo $character['missions'];?></td>
    <td><?php echo $character['rank_short'];?></td>
    <td><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=characters/edit.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></td>
    <td><?php echo $character['specialty_name'];?></td>
    <td>
      <details class="details">
        <summary><?php echo $glory;?></summary>
        <?php echo $medals;?>
      </details>
    </td>
    <td>
      <?php if ($character['status'] != "Active") { echo $character['status']; } ?>
    </td>
    <td><?php echo $character['forname'] . " " . $character['lastname'];?></td>
  </tr>
<?php } ?>
  </tbody>
</table>

<table class="table mt-20">
  <caption>
    They who sacrificed their lives in the line of duty
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Missions</th>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Glory</th>
    <th>Tour of Duty</th>
    <th>Cause of death</th>
    <th>Mission</th>
    <th>Player</th>
  </tr>
  </thead>
  <tbody>
<?php
// 	$characterres=mysql_query($deadcharactersql);
    $characterarray = listCharacters($deadcharactersql, "dead");
    foreach ($characterarray as $character) { ?>
  <tr>
    <td><?php echo $character['missions'];?></td>
    <td><?php echo $character['rank_short'];?></td>
    <td><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=characters/edit.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></td>
    <td><?php echo $character['specialty_name'];?></td>
    <td>
      <?php if ($character['glory'] != "0") {?>
      <details class="details">
        <summary><?php echo $character['glory'];?></summary>
        <?php echo $character['medals'];?>
      </details>
      <?php } ?>
    </td>
<?php
// $lastmissionsql="SELECT DATE_FORMAT(date,'%Y-%m-%d') as date,mission_name_short FROM uscm_mission_names LEFT JOIN uscm_missions as m on m.mission_id = uscm_mission_names.id WHERE character_id = '{$character['cid']}' ORDER BY date DESC LIMIT 1";
//     $lastmission=mysql_fetch_array(mysql_query($lastmissionsql));
    $lastMission = lastMissionForCharacter($character['cid'])?>
    <td>
      <span class="no-wrap">
        * <?php echo $character['enlisted'];?>
      </span>
      <span class="no-wrap">
        † <?php echo $lastMission['date'] ?? '';?>
      </span>
    </td>
    <td><?php echo $character['status_desc'];?></td>
    <td><?php echo $lastMission['mission_name_short'] ?? '';?></td>
    <td><?php echo $character['forname'] . " " . $character['lastname'];?></td>
  </tr>
<?php unset($medals,$glory);
  } ?>
  </tbody>
</table>

<table class="table mt-20">
  <caption>
    Retirements
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Missions</th>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Glory</th>
    <th>Tour of Duty</th>
    <th>Cause of retirement</th>
    <th>Mission</th>
    <th>Player</th>
  </tr>
  </thead>
  <tbody>
<?php
//$characterres=mysql_query($retiredcharactersql);
    $characterarray = listCharacters($retiredcharactersql,"retired");
    foreach ($characterarray as $character) { ?>
  <tr>
    <td><?php echo $character['missions'];?></td>
    <td><?php echo $character['rank_short'];?></td>
    <td><?php if ($admin || $gm || $_SESSION['user_id']==$character['userid']) {
          ?><a href="index.php?url=characters/edit.php&character_id=<?php echo $character['cid'];?>"> <?php
        } ?><?php echo $character['cfor'] . " " . $character['clast'];?></a></td>
    <td><?php echo $character['specialty_name'];?></td>
    <td>
      <?php if ($character['glory'] != "0") {?>
        <details class="details">
          <summary><?php echo $character['glory'];?></summary>
          <?php echo $character['medals'];?>
        </details>
      <?php } ?>
    </td>
<?php
// $lastmissionsql="SELECT DATE_FORMAT(date,'%Y-%m-%d') as date,mission_name_short FROM uscm_mission_names LEFT JOIN uscm_missions as m on m.mission_id = uscm_mission_names.id WHERE character_id = '{$character['cid']}' ORDER BY date DESC LIMIT 1";
// 		$lastMission=mysql_fetch_array(mysql_query($lastmissionsql));
    $lastMission = lastMissionForCharacter($character['cid']) ?>
    <td>
      <span class="no-wrap">
        * <?php echo $character['enlisted'];?>
      </span>
      <span class="no-wrap">
        † <?php echo $lastMission['date'] ?? '';?>
      </span>
    </td>
    <td><?php echo $character['status_desc'];?></td>
    <td><?php echo $lastMission['mission_name_short'] ?? '';?></td>
    <td><?php echo $character['forname'] . " " . $character['lastname'];?></td>
  </tr>
<?php unset($medals,$glory);
  } ?>
  </tbody>
</table>
