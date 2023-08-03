<?php

//TODO: remove
function advantages($characterId, $onlyvisible = false) {
  $db = getDatabaseConnection();
  $advarray = array ();
  $visible = $onlyvisible ? " AND an.visible = 1" : "";
  $sql = "SELECT an.id, advantage_name
          FROM uscm_advantage_names an
          LEFT JOIN uscm_advantages a ON a.advantage_name_id=an.id
          LEFT JOIN uscm_characters c ON c.id=a.character_id
          WHERE a.character_id=:cid " . $visible . " ORDER BY advantage_name";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $advarray [$row ['id']] ['advantage_name'] = $row ['advantage_name'];
  }
  return $advarray;
}

//TODO: remove
function disadvantages($characterId, $onlyvisible = false) {
  $db = getDatabaseConnection();
  $disadvarray = array ();
  $sql = "SELECT dn.id, disadvantage_name
          FROM uscm_disadvantage_names dn
          LEFT JOIN uscm_disadvantages d ON d.disadvantage_name_id=dn.id
          LEFT JOIN uscm_characters c ON c.id=d.character_id
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
          if (!empty($charskillattrib[$reqid['table_name']]) && array_key_exists($reqid ['id'], $charskillattrib [$reqid ['table_name']]) &&
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
  $sql = "SELECT attribute_id,attribute_name, value
                  FROM uscm_characters c
                  LEFT JOIN uscm_attributes a ON a.character_id=c.id
                  LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
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
  $attribarray = array ();
  $attribsql = "SELECT attribute_id as id,value
          FROM uscm_attributes
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
  $certreqsql = "SELECT certificate_id, req_item,value,value_greater,table_name,name
                FROM uscm_certificate_requirements cr
                LEFT JOIN uscm_certificate_names cn ON cn.id=cr.certificate_id";
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
  $chosencertarray = array ();
  $chosencertsql = "SELECT certificate_name_id FROM uscm_certificates
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
  $chosencertarray = array ();
  $commendationssql = "SELECT medal_short,medal_glory FROM uscm_characters c
                    LEFT JOIN uscm_missions as missions
                      ON missions.character_id = c.id
                    LEFT JOIN uscm_medal_names as mn
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
  $chosencertarray = array ();
  $missionssql = "SELECT count(id) as missions FROM uscm_missions
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
  $skillarray = array ();
  $skillsql = "SELECT skill_name_id as id,value
          FROM uscm_skills
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
  $platooncertarray = array ();
  $platooncertsql = "SELECT certificate_id FROM uscm_platoon_certificates
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

function servedWith($characterid) {
	$characters = array ();
	$dbReference = getDatabaseConnection();
	$stmt = $dbReference->query("select concat(oc.forname, ' ', oc.lastname) as name,count(om.id) as missions, oc.status from uscm_missions as m join uscm_characters as c on m.character_id=c.id join uscm_missions as om on m.mission_id=om.mission_id and m.id!=om.id join uscm_characters as oc on om.character_id=oc.id where m.character_id=$characterid group by oc.id order by missions desc, status asc");

	while ( $character = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$characters [sizeof($characters)] = $character;
	}
	return $characters;
}

function setMedalsAndGloryOnCharacter(&$characters, $key, $character) {
  $medals = "";
  $glory = "";
  $characterId = $character ['cid'];
  $db = getDatabaseConnection();
  $sql = 'SELECT count(m.id) as missions FROM uscm_missions m
        LEFT JOIN uscm_mission_names mn ON mn.id=m.mission_id
                WHERE character_id=:cid AND mn.date < NOW()';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':cid', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $characters [$key] ['missions'] = $missions [0] ['missions'];

  $commendationssql = "SELECT medal_short,medal_glory FROM uscm_characters c
                LEFT JOIN uscm_missions as missions
                  ON missions.character_id = c.id
                LEFT JOIN uscm_medal_names as mn
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
  $traitarray = array ();
  $sql = "SELECT tn.id, trait_name
          FROM uscm_trait_names tn
          LEFT JOIN uscm_traits t ON t.trait_name_id=tn.id
          LEFT JOIN uscm_characters c ON c.id=t.character_id
          WHERE t.character_id=:cid ORDER BY trait_name";
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
  $sql = "SELECT DATE_FORMAT(date,'%y-%m-%d') as date,mission_name_short FROM uscm_mission_names LEFT JOIN uscm_missions as m on m.mission_id = uscm_mission_names.id WHERE character_id = :characterId ORDER BY date DESC LIMIT 1";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':characterId', $characterId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch();
}

?>
