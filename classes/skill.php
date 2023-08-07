<?php
Class Skill extends DbEntity {
  private $name = NULL;
  private $optional = NULL;
  private $skillGroupId = NULL;
  private $defaultValue = NULL;
  private $description = NULL;

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getOptional() {
    return $this->optional;
  }

  public function setOptional($optional) {
    $this->optional = $optional;
  }

  public function getSkillGroupId() {
    return $this->skillGroupId;
  }

  public function setSkillGroupId($skillGroupId) {
    $this->skillGroupId = $skillGroupId;
  }

  public function getDefaultValue() {
    return $this->defaultValue;
  }

  public function setDefaultValue($defaultValue) {
    $this->defaultValue = $defaultValue;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;
  }
}
