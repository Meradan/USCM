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
      $platoon->setCertificates($this->platoonCertificates($platoon));
      $platoons[] = $platoon;
    }
    return $platoons;
  }

  /**
   *
   * @param Platoon $platoon
   * @return Certificate
   */
  private function platoonCertificates($platoon) {
    $certsql = "SELECT certificate_id, name, description FROM uscm_platoon_certificates pc
        INNER JOIN uscm_certificate_names cn on cn.id = certificate_id
        WHERE platoon_id = :platoonid";
    $stmt = $this->db->prepare($certsql);
    $stmt->bindValue(':platoonid', $platoon->getId(), PDO::PARAM_INT);
    $stmt->execute();
    $certificates = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $certificate = new Certificate();
      $certificate->setId($row['certificate_id']);
      $certificate->setName($row['name']);
      $certificate->setDescription($row['description']);
      $certificates[] = $certificate;
    }
    return $certificates;
  }
}
