<strong>Testing - more info to be added here later</strong>
<br />
<?php
$characterId = $_GET['character_id'];
$userController = new UserController();
$characterController = new CharacterController();
$character = new Character($characterId);
$character = $characterController->getCharacter($characterId);
$user = $userController->getCurrentUser();

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
  <div class="title">Character Statistics</div>
  <table cellspacing="10" border="0">
	<tr>
		<td class="thead">Name</td>
		<td class="thead">Value</td>
		<td class="thead">Comment</td>
	</tr>
	<tr>
		<td>Total XP value</td>
		<td><?php echo $xpval ?></td>
		<td>Disadvantages earned through play can reduce this</td>
	</tr>
	<tr>
		<td>Average XP per mission</td>
		<td><?php echo ($missionCount>0 ? round(($xpval-117)/$missionCount,2) : 0) ?></td>
		<td></td>
	</tr>
	<tr>
		<td>Average glory per mission</td>
		<td><?php if ($missionCount>0 and $totglory>0) {
			echo round($totglory/$missionCount,2);
		} else {
			echo 0;
		}?></td>
		<td></td>
	</tr>
  </table>
  <br />
  <br />
  <div class="title">Simulated missions (0)</div>
  <br />
  <br />
  <div class="title">Your combat missions (<?php echo $missionCount ?>)</div>
  <?php
  if ($missionCount > 0) {
	?>
	<table width="50%"  border="0">
	<tr>
		<td class="thead">Mission</td>
		<td class="thead">Results</td>
	</tr>
	<?php
    $missions = $character->getMissionsLong();
    foreach ($missions as $mission) {
        ?>
		<tr>
			<td><?php echo $mission['mission_name']; ?></td>
			<td colspan="3"><?php echo $mission['text']; ?></td>
		</tr>
		<?php
    }
	?>
	</table>
	<br />
	<br />
	<div class="title">Special Encounters <?php echo ($character->getStatus() == 'Active' ? "Survived" : "")?></div>
	<?php
	if ($character->getEncounterAlien() == 1) { echo "Aliens<br />"; }
	if ($character->getEncounterGrey() == 1) { echo "Greys<br />"; }
	if ($character->getEncounterPredator() == 1) { echo "Predators<br />"; }
	if ($character->getEncounterAI() == 1) { echo "AI/Android<br />"; }
	if ($character->getEncounterArachnid() == 1) { echo "Arachnids<br />"; }
	?>
	<br />
	<br />
	<div class="title">Certificate courses you qualify for</div>
	<?php
	$availableCerts = $character->getCertsBuyableWithoutReqCheck();
	$cert = getCertificateRequirements();
	
	foreach ( $cert as $id => $req ) {
      $req_met = FALSE;
      if (in_array($id, $availableCerts)) {
        $has_req = FALSE;
        foreach ( $req as $reqid ) {
//           echo "testing ".$charskillattrib[$reqid['table_name']][$reqid['id']]." against ".$reqid['value']." ";
//           print "\n<br>";
          if ($reqid['value_greater'] == "1") {
            if (array_key_exists($reqid['id'], $charskillattrib[$reqid['table_name']]) &&
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
			echo $name['name']." <br />";
		}
      }
      
	}
	?>
	<br />
	<br />
	<div class="title">Others you served with</div>
	<table width="50%"  border="0">
		<tr>
			<td class="thead">Name</td>
			<td class="thead">Missions</td>
			<td class="thead">Status</td>
		</tr>
		<?php
		$ocarray = servedWith($characterId);
		foreach ($ocarray as $comrade) { ?>
		<tr>
			<td><?php echo $comrade['name'];?></td>
			<td><?php echo $comrade['missions'];?></td>
			<td><?php echo $comrade['status'];?></td>
		</tr>
		<?php } ?>
	</table>
	<?php
  }
  ?>

  <?php
} else {
    include("not_allowed.php");
}
?>