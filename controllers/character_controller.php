<?php
Class CharacterController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  /**
   *
   * @param int $characterId
   * @return void|Character
   */
  public function getCharacter($characterId) {
    if ($characterId == NULL) {
      return;
    }
    $character = new Character();
    $sql = "SELECT userid, platoon_id, forname, lastname, Enlisted, Age, Gender, UnusedXP,
        AwarenessPoints, CoolPoints, ExhaustionPoints, FearPoints, LeadershipPoints, PsychoPoints,
        TraumaPoints, MentalPoints, status, status_desc, specialty_name, uscm_specialty_names.id as specialty_id,
        rank_id, rank_short, rank_long, rank_desc, count(*) as howmany
        FROM uscm_characters
        LEFT JOIN uscm_ranks ON uscm_characters.id = uscm_ranks.character_id
        LEFT JOIN uscm_rank_names ON  uscm_ranks.rank_id = uscm_rank_names.id
        LEFT JOIN uscm_specialty ON uscm_characters.id = uscm_specialty.character_id
        LEFT JOIN uscm_specialty_names ON  uscm_specialty.specialty_name_id = uscm_specialty_names.id
        WHERE uscm_characters.id = :cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row['howmany'] == 1) {
      $character->setId($characterId);
      $character->setGivenName($row['forname']);
      $character->setSurname($row['lastname']);
      $character->setPlayerId($row['userid']);
      $character->setPlatoonId($row['platoon_id']);
      $character->setEnlistedDate($row['Enlisted']);
      $character->setAge($row['Age']);
      $character->setGender($row['Gender']);
      $character->setUnusedXp($row['UnusedXP']);
      $character->setAwarenessPoints($row['AwarenessPoints']);
      $character->setCoolPoints($row['CoolPoints']);
      $character->setExhaustionPoints($row['ExhaustionPoints']);
      $character->setFearPoints($row['FearPoints']);
      $character->setLeadershipPoints($row['LeadershipPoints']);
      $character->setPsychoPoints($row['PsychoPoints']);
      $character->setTraumaPoints($row['TraumaPoints']);
      $character->setMentalPoints($row['MentalPoints']);
      $character->setStatus($row['status']);
      $character->setStatusDescription($row['status_desc']);
      $character->setRankShort($row['rank_short']);
      $character->setRankLong($row['rank_long']);
      $character->setRankDescription($row['rank_desc']);
      $character->setRankId($row['rank_id']);
      $character->setSpecialtyName($row['specialty_name']);
      $character->setSpecialtyId($row['specialty_id']);
    }
    return $character;
  }

    /**
   *
   * @return Character[]
   */
  public function getActiveCharacters() {
    $characters = array();
    $sql = "SELECT c.id
              FROM {$_SESSION['table_prefix']}characters c
              WHERE c.status!='Dead' AND c.status!='Retired'
              ORDER BY c.lastname,c.forname";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $character = $this->getCharacter($row['id']);
      $characters[] = $character;
    }
    return $characters;
  }

  /**
   *
   * @param Mission $mission
   * @return Character[]
   */
  public function getCharacterIdsOnMission($mission) {
    $withOnMission = array();
    $sql = "SELECT character_id FROM {$_SESSION['table_prefix']}missions m WHERE mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $withOnMission[$row['character_id']] = TRUE;
    }
    return $withOnMission;
  }

  /**
   *
   * @param Mission $mission
   * @return Character[]
   */
  public function getCharactersOnMission($mission) {
    $withOnMission = array();
    $sql = "SELECT character_id FROM {$_SESSION['table_prefix']}missions m WHERE mission_id=:missionId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':missionId', $mission->getId(), PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $character = $this->getCharacter($row['character_id']);
      $withOnMission[] = $character;
    }
    return $withOnMission;
  }

  /**
   *
   * @return Specialty[]
   */
  public function getSpecialties() {
    $specialties = array ();
    $sql = "SELECT id, specialty_name FROM uscm_specialty_names ORDER BY specialty_name";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $specialty = new Specialty();
      $specialty->setId($row['id']);
      $specialty->setName($row['specialty_name']);
      $specialties[] = $specialty;
    }
    return $specialties;
  }

  /**
   *
   * @return Attribute[]
   */
  public function getAttributes() {
    $attributesql = "SELECT id, attribute_name FROM uscm_attribute_names ORDER BY id";
    $stmt = $this->db->prepare($attributesql);
    $stmt->execute();
    $attributes = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $attribute = new Attribute();
      $attribute->setId($row['id']);
      $attribute->setName($row['attribute_name']);
      $attributes[] = $attribute;
    }
    return $attributes;
  }

  /**
   *
   * @return Skill[]
   */
  public function getSkills() {
    $sql = "SELECT id, skill_name,optional FROM uscm_skill_names";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $skills = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $skill = new Skill();
      $skill->setId($row['id']);
      $skill->setName($row['skill_name']);
      $skills[] = $skill;
    }
    return $skills;
  }
}
