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
    $sql = "SELECT sn.id as simulationid,sn.name as simulation_name ".
        "FROM uscm_simulation_names ORDER BY sn.id DESC";
    $stmt = $this->db->prepare($sql);
    try {
      $stmt->execute();
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $simulation = new Simulation();
        $simulation->setId($row['simulationid']);
        $simulation->setName($row['simulation_name']);
        $simulations[] = $simulation;
      }
    } catch (PDOException $e) {
      print "Error fetching simulations " . $e->getMessage() . "<br>";
    }
    return $simulations;
  }
}