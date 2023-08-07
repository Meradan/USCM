<h1 class="heading heading-h1">News archive</h1>
<?php
$newsController = new NewsController();
$listOfNews = $newsController->getNewsOlderThanAYear();
foreach ($listOfNews as $news) {
?>
<div> <font class="colorfont"><?php echo $news->getDate();?></font> <?php echo $news->getWrittenBy();?></div>
<div><?php echo $news->getText();?></div><br/>

<?php } ?>

<div class="title">Really, really old news</div><br/>

<div> <font class="colorfont">2004-11-28 � Oskar</font></div>
<div>Debriefing f�r M83 upplagd.</div><br/>

<div><font class="colorfont">2004-11-27 � Oskar</font></div>
<div>Uppdaterat med lite missions och debriefingar. M83 kommer spelas ikv�ll och det finns f�r ovanlighetens skull fortfarande plats �ver om n�gon mer vill vara med.</div><br/>

<div><font class="colorfont">2004-11-22 � Oskar</font></div>
<div>Debriefing f�r M80 �r upplagd.</div><br/>

<div><font class="colorfont">2004-11-19 � Oskar</font></div>
<div>Bes�k g�rna nyhetssidan <A HREF="http://www.galactic-news.tk/">Galactic News Network</A> som inneh�ller nyhetsreportage om h�ndelser inom USCM-spelv�rlden.<br><br/>

<div>Vi har �ven f�tt tre nya medaljer i USCM-kampanjen, se l�ngst ner p� personalsidan f�r mer detaljer.</div><br/>

<div><font class="colorfont">2004-11-04 � Oskar</font></div>
<div>Nu ger sig USCM iv�g till Bor�s f�r att spelleda p� <A HREF="http://bsk.sverok.net/">Bor�s Spelkonvent</A>. Vi kommer att finnas som drop-in arrangemang under hela konventet.</div><br/>

<div><font class="colorfont">2004-10-31 � Oskar</font></div>
<div>V�rt nya regelh�fte �r f�rdigskrivet, det blir traditionsenligt cocktailparty med upptr�dande av UA Marine Corps Band i Tottes aula!.</div><br/>

<div><font class="colorfont">2004-10-28 � Oskar</font></div>
<div>Nu har <A HREF="cc2.pdf" TARGET="_blank">Nr #2</A> av v�r kampanjtidning The Colonial Correspondent kommit ut. Vi h�lsar �ven en ny USCM-pluton v�lkommen till 4:e brigaden. Det �r Markhams Daring Dozens som nyligen har spelat sitt f�rsta uppdrag i Kalmar.</div><br/>

<div><font class="colorfont">2004-10-27 � Oskar</font></div>
<div>M79 spelades under dagen och debriefingen ligger uppe nu. Gl�m inte att anm�la er till <A HREF="http://bsk.sverok.net/">BSK</A> och passa p� att spela USCM n�r ni �r d�r.</div><br/>

<div><font class="colorfont">2004-10-19 � Oskar</font></div>
<div>Debriefingar f�r M77 och M78 �r upplagda. Plutonen har nu �ven f�r f�rsta g�ngen f�tt 4st sergeanter.</div><br/>

<div><font class="colorfont">2004-10-05 � Oskar</font></div>
<div>Personallistan uppdaterad efter M76, det finns �ven en debriefing f�r M76 upplagd och debriefing f�r M75 som spelades dagen innan �r p� v�g.</div><br/>

<div><font class="colorfont">2004-09-29 � Oskar</font></div>
<div>Debriefing f�r M74 �r uppe och v�rt nya regelh�fte �r snart f�rdigt.</div><br/>

<div><font class="colorfont">2004-09-18 � Oskar</font></div>
<div>Debriefing fr�n M73 �r uppe och karakt�rslistan uppdaterad.</div><br/>

<div><font class="colorfont">2004-09-11 � Oskar</font></div>
<div>V�r grannpluton i �rebro Terry's Tacklin' Tens har nu f�tt en egen hemsida p� <A HREF="http://www.angelfire.com/art2/pipkin/">http://www.angelfire.com/art2/pipkin/</A>.</div><br/>

