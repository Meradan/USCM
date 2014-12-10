<?php
Class Advantage extends DbEntity {
  private $name = NULL;
  private $description = NULL;
  private $value = NULL;
  private $visible = NULL;

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

  public function getValue() {
    return $this->value;
  }

  public function setValue($value) {
    $this->value = $value;
  }
  public function getVisible() {
    return $this->visible;
  }

  public function setVisible($visible) {
    $this->visible = $visible;
  }
}
