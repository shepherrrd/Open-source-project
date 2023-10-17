<?php
/*
 * 	Manhali - Free Learning Management System
 *	chaps.php
 *	2011-01-27 12:36
 * 	Author: El Haddioui Ismail <ismail.elhaddioui@gmail.com>
 * 	Copyright (C) 2009-2014  El Haddioui Ismail. All rights reserved
 * 	License : GNU/GPL v3

This file is part of Manhali

Manhali is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Manhali is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Manhali.  If not, see <http://www.gnu.org/licenses/>.

*/

defined("access_const") or die( 'Restricted access' );

$select_statut_comp = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'courses';");
if (mysql_num_rows($select_statut_comp) == 1) {
 	$statut_comp = mysql_result($select_statut_comp,0);
	if ($statut_comp == 1) {

		$chap = intval($_GET['chapter']);

if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
	$selectchap = mysql_query("select * from `" . $tblprefix . "chapitres` where id_chapitre = $chap;");
else
	$selectchap = mysql_query("select * from `" . $tblprefix . "chapitres` where id_chapitre = $chap and publie_chapitre = '1';");

if (mysql_num_rows($selectchap) == 1) {
	
 $chapitre = mysql_fetch_row($selectchap);

 $id_chapitre = $chapitre[0];
 $id_partie = $chapitre[1];
 $ordre_chapitre = $chapitre[6];
 
 // tutoriel infos
 
 if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
 		$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties` where id_partie = $id_partie;");
 else
 		$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties` where id_partie = $id_partie and publie_partie = '1';");

 if (mysql_num_rows($select_tuto_id) == 1) {
	$id_tutoriel = mysql_result($select_tuto_id,0,0);
	
	if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
		$select_tuto = mysql_query("select * from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tutoriel;");
	else
		$select_tuto = mysql_query("select * from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tutoriel and publie_tutoriel = '2';");

	if (mysql_num_rows($select_tuto) == 1) {
	 $tutoriel = mysql_fetch_row($select_tuto);
	
	 $id_user = $tutoriel[1];
	 $titre_tutoriel = $tutoriel[2];
	 $licence_tutoriel = $tutoriel[6];
		
	// acces tutoriel
	$acces = $tutoriel[13];
	$acces_valide = 0;
	if ($acces == "*")
		$acces_valide = 1;
	else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
		$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
		$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    if (mysql_num_rows($select_classe) == 1){
			$id_classe = mysql_result($select_classe,0);
			$tab_classes = explode("-",trim($acces,"-"));
			if (in_array($id_classe,$tab_classes))
				$acces_valide = 1;
		}
	}

	// acces chapitre
	$acces_chapitre = $chapitre[11];
	$acces_chap_valide = 0;
	if ($acces_chapitre == "*")
		$acces_chap_valide = 1;
	else if ($acces_chapitre == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
		$acces_chap_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_chap_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
		$tab_acces_chap = explode("-",trim($acces_chapitre,"-"));
		if (isset($grade_app_session) && in_array($grade_app_session,$tab_acces_chap))
			$acces_chap_valide = 1;
	}

	if ($acces_valide == 1 && $acces_chap_valide == 1){

// chapitre infos
		$titre_chapitre = html_ent($chapitre[2]);
		$objectifs_chapitre = trim($chapitre[3]);

		$nombre_lectures = $chapitre[4];
		$date_creation_chapitre = $chapitre[7];
		$date_modification_chapitre = $chapitre[8];

// select auteur name and email
	$selectauteur = mysql_query("select identifiant_user, email_user, photo_profil from `" . $tblprefix . "users` where id_user = $id_user;");
	if (mysql_num_rows($selectauteur) == 1) {
		
		$auteur = mysql_result($selectauteur,0,0);
		if (!empty($auteur))
			$auteur = html_ent($auteur);
		else $auteur = inconnu;

		$mail_auteur = mysql_result($selectauteur,0,1);
		if (!empty($mail_auteur)){
			$mail_auteur = html_ent($mail_auteur);
			$mail_auteur = mail_antispam($mail_auteur,0);
		} else $mail_auteur = "";

		$photo_profil = mysql_result($selectauteur,0,2);
	} else {
		$auteur = inconnu;
		$mail_auteur = "";
		$photo_profil = "";
	}

// incrementer le nombre de lectures
	if ((!isset($id_user_session) && !isset($_SESSION['log'])) || (isset($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2))
		$update_nbr_lectures = mysql_query("update `" . $tblprefix . "chapitres` set nombre_lectures = nombre_lectures + 1 where id_chapitre = $id_chapitre;");

// ordre partie
		$select_partie_ordre = mysql_query("select ordre_partie from `" . $tblprefix . "parties` where id_partie = $id_partie;");
		$ordre_partie = mysql_result($select_partie_ordre,0);
// chapitre precedent
		if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$selectprecedent = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_partie and ordre_chapitre < $ordre_chapitre order by ordre_chapitre desc;");
		else
			$selectprecedent = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_partie and ordre_chapitre < $ordre_chapitre and publie_chapitre = '1' order by ordre_chapitre desc;");
		
		if (mysql_num_rows($selectprecedent) > 0)
			$chap_precedent = mysql_result($selectprecedent,0);
		else {
			if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
				$select_partie_precedent = mysql_query("select id_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and ordre_partie < $ordre_partie order by ordre_partie desc;");
			else
				$select_partie_precedent = mysql_query("select id_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and ordre_partie < $ordre_partie and publie_partie = '1' order by ordre_partie desc;");

			if (mysql_num_rows($select_partie_precedent) > 0) {
				$partie_precedent = mysql_result($select_partie_precedent,0);
				
				if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
					$selectprecedent2 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $partie_precedent order by ordre_chapitre desc;");
				else
					$selectprecedent2 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $partie_precedent and publie_chapitre = '1' order by ordre_chapitre desc;");

				if (mysql_num_rows($selectprecedent2) > 0)
					$chap_precedent = mysql_result($selectprecedent2,0);
				else $chap_precedent = 0;
			}
			else $chap_precedent = 0;
		}

// chapitre suivant
		if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$selectsuivant = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_partie and ordre_chapitre > $ordre_chapitre order by ordre_chapitre;");
		else
			$selectsuivant = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_partie and ordre_chapitre > $ordre_chapitre and publie_chapitre = '1' order by ordre_chapitre;");

		if (mysql_num_rows($selectsuivant) > 0)
			$chap_suivant = mysql_result($selectsuivant,0);
		else {
			if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
				$select_partie_suivant = mysql_query("select id_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and ordre_partie > $ordre_partie order by ordre_partie;");
			else
				$select_partie_suivant = mysql_query("select id_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and ordre_partie > $ordre_partie and publie_partie = '1' order by ordre_partie;");

			if (mysql_num_rows($select_partie_suivant) > 0) {
				$partie_suivant = mysql_result($select_partie_suivant,0);
				
				if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
					$selectsuivant2 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $partie_suivant order by ordre_chapitre;");
				else
					$selectsuivant2 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $partie_suivant and publie_chapitre = '1' order by ordre_chapitre;");

				if (mysql_num_rows($selectsuivant2) > 0)
					$chap_suivant = mysql_result($selectsuivant2,0);
				else $chap_suivant = 0;
			}
			else $chap_suivant = 0;
		}

// reponses qcm
	$select_reponses_qcm = mysql_query("select sum(total_essais), sum(total_reponses_correctes) from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre;");
	$total_essais = mysql_result($select_reponses_qcm,0,0);
	$total_reponses_correctes = mysql_result($select_reponses_qcm,0,1);

// ********* rating
	$nombre_votes_chapitre = $chapitre[9];
	$rating_chapitre = $chapitre[10];
	if ($nombre_votes_chapitre > 0 && $rating_chapitre > 0 && ctype_digit($nombre_votes_chapitre) && ctype_digit($rating_chapitre))
		$rating_chap = round($rating_chapitre / $nombre_votes_chapitre);
	else $rating_chap = 0;

	if ($rating_chap < 0) $rating_chap = 0;
	else if ($rating_chap > 6) $rating_chap = 6;
	
	$can_rate = 0;
if (isset($_SESSION['log']) && !empty($_SESSION['log'])){
	if ($_SESSION['log'] == 1){
		$select_chaps_vote = mysql_query("select chaps_vote from `" . $tblprefix . "users` where id_user = $id_user_session;");
		if (mysql_num_rows($select_chaps_vote) == 1){
			$chaps_vote = mysql_result($select_chaps_vote,0);
			$tab_chaps_vote = explode("-",$chaps_vote);
			if (!in_array($id_chapitre,$tab_chaps_vote) && $id_user != $id_user_session){
				$can_rate = 1;
				if (!empty($_GET['rating']) && ctype_digit($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 7){
					$new_nombre_votes_chapitre = $nombre_votes_chapitre+1;
					$new_rating_chapitre = $rating_chapitre + $_GET['rating'];
					$update_rating = mysql_query("update `" . $tblprefix . "chapitres` set nombre_votes_chapitre = $new_nombre_votes_chapitre, rating_chapitre = $new_rating_chapitre where id_chapitre = $id_chapitre;");
					$chaps_vote .= $id_chapitre."-";
					$update_chaps_vote = mysql_query("update `" . $tblprefix . "users` set chaps_vote = '$chaps_vote' where id_user = $id_user_session;");
					echo "<script type=\"text/javascript\">window.location.href = \"?chapter=".$id_chapitre."\";</script>";
				}
			}
		}
	}
	else if ($_SESSION['log'] == 2){
		$select_chaps_vote = mysql_query("select chaps_vote from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
		if (mysql_num_rows($select_chaps_vote) == 1){
			$chaps_vote = mysql_result($select_chaps_vote,0);
			$tab_chaps_vote = explode("-",$chaps_vote);
			if (!in_array($id_chapitre,$tab_chaps_vote)){
				$can_rate = 1;
				if (!empty($_GET['rating']) && ctype_digit($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 7){
					$new_nombre_votes_chapitre = $nombre_votes_chapitre+1;
					$new_rating_chapitre = $rating_chapitre + $_GET['rating'];
					$update_rating = mysql_query("update `" . $tblprefix . "chapitres` set nombre_votes_chapitre = $new_nombre_votes_chapitre, rating_chapitre = $new_rating_chapitre where id_chapitre = $id_chapitre;");
					$chaps_vote .= $id_chapitre."-";
					$update_chaps_vote = mysql_query("update `" . $tblprefix . "apprenants` set chaps_vote = '$chaps_vote' where id_apprenant = $id_user_session;");
					echo "<script type=\"text/javascript\">window.location.href = \"?chapter=".$id_chapitre."\";</script>";
				}
			}
		}
	}
}

//affichage suivant precedent

		echo "<table width=\"50%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";

		if ($chap_precedent > 0)
			echo "<table border=\"0\"><tr><td><a href=\"?chapter=".$chap_precedent."\"><img border=\"0\" src=\"images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?chapter=".$chap_precedent."\"><b>".chap_precedent."</b></a></td></tr></table>";
		else echo "<table border=\"0\"><tr><td><img border=\"0\" src=\"images/others/precedent2.png\" width=\"32\" height=\"32\" /></td><td><b>".chap_precedent."</b></td></tr></table>";

		echo "</b></td><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";
		
		if ($chap_suivant > 0)
			echo "<table border=\"0\"><tr><td><a href=\"?chapter=".$chap_suivant."\"><b>".chap_suivant."</b></a></td><td><a href=\"?chapter=".$chap_suivant."\"><img border=\"0\" src=\"images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td></tr></table>";
		else echo "<table border=\"0\"><tr><td><b>".chap_suivant."</b></td><td><img border=\"0\" src=\"images/others/suivant2.png\" width=\"32\" height=\"32\" /></td></tr></table>";
		
		echo "</b></td></tr></table>";

// affichage chapitre

		echo "<table width=\"100%\" align=\"center\" cellpadding=\"5\" cellspacing=\"5\">";
		echo "<tr><td align=\"center\" width=\"100%\"><div id=\"titre\">".$titre_chapitre."</div></td></tr>\n";	
		echo "<tr><td width=\"100%\">";
		
// affichage infos

echo "\n<table border=\"0\" align=\"center\" width=\"100%\" class=\"infos\" cellpadding=\"0\" cellspacing=\"0\"><ul>";
echo "\n<tr>";
echo "\n<td colspan=\"3\" align=\"center\"><div id=\"tuto_infos\">".chap_infos."</div></td>";
echo "\n</tr>";
echo "\n<tr><td colspan=\"3\" align=\"center\"><b>".tutoriel." :</b> <a class=\"bloc\" href=\"?tutorial=".$id_tutoriel."\">".html_ent($titre_tutoriel)."</a></td></tr>";
echo "\n<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "\n<tr>";
echo "\n<td rowspan=\"4\" width=\"20%\" align=\"center\">";
	if (!empty($photo_profil))
		echo "<img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$auteur."\" width=\"100\" height=\"100\" />";
echo "</td>";
echo "\n<td width=\"30%\" align=\"left\">";
	if ($afficher_profil == 1)
		echo "<li><b>".auteur." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a></b></li>\n";
	else
		echo "<li><b>".auteur." ".$auteur."</b></li>\n";
echo "</td>";
echo "\n<td width=\"50%\" align=\"left\"><li><b>".cree." ".set_date($dateformat,$date_creation_chapitre)."</b></li></td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".email." : ".$mail_auteur."</b></li></td>";
echo "\n<td width=\"50%\" align=\"left\"><li><b>".modifie." ".set_date($dateformat,$date_modification_chapitre)."</b></li></td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".lu." ".$nombre_lectures." ".fois."</b></li></td>";
echo "\n<td width=\"50%\" align=\"left\">";
	if ($total_essais != 0 && $total_reponses_correctes <= $total_essais){
		$reponses_tuto = round(100 * $total_reponses_correctes / $total_essais,2);
		echo "<li><b>".reponses_qcm." ".$reponses_tuto." %</b></li>\n";
	}
echo "</td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".licence." </b><a target=\"_blank\" title=\"".licence_const($licence_tutoriel)."\" href=\"http://creativecommons.org/licenses/".$licence_tutoriel."/3.0/\"><img src=\"images/licenses/".$licence_tutoriel.".png\" border=\"0\" alt=\"".licence_const($licence_tutoriel)."\" /></a></li></td>";
echo "\n<td width=\"50%\" align=\"left\">";
	// ********* rating affichage
	if ($can_rate == 1){
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"styles/rating.js\"></script>";
		echo "<li><div onmouseout=\"old_rating(this, event, ".$rating_chap.");\" onmouseover=\"init_stars();\"><b>".rating." : </b>";
	}
	else
		echo "<li><div><b>".rating." : </b>";
	for($i=1;$i<=6;$i++){
		if ($i <= $rating_chap)
			$img_rating = "star_over.gif";
		else
			$img_rating = "star_out.gif";
		echo "<img border=\"0\" id=\"Star".$i."\" src=\"images/others/".$img_rating."\" alt=\"".$i."/6\" title=\"".$i."/6\" width=\"25\" height=\"23\" />";
	}
	echo " <b>(".$nombre_votes_chapitre." ".votes." : ".$rating_chap."/6)</b></div></li>\n";
	if ($can_rate == 1)
		echo "<script type=\"text/javascript\">NotationSystem('?chapter=".$id_chapitre."');</script>";
	//*********
echo "</td>";
echo "\n</tr>";
echo "\n<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "\n</ul></table>";

	echo "</td></tr><tr><td width=\"100%\">";

//objectifs
		if (!empty($objectifs_chapitre)) {
			echo "<table width=\"100%\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><div id=\"objectifs\">".objectifs_chapitre."</div>";
			echo "<div id=\"normal\">".$objectifs_chapitre."</div></td></tr></table>\n";
		}

// blocs	
		if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$selectblocs = mysql_query("select * from `" . $tblprefix . "blocs` where id_chapitre = $id_chapitre order by ordre_bloc;");
		else
			$selectblocs = mysql_query("select * from `" . $tblprefix . "blocs` where id_chapitre = $id_chapitre and publie_bloc = '1' order by ordre_bloc;");

		if (mysql_num_rows($selectblocs)> 0) {
			echo "<ul>";
			while($bloc = mysql_fetch_row($selectblocs)) {
				$id_bloc = $bloc[0];

				$titre_bloc = html_ent($bloc[2]);
				$contenu_bloc = trim($bloc[3]);

				echo "<li class=\"bloc\"><a class=\"bloc\" name=\"".$id_bloc."\" href=\"?chapter=".$id_chapitre."#".$id_bloc."\">".$titre_bloc."</a></li>\n";
				echo "<div id=\"normal\">".$contenu_bloc."</div>\n";
			}
			echo "</ul>";
		}
		echo "</td></tr>";

 // ******* Devoir

 if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
		$selectdevoir = mysql_query("select * from `" . $tblprefix . "devoirs` where id_chapitre = $id_chapitre order by ordre_devoir;");
 else
		$selectdevoir = mysql_query("select * from `" . $tblprefix . "devoirs` where id_chapitre = $id_chapitre and publie_devoir = '1' and date_publie_devoir < ".time()." and date_expire_devoir > ".time()." order by ordre_devoir;");

 if (mysql_num_rows($selectdevoir)> 0) {

	echo "<tr><td width=\"100%\"><hr />\n";
	echo "<div id=\"titre\"><a name=\"devoir\">".devoir."</a></div>\n";
	$extensions = array("zip","rar","pdf","txt","doc","docx","xls","xlsx","ppt","pptx","pps","ppsx","rtf","odt","ods");
	$upload_max_filesize = @ini_get('upload_max_filesize');

 	if (isset($_SESSION['log']) && !empty($_SESSION['log'])){

		if(!empty($_FILES["uploaded_file"]) && !empty($_POST['random']) && !empty($_POST['devoir']) && ctype_digit($_POST['devoir'])){
			if (!isset($_SESSION['upload_key']) || $_SESSION['upload_key'] != $_POST['random']){
				$_SESSION['upload_key'] = $_POST['random'];
				
				if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
				$id_devoir_up = intval($_POST['devoir']);
				
				$select_devoir_up = mysql_query("select acces_devoir from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir_up and id_chapitre = $id_chapitre and publie_devoir = '1' and date_publie_devoir < ".time()." and date_expire_devoir > ".time().";");
				if (mysql_num_rows($select_devoir_up) == 1){
					$acces_devoir_up = mysql_result($select_devoir_up,0);
					
					$acces_devoir_valide = 0;
					
					if ($acces_devoir_up == "*")
						$acces_devoir_valide = 1;
					else {
						$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
				  	if (mysql_num_rows($select_classe) == 1){
							$id_classe = mysql_result($select_classe,0);
							$tab_classes = explode("-",$acces_devoir_up);
							if (in_array($id_classe,$tab_classes))
								$acces_devoir_valide = 1;
						}
					}
					if ($acces_devoir_valide == 1){
					
						$filename = escape_string($_FILES['uploaded_file']['name']);
						$file_size = $_FILES["uploaded_file"]["size"];
 						if ($_FILES['uploaded_file']['error'] == 0) {
  						$ext = substr($filename, strrpos($filename, '.') + 1);
  						$ext = strtolower($ext);
  						if (in_array($ext, $extensions) && $_FILES['uploaded_file']['type'] != "application/octet-stream"){
  							if (!empty($classe_apprenant))
  								$classe_apprenant_devoir = "_".substr($classe_apprenant,0,10);
  							else $classe_apprenant_devoir = "";
  							if (!empty($pseudo))
  								$pseudo_devoir = "_".$pseudo;
  							else $pseudo_devoir = "";
  							$new_file = $id_devoir_up.$classe_apprenant_devoir."_".$id_user_session.$pseudo_devoir.".".$ext;
  							$new_file = special_chars($new_file);
  							$new_file = escape_string($new_file);
  							$destination = "docs/".$new_file;
								if ((@move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$destination))) {
  								$select_this_devoir = mysql_query("select lien_file from `" . $tblprefix . "devoirs_rendus` where id_devoir = $id_devoir_up and id_apprenant = $id_user_session;");
									if (mysql_num_rows($select_this_devoir) > 0){
										$update_devoir_rendu = mysql_query("update `" . $tblprefix . "devoirs_rendus` set nom_file = '$filename', taille_file = $file_size, lien_file = '$new_file', date_file = ".time()." where id_devoir = $id_devoir_up and id_apprenant = $id_user_session;");
										$lien_file = mysql_result($select_this_devoir,0);
										@unlink("docs/".$lien_file);
									}
									else $insert_devoir_rendu = mysql_query("INSERT INTO `" . $tblprefix . "devoirs_rendus` VALUES (NULL,$id_devoir_up,$id_user_session,'$filename',$file_size,'$new_file',".time().");");

          				redirection(fichier_uploade,"?chapter=".$id_chapitre,3,"tips",0);
								} else goback(erreur_upload,2,"error",0);
  						} else goback(erreur_upload_type,2,"error",0);
 						}
 					 	else {
						 switch ($_FILES['uploaded_file']['error']){
  						case 1 : goback(erreur_upload_1." : ".$upload_max_filesize,2,"error",0);
								break;
  						case 2 : goback(erreur_upload_2,2,"error",0);
								break;
  						case 3 : goback(erreur_upload_3,2,"error",0);
								break;
  						case 4 : goback(erreur_upload_4,2,"error",0);
								break;
  						case 6 : goback(erreur_upload_6,2,"error",0);
								break;
  						case 7 : goback(erreur_upload_7,2,"error",0);
								break;
  						case 8 : goback(erreur_upload_8,2,"error",0);
								break;
  						default : goback(erreur_upload_default,2,"error",0);
						 }
 					 	}
					} else goback(no_access_permission,2,"error",0);
				} else goback(devoir_ferme,2,"error",0);
			 } else goback(action_pour_apprenants,2,"error",0);
			}	else goback(erreur_upload_key,2,"error",0);
		}
		else {
			$peut_consulter_1 = 0;
			echo "<ul>";
			while($devoir = mysql_fetch_row($selectdevoir)) {
				$id_devoir = $devoir[0];
				$acces_devoir = $devoir[2];
				$titre_devoir = html_ent($devoir[3]);
				$contenu_devoir = trim($devoir[4]);
				$date_expiration_devoir = set_date($dateformat,$devoir[6]);

				$acces_devoir_valide = 0;

				if ($acces_devoir == "*")
					$acces_devoir_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$acces_devoir_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
					$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
				  if (mysql_num_rows($select_classe) == 1){
						$id_classe = mysql_result($select_classe,0);
						$tab_classes = explode("-",$acces_devoir);
						if (in_array($id_classe,$tab_classes))
							$acces_devoir_valide = 1;
					}
				}
				if ($acces_devoir_valide == 1){
					echo "<li class=\"bloc\"><a name=\"devoir".$id_devoir."\">".$titre_devoir."</a></li>\n";
					echo "<div id=\"normal\">".$contenu_devoir."</div>\n";

					if ($devoir[6] > time()){
						if ($devoir[5] < time()){
							$expiration_devoir = round(($devoir[6] - time()) / 60 / 60 / 24);
							$expiration_chaine = "<font color='green'>".$expiration_devoir." ".jours."</font>";

							if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
  							$select_this_devoir = mysql_query("select nom_file, lien_file, date_file from `" . $tblprefix . "devoirs_rendus` where id_devoir = $id_devoir and id_apprenant = $id_user_session;");
								if (mysql_num_rows($select_this_devoir) > 0){
									$nom_file = html_ent(mysql_result($select_this_devoir,0,0));
									$lien_file = html_ent(mysql_result($select_this_devoir,0,1));
									$date_file = mysql_result($select_this_devoir,0,2);
									$date_file = set_date($dateformat,$date_file);
									if (file_exists("docs/".$lien_file))
										echo "<b><u>- ".reupload_devoir." : </u><br /><br /><font color=\"red\">".action_ecrase_devoir."</font> : <a href=\"includes/download.php?f=".$lien_file."\" title=\"".download."\">".$nom_file."</a>, ".date_ajout." : ".$date_file."</b><br /><br />";
									else echo "<b><u>- ".upload_devoir." : </u></b>";
								} else echo "<b><u>- ".upload_devoir." : </u></b>";
							}
							$peut_consulter_1 = 1;
							echo "<form method=\"POST\" id=\"form_".$id_devoir."\" enctype=\"multipart/form-data\" action=\"?chapter=".$id_chapitre."#devoir\">";
							echo "<input name=\"uploaded_file\" type=\"file\" />";
							echo "<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" />";
							echo " <input type=\"hidden\" name=\"devoir\" value=\"".$id_devoir."\"> <input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
						} else $expiration_chaine = "<font color='red'>".not_open_yet."</font>";
					} else $expiration_chaine = "<font color='red'>".expire."</font>";

					echo "<p><b>".date_expiration." : </b>".$date_expiration_devoir."<b> - " .jours_restants. " : ".$expiration_chaine."</b><br />";
					echo "</p><hr />";
				}
			}
			if ($peut_consulter_1 == 1){
				if (!empty($upload_max_filesize))
					echo "<li><b>".taille_max." ".$upload_max_filesize."</b></li>";

					echo "<li><b>".extentions_autorisees." : </b>";
					echo "<br />- ".type_file5;
					echo "<br />- ".type_file6;
					echo "</li>";

			} else echo "<font color=\"red\"><b>".no_access_permission."</b></font>";
			echo "</ul>";
		}
	} else echo "<font color=\"red\"><b>".no_access_permission."</b></font>";
	echo "</td></tr>";
 }

 // ******* Traitement QCM
		
	echo "<tr><td width=\"100%\"><hr />\n";
	
	if (isset($_POST['qcm'])){
		
		echo "<div id=\"titre\"><a name=\"qcm\">".resultats_qcm."</a></div><br />\n";
		if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$select_qcm = mysql_query("select * from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre order by ordre_qcm;");
		else
			$select_qcm = mysql_query("select * from `" . $tblprefix . "qcm` where publie_qcm = '1' and id_chapitre = $id_chapitre order by ordre_qcm;");
		
		// save_learner_qcm_stats yes or no
		$save_learner_qcm_stats = 0;
		if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2){
			$select_chaps_qcm = mysql_query("select chaps_qcm from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
			if (mysql_num_rows($select_chaps_qcm) == 1){
				$chaps_qcm = mysql_result($select_chaps_qcm,0);
				$tab_chaps_qcm = explode("-",$chaps_qcm);
				if (!in_array($id_chapitre,$tab_chaps_qcm))
					$save_learner_qcm_stats = 1;
			}
		}

		if (mysql_num_rows($select_qcm) > 0) {
			
			$total_questions = mysql_num_rows($select_qcm);
			$mes_bonnes_reponses = 0;
			
			echo "<table width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td width=\"50%\" valign=\"top\">";
			
			while($qcm = mysql_fetch_row($select_qcm)){
				$id_qcm = $qcm[0];
				$question_qcm = trim($qcm[2]);
				$reponse_correcte = $qcm[9];
				
				$total_essais = $qcm[10];
				$total_reponses_correctes = $qcm[11];
				
				// repondu
				if(isset($_POST[$id_qcm]) && ctype_digit($_POST[$id_qcm])) {

					$total_essais += 1;

					if ($reponse_correcte == $_POST[$id_qcm]) {
						echo "<img border=\"0\" src=\"images/others/valide.png\" width=\"32\" height=\"32\" />";
						$total_reponses_correctes += 1;
						$mes_bonnes_reponses += 1;
						if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2 && $save_learner_qcm_stats == 1)
							$update_app_qcm = mysql_query("update `" . $tblprefix . "apprenants` set total_essais = total_essais + 1, total_reponses_correctes = total_reponses_correctes + 1 where id_apprenant = $id_user_session;");
					}
					else {
						echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
						if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2 && $save_learner_qcm_stats == 1)
							$update_app_qcm = mysql_query("update `" . $tblprefix . "apprenants` set total_essais = total_essais + 1 where id_apprenant = $id_user_session;");
					}
					
					if ((!isset($id_user_session) && !isset($_SESSION['log'])) || (isset($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2 && $save_learner_qcm_stats == 1))
						$update_qcm = mysql_query("update `" . $tblprefix . "qcm` set total_essais = $total_essais, total_reponses_correctes = $total_reponses_correctes where id_qcm = $id_qcm;");

					echo " ".$question_qcm;

					echo "<ul>";
					for ($i=1;$i<7;$i++){
						if (!empty($qcm[$i+2])){
							echo "<li>";

							if ($reponse_correcte == $i)
								echo "<font color=\"green\">";

							if ($_POST[$id_qcm] == $i && $_POST[$id_qcm] != $reponse_correcte)
								echo "<font color=\"red\">";

							echo "<b>".html_ent($qcm[$i+2])."</b>";

							if ($reponse_correcte == $i)
								echo "</font>";
							
							if ($_POST[$id_qcm] == $i && $_POST[$id_qcm] != $reponse_correcte)
								echo "</font>";
							
							echo "</li><br />";
						}
					}
					echo "</ul>";
				}
				// non repondu
				else {
					
					echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
					echo " ".$question_qcm;

					echo "<ul>";
					for ($i=1;$i<7;$i++){
						if (!empty($qcm[$i+2])){
							echo "<li>";
							
							if ($reponse_correcte == $i)
								echo "<font color=\"green\">";
		
							echo "<b>".html_ent($qcm[$i+2])."</b>";
							
							if ($reponse_correcte == $i)
								echo "</font>";

							echo "</li><br />";
						}
					}
					echo "</ul>";
				}
			}
			
			echo "</td><td width=\"50%\" valign=\"top\" align=\"right\"><table width=\"100%\" border=\"0\" class=\"infos\" cellpadding=\"5\" cellspacing=\"5\">";
			echo "<tr><td><br /><div id=\"tuto_infos\">".pourcentage_reponses."</div><br /></td></tr>";
			
			if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
				$select_question_qcm = mysql_query("select question_qcm, total_essais, total_reponses_correctes from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre order by ordre_qcm;");
			else
				$select_question_qcm = mysql_query("select question_qcm, total_essais, total_reponses_correctes from `" . $tblprefix . "qcm` where publie_qcm = '1' and id_chapitre = $id_chapitre order by ordre_qcm;");

			if (mysql_num_rows($select_question_qcm) > 0) {
				while($myqcm = mysql_fetch_row($select_question_qcm)){
					$question_qcm = trim($myqcm[0]);
					$question_qcm = strip_tags($question_qcm);
					$question_qcm = readmore($question_qcm,50);
					
					$total_essais = $myqcm[1];
					$total_reponses_correctes = $myqcm[2];
					
					if ($total_essais != 0 && $total_reponses_correctes <= $total_essais)
						$statistiques = round(100*$total_reponses_correctes/$total_essais,2);
					else $statistiques = 0;
					
					echo "<tr><td align=\"left\"><img border=\"0\" src=\"images/others/question.png\" width=\"20\" height=\"20\" /> ".$question_qcm." : <b>".$statistiques." %</b></td></tr>\n";
				}
			}
			echo "</table></td></tr><tr><td colspan=\"2\" align=\"center\" width=\"100%\">";
			$note = round(20*$mes_bonnes_reponses/$total_questions,2);
			if ($note >= 10 && $save_learner_qcm_stats == 1 && isset($chaps_qcm)){
				if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2){
					$chaps_qcm .= $id_chapitre."-";
					$update_chaps_vote = mysql_query("update `" . $tblprefix . "apprenants` set chaps_qcm = '$chaps_qcm' where id_apprenant = $id_user_session;");
				}
			}
			echo "<h2>".votre_note." ".$note." / 20</h2>";
			echo "</td></tr></table>";
			
		} else accueil();
	}
	else {
		if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$selectqcm = mysql_query("select * from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre order by ordre_qcm;");
		else
			$selectqcm = mysql_query("select * from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre and publie_qcm = '1' order by ordre_qcm;");

		if (mysql_num_rows($selectqcm)> 0) {

			echo "<div id=\"titre\"><a name=\"qcm\">".qcm."</a></div><form method=\"POST\" action=\"?chapter=".$id_chapitre."#qcm\">";
			while($qcm = mysql_fetch_row($selectqcm)) {
				$id_qcm = $qcm[0];
				$question_qcm = trim($qcm[2]);
				echo $question_qcm."<br />";
				for ($i=1;$i<7;$i++){
					if (!empty($qcm[$i+2])){
						echo "\n<input name=\"".$id_qcm."\" type=\"radio\" value=\"".$i."\">";
						echo html_ent($qcm[$i+2])."<br />";
					}
				}
				echo "<hr />";
			}
			echo "<input type=\"hidden\" name=\"qcm\" value=\"ok\">";
			echo "<center><input type=\"submit\" class=\"button\" value=\"" .btncorriger. "\"></center></form>";
		}
	}
	echo "</td></tr></table>";
		
		// ********* commentaires **********
		$type_objet = "c";
		$id_objet = $id_chapitre;
		$path_objet = "chapter";
		include_once ("includes/comments.php");
		
//affichage suivant precedent

		echo "<table width=\"50%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";

		if ($chap_precedent > 0)
			echo "<table border=\"0\"><tr><td><a href=\"?chapter=".$chap_precedent."\"><img border=\"0\" src=\"images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?chapter=".$chap_precedent."\"><b>".chap_precedent."</b></a></td></tr></table>";
		else echo "<table border=\"0\"><tr><td><img border=\"0\" src=\"images/others/precedent2.png\" width=\"32\" height=\"32\" /></td><td><b>".chap_precedent."</b></td></tr></table>";

		echo "</b></td><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";
		
		if ($chap_suivant > 0)
			echo "<table border=\"0\"><tr><td><a href=\"?chapter=".$chap_suivant."\"><b>".chap_suivant."</b></a></td><td><a href=\"?chapter=".$chap_suivant."\"><img border=\"0\" src=\"images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td></tr></table>";
		else echo "<table border=\"0\"><tr><td><b>".chap_suivant."</b></td><td><img border=\"0\" src=\"images/others/suivant2.png\" width=\"32\" height=\"32\" /></td></tr></table>";
		
		echo "</b></td></tr></table><br />";
	 } else echo "\n<div id=\"titre\">".$titre_tutoriel."</a></div><br /><font color=\"red\"><b>".no_access_permission."</b></font>";
	} else accueil();
 } else accueil();
} else accueil();
} else accueil();
} else accueil();
?>