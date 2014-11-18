<?php

function advantages($characterId, $onlyvisible = false) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $advarray = array ();
  $visible = $onlyvisible ? " AND an.visible = 1" : "";
  $sql = "SELECT an.id, advantage_name
          FROM " . $tablePrefix . "advantage_names an
          LEFT JOIN " . $tablePrefix . "advantages a ON a.advantage_name_id=an.id
          LEFT JOIN " . $tablePrefix . "characters c ON c.id=a.character_id
          WHERE a.character_id=:cid " . $visible . " ORDER BY advantage_name";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $advarray [$row ['id']] ['advantage_name'] = $row ['advantage_name'];
  }
  return $advarray;
}

function disadvantages($characterId, $onlyvisible = false) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $disadvarray = array ();
  $sql = "SELECT dn.id, disadvantage_name
          FROM " . $tablePrefix . "disadvantage_names dn
          LEFT JOIN " . $tablePrefix . "disadvantages d ON d.disadvantage_name_id=dn.id
          LEFT JOIN " . $tablePrefix . "characters c ON c.id=d.character_id
          WHERE d.character_id=:cid ORDER BY disadvantage_name";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $disadvarray [$row ['id']] ['disadvantage_name'] = $row ['disadvantage_name'];
  }
  return $disadvarray;
}

/**
 *
 * @param integer $characterId
 * @param integer $platoonId
 * @return array
 */
function certificates($characterId, $platoonId) {
  $chosencertarray = getCertForCharacter($characterId);
  $platooncertarray = getCertForPlatoon($platoonId);
  $skillarray = getSkillsForCharacter($characterId);
  $attribarray = getAttributesForCharacter($characterId);

  $charskillattrib = array ();
  foreach ( $skillarray as $id => $value ) {
    $charskillattrib ['skill_names'] [$id] = $value;
  }
  foreach ( $attribarray as $id => $value ) {
    $charskillattrib ['attribute_names'] [$id] = $value;
  }
  $cert = getCertificateRequirements();

  // print_r($cert);
  $certificatearray = array ();
  foreach ( $cert as $id => $req ) {
    $req_met = FALSE;
    // echo "cert test ".$id." ";
    // print_r($req);
    if (in_array($id, $platooncertarray) || in_array($id, $chosencertarray)) {
      $has_req = FALSE;
      foreach ( $req as $reqid ) {
        // echo $reqid['id'] . "<br>";
        // print_r($charskillattrib[$reqid['table_name']]) . "<br>";
        //
        // echo "testing ".$charskillattrib[$reqid['table_name']][$reqid['id']]." against ".$reqid['value']." ";
        // print "\n<br>";
        if ($reqid ['value_greater'] == "1") {
          if (array_key_exists($reqid ['id'], $charskillattrib [$reqid ['table_name']]) &&
               $charskillattrib [$reqid ['table_name']] [$reqid ['id']] >= $reqid ['value']) {
            $has_req = TRUE;
          } else {
            $has_req = FALSE;
            break;
          }
        } else {
          if ($charskillattrib [$reqid ['table_name']] [$reqid ['id']] <= $reqid ['value']) {
            $has_req = TRUE;
          } else {
            $has_req = FALSE;
            break;
          }
        }
      }
      $req_met = $has_req;
    }
    if ($req_met) {
      $certificatearray [$id] ['id'] = $id;
      reset($req);
      $name = current($req);
      $certificatearray [$id] ['name'] = $name ['name'];
    }
  }
  // print_r($certificatearray);
  // exit;
  return $certificatearray;
}

/**
 * Get an array containing attributes for the character
 *
 * @param integer $characterId
 * @return Array <Characters>
 */
