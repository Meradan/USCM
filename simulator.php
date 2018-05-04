<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
$simulationController = new SimulationController();
$simulations = $simulationController->getSimulations();
?>
<strong>In progress..</strong><br />
Simulated missions can be done any number of times without risk to your character. See description of available missions below.
<br />
<br />
<?php
$scoredisplaynumber = 5;

if ($user->getId() > 0)
{
	$characterController = new CharacterController();
	$characters = $characterController->getUserActiveCharacters($user->getId());
	
	?>
	<table cellspacing="10" border="0">
	<tr>
	<td class="title">Soldier</td>
	<td class="title">Simulated Mission</td>
	<td class="title">Click to start</td>
	</tr>
	<tr>
	<td>
	<select name="character">
	<?php foreach ($characters as $character) { ?>
			<option <?php echo (count($characters)==1) ? ("selected") : (""); ?> value="<?php echo $character->getId(); ?>" >
			<?php echo $character->getName(); ?></option>
		<?php } ?>
	</select>
	</td>
	<td>
	<select name="simulations">
	<?php foreach ($simulations as $simulation) { ?>
			<option <?php echo (count($simulations)==1) ? ("selected") : (""); ?> value="<?php echo $simulation->getId(); ?>" >
			<?php echo $simulation->getFullName(); ?></option>
		<?php } ?>
	</td>
	<td>Go!</td>
	</tr>
	</table>
	<?php
} //user logged in
?>
<br />
<br />
<br />

<?php foreach ($simulations as $simulation) { ?> 
	<br /><center><img src="images/line.jpg" width="449" height="1"></center><br />
	<div class="title"><?php echo $simulation->getFullName(); ?></div>
	<div><?php echo $simulation->getDescription(); ?></div>
	<table cellspacing="5" border="0">
	<tr class="title"><td colspan="2">Top <?=$scoredisplaynumber?> scores</td><td></td><td></td><td></td><td></td><td colspan="2">Last <?=$scoredisplaynumber?> scores</td><td></td><td></td><td></td></tr>
	<tr class="title"><td>Platoon</td><td>Character</td><td>Player</td><td>Score</td><td>Date</td><td>|</td><td>Platooon</td><td>Character</td><td>Player</td><td>Score</td><td>Date</td></tr>
	<?php
	$highscores = $simulation->getHighScores($scoredisplaynumber);
	$lastscores = $simulation->getLastScores($scoredisplaynumber);
	for ($i=0; $i<$scoredisplaynumber; $i++) {
		?><tr><?php
		if (!empty($highscores) && $i<count($highscores)) {
			echo "<td>{$highscores[$i]->platoon}</td><td>{$highscores[$i]->charactername}</td><td>{$highscores[$i]->playername}</td><td>{$highscores[$i]->points}</td><td>{$highscores[$i]->scoretime->format('Y-m-d')}</td>";
		} else {
			echo '<td></td><td></td><td></td><td></td><td></td>';
		}
		echo '<td class="title">|</td>';
		if (!empty($lastscores) && $i<count($lastscores)) {
			echo "<td>{$lastscores[$i]->platoon}</td><td>{$lastscores[$i]->charactername}</td><td>{$lastscores[$i]->playername}</td><td>{$lastscores[$i]->points}</td><td>{$lastscores[$i]->scoretime->format('Y-m-d')}</td>";
		} else {
			echo '<td></td><td></td><td></td><td></td><td></td>';
		}
		?></tr><?php
	}
	
	
	
	//<tr><td></td><td></td><td></td><td></td><td>|</td><td></td><td></td><td></td><td></td></tr>
	?>
	</table>
	<?php
	}
?>
