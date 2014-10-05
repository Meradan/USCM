<?php

$characterId = $_GET['character_id'];
$character = new Character($characterId);
$user = new Player();

$userid = $character->getPlayer();

if ($user->getPlayerId() == $character->getPlayer() || $user->isAdmin() || $user->isGm()) {
  $platoon_id = $character->getPlatoon();
  $player = new Player($character->getPlayer());
  $player->loadData();
  $character->loadData();
  $playerPlatoon = NULL;
    ?>


    <?php if ( $user->isAdmin() || $user->isGm()) { ?><form method="post" action="character.php?action=update_character"><?php } ?>
        <table width="50%"  border="0">
            <tr>
                <td>Player</td>
                <td><?php
    echo $player->getName();
    ?>
                    <input type="hidden" name="player" value="<?php echo $player->getPlayerId(); ?>">
                    <input type="hidden" name="character" value="<?php echo $characterId; ?>">

                </td>
                <td>&nbsp;</td>
                <td><a class="colorfont" href="./create_sheet.php?character_id=<?php echo $characterId; ?>">Generate character sheet</a></td>
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
                $platoons = getPlatoons();
    ?>
                    <select name="platoon">
    <?php foreach ($platoons as $platoon) {
            $platoonId = $platoon->getId();
            if ($platoonId == $character->getPlatoon()) {
              $playerPlatoon = $platoon;
            } ?>
                            <option value="<?php echo $platoonId; ?>" <?php echo ($platoonId == $character->getPlatoon()) ? ("selected") : (""); ?> ><?php echo $platoon->getName(); ?></option>
    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Specialty</td>
                <td><?php
    $specialties = getSpecialties();
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
                    $ranks = getRanks();
                    ?>
                    <select name="rank">
    <?php foreach ($ranks as $rank) { ?>
                            <option <?php echo ($rank['id'] == $character->getRankId()) ? ("selected") : (""); ?> value="<?php echo $rank['id']; ?>" >
        <?php echo $rank['rank_long']; ?></option>
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
            $allattributes = getAttributes();
            $characterAttributes = $character->getAttributes();
            foreach ($allattributes as $attribute) {
              $attributeId = $attribute['id'];
                ?>
                <tr>
                    <td><?php echo $attribute['attribute_name']; ?></td>
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
            $allSkills = getSkillsGrouped();
            $characterSkills = $character->getSkillsGrouped();
            foreach($allSkills as $skill) {
              $skillId = $skill['id'];
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $skill['skill_name']; ?></td>
                <td><input type="text" name="skills[<?php echo $skillId; ?>]" value="<?php echo (array_key_exists($skillId, $characterSkills)) ? ($characterSkills[$skillId]['value']) : (""); ?>" size="2">
                    <input type="hidden" name="optional[<?php echo $skillId; ?>]" value="<?php echo $skill['optional']; ?>">
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
            $allTraits = getTraits();
            $characterTraits = $character->getTraits();
            foreach ($allTraits as $trait) {
              $traitId = $trait['id'];
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $trait['trait_name']; ?></td>
                <td><input type="checkbox" name="traits[<?php echo $traitId; ?>]" <?php echo (array_key_exists($traitId, $characterTraits)) ? ("checked") : (""); ?> ></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //advantages
            $side = 0;
            $allAdvantages = getAdvantages();
            $characterAdvantages = $character->getAdvantages();
            foreach ($allAdvantages as $advantage) {
              $advantageId = $advantage['id'];
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $advantage['advantage_name'] . " (" . $advantage['value'] . ")"; ?></td>
                <td><input type="checkbox" name="advs[<?php echo $advantageId; ?>]" <?php
                  echo (array_key_exists($advantageId, $characterAdvantages)) ? ("checked") : (""); ?> ></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //disadvantages
            $side = 0;
            $allDisadvantages = getDisadvantages();
            $characterDisadvantages = $character->getDisadvantages();
            foreach ($allDisadvantages as $disadvantage) {
              $disadvantageId = $disadvantage['id'];
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $disadvantage['disadvantage_name'] . " (" . $disadvantage['value'] . ")"; ?></td>
                <td><input type="checkbox" name="disadvs[<?php echo $disadvantageId; ?>]" <?php echo (array_key_exists($disadvantageId, $characterDisadvantages)) ? ("checked") : (""); ?> ></td>
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
              $allPlatoonCertificates[] = $certificate['certificate_id'];
            }
            $allCertificates = getCertificates();
            $characterCertificates = $character->getCertsForCharacterWithoutReqCheck();
            $enumerate_disadv = TRUE;
            $side = 0;
            foreach ($allCertificates as $certificate) {
              $certificateId = $certificate['id'];
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $certificate['name']; ?></td>
                <td><input type="checkbox" name="certs[<?php echo $certificateId; ?>]" <?php echo (array_key_exists($certificateId, $characterCertificates)) ? ("checked ") : ("");
                echo (in_array($certificateId, $allPlatoonCertificates)) ? ("disabled ") : (""); ?> ></td>
        <?php
        echo ($side == 1) ? ("</tr>") : ("");
        $side = ($side + 1) % 2;
    }
    ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
    <?php
    $missions = $character->getMissionsLong();
    foreach ($missions as $mission) {
        ?>
                <tr>
                    <td><?php echo $mission['mission_name']; ?></td>
                    <td colspan="3"><?php echo $mission['text']; ?></td>
                </tr>
    <?php }
    if ($user->isAdmin() || $user->isGm()) {
        ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Submit"></td>
                </tr>
    <?php } ?>
        </table>
    <?php if ($user->isAdmin() || $user->isGm()) { ?></form> <?php }
}
?>