function characterAttributes($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $sql = "SELECT attribute_id,attribute_name, value
                  FROM " . $tablePrefix . "characters c
                  LEFT JOIN " . $tablePrefix . "attributes a ON a.character_id=c.id
                  LEFT JOIN " . $tablePrefix . "attribute_names an ON an.id=a.attribute_id
                  WHERE c.id=:cid ORDER BY attribute_name";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $attributearray [$row ['attribute_id']] ['value'] = $row ['value'];
    $attributearray [$row ['attribute_id']] ['attribute_name'] = $row ['attribute_name'];
  }
  return $attributearray;
}

function getAttributesForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $attribarray = array ();
  $attribsql = "SELECT attribute_id as id,value
          FROM " . $tablePrefix . "attributes
          WHERE character_id=:cid";
  $stmt = $db->prepare($attribsql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $attribarray [$row ['id']] = $row ['value'];
  }
  return $attribarray;
}

function getCertificateRequirements() {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $certreqsql = "SELECT certificate_id, req_item,value,value_greater,table_name,name
                FROM " . $tablePrefix . "certificate_requirements cr
                LEFT JOIN " . $tablePrefix . "certificate_names cn ON cn.id=cr.certificate_id";
  $cert = array ();
  $stmt = $db->prepare($certreqsql);
  $stmt->execute();
  /*
   * Array
   * (
   * [1] => Array //certificate id
   * (
   * [1] => Array //req_item id
   * (
   * [value] => 4
   * [value_greater] => 1
   * [table] => skill_names
   * )
   * )
   * }
   */
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $cert [$row ['certificate_id']] [$row ['req_item']] ['id'] = $row ['req_item'];
    $cert [$row ['certificate_id']] [$row ['req_item']] ['value'] = $row ['value'];
    $cert [$row ['certificate_id']] [$row ['req_item']] ['value_greater'] = $row ['value_greater'];
    $cert [$row ['certificate_id']] [$row ['req_item']] ['name'] = $row ['name'];
    $cert [$row ['certificate_id']] [$row ['req_item']] ['table_name'] = $row ['table_name'];
  }
  return $cert;
}

function getCertForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array ();
  $chosencertsql = "SELECT certificate_name_id FROM " . $tablePrefix . "certificates
                    WHERE character_id=:cid";
  $stmt = $db->prepare($chosencertsql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $chosencertarray [] = $row ['certificate_name_id'];
  }
  return $chosencertarray;
}

function getCommendationsForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array ();
  $commendationssql = "SELECT medal_short,medal_glory FROM " . $tablePrefix . "characters c
                    LEFT JOIN " . $tablePrefix . "missions as missions
                      ON missions.character_id = c.id
                    LEFT JOIN " . $tablePrefix . "medal_names as mn
                      ON mn.id = missions.medal_id
                    WHERE character_id=:cid ORDER BY medal_glory DESC";
  $stmt = $db->prepare($commendationssql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  $commendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $commendations;
}

function getNumberOfMissionsForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $chosencertarray = array ();
  $missionssql = "SELECT count(id) as missions FROM " . $tablePrefix . "missions
                  WHERE character_id=:cid";
  $stmt = $db->prepare($missionssql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  $row = $stmt->fetch();
  $missions = $row ['missions'];
  return $missions;
}

function getSkillsForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $skillarray = array ();
  $skillsql = "SELECT skill_name_id as id,value
          FROM " . $tablePrefix . "skills
          WHERE character_id=:cid";
  $stmt = $db->prepare($skillsql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $skillarray [$row ['id']] = $row ['value'];
  }
  return $skillarray;
}

function getCertForPlatoon($platoonId) {
  $db = getDatabaseConnection();
  $tablePrefix = getTablePrefix();
  $platooncertarray = array ();
  $platooncertsql = "SELECT certificate_id FROM " . $tablePrefix . "platoon_certificates
                    WHERE platoon_id=:platoonid";

  $stmt = $db->prepare($platooncertsql);
  $stmt->bindValue(':platoonid', $platoonId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $platooncertarray [] = $row ['certificate_id'];
  }
  return $platooncertarray;
}

