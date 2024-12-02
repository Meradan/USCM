<?php
class MissionController {
  private $db = NULL;
  private $characterController = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
    $this->characterController = new CharacterController();
  }

  /**
   *
   * @param Mission $mission
   */
  public function save($mission) {
    $insertId = NULL;
    $sql = "INSERT INTO uscm_mission_names SET mission_name_short=:shortName,
                mission_name=:name, date=:date, gm=:gm, briefing=:briefing,
                debriefing=:debriefing, platoon_id=:platoonId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':shortName', $mission->getShortName(), PDO::PARAM_STR);
    $stmt->bindValue(':name', $mission->getName(), PDO::PARAM_STR);
    $stmt->bindValue(':date', $mission->getDate(), PDO::PARAM_STR);
    $stmt->bindValue(':gm', $mission->getGmId(), PDO::PARAM_INT);
    $stmt->bindValue(':briefing', $mission->getBriefing(),PDO::PARAM_STR);
    $stmt->bindValue(':debriefing', $mission->getDebriefing(), PDO::PARAM_STR);
    $stmt->bindValue(':platoonId', $mission->getPlatoonId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $insertId = $this->db->lastInsertId();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
    return $insertId;
  }

/**
   *
   * @param Mission $mission
   */
  public function update($mission) {
    $sql = "UPDATE uscm_mission_names SET mission_name_short=:shortName,
                mission_name=:name, date=:date, gm=:gm, briefing=:briefing,
                debriefing=:debriefing, platoon_id=:platoonId WHERE id = :missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':shortName', $mission->getShortName(), PDO::PARAM_STR);
    $stmt->bindValue(':name', $mission->getName(), PDO::PARAM_STR);
    $stmt->bindValue(':date', $mission->getDate(), PDO::PARAM_STR);
    $stmt->bindValue(':gm', $mission->getGmId(), PDO::PARAM_INT);
    $stmt->bindValue(':briefing', $mission->getBriefing(),PDO::PARAM_STR);
    $stmt->bindValue(':debriefing', $mission->getDebriefing(), PDO::PARAM_STR);
    $stmt->bindValue(':platoonId', $mission->getPlatoonId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }

  /**
   *
   * @return Mission[]
   */
  function getMissions() {
    $missions = array();
    $sql = "SELECT mission_name_short,mission_name,mn.id as missionid,pn.name_short as platoonnameshort ".
        "FROM uscm_mission_names mn " .
        "LEFT JOIN uscm_platoon_names pn ON pn.id=mn.platoon_id ORDER BY date DESC,mission_name_short DESC";
    $stmt = $this->db->prepare($sql);
    try {
      $stmt->execute();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $mission = new Mission();
        $mission->setId($row['missionid']);
        $mission->setName($row['mission_name']);
        $mission->setShortName($row['mission_name_short']);
        $mission->setPlatoonShortName($row['platoonnameshort']);
        $missions[] = $mission;
      }
    } catch (PDOException $e) {
      print "Error fetching missions " . $e->getMessage() . "<br>";
    }
    return $missions;
  }

  /**
   *
   * @param int $missionId
   * @return Mission
   */
  function getMission($missionId) {
    $sql = "SELECT mission_name_short, mission_name, mn.id as missionid, pn.name_short as platoonnameshort, gm, date, briefing, debriefing, platoon_id, GROUP_CONCAT(t.tag SEPARATOR ', ') as tags FROM uscm_mission_names mn LEFT JOIN uscm_platoon_names pn ON pn.id=mn.platoon_id LEFT JOIN uscm_mission_tags mt ON mn.id=mt.missionid LEFT JOIN uscm_tags t ON mt.tagid=t.id WHERE mn.id = :missionId GROUP BY mn.id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $missionId, PDO::PARAM_INT);
    $mission = new Mission();
    try {
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!empty($row)) {
        $mission->setId($row['missionid']);
        $mission->setName($row['mission_name']);
        $mission->setShortName($row['mission_name_short']);
        $mission->setPlatoonShortName($row['platoonnameshort']);
        $mission->setGmId($row['gm']);
        $mission->setDate($row['date']);
        $mission->setBriefing($row['briefing']);
        $mission->setDebriefing($row['debriefing']);
        $mission->setPlatoonId($row['platoon_id']);
        $mission->setTags($row['tags']);
      }
    } catch (PDOException $e) {
    }
    return $mission;
  }

  /**
   *
   * @param Mission $mission
   * @return array
   */
  public function getCharactersAndPlayers($mission) {
    $sql="SELECT c.forname,c.lastname,p.forname as pforname,p.lastname as plastname
                  FROM uscm_missions m
                  LEFT JOIN uscm_mission_names mn ON m.mission_id=mn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  LEFT JOIN Users p ON p.id=c.userid
                  WHERE m.mission_id=:missionId AND (p.id != '0' AND p.id != '59') ORDER BY c.lastname,c.forname";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return array();
    }
  }

  public function getNonPlayerCharacters($mission) {
    $sql="SELECT c.forname,c.lastname
                  FROM uscm_missions m
                  LEFT JOIN uscm_mission_names mn ON m.mission_id=mn.id
                  LEFT JOIN uscm_characters c ON c.id=m.character_id
                  LEFT JOIN Users p ON p.id=c.userid
                  WHERE m.mission_id=:missionId AND (p.id = '0' OR p.id = '59') ORDER BY c.lastname,c.forname";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return array();
    }
  }

