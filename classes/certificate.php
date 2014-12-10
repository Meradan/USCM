<?php
Class Certificate extends DbEntity {
  private $name = NULL;
  private $description = NULL;

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;
  }
}
