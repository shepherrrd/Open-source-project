<?php
/*
 * 	Manhali - Free Learning Management System
 *	site_config.php
 *	2009-05-08 14:06
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")){

 echo "<div id=\"titre\">".configuration_generale."</div><br />";
	
 $select_site_infos = mysql_query("select * from `" . $tblprefix . "site_infos`;");

 if (mysql_num_rows($select_site_infos) == 1) {
	$site_infos = mysql_fetch_row($select_site_infos);

	$id_site = $site_infos[0];
	$nom_site = html_ent($site_infos[1]);
	$titre_site = html_ent($site_infos[2]);
	$url_site = html_ent($site_infos[3]);
	$description_site = html_ent($site_infos[4]);
	$keywords_site = html_ent($site_infos[5]);
	$langue_site = $site_infos[6];
	$footer_site = html_ent($site_infos[7]);
	$inscription = $site_infos[9];
	$activation = $site_infos[10];
	$classe = $site_infos[11];
	$autoriser_edit_classe = $site_infos[12];
	$afficher_profil = $site_infos[13];
	$elements_page = $site_infos[14];
	$caracteres = $site_infos[15];

  function checked_checked($var_bool,$value){
		if ($var_bool == $value)
			return " checked=\"checked\" ";
		else
			return " ";
	}
			
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){

	// ****************** website **************************
	 case "website" : {

   	echo "<table border=\"0\" width=\"80%\" align=\"center\"><tr>";
   	echo "<td align=\"center\" width=\"33%\"><a href=\"?inc=site_config\"><b>".system_conf."</b></a></td>";
   	echo "<td align=\"center\" width=\"34%\"><a href=\"?inc=site_config&do=registration\"><b>".registration."</b></a></td>";
   	echo "<td align=\"center\" width=\"33%\"><b>".website."</b></td>";
   	echo "</tr></table><hr />";

		if (!empty($_POST['send'])){
			$nom = escape_string($_POST['nom']);
			$titre = escape_string($_POST['titre']);
			$url = escape_string($_POST['url']);
			$description = escape_string($_POST['description']);
			$keywords = escape_string($_POST['keywords']);
			$footer = escape_string($_POST['footer']);
			
			if (substr($url,0,7) != "http://" && substr($url,0,8) != "https://")
				$url = "http://".$url;

				$update_site = mysql_query("update `" . $tblprefix . "site_infos` SET nom_site = '$nom', titre_site = '$titre', url_site = '$url', description_site = '$description', keywords_site = '$keywords', footer_site = '$footer' where id_site = $id_site;");
 				redirection(infossite_modifie,"?inc=site_config&do=website",3,"tips",1);
		}
		else {
			echo "\n<form method=\"POST\" action=\"\">";
    	echo "\n<p><b>" .nom_site. " : </b><br /><input name=\"nom\" type=\"text\" maxlength=\"30\" size=\"50\" value=\"".$nom_site."\"></p>";
    	echo "\n<p><b>" .titre_site. " : </b><br /><input name=\"titre\" type=\"text\" maxlength=\"100\" size=\"50\" value=\"".$titre_site."\"></p>";
    	echo "\n<p><b>" .url_site. " : </b><br /><input name=\"url\" type=\"text\" maxlength=\"100\" size=\"50\" value=\"".$url_site."\"></p>";
    	echo "\n<p><b>" .description_site. " : </b><br /><textarea name=\"description\" id=\"description\" rows=\"5\" cols=\"63\">".$description_site."</textarea></p>";
    	echo "\n<p><b>" .keywords_site. " : </b><br />".remarque_keywords."<br /><textarea name=\"keywords\" id=\"keywords\" rows=\"5\" cols=\"63\">".$keywords_site."</textarea></p>";
    	echo "\n<p><b>" .footer_site. " : </b><br /><textarea name=\"footer\" id=\"footer\" rows=\"5\" cols=\"63\">".$footer_site."</textarea></p>";
    	echo "\n<p><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   	}
	 } break;
	// ****************** registration **************************
	 case "registration" : {

   		echo "<table border=\"0\" width=\"80%\" align=\"center\"><tr>";
   		echo "<td align=\"center\" width=\"33%\"><a href=\"?inc=site_config\"><b>".system_conf."</b></a></td>";
   		echo "<td align=\"center\" width=\"34%\"><b>".registration."</b></td>";
   		echo "<td align=\"center\" width=\"33%\"><a href=\"?inc=site_config&do=website\"><b>".website."</b></a></td>";
   		echo "</tr></table><hr />";

			if ($inscription == 1) $insc = 1;
			else $insc = 0;
			if ($activation == 1) $activ = 1;
			else $activ = 0;
			if ($classe == 1) $clas = 1;
			else $clas = 0;
			if ($autoriser_edit_classe == 1) $edit_clas = 1;
			else $edit_clas = 0;
					
			if (!empty($_POST['send'])){
				if (isset($_POST['autoriser']) && ($_POST['autoriser'] == 1 || $_POST['autoriser'] == 0) && $_POST['autoriser'] != $insc){
					$post_insc = $_POST['autoriser'];
					mysql_query ("update `" . $tblprefix . "site_infos` SET inscription = '$post_insc' where id_site = $id_site;");
				}
				if (isset($_POST['activer']) && ($_POST['activer'] == 1 || $_POST['activer'] == 0) && $_POST['activer'] != $activ){
					$post_activ = $_POST['activer'];
					mysql_query ("update `" . $tblprefix . "site_infos` SET activation_apprenants = '$post_activ' where id_site = $id_site;");
				}
				if (isset($_POST['demander']) && ($_POST['demander'] == 1 || $_POST['demander'] == 0) && $_POST['demander'] != $clas){
					$post_clas = $_POST['demander'];
					mysql_query ("update `" . $tblprefix . "site_infos` SET demander_classe = '$post_clas' where id_site = $id_site;");
				}
				if (isset($_POST['modifier']) && ($_POST['modifier'] == 1 || $_POST['modifier'] == 0) && $_POST['modifier'] != $edit_clas){
					$post_edit_clas = $_POST['modifier'];
					mysql_query ("update `" . $tblprefix . "site_infos` SET autoriser_modification_classe = '$post_edit_clas' where id_site = $id_site;");
				}
				redirection(infos_modifies,"?inc=site_config&do=registration",3,"tips",1);
			}
			else {
				echo "<form method=\"POST\" action=\"\"><ul><table align=\"center\" width=\"100%\"><tr><td width=\"50%\">";
				echo "<li><b>".autoriser_inscription." :</b></td><td width=\"50%\">";
				echo "<input name=\"autoriser\" type=\"radio\"".checked_checked($insc,1)."value=\"1\">".oui;
				echo " <input name=\"autoriser\" type=\"radio\"".checked_checked($insc,0)."value=\"0\">".non."</td></tr><tr><td width=\"50%\">";
				echo "<li><b>".activation_compte." :</b></td><td width=\"50%\">";
				echo "<input name=\"activer\" type=\"radio\"".checked_checked($activ,1)."value=\"1\">".oui;
				echo " <input name=\"activer\" type=\"radio\"".checked_checked($activ,0)."value=\"0\">".non."</td></tr><tr><td width=\"50%\">";
				echo "<li><b>".demander_classe." :</b></td><td width=\"50%\">";
				echo "<input name=\"demander\" type=\"radio\"".checked_checked($clas,1)."value=\"1\">".oui;
				echo " <input name=\"demander\" type=\"radio\"".checked_checked($clas,0)."value=\"0\">".non."</td></tr><tr><td width=\"50%\">";
				echo "<li><b>".autoriser_modifier_classe." :</b></td><td width=\"50%\">";
				echo "<input name=\"modifier\" type=\"radio\"".checked_checked($edit_clas,1)."value=\"1\">".oui;
				echo " <input name=\"modifier\" type=\"radio\"".checked_checked($edit_clas,0)."value=\"0\">".non;
				echo "</td></tr><tr><td colspan=\"2\" align=\"center\" width=\"100%\"><input type=\"hidden\" name=\"send\" value=\"ok\"><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></td></tr></table></ul></form>";
				
				echo "<hr /><h3><u><a name=\"classe\">".classe."</a></u></h3>";
				
				// ************** edit_classes *****************
				if (isset($_GET['cat']) && $_GET['cat'] == "edit_classe"){
					if (isset($_GET['id_classe']) && ctype_digit($_GET['id_classe'])){
						$id_classe = $_GET['id_classe'];
						$select_classe_all = mysql_query("select * from `" . $tblprefix . "classes` where id_classe = $id_classe;");
    				if (mysql_num_rows($select_classe_all) == 1) {
    					$la_classe = mysql_result($select_classe_all,0,1);
    					
							if (isset($_POST['nom_classe'])){
								$nom_classe = trim($_POST['nom_classe']);
								if (!empty($nom_classe)){
									$nom_classe = escape_string($nom_classe);
									$select_classe = mysql_query("select id_classe from `" . $tblprefix . "classes` where classe = '$nom_classe';");
 									if ((mysql_num_rows($select_classe) == 0) || (mysql_num_rows($select_classe) == 1 && mysql_result($select_classe,0) == $id_classe)) {
 										mysql_query ("update `" . $tblprefix . "classes` SET classe = '$nom_classe' where id_classe = $id_classe;");
	          				redirection(classe_modifiee,"?inc=site_config&do=registration#classe",1,"tips",1);
 									} else goback(classe_existe,2,"error",1);
 								} else goback(remplir_champ,2,"error",1);
							}
							else{
								echo "<form method=\"POST\" action=\"\">";
								echo "<center><input name=\"nom_classe\" value=\"".$la_classe."\" type=\"text\" size=\"30\" maxlength=\"30\"> ";
								echo "<input type=\"submit\" class=\"button\" value=\"" .modifier_classe. "\"> <input type=\"button\" class=\"button\" value=\"".annuler."\" onclick=\"window.location.href='?inc=site_config&do=registration#classe'\" /></center></form><br />";
							}
						} else locationhref_admin("?inc=site_config&do=registration#classe");
					} else locationhref_admin("?inc=site_config&do=registration#classe");
				}
				
				// ************** delete_classes *****************
				else if (isset($_GET['cat']) && $_GET['cat'] == "delete_classe"){
					if (isset($_GET['key']) && $_GET['key'] == $key && isset($_GET['id_classe']) && ctype_digit($_GET['id_classe'])){
						$id_classe = $_GET['id_classe'];
						$delete_classe = mysql_query("delete from `" . $tblprefix . "classes` where id_classe = $id_classe;");
					}
					locationhref_admin("?inc=site_config&do=registration#classe");
				}
				
				// ************** ajouter classe *****************
				else{
					if (isset($_POST['nom_classe'])){
						$nom_classe = trim($_POST['nom_classe']);
						if (!empty($nom_classe)){
							$nom_classe = escape_string($nom_classe);
							$select_classe = mysql_query("select id_classe from `" . $tblprefix . "classes` where classe = '$nom_classe';");
 							if (mysql_num_rows($select_classe) == 0){
 								mysql_query ("INSERT INTO `" . $tblprefix . "classes` VALUES (NULL,'$nom_classe');");
	          		redirection(classe_ajoutee,"?inc=site_config&do=registration",1,"tips",1);
 							} else goback(classe_existe,2,"error",1);
 						} else goback(remplir_champ,2,"error",1);
					}
					else{
						echo "<form method=\"POST\" action=\"\">";
						echo "<center><input name=\"nom_classe\" type=\"text\" size=\"30\" maxlength=\"30\"> ";
						echo "<input type=\"submit\" class=\"button\" value=\"" .ajouter_classe. "\"></center></form><br />";
					}
				}
				
				// ************** liste classes *****************
				$select_classes = mysql_query("select * from `" . $tblprefix . "classes`;");
				confirmer();
				if (mysql_num_rows($select_classes) > 0){
					echo "<br /><table width=\"50%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
					echo "\n<td class=\"affichage_table\" width=\"60%\"><b>".classe."</b></td>";
					echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".editer."</b></td>";
					echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".supprimer."</b></td>";
					echo "</tr>";
					while($classe = mysql_fetch_row($select_classes)){
						$id_classe = $classe[0];
						$nom_classe = html_ent($classe[1]);
						echo "<tr>\n";
						echo "\n<td class=\"affichage_table\"><b>".$nom_classe."</b></font></td>";
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=site_config&do=registration&cat=edit_classe&id_classe=".$id_classe."#classe\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
						echo "\n<td class=\"affichage_table\"><a href=\"#classe\" onClick=\"confirmer('?inc=site_config&do=registration&cat=delete_classe&id_classe=".$id_classe."&key=".$key."','".confirm_supprimer_classe."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
						echo "</tr>\n";
					}
					echo "\n</table>";
				} else echo "<b>".aucune_classe."</b>";
			}
	 } break;

  // ****************** other **************************	
	 default : {
		
   	echo "<table border=\"0\" width=\"80%\" align=\"center\"><tr>";
   	echo "<td align=\"center\" width=\"33%\"><b>".system_conf."</b></td>";
 		echo "<td align=\"center\" width=\"34%\"><a href=\"?inc=site_config&do=registration\"><b>".registration."</b></a></td>";
  	echo "<td align=\"center\" width=\"33%\"><a href=\"?inc=site_config&do=website\"><b>".website."</b></a></td>";
   	echo "</tr></table><hr />";

		if ($afficher_profil == 1)
			$affich_profil = 1;
		else $affich_profil = 0;
		
		if (ctype_digit($elements_page) && $elements_page > 0)
			$nb_elements_page = $elements_page;
		else $nb_elements_page = 10;

		if (ctype_digit($caracteres) && $caracteres > 0)
			$nb_caracteres = $caracteres;
		else $nb_caracteres = 500;
		
		if (!empty($_POST['send'])){
    	$langue_home = escape_string($_POST['langue_home']);
			if (file_exists("../language/".$langue_home."/home.ini"))
				$update_langue = mysql_query("update `" . $tblprefix . "site_infos` SET langue_site = '$langue_home' where id_site = $id_site;");
			else goback(langue_introuvable,2,"error",1);
			
			if (isset($_POST['afficher_le_profil']) && ($_POST['afficher_le_profil'] == 1 || $_POST['afficher_le_profil'] == 0) && $_POST['afficher_le_profil'] != $affich_profil){
				$post_affich_profil = $_POST['afficher_le_profil'];
				mysql_query ("update `" . $tblprefix . "site_infos` SET afficher_profil_aux_visiteurs = '$post_affich_profil' where id_site = $id_site;");
			}
			if (isset($_POST['nb_d_elements_page']) && ctype_digit($_POST['nb_d_elements_page']) && $_POST['nb_d_elements_page'] > 0 && $_POST['nb_d_elements_page'] != $nb_elements_page){
				$nb_d_elements_page = escape_string($_POST['nb_d_elements_page']);
				mysql_query ("update `" . $tblprefix . "site_infos` SET nombre_elements_page = '$nb_d_elements_page' where id_site = $id_site;");
			}
			if (isset($_POST['nb_de_caracteres']) && ctype_digit($_POST['nb_de_caracteres']) && $_POST['nb_de_caracteres'] > 0 && $_POST['nb_de_caracteres'] != $nb_caracteres){
				$nb_de_caracteres = escape_string($_POST['nb_de_caracteres']);
				mysql_query ("update `" . $tblprefix . "site_infos` SET nombre_caracteres = '$nb_de_caracteres' where id_site = $id_site;");
			}
			redirection(infos_modifies,"?inc=site_config",3,"tips",1);
		}
		else {
			echo "\n<form method=\"POST\" action=\"\"><ul><table align=\"center\" width=\"100%\" cellpadding=\"10\">";
			if($dir = opendir("../language")){
    		echo "\n<tr><td width=\"50%\"><li><b>" .langue_site_unregistered. " : </b>".remarque_langue."</td><td width=\"50%\"><select name=\"langue_home\">";
    		while($lang = readdir($dir)) {
					if ($lang != ".." && $lang != "." && strtolower(substr($lang,0,5) != "index")) {
						if ($fd = @fopen("../language/".$lang."/home.ini","r")){
							while (!feof($fd)) {
								$line = fgets($fd);
  							if (strpos($line,"language=")===0 || strpos($line,"language="))
  								break;
  						}
  						@fclose($fd);
  						$line = substr($line,strpos($line,"=")+1);
						}
						else $line = "";
						if ($langue_site == $lang)
							echo "<option  value=\"".$lang."\" selected=\"selected\">".$line." (".$lang.")</option>";
						else echo "<option  value=\"".$lang."\">".$line." (".$lang.")</option>";
					}
				}
    		echo "</select></td></tr>";
    		closedir($dir);
    	}

			echo "<tr><td width=\"50%\"><li><b>".afficher_profil." :</b></td><td width=\"50%\">";
			echo "<input name=\"afficher_le_profil\" type=\"radio\"".checked_checked($affich_profil,1)."value=\"1\">".oui;
			echo " <input name=\"afficher_le_profil\" type=\"radio\"".checked_checked($affich_profil,0)."value=\"0\">".non."</td></tr>";
			
			echo "<tr><td width=\"50%\"><li><b>".nombre_elements_page." :</b></td><td width=\"50%\">";
			echo "<input name=\"nb_d_elements_page\" type=\"text\" maxlength=\"5\" size=\"5\" value=\"".$nb_elements_page."\"></td></tr>";
			
			echo "<tr><td width=\"50%\"><li><b>".nombre_caracteres_resume." :</b></td><td width=\"50%\">";
			echo "<input name=\"nb_de_caracteres\" type=\"text\" maxlength=\"5\" size=\"5\" value=\"".$nb_caracteres."\"></td></tr>";
			
			echo "<tr><td colspan=\"2\" align=\"center\" width=\"100%\"><input type=\"hidden\" name=\"send\" value=\"ok\"><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></td></tr></table></ul></form>";
		}
	 }
	}
 }
} else echo restricted_access;

?>