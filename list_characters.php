<?php
$platoonController = new PlatoonController();
$rankController = new RankController();
$medalController = new MedalController();
$characterController = new CharacterController();

$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']==2)?(TRUE):(FALSE);
if (!array_key_exists('platoon', $_GET)) {
	if (array_key_exists('platoon_id', $_SESSION)) {
		$_GET['platoon']=$_SESSION['platoon_id'];
	} else {
		$_GET['platoon']=1;
	}
}

$where="AND c.platoon_id={$_GET['platoon']}";

$charactersql="SELECT rank_id,c.id as cid,c.forname,c.lastname,DATE_FORMAT(c.enlisted,'%Y-%m-%d') as enlisted,c.status,
              rank_short,specialty_name, p.forname as playerforname,p.lastname as playerlastname,p.nickname,
              p.use_nickname,c.userid
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
              WHERE c.status != 'Dead' AND c.status != 'Retired' AND p.id != '0' AND p.id != '59' {$where}
              ORDER BY rank_id DESC";
$npcsql="SELECT c.id as cid,c.forname,c.lastname,DATE_FORMAT(c.enlisted,'%Y-%m-%d') as enlisted,c.status,
              rank_id,rank_short,specialty_name,c.userid
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
              WHERE c.status != 'Dead' AND c.status != 'Retired' AND (p.id = '0' OR p.id = '59') {$where}
              ORDER BY rank_id DESC";

//echo $charactersql . "<br><br><br><br><br><br>";

?>

<h1 class="heading heading-h1">Characters</h1>

<label for="select-platoon" style="display: block; margin-bottom: 20px;">
  Select platoon
  <select id="select-platoon" onchange="window.location.href = this.value">
    <?php
      $platoons = $platoonController->getPlatoons();
      foreach ($platoons as $platoon ) {
    ?>
        <option
          <?php if (array_key_exists("platoon", $_GET) && $_GET['platoon'] == $platoon->getId()) echo "selected"; ?>
          value="index.php?url=list_characters.php&platoon=<?php echo $platoon->getId(); ?>"
        >
          <?php echo $platoon->getName(); ?> (<?php echo $platoon->getShortName(); ?>)
        </option>
    <?php
      }
    ?>
  </select>
</label>

<?php if ($_GET['platoon'] == "1") { ?>
  Assigned ship: USS Deliverance (Conestoga-class frigate)<br>
<?php } elseif ($_GET['platoon'] == "5") {?>
  Assigned ship: USS Nautilus (Conestoga-class frigate)<br>
<?php } ?>

<table class="table">
  <caption>
    Player Characters
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Missions</th>
	  <th>Last</th>
    <th>Enlisted</th>
    <th>Commendations</th>
    <th>Glory</th>
    <th>Player</th>
    <th>Status</th>
  </tr>
  </thead>
  <tbody>
