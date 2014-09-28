<?php
/*
 * Note: libPDF uses coordinates from the lower left corner
 */
session_start();
include "functions.php";
include "tcpdf/tcpdf.php";

myconnect(); 
mysql_select_db("skynet");
$userres = mysql_query("SELECT Users.id FROM Users 
												LEFT JOIN {$_SESSION['table_prefix']}characters as c ON c.userid=Users.id 
												WHERE c.id='{$_GET['character_id']}'");
$userquery=mysql_fetch_array($userres);
$userid=$userquery['id'];
$admin=($_SESSION['level']==3)?(TRUE):(FALSE);
$gm=($_SESSION['level']==2)?(TRUE):(FALSE);
$user=($_SESSION['user_id']==$userid)?(TRUE):(FALSE);
$user=($_GET['character_id'])?($user):(FALSE);
$platoon_idsql = "SELECT platoon_id FROM {$_SESSION['table_prefix']}characters WHERE id='{$_GET['character_id']}'";
$platoon_idres=mysql_query($platoon_idsql);
while($row=mysql_fetch_array($platoon_idres)) {
	$platoon_id=$row['platoon_id'];
}

if ($admin||$user||$gm) {

function fontregular ($font,$pdf) {
	$font = pdf_findfont($pdf, "Helvetica", "host", 0); 
	pdf_setfont($pdf,$font,10);
}
function fontbold ($font,$pdf) {
	$font = pdf_findfont($pdf, "Helvetica-Bold", "host", 0); 
	pdf_setfont($pdf,$font,10);
}

$aapcolumnone = 50;
$aapcolumntwo = 110;
$aapcolumnthree = 120;
$attribpcolumnthree = 160;
$aapcolumnfour = 200; //170
$aapcolumnfive = 290; //260
$aapcolumnsix = 310;  //280

//$pdf = pdf_new();
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//pdf_open_file($pdf,"");
//pdf_begin_document($pdf,"");
//pdf_set_parameter($pdf, "warning", "true");

$pdf->SetCreator("create_sheet.php");
$pdf->SetAuthor('Skynet');
$pdf->SetTitle('Character Sheet, USCM');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
//$pdf->setFooterData($tc=array(0,64,0), $lc=array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetFont('Helvetica', '', 10, '', true);
//$font = pdf_findfont($pdf, "Helvetica", "host", 0); 
//pdf_setfont($pdf,$font,10);

//$logoimage=pdf_open_image_file($pdf,"png","./resources/logoimage_sized.png","",0);
//pdf_place_image($pdf,$logoimage,50,700,0.7);
//$pdf->Image('./resources/logoimage_sized.png', 50, 700, 500, 146, 'PNG', '', '', true, 300, '', false, false, 1, false, false, false);

pdf_set_text_pos($pdf, 50, 690);
pdf_show($pdf,"Character sheet");
pdf_set_text_pos($pdf, 290, 690);
pdf_show($pdf,"www.uscm.tk");

$character = characterdata($_GET['character_id']);
pdf_set_text_pos($pdf, 50, 670);
pdf_show($pdf,"Player");
pdf_set_text_pos($pdf, 100, 670);
pdf_show($pdf,$character['pforname']." ".$character['plastname']);

pdf_set_text_pos($pdf, 50, 656);
pdf_show($pdf,"Email");
pdf_set_text_pos($pdf, 100, 656);
pdf_show($pdf,$character['emailadress']);

pdf_set_text_pos($pdf, 50, 627);
pdf_show($pdf,"Name");
pdf_set_text_pos($pdf, 100, 627);
pdf_show($pdf,$character['forname']." ".$character['lastname']);

pdf_set_text_pos($pdf, 50, 615);
pdf_show($pdf,"Rank");
pdf_set_text_pos($pdf, 100, 615);
pdf_show($pdf,$character['rank_long']);

pdf_set_text_pos($pdf, 50, 603);
pdf_show($pdf,"Specialty");
pdf_set_text_pos($pdf, 100, 603);
pdf_show($pdf,$character['specialty_name']);

pdf_set_text_pos($pdf, 50, 591);
pdf_show($pdf,"Enlisted");
pdf_set_text_pos($pdf, 100, 591);
pdf_show($pdf,$character['enlisted']);

pdf_set_text_pos($pdf, 50, 579);
pdf_show($pdf,"Age");
pdf_set_text_pos($pdf, 100, 579);
pdf_show($pdf,$character['age']);

pdf_set_text_pos($pdf, 50, 567);
pdf_show($pdf,"Gender");
pdf_set_text_pos($pdf, 100, 567);
pdf_show($pdf,$character['gender']);


// Attributes
fontbold($font,$pdf);
pdf_set_text_pos($pdf, $aapcolumnone, 534);
pdf_show($pdf,"Attributes");
pdf_set_text_pos($pdf, $aapcolumntwo, 534);
pdf_show($pdf,"Level");
pdf_set_text_pos($pdf, $attribpcolumnthree, 534);
pdf_show($pdf,"Bonus");


fontregular($font,$pdf);
$attributearray = characterattributes($_GET['character_id']);
$height = 520;
foreach ($attributearray as $attributeid => $attribute) {
	pdf_set_text_pos($pdf, $aapcolumnone, $height);
	pdf_show($pdf,$attribute['attribute_name']);
   
	pdf_set_text_pos($pdf, $aapcolumntwo, $height);
	pdf_show($pdf,$attribute['value']);
   $attributepointsbonus = attributebonus($_GET['character_id'], "modifier_basic_value", $attributeid);
   print_pdf_bonus($pdf, $attributepointsbonus);
   
   pdf_set_text_pos($pdf, $attribpcolumnthree, $height);
   $attributepointsbonus = attributebonus($_GET['character_id'], "modifier_dice_value", $attributeid);
   print_pdf_bonus($pdf, $attributepointsbonus);
	$height -= 12;
}

// Points
fontbold($font,$pdf);
pdf_set_text_pos($pdf, $aapcolumnone, 388);
pdf_show($pdf,"Points");

fontregular($font,$pdf);
pdf_set_text_pos($pdf, $aapcolumnone, 374);
pdf_show($pdf,"Experience:");
pdf_set_text_pos($pdf, $aapcolumntwo, 374);
pdf_show($pdf,$character['unusedxp']);

pdf_set_text_pos($pdf, $aapcolumnone, 362);
pdf_show($pdf,"Cool Points:");
pdf_set_text_pos($pdf, $aapcolumntwo, 362);
pdf_show($pdf,$character['coolpoints']);

pdf_set_text_pos($pdf, $aapcolumnone, 350);
pdf_show($pdf,"Awareness:");

pdf_setlinewidth($pdf,.5);
$awareness=awareness($_GET['character_id']);
$awarenessbonus=pointandlimitbonus($_GET['character_id'], "awareness");
if ($awarenessbonus['always'] != 0) {
   $awareness += $awarenessbonus['always'];
}
$awarenessbonussometimes = 0;
if (is_array($awarenessbonus['sometimes'])) {
   foreach ($awarenessbonus['sometimes'] as $bonus) {
      $awarenessbonussometimes += $bonus;
   }
}
$width=8;
$height=8;
$xpos=$aapcolumntwo;
if ($awarenessbonussometimes >= 0) {
   $white = $awareness; 
   $lightgrey = $awareness + $awarenessbonussometimes;
} else {
   // use addition since $awarenessbonussometimes is negative
   $white = $awareness + $awarenessbonussometimes;
   $lightgrey = $awareness;
}
/*print_r($white);
print_r($lightgrey);
print_r($awareness);
print_r($awarenessbonus);*/
pdf_setcolor($pdf,"fill","gray",0.8,0,0,0);
for ($i=1;$i<=10;$i++) {
   if ($i > $lightgrey) {
      pdf_setcolor($pdf,"fill","gray",0.4,0,0,0);
   }
   pdf_rect($pdf,$xpos,350,$width,$height);
   if ($i <= $white ) {
      pdf_stroke($pdf);
   } else {
      pdf_fill_stroke($pdf);
   }
   $xpos += $width;
}

pdf_setcolor($pdf,"fill","gray",0,0,0,0);

pdf_set_text_pos($pdf, $aapcolumnone, 338);
pdf_show($pdf,"Leadership:");
$leadership=leadership($_GET['character_id']);
$leadershipbonus=pointandlimitbonus($_GET['character_id'], "leadership");
if ($leadershipbonus['always'] != 0) {
   $leadership += $leadershipbonus['always'];
}
$leadershipbonussometimes = 0;
if (is_array($leadershipbonus['sometimes'])) {
   foreach ($leadershipbonus['sometimes'] as $bonus) {
      $leadershipbonussometimes += $bonus;
   }
}
$width=8;
$height=8;
$xpos=$aapcolumntwo;
pdf_setcolor($pdf,"fill","gray",0.9,0,0,0);
if ($leadershipbonussometimes >= 0) {
   $white = $leadership; 
   $lightgrey = $leadership + $leadershipbonussometimes;
} else {
   // use addition since $leadershipbonussometimes is negative
   $white = $leadership + $leadershipbonussometimes;
   $lightgrey = $leadership;
}
for ($i=1;$i<=10;$i++) {
   if ($i > $lightgrey) {
      pdf_setcolor($pdf,"fill","gray",0.4,0,0,0);
   }
	pdf_rect($pdf,$xpos,338,$width,$height);
	if ($i <= $white ) {
		pdf_stroke($pdf);
	} else {
      pdf_fill_stroke($pdf);
	}
	$xpos += $width;
}
pdf_setcolor($pdf,"fill","gray",0,0,0,0);

pdf_set_text_pos($pdf, $aapcolumnone, 326);
pdf_show($pdf,"Psycho:");
pdf_set_text_pos($pdf, $aapcolumntwo, 326);
pdf_show($pdf,$character['psychopoints']);
pdf_set_text_pos($pdf, $aapcolumnthree, 326);
$psychopointsbonus = pointandlimitbonus($_GET['character_id'], 'psychopoints');
print_pdf_bonus($pdf, $psychopointsbonus);

pdf_set_text_pos($pdf, $aapcolumnone, 314);
pdf_show($pdf,"Fear:");
pdf_set_text_pos($pdf, $aapcolumntwo, 314);
pdf_show($pdf,$character['fearpoints']);
pdf_set_text_pos($pdf, $aapcolumnthree, 314);
$fearpointsbonus = pointandlimitbonus($_GET['character_id'], 'fearpoints');
print_pdf_bonus($pdf, $fearpointsbonus);

pdf_set_text_pos($pdf, $aapcolumnone, 302);
pdf_show($pdf,"Exhaustion:");
pdf_set_text_pos($pdf, $aapcolumntwo, 302);
pdf_show($pdf,$character['exhaustionpoints']);
pdf_set_text_pos($pdf, $aapcolumnthree, 302);
$exhaustionpointsbonus = pointandlimitbonus($_GET['character_id'], 'exhaustionpoints');
print_pdf_bonus($pdf, $exhaustionpointsbonus);

pdf_set_text_pos($pdf, $aapcolumnone, 290);
pdf_show($pdf,"Trauma:");
pdf_set_text_pos($pdf, $aapcolumntwo, 290);
pdf_show($pdf,$character['traumapoints']);

pdf_set_text_pos($pdf, $aapcolumnfour, 374);
pdf_show($pdf,"Carry Capacity:");
pdf_set_text_pos($pdf, $aapcolumnfive, 374);
pdf_show($pdf,carrycapacity($_GET['character_id']));
pdf_set_text_pos($pdf, $aapcolumnsix, 374);
$carrybonus = carrycapacitybonus($_GET['character_id']);
print_pdf_bonus($pdf, $carrybonus);

pdf_set_text_pos($pdf, $aapcolumnfour, 362);
pdf_show($pdf,"Combat Load:");
pdf_set_text_pos($pdf, $aapcolumnfive, 362);
pdf_show($pdf,combatload($_GET['character_id']));
pdf_set_text_pos($pdf, $aapcolumnsix, 362);
$combatbonus = combatloadbonus($_GET['character_id']);
print_pdf_bonus($pdf, $combatbonus);

pdf_set_text_pos($pdf, $aapcolumnfour, 326);
pdf_show($pdf,"Psycho Limit:");
pdf_set_text_pos($pdf, $aapcolumnfive, 326);
pdf_show($pdf,psycholimit($_GET['character_id']));
pdf_set_text_pos($pdf, $aapcolumnsix, 326);
$psycholimitbonus = pointandlimitbonus($_GET['character_id'], 'psycholimit');
print_pdf_bonus($pdf, $psycholimitbonus);

pdf_set_text_pos($pdf, $aapcolumnfour, 314);
pdf_show($pdf,"Fear Limit:");
pdf_set_text_pos($pdf, $aapcolumnfive, 314);
pdf_show($pdf,fearlimit($_GET['character_id']));
pdf_set_text_pos($pdf, $aapcolumnsix, 314);
$fearlimitbonus = pointandlimitbonus($_GET['character_id'], 'fearlimit');
print_pdf_bonus($pdf, $fearlimitbonus);

pdf_set_text_pos($pdf, $aapcolumnfour, 302);
pdf_show($pdf,"Exhaustion Limit:");
pdf_set_text_pos($pdf, $aapcolumnfive, 302);
pdf_show($pdf,exhaustionlimit($_GET['character_id']));
pdf_set_text_pos($pdf, $aapcolumnsix, 302);
$exhaustionlimitbonus = pointandlimitbonus($_GET['character_id'], 'exhaustionlimit');
print_pdf_bonus($pdf, $exhaustionlimitbonus);


// Missions
fontbold($font,$pdf);
$missionheight=260;
pdf_set_text_pos($pdf, 50, $missionheight);
pdf_show($pdf,"Missions");
fontregular($font,$pdf);
$missionarray=missions($_GET['character_id'],"short");
$missionheight-=12;
foreach($missionarray as $mission) {
	pdf_set_text_pos($pdf, 50, $missionheight);
	pdf_show($pdf,$mission['mission_name']);
	pdf_set_text_pos($pdf, 90, $missionheight);
	pdf_show($pdf,$mission['text']);
	$missionheight -= 12;
}

// Medals
fontbold($font,$pdf);
$commendationheight=494;
pdf_set_text_pos($pdf, 220, 494);
pdf_show($pdf,"Commendations");
fontregular($font,$pdf);
$medalarray=medals($_GET['character_id']);
$commendationheight-=12;
foreach($medalarray as $medal) {
	pdf_set_text_pos($pdf, 220, $commendationheight);
	pdf_show($pdf,$medal['medal']);
	$commendationheight -= 12;
}

// Certificates
fontbold($font,$pdf);
$certificateheight=639;
pdf_set_text_pos($pdf, 220, $certificateheight);
pdf_show($pdf,"Certificates");
fontregular($font,$pdf);
$certificatearray=certificates($_GET['character_id'],$platoon_id);
$certificateheight-=12;
foreach($certificatearray as $certificate) {
	pdf_set_text_pos($pdf, 220, $certificateheight);
	pdf_show($pdf,$certificate['name']);
	$certificateheight -= 12;
}

// Traits
fontbold($font,$pdf);
$traitsheight=260;
pdf_set_text_pos($pdf, 170, $traitsheight);
pdf_show($pdf,"Traits");
fontregular($font,$pdf);
$traitarray=traits($_GET['character_id']);
$traitsheight-=12;
foreach($traitarray as $trait) {
	pdf_set_text_pos($pdf, 170, $traitsheight);
	pdf_show($pdf,$trait['trait_name']);
	$traitsheight -= 12;
}

// Advantages
fontbold($font,$pdf);
$advheight=200;
if ($traitsheight - 12 < $advheight) {
	$advheight = $traitsheight - 12;
}

pdf_set_text_pos($pdf, 170, $advheight);
pdf_show($pdf,"Advantages");
fontregular($font,$pdf);
$advarray=advantages($_GET['character_id']);
$advheight-=12;
foreach($advarray as $adv) {
	pdf_set_text_pos($pdf, 170, $advheight);
	pdf_show($pdf,$adv['advantage_name']);
	$advheight -= 12;
}

// Disadvantages
fontbold($font,$pdf);
$disadvheight=260;
pdf_set_text_pos($pdf, 280, $disadvheight);
pdf_show($pdf,"Disadvantages");
fontregular($font,$pdf);
$disadvarray=disadvantages($_GET['character_id']);
$disadvheight-=12;
foreach($disadvarray as $disadv) {
	pdf_set_text_pos($pdf, 280, $disadvheight);
	pdf_show($pdf,$disadv['disadvantage_name']);
	$disadvheight -= 12;
}

// Skills
fontbold($font,$pdf);
$skillsheight=710;
pdf_set_text_pos($pdf, 380, $skillsheight);
pdf_show($pdf,"Skills");
pdf_set_text_pos($pdf, 470, $skillsheight);
pdf_show($pdf,"Level");
pdf_set_text_pos($pdf, 500, $skillsheight);
pdf_show($pdf,"Bonus");
fontregular($font,$pdf);
$skillarray = characterskills($_GET['character_id'],"Weapons",$certificatearray);
$skillsheight-=12;
foreach ($skillarray as $skill) {
	pdf_set_text_pos($pdf, 380, $skillsheight);
	pdf_show($pdf,$skill['name']);

	pdf_set_text_pos($pdf, 480, $skillsheight);
	pdf_show($pdf,$skill['value']);

	pdf_set_text_pos($pdf, 500, $skillsheight);
	if ($skill['bonus_always'] != 0) {
	   if ($skill['bonus_always'] > 0) {
	      $bonussign = "+";
	   } else {
	      $bonussign = "-";
	   }
		pdf_show($pdf,$bonussign.$skill['bonus_always']." ");
	}
   if (is_array($skill['bonus_sometimes'])) {
      foreach ($skill['bonus_sometimes'] as $bonus) {
         if ($bonus > 0) {
            $bonussign = "+";
         } else {
            $bonussign = "-";
         }
         pdf_show($pdf," (".$bonussign.$bonus.") ");
      }
   }
	$skillsheight -= 12;
}
$skillsheight -= 12;
$skillarray = characterskills($_GET['character_id'],"Physical",$certificatearray);
foreach ($skillarray as $skill) {
	pdf_set_text_pos($pdf, 380, $skillsheight);
	pdf_show($pdf,$skill['name']);

	pdf_set_text_pos($pdf, 480, $skillsheight);
	pdf_show($pdf,$skill['value']);

	pdf_set_text_pos($pdf, 500, $skillsheight);
if ($skill['bonus_always'] != 0) {
      if ($skill['bonus_always'] > 0) {
         $bonussign = "+";
      } else {
         $bonussign = "-";
      }
      pdf_show($pdf,$bonussign.$skill['bonus_always']);
   }
   if (is_array($skill['bonus_sometimes'])) {
      foreach ($skill['bonus_sometimes'] as $bonus) {
         if ($bonus > 0) {
            $bonussign = "+";
         } else {
            $bonussign = "-";
         }
         pdf_show($pdf," (".$bonussign.$bonus.") ");
      }
   }
	$skillsheight -= 12;
}
$skillsheight -= 12;
$skillarray = characterskills($_GET['character_id'],"Vehicles",$certificatearray);
foreach ($skillarray as $skill) {
	pdf_set_text_pos($pdf, 380, $skillsheight);
	pdf_show($pdf,$skill['name']);

	pdf_set_text_pos($pdf, 480, $skillsheight);
	pdf_show($pdf,$skill['value']);

	pdf_set_text_pos($pdf, 500, $skillsheight);
if ($skill['bonus_always'] != 0) {
      if ($skill['bonus_always'] > 0) {
         $bonussign = "+";
      } else {
         $bonussign = "-";
      }
      pdf_show($pdf,$bonussign.$skill['bonus_always']);
   }
   if (is_array($skill['bonus_sometimes'])) {
      foreach ($skill['bonus_sometimes'] as $bonus) {
         if ($bonus > 0) {
            $bonussign = "+";
         } else {
            $bonussign = "-";
         }
         pdf_show($pdf," (".$bonussign.$bonus.") ");
      }
   }
	$skillsheight -= 12;
}
$skillsheight -= 12;
$skillarray = characterskills($_GET['character_id'],"Other",$certificatearray);
foreach ($skillarray as $skill) {
	pdf_set_text_pos($pdf, 380, $skillsheight);
	pdf_show($pdf,$skill['name']);

	pdf_set_text_pos($pdf, 480, $skillsheight);
	pdf_show($pdf,$skill['value']);

	pdf_set_text_pos($pdf, 500, $skillsheight);
if ($skill['bonus_always'] != 0) {
      if ($skill['bonus_always'] > 0) {
         $bonussign = "+";
      } else {
         $bonussign = "-";
      }
      pdf_show($pdf,$bonussign.$skill['bonus_always']);
   }
   if (is_array($skill['bonus_sometimes'])) {
      foreach ($skill['bonus_sometimes'] as $bonus) {
         if ($bonus > 0) {
            $bonussign = "+";
         } else {
            $bonussign = "-";
         }
         pdf_show($pdf," (".$bonussign.$bonus.") ");
      }
   }
	$skillsheight -= 12;
}
$skillsheight -= 12;
$skillarray = characterskills($_GET['character_id'],"Languages",$certificatearray);
foreach ($skillarray as $skill) {
	pdf_set_text_pos($pdf, 380, $skillsheight);
	pdf_show($pdf,$skill['name']);

	pdf_set_text_pos($pdf, 480, $skillsheight);
	pdf_show($pdf,$skill['value']);

	pdf_set_text_pos($pdf, 500, $skillsheight);
//	pdf_show($pdf,$skill['bonus']);
	$skillsheight -= 12;
}


pdf_end_page($pdf);
pdf_close($pdf);

$buf = pdf_get_buffer($pdf);
$len = strlen($buf);

header("Content-type: application/pdf");
header("Content-Length: $len");
header("Content-Disposition: inline; filename=character.pdf");
print $buf;
}
