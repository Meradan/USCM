<?php
class MissionController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  function getMissions() {
    $sql = "SELECT mission_name_short,mission_name,mn.id as missionid,pn.name_short as platoonnameshort ".
        "FROM uscm_mission_names mn " .
        "LEFT JOIN uscm_platoon_names pn ON pn.id=mn.platoon_id ORDER BY date DESC,mission_name_short DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $mission = new Mission();
      $mission->setId($row['missionid']);
      $mission->setName($row['mission_name']);
      $mission->setShortName($row['mission_name_short']);
      $mission->setPlatoonShortName($row['platoonnameshort']);
      $missions[] = $mission;
    }
    return $missions;
  }

function getMission($missionId) {
  $sql = "SELECT mission_name_short, mission_name, mn.id as missionid, " .
      "pn.name_short as platoonnameshort, gm, date, briefing, debriefing, platoon_id, count(*) as howmany ".
      "FROM uscm_mission_names mn " .
      "LEFT JOIN uscm_platoon_names pn ON pn.id=mn.platoon_id ".
      "WHERE mn.id = :missionId ORDER BY date DESC,mission_name_short DESC";
  $stmt = $this->db->prepare($sql);
  $stmt->bindValue(':missionId', $missionId, PDO::PARAM_INT);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $mission = new Mission();
  if ($row['howmany'] == 1) {
    $mission->setId($row['missionid']);
    $mission->setName($row['mission_name']);
    $mission->setShortName($row['mission_name_short']);
    $mission->setPlatoonShortName($row['platoonnameshort']);
    $mission->setGmId($row['gm']);
    $mission->setDate($row['date']);
    $mission->setBriefing($row['briefing']);
    $mission->setDebriefing($row['debriefing']);
    $mission->setPlatoonId($row['platoon_id']);

  }
  return $mission;
}

  public function getCharactersAndPlayers($mission) {
    $sql="SELECT c.forname,c.lastname,p.forname as pforname,p.lastname as plastname
                  FROM uscm_missions m
                  LEFT JOIN uscm_mission_names mn ON m.mission_id=mn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  LEFT JOIN Users p ON p.id=c.userid
                  WHERE m.mission_id=:missionId ORDER BY c.lastname,c.forname";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getCommendations($mission) {
    $sql="SELECT c.forname,c.lastname,medal_short
                  FROM uscm_medal_names mn
                  LEFT JOIN uscm_missions m ON m.medal_id=mn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  WHERE m.mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPromotions($mission) {
    $sql="SELECT c.forname,c.lastname,rank_short
                  FROM uscm_rank_names rn
                  LEFT JOIN uscm_missions m ON m.rank_id=rn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  WHERE m.mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
