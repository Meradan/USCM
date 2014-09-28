<?php 
/*
 *	Functions handling missions should go in here
 *	Care should be taken when removing a character from a mission as promotions and 
 *	commendations could be screwed up
 *
 */
session_start();
include("functions.php");
myconnect();
mysql_select_db("skynet");

if ($_GET['what']=="names") {
	$short=strtr(htmlspecialchars($_POST['mission_name_short'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n")); 
	$name=strtr(htmlspecialchars($_POST['mission_name'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$date=strtr(htmlspecialchars($_POST['date'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$platoon_id=$_POST['platoon_id'];
	$sql="UPDATE {$_SESSION['table_prefix']}mission_names 
		SET mission_name_short='{$short}',mission_name='{$name}',date='{$date}',platoon_id='{$platoon_id}'
		WHERE id='{$_GET['mission']}'";
	mysql_query($sql);
}
elseif ($_GET['what']=="gm") {
	$sql="UPDATE {$_SESSION['table_prefix']}mission_names 
		SET gm='{$_POST['gm']}' WHERE id='{$_GET['mission']}'";
	mysql_query($sql);
}
elseif ($_GET['what']=="briefing") { 
	$briefing=strtr(htmlspecialchars($_POST['briefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$sql="UPDATE {$_SESSION['table_prefix']}mission_names 
		SET briefing='{$briefing}'
		WHERE id='{$_GET['mission']}'";
	mysql_query($sql);
}
elseif ($_GET['what']=="debriefing") { 
	$debriefing=strtr(htmlspecialchars($_POST['debriefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$sql="UPDATE {$_SESSION['table_prefix']}mission_names 
		SET debriefing='{$debriefing}'
		WHERE id='{$_GET['mission']}'";
	mysql_query($sql);
}
elseif ($_GET['what']=="characters") { 
	$remove_characters=array();
	$add_characters=array();
	$old_characters=array();
	$mission_id=$_GET['mission'];
	$table=$_SESSION['table_prefix'];
	
	//
	// Characters on a mission
	//
	// Finds all character currently in database for the mission
	$sql="select character_id,id,mission_id from {$table}missions where mission_id={$mission_id}";
	$characterres=mysql_query($sql) or die(mysql_error());
	while($character=mysql_fetch_array($characterres)){
		$old_characters[$character['character_id']][mission_id]=$character['mission_id'];
		$old_characters[$character['character_id']][id]=$character['id'];
	}
	// walks through $_POST[] and decides what to delete and insert in database
	foreach($_POST['characters'] as $character_id) {
		if($old_characters[$character_id]) {
			// remove the handled data from old_characters since it's already in the database
			unset($old_characters[$character_id]);
		} else {
			// new data, add it
			$add_characters[$character_id]=$mission_id;
		}
	}
	// remove the characters that weren't in the $_POST, and thereby were removed from the mission
	foreach($old_characters as $index => $id){
		
		$remove_characters[$id[id]]=$id[id];
		unset($old_characters[$index]);
	}
	foreach($remove_characters as  $id) {
		$sql="DELETE FROM {$table}missions WHERE id='{$id}' LIMIT 1";
		mysql_query($sql) or die("tabort: " . mysql_error());
	}
	foreach($add_characters as $character_id => $mission_id) {
		$sql="INSERT INTO {$table}missions SET character_id='{$character_id}',mission_id='{$mission_id}'";
		mysql_query($sql);
	}

}
elseif ($_GET['what']=="commendations") { 
	foreach ($_POST['characters'] as $character_id => $dummy) {
		//check for foreign respective national medals 
		$sql="SELECT mn.id,mn.foreign_medal FROM {$_SESSION['table_prefix']}medal_names mn 
					LEFT JOIN {$_SESSION['table_prefix']}missons m ON mn.id=m.medal_id
					WHERE m.character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
		$sqlres=mysql_query($sql);
		
		$sql="UPDATE {$_SESSION['table_prefix']}missions SET medal_id='{$_POST['medal']}' WHERE character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
		mysql_query($sql);
	}
}
elseif ($_GET['what']=="create_mission") { 
	$short=strtr(htmlspecialchars($_POST['mission'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n")); 
	$name=strtr(htmlspecialchars($_POST['name'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$date=strtr(htmlspecialchars($_POST['date'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$briefing=strtr(htmlspecialchars($_POST['briefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$debriefing=strtr(htmlspecialchars($_POST['debriefing'], ENT_QUOTES, "UTF-8"),array("\n"=>"<br/>\n"));
	$platoon_id=$_POST['platoon_id'];
	$sql="INSERT INTO {$_SESSION['table_prefix']}mission_names SET mission_name_short='{$short}',mission_name='{$name}',date='{$date}',gm='{$_SESSION['user_id']}',briefing='{$briefing}', debriefing='{$debriefing}',platoon_id='{$platoon_id}'";
	mysql_query($sql);
	$mission=mysql_insert_id();
	$_GET['mission']=$mission;	
}
elseif ($_GET['what']=="promotion") {
	$character_id=$_POST['character'];
	// The rank for promotion has been selected, time to insert into 
	// the database
	if ($_POST['rank']) {
		$sql="UPDATE {$_SESSION['table_prefix']}missions SET rank_id='{$_POST['rank']}' WHERE character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
		mysql_query($sql);
		$sql="UPDATE {$_SESSION['table_prefix']}ranks SET rank_id='{$_POST['rank']}' WHERE character_id='{$character_id}'";
		mysql_query($sql);
	} 
	// A character that previously was selected for promotion on the mission
	// which has been withdrawn. Previous rank should be restored.
	else {
		// Select the rank that was choosen for this mission
		$sql="SELECT rank_id FROM {$_SESSION['table_prefix']}missions WHERE character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
		$sqlres=mysql_query($sql);
		$formerrank=mysql_fetch_array($sqlres);
		// Select the previous rank that the character held before this mission, or rather
		// the promotion the character had gotten before. If no promotion, result is empty
		$missiondate=mysql_fetch_array(mysql_query("SELECT date FROM {$_SESSION['table_prefix']}mission_names WHERE id='{$_GET['mission']}'"));
		$sql="SELECT m.rank_id FROM {$_SESSION['table_prefix']}missions m LEFT JOIN {$_SESSION['table_prefix']}mission_names mn on mn.id =m.mission_id WHERE character_id='{$character_id}' AND date<'{$missiondate['date']}' ORDER BY date DESC LIMIT 1";
		$sqlres=mysql_query($sql);
		$rankbeforeformerrank=mysql_fetch_array($sqlres);
		// The character has been promoted before and should retain that rank
		if ($rankbeforeformerrank['rank_id']) {
			$sql="UPDATE {$_SESSION['table_prefix']}ranks SET rank_id='{$rankbeforeformerrank['rank_id']}' WHERE character_id='{$character_id}'";
			mysql_query($sql);
			$sql="UPDATE {$_SESSION['table_prefix']}missions SET rank_id='{$_POST['rank']}' WHERE character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
			mysql_query($sql);
		} 
		// The character have not been promoted before the mission and should be restored to the rank
		// the character had upon joining the corps, i.e. LCpl or Pvt. This however isn't consistent with 
		// characters who has the military family advantage.
		else {
			if ($formerrank['rank_id']>2) {
				$rank = 3; //LCpl
			}
			else {
				$rank = 1; //Pvt
			}
			$sql="UPDATE {$_SESSION['table_prefix']}ranks SET rank_id='{$rank}' WHERE character_id='{$character_id}'";
			mysql_query($sql);
			$sql="UPDATE {$_SESSION['table_prefix']}missions SET rank_id='{$_POST['rank']}' WHERE character_id='{$character_id}' AND mission_id='{$_GET['mission']}'";
			mysql_query($sql);
		}
	}
}

header("location:{$url_root}/index.php?url=show_mission.php&id={$_GET['mission']}");

?>
