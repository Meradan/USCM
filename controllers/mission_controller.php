<?php
class MissionController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  /**
   *
   * @return Mission[]
   */
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

  /**
   *
   * @param int $missionId
   * @return Mission
   */
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

  /**
   *
   * @param Mission $mission
   * @return array:
   */
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
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   *
   * @param Mission $mission
   * @return array
   */
  public function getCommendations($mission) {
    $sql="SELECT c.forname,c.lastname,medal_short
                  FROM uscm_medal_names mn
                  LEFT JOIN uscm_missions m ON m.medal_id=mn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  WHERE m.mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   *
   * @param Mission $mission
   * @return array
   */
  public function getPromotions($mission) {
    $sql="SELECT c.forname,c.lastname,rank_short
                  FROM uscm_rank_names rn
                  LEFT JOIN uscm_missions m ON m.rank_id=rn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  WHERE m.mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   *
   * @param Mission $mission
   * @param int[] $characterIds Array of character id
   * @return int Id of the higest awarded medal
   */
  public function getHighestAwardedUscmMedalOnMissionForCharacterIds($mission, $characterIds) {
    $sql = "SELECT mn.id as medalid,m.character_id  FROM uscm_medal_names mn
              LEFT JOIN uscm_missions m ON m.medal_id=mn.id
              WHERE m.mission_id=:missionId AND mn.foreign_medal='0'";
    $first = TRUE;
    foreach ($characterIds as $characterId => $dummy) {
      if ($first) {
        $sql = $sql . " AND (m.character_id='{$characterId}'";
        $first = FALSE;
      } else {
        $sql = $sql . " OR m.character_id='{$characterId}'";
      }
    }
    $sql = $sql . ") ORDER BY mn.medal_glory DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      return $row['medalid'];
    } else {
      return 0;
    }
  }

  /**
   *
   * @param Character $character
   * @param Mission $mission
   * @return int|NULL Returns the Rank id if character was promoted on mission
   */
  public function getPromotionForCharacterOnMission($character, $mission) {
    $sql = "SELECT rank_id FROM uscm_missions WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['rank_id'];
  }
}
