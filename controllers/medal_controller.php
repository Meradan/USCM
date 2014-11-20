<?php
Class MedalController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  /**
   *
   * @return Medal[]
   */
  public function getMedals() {
    return $this->getMedalsWithConstraint("");
  }

  /**
   *
   * @return Medal[]
   */
  public function getUscmMedals() {
    $constraint = " WHERE foreign_medal = 0";
    return $this->getMedalsWithConstraint($constraint);
  }

  /**
   *
   * @return Medal[]
   */
  public function getForeignMedals() {
    $constraint = " WHERE foreign_medal = 1";
    return $this->getMedalsWithConstraint($constraint);
  }

  /**
   *
   * @param string $constraint Where clause to use in query
   * @return Medal[]
   */
  private function getMedalsWithConstraint($constraint) {
    $sql = "SELECT id, medal_name, medal_short, medal_glory, description, foreign_medal " .
        "FROM uscm_medal_names";
    if ($constraint != "") {
      $sql = $sql . $constraint;
    }
    $sql = $sql . " ORDER BY medal_glory DESC, medal_name DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $medals = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $medal = new Medal();
      $medal->setId($row['id']);
      $medal->setName($row['medal_name']);
      $medal->setShortName($row['medal_short']);
      $medal->setGlory($row['medal_glory']);
      $medal->setDescription($row['description']);
      $medal->setForeign($row['foreign_medal']);
      $medals[] = $medal;
    }
    return $medals;
  }

  /**
   *
   * @param int $medalId
   * @return Medal
   */
  public function getMedal($medalId) {
    $sql = "SELECT medal_name, medal_short, medal_glory, description, foreign_medal, count(*) as howmany " .
        "FROM uscm_medal_names " .
        "WHERE id = :medalId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':medalId', $medalId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $medal = new Medal();
    if ($row['howmany'] == 1) {
      $medal->setId($medalId);
      $medal->setName($row['medal_name']);
      $medal->setShortName($row['medal_short']);
      $medal->setGlory($row['medal_glory']);
      $medal->setDescription($row['description']);
      $medal->setForeign($row['foreign_medal']);
    }
    return $mission;
  }
}