<div><font class="colorfont">2004-09-10 � Oskar</font></div>
<div>Totte har �ndrat lite p� rollformul�ret och fixat s� att man kan fylla i v�rden i pdf-filen innan man skriver ut den. Det uppdaterade formul�ret finns nu att ladda hem.</div><br/>

<div><font class="colorfont">2004-09-08 � Oskar</font></div>
<div>Debriefing f�r M70 och M71 �r utlagda. M72 spelas idag och M73 som �r ett specialmission med flera speltillf�llen kommer spelas f�rsta g�ngen imorgon, s� det �r full fart p� USCM-kampanjen just nu.</div><br/>

<div><font class="colorfont">2004-09-07 � Oskar & Pontus</font></div>
<div>USCM-forumet har nu flyttat till en ny adress <A HREF="http://dallandra.ath.cx/uscm/forum/">http://dallandra.ath.cx/uscm/forum/</A><br>Ni hittar det �ven genom l�nken i menyn ovanf�r.</div><br/>

<div><font class="colorfont">2004-09-06 � Oskar</font></div>
<div>Version 3.12 av karakt�rsgeneratorn finns nu att ladda hem. Det finns �ven en ny utrustningslista i Excelformat samt en ensidesversion som Pontus har gjort f�r att det ska vara smidigt att skriva ut den.</div><br/>

<div><font class="colorfont">2004-08-24 � Oskar</font></div>
<div>Nu finns debriefingar f�r M67 och M68 under missions.</div>

<div><font class="colorfont">2004-08-23 � Oskar</font></div>
<div>USCM kommer �ka p� konvent igen. Den h�r g�ngen blir det p�  <a href="http://narcon.sverok.net" target="_blank">N�rCon</a> i �rebro som b�rjar den 27/8. Kom dit och spela med oss, vi kommer ha drop-in rollspel p� N�rCon ocks� �ven om vi inte blir lika m�nga spelledare som p� LinCon.</div><br/>

<div><font class="colorfont">2004-08-19 � Oskar</font></div>
<div>Personallistan �r uppdaterad efter M66 och borde st�mma nu. Debriefing f�r M64 upplagd.</div><br/>

<div><font class="colorfont">2004-08-17 � Oskar</font></div>
<div>Debriefing f�r M65 uppe och M64 p� v�g. M66 och M67 kommer spelas under veckan s� det �r mycket USCM just nu. Jag ska f�rs�ka hinna med att h�lla webbsidan uppdaterad.</div><br/>

<div><font class="colorfont">2004-08-16 � Oskar</font></div>
<div>Debriefing f�r M63 upplagd. Det �r m�nga nya missions p� g�ng den n�rmaste tiden s� h�ll koll p� mail och forumet.</div><br/>

<div><font class="colorfont">2004-08-06 � Oskar</font></div>
<div>Nu �r debriefingen fr�n M62 upplagd.</div><br/>

<div><font class="colorfont">2004-08-05 � Oskar</font></div>
<div>Debriefing fr�n M61 �r uppe, personalsidorna har �ven uppdaterats efter M62 som spelades idag. Debriefing f�r M62 kommer senare.</div><br/>

<div><font class="colorfont">2004-07-03 � Oskar</font></div>
<div>All info uppdaterad efter M60.</div><br/>

<div><font class="colorfont">2004-06-16 � Oskar</font></div>
<div>Debriefing och uppdateringar efter M59 �r klara.</div><br/>

<div><font class="colorfont">2004-06-11 � Oskar</font></div>
<div>Karakt�rslistorna har uppdaterats med dom nya v�rdena f�r Glory-points.</div><br/>

<div><font class="colorfont">2004-06-10 � Oskar</font></div>
<div>Karakt�rerna uppdaterade efter M58 och alla nya spelare som har gjort karakt�rer efter LinCon har kommit med p� listan ocks�. N�sta uppdrag, M59 har skickats ut p� maillistan.</div><br/>

<div><font class="colorfont">2004-06-07 � Oskar</font></div>
<div>Det fanns en bugg i karakt�rsgeneratorn s� ladda hem den senaste versionen ist�llet d�r buggen �r fixad.</div><br/>

<div><font class="colorfont">2004-05-23 � Oskar</font></div>
<div>Tack till alla som ville spela med oss p� LinCon. Vi hoppas att ni hade kul och ni f�r g�rna komma in p� v�rt forum och s�ga vad ni tyckte om v�rt arrangemang.</div><br/>

