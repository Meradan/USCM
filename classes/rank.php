<?php
Class Rank extends DbEntity {
  private $name = NULL;
  private $shortName = NULL;
  private $description = NULL;

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

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($desc) {
    $this->description = $desc;
  }
}
