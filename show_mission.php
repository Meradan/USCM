<?php
$admin=($_SESSION['level']>=3)?(TRUE):(FALSE);
$gm=($_SESSION['level']>=2)?(TRUE):(FALSE);
$missionId = $_GET['id'];
$missionController = new MissionController();
$mission = $missionController->getMission($missionId);
$playerController = new PlayerController();
$gmUser = $playerController->getPlayer($mission->getGmId());
?>

<h1 class="heading heading-h1">
  Mission <?php echo $mission->getShortName();?>
</h1>

<h2 class="heading heading-h2">
  <?php echo $mission->getName();?>
</h2>

<?php if ($admin or $gm): ?>
  <div>
    <a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=names">Change</a>
  </div>
<?php endif ?>

<table width="100%"  border="0" cellpadding="5">
    <tr>
        <td class="colorfont">Date</td>
        <td colspan="2"><?php echo $mission->getDate();?></td>
    </tr>
    <tr>
        <td class="colorfont">GM</td>
        <td><?php echo $gmUser->getName()?></td>
        <td><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=gm" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
        <td valign="top" class="colorfont">Briefing</td>
        <td><?php echo $mission->getBriefing();?></td>
        <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=briefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
        <td colspan="3"><hr class="line"></td>
    </tr>
    <tr>
        <td valign="top" class="colorfont">Debriefing</td>
        <td><?php echo $mission->getDebriefing();?></td>
        <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=debriefing" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Characters</td>
      <td><?php
        $charactersAndPlayers = $missionController->getCharactersAndPlayers($mission);
        foreach ($charactersAndPlayers as $character) {
          echo $character['forname'] . " " . $character['lastname'] . " - " .  $character['pforname'] . " " . $character['plastname'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=characters" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Commendations</td>
      <td><?php
        $commendations = $missionController->getCommendations($mission);
        foreach ($commendations as $commendation) {
          echo $commendation['forname'] . " " . $commendation['lastname'] . " - " .  $commendation['medal_short'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=commendations" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
    <tr>
      <td valign="top" class="colorfont">Promotions</td>
      <td><?php
      $promotions = $missionController->getPromotions($mission);
        foreach ($promotions as $promotion) {
          echo $promotion['forname'] . " " . $promotion['lastname'] . " - " .  $promotion['rank_short'] . "<br>";
        } ?>
      </td>
      <td valign="top"><?php if ($admin or $gm) {?><a href="index.php?url=modify_mission.php&mission=<?php echo $mission->getId();?>&what=promotions" class="colorfont">Change</a> <?php } else {?>&nbsp;<?php } ?></td>
    </tr>
</table>