<?php
  $characterarray = listCharacters($charactersql, "alive");
  foreach ($characterarray as $character) { ?>
  <TR><?php $overlib = false;
  if ($_SESSION['level']>=1  ) {
    $overlib = true;
    $attributearray = characterAttributes($character['cid']);
    $attributearray = attribute2visible($attributearray);
    $certificatearray = certificates($character['cid'],$_GET['platoon']);
    $overlibtext = "";
    foreach ( $attributearray as $id => $key ) {
      $attrib = $key;
      $overlibtext = $overlibtext . htmlentities($attrib,ENT_QUOTES) . "<br>";
    }
    $overlibtext = $overlibtext . "<br>";
    foreach ( $certificatearray as $id => $key ) {
      $cert = $key['name'];
      $cert2 = htmlentities($cert,ENT_QUOTES);
      $overlibtext = $overlibtext . $cert2 . "<br>";
    }
    $traitarray = traits($character['cid']);
    $traitarray ? $overlibtext = $overlibtext . "<br>": "";
    foreach ( $traitarray as $id => $key ) {
      $trait = $key['trait_name'];
      $overlibtext = $overlibtext . htmlentities($trait,ENT_QUOTES) . "<br>";
    }
    $allAdvantages = $characterController->getCharactersVisibleAdvantages($character['cid']);
    count($allAdvantages) > 0 ? $overlibtext = $overlibtext . "<br>": "";
    foreach ($allAdvantages as $advantage) {
      $overlibtext = $overlibtext . htmlentities($advantage->getName(), ENT_QUOTES) . "<br>";
    }
    $visibleDisadvantages = $characterController->getCharactersVisibleDisadvantages($character['cid']);
    count($visibleDisadvantages) > 0 ? $overlibtext = $overlibtext . "<br>": "";
    foreach ($visibleDisadvantages as $disadvantage) {
      $overlibtext = $overlibtext . htmlentities($disadvantage->getName(), ENT_QUOTES) . "<br>";
    }
  }
  $lastMission = lastMissionForCharacter($character['cid']);
    ?><TD><?php echo $character['rank_short'];?></TD>
    <td <?php if ($overlib) {?>class="popover" tabindex="0"<?php } ?>><?php
    $link = false;
    if ($admin || $gm || $_SESSION['user_id']==$character['userid']) { $link = true;?>
        <a href="index.php?url=modify_character.php&character_id=<?php echo $character['cid'];?>"><?php }
    ?><?php echo $character['forname'] . " " . $character['lastname'];?><?php echo $link ? "</a>" : ""; ?>
          <?php if ($overlib) {?>
            <div class="popover-panel">
              <?php echo $overlibtext ?>
            </div>
          <?php } ?>
    </td>
    <TD><?php echo $character['specialty_name'];?></TD>
    <TD class="center"><?php echo $character['missions'];?></TD>
	<TD><?php echo $lastMission['mission_name_short'] ?? '';?></TD>
    <td class="no-wrap"><?php echo $character['enlisted'];?></td>
<?php
      $medals = "";
      $glory = 0;
      $commendationsArray = getCommendationsForCharacter($character['cid']);
      foreach ($commendationsArray as $key => $value) {
        if ($commendationsArray[$key]['medal_short'] != "") $medals = $medals . " " . $commendationsArray[$key]['medal_short'];
        $glory = $glory + $commendationsArray[$key]['medal_glory'];
      }
      ?>
    <TD><?php echo ($medals != "")?($medals):("-");?></TD>
    <TD><?php echo ($glory != "0")?($glory):("");?></TD>
    <TD><?php echo ($character['use_nickname']=="1")?(stripslashes($character['nickname'])):(stripslashes($character['playerforname']) . " " . stripslashes($character['playerlastname']));?></TD>
    <TD><?php echo $character['status'];?></TD>
  </TR>
<?php unset($medals,$glory);
  } ?>
  </tbody>
</table>

<table class="table" style="margin-top: 20px;">
  <caption class="colorfont">
    Non-Player Characters
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Missions</th>
    <th>Enlisted</th>
    <th>Commendations</th>
    <th>Status</th>
  </tr>
  </thead>
  <tbody>
<?php
  $npcarray = listCharacters($npcsql,"alive");
  $medals = "";
  $glory = 0;
  foreach ($npcarray as $npc) { ?>
  <TR><?php $overlib = false;
  if ( $_SESSION['level']>=1  ) {
    $overlib = true;
      $attributearray = characterAttributes($character['cid']);
      $attributearray = attribute2visible($attributearray);
      $certificatearray = certificates($npc['cid'],$_GET['platoon']);
    $overlibtext = "";
    foreach ( $attributearray as $id => $key ) {
      $attrib = $key;
      $overlibtext = $overlibtext . htmlentities($attrib,ENT_QUOTES) . "<br>";
    }
    $overlibtext = $overlibtext . "<br>";
    foreach ( $certificatearray as $id => $key ) {
      $cert = $key['name'];
      $cert2 = htmlentities($cert,ENT_QUOTES);
      $overlibtext = $overlibtext . $cert2 . "<br>";
    }
    $traitarray = traits($npc['cid']);
    $traitarray ? $overlibtext = $overlibtext . "<br>": "";
    foreach ( $traitarray as $id => $key ) {
      $trait = $key['trait_name'];
      $overlibtext = $overlibtext . htmlentities($trait,ENT_QUOTES) . "<br>";
    }
    $advarray = advantages($npc['cid'], true);
    $advarray ? $overlibtext = $overlibtext . "<br>": "";
    foreach ( $advarray as $id => $key ) {
      $adv = $key['advantage_name'];
      $overlibtext = $overlibtext . htmlentities($adv,ENT_QUOTES) . "<br>";
    }
    $disadvantages = disadvantages($npc['cid'], true);
    $disadvantages ? $overlibtext = $overlibtext . "<br>": "";
    foreach ( $disadvantages as $id => $key ) {
      $dis = $key['disadvantage_name'];
      $overlibtext = $overlibtext . htmlentities($dis,ENT_QUOTES) . "<br>";
    }
  }
    ?><TD><?php echo $npc['rank_short'];?></TD>
    <td <?php if ($overlib) {?>class="popover" tabindex="0"<?php } ?>>
      <?php if ($admin || $gm || $_SESSION['user_id']==$npc['userid']) { ?>
        <a href="index.php?url=modify_character.php&character_id=<?php echo $npc['cid'];?>">
          <?php echo $npc['forname'] . " " . $npc['lastname'];?>
        </a>
      <?php } else { ?>
        <?php echo $npc['forname'] . " " . $npc['lastname'];?>
      <?php } ?>
      <?php if ($overlib) {?>
        <div class="popover-panel">
          <?php echo $overlibtext ?>
        </div>
      <?php } ?>
    </td>
    <TD><?php echo $npc['specialty_name'];?></TD>
<?php
  $missionCount = getNumberOfMissionsForCharacter($npc['cid'])?>
    <TD><?php echo $missionCount;?></TD>
    <TD class="no-wrap"><?php echo $npc['enlisted'];?></TD>
<?php
  $medals = "";
  $glory = 0;
  $commendationsArray = getCommendationsForCharacter($npc['cid']);
  foreach ($commendationsArray as $key => $value) {
    if ($commendationsArray[$key]['medal_short'] != "") $medals = $medals . " " . $commendationsArray[$key]['medal_short'];
    $glory = $glory + $commendationsArray[$key]['medal_glory'];
  }

?>
    <TD><?php echo ($medals!="")?($medals):("-");?></TD>
    <TD><?php echo $npc['status'];?></TD>
  </TR>
<?php
  unset($medals,$glory);
}
?>
  </tbody>
