<?php
$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']>=2)?(TRUE):(FALSE);
$missionId = $_GET['id'];
$missionController = new MissionController();
$mission = $missionController->getMission($missionId);
$playerController = new PlayerController();
$gmUser = $playerController->getPlayer($mission->getGmId());
?>

<table width="100%"  border="0" cellpadding="5">
    <tr>
        <td class="colorfont">Mission</td>
        <td colspan="3"><?php echo $mission->getShortName();?></td>
        <td rowspan="2"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=names" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
        <td class="colorfont">Name</td>
        <td colspan="3"><?php echo $mission->getName();?></td>
    </tr>
    <tr>
        <td class="colorfont">Date</td>
        <td colspan="3"><?php echo $mission->getDate();?></td>
    </tr>
    <tr>
        <td class="colorfont">GM</td>
        <td colspan="3"><?php echo $gmUser->getName()?></td>
        <td><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=gm" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>

    </tr>
    <tr>
        <td valign="top" class="colorfont">Briefing</td>
        <td colspan="3"><?php echo $mission->getBriefing();?></td>
        <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=briefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
        <td></td>
        <td><center><img src="images/line.jpg" width="449" height="1"></center></td>
        <td></td>
    </tr>
    <tr>
        <td valign="top" class="colorfont">Debriefing</td>
        <td colspan="3"><?php echo $mission->getDebriefing();?></td>
        <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=debriefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Characters</td>
      <td colspan="3"><?php
        $charactersAndPlayers = $missionController->getCharactersAndPlayers($mission);
        foreach ($charactersAndPlayers as $character) {
          echo $character['forname'] . " " . $character['lastname'] . " - " .  $character['pforname'] . " " . $character['plastname'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=characters" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Commendations</td>
      <td colspan="3"><?php
        $commendations = $missionController->getCommendations($mission);
        foreach ($commendations as $commendation) {
          echo $commendation['forname'] . " " . $commendation['lastname'] . " - " .  $commendation['medal_short'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=commendations" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Promotions</td>
      <td colspan="3"><?php
      $promotions = $missionController->getPromotions($mission);
        foreach ($promotions as $promotion) {
          echo $promotion['forname'] . " " . $promotion['lastname'] . " - " .  $promotion['rank_short'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=promotions" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
</table>
