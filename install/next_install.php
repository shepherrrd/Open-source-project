<?php
/*
 * 	Manhali - Free Learning Management System
 *	next_install.php
 *	2009-01-01 22:54
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
	define("access_const","access_const");

	header('Content-type: text/html; charset=UTF-8');
	mb_internal_encoding("UTF-8");
	
	include_once ("../includes/dbconfig.php");

	$initial_path = $_SERVER['PHP_SELF'];
	$initial_path2 = substr($initial_path,0,strrpos($initial_path,'/install'))."/docs/";
	
	if (isset($_POST['lang']) && !empty($_POST['lang']))
		$language = $_POST['lang'];
	if (file_exists("../".$adminfolder."/admin_language.php")) {
		//include_once ("../".$adminfolder."/admin_language.php");
	include_once ("../includes/security_functions.php");
	include_once ("../includes/display_functions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali installation</title>
<link rel="stylesheet" href="../styles/style1.css" type="text/css" />
<link rel="shortcut icon" href="../styles/favicon.gif" type="image/x-icon" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

<!--[if lt IE 7]>
<script defer type="text/javascript" src="../styles/pngfix.js"></script>
<![endif]-->

</head>
<body>
<br />

<?php

//echo "<center><h2></h2></center>";

if (isset($_POST['pass']) && $_POST['pass'] == md5($installpass)){
	$pass = $_POST['pass'];
 if (isset($_POST['etape']) && !empty($_POST['etape'])) $etape = $_POST['etape']; else $etape = 0;


 switch ($etape) {

	case "1" : {
		if (!$db){
			$reqcreation = "CREATE DATABASE " . $dbname . " CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$create_db = $connect->query($reqcreation);
			if ($create_db){
				$db = mysqli_select_db($connect,$create_db);
				echo "<h4><img src=\"../images/icones/tips.png\" />bd_create_succes</h4>";
				echo "<h3></h3>";
				echo "<h4><img src=\"../images/icones/info.png\" /><font color=\"red\"></font></h4>";
				echo "<form name=\"form1\" method=\"POST\"><br /><input name=\"insert\" type=\"radio\" value=\"non\" checked>non&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"insert\" type=\"radio\" value=\"oui\">oui<br />";
				echo "<br /><br /><input type=\"hidden\" name=\"etape\" value=\"2\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"lang\" value=\"".$language."\"><input type=\"submit\" class=\"button\" value=\"install_step2\"></form>";
			}
			else {
				echo "<h4><img src=\"../images/icones/error.png\" /><font color=\"red\"> ".$dbname."</font></h4>";
				echo "<a href=\"#\" onClick=\"window.location.reload();\"></a>";
			}
		}
		else {
			echo "<h3>tables_create</h3>";
			echo "<h4><img src=\"../images/icones/info.png\" /><font color=\"red\">prefix_recommandation</font></h4>";
			echo "<form name=\"form1\" method=\"POST\">bd_default_data<br /><input name=\"insert\" type=\"radio\" value=\"non\" checked>non&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"insert\" type=\"radio\" value=\"oui\">oui<br />";
			echo "<br /><br /><input type=\"hidden\" name=\"etape\" value=\"2\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"lang\" value=\"".$language."\"><input type=\"submit\" class=\"button\" value=\"install_step2\"></form>";
		}
	} break;

	//*****************************************************************************
	case "2" : {
	
				echo "<h3></h3>";
				echo "<ul>";
				
				$error_create_table = 0;
				
				$deletetable1 = "DROP TABLE IF EXISTS `" . $tblprefix . "antiaspirateur`;";
				$createtable1 = "CREATE TABLE `" . $tblprefix . "antiaspirateur` (ip_aspi varchar(16) NOT NULL default '', compteur_aspi int(3) unsigned NOT NULL default '1', heure_aspi time NOT NULL default '00:00:00', KEY heure_aspi (heure_aspi), KEY ip_aspi (ip_aspi)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable1);
				if ($connect->query($createtable1))
					echo "<li> antiaspirateur</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> antiaspirateur</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable2 = "DROP TABLE IF EXISTS `" . $tblprefix . "articles`;";
				$createtable2 = "CREATE TABLE `" . $tblprefix . "articles` (id_article int(10) unsigned NOT NULL auto_increment, id_user int(10) unsigned NOT NULL default '0', id_menu int(10) unsigned NOT NULL default '0', titre_article varchar(100) NOT NULL default '', contenu_article text NOT NULL, publie_article enum('0','1') NOT NULL default '0', accueil_article enum('0','1') NOT NULL default '0', ordre_article int(10) unsigned NOT NULL default '0', ordre_accueil int(10) unsigned NOT NULL default '0', id_validateur int(10) unsigned NOT NULL default '0', date_creation_article int(10) unsigned NOT NULL default '0', date_modification_article int(10) unsigned NOT NULL default '0', acces_article varchar(200) NOT NULL default '*', id_menu_ver int(10) unsigned NOT NULL default '0', ordre_article_ver int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_article)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable2);
				if ($connect->query($createtable2))
					echo "<li> articles</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> articles</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable3 = "DROP TABLE IF EXISTS `" . $tblprefix . "blocs`;";
				$createtable3 = "CREATE TABLE `" . $tblprefix . "blocs` (id_bloc int(10) unsigned NOT NULL auto_increment, id_chapitre int(10) unsigned NOT NULL default '0', titre_bloc varchar(100) NOT NULL default '', contenu_bloc text NOT NULL, publie_bloc enum('1','0') NOT NULL default '1', ordre_bloc int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_bloc)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable3);
				if ($connect->query($createtable3))
					echo "<li> blocs</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> blocs</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable4 = "DROP TABLE IF EXISTS `" . $tblprefix . "chapitres`;";
				$createtable4 = "CREATE TABLE `" . $tblprefix . "chapitres` (id_chapitre int(10) unsigned NOT NULL auto_increment, id_partie int(10) unsigned NOT NULL default '0', titre_chapitre varchar(100) NOT NULL default '', objectifs_chapitre text NOT NULL, nombre_lectures int(10) unsigned NOT NULL default '0', publie_chapitre enum('1','0') NOT NULL default '1', ordre_chapitre int(10) unsigned NOT NULL default '0', date_creation_chapitre int(10) unsigned NOT NULL default '0', date_modification_chapitre int(10) unsigned NOT NULL default '0', nombre_votes_chapitre int(10) unsigned NOT NULL default '0', rating_chapitre int(10) unsigned NOT NULL default '0', grade_chapitre varchar(30) NOT NULL default '*', PRIMARY KEY  (id_chapitre)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable4);
				if ($connect->query($createtable4))
					echo "<li> chapitres</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> chapitres</li></font>";
					$error_create_table = 1;
				}
			  flush();
			  
				$deletetable5 = "DROP TABLE IF EXISTS `" . $tblprefix . "composants`;";
				$createtable5 = "CREATE TABLE `" . $tblprefix . "composants` (id_composant int(4) unsigned NOT NULL auto_increment, nom_composant varchar(50) NOT NULL default '', titre_composant varchar(100) NOT NULL default '', contenu_composant text NOT NULL, active_composant enum('1','0') NOT NULL default '1', ordre_composant int(3) unsigned NOT NULL default '0', PRIMARY KEY  (id_composant)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable5);
				if ($connect->query($createtable5))
					echo "<li> composants</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> composants</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable6 = "DROP TABLE IF EXISTS `" . $tblprefix . "hormenu`;";
				$createtable6 = "CREATE TABLE `" . $tblprefix . "hormenu` (id_hormenu int(10) unsigned NOT NULL auto_increment, titre_hormenu varchar(100) NOT NULL default '', type_hormenu enum('article','url','module') NOT NULL default 'article', lien_hormenu varchar(200) NOT NULL default '', active_hormenu enum('1','0') NOT NULL default '1', ordre_hormenu int(3) unsigned NOT NULL default '0', PRIMARY KEY  (id_hormenu)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable6);
				if ($connect->query($createtable6))
					echo "<li> hormenu</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> hormenu</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable7 = "DROP TABLE IF EXISTS `" . $tblprefix . "messages`;";
				$createtable7 = "CREATE TABLE `" . $tblprefix . "messages` (id_message int(10) unsigned NOT NULL auto_increment, id_emetteur int(10) unsigned NOT NULL default '0', id_emetteur_app int(10) unsigned NOT NULL default '0', nom_emetteur varchar(50) NOT NULL default '', email_emetteur varchar(50) NOT NULL default '', id_destinataires text NOT NULL, id_destinataires_app text NOT NULL, titre_message varchar(100) NOT NULL default '', contenu_message text NOT NULL, lu_message text NOT NULL, lu_message_app text NOT NULL, date_message int(10) unsigned NOT NULL default '0', boite_envoi text NOT NULL, boite_envoi_app text NOT NULL, deleted_from_outbox enum('1','0') NOT NULL default '0', PRIMARY KEY  (id_message)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable7);
				if ($connect->query($createtable7))
					echo "<li> messages</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> messages</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable8 = "DROP TABLE IF EXISTS `" . $tblprefix . "parties`;";
				$createtable8 = "CREATE TABLE `" . $tblprefix . "parties` (id_partie int(10) unsigned NOT NULL auto_increment, id_tutoriel int(10) unsigned NOT NULL default '0', titre_partie varchar(100) NOT NULL default '', objectifs_partie text NOT NULL, introduction_partie text NOT NULL, conclusion_partie text NOT NULL, publie_partie enum('1','0') NOT NULL default '1', ordre_partie int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_partie)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable8);
				if ($connect->query($createtable8))
					echo "<li> parties</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> parties</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable9 = "DROP TABLE IF EXISTS `" . $tblprefix . "qcm`;";
				$createtable9 = "CREATE TABLE `" . $tblprefix . "qcm` (id_qcm int(10) unsigned NOT NULL auto_increment, id_chapitre int(10) unsigned NOT NULL default '0', question_qcm text NOT NULL, reponse1_qcm varchar(200) NOT NULL default '', reponse2_qcm varchar(200) NOT NULL default '', reponse3_qcm varchar(200) NOT NULL default '', reponse4_qcm varchar(200) NOT NULL default '', reponse5_qcm varchar(200) NOT NULL default '', reponse6_qcm varchar(200) NOT NULL default '', reponse_correcte enum('1','2','3','4','5','6') NOT NULL default '1', total_essais int(10) unsigned NOT NULL default '0', total_reponses_correctes int(10) unsigned NOT NULL default '0', publie_qcm enum('1','0') NOT NULL default '1', ordre_qcm int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_qcm)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable9);
				if ($connect->query($createtable9))
					echo "<li> qcm</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> qcm</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable10 = "DROP TABLE IF EXISTS `" . $tblprefix . "site_infos`;";
				$createtable10 = "CREATE TABLE `" . $tblprefix . "site_infos` (id_site int(3) unsigned NOT NULL auto_increment, nom_site varchar(50) NOT NULL default '', titre_site varchar(100) NOT NULL default '', url_site varchar(100) NOT NULL default '', description_site text NOT NULL, keywords_site text NOT NULL, langue_site varchar(20) NOT NULL default '', footer_site text NOT NULL, accueil_multicolonnes enum('1','0') NOT NULL default '1', inscription enum('1','0') NOT NULL default '1', activation_apprenants enum('1','0') NOT NULL default '0', demander_classe enum('1','0') NOT NULL default '1', autoriser_modification_classe enum('1','0') NOT NULL default '0', afficher_profil_aux_visiteurs enum('1','0') NOT NULL default '0', nombre_elements_page int(10) unsigned NOT NULL default '10', nombre_caracteres int(10) unsigned NOT NULL default '500', PRIMARY KEY  (id_site)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable10);
				if ($connect->query($createtable10))
					echo "<li> site_infos</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> site_infos</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable11 = "DROP TABLE IF EXISTS `" . $tblprefix . "tutoriels`;";
				$createtable11 = "CREATE TABLE `" . $tblprefix . "tutoriels` (id_tutoriel int(10) unsigned NOT NULL auto_increment, id_user int(10) unsigned NOT NULL default '0', titre_tutoriel varchar(100) NOT NULL default '', objectifs_tutoriel text NOT NULL, introduction_tutoriel text NOT NULL, conclusion_tutoriel text NOT NULL, licence_tutoriel enum('by','by-sa','by-nd','by-nc','by-nc-sa','by-nc-nd') NOT NULL default 'by', notes_tutoriel text NOT NULL, publie_tutoriel enum('0','1','2') NOT NULL default '0', ordre_tutoriel int(10) unsigned NOT NULL default '0', date_creation_tutoriel int(10) unsigned NOT NULL default '0', date_modification_tutoriel int(10) unsigned NOT NULL default '0', id_validateur int(10) unsigned NOT NULL default '0', acces_tutoriel varchar(200) NOT NULL default '*', nombre_votes_tutoriel int(10) unsigned NOT NULL default '0', rating_tutoriel int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_tutoriel)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable11);
				if ($connect->query($createtable11))
					echo "<li> tutoriels</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b>tutoriels</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable12 = "DROP TABLE IF EXISTS `" . $tblprefix . "users`;";
				$createtable12 = "CREATE TABLE `" . $tblprefix . "users` (id_user int(10) unsigned NOT NULL auto_increment, nom_user varchar(50) NOT NULL default '', identifiant_user varchar(30) NOT NULL default '', mdp_user varchar(40) NOT NULL default '', email_user varchar(50) NOT NULL default '', active_user enum('1','0') NOT NULL default '1', grade_user enum('0','1','2','3') NOT NULL default '0', photo_profil varchar(30) NOT NULL default '', sexe_user enum('M','F') NOT NULL default 'M', date_inscription int(10) unsigned NOT NULL default '0', last_connect int(10) unsigned NOT NULL default '0', connected_now enum('0','1') NOT NULL default '0', langue_user varchar(20) NOT NULL default '', tutos_vote text NOT NULL, chaps_vote text NOT NULL, last_duration int(10) unsigned NOT NULL default '0', total_duration int(10) unsigned NOT NULL default '0', nbr_connexion int(10) unsigned NOT NULL default '0', nbr_pages int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_user)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable12);
				if ($connect->query($createtable12))
        	echo "<li> users</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> users</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable13 = "DROP TABLE IF EXISTS `" . $tblprefix . "apprenants`;";
				$createtable13 = "CREATE TABLE `" . $tblprefix . "apprenants` (id_apprenant int(10) unsigned NOT NULL auto_increment, id_classe int(4) unsigned NOT NULL default '0', nom_apprenant varchar(50) NOT NULL default '', identifiant_apprenant varchar(30) NOT NULL default '', mdp_apprenant varchar(40) NOT NULL default '', email_apprenant varchar(50) NOT NULL default '', naissance_apprenant varchar(10) NOT NULL default '', active_apprenant enum('1','0') NOT NULL default '1', photo_apprenant varchar(30) NOT NULL default '', sexe_apprenant enum('M','F') NOT NULL default 'M', date_inscription_apprenant int(10) unsigned NOT NULL default '0', last_connect_apprenant int(10) unsigned NOT NULL default '0', connected_now_apprenant enum('0','1') NOT NULL default '0', tutos_vote text NOT NULL, chaps_vote text NOT NULL, langue_apprenant varchar(20) NOT NULL default '', last_duration int(10) unsigned NOT NULL default '0', total_duration int(10) unsigned NOT NULL default '0', nbr_connexion int(10) unsigned NOT NULL default '0', total_essais int(10) unsigned NOT NULL default '0', total_reponses_correctes int(10) unsigned NOT NULL default '0', nbr_pages int(10) unsigned NOT NULL default '0', cree_par int(10) unsigned NOT NULL default '0', chaps_qcm text NOT NULL, grade_apprenant enum('A','B','C','D','E') NOT NULL default 'E', machine_apprenant text NOT NULL, style_apprenant varchar(50) NOT NULL default '-', PRIMARY KEY  (id_apprenant)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable13);
				if ($connect->query($createtable13))
        	echo "<li> apprenants</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> apprenants</li></font>";
        	$error_create_table = 1;
        }
        flush();
        
				$deletetable14 = "DROP TABLE IF EXISTS `" . $tblprefix . "files`;";
				$createtable14 = "CREATE TABLE `" . $tblprefix . "files` (id_file int(10) unsigned NOT NULL auto_increment, id_user int(10) unsigned NOT NULL default '0', nom_file varchar(100) NOT NULL default '', taille_file int(10) unsigned NOT NULL default '0', lien_file varchar(30) NOT NULL default '', date_file int(10) unsigned NOT NULL default '0', is_image enum('0','1') NOT NULL default '0', type_user enum('u','l') NOT NULL default 'u', id_folder int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_file)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable14);
				if ($connect->query($createtable14))
        	echo "<li> files</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> files</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable15 = "DROP TABLE IF EXISTS `" . $tblprefix . "sondage_questions`;";
				$createtable15 = "CREATE TABLE `" . $tblprefix . "sondage_questions` (id_question int(10) unsigned NOT NULL auto_increment, id_conjoint int(10) unsigned NOT NULL default '0', question text NOT NULL, active_question enum('0','1') NOT NULL default '0', PRIMARY KEY  (id_question)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable15);
				if ($connect->query($createtable15))
        	echo "<li> sondage_questions</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> sondage_questions</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable16 = "DROP TABLE IF EXISTS `" . $tblprefix . "sondage_reponses`;";
				$createtable16 = "CREATE TABLE `" . $tblprefix . "sondage_reponses` (id_reponse int(10) unsigned NOT NULL auto_increment, id_question int(10) unsigned NOT NULL default '0', reponse varchar(200) NOT NULL default '', PRIMARY KEY  (id_reponse)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable16);
				if ($connect->query($createtable16))
        	echo "<li> sondage_reponses</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> sondage_reponses</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable17 = "DROP TABLE IF EXISTS `" . $tblprefix . "sondage_votes`;";
				$createtable17 = "CREATE TABLE `" . $tblprefix . "sondage_votes` (id_vote int(10) unsigned NOT NULL auto_increment, id_reponse1 int(10) unsigned NOT NULL default '0', id_reponse2 int(10) unsigned NOT NULL default '0', nbr_votes int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_vote)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable17);
				if ($connect->query($createtable17))
        	echo "<li> sondage_votes</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> sondage_votes</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable18 = "DROP TABLE IF EXISTS `" . $tblprefix . "sondage_ip`;";
				$createtable18 = "CREATE TABLE `" . $tblprefix . "sondage_ip` (id_ip int(10) unsigned NOT NULL auto_increment, ip_vote varchar(16) NOT NULL default '', heure_vote time NOT NULL default '00:00:00', id_question int(10) unsigned NOT NULL default '0', PRIMARY KEY  (id_ip)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable18);
				if ($connect->query($createtable18))
        	echo "<li> sondage_ip</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> sondage_ip</li></font>";
        	$error_create_table = 1;
        }
        flush();

				$deletetable19 = "DROP TABLE IF EXISTS `" . $tblprefix . "classes`;";
				$createtable19 = "CREATE TABLE `" . $tblprefix . "classes` (id_classe int(4) unsigned NOT NULL auto_increment, classe varchar(30) NOT NULL default '', PRIMARY KEY  (id_classe)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable19);
				if ($connect->query($createtable19))
					echo "<li> classes</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> classes</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable20 = "DROP TABLE IF EXISTS `" . $tblprefix . "devoirs`;";
				$createtable20 = "CREATE TABLE `" . $tblprefix . "devoirs` (id_devoir int(10) unsigned NOT NULL auto_increment, id_chapitre int(10) unsigned NOT NULL default '0', acces_devoir varchar(200) NOT NULL default '*', titre_devoir varchar(100) NOT NULL default '', contenu_devoir text NOT NULL, date_publie_devoir int(10) unsigned NOT NULL default '0', date_expire_devoir int(10) unsigned NOT NULL default '0', publie_devoir enum('1','0') NOT NULL default '1', ordre_devoir int(10) unsigned NOT NULL default '0', PRIMARY KEY (id_devoir)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable20);
				if ($connect->query($createtable20))
					echo "<li> devoirs</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> devoirs</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable21 = "DROP TABLE IF EXISTS `" . $tblprefix . "devoirs_rendus`;";
				$createtable21 = "CREATE TABLE `" . $tblprefix . "devoirs_rendus` (id_devoir_rendu int(10) unsigned NOT NULL auto_increment, id_devoir int(10) unsigned NOT NULL default '0', id_apprenant int(10) unsigned NOT NULL default '0', nom_file varchar(100) NOT NULL default '', taille_file int(10) unsigned NOT NULL default '0', lien_file varchar(100) NOT NULL default '', date_file int(10) unsigned NOT NULL default '0', PRIMARY KEY (id_devoir_rendu)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable21);
				if ($connect->query($createtable21))
					echo "<li> devoirs_rendus</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> devoirs_rendus</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable22 = "DROP TABLE IF EXISTS `" . $tblprefix . "devoirs_notes`;";
				$createtable22 = "CREATE TABLE `" . $tblprefix . "devoirs_notes` (id_devoir_note int(10) unsigned NOT NULL auto_increment, id_devoir int(10) unsigned NOT NULL default '0', id_apprenant int(10) unsigned NOT NULL default '0', note_devoir float(10) unsigned NOT NULL default '0', PRIMARY KEY (id_devoir_note)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable22);
				if ($connect->query($createtable22))
					echo "<li> devoirs_notes</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> devoirs_notes</li></font>";
					$error_create_table = 1;
				}
				flush();
				
				$deletetable23 = "DROP TABLE IF EXISTS `" . $tblprefix . "vermenu`;";
				$createtable23 = "CREATE TABLE `" . $tblprefix . "vermenu` (id_vermenu int(10) unsigned NOT NULL auto_increment, titre_vermenu varchar(100) NOT NULL default '', type_vermenu enum('article','url','module') NOT NULL default 'article', lien_vermenu varchar(200) NOT NULL default '', active_vermenu enum('1','0') NOT NULL default '1', ordre_vermenu int(3) unsigned NOT NULL default '0', PRIMARY KEY  (id_vermenu)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable23);
				if ($connect->query($createtable23))
					echo "<li> vermenu</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> vermenu</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable24 = "DROP TABLE IF EXISTS `" . $tblprefix . "commentaires`;";
				$createtable24 = "CREATE TABLE `" . $tblprefix . "commentaires` (id_post int(10) unsigned NOT NULL auto_increment, type_objet enum('a','t','c') NOT NULL default 'a', id_objet int(10) unsigned NOT NULL default '0', type_user enum('l','u') NOT NULL default 'l', id_user int(10) unsigned NOT NULL default '0', contenu_post text NOT NULL, date_creation int(10) unsigned NOT NULL default '0', date_modification int(10) unsigned NOT NULL default '0', PRIMARY KEY (id_post)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable24);
				if ($connect->query($createtable24))
					echo "<li> commentaires</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> commentaires</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable25 = "DROP TABLE IF EXISTS `" . $tblprefix . "behavior_notes`;";
				$createtable25 = "CREATE TABLE `" . $tblprefix . "behavior_notes` (id_behavior_note int(10) unsigned NOT NULL auto_increment, mois_note int(2) unsigned NOT NULL default '0', annee_note int(4) unsigned NOT NULL default '0', id_apprenant int(10) unsigned NOT NULL default '0', behavior_note float(10,5) unsigned NOT NULL default '0', PRIMARY KEY (id_behavior_note)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable25);
				if ($connect->query($createtable25))
					echo "<li> behavior_notes</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> behavior_notes</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable26 = "DROP TABLE IF EXISTS `" . $tblprefix . "infos_acces`;";
				$createtable26 = "CREATE TABLE `" . $tblprefix . "infos_acces` (id_acces int(10) unsigned NOT NULL auto_increment, type_user enum('l','u') NOT NULL default 'l', id_user int(10) unsigned NOT NULL default '0', ip_user varchar(16) NOT NULL default '', date_acces int(10) unsigned NOT NULL default '0', PRIMARY KEY (id_acces)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable26);
				if ($connect->query($createtable26))
					echo "<li> infos_acces</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> infos_acces</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable27 = "DROP TABLE IF EXISTS `" . $tblprefix . "reset_pass`;";
				$createtable27 = "CREATE TABLE `" . $tblprefix . "reset_pass` (id_reset int(10) unsigned NOT NULL auto_increment, type_user enum('l','u') NOT NULL default 'l', id_user int(10) unsigned NOT NULL default '0', key_reset varchar(16) NOT NULL default '', date_reset int(10) unsigned NOT NULL default '0', PRIMARY KEY (id_reset)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable27);
				if ($connect->query($createtable27))
					echo "<li> reset_pass</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> reset_pass</li></font>";
					$error_create_table = 1;
				}
				flush();

				$deletetable28 = "DROP TABLE IF EXISTS `" . $tblprefix . "folders`;";
				$createtable28 = "CREATE TABLE `" . $tblprefix . "folders` (id_folder int(10) unsigned NOT NULL auto_increment, id_user int(10) unsigned NOT NULL default '0', nom_folder varchar(100) NOT NULL default '', acces_folder varchar(200) NOT NULL default '0', date_folder int(10) unsigned NOT NULL default '0', publie_folder enum('1','0') NOT NULL default '1', apps_upload enum('0','1') NOT NULL default '0', PRIMARY KEY (id_folder)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
				$connect->query($deletetable28);
				if ($connect->query($createtable28))
					echo "<li> folders</li>";
				else {
					echo "<font color=\"red\"><li><b> : </b> folders</li></font>";
					$error_create_table = 1;
				}
				flush();
								
        $doinsertsite_infos = "INSERT INTO `" . $tblprefix . "site_infos` VALUES (1, 'website name', 'website title', 'http://www.manhali.com', 'Manhali - Free Learning Management System', 'manhali,Manhali,e-learning,e-formation,LMS,LCMS,Learning Management System,Managed Learning Environment,Virtual Learning Environment,Open Distance Learning,Computer Assisted Learning,Course Management System,Learning Support System,Tutorial Management System,Learning Content Management System,plate-forme d\'apprentissage en ligne,formation ouverte et à distance,enseignement assisté par ordinateur', '".$language."', 'website title','1','1','0','1','0','0',10,500);";
        if ($connect->query($doinsertsite_infos))
        	echo "<li> site_infos</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> site_infos</li></font>";
        	$error_create_table = 1;
        }
        flush();

        $doinsert1 = "INSERT INTO `" . $tblprefix . "composants` VALUES (1, 'horizontal_menu', 'horizontal_menu', '', '1',0);";
        $doinsert2 = "INSERT INTO `" . $tblprefix . "composants` VALUES (2, 'contact', 'contact_us', '', '1',0);";
        $doinsert3 = "INSERT INTO `" . $tblprefix . "composants` VALUES (3, 'breadcrumbs', 'breadcrumbs', '', '1',0);";
        $doinsert4 = "INSERT INTO `" . $tblprefix . "composants` VALUES (4, 'search', 'search', '', '1',0);";
        $doinsert5 = "INSERT INTO `" . $tblprefix . "composants` VALUES (5, 'comments', 'comments', '', '1',0);";
        $doinsert6 = "INSERT INTO `" . $tblprefix . "composants` VALUES (6, 'documents', 'document_sharing', '', '1',0);";
        $doinsert7 = "INSERT INTO `" . $tblprefix . "composants` VALUES (7, 'courses', 'tutoriels2', '', '1',2);";
        $doinsert8 = "INSERT INTO `" . $tblprefix . "composants` VALUES (8, 'vertical_menu', 'vertical_menu', '', '1',3);";
        $doinsert9 = "INSERT INTO `" . $tblprefix . "composants` VALUES (9, 'identification', 'identification', '', '1',1);";
        $doinsert10 = "INSERT INTO `" . $tblprefix . "composants` VALUES (10, 'poll', 'poll', '', '1',4);";
        $doinsert11 = "INSERT INTO `" . $tblprefix . "composants` VALUES (11, 'additional_block', 'additional_block', '<b>Powered by Manhali</b>', '0',5);";
        				
        $connect->query($doinsert1);
        $connect->query($doinsert2);
        $connect->query($doinsert3);
        $connect->query($doinsert4);
        $connect->query($doinsert5);
        $connect->query($doinsert6);
        $connect->query($doinsert7);
        $connect->query($doinsert8);
        $connect->query($doinsert9);
        $connect->query($doinsert10);
        if ($connect->query($doinsert11))
        	echo "<li> composants</li>";
        else {
        	echo "<font color=\"red\"><li><b> : </b> composants</li></font>";
        	$error_create_table = 1;
        }
        flush();
        
        if (isset($_POST['insert']) && !empty($_POST['insert'])) $insert = $_POST['insert']; else $insert = "non";
        if ($insert == "oui") {

						//************************** tutoriels ***********************************
							$insert_tutoriels_1 = "INSERT INTO `" . $tblprefix . "tutoriels` VALUES (1, 1, 'Qu’est ce que Manhali ?', '', '<p><strong>Manhali </strong>is a free and adaptive Learning Management System (LMS). It is installable and multi-language. Licensed under the GNU-GPL 3 and written in PHP and MySQL. Manhali can track and evaluate learners&rsquo; behavior and learning styles by clustering all learners in profiles (A, B, C, D and E) according to their behavior on the platform to allow educators to personalize courses for each profile.</p>\r\n\r\n<p style=\"text-align: center;\"><br />\r\n<img alt=\"Logo de Manhali\" src=\"".$initial_path2."images/logo.gif\" style=\"width: 200px; height: 200px;\" /></p>\r\n\r\n<p style=\"text-align: center;\"><strong>&quot;When teaching becomes amusing&quot;</strong></p>', '', 'by', '', '2', 1, ".time().", ".time().", 1, '*', 3, 15);";
        			
        			if ($connect->query($insert_tutoriels_1))
        				echo "<li> tutoriels</li>";
        			else echo "<font color=\"red\"><li><b> : </b> tutoriels</li></font>";
        			flush();
        			
						//************************** parties ***********************************

							$insert_parties_1 = "INSERT INTO `" . $tblprefix . "parties` VALUES (1, 1, 'Présentation du système Manhali', '', '', '', '1', 1);";
							$insert_parties_2 = "INSERT INTO `" . $tblprefix . "parties` VALUES (2, 1, 'Fonctionnalités du système Manhali', '', '', '', '1', 2);";

        			$connect->query($insert_parties_1);
        			if ($connect->query($insert_parties_2))
								echo "<li> parties</li>";
							else echo "<font color=\"red\"><li><b> : </b> parties</li></font>";
							flush();
							
						//************************** chapitres ***********************************

							$insert_chapitres_1 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (1, 1, 'Introduction', '', 18, '1', 1, ".time().", ".time().", 1, 3, '*');";
							$insert_chapitres_2 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (2, 1, 'Cours et profil d’apprenant', '', 7, '1', 2, ".time().", ".time().", 2, 8, '*');";
							$insert_chapitres_3 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (3, 1, 'Interface graphique', '', 23, '1', 3, ".time().", ".time().", 3, 16, '*');";
							$insert_chapitres_4 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (4, 2, 'Fonctionnalités générales', '', 12, '1', 1, ".time().", ".time().", 2, 10, '*');";
							$insert_chapitres_5 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (5, 2, 'Interface des apprenants', '', 5, '1', 2, ".time().", ".time().", 1, 6, '*');";
							$insert_chapitres_6 = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (6, 2, 'Interface d’administration', '', 40, '1', 3, ".time().", ".time().", 2, 11, '*');";

        			$connect->query($insert_chapitres_1);
        			$connect->query($insert_chapitres_2);
        			$connect->query($insert_chapitres_3);
        			$connect->query($insert_chapitres_4);
        			$connect->query($insert_chapitres_5);
        			if ($connect->query($insert_chapitres_6))
								echo "<li> chapitres</li>";
							else echo "<font color=\"red\"><li><b> : </b> chapitres</li></font>";
							flush();
							
						//************************** blocs ***********************************

							$insert_blocs_1 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (1, 1, 'Présentation rapide de Manhali', '<p><strong>Manhali </strong>est un syst&egrave;me de gestion d&rsquo;apprentissage adaptatif, installable et multilingue. C&rsquo;est un projet libre sous la licence GNU-GPL 3, cr&eacute;&eacute; en PHP/MYSQL.<br />\r\nManhali permet l&rsquo;adaptation de la formation sur plusieurs niveaux, l&rsquo;adaptation de l&rsquo;interface graphique selon la configuration de la machine apprenant, l&rsquo;adaptation du contenu scientifique par rapport &agrave; ses comp&eacute;tences sur le domaine enseign&eacute; et l&rsquo;adaptation des strat&eacute;gies p&eacute;dagogiques selon le comportement et le style d&rsquo;apprentissage de l&rsquo;apprenant. Le profil d&rsquo;apprenant de Manhali permet l&rsquo;analyse et l&rsquo;&eacute;valuation du comportement des apprenants en se basant sur des indicateurs d&rsquo;interaction entre l&rsquo;apprenant et le syst&egrave;me, et aussi sur l&rsquo;utilisation des outils p&eacute;dagogiques de la plateforme. Il propose &eacute;galement des outils pour la d&eacute;termination du style d&rsquo;apprentissage de l&rsquo;e-apprenant.</p>', '1', 1);";
							$insert_blocs_2 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (2, 1, 'Diagramme de communication de Manhali', '<p>Le diagramme de communication (appel&eacute; diagramme de collaboration en UML1), se concentre sur les &eacute;changes de messages entre les objets, il d&eacute;finit les interactions d&rsquo;un point de vue temporel entre les diff&eacute;rents objets du syst&egrave;me pour un cas d&rsquo;utilisation donn&eacute;. Pour Manhali, le cas d&rsquo;utilisation est le processus d&rsquo;apprentissage et ses interactions entre l&rsquo;apprenant, le formateur et le superviseur.</p>\r\n\r\n<p><img alt=\"\" src=\"".$initial_path2."images/collaboration.jpg\" style=\"width: 968px; height: 511px;\" /></p>', '1', 2);";
							$insert_blocs_3 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (3, 1, 'Caractéristiques du système Manhali', '<p>Cette version de Manhali contient plusieurs caract&eacute;ristiques qui rendent le syst&egrave;me :</p>\r\n\r\n<ul>\r\n	<li><u><strong>Multi-utilisateurs</strong></u></li>\r\n</ul>\r\n\r\n<p>Notre syst&egrave;me permet la pr&eacute;sence d&rsquo;utilisateurs multiples de mani&egrave;re synchrone sur la plateforme et la mise &agrave; jour des profils d&rsquo;apprenants instantan&eacute;ment avec le changement observ&eacute; sur leur comportement ou sur leur style d&rsquo;apprentissage.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Installable</strong></u></li>\r\n</ul>\r\n\r\n<p>La possibilit&eacute; d&rsquo;installer le syst&egrave;me sans devoir cr&eacute;er ni la base de donn&eacute;es ni les tables manuellement et sans devoir configurer le serveur web.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Totalement configurable</strong></u></li>\r\n</ul>\r\n\r\n<p>La possibilit&eacute; d&rsquo;activer, d&eacute;sactiver et configurer chaque fonctionnalit&eacute; du syst&egrave;me.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Multi-langues</strong></u></li>\r\n</ul>\r\n\r\n<p>Le syst&egrave;me contient actuellement trois langues, le fran&ccedil;ais, l&rsquo;anglais et l&rsquo;arabe. Il supporte &eacute;galement la possibilit&eacute; d&rsquo;ajouter d&rsquo;autres langues au syst&egrave;me par d&rsquo;autres utilisateurs sans mettre le syst&egrave;me &agrave; jour.</p>\r\n\r\n<ul>\r\n	<li><u><strong>S&eacute;curis&eacute;</strong></u></li>\r\n</ul>\r\n\r\n<p>La s&eacute;curit&eacute; du syst&egrave;me contre les failles web est prise en compte pour le d&eacute;veloppement du projet.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Conforme aux nouvelles normes</strong></u></li>\r\n</ul>\r\n\r\n<p>Le syst&egrave;me utilise les nouveaux standards de &laquo; W3C &raquo; pour pr&eacute;senter le contenu, ce dernier est encod&eacute; en &laquo; Unicode &raquo;, l&rsquo;encodage qui permet d&rsquo;avoir un affichage correct quelle que soit la langue utilis&eacute;e dans le syst&egrave;me.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Libre</strong></u></li>\r\n</ul>\r\n\r\n<p>Manhali est un projet libre sous la licence GNU-GPL&nbsp; version 3, c&rsquo;est-&agrave;-dire que tout le monde peut l&rsquo;utiliser librement et gratuitement. La licence garantit &agrave; l&rsquo;utilisateur les droits suivants :<br />\r\n- La libert&eacute; d&rsquo;utiliser le projet, pour n&rsquo;importe quel usage ;<br />\r\n- La libert&eacute; d&rsquo;&eacute;tudier le fonctionnement du projet et de l&rsquo;adapter &agrave; ses besoins, ce qui passe par l&rsquo;acc&egrave;s aux codes sources ;<br />\r\n- La libert&eacute; de redistribuer des copies du projet ;<br />\r\n- La libert&eacute; de faire b&eacute;n&eacute;ficier &agrave; la communaut&eacute; des versions modifi&eacute;es.</p>', '1', 3);";
							$insert_blocs_4 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (4, 2, 'Représentation d’un cours sous Manhali', '<p>Les cours r&eacute;dig&eacute;s sur Manhali sont divis&eacute;s sur des parties, chaque partie contient des chapitres, et chaque chapitre contient plusieurs blocs, devoirs et des questions d&rsquo;auto-&eacute;valuation. Le cours contient &eacute;galement un espace r&eacute;serv&eacute; aux commentaires et discussions.<br />\r\nLe contenu de chaque bloc peut varier entre du texte, des tableaux, des images, des animations Flash, des pistes audio ou vid&eacute;o.</p>\r\n\r\n<p style=\"text-align: center;\"><br />\r\n<img alt=\"\" src=\"".$initial_path2."images/cours_manhali.jpg\" style=\"width: 500px; height: 260px;\" /></p>', '1', 1);";
							$insert_blocs_5 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (5, 3, 'Template', '<p>Le template de Manhali se compose de trois panneaux essentiels, le panneau horizontal, le panneau vertical et le panneau de contenu. Le template utilis&eacute; contient en total 9 &eacute;l&eacute;ments :<br />\r\n1. Ent&ecirc;te (Header) ;<br />\r\n2. Menu horizontal ;<br />\r\n3. Chemin de navigation ;<br />\r\n4. Barre de recherche ;<br />\r\n5. Espace utilisateur ;<br />\r\n6. Zone des cours ;<br />\r\n7. Sondage ;<br />\r\n8. Informations sur le cours ou le chapitre ;<br />\r\n9. Contenu du cours ou du chapitre.</p>\r\n\r\n<p style=\"text-align: center;\"><img alt=\"\" src=\"".$initial_path2."images/interface_manhali.jpg\" style=\"width: 900px; height: 570px;\" /></p>', '1', 1);";
							$insert_blocs_6 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (6, 3, 'Palettes des couleurs', '<p>Manhali contient un s&eacute;lecteur de th&egrave;mes qui permet &agrave; l&rsquo;apprenant de choisir sa palette de couleurs pr&eacute;f&eacute;r&eacute;e parmi 5 palettes du template existantes sur le syst&egrave;me afin de favoriser la relaxation et la d&eacute;tente.</p>\r\n\r\n<p style=\"text-align: center;\"><img alt=\"\" src=\"".$initial_path2."images/palettes_manhali.jpg\" style=\"width: 345px; height: 317px;\" /></p>', '1', 2);";
							$insert_blocs_7 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (7, 2, 'Profil d’apprenant', '<p>Le profil d&rsquo;apprenant de Manhali contient les informations personnelles de l&rsquo;apprenant, ses r&eacute;sultats des devoirs, son navigateur web et son syst&egrave;me d&rsquo;exploitation, les statistiques de son comportement, son style d&rsquo;apprentissage selon les th&eacute;ories de Kolb et Felder et l&rsquo;historique de ses notes de comportement class&eacute;es par mois.<br />\r\nEn se basant sur ce profil d&rsquo;apprenant, le formateur peut adapter son cours pour chaque classe d&rsquo;apprenant (grades : A, B, C, D et E) en cr&eacute;ant des chapitres personnalis&eacute;s pour chaque grade en fonction des besoins actuels des apprenants (pr&eacute;f&eacute;rences, comportement et styles d&rsquo;apprentissage).</p>\r\n\r\n<p style=\"text-align: center;\"><img alt=\"\" src=\"".$initial_path2."images/profil_apprenant.jpg\" style=\"width: 900px; height: 590px;\" /></p>', '1', 2);";
							$insert_blocs_8 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (8, 4, 'Installation et configuration', '<p>Les fonctionnalit&eacute;s g&eacute;n&eacute;rales du syst&egrave;me permettent la mise en place et l&rsquo;administration d&rsquo;une plateforme d&rsquo;apprentissage en ligne, ces fonctionnalit&eacute;s int&eacute;ressent seulement le super-administrateur, c&rsquo;est lui le seul responsable sur la partie technique de la plateforme.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Configuration g&eacute;n&eacute;rale</strong></u></li>\r\n</ul>\r\n\r\n<p>Pour utiliser Manhali, on doit d&rsquo;abord configurer le syst&egrave;me en utilisant le fichier &laquo; includes/dbconfig.php &raquo;, on doit fournir les informations n&eacute;cessaires pour la connexion &agrave; la base de donn&eacute;es (Nom de serveur de base de donn&eacute;es, nom d&rsquo;utilisateur, mot de passe et nom de base de donn&eacute;es). On trouve ensuite trois valeurs &agrave; modifier, cette modification est fortement recommand&eacute;e pour des raisons de s&eacute;curit&eacute; :<br />\r\n- Le mot de passe d&rsquo;installation : pour prot&eacute;ger la page d&rsquo;installation contre une utilisation ult&eacute;rieure par un intrus.<br />\r\n- Le pr&eacute;fix des tables de la base de donn&eacute;es : cette option permet de personnaliser les noms des tables dans la base de donn&eacute;es. Gr&acirc;ce &agrave; cette modification, m&ecirc;me si un utilisateur malveillant trouve une faille au niveau des requ&ecirc;tes envoy&eacute;es et re&ccedil;ues du serveur de base de donn&eacute;es, il ne peut pas appliquer des &laquo; injections SQL &raquo; parce qu&rsquo;il ne conna&icirc;t pas les noms des tables &agrave; utiliser dans ses requ&ecirc;tes imbriqu&eacute;es.<br />\r\n- Le nom du dossier d&rsquo;administration : le nom du dossier d&rsquo;administration est dynamique dans le syst&egrave;me, l&rsquo;administrateur peut renommer le dossier d&rsquo;administration plusieurs fois &agrave; condition qu&rsquo;il modifie aussi cette valeur dans le fichier de configuration. De cette mani&egrave;re personne ne conna&icirc;tra le chemin d&rsquo;administration sur le serveur.</p>\r\n\r\n<ul>\r\n	<li><u><strong>Installation</strong></u></li>\r\n</ul>\r\n\r\n<p>Pour installer ou r&eacute;installer le syst&egrave;me, l&rsquo;administrateur lance la page &laquo; install.php &raquo;. Apr&egrave;s que l&rsquo;administrateur saisit correctement le mot de passe d&rsquo;installation, l&rsquo;assistance le guide &agrave; choisir la langue du site &agrave; partir des langues existantes sur le syst&egrave;me. Ensuite, le syst&egrave;me va v&eacute;rifier la connexion au serveur de base de donn&eacute;es, cr&eacute;er la base de donn&eacute;es, &eacute;tablir la connexion par la suite, cr&eacute;er les tables et les donn&eacute;es d&rsquo;exemples si l&rsquo;administrateur veut tester les fonctionnalit&eacute;s du syst&egrave;me sans cr&eacute;er son propre contenu. A la fin de l&rsquo;installation, l&rsquo;administrateur doit obligatoirement renommer ou supprimer le dossier d&rsquo;installation avant qu&rsquo;il commence &agrave; g&eacute;rer son site.</p>\r\n\r\n<ul>\r\n	<li><strong><u>Gestion des langues</u></strong></li>\r\n</ul>\r\n\r\n<p>Manhali est un syst&egrave;me multilingue, chaque utilisateur du syst&egrave;me (apprenant ou formateur) peut choisir la langue &agrave; utiliser pour son interface, le syst&egrave;me m&eacute;morise ce choix dans son profil.</p>', '1', 1);";
							$insert_blocs_9 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (9, 5, 'Composants de l’interface apprenant', '<p>L&rsquo;interface des apprenants contient un ensemble des &eacute;l&eacute;ments g&eacute;r&eacute;s par les administrateurs et qui peuvent &ecirc;tre personnalis&eacute;s &eacute;galement par l&rsquo;apprenant lui-m&ecirc;me, ces &eacute;l&eacute;ments sont :</p>\r\n\r\n<ul>\r\n	<li><u><strong>Cours</strong></u></li>\r\n</ul>\r\n\r\n<p>Les cours sont pr&eacute;sent&eacute;s pour l&rsquo;apprenant sur deux types d&rsquo;affichage, l&rsquo;affichage du sommaire et des informations sur le cours et l&rsquo;affichage du chapitre :<br />\r\n<u>- Affichage du sommaire</u><br />\r\nL&rsquo;apprenant trouve dans la page du sommaire les informations sur le cours, notamment, le nom d&rsquo;auteur, la licence, la date de cr&eacute;ation, la date de derni&egrave;re modification, le nombre de lectures, le pourcentage de r&eacute;ponses correctes d&rsquo;auto-&eacute;valuation et la note d&rsquo;appr&eacute;ciation donn&eacute;e par les apprenants. Ensuite, on trouve la hi&eacute;rarchie compl&egrave;te du cours qui contient les objectifs, l&rsquo;introduction, la conclusion et des liens vers les parties, les chapitres et les blocs.<br />\r\n<u>- Affichage du chapitre</u><br />\r\nLe contenu d&rsquo;un cours est pr&eacute;sent&eacute; comme nous avons vu dans la conception du cours sous forme des chapitres, le contenu d&rsquo;un chapitre se diff&egrave;re entre des blocs du texte, des animations Flash, des pistes audio et vid&eacute;o, des images et sch&eacute;mas, des questions d&rsquo;auto-&eacute;valuation et des devoirs, et une partie &agrave; la fin r&eacute;serv&eacute;e aux commentaires et discussions.</p>\r\n\r\n<ul>\r\n	<li><strong><u>Profil d&rsquo;apprenant</u></strong></li>\r\n</ul>\r\n\r\n<p>Le profil d&rsquo;apprenant est l&rsquo;&eacute;l&eacute;ment qui permet la mod&eacute;lisation des connaissances, du comportement et du style d&rsquo;apprentissage de l&rsquo;apprenant. Il englobe cinq types des informations, les informations personnelles de l&rsquo;apprenant, des informations sur sa machine, son comportement en ligne, ses comp&eacute;tences de domaine et son style d&rsquo;apprentissage. Le profil d&rsquo;apprenant permet l&rsquo;adaptation de la formation aux besoins de l&rsquo;apprenant sur trois niveaux :<br />\r\n- Adaptation de l&rsquo;aspect graphique de la plateforme selon la configuration de la machine apprenant ;<br />\r\n- Adaptation du contenu scientifique par rapport aux comp&eacute;tences de l&rsquo;apprenant sur le domaine enseign&eacute;, en utilisant les notes de l&rsquo;apprenant obtenues par les outils d&rsquo;&eacute;valuations propos&eacute;s (devoirs, QCM&hellip;) ;<br />\r\n- Adaptation des strat&eacute;gies p&eacute;dagogiques selon le comportement et le style d&rsquo;apprentissage de l&rsquo;apprenant.</p>\r\n\r\n<ul>\r\n	<li><strong><u>Volet vertical</u></strong></li>\r\n</ul>\r\n\r\n<p>Le volet vertical de l&rsquo;interface d&rsquo;apprenant contient plusieurs blocs de contenu :<br />\r\n- Bloc des cours : contient l&rsquo;arborescence des cours valid&eacute;s et publi&eacute;s sur le site ;<br />\r\n- Sondage : Il contient les questions d&rsquo;un sondage ajout&eacute; par l&rsquo;administrateur et le lien vers les r&eacute;sultats.<br />\r\n- Bloc suppl&eacute;mentaire : l&rsquo;administrateur peut choisir librement le contenu de ce bloc (informations de contact, partenaires, mesure d&rsquo;audience&hellip;).</p>\r\n\r\n<ul>\r\n	<li><strong><u>Volet horizontal</u></strong></li>\r\n</ul>\r\n\r\n<p>Le volet horizontal contient &eacute;galement quatre composants :<br />\r\n- S&eacute;lecteur de th&egrave;me : L&rsquo;apprenant peut modifier la palette des couleurs utilis&eacute;e, et le syst&egrave;me enregistre la palette favorable pour chaque apprenant et l&rsquo;applique chaque fois que l&rsquo;apprenant retourne &agrave; la plateforme.<br />\r\n- Menu horizontal : contient plusieurs &eacute;l&eacute;ments de menu, chaque &eacute;l&eacute;ment peut contenir :<br />\r\n+ des articles ;<br />\r\n+ des modules, l&rsquo;administrateur du site peut d&eacute;velopper ses propres modules en PHP et les ajouter dans le syst&egrave;me.<br />\r\n+ ou bien un lien vers une page interne ou externe.<br />\r\n- Chemin de navigation : (BreadCrumbs) ce composant permet d&rsquo;afficher l&rsquo;emplacement exact du visiteur sur le site.<br />\r\n- Bloc de recherche : pour rechercher une information sur la plateforme avec un syst&egrave;me de correction orthographique des phrases recherch&eacute;es.</p>', '1', 1);";
							$insert_blocs_10 = "INSERT INTO `" . $tblprefix . "blocs` VALUES (10, 6, 'Composants de l’interface d’administration', '<p>L&rsquo;interface d&rsquo;administration contient 14 fonctionnalit&eacute;s, les formateurs et les superviseurs peuvent utiliser seulement 7 fonctionnalit&eacute;s du syst&egrave;me, le reste est r&eacute;serv&eacute; aux administrateurs et au Super-Administrateur.</p>\r\n\r\n<ul>\r\n	<li><strong><u>Fonctionnalit&eacute;s ouvertes &agrave; toute l&rsquo;&eacute;quipe d&rsquo;administration</u></strong></li>\r\n</ul>\r\n\r\n<p><u>- Statistiques</u> : contient des statistiques sur les apprenants, les utilisateurs, les articles, les cours et les devoirs.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des messages</u> : une messagerie compl&egrave;te entre les utilisateurs du syst&egrave;me avec la possibilit&eacute; de r&eacute;pondre, transf&eacute;rer les messages et aussi envoyer des messages multi-destinataires.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des utilisateurs</u> : permet aux administrateurs de g&eacute;rer toutes les informations des utilisateurs, et permet aux formateurs et superviseurs de modifier leurs informations personnelles. Cette rubrique permet aussi de consulter les profils des utilisateurs.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des apprenants</u> : permet de g&eacute;rer les apprenants et les classes, de consulter leurs profils et de t&eacute;l&eacute;charger les informations des apprenants en fichier csv.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des cours</u> : permet aux formateurs d&rsquo;ajouter et g&eacute;rer leurs propres cours, et permet aux superviseurs et administrateurs de g&eacute;rer et valider tous les cours. Il existe trois types de cours :<br />\r\n+ Les cours en cours de cr&eacute;ation : chaque formateur peut g&eacute;rer librement ses cours qui sont en cours de cr&eacute;ation, il peut aussi ajouter des remarques sur certaine partie du cours afin d&rsquo;aider les superviseurs &agrave; v&eacute;rifier le cours et l&rsquo;ordonner correctement par rapport aux autres cours. Lorsqu&rsquo;un formateur termine la cr&eacute;ation de son cours, il demande la validation, le statut du cours devient &laquo; en attente de validation &raquo;.<br />\r\n+ Les cours en attente de validation : L&rsquo;interface des superviseurs permet de g&eacute;rer les cours en attente de validation. Le superviseur peut valider le cours, le modifier ou bien envoyer un rapport des modifications au formateur.<br />\r\n+ Les cours valid&eacute;s : ce sont les cours qui vont &ecirc;tre publi&eacute;s sur l&rsquo;interface des apprenants. Le formateur ne peut pas modifier un cours valid&eacute; qu&rsquo;apr&egrave;s qu&rsquo;il le d&eacute;publie, dans ce cas, le cours devient &laquo; en cours de cr&eacute;ation &raquo;, il ne sera publier qu&rsquo;apr&egrave;s une deuxi&egrave;me validation des modifications.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des articles</u> : permet aux formateurs d&rsquo;ajouter et g&eacute;rer leurs propres articles, et permet aux superviseurs et administrateurs de g&eacute;rer et valider tous les articles, et aussi de s&eacute;lectionner les articles qui s&rsquo;affichent dans la page d&rsquo;accueil.</p>\r\n\r\n<p><br />\r\n<u>- Gestion &eacute;lectronique des documents (GED)</u> : permet le partage de documents entre les diff&eacute;rents types des utilisateurs. L&rsquo;administrateur d&eacute;termine les groupes d&rsquo;apprenants qui peuvent acc&eacute;der &agrave; certain dossier, il g&egrave;re &eacute;galement les privil&egrave;ges d&rsquo;&eacute;criture de chaque dossier, est ce que les apprenants peuvent partager leurs documents ou juste consulter les documents des formateurs.</p>\r\n\r\n<ul>\r\n	<li><strong><u>Fonctionnalit&eacute;s r&eacute;serv&eacute;es aux administrateurs</u></strong></li>\r\n</ul>\r\n\r\n<p><u>- Gestion des sondages</u> : permet de d&eacute;terminer les opinions des utilisateurs sur un sujet, le syst&egrave;me contient deux types de sondage :<br />\r\n+ Sondage simple : permet d&rsquo;analyser les votes sur des r&eacute;ponses d&rsquo;une question.<br />\r\n+ Sondage d&rsquo;analyse crois&eacute;e : permet d&rsquo;analyser les r&eacute;sultats d&rsquo;une enqu&ecirc;te compos&eacute;e de deux questions d&eacute;pendantes.</p>\r\n\r\n<p><br />\r\n<u>- Gestion du menu horizontal</u> : permet de g&eacute;rer le contenu du menu horizontal (articles, liens ou modules).</p>\r\n\r\n<p><br />\r\n<u>- Gestion du menu vertical</u> : permet de g&eacute;rer le contenu du menu vertical.</p>\r\n\r\n<p><br />\r\n<u>- Gestion de la page d&rsquo;accueil</u> : permet d&rsquo;ordonner les articles de la page d&rsquo;accueil et modifier le type d&rsquo;affichage de la page d&rsquo;accueil.</p>\r\n\r\n<p><br />\r\n<u>- Configuration g&eacute;n&eacute;rale</u> : permet la configuration g&eacute;n&eacute;rale du syst&egrave;me comme la langue par d&eacute;faut, le nombre d&rsquo;&eacute;l&eacute;ments &agrave; afficher par page, la gestion des inscriptions et les informations g&eacute;n&eacute;rales du site web utilis&eacute;es pour le r&eacute;f&eacute;rencement du site.</p>\r\n\r\n<p><br />\r\n<u>- Gestion des composants</u> : permet d&rsquo;activer ou d&eacute;sactiver certain composant du syst&egrave;me.</p>\r\n\r\n<p><br />\r\n<u>- Informations d&rsquo;acc&egrave;s</u> : consulter les statistiques d&rsquo;acc&egrave;s des utilisateurs par date ou par adresse IP.</p>', '1', 1);";

        			$connect->query($insert_blocs_1);
        			$connect->query($insert_blocs_2);
        			$connect->query($insert_blocs_3);
        			$connect->query($insert_blocs_4);
        			$connect->query($insert_blocs_5);
        			$connect->query($insert_blocs_6);
        			$connect->query($insert_blocs_7);
        			$connect->query($insert_blocs_8);
        			$connect->query($insert_blocs_9);
        			if ($connect->query($insert_blocs_10))
								echo "<li> blocs</li>";
							else echo "<font color=\"red\"><li><b> : </b> blocs</li></font>";
							flush();
							
						//************************** articles ***********************************

        			$insert_articles_1 = "INSERT INTO `" . $tblprefix . "articles` VALUES (1,1,1,'Article 1','Article content 1','1','1',2,2,1,".time().",".time().",'*',4,2);";
        			$insert_articles_2 = "INSERT INTO `" . $tblprefix . "articles` VALUES (2,1,1,'Article 2','Article content 2','1','0',3,1,1,".time().",".time().",'0',4,3);";
        			$insert_articles_3 = "INSERT INTO `" . $tblprefix . "articles` VALUES (3,2,1,'Article 3','Article content 3','1','1',1,3,1,".time().",".time().",'*',4,1);";
        			$insert_articles_4 = "INSERT INTO `" . $tblprefix . "articles` VALUES (4,2,6,'Article 4','Article content 4','1','1',1,4,1,".time().",".time().",'-1-',4,4);";

        			$connect->query($insert_articles_1);
        			$connect->query($insert_articles_2);
        			$connect->query($insert_articles_3);
        			if ($connect->query($insert_articles_4))
								echo "<li> articles</li>";
							else echo "<font color=\"red\"><li><b> : </b> articles</li></font>";
							flush();
							
						//************************** hormenu ***********************************

        			$insert_hormenu_1 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (1,'Articles','article','','1',1);";
        			$insert_hormenu_2 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (2,'Administration','url','".$adminfolder."','1',3);";
        			$insert_hormenu_3 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (3,'Contact','url','?contact','1',2);";
        			$insert_hormenu_4 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (4,'Modules','module','module1','1',5);";
        			$insert_hormenu_5 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (5,'Manhali website','url','http://www.manhali.com','1',4);";
							$insert_hormenu_6 = "INSERT INTO `" . $tblprefix . "hormenu` VALUES (6,'Articles2','article','','0',6);";
							
        			$connect->query($insert_hormenu_1);
        			$connect->query($insert_hormenu_2);
        			$connect->query($insert_hormenu_3);
        			$connect->query($insert_hormenu_4);
        			$connect->query($insert_hormenu_5);
        			if ($connect->query($insert_hormenu_6))
								echo "<li> hormenu</li>";
							else echo "<font color=\"red\"><li><b> : </b> hormenu</li></font>";
							flush();

						//************************** vermenu ***********************************

        			$insert_vermenu_1 = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (1,'Document sharing','url','?documents','1',1);";
        			$insert_vermenu_2 = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (2,'Contact','url','?contact','1',2);";
        			$insert_vermenu_3 = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (3,'Search','url','?search','1',3);";
							
        			$connect->query($insert_vermenu_1);
        			$connect->query($insert_vermenu_2);
        			if ($connect->query($insert_vermenu_3))
								echo "<li> vermenu</li>";
							else echo "<font color=\"red\"><li><b> : </b> vermenu</li></font>";
							flush();
							
						//************************** qcm ***********************************

        			$insert_qcm_1 = "INSERT INTO `" . $tblprefix . "qcm` VALUES (1,1,'<b>Question 1</b>','Answer 1','Answer 2','Answer 3','','','','2',1,1,'1',1);";
         			$insert_qcm_2 = "INSERT INTO `" . $tblprefix . "qcm` VALUES (2,1,'<b>Question 2</b>','Answer 1','Answer 2','Answer 3','Answer 4','Answer 5','Answer 6','6',2,0,'1',2);";       			
        			$insert_qcm_3 = "INSERT INTO `" . $tblprefix . "qcm` VALUES (3,2,'<b>Question 3</b>','Answer 1','Answer 2','','','','','2',1,1,'1',3);";
         			$insert_qcm_4 = "INSERT INTO `" . $tblprefix . "qcm` VALUES (4,3,'<b>Question 4</b>','Answer 1','Answer 2','Answer 3','Answer 4','','','3',4,1,'1',1);";       			

        			$connect->query($insert_qcm_1);
        			$connect->query($insert_qcm_2);
        			$connect->query($insert_qcm_3);
        			if ($connect->query($insert_qcm_4))
								echo "<li> qcm</li>";
							else echo "<font color=\"red\"><li><b> : </b> qcm</li></font>";
							flush();
							
						//************************** messages ***********************************

        			$insert_messages_1 = "INSERT INTO `" . $tblprefix . "messages` VALUES (1,0,0,'Visitor 1','visitor1@mail.com','*','*','Message subject 1','Messsage content 1','-','-',".time().",'*','*','1');";
        			
        			if ($connect->query($insert_messages_1))
								echo "<li> messages</li>";
							else echo "<font color=\"red\"><li><b> : </b> messages</li></font>";
							flush();
							
						//************************** users ***********************************

        			$insert_users_1 = "INSERT INTO `" . $tblprefix . "users` VALUES (2,'trainer name','trainer','8446c9a974f3e57149b864682572bb75p4krx71s','trainer@manhali.com','0','0','woman.jpg','F',".time().",0,'0','".$language."','-','-',0,0,0,0);";
        			
        			if ($connect->query($insert_users_1))
        				echo "<li> users</li>";
        			else echo "<font color=\"red\"><li><b> : </b> users</li></font>";
        			flush();

						//************************** apprenants ***********************************

        			$insert_apprenants_1 = "INSERT INTO `" . $tblprefix . "apprenants` VALUES (1,1,'Student name','student','c177263d7ccd730a04b2fdc809ed2e9b22oy4qr4','student@manhali.com','1/7/1985','0','man.jpg','M',".time().",0,'0','-','-','".$language."',0,0,0,0,0,0,1,'-','E','','-');";
        			
        			if ($connect->query($insert_apprenants_1))
        				echo "<li> apprenants</li>";
        			else echo "<font color=\"red\"><li><b> : </b> apprenants</li></font>";
        			flush();

						//************************** classes ***********************************

        			$insert_classes_1 = "INSERT INTO `" . $tblprefix . "classes` VALUES (1,'Class 1');";
        			
        			if ($connect->query($insert_classes_1))
        				echo "<li> classes</li>";
        			else echo "<font color=\"red\"><li><b> : </b> classes</li></font>";
        			flush();

						//************************** files ***********************************

							$insert_files_1 = "INSERT INTO `" . $tblprefix . "files` VALUES (1, 1, 'Manhali.txt', 393, '8olq86qur0udopffipvaqoox.txt', ".time().", '0', 'u', 1);";

        			if ($connect->query($insert_files_1))
								echo "<li> files</li>";
							else echo "<font color=\"red\"><li><b> : </b> files</li></font>";
							flush();

						//************************** folders ***********************************

							$insert_folders_1 = "INSERT INTO `" . $tblprefix . "folders` VALUES (1, 1, 'Public folder', '*', ".time().", '1', '0');";

        			if ($connect->query($insert_folders_1))
								echo "<li> folders</li>";
							else echo "<font color=\"red\"><li><b> : </b> folders</li></font>";
							flush();
							
						//************************** sondage_questions ***********************************

        			$insert_sondage_questions_1 = "INSERT INTO `" . $tblprefix . "sondage_questions` VALUES (1,2,'What is your occupation?','1');";
         			$insert_sondage_questions_2 = "INSERT INTO `" . $tblprefix . "sondage_questions` VALUES (2,0,'How satisfied are you with Manhali?','1');";

        			$connect->query($insert_sondage_questions_1);
        			if ($connect->query($insert_sondage_questions_2))
								echo "<li> sondage_questions</li>";
							else echo "<font color=\"red\"><li><b> : </b> sondage_questions</li></font>";
							flush();

						//************************** sondage_reponses ***********************************

        			$insert_sondage_reponses_1 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (1,1,'Student');";
							$insert_sondage_reponses_2 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (2,1,'Teacher / Educator');";
							$insert_sondage_reponses_3 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (3,1,'Webmaster / Developer');";
							$insert_sondage_reponses_4 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (4,1,'Journalist / Blogger / Web writer');";
							$insert_sondage_reponses_5 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (5,1,'Other');";
							$insert_sondage_reponses_6 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (6,2,'Very satisfied');";
							$insert_sondage_reponses_7 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (7,2,'Satisfied');";
							$insert_sondage_reponses_8 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (8,2,'Neutral');";
							$insert_sondage_reponses_9 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (9,2,'Not satisfied');";
							$insert_sondage_reponses_10 = "INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (10,2,'Very dissatisfied');";

        			$connect->query($insert_sondage_reponses_1);
        			$connect->query($insert_sondage_reponses_2);
        			$connect->query($insert_sondage_reponses_3);
        			$connect->query($insert_sondage_reponses_4);
        			$connect->query($insert_sondage_reponses_5);
        			$connect->query($insert_sondage_reponses_6);
        			$connect->query($insert_sondage_reponses_7);
        			$connect->query($insert_sondage_reponses_8);
        			$connect->query($insert_sondage_reponses_9);
        			if ($connect->query($insert_sondage_reponses_10))
								echo "<li> sondage_reponses</li>";
							else echo "<font color=\"red\"><li><b> : </b> sondage_reponses</li></font>";
							flush();

						//************************** sondage_votes ***********************************

        			$insert_sondage_votes_1 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (1,1,6,5);";
							$insert_sondage_votes_2 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (2,1,7,8);";
							$insert_sondage_votes_3 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (3,1,8,2);";
							$insert_sondage_votes_4 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (4,1,9,12);";
							$insert_sondage_votes_5 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (5,1,10,0);";
							$insert_sondage_votes_6 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (6,2,6,9);";
							$insert_sondage_votes_7 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (7,2,7,5);";
							$insert_sondage_votes_8 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (8,2,8,8);";
							$insert_sondage_votes_9 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (9,2,9,18);";
							$insert_sondage_votes_10 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (10,2,10,1);";
        			$insert_sondage_votes_11 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (11,3,6,15);";
							$insert_sondage_votes_12 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (12,3,7,2);";
							$insert_sondage_votes_13 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (13,3,8,0);";
							$insert_sondage_votes_14 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (14,3,9,2);";
							$insert_sondage_votes_15 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (15,3,10,0);";
							$insert_sondage_votes_16 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (16,4,6,19);";
							$insert_sondage_votes_17 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (17,4,7,3);";
							$insert_sondage_votes_18 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (18,4,8,18);";
							$insert_sondage_votes_19 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (19,4,9,18);";
							$insert_sondage_votes_20 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (20,4,10,1);";
        			$insert_sondage_votes_21 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (21,5,6,6);";
							$insert_sondage_votes_22 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (22,5,7,9);";
							$insert_sondage_votes_23 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (23,5,8,7);";
							$insert_sondage_votes_24 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (24,5,9,12);";
							$insert_sondage_votes_25 = "INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (25,5,10,0);";

        			$connect->query($insert_sondage_votes_1);
        			$connect->query($insert_sondage_votes_2);
        			$connect->query($insert_sondage_votes_3);
        			$connect->query($insert_sondage_votes_4);
        			$connect->query($insert_sondage_votes_5);
        			$connect->query($insert_sondage_votes_6);
        			$connect->query($insert_sondage_votes_7);
        			$connect->query($insert_sondage_votes_8);
        			$connect->query($insert_sondage_votes_9);
        			$connect->query($insert_sondage_votes_10);
        			$connect->query($insert_sondage_votes_11);
        			$connect->query($insert_sondage_votes_12);
        			$connect->query($insert_sondage_votes_13);
        			$connect->query($insert_sondage_votes_14);
        			$connect->query($insert_sondage_votes_15);
        			$connect->query($insert_sondage_votes_16);
        			$connect->query($insert_sondage_votes_17);
        			$connect->query($insert_sondage_votes_18);
        			$connect->query($insert_sondage_votes_19);
        			$connect->query($insert_sondage_votes_20);
        			$connect->query($insert_sondage_votes_21);
        			$connect->query($insert_sondage_votes_22);
        			$connect->query($insert_sondage_votes_23);
        			$connect->query($insert_sondage_votes_24);
        			if ($connect->query($insert_sondage_votes_25))
								echo "<li> sondage_votes</li>";
							else echo "<font color=\"red\"><li><b> : </b> sondage_votes</li></font>";
							flush();

						//************************** devoirs ***********************************

        			$insert_devoirs_1 = "INSERT INTO `" . $tblprefix . "devoirs` VALUES (1,1,'*','Homework 1','Homework 1 content',".time().", ".time()."+2592000,'1',1);";
        			
        			if ($connect->query($insert_devoirs_1))
        				echo "<li> devoirs</li>";
        			else echo "<font color=\"red\"><li><b> : </b> devoirs</li></font>";
        			flush();
							
						//************************** devoirs_rendus ***********************************

        			$insert_devoirs_rendus_1 = "INSERT INTO `" . $tblprefix . "devoirs_rendus` VALUES (1,1,1,'Homework 1 Student.docx',10171,'1_Class_1_1_student.docx',".time()."+60);";
        			
        			if ($connect->query($insert_devoirs_rendus_1))
        				echo "<li> devoirs_rendus</li>";
        			else echo "<font color=\"red\"><li><b> : </b> devoirs_rendus</li></font>";
        			flush();

						//************************** devoirs_notes ***********************************

        			$insert_devoirs_notes_1 = "INSERT INTO `" . $tblprefix . "devoirs_notes` VALUES (1,1,1,15.75);";
        			
        			if ($connect->query($connect,$insert_devoirs_notes_1,))
        				echo "<li> devoirs_notes</li>";
        			else echo "<font color=\"red\"><li><b> : </b> devoirs_notes</li></font>";
        			flush();
        			
						//************************** commentaires ***********************************

        			$insert_commentaires_1 = "INSERT INTO `" . $tblprefix . "commentaires` VALUES (1,'a',1,'u',1,'Post 1 article',".time().",".time()."+1000);";
        			$insert_commentaires_2 = "INSERT INTO `" . $tblprefix . "commentaires` VALUES (2,'a',1,'l',1,'Post 2 article',".time().",".time()."+2000);";
         			$insert_commentaires_3 = "INSERT INTO `" . $tblprefix . "commentaires` VALUES (3,'t',1,'l',1,'Post tutoriel',".time().",".time()."+3000);";

        			$connect->query($insert_commentaires_1);
        			$connect->query($insert_commentaires_2);
        			if ($connect->query($insert_commentaires_3))
								echo "<li> commentaires</li>";
							else echo "<font color=\"red\"><li><b> : </b> commentaires</li></font>";
							flush();

						//************************** behavior_notes ***********************************

        			$insert_behavior_notes_1 = "INSERT INTO `" . $tblprefix . "behavior_notes` VALUES (1,".date("m",time()).",".date("Y",time()).",1,13);";

        			if ($connect->query($insert_behavior_notes_1))
        				echo "<li> behavior_notes</li>";
        			else echo "<font color=\"red\"><li><b> : </b> behavior_notes</li></font>";
        			flush();

						//************************** infos_acces ***********************************

        			$insert_infos_acces_1 = "INSERT INTO `" . $tblprefix . "infos_acces` VALUES (1,'l',1,'100.100.100.100',".time().");";
        			
        			if ($connect->query($insert_infos_acces_1))
        				echo "<li> infos_acces</li>";
        			else echo "<font color=\"red\"><li><b> : </b> infos_acces</li></font>";
        			flush();
        			
  					//***********************************************************************
        }
        
        echo "</ul>";
        if ($error_create_table == 0)
					echo "<p><form name=\"form2\" method=\"POST\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"etape\" value=\"3\"><input type=\"submit\" class=\"button\" value=\"install_step3\"></form></p>";
				else
					echo "<p><form name=\"form\" method=\"POST\"><input type=\"hidden\" name=\"etape\" value=\"1\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"lang\" value=\"".$language."\"><input type=\"submit\" class=\"button\" value=\"retour\"></form></p>";
	}
	break;
	
	//*****************************************************************************
	
	case "3" : {
						echo "<h4>administration</h4>";
	        	echo "<form method=\"POST\" action=\"\">";
	        	echo "<p><b>identifiant : </b><br /><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	        	echo "<p><b>password : </b><br /><br /><input name=\"password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> carac5_min</p>";
	        	echo "<p><b>confirmpassword : </b><br /><br /><input name=\"pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	        	echo "<p><b>email : </b><br /><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"\"></p>";
	        	echo "<p><b>select_sex : </b><br /><select name=\"sexe\">";
						echo "<option value=\"0\"></option>";
						echo "<option value=\"F\">female</option>";
						echo "<option value=\"M\">male</option>";
						echo "</select>";
	        	echo "<p><font color=\"red\"><b>tous_champs_obligatoires</b></font><br /><br /><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"etape\" value=\"4\">";
	        	echo "<input type=\"submit\" class=\"button\" value=\"install_step4\"></form></p>";
	} break;
	
	//*****************************************************************************
	
	case "4" : {
	              if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['pass_conf']) && !empty($_POST['email']) && ($_POST['sexe'] == "M" || $_POST['sexe'] == "F")) {
	                
	                $login = trim($_POST['login']);
	                $login = special_chars($login);
	                $login = escape_string($login);
	                $password = escape_string($_POST['password']);
	                $pass_conf = escape_string($_POST['pass_conf']);
	                $email = escape_string($_POST['email']);
	                $sexe = $_POST['sexe'];
									if ($sexe == "M") $photo_user = "man.jpg";
									else if ($sexe == "F") $photo_user = "woman.jpg";
									
    							$select_user_login = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
    							$select_app_login = $connect->query($connect,"select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
 									if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_login) == 0) {
										if (mail_valide($email)) {
	                  	if ($password == $pass_conf) {
	                    	if (strlen($password) >= 5) {
	                    		$rndm = fonc_rand(8);
	                    		$rndm1 = substr($rndm,0,4);
	                    		$rndm2 = substr($rndm,4,4);
	                    		$crypt = md5($rndm2.$password.$rndm1);
	                    		$mdp = $crypt.$rndm;
	                				$insertadmin = "INSERT INTO `" . $tblprefix . "users` VALUES (1,'Super Administrator','".$login."','".$mdp."','".$email."','1','3','".$photo_user."','".$sexe."',".time().",0,'0','".$language."','-','-',0,0,0,0);";
	                    		$connect->query($insertadmin);
	                    		echo "<h4><img src=\"../images/icones/tips.png\" />operation_ok<br />identifiant : ".$login."</h4>";
	                    		echo "<p><form name=\"form4\" method=\"POST\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"etape\" value=\"5\"><input type=\"submit\" class=\"button\" value=\"install_step5\"></form></p>";
	                    	} else goback("pass_court",2,"error",1);
	                  	} else goback("confirm_pass_err",2,"error",1);
	                  } else goback("format_mail_err",2,"error",1);
	                } else goback("login_existe",2,"error",1);
	              } else goback("tous_champs",2,"error",1);
	} break;
	
	//*****************************************************************************
		
	case "5" : {
      echo "<br /><br /><br /><center><h3><img src=\"../images/icones/tips.png\" />install_done</h3>";
			echo "<br /><br /><h2><font color=\"red\">del_folderinstall</font></h2>";
			echo "<br /><br /><h4><a href=\"../?\">link_done</a></h4></center>";
	} break;
	
	//*****************************************************************************

	default : {
				echo "<h4></h4>";
				echo " <b><font color=\"green\">" . $host . "</font></b><br />";
				echo " <b><font color=\"green\">" . $dbname . "</font></b><br />";
				echo " <b><font color=\"green\">" . $user . "</font></b><br />";
				echo " <b><font color=\"green\">" . $passwd . "</font></b><br />";
				echo " <b><font color=\"green\">" . $tblprefix . "</font></b><br />";
		
		if (!$connect){
			echo "<h4><img src=\"../images/icones/error.png\" /><font color=\"red\"></font></h4>";
		}
		else if (!$db){
				echo "<h4><img src=\"../images/icones/warning.png\" /><font color=\"red\">bd_introuvable</font></h4>";
				echo "<h4></h4>";
		}
		else {
				echo "<h4><img src=\"../images/icones/tips.png\" /></h4>";
		}
				echo "<p><form name=\"form\" method=\"POST\"><input type=\"hidden\" name=\"etape\" value=\"1\"><input type=\"hidden\" name=\"pass\" value=\"".$pass."\"><input type=\"hidden\" name=\"lang\" value=\"".$language."\"><input type=\"submit\" class=\"button\" value=\" in\"></form></p>";
	}
 }
}
else {
	goback("installmdp_incorrect",2,"error",1);
}



	echo "</body></html>";

} else echo "<h3><img src=\"../images/icones/error.png\" /><font color=\"red\">Admin folder not found : ".$adminfolder."</font></h3>";
?>