<h1 class="heading heading-h1">Missions</h1>
<ul class="list">
<?php
$missionController = new MissionController();
$missions = $missionController->getMissions();
foreach ($missions as $mission) { ?>
  <li>
    <span class="tag tag-<?php echo strtolower($mission->getPlatoonShortName());?>"><?php echo $mission->getPlatoonShortName();?></span>
    <a href="index.php?url=show_mission.php&id=<?php echo $mission->getId();?>">
      <?php echo $mission->getShortName();?>
    </a>
    <?php echo $mission->getName();?>
  </li>
<?php } ?>
</ul>
