<?php
Class PlatoonController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  /**
   *
   * @return Platoon[]
   */
  public function getPlatoons() {
    $platoons = array ();
    $sql = "SELECT id, name_short, name_long
                FROM uscm_platoon_names";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $platoon = new Platoon();
      $platoon->setId($row['id']);
      $platoon->setName($row['name_long']);
      $platoon->setShortName($row['name_short']);
      $certsql = "SELECT certificate_id FROM uscm_platoon_certificates
        WHERE platoon_id = :platoonid";
      $stmt_cert = $this->db->prepare($certsql);
      $stmt_cert->execute(array (':platoonid' => $row['id']
      ));
      $platoonCerts = $stmt_cert->fetchAll(PDO::FETCH_ASSOC);
      $platoon->setCertificates($platoonCerts);
      $platoons[] = $platoon;
    }
    return $platoons;
  }
}
