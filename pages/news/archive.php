<h1 class="heading heading-h1">News archive</h1>

<?php
$newsController = new NewsController();
$listOfNews = $newsController->getNewsOlderThanAYear();
foreach ($listOfNews as $news) {
?>
  <article class="news">
    <header>
      <h2 class="heading"><?php echo $news->getDate(); ?></h2>
      <?php echo $news->getWrittenBy(); ?>
    </header>
    <div>
      <?php echo $news->getText(); ?>
    </div>
  </article>
<?php } ?>

<h2 class="heading heading-h2">Really, really old news</h2>

<div class="colorfont">2004-11-28 Oskar</div>
<div>Debriefing för M83 upplagd.</div><br/>

<div class="colorfont">2004-11-27 Oskar</div>
<div>Uppdaterat med lite missions och debriefingar. M83 kommer spelas ikväll och det finns för ovanlighetens skull fortfarande plats över om någon mer vill vara med.</div><br/>

<div class="colorfont">2004-11-22 Oskar</div>
<div>Debriefing för M80 är upplagd.</div><br/>

<div class="colorfont">2004-11-19 Oskar</div>
<div>Besök gärna nyhetssidan Galactic News Network som innehåller nyhetsreportage om händelser inom USCM-spelvärlden.<br><br/>

<div>Vi har även fått tre nya medaljer i USCM-kampanjen, se längst ner på personalsidan för mer detaljer.</div><br/>

<div class="colorfont">2004-11-04 Oskar</div>
<div>Nu ger sig USCM iväg till Borås för att spelleda på <a href="http://bsk.sverok.net/">Borås Spelkonvent</a>. Vi kommer att finnas som drop-in arrangemang under hela konventet.</div><br/>

<div class="colorfont">2004-10-31 Oskar</div>
<div>Vårt nya regelhäfte är färdigskrivet, det blir traditionsenligt cocktailparty med uppträdande av UA Marine Corps Band i Tottes aula!.</div><br/>

<div class="colorfont">2004-10-28 Oskar</div>
<div>Nu har Nr #2 av vår kampanjtidning The Colonial Correspondent kommit ut. Vi hälsar även en ny USCM-pluton välkommen till 4:e brigaden. Det är Markhams Daring Dozens som nyligen har spelat sitt första uppdrag i Kalmar.</div><br/>

<div class="colorfont">2004-10-27 Oskar</div>
<div>M79 spelades under dagen och debriefingen ligger uppe nu. Glöm inte att anmäla er till <a href="http://bsk.sverok.net/">BSK</a> och passa på att spela USCM när ni är där.</div><br/>

<div class="colorfont">2004-10-19 Oskar</div>
<div>Debriefingar för M77 och M78 är upplagda. Plutonen har nu även för första gången fått 4st sergeanter.</div><br/>

<div class="colorfont">2004-10-05 Oskar</div>
<div>Personallistan uppdaterad efter M76, det finns även en debriefing för M76 upplagd och debriefing för M75 som spelades dagen innan är på väg.</div><br/>

<div class="colorfont">2004-09-29 Oskar</div>
<div>Debriefing för M74 är uppe och vårt nya regelhäfte är snart färdigt.</div><br/>

<div class="colorfont">2004-09-18 Oskar</div>
<div>Debriefing från M73 är uppe och karaktärslistan uppdaterad.</div><br/>

<div class="colorfont">2004-09-11 Oskar</div>
<div>vår grannpluton i Örebro Terry's Tacklin' Tens har nu fått en egen hemsida på <a href="http://www.angelfire.com/art2/pipkin/">http://www.angelfire.com/art2/pipkin/</a>.</div><br/>

<div class="colorfont">2004-09-10 Oskar</div>
<div>Totte har ändrat lite på rollformuläret och fixat så att man kan fylla i värden i pdf-filen innan man skriver ut den. Det uppdaterade formuläret finns nu att ladda hem.</div><br/>

<div class="colorfont">2004-09-08 Oskar</div>
<div>Debriefing för M70 och M71 är utlagda. M72 spelas idag och M73 som är ett specialmission med flera speltillfällen kommer spelas första gången imorgon, så det är full fart på USCM-kampanjen just nu.</div><br/>

<div class="colorfont">2004-09-07 Oskar & Pontus</div>
<div>USCM-forumet har nu flyttat till en ny adress <a href="http://dallandra.ath.cx/uscm/forum/">http://dallandra.ath.cx/uscm/forum/</a><br>Ni hittar det även genom länken i menyn ovanför.</div><br/>

<div class="colorfont">2004-09-06 Oskar</div>
<div>Version 3.12 av Karaktärsgeneratorn finns nu att ladda hem. Det finns även en ny utrustningslista i Excelformat samt en ensidesversion som Pontus har gjort för att det ska vara smidigt att skriva ut den.</div><br/>

<div class="colorfont">2004-08-24 Oskar</div>
<div>Nu finns debriefingar för M67 och M68 under missions.</div>

<div class="colorfont">2004-08-23 Oskar</div>
<div>USCM kommer åka på konvent igen. Den här gången blir det på  <a href="http://narcon.sverok.net" target="_blank">NärCon</a> i Örebro som börjar den 27/8. Kom dit och spela med oss, vi kommer ha drop-in rollspel på NärCon också även om vi inte blir lika många spelledare som på LinCon.</div><br/>

<div class="colorfont">2004-08-19 Oskar</div>
<div>Personallistan är uppdaterad efter M66 och borde stämma nu. Debriefing för M64 upplagd.</div><br/>

