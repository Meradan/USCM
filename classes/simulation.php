<?php
Class Simulation {
  private $db = NULL;
  private $id = NULL;
  private $name = "";
  private $description = "";
  
  function __construct($simulationId = NULL) {
    $this->id = $simulationId;
    $this->db = getDatabaseConnection();
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function setId($id) {
    $this->id = $id;
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function setName($name) {
    $this->name = $name;
  }
  
  public function getFullName() {
    return "S$this->id: $this->name";
  }
  
  public function getDescription() {
    return $this->description;
  }
  
  public function setDescription($desc) {
    $this->description = $desc;
  }
  
  public function getHighScores($number) {
	$scores = array();
	$sql = "SELECT p.name_short AS platoon,CONCAT_WS(' ', c.forname, c.lastname) AS charactername,CONCAT_WS(' ', u.forname,u.lastname) as playername,u.nickname,u.use_nickname,score,scoretime FROM uscm_simulation_scores JOIN uscm_characters AS c ON character_id=c.id JOIN uscm_platoon_names as p ON c.platoon_id=p.id LEFT JOIN Users as u ON c.userid = u.id WHERE mission_id=:mId ORDER BY score desc,scoretime desc LIMIT :num";
	$stmt = $this->db->prepare($sql);
	$stmt->bindValue(':mId', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':num', $number, PDO::PARAM_INT);
    try {
      $stmt->execute();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $score = new Score();
        $score->missionid = $this->id;
        $score->charactername = $row['charactername'];
        $score->platoon = $row['platoon'];
		$score->points = $row['score'];
		$score->scoretime = date_create($row['scoretime']);
		if ($row['use_nickname']==1)
		{
			$score->playername = $row['nickname'];
		} else {
			$score->playername = $row['playername'];
		}
        $scores[] = $score;
      }
    } catch (PDOException $e) {
    }
	return $scores;
  }
  
  public function getLastScores($number) {
	$scores = array();
	$sql = "SELECT p.name_short AS platoon,CONCAT_WS(' ', c.forname, c.lastname) AS charactername,CONCAT_WS(' ', u.forname,u.lastname) as playername,u.nickname,u.use_nickname,score,scoretime FROM uscm_simulation_scores JOIN uscm_characters AS c ON character_id=c.id JOIN uscm_platoon_names as p ON c.platoon_id=p.id LEFT JOIN Users as u ON c.userid = u.id WHERE mission_id=:mId ORDER BY scoretime desc LIMIT :num";
	$stmt = $this->db->prepare($sql);
	$stmt->bindValue(':mId', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':num', $number, PDO::PARAM_INT);
    try {
      $stmt->execute();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $score = new Score();
        $score->missionid = $this->id;
        $score->charactername = $row['charactername'];
        $score->platoon = $row['platoon'];
		$score->points = $row['score'];
		$score->scoretime = date_create($row['scoretime']);
		if ($row['use_nickname']==1)
		{
			$score->playername = $row['nickname'];
		} else {
			$score->playername = $row['playername'];
		}
        $scores[] = $score;
      }
    } catch (PDOException $e) {
    }
	return $scores;
  }
}