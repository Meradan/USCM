<?php
function getRanks() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array();
  $ranksql="SELECT rank_long,rank_short FROM " . $tablePrefix . "rank_names
          ORDER BY id DESC";
  $stmt = $db->prepare($ranksql);
  $stmt->execute();
  $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $ranks;
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
