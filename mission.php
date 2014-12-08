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
  myconnect();
  mysql_select_db("skynet");

  if ($_GET['what']=="names") {
    $missionId = $_GET['mission'];
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
    $remove_characters=array();
    $add_characters=array();
    $old_characters=array();
    $mission_id=$_GET['mission'];
    $table=$_SESSION['table_prefix'];

    //
    // Characters on a mission
    //
    // Finds all character currently in database for the mission
    $sql="select character_id,id,mission_id from {$table}missions where mission_id={$mission_id}";
    $characterres=mysql_query($sql) or die(mysql_error());
    while($character=mysql_fetch_array($characterres)){
      $old_characters[$character['character_id']][mission_id]=$character['mission_id'];
      $old_characters[$character['character_id']][id]=$character['id'];
    }
    // walks through $_POST[] and decides what to delete and insert in database
    foreach($_POST['characters'] as $character_id) {
      if($old_characters[$character_id]) {
        // remove the handled data from old_characters since it's already in the database
        unset($old_characters[$character_id]);
      } else {
        // new data, add it
        $add_characters[$character_id]=$mission_id;
      }
    }
    // remove the characters that weren't in the $_POST, and thereby were removed from the mission
    foreach($old_characters as $index => $id){

      $remove_characters[$id[id]]=$id[id];
      unset($old_characters[$index]);
    }
    foreach($remove_characters as  $id) {
      $sql="DELETE FROM {$table}missions WHERE id='{$id}' LIMIT 1";
      mysql_query($sql) or die("tabort: " . mysql_error());
    }
    foreach($add_characters as $character_id => $mission_id) {
      $sql="INSERT INTO {$table}missions SET character_id='{$character_id}',mission_id='{$mission_id}'";
      mysql_query($sql);
    }

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
