<?php 

/*
 *	Functions handling characters should go in here
 *
 */
session_start();
include "functions.php";

myconnect();
mysql_select_db("skynet"); 
if ($_GET['action']=="update_character") {
	/*
	 *	Updates a characters stats and skills
	 *
	 */
	$remove_attributes=array();
	$add_attributes=array();
	$updated_attributes=array();
	$old_attributes=array();
	$remove_skills=array();
	$add_skills=array();
	$updated_skills=array();
	$old_skills=array();
	$remove_traits=array();
	$add_traits=array();
	$updated_traits=array();
	$old_traits=array();
	$remove_advs=array();
	$add_advs=array();
	$updated_advs=array();
	$old_advs=array();
	$remove_disadvs=array();
	$add_disadvs=array();
	$updated_disadvs=array();
	$old_disadvs=array();
	$remove_certificate=array();
	$add_certificate=array();
	$updated_certificate=array();
	$old_certificate=array();
	$character_id=quote_smart($_POST['character']);
	$table=$_SESSION['table_prefix'];
	
	
	// updates character basic stats
	$player=quote_smart($_POST['player']);
	$platoon=quote_smart($_POST['platoon']);
	$forname=quote_smart($_POST['forname']);
	$lastname=quote_smart($_POST['lastname']);
	$enlisted=quote_smart($_POST['enlisted']);
	$age=quote_smart($_POST['age']);
	$gender=quote_smart($_POST['gender']);
	$xp=quote_smart($_POST['xp']);
	$ap=quote_smart($_POST['ap']);
	$cp=quote_smart($_POST['cp']);
	$ep=quote_smart($_POST['ep']);
	$fp=quote_smart($_POST['fp']);
	$lp=quote_smart($_POST['lp']);
	$pp=quote_smart($_POST['pp']);
	$tp=quote_smart($_POST['tp']);
	$mp=quote_smart($_POST['mp']);
	$status=quote_smart($_POST['status']);
	$status_desc=quote_smart($_POST['status_desc']);
	$charactersql="UPDATE {$table}characters SET userid='{$player}',
                         platoon_id='{$platoon}',
                         forname='{$forname}',
                         lastname='{$lastname}',
                         Enlisted='{$enlisted}',
                         Age='{$age}',
                         Gender='{$gender}',
                         UnusedXP='{$xp}',
												 AwarenessPoints='{$ap}',
                         CoolPoints='{$cp}',
												 ExhaustionPoints='{$ep}',
												 FearPoints='{$fp}',
												 LeadershipPoints='{$lp}',
                         PsychoPoints='{$pp}',
												 TraumaPoints='{$tp}',
												 MentalPoints='{$mp}',
                         status='{$status}',
                         status_desc='{$status_desc}'
                    WHERE id='{$character_id}'";
	mysql_query($charactersql);
	// update character specialty
	$specialty=quote_smart($_POST['specialty']);
	$ranksql="UPDATE {$table}specialty SET specialty_name_id='{$specialty}' WHERE character_id='{$character_id}'";
	mysql_query($ranksql);
	// update character rank
	$rank=quote_smart($_POST['rank']);
	$ranksql="UPDATE {$table}ranks SET rank_id='{$rank}' WHERE character_id='{$character_id}' LIMIT 1";
	mysql_query($ranksql);
	
	//
	// Attributes
	//
	// Finds all attributes currently in database for character
	$attributes=mysql_query("select attribute_id,value,id from {$table}attributes where character_id={$character_id}") or die(mysql_error());
	while($attribute=mysql_fetch_array($attributes)){
		$old_attributes[$attribute['attribute_id']]['id']=$attribute['id'];
		$old_attributes[$attribute['attribute_id']]['value']=$attribute['value'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	foreach($_POST['attribute'] as $attribute_id => $value) {
		if($old_attributes[$attribute_id]) {
			//an optonal attribute has been revoked
			if ($_POST['optional'][$attribute_id]=="1" && $value <= "0") {
				$remove_attributes[$attribute_id]=$old_attributes[$attribute_id]['id'];
			} else {  // update the attribute (regardless if the value has changed)
				$updated_attributes[$attribute_id]['id']=$old_attributes[$attribute_id]['id'];
				$updated_attributes[$attribute_id]['value']=quote_smart($value);
			}
			// remove the handled data from old_skills
			unset($old_attributes[$attribute_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$attribute_id] == "0") && 
			// add the data, it's either an optional attribute that has been added, or a faulty value in an non-optional
			$add_attributes[$attribute_id]=(quote_smart($value)!="0")?(quote_smart($value)):("0");
		}
	}
	// remove the attribute that weren't in the $_POST
	foreach($old_attributes as $attribute_id => $id){
		$remove_attributes[$attribute_id]=$attribute_id[id];
		unset($old_attributes[$attribute_id]);
	}


	foreach($remove_attributes as $attribute_id => $id) {
		$sql="DELETE FROM {$table}attributes WHERE character_id='{$_POST['character']}' AND id='{$id}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_attributes as $attribute_id => $value) {
		$sql="INSERT INTO {$table}attributes SET character_id='{$_POST['character']}',attribute_id='{$attribute_id}',value='{$value}'";
		mysql_query($sql);
	}
	foreach($updated_attributes as $attribute_id => $value) {
		$sql="UPDATE {$table}attributes SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND attribute_id='{$attribute_id}' LIMIT 1";
		mysql_query($sql);
	}
	
	//
	// Skills
	//
	// Finds all skills currently in database for character
	$anmalningar=mysql_query("select skill_name_id,value,id from {$table}skills where character_id={$_POST['character']}") or die(mysql_error());
	while($skill=mysql_fetch_array($anmalningar)){
		$old_skills[$skill['skill_name_id']]['id']=$skill['id'];
		$old_skills[$skill['skill_name_id']]['value']=$skill['value'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	foreach($_POST['skills'] as $skill_name_id => $value) {
		if($old_skills[$skill_name_id]) {
			//an optonal skill has been revoked
			if ($_POST['optional'][$skill_name_id]=="1" && $value <= "0") {
				$remove_skills[$skill_name_id]=$old_skills[$skill_name_id]['id'];
			} else {  // update the skill (regardless if the value has changed)
				$updated_skills[$skill_name_id]['id']=$old_skills[$skill_name_id]['id'];
				$updated_skills[$skill_name_id]['value']=quote_smart($value);
			}
			// remove the handled data from old_skills
			unset($old_skills[$skill_name_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$skill_name_id] == "0") && 
			// add the data, it's either an optional skill that has been added, or a faulty value in an non-optional
			$add_skills[$skill_name_id]=($value!="0")?(quote_smart($value)):("0");
		}
	}
	
	// remove the skills that weren't in the $_POST
	foreach($old_skills as $skill_name_id => $id){
		$remove[$skill_name_id]=$skill_name_id['id'];
		unset($old_skills[$skill_name_id]);
	}

	foreach($remove_skills as $skill_name_id => $id) {
		$sql="DELETE FROM {$table}skills WHERE character_id='{$_POST['character']}' AND id='{$id}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_skills as $skill_name_id => $value) {
		$sql="INSERT INTO {$table}skills SET character_id='{$_POST['character']}',skill_name_id='{$skill_name_id}',value='{$value}'";
		mysql_query($sql);
	}
	foreach($updated_skills as $skill_name_id => $value) {
		$sql="UPDATE {$table}skills SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND skill_name_id='{$skill_name_id}' LIMIT 1";
		mysql_query($sql);
	}
	
	//
	// Traits
	//
	// Finds all traits currently in database for character
	$traits=mysql_query("select trait_name_id,id from {$table}traits where character_id={$character_id}") or die(mysql_error());
	while($trait=mysql_fetch_array($traits)){
		$old_traits[$trait['trait_name_id']][id]=$trait['id'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	if ($_POST['traits'] == NULL) $_POST['traits'] = array();
	foreach($_POST['traits'] as $trait_id => $value) {
		if($old_traits[$trait_id]) {
			// update the traits (regardless if the value has changed)
			$updated_traits[$trait_id]['id']=$old_traits[$trait_id]['id'];
			// remove the handled data from old_traits
			unset($old_traits[$trait_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$attribute_id] == "0") && 
			// add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
			$add_traits[$trait_id]=1;
		}
	}
	// remove the traits that weren't in the $_POST
	foreach($old_traits as $trait_id => $id){
		$remove_traits[$trait_id]=$id;
		unset($old_traits[$trait_id]);
	}

	foreach($remove_traits as $trait_id => $id) {
		$sql="DELETE FROM {$table}traits WHERE character_id='{$_POST['character']}' AND id='{$id['id']}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_traits as $trait_id => $value) {
		$sql="INSERT INTO {$table}traits SET character_id='{$_POST['character']}',trait_name_id='{$trait_id}'";
		mysql_query($sql);
	}
	foreach($updated_traits as $trait_id => $value) {
//		$sql="UPDATE {$table}traits SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND trait_name_id='{$trait_id}' LIMIT 1";
//		mysql_query($sql);
	}
	//
	// Advantages
	//
	// Finds all advantages currently in database for character
	$advs=mysql_query("select advantage_name_id,id from {$table}advantages where character_id={$character_id}") or die(mysql_error());
	while($adv=mysql_fetch_array($advs)){
		$old_advs[$adv['advantage_name_id']][id]=$adv['id'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	if ($_POST['advs'] == NULL) $_POST['advs'] = array();
	foreach($_POST['advs'] as $adv_id => $value) {
		if($old_advs[$adv_id]) {
			// update the advantages (regardless if the value has changed)
			$updated_advs[$adv_id]['id']=$old_advs[$adv_id]['id'];
			// remove the handled data from old_traits
			unset($old_advs[$adv_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$attribute_id] == "0") && 
			// add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
			$add_advs[$adv_id]=1;
		}
	}
	// remove the advantages that weren't in the $_POST
	foreach($old_advs as $adv_id => $id){
		$remove_advs[$adv_id]=$id;
		unset($old_advs[$adv_id]);
	}

	foreach($remove_advs as $adv_id => $id) {
		$sql="DELETE FROM {$table}advantages WHERE character_id='{$_POST['character']}' AND id='{$id['id']}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_advs as $adv_id => $value) {
		$sql="INSERT INTO {$table}advantages SET character_id='{$_POST['character']}',advantage_name_id='{$adv_id}'";
		mysql_query($sql);
	}
	foreach($updated_advs as $adv_id => $value) {
//		$sql="UPDATE {$table}advantages SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND advantage_name_id='{$adv_id}' LIMIT 1";
//		mysql_query($sql);
	}
	//
	// Disdvantages
	//
	// Finds all disadvantages currently in database for character
	$disadvs=mysql_query("select disadvantage_name_id,id from {$table}disadvantages where character_id={$character_id}") or die(mysql_error());
	while($disadv=mysql_fetch_array($disadvs)){
		$old_disadvs[$disadv['disadvantage_name_id']][id]=$disadv['id'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	if ($_POST['disadvs'] == NULL) $_POST['disadvs'] = array();
	foreach($_POST['disadvs'] as $disadv_id => $value) {
		if($old_disadvs[$disadv_id]) {
			// update the advantages (regardless if the value has changed)
			$updated_disadvs[$disadv_id]['id']=$old_disadvs[$disadv_id]['id'];
			// remove the handled data from old_traits
			unset($old_disadvs[$disadv_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$attribute_id] == "0") && 
			// add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
			$add_disadvs[$disadv_id]=1;
		}
	}
	// remove the advantages that weren't in the $_POST
	foreach($old_disadvs as $disadv_id => $id){
		$remove_disadvs[$disadv_id]=$id;
		unset($old_disadvs[$disadv_id]);
	}

	foreach($remove_disadvs as $disadv_id => $id) {
		$sql="DELETE FROM {$table}disadvantages WHERE character_id='{$_POST['character']}' AND id='{$id['id']}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_disadvs as $disadv_id => $value) {
		$sql="INSERT INTO {$table}disadvantages SET character_id='{$_POST['character']}',disadvantage_name_id='{$disadv_id}'";
		mysql_query($sql);
	}
	foreach($updated_disadvs as $disadv_id => $value) {
//		$sql="UPDATE {$table}disadvantages SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND disadvantage_name_id='{$disadv_id}' LIMIT 1";
//		mysql_query($sql);
	}
	//
	// Certificates
	//
	// Finds all certificates currently in database for character
	$certificates=mysql_query("select certificate_name_id,id from {$table}certificates where character_id={$character_id}") or die(mysql_error());

	while($certificate=mysql_fetch_array($certificates)){
		$old_certificate[$certificate['disadvantage_name_id']][id]=$certificate['id'];
	}
	// walks through $_POST[] and decides what to delete, update and insert in database
	if ($_POST['certs'] == NULL) $_POST['certs'] = array();
	foreach($_POST['certs'] as $certificate_id => $value) {
		if($old_certificate[$certificate_id]) {
			// update the certificates (regardless if the value has changed)
			$updated_certificate[$certificate_id]['id']=$old_certificate[$certificate_id]['id'];
			// remove the handled data from old_traits
			unset($old_certificate[$certificate_id]);
		} elseif( $value != NULL) { //($value !=  "0" || $_POST['optional'][$attribute_id] == "0") && 
			// add the data, it's either an optional traits that has been added, or a faulty value in an non-optional
			$add_certificate[$certificate_id]=1;
		}
	}
	// remove the certificates that weren't in the $_POST
	foreach($old_certificate as $certificate_id => $id){
		$remove_certificate[$certificate_id]=$id;
		unset($old_certificate[$certificate_id]);
	}

	foreach($remove_certificate as $certificate_id => $id) {
		$sql="DELETE FROM {$table}certificates WHERE character_id='{$_POST['character']}' AND id='{$id['id']}' LIMIT 1";
//		print $sql."<br>";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_certificate as $certificate_id => $value) {
		$sql="INSERT INTO {$table}certificates SET character_id='{$_POST['character']}',certificate_name_id='{$certificate_id}'";
//		print $sql."<br>";
		mysql_query($sql);
	}
	foreach($updated_certificate as $certificate_id => $value) {
//		$sql="UPDATE {$table}certificates SET value='{$value[value]}' WHERE character_id='{$_POST['character']}' AND certificate_name_id='{$certificate_id}' LIMIT 1";
//		mysql_query($sql);
	}
	
	header("location:{$url_root}/index.php?url=list_characters.php");
}
elseif($_GET['action']=="create_character") {
 	/*
	 *	Creates a new character
	 *
	 */
	if ($_POST['enlisted'] != "") 
		$enlisted = "'".quote_smart($_POST['enlisted'])."'";
	else	
		$enlisted = "NOW()";
	
	$player=quote_smart($_POST['player']);
	$platoon=quote_smart($_POST['platoon']);
	$forname=quote_smart($_POST['forname']);
	$lastname=quote_smart($_POST['lastname']);
	$age=quote_smart($_POST['age']);
	$gender=quote_smart($_POST['gender']);
	$xp=quote_smart($_POST['xp']);
	$ap=quote_smart($_POST['ap']);
	$cp=quote_smart($_POST['cp']);
	$ep=quote_smart($_POST['ep']);
	$fp=quote_smart($_POST['fp']);
	$lp=quote_smart($_POST['lp']);
	$pp=quote_smart($_POST['pp']);
	$tp=quote_smart($_POST['tp']);
	$mp=quote_smart($_POST['mp']);
		
	$charactersql="INSERT INTO {$_SESSION['table_prefix']}characters (`userid`, `platoon_id`, `forname`, `lastname`, 
						`Enlisted`, `Age`, `Gender`, `UnusedXP`, `AwarenessPoints`,	`CoolPoints`, `ExhaustionPoints`, `FearPoints`,
						`LeadershipPoints`, `PsychoPoints`, `TraumaPoints`, `MentalPoints`) 
					VALUES('{$player}','{$platoon}','{$forname}','{$lastname}',{$enlisted},'{$age}','{$gender}','{$xp}','{$ap}'
						,'{$cp}','{$ep}','{$fp}','{$lp}','{$pp}','{$tp}','{$mp}')";

	$characterres=mysql_query($charactersql);
	$characterid=mysql_insert_id();
	
	$attributes=current($_POST['attribute']);
	$attributes=quote_smart($attributes);
	$attribute_id=key($_POST['attribute']);
	if ($_POST['attribute']!="") {
		$attributesql="INSERT INTO {$_SESSION['table_prefix']}attributes (character_id,value,attribute_id) VALUES ('{$characterid}','{$attributes}','{$attribute_id}')";
		mysql_query($attributesql);
	}
	while($attributes=next($_POST['attribute'])) {
		$attributes=quote_smart($attributes);
		$attribute_id=key($_POST['attribute']);
		if ($_POST['attribute']!="") {
			mysql_query("INSERT INTO {$_SESSION['table_prefix']}attributes (character_id,value,attribute_id) VALUES ('{$characterid}','{$attributes}','{$attribute_id}')");
		}
	}
	
	$skills=current($_POST['skill']);
	$skills=quote_smart($skills);
	$skills_id=key($_POST['skill']);
	$skillssql="INSERT INTO {$_SESSION['table_prefix']}skills (character_id,value,skill_name_id) VALUES ('{$characterid}','{$skills}','{$skills_id}')";
	if ($_POST['skill'][$skills_id] != NULL) { //($_POST['optional'][$skills_id] != 1 || $_POST['skill'][$skills_id] != NULL) && $_POST['skill']!=""
		mysql_query($skillssql);
	}
	$skills=next($_POST['skill']);
	$skills=quote_smart($skills);
	while($skills_id) {
		$skills_id=key($_POST['skill']);
		if($_POST['skill'][$skills_id] != NULL ) { //($_POST['optional'][$skills_id] != 1 || $_POST['skill'][$skills_id] != NULL) && $_POST['skill']!=""
			mysql_query("INSERT INTO {$_SESSION['table_prefix']}skills (character_id,value,skill_name_id) VALUES ('{$characterid}','{$skills}','{$skills_id}')");
		}
		$skills=next($_POST['skill']);
		$skills=quote_smart($skills);
		$skills_id=key($_POST['skill']);
	}
	
	$specialty=quote_smart($_POST['specialty']);
	$specialtysql="INSERT INTO {$_SESSION['table_prefix']}specialty (character_id,specialty_name_id) VALUES ('{$characterid}','{$specialty}')";
	mysql_query($specialtysql);
	$rank=quote_smart($_POST['rank']);
	$ranksql="INSERT INTO {$_SESSION['table_prefix']}ranks (character_id,rank_id) VALUES ('{$characterid}','{$rank}')";
	mysql_query($ranksql);
	
	header("location:{$url_root}/index.php?url=list_characters.php");
}