</table>

<?php if ($_GET['platoon'] == "1" || $_GET['platoon'] == "5" || $_GET['platoon'] == "6") { ?>
<table class="table" style="margin-top: 20px;">
  <caption>
    Special Non-Player Characters
    <hr class="line">
  </caption>
  <thead>
  <tr>
    <th>Rank</th>
    <th>Name</th>
    <th>Specialty</th>
    <th>Enlisted</th>
	  <th>Status</th>
  </tr>
  </thead>
  <tbody>
<?php if ($_GET['platoon'] == "1") {?>
  <tr>
    <td>Lieutenant</td>
    <td>Louise Wheatly</td>
    <td>Officer</td>
    <td class="no-wrap">19-08-10</td>
    <td>KIA</td>
  </tr>
  <tr>
    <td>Lieutenant</td>
    <td>Michael Brixton</td>
    <td>Officer</td>
    <td class="no-wrap">00-10-14</td>
    <td>Active</td>
  </tr>
  <tr>
    <td>Android</td>
    <td>Garth</td>
    <td>Synthetic</td>
    <td class="no-wrap">00-11-28</td>
    <td>Active</td>
  </tr>
<?php } elseif ($_GET['platoon'] == "5") {?>
  <tr>
    <td>Lieutenant</td>
    <td>Lionel Lee</td>
    <td>Officer</td>
    <td class="no-wrap">18-01-21</td>
    <td></td>
  </tr>
  <tr>
    <td>Android</td>
    <td>Ishmael</td>
    <td>Synthetic</td>
    <td class="no-wrap">18-01-21</td>
    <td></td>
  </tr>
<?php } elseif ($_GET['platoon'] == "6") {?>
  <tr>
    <td>Lieutenant</td>
    <td>Drake</td>
    <td>Officer</td>
    <td class="no-wrap">17-10-31</td>
    <td></td>
  </tr>
<?php } ?>
  </tbody>
</table>
<?php } ?>

<hr class="line mt-40">

<h2 class="heading heading-h2">Ranks</h2>

<?php
$ranks = $rankController->getRanks();
foreach ($ranks as $rank) { ?>
  <dl class="list-description">
    <dt>
      <span class="tag"><?php echo $rank->getShortName() ?></span>
    </dt>
    <dd>
      <?php echo $rank->getName() ?>
    </dd>
  </dl>
<?php } ?>

<hr class="line mt-40">

<h2 class="heading heading-h2">Medals</h2>

<h3 class="heading heading-h3">USCM Medals</h3>

<?php
$medals = $medalController->getUscmMedals();
foreach ($medals as $medal) { ?>
  <dl class="list-description">
    <dt class="no-wrap">
      <span class="tag"><?php echo $medal->getShortName() ?></span>
      Glory <?php echo $medal->getGlory() ?>
    </dt>
    <dd>
      <details class="details">
        <summary><?php echo $medal->getName() ?></summary>
        <?php echo $medal->getDescription() ?>
      </details>
    </dd>
  </dl>
<?php } ?>

<h3 class="heading heading-h3">Non-USCM Medals</h3>
<?php
$foreignmedals = $medalController->getForeignMedals();
foreach ($foreignmedals as $medal) { ?>
<dl class="list-description">
  <dt class="no-wrap">
    <span class="tag"><?php echo $medal->getShortName() ?></span>
    Glory <?php echo $medal->getGlory() ?>
  </dt>
  <dd>
    <details class="details">
      <summary><?php echo $medal->getName() ?></summary>
      <?php echo $medal->getDescription() ?>
    </details>
  </dd>
</dl>
<?php } ?>
