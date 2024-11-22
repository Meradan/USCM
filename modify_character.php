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
  <h1 class="heading heading-h1">
    Modify character
    <span class="span">
      <a href="index.php?url=list_characters.php&platoon=<?php echo $character->getPlatoonId(); ?>">Back</a>
    </span>
  </h1>
  <h2 class="heading heading-h2">
    <?php echo $character->getGivenName(); ?> <?php echo $character->getSurname(); ?>
    <span class="span">
      <a href="index.php?url=character_more.php&character_id=<?php echo $characterId; ?>">Do you want to know more?</a>
    </span>
  </h2>

  <a href="create_sheet.php?character_id=<?php echo $characterId; ?>" target="_blank">Generate character sheet</a>

    <?php
	if ($user->isAdmin() || ($user->isGm() && $character->getPlatoonId() == $user->getPlatoonId())) {
		$canmodify = TRUE;
	}

	if ($canmodify) { ?><form class="form" method="post" action="character.php?action=update_character"><?php } ?>
    <input type="hidden" name="player" value="<?php echo $player->getId(); ?>">
    <input type="hidden" name="character" value="<?php echo $characterId; ?>">

  <div class="grid grid--1x2 mt-20">
    <div>
      Player
      <div>
        <?php echo $player->getName(); ?>
      </div>
    </div>
    <label for="platoon">
      Platoon
      <?php
                $platoons = $platoonController->getPlatoons();
    ?>
                    <select id="platoon" name="platoon">
    <?php foreach ($platoons as $platoon) {
            $platoonId = $platoon->getId();
            if ($platoonId == $character->getPlatoonId()) {
              $playerPlatoon = $platoon;
            } ?>
                            <option value="<?php echo $platoonId; ?>" <?php echo ($platoonId == $character->getPlatoonId()) ? ("selected") : (""); ?> ><?php echo $platoon->getName(); ?></option>
    <?php } ?>
                    </select>
    </label>

    <label for="forname">
      Firstname
      <input type="text" id="forname" name="forname" value="<?php echo $character->getGivenName(); ?>">
    </label>

    <label for="lastname">
      Lastname
      <input type="text" id="lastname" name="lastname" value="<?php echo $character->getSurname(); ?>">
    </label>

    <label for="specialty">
      Specialty
      <select id="specialty" name="specialty">
        <?php $specialties = $characterController->getSpecialties(); ?>
        <?php foreach ($specialties as $specialty) {
                      $specialtyId = $specialty->getId(); ?>
          <option <?php echo ($specialtyId == $character->getSpecialtyId()) ? ("selected") : (""); ?> value="<?php echo $specialtyId; ?>" >
                            <?php echo $specialty->getName(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="rank">
      Rank (bör ej ändras om karaktären har fått befordran)
      <select id="rank" name="rank">
        <?php $ranks = $rankController->getRanks(); ?>
        <?php foreach ($ranks as $rank) { ?>
          <option <?php echo ($rank->getId() == $character->getRankId()) ? ("selected") : (""); ?> value="<?php echo $rank->getId(); ?>" >
        <?php echo $rank->getName(); ?></option>
        <?php } ?>
      </select>
    </label>

    <label for="enlisted">
      Enlisted (format: YYYYMMDD)
      <input type="text" id="enlisted" name="enlisted" value="<?php echo $character->getEnlistedDate(); ?>">
    </label>

    <label for="age">
      Age
      <input type="number" id="age" name="age" value="<?php echo $character->getAge(); ?>">
    </label>

    <label for="gender">
      Gender
      <select id="gender" name="gender">
        <option <?php echo ($character->getGender() == "Male") ? ("selected ") : (""); ?>value="Male">Male</option>
        <option <?php echo ($character->getGender() == "Female") ? ("selected ") : (""); ?>value="Female">Female</option>
      </select>
    </label>

    <label for="status">
      Status
      <select id="status" name="status">
          <option <?php echo ($character->getStatus() == "Active") ? ("selected ") : (""); ?>value="Active" >Active</option>
          <option <?php echo ($character->getStatus() == "PoW") ? ("selected ") : (""); ?>value="PoW">PoW</option>
          <option <?php echo ($character->getStatus() == "Retired") ? ("selected ") : (""); ?>value="Retired">Retired</option>
          <option <?php echo ($character->getStatus() == "Dead") ? ("selected ") : (""); ?>value="Dead">Dead</option>
        </select>
    </label>

    <label for="status_desc">
      Status Description
      <input type="text" id="status_desc" name="status_desc" value="<?php echo $character->getStatusDescription(); ?>" size="60"></td>
    </label>
  </div>

  <fieldset class="form--inline grid grid--small">
    <legend>Attributes</legend>
            <?php
            //Ta ut alla attribut
            $allattributes = $characterController->getAttributes();
            $characterAttributes = $character->getAttributes();
            foreach ($allattributes as $attribute) {
              $attributeId = $attribute->getId();
                ?>
              <label for="attribute_<?php echo $attribute->getId();?>">
                <?php echo $attribute->getName();?>
                <input
                  type="number"
                  id="attribute_<?php echo $attribute->getId();?>"
                  name="attribute[<?php echo $attribute->getId();?>]"
                  min="0"
                  max="10"
                  value="<?php echo (array_key_exists($attributeId, $characterAttributes)) ? ($characterAttributes[$attributeId]) : (""); ?>"
                >
              </label>
    <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Points</legend>

    <label for="ap">
      Awareness Points
      <input type="number" id="ap" name="ap" min="0" value="<?php echo $character->getAwarenessPoints(); ?>">
    </label>

    <label for="cp">
      Cool Points
      <input type="number" id="cp" name="cp" min="0" value="<?php echo $character->getCoolPoints(); ?>">
    </label>

    <label for="ep">
      Exhaustion Points
      <input type="number" id="ep" name="ep" min="0" value="<?php echo $character->getExhaustionPoints(); ?>">
    </label>

    <label for="fp">
      Fear Points
      <input type="number" id="fp" name="fp" min="0" value="<?php echo $character->getFearPoints(); ?>">
    </label>

    <label for="lp">
      Leadership Points
      <input type="number" id="lp" name="lp" min="0" value="<?php echo $character->getLeadershipPoints(); ?>">
    </label>

    <label for="pp">
      Psycho Points
      <input type="number" id="pp" name="pp" min="0" value="<?php echo $character->getPsychoPoints(); ?>">
    </label>

    <label for="tp">
      Trauma Points
      <input type="number" id="tp" name="tp" min="0" value="<?php echo $character->getTraumaPoints(); ?>">
    </label>

    <label for="mp">
      Mental Points
      <input type="number" id="mp" name="mp" min="0" value="<?php echo $character->getMentalPoints(); ?>">
    </label>

    <label for="xp">
      Unused XP
      <input type="number" id="xp" name="xp" min="0" value="<?php echo $character->getUnusedXp(); ?>">
    </label>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Skills</legend>
            <?php
            $allSkills = $characterController->getSkillsGrouped();
            $characterSkills = $character->getSkillsGrouped();
            foreach($allSkills as $skill) {
              $skillId = $skill->getId();
            ?>
    <label for="skills_<?php echo $skillId; ?>">
      <?php echo $skill->getName(); ?>
                  <input type="number" min="0" max="10" id="skills_<?php echo $skillId; ?>" name="skills[<?php echo $skillId; ?>]" value="<?php echo (array_key_exists($skillId, $characterSkills)) ? ($characterSkills[$skillId]['value']) : (""); ?>">
    </label>
                  <input type="hidden" name="optional[<?php echo $skillId; ?>]" value="<?php echo $skill->getOptional(); ?>">
            <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Traits</legend>
            <?php
            $allTraits = $characterController->getTraits();
            $characterTraits = $character->getTraits();
            foreach ($allTraits as $trait) {
              $traitId = $trait->getId();
                ?>
              <label for="traits_<?php echo $traitId; ?>">
                <?php echo $trait->getName(); ?>
                <input type="checkbox" id="traits_<?php echo $traitId; ?>" name="traits[<?php echo $traitId; ?>]" <?php echo (array_key_exists($traitId, $characterTraits)) ? ("checked") : (""); ?>>
              </label>
            <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Advantages</legend>
            <?php
            $allAdvantages = $characterController->getAdvantages();
            foreach ($allAdvantages as $advantage) {
              $advantageId = $advantage->getId();
            ?>
              <label for="advs_<?php echo $advantageId; ?>">
                <?php echo $advantage->getName() . " (" . $advantage->getValue() . ")"; ?>
                <input type="checkbox" id="advs_<?php echo $advantageId; ?>" name="advs[<?php echo $advantageId; ?>]" <?php echo ($character->hasCharacterAdvantage($advantageId)) ? ("checked") : (""); ?> >
              </label>
            <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Disadvantages</legend>
            <?php
            $allDisadvantages = $characterController->getDisadvantages();
            foreach ($allDisadvantages as $disadvantage) {
              $disadvantageId = $disadvantage->getId();
                ?>
              <label for="disadvs_<?php echo $disadvantageId; ?>">
                <?php echo $disadvantage->getName() . " (" . $disadvantage->getValue() . ")"; ?>
                <input type="checkbox" id="disadvs_<?php echo $disadvantageId; ?>" name="disadvs[<?php echo $disadvantageId; ?>]" <?php echo ($character->hasCharacterDisadvantage($disadvantageId)) ? ("checked") : (""); ?>>
              </label>
            <?php } ?>
  </fieldset>

  <fieldset class="form--inline grid grid--small">
    <legend>Certificates</legend>
            <?php
            $allPlatoonCertificates = array();
            foreach ($playerPlatoon->getCertificates() as $certificate) {
              $allPlatoonCertificates[] = $certificate->getId();
            }
            $allCertificates = $characterController->getCertificates();
            $characterCertificates = $character->getCertsForCharacterWithoutReqCheck();
            $enumerate_disadv = TRUE;
            foreach ($allCertificates as $certificate) {
              $certificateId = $certificate->getId();
                ?>
              <label for="certs_<?php echo $certificateId; ?>">
                <?php echo $certificate->getName(); ?>
                <input type="checkbox" id="certs_<?php echo $certificateId; ?>" name="certs[<?php echo $certificateId; ?>]" <?php echo (array_key_exists($certificateId, $characterCertificates)) ? ("checked ") : (""); echo (in_array($certificateId, $allPlatoonCertificates)) ? ("disabled ") : (""); ?>>
              </label>
        <?php } ?>
  </fieldset>

    <fieldset class="form--inline grid grid--small">
      <legend>Encounters</legend>

      <label for="cbalien">
        Alien
        <input type="checkbox" id="cbalien" name="cbalien" <?php echo ($character->getEncounterAlien()==1) ? ('checked="1"') : (""); ?>>
      </label>

      <label for="cbgrey">
        Grey
        <input type="checkbox" id="cbgrey" name="cbgrey" <?php echo ($character->getEncounterGrey()==1) ? ('checked="1"') : (""); ?>>
      </label>

      <label for="cbpredator">
        Predator
        <input type="checkbox" id="cbpredator" name="cbpredator" <?php echo ($character->getEncounterPredator()==1) ? ('checked="1"') : (""); ?>>
      </label>

      <label for="cbai">
        AI/Android
        <input type="checkbox" id="cbai" name="cbai" <?php echo ($character->getEncounterAI()==1) ? ('checked="1"') : (""); ?>>
      </label>

      <label for="cbarachnid">
        Arachnid
        <input type="checkbox" id="cbarachnid" name="cbarachnid" <?php echo ($character->getEncounterArachnid()==1) ? ('checked="1"') : (""); ?>>
      </label>
    </fieldset>

    <?php if ($canmodify) { ?>
      <input class="button" type="submit" value="Modify Character">
    </form>
    <?php }
}
?>
