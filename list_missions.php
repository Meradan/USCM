<?php
$missions = getMissions();
foreach ($missions as $mission) { ?>
<a href="index.php?url=show_mission.php&id=<?php echo $mission['id'];?>"><?php echo $mission['name_short'];?>: <?php echo $mission['mission_name_short'];?></a> <?php echo $mission['mission_name'];?><br><br>
<?php } ?>