function listCharacters($charactersql, $sortType) {
  // $sortType is either "alive", "dead", "retired" or "glory"
  $characters = array ();
  $medals = "";
  $glory = "";
  $dbReference = getDatabaseConnection();

  $stmt = $dbReference->query($charactersql);
  while ( $character = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $characters [sizeof($characters)] = $character;
  }

  foreach ( $characters as $key => $character ) {
    setMedalsAndGloryOnCharacter($characters, $key, $character);
  }
  // Obtain a list of columns
  $missions = array ();
  $rank = array ();
  $glory = array ();
  $medals = array ();
  foreach ( $characters as $key => $row ) {
    // var_dump($row);
    // echo "<br>";
    $rank [$key] = $row ['rank_id'];
    $missions [$key] = $row ['missions'];
    $glory [$key] = $row ['glory'];
    $medals [$key] = $row ['medals'];
  }

  // Sort the data with volume descending, edition ascending
  // Add $data as the last parameter, to sort by the common key
  if ($sortType == "alive") {
    array_multisort($rank, SORT_DESC, $missions, SORT_DESC, $characters);
  } elseif ($sortType == "dead" || $sortType == "retired") {
    array_multisort($missions, SORT_DESC, $rank, SORT_DESC, $glory, SORT_DESC, $characters);
  } elseif ($sortType == "glory") {
    array_multisort($glory, SORT_DESC, $missions, SORT_DESC, $rank, SORT_DESC, $characters);
  }
  return $characters;
}

function setMedalsAndGloryOnCharacter(&$characters, $key, $character) {
  $medals = "";
  $glory = "";
  $characterId = $character ['cid'];
  $db = getDatabaseConnection();
  $tablePrefix = $_SESSION ['table_prefix'];
  $sql = 'SELECT count(m.id) as missions FROM ' . $tablePrefix . 'missions m
        LEFT JOIN ' . $tablePrefix . 'mission_names mn ON mn.id=m.mission_id
                WHERE character_id=:cid AND mn.date < NOW()';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $characters [$key] ['missions'] = $missions [0] ['missions'];

  $commendationssql = "SELECT medal_short,medal_glory FROM {$_SESSION['table_prefix']}characters c
                LEFT JOIN {$_SESSION['table_prefix']}missions as missions
                  ON missions.character_id = c.id
                LEFT JOIN {$_SESSION['table_prefix']}medal_names as mn
                  ON mn.id = missions.medal_id
                WHERE character_id=:cid ORDER BY medal_glory DESC";
  $stmt = $db->prepare($commendationssql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $commendations = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    if ($commendations ['medal_short'] != "") {
      $medals = $medals . " " . $commendations ['medal_short'];
    }
    $glory = $glory + $commendations ['medal_glory'];
  }
  $characters [$key] ['medals'] = ($medals != "") ? ($medals) : ("-");
  $characters [$key] ['glory'] = ($glory != "") ? ($glory) : ("0");
}

function traits($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = $_SESSION ['table_prefix'];
  $traitarray = array ();
  $sql = "SELECT tn.id, trait_name
          FROM " . $tablePrefix . "trait_names tn
          LEFT JOIN " . $tablePrefix . "traits t ON t.trait_name_id=tn.id
          LEFT JOIN " . $tablePrefix . "characters c ON c.id=t.character_id
          WHERE t.character_id=cid ORDER BY trait_name";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $traitarray [$row ['id']] ['trait_name'] = $row ['trait_name'];
  }
  return $traitarray;
}

function lastMissionForCharacter($characterId) {
  $db = getDatabaseConnection();
  $tablePrefix = $_SESSION ['table_prefix'];
  $sql = "SELECT DATE_FORMAT(date,'%y-%m-%d') as date,mission_name_short FROM " . $tablePrefix . "mission_names LEFT JOIN " . $tablePrefix . "missions as m on m.mission_id = " . $tablePrefix . "mission_names.id WHERE character_id = :characterId ORDER BY date DESC LIMIT 1";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':characterId', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch();
}

?>
