<?php

function getSkillsGrouped() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT sn.id,skill_name,optional FROM " . $tablePrefix . "skill_names sn
                    LEFT JOIN ". $tablePrefix . "skill_groups sg on sn.skill_group_id=sg.id
                    ORDER BY sg.id,sn.skill_name";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTraits() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT tn.id,trait_name FROM " . $tablePrefix . "trait_names tn ORDER BY tn.trait_name";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAdvantages() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT id,advantage_name,value FROM " . $tablePrefix . "advantage_names ORDER BY advantage_name";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getDisadvantages() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT id,disadvantage_name,value FROM " . $tablePrefix . "disadvantage_names ORDER BY disadvantage_name";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getCertificates() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT id,name FROM " . $tablePrefix . "certificate_names ORDER BY name";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMedals() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array ();
  $medalsql = "SELECT id, medal_short,medal_name,medal_glory,description
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
  $chosencertarray = array ();
  $foreignmedalsql = "SELECT medal_short,medal_name,medal_glory,description
          FROM " . $tablePrefix . "medal_names
          WHERE " . $tablePrefix . "medal_names.foreign_medal=1
          ORDER BY medal_glory ASC";

  $stmt = $db->prepare($foreignmedalsql);
  $stmt->execute();
  $medals = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $medals;
}

function getPlatoons_() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array ();
  $platoonsql = "SELECT id,name_short,name_long FROM " . $tablePrefix . "platoon_names";

  $stmt = $db->prepare($platoonsql);
  $stmt->execute();
  $platoons = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $platoons;
}

function getSpecialties_() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $specialtysql = "SELECT id, specialty_name FROM " . $tablePrefix . "specialty_names
      ORDER BY specialty_name";
  $stmt = $db->prepare($specialtysql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
