<?php

/*
 * Functions handling characters should go in here
 *
 */
session_start();
require_once "functions.php";
$characterController = new CharacterController();
$db = getDatabaseConnection();
if ($_GET['action'] == "update_character") {
  /*
   * Updates a characters stats and skills
   *
   */
  $remove_attributes = array ();
  $add_attributes = array ();
  $updated_attributes = array ();
  $old_attributes = array ();
  $remove_skills = array ();
  $add_skills = array ();
  $updated_skills = array ();
  $old_skills = array ();
  $remove_traits = array ();
  $add_traits = array ();
  $updated_traits = array ();
  $old_traits = array ();
  $remove_advs = array ();
  $add_advs = array ();
  $updated_advs = array ();
  $old_advs = array ();
  $remove_disadvs = array ();
  $add_disadvs = array ();
  $updated_disadvs = array ();
  $old_disadvs = array ();
  $remove_certificate = array ();
  $add_certificate = array ();
  $updated_certificate = array ();
  $old_certificate = array ();
  $character_id = $_POST['character'];
  $table = $_SESSION['table_prefix'];
  $character = $characterController->getCharacter($character_id);

  // updates character basic stats
  $player = $_POST['player'];
  $platoon = $_POST['platoon'];
  $givenname = $_POST['forname'];
  $lastname = $_POST['lastname'];
  $enlisted = $_POST['enlisted'];
  $age = $_POST['age'];
  $gender = $_POST['gender'];
  $xp = $_POST['xp'];
  $ap = $_POST['ap'];
  $cp = $_POST['cp'];
  $ep = $_POST['ep'];
  $fp = $_POST['fp'];
  $lp = $_POST['lp'];
  $pp = $_POST['pp'];
  $tp = $_POST['tp'];
  $mp = $_POST['mp'];
  $status = $_POST['status'];
  $status_desc = $_POST['status_desc'];
  $cbalien = 0;
  $cbgrey = 0;
  $cbpredator = 0;
  $cbai = 0;
  $cbarachnid = 0;
  if (isset($_POST['cbalien'])) { $cbalien = 1; }
  if (isset($_POST['cbgrey'])) { $cbgrey = 1; }
  if (isset($_POST['cbpredator'])) { $cbpredator = 1; }
  if (isset($_POST['cbai'])) { $cbai = 1; }
  if (isset($_POST['cbarachnid'])) { $cbarachnid = 1; }
  
  $charactersql = "UPDATE {$table}characters SET userid=:playerid,
                         platoon_id=:platoonid,
                         forname=:givenname,
                         lastname=:lastname,
                         Enlisted=:enlisted,
                         Age=:age,
                         Gender=:gender,
                         UnusedXP=:xp,
                         AwarenessPoints=:ap,
                         CoolPoints=:cp,
                         ExhaustionPoints=:ep,
                         FearPoints=:fp,
                         LeadershipPoints=:lp,
                         PsychoPoints=:pp,
                         TraumaPoints=:tp,
                         MentalPoints=:mp,
                         status=:status,
                         status_desc=:status_desc,
						 encalien=:cbalien,
						 encgrey=:cbgrey,
						 encpred=:cbpredator,
						 encai=:cbai,
						 encarach=:cbarachnid
                    WHERE id=:character_id";

  $stmt = $db->prepare($charactersql);
  $stmt->bindValue(':playerid', $player, PDO::PARAM_INT);
  $stmt->bindValue(':platoonid', $platoon, PDO::PARAM_INT);
  $stmt->bindValue(':givenname', $givenname, PDO::PARAM_STR);
  $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
  $stmt->bindValue(':enlisted', $enlisted, PDO::PARAM_STR);
  $stmt->bindValue(':age', $age, PDO::PARAM_INT);
  $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
  $stmt->bindValue(':xp', $xp, PDO::PARAM_INT);
  $stmt->bindValue(':ap', $ap, PDO::PARAM_INT);
  $stmt->bindValue(':cp', $cp, PDO::PARAM_INT);
  $stmt->bindValue(':ep', $ep, PDO::PARAM_INT);
  $stmt->bindValue(':fp', $fp, PDO::PARAM_INT);
  $stmt->bindValue(':lp', $lp, PDO::PARAM_INT);
  $stmt->bindValue(':pp', $pp, PDO::PARAM_INT);
  $stmt->bindValue(':tp', $tp, PDO::PARAM_INT);
  $stmt->bindValue(':mp', $mp, PDO::PARAM_INT);
  $stmt->bindValue(':status', $status, PDO::PARAM_STR);
  $stmt->bindValue(':status_desc', $status_desc, PDO::PARAM_STR);
  $stmt->bindValue(':cbalien', $cbalien, PDO::PARAM_INT);
  $stmt->bindValue(':cbgrey', $cbgrey, PDO::PARAM_INT);
  $stmt->bindValue(':cbpredator', $cbpredator, PDO::PARAM_INT);
  $stmt->bindValue(':cbai', $cbai, PDO::PARAM_INT);
  $stmt->bindValue(':cbarachnid', $cbarachnid, PDO::PARAM_INT);
  $stmt->bindValue(':character_id', $character_id, PDO::PARAM_STR);
  $stmt->execute();

  $specialty = $_POST['specialty'];
  $specialtysql = "UPDATE {$table}specialty SET specialty_name_id=:specialty WHERE character_id=:cid";
  $stmt = $db->prepare($specialtysql);
  $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
  $stmt->bindValue(':specialty', $specialty, PDO::PARAM_STR);
  $stmt->execute();

  $rank = $_POST['rank'];
  $ranksql = "UPDATE {$table}ranks SET rank_id=:rank WHERE character_id=:cid LIMIT 1";
  $stmt = $db->prepare($ranksql);
  $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
  $stmt->bindValue(':rank', $rank, PDO::PARAM_INT);
  $stmt->execute();

  //
  // Attributes
  //
  // Finds all attributes currently in database for character
  $attributes = $character->getAttributesWithUid();
  foreach ( $attributes as $attribute ) {
    $old_attributes[$attribute['id']]['id'] = $attribute['uid'];
    $old_attributes[$attribute['id']]['value'] = $attribute['value'];
  }
  // walks through $_POST[] and decides what to delete, update and insert in database
  foreach ( $_POST['attribute'] as $attribute_id => $value ) {
    if (array_key_exists($attribute_id, $old_attributes)) {
      // an optonal attribute has been revoked
      if ($_POST['optional'][$attribute_id] == "1" && $value <= "0") {
        $remove_attributes[$attribute_id] = $old_attributes[$attribute_id]['id'];
      } else { // update the attribute (regardless if the value has changed)
        $updated_attributes[$attribute_id]['id'] = $old_attributes[$attribute_id]['id'];
        $updated_attributes[$attribute_id]['value'] = $value;
      }
      // remove the handled data from old_skills
      unset($old_attributes[$attribute_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$attribute_id] == "0") &&
                                // add the data, it's either an optional attribute that has been added, or a faulty value in an non-optional
      $add_attributes[$attribute_id] = ($value != "0") ? ($value) : ("0");
    }
  }
  // remove the attribute that weren't in the $_POST
  foreach ( $old_attributes as $attribute_id => $id ) {
    $remove_attributes[$attribute_id] = $attribute_id[id];
    unset($old_attributes[$attribute_id]);
  }

  foreach ( $remove_attributes as $attribute_id => $id ) {
    $sql = "DELETE FROM {$table}attributes WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_attributes as $attribute_id => $value ) {
    $sql = "INSERT INTO {$table}attributes SET character_id=:cid,attribute_id=:attribute_id,value=:value";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':attribute_id', $attribute_id, PDO::PARAM_INT);
    $stmt->bindValue(':value', $value, PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $updated_attributes as $attribute_id => $value ) {
    $sql = "UPDATE {$table}attributes SET value=:value WHERE character_id=:cid AND attribute_id=:attribute_id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':attribute_id', $attribute_id, PDO::PARAM_INT);
    $stmt->bindValue(':value', $value['value'], PDO::PARAM_INT);
    $stmt->execute();
  }

  //
  // Skills
  //
  // Finds all skills currently in database for character
  $characterSkills = $character->getSkillsWithUid();
  foreach ( $characterSkills as $skill ) {
    $old_skills[$skill['skill_name_id']]['id'] = $skill['uid'];
    $old_skills[$skill['skill_name_id']]['value'] = $skill['value'];
  }
  // walks through $_POST[] and decides what to delete, update and insert in database
  foreach ( $_POST['skills'] as $skill_name_id => $value ) {
    if (array_key_exists($skill_name_id, $old_skills)) {
      // an optonal skill has been revoked
      if ($_POST['optional'][$skill_name_id] == "1" && $value <= "0") {
        $remove_skills[$skill_name_id] = $old_skills[$skill_name_id]['id'];
      } else { // update the skill (regardless if the value has changed)
        $updated_skills[$skill_name_id]['id'] = $old_skills[$skill_name_id]['id'];
        $updated_skills[$skill_name_id]['value'] = $value;
      }
      // remove the handled data from old_skills
      unset($old_skills[$skill_name_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$skill_name_id] == "0") &&
                                // add the data, it's either an optional skill that has been added, or a faulty value in an non-optional
      $add_skills[$skill_name_id] = ($value != "0") ? ($value) : ("0");
    }
  }

  // remove the skills that weren't in the $_POST
  foreach ( $old_skills as $skill_name_id => $id ) {
    $remove[$skill_name_id] = $skill_name_id['id'];
    unset($old_skills[$skill_name_id]);
  }

  foreach ( $remove_skills as $skill_name_id => $id ) {
    $sql = "DELETE FROM {$table}skills WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_skills as $skill_name_id => $value ) {
    $sql = "INSERT INTO {$table}skills SET character_id=:cid, skill_name_id=:skill_name_id,value=:value";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':skill_name_id', $skill_name_id, PDO::PARAM_INT);
    $stmt->bindValue(':value', $value, PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $updated_skills as $skill_name_id => $value ) {
    $sql = "UPDATE {$table}skills SET value=:value WHERE character_id=:cid AND skill_name_id=:skill_name_id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':skill_name_id', $skill_name_id, PDO::PARAM_INT);
    $stmt->bindValue(':value', $value['value'], PDO::PARAM_INT);
    $stmt->execute();
  }

  //
  // Traits
  //
  // Finds all traits currently in database for character
  $traits = $character->getTraits();
  foreach ( $traits as $trait_name_id => $trait ) {
    $old_traits[$trait_name_id]['id'] = $trait['uid'];
  }
  // walks through $_POST[] and decides what to delete, update and insert in database
  if ($_POST['traits'] == NULL)
    $_POST['traits'] = array ();
  foreach ( $_POST['traits'] as $trait_id => $value ) {
    if (array_key_exists($trait_id, $old_traits)) {
      // update the traits (regardless if the value has changed)
      $updated_traits[$trait_id]['id'] = $old_traits[$trait_id]['id'];
      // remove the handled data from old_traits
      unset($old_traits[$trait_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$attribute_id] == "0") &&
                                // add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
      $add_traits[$trait_id] = 1;
    }
  }
  // remove the traits that weren't in the $_POST
  foreach ( $old_traits as $trait_id => $id ) {
    $remove_traits[$trait_id] = $id;
    unset($old_traits[$trait_id]);
  }

  foreach ( $remove_traits as $trait_id => $id ) {
    $sql = "DELETE FROM {$table}traits WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id['id'], PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_traits as $trait_id => $value ) {
    $sql = "INSERT INTO {$table}traits SET character_id=:cid,trait_name_id=:trait_name_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':trait_name_id', $trait_id, PDO::PARAM_INT);
    $stmt->execute();
  }

  //
  // Advantages
  //
  // Finds all advantages currently in database for character
  $characterAdvantages = $character->getAdvantagesAll();
  foreach ( $characterAdvantages as $key => $advantage) { // $advantage_name_id => $adv ) {
    $old_advs[$advantage->getId()]['id'] = $key;
  }

  // walks through $_POST[] and decides what to delete, update and insert in database
  if ($_POST['advs'] == NULL)
    $_POST['advs'] = array ();
  foreach ( $_POST['advs'] as $adv_id => $value ) {
    if (array_key_exists($adv_id, $old_advs)) {
      // update the advantages (regardless if the value has changed)
      $updated_advs[$adv_id]['id'] = $old_advs[$adv_id]['id'];
      // remove the handled data from old_traits
      unset($old_advs[$adv_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$attribute_id] == "0") &&
                                // add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
      $add_advs[$adv_id] = 1;
    }
  }

  // remove the advantages that weren't in the $_POST
  foreach ( $old_advs as $adv_id => $id ) {
    $remove_advs[$adv_id] = $id;
    unset($old_advs[$adv_id]);
  }

  foreach ( $remove_advs as $adv_id => $id ) {
    $sql = "DELETE FROM {$table}advantages WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id['id'], PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_advs as $adv_id => $value ) {
    $sql = "INSERT INTO {$table}advantages SET character_id=:cid,advantage_name_id=:advantage_name_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':advantage_name_id', $adv_id, PDO::PARAM_INT);
    $stmt->execute();
  }

  //
  // Disdvantages
  //
  // Finds all disadvantages currently in database for character
  $characterDisadvantages = $character->getDisadvantagesAll();
  foreach ( $characterDisadvantages as $key => $disadvantage) {
    $old_disadvs[$disadvantage->getId()]['id'] = $key;
  }

  // walks through $_POST[] and decides what to delete, update and insert in database
  if ($_POST['disadvs'] == NULL)
    $_POST['disadvs'] = array ();
  foreach ( $_POST['disadvs'] as $disadv_id => $value ) {
    if (array_key_exists($disadv_id, $old_disadvs)) {
      // update the advantages (regardless if the value has changed)
      $updated_disadvs[$disadv_id]['id'] = $old_disadvs[$disadv_id]['id'];
      // remove the handled data from old_traits
      unset($old_disadvs[$disadv_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$attribute_id] == "0") &&
                                // add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
      $add_disadvs[$disadv_id] = 1;
    }
  }
  // remove the advantages that weren't in the $_POST
  foreach ( $old_disadvs as $disadv_id => $id ) {
    $remove_disadvs[$disadv_id] = $id;
    unset($old_disadvs[$disadv_id]);
  }

  foreach ( $remove_disadvs as $disadv_id => $id ) {
    $sql = "DELETE FROM {$table}disadvantages WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id['id'], PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_disadvs as $disadv_id => $value ) {
    $sql = "INSERT INTO {$table}disadvantages SET character_id=:cid,disadvantage_name_id=:disadvantage_name_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':disadvantage_name_id', $disadv_id, PDO::PARAM_INT);
    $stmt->execute();
  }
  //
  // Certificates
  //
  // Finds all certificates currently in database for character
  $characterCertificates = $character->getCertsForCharacterWithoutReqCheck();
  foreach ( $characterCertificates as $certificate_name_id => $certificate ) {
    $old_certificate[$certificate_name_id]['id'] = $certificate['uid'];
  }
  // walks through $_POST[] and decides what to delete, update and insert in database
  if ($_POST['certs'] == NULL)
    $_POST['certs'] = array ();
  foreach ( $_POST['certs'] as $certificate_id => $value ) {
    if (array_key_exists($certificate_id, $old_certificate)) {
      // update the certificates (regardless if the value has changed)
      $updated_certificate[$certificate_id]['id'] = $old_certificate[$certificate_id]['id'];
      // remove the handled data from old_traits
      unset($old_certificate[$certificate_id]);
    } elseif ($value != NULL) { // ($value != "0" || $_POST['optional'][$attribute_id] == "0") &&
                                // add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
      $add_certificate[$certificate_id] = 1;
    }
  }
  // remove the certificates that weren't in the $_POST
  foreach ( $old_certificate as $certificate_id => $id ) {
    $remove_certificate[$certificate_id] = $id;
    unset($old_certificate[$certificate_id]);
  }

  foreach ( $remove_certificate as $certificate_id => $id ) {
    $sql = "DELETE FROM {$table}certificates WHERE character_id=:cid AND id=:id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id['id'], PDO::PARAM_INT);
    $stmt->execute();
  }
  foreach ( $add_certificate as $certificate_id => $value ) {
    $sql = "INSERT INTO {$table}certificates SET character_id=:cid,certificate_name_id=:certificate_name_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cid', $character_id, PDO::PARAM_INT);
    $stmt->bindValue(':certificate_name_id', $certificate_id, PDO::PARAM_INT);
    $stmt->execute();
  }

  header("location:{$url_root}/index.php?url=list_characters.php");
} elseif ($_GET['action'] == "create_character") {
  /*
   * Creates a new character
   *
   */
  if ($_POST['enlisted'] != "") {
    $enlisted = $_POST['enlisted'];
  } else {
    $enlisted = date("Y-m-d");
  }

  $player = $_POST['player'];
  $platoon = $_POST['platoon'];
  $givenname = $_POST['forname'];
  $lastname = $_POST['lastname'];
  $age = $_POST['age'];
  $gender = $_POST['gender'];
  $xp = $_POST['xp'];
  $ap = $_POST['ap'];
  $cp = $_POST['cp'];
  $ep = $_POST['ep'];
  $fp = $_POST['fp'];
  $lp = $_POST['lp'];
  $pp = $_POST['pp'];
  $tp = $_POST['tp'];
  $mp = $_POST['mp'];

  $charactersql = "INSERT INTO {$_SESSION['table_prefix']}characters (`userid`, `platoon_id`, `forname`, `lastname`,
            `Enlisted`, `Age`, `Gender`, `UnusedXP`, `AwarenessPoints`,	`CoolPoints`, `ExhaustionPoints`, `FearPoints`,
            `LeadershipPoints`, `PsychoPoints`, `TraumaPoints`, `MentalPoints`)
          VALUES(:playerid, :platoonid, :givenname, :lastname, :enlisted, :age, :gender, :xp, :ap,
            :cp, :ep, :fp, :lp, :pp, :tp, :mp)";

  $stmt = $db->prepare($charactersql);
  $stmt->bindValue(':playerid', $player, PDO::PARAM_INT);
  $stmt->bindValue(':platoonid', $platoon, PDO::PARAM_INT);
  $stmt->bindValue(':givenname', $givenname, PDO::PARAM_STR);
  $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
  $stmt->bindValue(':enlisted', $enlisted, PDO::PARAM_STR);
  $stmt->bindValue(':age', $age, PDO::PARAM_INT);
  $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
  $stmt->bindValue(':xp', $xp, PDO::PARAM_INT);
  $stmt->bindValue(':ap', $ap, PDO::PARAM_INT);
  $stmt->bindValue(':cp', $cp, PDO::PARAM_INT);
  $stmt->bindValue(':ep', $ep, PDO::PARAM_INT);
  $stmt->bindValue(':fp', $fp, PDO::PARAM_INT);
  $stmt->bindValue(':lp', $lp, PDO::PARAM_INT);
  $stmt->bindValue(':pp', $pp, PDO::PARAM_INT);
  $stmt->bindValue(':tp', $tp, PDO::PARAM_INT);
  $stmt->bindValue(':mp', $mp, PDO::PARAM_INT);
  $stmt->execute();
  $characterid = $db->lastInsertId();

  $attributes = current($_POST['attribute']);
  $attribute_id = key($_POST['attribute']);
  if ($_POST['attribute'] != "") {
    $attributesql = "INSERT INTO {$_SESSION['table_prefix']}attributes (character_id,value,attribute_id) VALUES (:cid,:value,:aid)";
    $stmt = $db->prepare($attributesql);
    $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
    $stmt->bindValue(':value', $attributes, PDO::PARAM_INT);
    $stmt->bindValue(':aid', $attribute_id, PDO::PARAM_INT);
    $stmt->execute();
  }
  while ( $attributes = next($_POST['attribute']) ) {
    $attribute_id = key($_POST['attribute']);
    if ($_POST['attribute'] != "") {
      $attributesql = "INSERT INTO {$_SESSION['table_prefix']}attributes (character_id,value,attribute_id) VALUES (:cid,:value,:aid)";
      $stmt = $db->prepare($attributesql);
      $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
      $stmt->bindValue(':value', $attributes, PDO::PARAM_INT);
      $stmt->bindValue(':aid', $attribute_id, PDO::PARAM_INT);
      $stmt->execute();
    }
  }

  $skill = current($_POST['skill']);
  $skill = $skill;
  $skill_id = key($_POST['skill']);
  if ($_POST['skill'][$skill_id] != NULL) { // ($_POST['optional'][$skill_id] != 1 || $_POST['skill'][$skill_id] != NULL) && $_POST['skill']!=""
    $skillssql = "INSERT INTO {$_SESSION['table_prefix']}skills (character_id,value,skill_name_id) VALUES (:cid, :value, :sid)";
    $stmt = $db->prepare($skillssql);
    $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
    $stmt->bindValue(':value', $skill, PDO::PARAM_INT);
    $stmt->bindValue(':sid', $skill_id, PDO::PARAM_INT);
    $stmt->execute();
  }
  $skill = next($_POST['skill']);
  $skill = $skill;
  while ( $skill_id ) {
    $skill_id = key($_POST['skill']);
    if ($_POST['skill'][$skill_id] != NULL) { // ($_POST['optional'][$skill_id] != 1 || $_POST['skill'][$skill_id] != NULL) && $_POST['skill']!=""
      $skillssql = "INSERT INTO {$_SESSION['table_prefix']}skills (character_id,value,skill_name_id) VALUES (:cid, :value, :sid)";
      $stmt = $db->prepare($skillssql);
      $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
      $stmt->bindValue(':value', $skill, PDO::PARAM_INT);
      $stmt->bindValue(':sid', $skill_id, PDO::PARAM_INT);
      $stmt->execute();
    }
    $skill = next($_POST['skill']);
    $skill_id = key($_POST['skill']);
  }

  $specialty = $_POST['specialty'];
  $specialtysql = "INSERT INTO {$_SESSION['table_prefix']}specialty (character_id,specialty_name_id) VALUES (:cid, :specialty)";
  $stmt = $db->prepare($specialtysql);
  $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
  $stmt->bindValue(':specialty', $specialty, PDO::PARAM_STR);
  $stmt->execute();
  $rank = $_POST['rank'];
  $ranksql = "INSERT INTO {$_SESSION['table_prefix']}ranks (character_id,rank_id) VALUES (:cid, :rankid)";
  $stmt = $db->prepare($ranksql);
  $stmt->bindValue(':cid', $characterid, PDO::PARAM_INT);
  $stmt->bindValue(':rankid', $rank, PDO::PARAM_INT);
  $stmt->execute();

  header("location:{$url_root}/index.php?url=list_characters.php");
}