<div><font class="colorfont">2004-05-19 � Oskar</font></div>
<div>Idag b�rjar LinCon 2004 p� universitetet i Link�ping. USCM kommer sj�lvklart vara med som arrangemang och jag hoppas det �r m�nga som tar chansen att prova p� att spela med oss.</div><br/>

<div><font class="colorfont">2004-05-15 � Oskar</font></div>
<div>Ytterligare en uppdatering av karakt�rsgeneratorn till version 3.10. Det h�r blir den sista uppdateringen f�r ett tag fram�ver f�rutsatt att inga buggar uppt�cks i den.
karakt�rsgeneratorn kan laddas hem under USCM RPG.</div><br/>

<div><font class="colorfont">2004-05-09 � Oskar</font></div>
<div>Nu finns B�vers lista �ver dom mest medaljprydda soldaterna i plutonen med p� Hall of Fame.</div><br/>

<div><font class="colorfont">2004-04-30 � Oskar & Totte</font></div>
Ny finns en uppdaterad version av utrustningslistan att ladda hem i b�de Excel och PDF-format.</div><br/>

<div><font class="colorfont">2004-04-27 � Oskar</font></div>
<div>Nu �r uppdateringarna efter M56 och M57 klara.</div><br/>

<div><font class="colorfont">2004-04-14 � Oskar</font></div>
<div>Det hade smugit sig in ett litet fel i javascriptet som visar debriefingtexterna till uppdragen, men nu fungerar allt som det ska igen.</div><br/>

<div><font class="colorfont">2004-04-13 � Oskar</font></div>
<div>Karakt�rsgeneratorn har uppdaterats igen. Version 3.09 finns nu att ladda hem.</div><br/>

<div><font class="colorfont">2004-03-30 � Oskar</font></div>
<div>Uppdaterat och klart efter M55.</div><br/>

<div><font class="colorfont">2004-03-26 � Oskar</font></div>
<div>Karakt�rsgeneratorn har f�tt en ny uppdatering. Ladda hem version 3.08 under USCM RPG.</div><br/>

<div><font class="colorfont">2004-03-23 � Oskar</font></div>
<div>Debriefing fr�n M52 �r uppe. Personalsidan uppdaterad efter M53, debriefing fr�n det �r p� g�ng.</div><br/>

<div><font class="colorfont">2004-03-19 � Oskar</font></div>
<div>En felaktig l�nk �r fixad s� nu g�r det att komma �t utrustningslistan igen, tack Bj�rn som uppm�rksammade den.</div><br/>

<div><font class="colorfont">2004-03-15 � Oskar</font></div>
<div>Debriefing till M44 saknades tidigare men Andreas har skrivit en nu som kan l�sas under missions.</div><br/>

<div><font class="colorfont">2004-03-07 � Oskar</font></div>
<div>Pelle och Mattias har skrivit mer utf�rliga debriefings till uppdragen 45 och 47, dom finns p� missions-sidan.</div><br/>

<div><font class="colorfont">2004-03-05 � Oskar</font></div>
<div>Efter vissa problem med den nya hemsidan �r den gamla tillbaka igen. Den �r dessutom uppdaterad t.o.m. senaste uppdraget, M51.</div><br/>

<div><font class="colorfont">2004-02-01 � Totte</font></div>
<div>Version 2.00 av webbsidan �r nu uppe p� n�tet, hoppas ni gillar den :).</div><br/>

<div><font class="colorfont">2004-01-14 � Oskar</font></div>
<div>Karakt�rsgeneratorn har f�tt ytterligare n�gra uppdateringar. Version 3.07 finns nu att ladda hem under USCM RPG. En ny utrustningslista �r ocks� p� g�ng inom den n�rmaste framtiden.</div><br/>

<div><font class="colorfont">2003-12-12 � Oskar</font></div>
<div>Sidan uppdaterad med nya debriefingar och nytillkommna NPC:s.</div><br/>

<div><font class="colorfont">2003-12-05 � Oskar</font></div>
<div>Personalsidan �r uppdaterad och har nu �ven f�tt f�rklaringar till f�rkortningarna som anv�nds f�r grader och medaljer.</div><br/>

