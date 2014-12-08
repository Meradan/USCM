<?php
/*
 *	Functions handling missions should go in here
 *	Care should be taken when removing a character from a mission as promotions and
 *	commendations could be screwed up
 *
 */
session_start();
include("functions.php");
$missionController = new MissionController();
$characterController = new CharacterController();
$rankController = new RankController();
$userController = new UserController();
$user = $userController->getCurrentUser();

if ($user->isAdmin() || $user->isGm()) {
  $missionId = $_GET['mission'];
  if ($_GET['what']=="names") {
    $short=strtr(htmlspecialchars($_POST['mission_name_short'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $name=strtr(htmlspecialchars($_POST['mission_name'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $date=strtr(htmlspecialchars($_POST['date'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $platoon_id=$_POST['platoon_id'];
    $mission = $missionController->getMission($missionId);
    $mission->setName($name);
    $mission->setShortName($short);
    $mission->setDate($date);
    $mission->setPlatoonId($platoon_id);
    $missionController->update($mission);
  } elseif ($_GET['what']=="gm") {
    $mission = $missionController->getMission($missionId);
    $mission->setGmId($_POST['gm']);
    $missionController->update($mission);
  } elseif ($_GET['what']=="briefing") {
    $briefing=strtr(htmlspecialchars($_POST['briefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $mission = $missionController->getMission($missionId);
    $mission->setBriefing($briefing);
    $missionController->update($mission);
  } elseif ($_GET['what']=="debriefing") {
    $debriefing=strtr(htmlspecialchars($_POST['debriefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $mission = $missionController->getMission($missionId);
    $mission->setDebriefing($debriefing);
    $missionController->update($mission);
  } elseif ($_GET['what']=="characters") {
    $characters = array();
    $mission = $missionController->getMission($missionId);
    foreach ($_POST['characters'] as $characterId) {
      $characters[] = $characterController->getCharacter($characterId);
    }
    $missionController->setCharacters($characters, $mission);
  }
  elseif ($_GET['what']=="commendations") {
    foreach ($_POST['characters'] as $character_id => $dummy) {
      $mission = new Mission();
      $mission->setId($_GET['mission']);
      $character = new Character();
      $character->setId($character_id);
      $medal = new Medal();
      $medal->setId($_POST['medal']);
      $missionController->giveCharacterCommendationOnMission($character, $medal, $mission);
    }
  }
  elseif ($_GET['what']=="create_mission") {
    $short=strtr(htmlspecialchars($_POST['mission'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $name=strtr(htmlspecialchars($_POST['name'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $date=strtr(htmlspecialchars($_POST['date'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $briefing=strtr(htmlspecialchars($_POST['briefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $debriefing=strtr(htmlspecialchars($_POST['debriefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
    $platoon_id=$_POST['platoon_id'];
    $mission = new Mission();
    $mission->setName($name);
    $mission->setShortName($short);
    $mission->setDate($date);
    $mission->setBriefing($briefing);
    $mission->setDebriefing($debriefing);
    $mission->setPlatoonId($platoon_id);
    $missionId = $missionController->save($mission);
    $_GET['mission'] = $missionId;
  }
  elseif ($_GET['what']=="promotion") {
    $character_id=$_POST['character'];
    $characterId = $_POST['character'];
    $character = $characterController->getCharacter($characterId);
    $mission = $missionController->getMission($_GET['mission']);

    if ($_POST['rank']) {
      $rank = $rankController->getRank($_POST['rank']);
      $missionController->promoteCharacterOnMission($character, $rank, $mission);
      $rankController->promoteCharacter($rank, $character);
    } else {
      $previousRankId = $missionController->getRankBeforePromotion($character, $mission);
      $missionController->removeCharacterPromotionOnMission($character, $mission);
      $previousRank = $rankController->getRank($previousRankId);
      $rankController->promoteCharacter($previousRank, $character);
    }
  }
}

header("location:{$url_root}/index.php?url=show_mission.php&id={$_GET['mission']}");

?>
