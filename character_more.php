<?php
$characterId = $_GET['character_id'];
$userController = new UserController();
$characterController = new CharacterController();
$character = new Character($characterId);
$character = $characterController->getCharacter($characterId);
$user = $userController->getCurrentUser();
?>

<h1 class="heading heading-h1">
  Do you want to know more?
  <span class="span">
    <a href="index.php?url=modify_character.php&character_id=<?php echo $characterId;?>">Back</a>
  </span>
</h1>
<h2 class="heading heading-h2">
  <?php echo $character->getGivenName(); ?> <?php echo $character->getSurname(); ?>
</h2>

<?php
if ($user->getId() == $character->getPlayerId() || $user->isAdmin() || $user->isGm()) {
  $player = $character->getPlayer();
  $missionCount = getNumberOfMissionsForCharacter($characterId);
  $xpval = $character->getXPvalue();
  $totglory = $character->getGlory();
  $skillarray = $character->getSkillsForCharacter();
  $attribarray = $character->getAttributesForCharacter();

  $charskillattrib = array ();
  foreach ( $skillarray as $id => $value ) {
	$charskillattrib['skill_names'][$id] = $value;
  }
  foreach ( $attribarray as $id => $value ) {
	$charskillattrib['attribute_names'][$id] = $value;
  }
  ?>

  <h3 class="heading heading-h3">Character Statistics</h3>

  <dl class="list-description">
    <dt>
      Total XP value
    </dt>
    <dd>
      <details class="details">
        <summary><?php echo $xpval ?></summary>
        New characters start at 117 XP.
      </details>
    </dd>
    <dt>
      Average XP per mission
    </dt>
    <dd>
      <details class="details">
        <summary><?php echo ($missionCount>0 ? round(($xpval-117)/$missionCount,2) : 0) ?></summary>
        Disadvantages earned through play can reduce this number.
      </details>
    </dd>
    <dt>
      Average glory per mission
    </dt>
    <dd>
      <?php if ($missionCount>0 and $totglory>0) {
        echo round($totglory/$missionCount,2);
      } else {
        echo 0;
      }?>
    </dd>
  </dl>

  <h3 class="heading heading-h3">
    Combat missions (<?php echo $missionCount ?>)
  </h3>

  <?php
  if ($missionCount > 0) {
	?>
	<table class="table">
    <thead>
    <tr>
      <th>Mission</th>
      <th>Results</th>
    </tr>
    </thead>
    <tbody>
	<?php
    $missions = $character->getMissionsLong();
    foreach ($missions as $mission) {
        ?>
		<tr>
			<td><a href="index.php?url=show_mission.php&id=<?php echo $mission['id'];?>"><?php echo $mission['mission_name'];?></a></td>
			<td><?php echo $mission['text']; ?></td>
		</tr>
		<?php
    }
	?>
    </tbody>
	</table>

    <h3 class="heading heading-h3">
      Special Encounters <?php echo ($character->getStatus() == 'Active' ? "Survived" : "")?>
    </h3>

    <ul class="list">
      <?php
      if ($character->getEncounterAlien() == 1) { echo "<li>Aliens</li>"; }
      if ($character->getEncounterGrey() == 1) { echo "<li>Greys</li>"; }
      if ($character->getEncounterPredator() == 1) { echo "<li>Predators</li>"; }
      if ($character->getEncounterAI() == 1) { echo "<li>AI/Androids</li>"; }
      if ($character->getEncounterArachnid() == 1) { echo "<li>Arachnids</li>"; }
      ?>
    </ul>

    <h3 class="heading heading-h3">
      Certificate courses you qualify for
    </h3>

    <ul class="list">
	<?php
	$availableCerts = $character->getCertsBuyableWithoutReqCheck();
	$cert = getCertificateRequirements();

	foreach ( $cert as $id => $req ) {
      $req_met = FALSE;
      if (in_array($id, $availableCerts)) {
        $has_req = FALSE;
        foreach ( $req as $reqid ) {
			//echo "testing ".$charskillattrib[$reqid['table_name']][$reqid['id']]." against ".$reqid['value']." ";
			//print "\n<br>";
          if ($reqid['value_greater'] == "1") {
            if (!empty($charskillattrib[$reqid['table_name']]) && array_key_exists($reqid['id'], $charskillattrib[$reqid['table_name']]) &&
                 $charskillattrib[$reqid['table_name']][$reqid['id']] >= $reqid['value']) {
              $has_req = TRUE;
            } else {
              $has_req = FALSE;
              break;
            }
          } else {
            if ($charskillattrib[$reqid['table_name']][$reqid['id']] <= $reqid['value']) {
              $has_req = TRUE;
            } else {
              $has_req = FALSE;
              break;
            }
          }
        }
        $req_met = $has_req;
		if ($req_met) {
			reset($req);
			$name = current($req);
      echo "<li>".$name['name']."</li>";
		}
      }

	}
	?>
    </ul>

    <h3 class="heading heading-h3">
      Others you served with
    </h3>

	<table class="table">
    <thead>
    <tr>
      <th>Name</th>
      <th>Missions</th>
      <th>Status</th>
    </tr>
    </thead>
		<tbody>
		<?php
		$ocarray = servedWith($characterId);
		foreach ($ocarray as $comrade) { ?>
		<tr>
			<td><?php echo $comrade['name'];?></td>
			<td><?php echo $comrade['missions'];?></td>
			<td><?php echo $comrade['status'];?></td>
		</tr>
		<?php } ?>
    </tbody>
	</table>
	<?php
  }
  ?>

  <?php
} else {
    include("components/403.php");
}
?>
