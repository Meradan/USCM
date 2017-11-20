<strong>Testing</strong>
123
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

  if ($missionCount > 0) {
	$ocarray = servedWith($characterid);
	?>
	<div class="title">Others you served with</div>
	<table width="50%"  border="0">
		<tr>
			<td>Name</td>
			<td>Missions</td>
			<td>Status</td>
		</tr>
		<?php foreach ($ocarray as $comrade) { ?>
		<tr>
			<TD><?php echo $comrade['name'];?></TD>
			<TD><?php echo $comrade['missions'];?></TD>
			<TD><?php echo $comrade['status'];?></TD>
		</tr>
		<?php } ?>
	</table>
	<?php
  } else {
	?>
	You did not go on any missions yet
	<?php
  }
  ?>

  <?php
} else {
    include("not_allowed.php");
}
?>