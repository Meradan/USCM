<?php
class SimulationController {
  private $db = NULL;
  
  function __construct() {
    $this->db = getDatabaseConnection();
  }
  
  /**
   *
   * @return Simulation[]
   */
  function getSimulations() {
    $simulations = array();
    $sql = "SELECT id as simulationid,name as simulation_name,description ".
        "FROM uscm_simulation_names ORDER BY id DESC";
    $stmt = $this->db->prepare($sql);
    try {
      $stmt->execute();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $simulation = new Simulation();
        $simulation->setId($row['simulationid']);
        $simulation->setName($row['simulation_name']);
		$simulation->setDescription($row['description']);
        $simulations[] = $simulation;
      }
    } catch (PDOException $e) {
      print "Error fetching simulations " . $e->getMessage() . "<br>";
    }
    return $simulations;
  }
}