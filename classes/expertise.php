<?php
Class Expertise extends DbEntity {
  private $name = NULL;
  private $expertiseGroupId = NULL;
  private $value = NULL;

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }
  
    public function getExpertiseGroupId() {
    return $this->expertiseGroupId;
  }

  public function setExpertiseGroupId($expertiseGroupId) {
    $this->expertiseGroupId = $expertiseGroupId;
  }

  public function getValue() {
    return $this->value;
  }

  public function setValue($value) {
    $this->value = $value;
  }
}
