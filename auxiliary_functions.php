<?php

function getAttributes() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $attributesql="SELECT id, attribute_name FROM " . $tablePrefix . "attribute_names";
  $stmt = $db->prepare($attributesql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRanks() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array();
  $ranksql="SELECT id,rank_long,rank_short FROM " . $tablePrefix . "rank_names
          ORDER BY id DESC";
  $stmt = $db->prepare($ranksql);
  $stmt->execute();
  $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $ranks;
}

function getSkills() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $skillssql="SELECT id, skill_name,optional FROM " . $tablePrefix . "skill_names";
  $stmt = $db->prepare($skillssql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMedals() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array();
  $medalsql="SELECT medal_short,medal_name,medal_glory,description
          FROM " . $tablePrefix . "medal_names
          WHERE " . $tablePrefix . "medal_names.foreign_medal=0
          ORDER BY medal_glory ASC";
  $stmt = $db->prepare($medalsql);
  $stmt->execute();
  $medals = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $medals;
}

function getForeignMedals() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array();
  $foreignmedalsql="SELECT medal_short,medal_name,medal_glory,description
          FROM " . $tablePrefix . "medal_names
          WHERE " . $tablePrefix . "medal_names.foreign_medal=1
          ORDER BY medal_glory ASC";

  $stmt = $db->prepare($foreignmedalsql);
  $stmt->execute();
  $medals = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $medals;
}

function getPlatoons() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array();
  $platoonsql="SELECT id,name_short,name_long FROM " . $tablePrefix . "platoon_names";

  $stmt = $db->prepare($platoonsql);
  $stmt->execute();
  $platoons = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $platoons;
}

function getSpecialties () {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $specialtysql = "SELECT id, specialty_name FROM " . $tablePrefix . "specialty_names
      ORDER BY specialty_name";
  $stmt = $db->prepare($specialtysql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
