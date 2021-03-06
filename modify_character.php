<?php

$characterId = $_GET['character_id'];
$userController = new UserController();
$platoonController = new PlatoonController();
$rankController = new RankController();
$characterController = new CharacterController();
$character = new Character($characterId);
$character = $characterController->getCharacter($characterId);
$user = $userController->getCurrentUser();
$canmodify = FALSE;

if ($user->getId() == $character->getPlayerId() || $user->isAdmin() || $user->isGm()) {
  $player = $character->getPlayer();
  $playerPlatoon = NULL;
    ?>

    <?php
	if ($user->isAdmin() || ($user->isGm() && $character->getPlatoonId() == $user->getPlatoonId())) {
		$canmodify = TRUE;
	}
	
	if ($canmodify) { ?><form method="post" action="character.php?action=update_character"><?php } ?>
        <table width="50%"  border="0">
            <tr>
                <td><a href="index.php?url=list_characters.php&platoon=<?php echo $character->getPlatoonId(); ?>">Back</a></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><a class="colorfont" href="./create_sheet.php?character_id=<?php echo $characterId; ?>">Generate character sheet</a></td>
            </tr>
            <tr>
                <td>Player</td>
                <td><?php
    echo $player->getName();
    ?>
                    <input type="hidden" name="player" value="<?php echo $player->getId(); ?>">
                    <input type="hidden" name="character" value="<?php echo $characterId; ?>">

                </td>
                <td>&nbsp;</td>
                <td><a class="colorfont" href="index.php?url=character_more.php&character_id=<?php echo $characterId; ?>">Do you want to know more?</a></td>
            </tr>
            <tr>
                <td>Forname</td>
                <td><input type="text" name="forname" value="<?php echo $character->getGivenName(); ?>"></td>
                <td>Lastname</td>
                <td><input type="text" name="lastname" value="<?php echo $character->getSurname(); ?>"></td>
            </tr>
            <tr>
                <td>Platoon</td>
                <td><?php
                $platoons = $platoonController->getPlatoons();
    ?>
                    <select name="platoon">
    <?php foreach ($platoons as $platoon) {
            $platoonId = $platoon->getId();
            if ($platoonId == $character->getPlatoonId()) {
              $playerPlatoon = $platoon;
            } ?>
                            <option value="<?php echo $platoonId; ?>" <?php echo ($platoonId == $character->getPlatoonId()) ? ("selected") : (""); ?> ><?php echo $platoon->getName(); ?></option>
    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Specialty</td>
                <td><?php
          $specialties = $characterController->getSpecialties();
    ?>
                    <select name="specialty">
                    <?php foreach ($specialties as $specialty) {
                      $specialtyId = $specialty->getId(); ?>
                            <option <?php echo ($specialtyId == $character->getSpecialtyId()) ? ("selected") : (""); ?> value="<?php echo $specialtyId; ?>" >
                            <?php echo $specialty->getName(); ?></option>
                        <?php } ?>
                    </select></td>
                <td>Grad (Bör ej ändras om karaktären har fått befordran)</td>
                <td><?php
                    $ranks = $rankController->getRanks();
                    ?>
                    <select name="rank">
    <?php foreach ($ranks as $rank) { ?>
                            <option <?php echo ($rank->getId() == $character->getRankId()) ? ("selected") : (""); ?> value="<?php echo $rank->getId(); ?>" >
        <?php echo $rank->getName(); ?></option>
    <?php } ?>
                    </select></td>
            </tr>
            <tr>
                <td>Enlisted</td>
                <td><input type="text" name="enlisted" value="<?php echo $character->getEnlistedDate(); ?>">  <br/>format: YYYYMMDD</td>
                <td>Age</td>
                <td><input type="text" name="age" value="<?php echo $character->getAge(); ?>"></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>
                    <select name="gender">
                        <option <?php echo ($character->getGender() == "Male") ? ("selected ") : (""); ?>value="Male" >Male</option>
                        <option <?php echo ($character->getGender() == "Female") ? ("selected ") : (""); ?>value="Female">Female</option>
                    </select>
                </td>
            </tr>
            <?php
            //Ta ut alla attribut
            $allattributes = $characterController->getAttributes();
            $characterAttributes = $character->getAttributes();
            foreach ($allattributes as $attribute) {
              $attributeId = $attribute->getId();
                ?>
                <tr>
                    <td><?php echo $attribute->getName(); ?></td>
                    <td><input name="attribute[<?php echo $attributeId; ?>]" type="text" value="<?php
                    echo (array_key_exists($attributeId, $characterAttributes)) ? ($characterAttributes[$attributeId]) : ("");

                    ?>" size="2"></td>
                </tr>
    <?php } ?>
            <tr>
                <td>Unused XP</td>
                <td><input type="text" name="xp" value="<?php echo $character->getUnusedXp(); ?>" size="2"></td>
            </tr>
            <tr>
                <td>Awareness Points</td>
                <td><input type="text" name="ap" value="<?php echo $character->getAwarenessPoints(); ?>" size="2"></td>
                <td>Cool Points</td>
                <td><input type="text" name="cp" value="<?php echo $character->getCoolPoints(); ?>" size="2"></td>
            </tr>
            <tr>
                <td>Exhaustion Points</td>
                <td><input type="text" name="ep" value="<?php echo $character->getExhaustionPoints(); ?>" size="2"></td>
                <td>Fear Points</td>
                <td><input type="text" name="fp" value="<?php echo $character->getFearPoints(); ?>" size="2"></td>
            </tr>
            <tr>
                <td>Leadership Points</td>
                <td><input type="text" name="lp" value="<?php echo $character->getLeadershipPoints(); ?>" size="2"></td>
                <td>Psycho Points</td>
                <td><input type="text" name="pp" value="<?php echo $character->getPsychoPoints(); ?>" size="2"></td>
            </tr>
            <tr>
                <td>Trauma Points</td>
                <td><input type="text" name="tp" value="<?php echo $character->getTraumaPoints(); ?>" size="2"></td>
                <td>Mental Points</td>
                <td><input type="text" name="mp" value="<?php echo $character->getMentalPoints(); ?>" size="2"></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="status">
                        <option <?php echo ($character->getStatus() == "Active") ? ("selected ") : (""); ?>value="Active" >Active</option>
                        <option <?php echo ($character->getStatus() == "PoW") ? ("selected ") : (""); ?>value="PoW">PoW</option>
                        <option <?php echo ($character->getStatus() == "Retired") ? ("selected ") : (""); ?>value="Retired">Retired</option>
                        <option <?php echo ($character->getStatus() == "Dead") ? ("selected ") : (""); ?>value="Dead">Dead</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Status Desc.</td>
                <td colspan="3"><input type="text" name="status_desc" value="<?php echo $character->getStatusDescription(); ?>" size="60"></td>
            </tr>
            <?php
            //Ta ut alla skills
            $side = 0;
            $allSkills = $characterController->getSkillsGrouped();
            $characterSkills = $character->getSkillsGrouped();
            foreach($allSkills as $skill) {
              $skillId = $skill->getId();
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td colspan="2"><?php echo $skill->getName(); ?>
                  <input style="float: right; margin-right: 20px;" type="text" name="skills[<?php echo $skillId; ?>]" value="<?php echo (array_key_exists($skillId, $characterSkills)) ? ($characterSkills[$skillId]['value']) : (""); ?>" size="2">
                  <input type="hidden" name="optional[<?php echo $skillId; ?>]" value="<?php echo $skill->getOptional(); ?>">
                </td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //Traits
            $side = 0;
            $allTraits = $characterController->getTraits();
            $characterTraits = $character->getTraits();
            foreach ($allTraits as $trait) {
              $traitId = $trait->getId();
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td colspan="2"><input type="checkbox" name="traits[<?php echo $traitId; ?>]" <?php echo (array_key_exists($traitId, $characterTraits)) ? ("checked") : (""); ?> >
                <?php echo $trait->getName(); ?></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            $side = 0;
            $allAdvantages = $characterController->getAdvantages();
            foreach ($allAdvantages as $advantage) {
              $advantageId = $advantage->getId();
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td colspan="2"><input type="checkbox" name="advs[<?php echo $advantageId; ?>]" <?php
                  echo ($character->hasCharacterAdvantage($advantageId)) ? ("checked") : (""); ?> >
                <?php echo $advantage->getName() . " (" . $advantage->getValue() . ")"; ?>
                </td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            $side = 0;
            $allDisadvantages = $characterController->getDisadvantages();
            foreach ($allDisadvantages as $disadvantage) {
              $disadvantageId = $disadvantage->getId();
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td colspan="2"><input type="checkbox" name="disadvs[<?php echo $disadvantageId; ?>]" <?php echo ($character->hasCharacterDisadvantage($disadvantageId)) ? ("checked") : (""); ?> >
                <?php echo $disadvantage->getName() . " (" . $disadvantage->getValue() . ")"; ?></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //Certificates
            $allPlatoonCertificates = array();
            foreach ($playerPlatoon->getCertificates() as $certificate) {
              $allPlatoonCertificates[] = $certificate->getId();
            }
            $allCertificates = $characterController->getCertificates();
            $characterCertificates = $character->getCertsForCharacterWithoutReqCheck();
            $enumerate_disadv = TRUE;
            $side = 0;
            foreach ($allCertificates as $certificate) {
              $certificateId = $certificate->getId();
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td colspan="2"><input type="checkbox" name="certs[<?php echo $certificateId; ?>]" <?php echo (array_key_exists($certificateId, $characterCertificates)) ? ("checked ") : ("");
                echo (in_array($certificateId, $allPlatoonCertificates)) ? ("disabled ") : (""); ?> >
                <?php echo $certificate->getName(); ?>
                </td>
        <?php
        echo ($side == 1) ? ("</tr>") : ("");
        $side = ($side + 1) % 2;
    }
    ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="thead">Encounters;</td>
            </tr>
            <tr>
                <td>Alien</td>
				<td><input type="checkbox" name="cbalien" <?php echo ($character->getEncounterAlien()==1) ? ('checked="1"') : (""); ?>></td>
            </tr>
            <tr>
                <td>Grey</td>
				<td><input type="checkbox" name="cbgrey" <?php echo ($character->getEncounterGrey()==1) ? ('checked="1"') : (""); ?>></td>
            </tr>
            <tr>
                <td>Predator</td>
				<td><input type="checkbox" name="cbpredator" <?php echo ($character->getEncounterPredator()==1) ? ('checked="1"') : (""); ?>></td>
            </tr>
            <tr>
                <td>AI</td>
				<td><input type="checkbox" name="cbai" <?php echo ($character->getEncounterAI()==1) ? ('checked="1"') : (""); ?>></td>
            </tr>
            <tr>
                <td>Arachnid</td>
				<td><input type="checkbox" name="cbarachnid" <?php echo ($character->getEncounterArachnid()==1) ? ('checked="1"') : (""); ?>></td>
            </tr>

    <?php
    if ($canmodify) {
        ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Submit"></td>
                </tr>
    <?php } ?>
        </table>
    <?php if ($canmodify) { ?></form> <?php }
}
?>
