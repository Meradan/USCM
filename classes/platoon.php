<?php

class Platoon {
  private $id = NULL;
  private $longName = "";
  private $shortName = "";
  private $certificates = NULL;

  public function getName() {
    return $this->longName;
  }

  public function setName($name) {
    $this->longName = $name;
  }

  public function getShortName() {
    return $this->shortName;
  }

  public function setShortName($name) {
    $this->shortName = $name;
  }

  public function getId() {
    return $this->id;
  }
  public function setId($id) {
    $this->id = $id;
  }

  public function getCertificates() {
    return $this->certificates;
  }
  public function setCertificates($certificates) {
    $this->certificates = $certificates;
  }
}
