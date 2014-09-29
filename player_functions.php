<?php
Class Player {

  private $level = 0;
  private $playerPlatoon = 0;
  private $db = NULL;

  function __construct() {
    $this->level = $_SESSION['level'];
    $this->playerPlatoon = $_SESSION['platoon_id'];
    $this->db = getDatabaseConnection();
  }

  public function isAdmin() {
    return ($this->level == 3)?(TRUE):(FALSE);
  }

  public function isGm() {
    return ($this->level == 2)?(TRUE):(FALSE);
  }

  public function getAllPlayers() {
    if (!$this->isAdmin()) {
      return $this->getPlayersInPlatoon($this->playerPlatoon);
    }

    $playersql="SELECT Users.id,forname,lastname,name_short FROM Users
                  LEFT JOIN uscm_platoon_names pn ON pn.id=Users.platoon_id
                  ORDER BY platoon_id,lastname,forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPlayersInPlatoon($platoonId) {
    $playersql="SELECT Users.id,forname,lastname,name_short FROM Users
                  LEFT JOIN uscm_platoon_names pn ON pn.id=Users.platoon_id
                  WHERE platoon_id=:platoonid
                  ORDER BY platoon_id,lastname,forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->bindValue(':platoonid', $platoonId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPlatoons() {
    $platoonsql="SELECT id,name_long FROM uscm_platoon_names";
    if (!$this->isAdmin()) {
      $platoonsql .= " WHERE id=:platoonid";
    }
    $stmt = $this->db->prepare($platoonsql);
    $stmt->bindValue(':platoonid', $this->playerPlatoon, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