/**
   *
   * @param Mission $mission
   * @return Character[]
   */
  public function getCharacters($mission) {
    $characters = array();
    $sql="SELECT character_id FROM uscm_missions m
                  WHERE mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $characters[$row['character_id']] = $this->characterController->getCharacter($row['character_id']);
      }
    } catch (PDOException $e) {
    }
    return $characters;
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
    try {
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return array();
    }
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
    try {
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return array();
    }
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
    try {
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        return $row['medalid'];
      }
    } catch (PDOException $e) {
      return array();
    }

    return 0;
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
    try {
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row['rank_id'];
    } catch (PDOException $e) {
    }
  }

    /**
   *
   * @param Character $character
   * @param Mission $mission
   * @return int|NULL Returns the previous Rank id if character was promoted on mission
   */
  public function getRankBeforePromotion($character, $mission) {
    $sql = "SELECT previous_rank_id FROM uscm_missions WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    try {
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row['previous_rank_id'];
    } catch (PDOException $e) {
    }
  }

  /**
   *
   * @param Character $character
   * @param Medal $medal
   * @param Mission $mission
   */
  public function giveCharacterCommendationOnMission($character, $medal, $mission) {
    $sql="UPDATE uscm_missions SET medal_id=:medalId WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':medalId', $medal->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }

  /**
   *
   * @param Character $character
   * @param Rank $rank
   * @param Mission $mission
   */
  public function promoteCharacterOnMission($character, $rank, $mission) {
    $sql="UPDATE uscm_missions SET rank_id=:rankId, previous_rank_id=:previousRankId
            WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':rankId', $rank->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':previousRankId', $character->getRankId(), PDO::PARAM_INT);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }

  public function removeCharacterPromotionOnMission($character, $mission) {
    $sql="UPDATE uscm_missions SET rank_id=NULL, previous_rank_id=NULL
            WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }

  /**
   * Sets the provided characters as participants on the mission. If there previously where
   * other characters listed on the mission, those will be removed
   * @param Character[] $characters All characters that participated on mission
   * @param Mission $mission
   */
  public function setCharacters($characters, $mission) {
    //echo "setCharacters:\n";
    //print_r($characters);
    $previousParticipants = $this->getCharacters($mission);
    //echo "Previous:\n";
    //print_r($previousParticipants);
    $previousCharacterIdsOnMission = array();
    $charactersToRemove = array();
    foreach ($previousParticipants as $character) {
      $previousCharacterIdsOnMission[] = $character->getId();
    }
    //echo "Previous ids:\n";
    print_r($previousCharacterIdsOnMission);
    foreach ($characters as $character) {
      //echo "Id: " . $character->getId() . " ";
      if (array_key_exists($character->getId(), $previousParticipants)) {
        //echo "Character was on mission before: \n";
        //print_r($character);
        unset($previousParticipants[$character->getId()]);
      } else {
        //echo "Character not on mission before\n";
        //print_r($character);
        $this->addCharacter($character, $mission);
      }
    }
    foreach ($previousParticipants as $character) {
      //echo "Removing character that was on mission before: ". $character->getId() . "\n";
      $this->removeCharacter($character, $mission);
    }
  }

  /**
   *
   * @param Mission $mission
   * @param Character $character
   */
  public function removeCharacter($character, $mission) {
    $sql="DELETE FROM uscm_missions WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }
  /**
   *
   * @param Mission $mission
   * @param int $characterId Character id
   */
  private function removeCharacterId($characterId, $mission) {
    $sql="DELETE FROM uscm_missions WHERE character_id=:characterId AND mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':characterId', $characterId, PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }

  /**
   *
   * @param Character $character
   * @param Mission $mission
   */
  public function addCharacter($character, $mission) {
    $sql="INSERT INTO uscm_missions SET character_id=:characterId, mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':characterId', $character->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    try {
      $this->db->beginTransaction();
      $stmt->execute();
      $this->db->commit();
    } catch (PDOException $e) {
      $this->db->rollBack();
    }
  }
}
