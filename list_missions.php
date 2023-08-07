<h1 class="heading heading-h1">Missions</h1>
<?php
$missionController = new MissionController();
$missions = $missionController->getMissions();
foreach ($missions as $mission) { ?>
<a href="index.php?url=show_mission.php&id=<?php echo $mission->getId();?>"><?php
  echo $mission->getPlatoonShortName();?>: <?php echo $mission->getShortName();?></a> <?php
  echo $mission->getName();?><br><br>
<?php } ?>
