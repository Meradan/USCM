<?php
Class Medal extends DbEntity {
  private $name = NULL;
  private $shortName = NULL;
  private $glory = NULL;
  private $description = NULL;
  private $foreign = NULL;

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getShortName() {
    return $this->shortName;
  }

  public function setShortName($name) {
    $this->shortName = $name;
  }

  public function getGlory() {
    return $this->glory;
  }

  public function setGlory($glory) {
    $this->glory = $glory;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($desc) {
    $this->description = $desc;
  }

  public function getForeign() {
    return $this->foreign;
  }

  public function setForeign($foreign) {
    $this->foreign = $foreign;
  }
}