<div class="colorfont">2004-08-17 Oskar</div>
<div>Debriefing för M65 uppe och M64 på väg. M66 och M67 kommer spelas under veckan så det är mycket USCM just nu. Jag ska försöka hinna med att hålla webbsidan uppdaterad.</div><br/>

<div class="colorfont">2004-08-16 Oskar</div>
<div>Debriefing för M63 upplagd. Det är många nya missions på gång den närmaste tiden så håll koll på mail och forumet.</div><br/>

<div class="colorfont">2004-08-06 Oskar</div>
<div>Nu är debriefingen från M62 upplagd.</div><br/>

<div class="colorfont">2004-08-05 Oskar</div>
<div>Debriefing från M61 är uppe, personalsidorna har även uppdaterats efter M62 som spelades idag. Debriefing för M62 kommer senare.</div><br/>

<div class="colorfont">2004-07-03 Oskar</div>
<div>All info uppdaterad efter M60.</div><br/>

<div class="colorfont">2004-06-16 Oskar</div>
<div>Debriefing och uppdateringar efter M59 är klara.</div><br/>

<div class="colorfont">2004-06-11 Oskar</div>
<div>Karaktärslistorna har uppdaterats med dom nya värdena för Glory-points.</div><br/>

<div class="colorfont">2004-06-10 Oskar</div>
<div>Karaktärerna uppdaterade efter M58 och alla nya spelare som har gjort karaktärer efter LinCon har kommit med på listan också. Nästa uppdrag, M59 har skickats ut på maillistan.</div><br/>

<div class="colorfont">2004-06-07 Oskar</div>
<div>Det fanns en bugg i Karaktärsgeneratorn så ladda hem den senaste versionen istället där buggen är fixad.</div><br/>

<div class="colorfont">2004-05-23 Oskar</div>
<div>Tack till alla som ville spela med oss på LinCon. Vi hoppas att ni hade kul och ni för gärna komma in på vårt forum och säga vad ni tyckte om vårt arrangemang.</div><br/>

<div class="colorfont">2004-05-19 Oskar</div>
<div>Idag börjar LinCon 2004 på universitetet i Linköping. USCM kommer självklart vara med som arrangemang och jag hoppas det är många som tar chansen att prova på att spela med oss.</div><br/>

<div class="colorfont">2004-05-15 Oskar</div>
<div>Ytterligare en uppdatering av Karaktärsgeneratorn till version 3.10. Det här blir den sista uppdateringen för ett tag framöver förutsatt att inga buggar upptäcks i den.
Karaktärsgeneratorn kan laddas hem under USCM RPG.</div><br/>

<div class="colorfont">2004-05-09 Oskar</div>
<div>Nu finns Bävers lista över dom mest medaljprydda soldaterna i plutonen med på Hall of Fame.</div><br/>

<div class="colorfont">2004-04-30 Oskar & Totte</div>
Ny finns en uppdaterad version av utrustningslistan att ladda hem i både Excel och PDF-format.</div><br/>

<div class="colorfont">2004-04-27 Oskar</div>
<div>Nu är uppdateringarna efter M56 och M57 klara.</div><br/>

<div class="colorfont">2004-04-14 Oskar</div>
<div>Det hade smugit sig in ett litet fel i javascriptet som visar debriefingtexterna till uppdragen, men nu fungerar allt som det ska igen.</div><br/>

<div class="colorfont">2004-04-13 Oskar</div>
<div>Karaktärsgeneratorn har uppdaterats igen. Version 3.09 finns nu att ladda hem.</div><br/>

<div class="colorfont">2004-03-30 Oskar</div>
<div>Uppdaterat och klart efter M55.</div><br/>

<div class="colorfont">2004-03-26 Oskar</div>
<div>Karaktärsgeneratorn har fått en ny uppdatering. Ladda hem version 3.08 under USCM RPG.</div><br/>

<div class="colorfont">2004-03-23 Oskar</div>
<div>Debriefing från M52 är uppe. Personalsidan uppdaterad efter M53, debriefing från det är på gång.</div><br/>

<div class="colorfont">2004-03-19 Oskar</div>
<div>En felaktig länk är fixad så nu gör det att komma åt utrustningslistan igen, tack Björn som uppmärksammade den.</div><br/>

<div class="colorfont">2004-03-15 Oskar</div>
<div>Debriefing till M44 saknades tidigare men Andreas har skrivit en nu som kan läsas under missions.</div><br/>

<div class="colorfont">2004-03-07 Oskar</div>
<div>Pelle och Mattias har skrivit mer utförliga debriefings till uppdragen 45 och 47, dom finns på missions-sidan.</div><br/>

<div class="colorfont">2004-03-05 Oskar</div>
<div>Efter vissa problem med den nya hemsidan är den gamla tillbaka igen. Den är dessutom uppdaterad t.o.m. senaste uppdraget, M51.</div><br/>

<div class="colorfont">2004-02-01 Totte</div>
<div>Version 2.00 av webbsidan är nu uppe på nätet, hoppas ni gillar den :).</div><br/>

<div class="colorfont">2004-01-14 Oskar</div>
<div>Karaktärsgeneratorn har fått ytterligare några uppdateringar. Version 3.07 finns nu att ladda hem under USCM RPG. En ny utrustningslista är också på gång inom den närmaste framtiden.</div><br/>

<div class="colorfont">2003-12-12 Oskar</div>
<div>Sidan uppdaterad med nya debriefingar och nytillkommna NPC:s.</div><br/>

<div class="colorfont">2003-12-05 Oskar</div>
<div>Personalsidan är uppdaterad och har nu även fått förklaringar till förkortningarna som används för grader och medaljer.</div><br/>

