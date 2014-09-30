<?php
class Player {
  private $level = 0;
  private $playerPlatoon = 0;
  private $db = NULL;
  private $playerId = NULL;
  private $givenName = NULL;
  private $nickname = NULL;
  private $surname = NULL;
  private $emailaddress = NULL;
  private $use_nickname = NULL;
  private $platoon_id = NULL;
  private $logintime = NULL;
  private $lastlogintime = NULL;

  function __construct($playerId = NULL) {
    $this->level = $_SESSION ['level'];
    $this->playerPlatoon = $_SESSION ['platoon_id'];
    $this->db = getDatabaseConnection();
    if ($playerId == NULL) {
      $this->playerId = $_SESSION ['user_id'];
    } else {
      $this->playerId = $playerId;
    }
  }

  public function loadData() {
    if ($this->playerId == NULL) {
      return;
    }
    $playersql = "SELECT forname, nickname, lastname, emailaddress, use_nickname, platoon_id,
        logintime, lastlogintime, count(*) as howmany FROM Users WHERE id = :userid";
    $stmt = $this->db->prepare($playersql);
    $stmt->bindValue(':userid', $this->playerId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row ['howmany'] == 1) {
      $this->givenName = $row ['forname'];
      $this->nickname = $row ['nickname'];
      $this->surname = $row ['lastname'];
      $this->emailaddress = $row ['emailaddress'];
      $this->use_nickname = $row ['use_nickname'];
      $this->platoon_id = $row ['platoon_id'];
      $this->logintime = $row ['logintime'];
      $this->lastlogintime = $row ['lastlogintime'];
    }
  }

  public function getPlayerId() {
    return $this->playerId;
  }

  public function getGivenName() {
    return $this->givenName;
  }

  public function getSurname() {
    return $this->surname;
  }

  public function getNickname() {
    return $this->nickname;
  }

  public function getEmailaddress() {
    return $this->emailaddress;
  }

  public function isAdmin() {
    return ($this->level == 3) ? (TRUE) : (FALSE);
  }

  public function isGm() {
    return ($this->level == 2) ? (TRUE) : (FALSE);
  }

  public function getAllPlayers() {
    if (!$this->isAdmin()) {
      return $this->getPlayersInPlatoon($this->playerPlatoon);
    }

    $playersql = "SELECT Users.id,forname,lastname,name_short FROM Users
                  LEFT JOIN uscm_platoon_names pn ON pn.id=Users.platoon_id
                  ORDER BY platoon_id,lastname,forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPlayersInPlatoon($platoonId) {
    $playersql = "SELECT Users.id,forname,lastname,name_short FROM Users
                  LEFT JOIN uscm_platoon_names pn ON pn.id=Users.platoon_id
                  WHERE platoon_id=:platoonid
                  ORDER BY platoon_id,lastname,forname";
    $stmt = $this->db->prepare($playersql);
    $stmt->bindValue(':platoonid', $platoonId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPlatoons() {
    $platoonsql = "SELECT id,name_long FROM uscm_platoon_names";
    if (!$this->isAdmin()) {
      $platoonsql .= " WHERE id=:platoonid";
    }
    $stmt = $this->db->prepare($platoonsql);
    $stmt->bindValue(':platoonid', $this->playerPlatoon, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
