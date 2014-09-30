<?php

class Character {
  private $db = NULL;
  private $characterId = NULL;
  private $userId = NULL;
  private $givenName = NULL;
  private $surname = NULL;
  private $platoonId = NULL;
  private $enlisted = NULL;
  private $age = NULL;
  private $gender = NULL;
  private $unusedXp = NULL;
  private $awarenessPoints = NULL;
  private $coolPoints = NULL;
  private $exhaustionPoints = NULL;
  private $fearPoints = NULL;
  private $leadershipPoints = NULL;
  private $psychoPoints = NULL;
  private $traumaPoints = NULL;
  private $mentalPoints = NULL;
  private $status = NULL;
  private $statusDesc = NULL;
  private $rankLong = NULL;
  private $rankShort = NULL;
  private $rankDesc = NULL;
  private $specialtyName = NULL;

  function __construct($characterId = NULL) {
    $this->characterId = $characterId;
    $this->db = getDatabaseConnection();
  }

  public function loadData() {
    if ($this->characterId == NULL) {
      return;
    }
    $sql = "SELECT userid, platoon_id, forname, lastname, Enlisted, Age, Gender, UnusedXP,
        AwarenessPoints, CoolPoints, ExhaustionPoints, FearPoints, LeadershipPoints, PsychoPoints,
        TraumaPoints, MentalPoints, status, status_desc, specialty_name
        rank_short, rank_long, rank_desc, count(*) as howmany
        FROM uscm_characters
        LEFT JOIN uscm_ranks ON uscm_characters.id = uscm_ranks.character_id
        LEFT JOIN uscm_rank_names ON  uscm_ranks.rank_id = uscm_rank_names.id
        LEFT JOIN uscm_specialty ON uscm_characters.id = uscm_specialty.character_id
        LEFT JOIN uscm_specialty_names ON  uscm_specialty.rank_id = uscm_specialty_names.id
        WHERE uscm_characters.id = :cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row ['howmany'] == 1) {
      $this->givenName = $row ['forname'];
      $this->surname = $row ['lastname'];
      $this->userId = $row ['userid'];
      $this->platoonId = $row ['platoon_id'];
      $this->enlisted = $row ['Enlisted'];
      $this->age = $row ['Age'];
      $this->gender = $row ['Gender'];
      $this->unusedXp = $row ['UnusedXP'];
      $this->awarenessPoints = $row ['AwarenessPoints'];
      $this->coolPoints = $row ['CoolPoints'];
      $this->exhaustionPoints = $row ['ExhaustionPoints'];
      $this->fearPoints = $row ['FearPoints'];
      $this->leadershipPoints = $row ['LeadershipPoints'];
      $this->psychoPoints = $row ['PsychoPoints'];
      $this->traumaPoints = $row ['TraumaPoints'];
      $this->mentalPoints = $row ['MentalPoints'];
      $this->status = $row ['status'];
      $this->statusDesc = $row ['status_desc'];
      $this->rankShort = $row ['rank_short'];
      $this->rankLong = $row ['rank_long'];
      $this->rankDesc = $row ['rank_desc'];
      $this->specialtyName = $row ['specialty_name'];
    }
  }

  public function getGivenName() {
    return $this->givenName;
  }

  public function getSurename() {
    return $this->surname;
  }

  public function getName() {
    return $this->givenName . " " . $this->surname;
  }

  public function getEnlistedDate() {
    return $this->enlisted;
  }

  public function getAge() {
    return $this->age;
  }

  public function getGender() {
    return $this->gender;
  }

  public function getUnusedXp() {
    return $this->unusedXp;
  }

  public function getAwarenessPoints() {
    return $this->awerenessPoints;
  }

  public function getCoolPoints() {
    return $this->coolPoints;
  }

  public function getExhaustionPoints() {
    return $this->exhaustionPoints;
  }

  public function getFearPoints() {
    return $this->fearPoints;
  }

  public function getLeadershipPoints() {
    return $this->leadershipPoints;
  }

  public function getPyschoPoints() {
    return $this->psychoPoints;
  }

  public function getTraumaPoints() {
    return $this->traumaPoints;
  }

  public function getMentalPoints() {
    return $this->mentalPoints;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getStatusDescription() {
    return $this->statusDesc;
  }

  public function getRankShort() {
    return $this->rankShort;
  }

  public function getRankLong() {
    return $this->rankLong;
  }

  public function getSpecialtyName() {
    return $this->specialtyName;
  }

  public function getAwareness() {
    $sql = "SELECT (value * 2)  as value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          LEFT JOIN uscm_characters c ON c.id=a.character_id
          WHERE an.attribute_name='Perception' AND a.character_id=:cid";
    $stmt = $this->db->prepare($advsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ['value'];
  }

  public function getPlayer() {
    if ($this->userId != NULL || $this->characterId == NULL) {
      return $this->userId;
    }
    $db = getDatabaseConnection();
    $sql = "SELECT Users.id as userid FROM Users
                        LEFT JOIN uscm_characters as c ON c.userid=Users.id
                        WHERE c.id=:cid";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $userId = $row ['userid'];
    $this->userId = $userId;
    return $userId;
  }

  public function getPlatoon() {
    if ($this->userId != NULL || $this->platoonId == NULL) {
      return $this->platoonId;
    }
    $sql = "SELECT platoon_id FROM uscm_characters WHERE id=:cid";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $platoonId = $row ['platoon_id'];
    $this->platoonId = $platoonId;
    return $platoonId;
  }
}
