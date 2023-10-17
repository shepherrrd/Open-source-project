<?php
/*
 * 	Manhali - Free Learning Management System
 *	vertical_menu.php
 *	2011-11-08 11:43
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

	echo "<div id=\"titre\">".gestion_vertical_menu."</div>";
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/radio_div.js\"></script>";

	$select_statut_comp = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'vertical_menu';");
	if (mysql_num_rows($select_statut_comp) == 1) {
 		$statut_comp = mysql_result($select_statut_comp,0);
		if ($statut_comp == 0)
		 echo "<h3><img src=\"../images/icones/warning.png\" /><font color=\"red\">".component_disabled." ".enable_it_now." : </font><a href=\"?inc=components\"\">".gestion_composants."</a></h3>";
	}
	
	if (isset($_GET['id_menu']) && ctype_digit($_GET['id_menu']))
		$id_menu = intval($_GET['id_menu']);
	else $id_menu = 0;

	if (isset($_GET['id_article']) && ctype_digit($_GET['id_article']))
		$id_article = intval($_GET['id_article']);
	else $id_article = 0;

	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){

	// ****************** add_menu **************************
		case "add_menu" : {
			if (isset($_POST['menu_titre']) && isset($_POST['menu_contenu'])){
    	 $menu_titre = trim($_POST['menu_titre']);
    	 if (!empty($menu_titre) && !empty($_POST['menu_contenu'])){
    		$menu_titre = escape_string($menu_titre);
    		
    		$select_menu_titre = mysql_query("select id_vermenu from `" . $tblprefix . "vermenu` where titre_vermenu = '$menu_titre';");
    		if (mysql_num_rows($select_menu_titre) == 0){
    		
    			$select_max_order = mysql_query("select max(ordre_vermenu) from `" . $tblprefix . "vermenu`;");
 					if (mysql_num_rows($select_max_order) == 1)
 						$ordre_menu = mysql_result($select_max_order,0) + 1;
 					else $ordre_menu = 1;
    		
    			if ($_POST['menu_contenu'] == "article"){

    				$insert_menu = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (NULL,'$menu_titre','article','','1',$ordre_menu);";
						mysql_query($insert_menu,$connect);
    				$this_menu_insert = mysql_insert_id();
    				
    				if (isset($_POST['articles']) && !empty($_POST['articles']))
				  	$array_art = $_POST['articles'];
				  	else $array_art = array();

    				$select_article = mysql_query("select id_article from `" . $tblprefix . "articles`;");
    				if (mysql_num_rows($select_article) > 0){
    					$order_i = 1;
    					while($article = mysql_fetch_row($select_article)){
    						$id_article = $article[0];
    						if (in_array($id_article,$array_art)){
									$update_article = mysql_query("update `" . $tblprefix . "articles` set id_menu_ver = $this_menu_insert, ordre_article_ver = $order_i where id_article = $id_article;");
    							$order_i++;
    						}
    					}
    				}
    				redirection(element_ajoute,"?inc=vertical_menu",3,"tips",1);
    			}
    			else if ($_POST['menu_contenu'] == "lien"){
    				$lien = trim($_POST['lien']);
    				if (!empty($lien)) {
    					$lien = escape_string($lien);
    					$insert_menu = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (NULL,'$menu_titre','url','$lien','1',$ordre_menu);";
							mysql_query($insert_menu,$connect);
							redirection(element_ajoute,"?inc=vertical_menu",3,"tips",1);
    				} else goback(remplir_lien,2,"error",1);
    			}
    			else if ($_POST['menu_contenu'] == "module"){
    				if (!empty($_POST['modules'])) {
    					$modules = escape_string($_POST['modules']);
    					if ($modules != "." && $modules != ".." && substr($modules,-4,1) != "." && substr($modules,-5,1) != ".") {
    						$insert_menu = "INSERT INTO `" . $tblprefix . "vermenu` VALUES (NULL,'$menu_titre','module','$modules','1',$ordre_menu);";
								mysql_query($insert_menu,$connect);
								redirection(element_ajoute,"?inc=vertical_menu",3,"tips",1);
							} else goback(contenu_menu_invalide,2,"error",1);
    				} else goback(remplir_module,2,"error",1);
    			} else goback(contenu_menu_invalide,2,"error",1);
				} else goback(titre_existe,2,"error",1);
    	 } else goback(titre_vide,2,"error",1);
    	}
    	else {
    		goback_button();
    		echo "\n<form method=\"POST\" action=\"\">";
    		echo "\n<p><b><font color=\"red\">*</font> <u>" .titre_element_menu. " :</u> </b><br /><br /><input name=\"menu_titre\" type=\"text\" maxlength=\"100\" size=\"50\" value=\"\"></p>";
				echo "\n<p><b><font color=\"red\">*</font> <u>".contenu_element_menu." :</u> </b></p>";

    		$select_article = mysql_query("select id_article, titre_article, publie_article from `" . $tblprefix . "articles`;");
    		if (mysql_num_rows($select_article) > 0){
    			echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"article\" checked=\"checked\" onclick=\"DisplayHide('contenu_menu', 'contenu1')\"> " .article. " : </b>";
    			echo "<div style=\"display: block; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu1\">";
    			echo "<select size=\"10\" name=\"articles[]\" multiple=\"multiple\">";
    			while($article = mysql_fetch_row($select_article)){
    				$id_article = $article[0];
    				$titre_article = html_ent($article[1]);
    				if ($article[2] == 1)
    					$publie_article = publie;
    				else $publie_article = depublie;
    				echo "\n<option value=\"".$id_article."\">".$titre_article." (".$publie_article.")</option>";
    			}
    			echo "\n</select><br />".hold_down_ctrl;
    			echo "</div><br /><br />";
    		}
    		
    		echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"lien\" onclick=\"DisplayHide('contenu_menu', 'contenu2')\"> " .lien. " : </b>";
    		echo "<div style=\"display: none; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu2\">";
    		echo "<br />- ".remarque_lien_interne." :<br /><ul>";
    		echo "<li><u>".tutoriels2."</u> : <b>?tutorial=1</b></li>";
    		echo "<li><u>".contact_us."</u> : <b>?contact</b></li>";
    		echo "<li><u>".document_sharing."</u> : <b>?documents</b></li>";
    		echo "<li><u>".poll."</u> : <b>?poll</b></li>";
    		echo "<li><u>".search."</u> : <b>?search</b></li>";
    		echo "</ul>";
    		echo "<br />- ".remarque_lien_externe." : http://www.manhali.com<br />";
    		echo "<br /><input name=\"lien\" type=\"text\" maxlength=\"200\" size=\"46\" value=\"?\">";
    		echo "</div><br /><br />";

    		if($dir = opendir("../modules")){
    			echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"module\" onclick=\"DisplayHide('contenu_menu', 'contenu3')\"> " .module. " : </b>";
    			echo "<div style=\"display: none; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu3\">";
    			echo "<br />".remarque_module."<br />";
    			while($file = readdir($dir)) {
						if ($file != "." && $file != ".." && substr($file,-4,1) != "." && substr($file,-5,1) != ".") {
							echo "\n<br /><b><input name=\"modules\" type=\"radio\" value=\"".$file."\"> " .$file. "</b>";
						}
					}
    			echo "</div><br /><br />";
    			closedir($dir);
    		}
    		
    		echo "\n<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    	}
		} break;

	// ****************** edit_menu **************************
		case "edit_menu" : {
			
		 $select_menu = mysql_query("select * from `" . $tblprefix . "vermenu` where id_vermenu = $id_menu;");
		 if (mysql_num_rows($select_menu) == 1) {
		 	$menu = mysql_fetch_row($select_menu);
		 	$titre_vermenu = html_ent($menu[1]);
		 	$lien_vermenu = html_ent($menu[3]);
		 	$type_vermenu = $menu[2];

		  function check_menu($type,$notre_menu,$value){
		  	if($value == 1){
					if ($type == $notre_menu)
						return " checked=\"checked\"";
				}
				else {
					if ($type == $notre_menu)
						return "block";
					else return "none";
				}
			}
		 
		 	if (isset($_POST['menu_titre']) && isset($_POST['menu_contenu'])){
    		$menu_titre = trim($_POST['menu_titre']);
    	 	if (!empty($menu_titre) && !empty($_POST['menu_contenu'])){
    			$menu_titre = escape_string($menu_titre);
    		
    			if ($_POST['menu_contenu'] == "article"){

    				$update_menu = "update `" . $tblprefix . "vermenu` set titre_vermenu = '$menu_titre', type_vermenu = 'article' where id_vermenu = $id_menu;";
						mysql_query($update_menu,$connect);
    				
   				  if (isset($_POST['articles']) && !empty($_POST['articles']))
					  	$array_art = $_POST['articles'];
				  	else $array_art = array();
						
						$tab_order = array();
						$select_order_tab = mysql_query("select ordre_article_ver from `" . $tblprefix . "articles` where id_menu_ver = $id_menu;");
 						if (mysql_num_rows($select_order_tab) > 0){
 							while ($re_tab_order = mysql_fetch_row($select_order_tab))
 								$tab_order[] = $re_tab_order[0];
 						}
 						$order_new_article = 1;
 						
    				$select_article = mysql_query("select id_article, id_menu_ver, ordre_article_ver from `" . $tblprefix . "articles`;");
    				if (mysql_num_rows($select_article) > 0){
    					while($article = mysql_fetch_row($select_article)){
    						$id_article = $article[0];
    						$idmenu = $article[1];
    						$ordre_article = $article[2];
    						if (in_array($id_article,$array_art)) {
    							if ($idmenu != $id_menu){
    								while (in_array($order_new_article,$tab_order))
    									$order_new_article++;
    								$update_article = mysql_query("update `" . $tblprefix . "articles` set id_menu_ver = $id_menu, ordre_article_ver = $order_new_article where id_article = $id_article;");
    								$order_new_article++;
    							}
    						} else {
    							if ($idmenu == $id_menu)
    								$update_article = mysql_query("update `" . $tblprefix . "articles` set id_menu_ver = 0, ordre_article_ver = 1 where id_article = $id_article;");
    						}
    					}
    				}
    				redirection(element_modifie,"?inc=vertical_menu",3,"tips",1);
    			}
    			else if ($_POST['menu_contenu'] == "lien"){
    				$lien = trim($_POST['lien']);
    				if (!empty($lien)) {
    					$lien = escape_string($lien);
							$update_menu = "update `" . $tblprefix . "vermenu` set titre_vermenu = '$menu_titre', type_vermenu = 'url', lien_vermenu = '$lien' where id_vermenu = $id_menu;";
							mysql_query($update_menu,$connect);
							redirection(element_modifie,"?inc=vertical_menu",3,"tips",1);
    				} else goback(remplir_lien,2,"error",1);
    			}
    			else if ($_POST['menu_contenu'] == "module"){
    				if (!empty($_POST['modules'])) {
    					$modules = escape_string($_POST['modules']);
    					if ($modules != "." && $modules != ".." && substr($modules,-4,1) != "." && substr($modules,-5,1) != ".") {
								$update_menu = "update `" . $tblprefix . "vermenu` set titre_vermenu = '$menu_titre', type_vermenu = 'module', lien_vermenu = '$modules' where id_vermenu = $id_menu;";
								mysql_query($update_menu,$connect);
								redirection(element_modifie,"?inc=vertical_menu",3,"tips",1);
							} else goback(contenu_menu_invalide,2,"error",1);
    				} else goback(remplir_module,2,"error",1);
    			} else goback(contenu_menu_invalide,2,"error",1);
    		} else goback(titre_vide,2,"error",1);
    	}
    	else {
    		goback_button();
    		echo "\n<form method=\"POST\" action=\"\">";
    		echo "\n<p><b><font color=\"red\">*</font> <u>" .titre_element_menu. " :</u> </b><br /><br /><input name=\"menu_titre\" type=\"text\" maxlength=\"100\" size=\"50\" value=\"".$titre_vermenu."\"></p>";
				echo "\n<p><b><font color=\"red\">*</font> <u>".contenu_element_menu." :</u> </b></p>";

    		$select_article = mysql_query("select id_article, id_menu_ver, titre_article, publie_article from `" . $tblprefix . "articles`;");
    		if (mysql_num_rows($select_article) > 0){
    			echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"article\"".check_menu('article',$type_vermenu,1)." onclick=\"DisplayHide('contenu_menu', 'contenu1')\"> " .article. " : </b>";
    			echo "\n<div style=\"display: ".check_menu('article',$type_vermenu,0)."; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu1\">";
    			echo "<select size=\"10\" name=\"articles[]\" multiple=\"multiple\">";
    			while($article = mysql_fetch_row($select_article)){
    				$id_article = $article[0];
    				$id_menu_article = $article[1];
    				$titre_article = html_ent($article[2]);
    				if ($article[3] == 1)
    					$publie_article = publie;
    				else $publie_article = depublie;
    				echo "\n<option value=\"".$id_article."\"";
    				if ($type_vermenu == "article" && $id_menu_article == $id_menu)
    					echo " selected=\"selected\"";
    				echo ">".$titre_article." (".$publie_article.")</option>";
    			}
    			echo "\n</select><br />".hold_down_ctrl;
    			echo "</div><br /><br />";
    		}
    		
    		echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"lien\"".check_menu('url',$type_vermenu,1)." onclick=\"DisplayHide('contenu_menu', 'contenu2')\"> " .lien. " : </b>";
    		echo "\n<div style=\"display: ".check_menu('url',$type_vermenu,0)."; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu2\">";
    		echo "<br />- ".remarque_lien_interne." :<br /><ul>";
    		echo "<li><u>".tutoriels2."</u> : <b>?tutorial=1</b></li>";
    		echo "<li><u>".contact_us."</u> : <b>?contact</b></li>";
    		echo "<li><u>".document_sharing."</u> : <b>?documents</b></li>";
    		echo "<li><u>".poll."</u> : <b>?poll</b></li>";
    		echo "<li><u>".search."</u> : <b>?search</b></li>";
    		echo "</ul>";
    		echo "\n<br />- ".remarque_lien_externe." : http://www.manhali.com<br />";
    		echo "\n<br /><input name=\"lien\" type=\"text\" maxlength=\"200\" size=\"46\" value=\"";
    		if ($type_vermenu == "url")
    			echo $lien_vermenu;
    		echo "\">";
    		echo "</div><br /><br />";

    		if($dir = opendir("../modules")){
    			echo "\n<b><input name=\"menu_contenu\" type=\"radio\" value=\"module\"".check_menu('module',$type_vermenu,1)." onclick=\"DisplayHide('contenu_menu', 'contenu3')\"> " .module. " : </b>";
    			echo "\n<div style=\"display: ".check_menu('module',$type_vermenu,0)."; margin-left: 20px;\" class=\"contenu_menu\" id=\"contenu3\">";
    			echo "\n<br />".remarque_module."<br />";
    			while($file = readdir($dir)) {
						if ($file != "." && $file != ".." && substr($file,-4,1) != "." && substr($file,-5,1) != ".") {
							echo "\n<br /><b><input name=\"modules\" type=\"radio\" value=\"".$file."\"";
							if ($type_vermenu == "module" &&  $file == $lien_vermenu)
    						echo " checked=\"checked\"";
							echo "> " .$file. "</b>";
						}
					}
    			echo "</div><br /><br />";
    			closedir($dir);
    		}
    		
    		echo "\n<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    	}
     } else locationhref_admin("?inc=vertical_menu");
		} break;
		
	// ****************** delete_menu **************************
		case "delete_menu" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$delete_menu = mysql_query("delete from `" . $tblprefix . "vermenu` where id_vermenu = $id_menu;");
			}
			locationhref_admin("?inc=vertical_menu");
		} break;

	// ****************** orderup_menu **************************
		case "orderup_menu" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_menu = mysql_query ("select ordre_vermenu from `" . $tblprefix . "vermenu` where id_vermenu = $id_menu;");
				if (mysql_num_rows($ce_menu) == 1) {
					$ordre_menu = mysql_result($ce_menu,0,0);

    			$menu_precedent = mysql_query ("select id_vermenu, ordre_vermenu from `" . $tblprefix . "vermenu` where ordre_vermenu < $ordre_menu order by ordre_vermenu desc;");
					if (mysql_num_rows($menu_precedent) > 0) {
						$idmenu_precedent = mysql_result($menu_precedent,0,0);
						$ordremenu_precedent = mysql_result($menu_precedent,0,1);

						$order_this_menu = mysql_query("update `" . $tblprefix . "vermenu` set ordre_vermenu = $ordremenu_precedent where id_vermenu = $id_menu;");
						$order_menu_precedent = mysql_query("update `" . $tblprefix . "vermenu` set ordre_vermenu = $ordre_menu where id_vermenu = $idmenu_precedent;");
					}
    		}
			}
			locationhref_admin("?inc=vertical_menu");
		} break;
		
	// ****************** orderdown_menu **************************
		case "orderdown_menu" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_menu = mysql_query ("select ordre_vermenu from `" . $tblprefix . "vermenu` where id_vermenu = $id_menu;");
				if (mysql_num_rows($ce_menu) == 1) {
					$ordre_menu = mysql_result($ce_menu,0,0);

    			$menu_suivant = mysql_query ("select id_vermenu, ordre_vermenu from `" . $tblprefix . "vermenu` where ordre_vermenu > $ordre_menu order by ordre_vermenu;");
					if (mysql_num_rows($menu_suivant) > 0) {
						$idmenu_suivant = mysql_result($menu_suivant,0,0);
						$ordremenu_suivant = mysql_result($menu_suivant,0,1);

						$order_this_menu = mysql_query("update `" . $tblprefix . "vermenu` set ordre_vermenu = $ordremenu_suivant where id_vermenu = $id_menu;");
						$order_menu_suivant = mysql_query("update `" . $tblprefix . "vermenu` set ordre_vermenu = $ordre_menu where id_vermenu = $idmenu_suivant;");
					}
    		}
			}
			locationhref_admin("?inc=vertical_menu");
		} break;

	// ****************** orderup_article **************************
		case "orderup_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cet_article = mysql_query ("select id_menu_ver, ordre_article_ver from `" . $tblprefix . "articles` where id_article = $id_article;");
				if (mysql_num_rows($cet_article) == 1) {
					$id_menu = mysql_result($cet_article,0,0);
					$ordre_article = mysql_result($cet_article,0,1);

    			$article_precedent = mysql_query ("select id_article, ordre_article_ver from `" . $tblprefix . "articles` where id_menu_ver = $id_menu and ordre_article_ver < $ordre_article order by ordre_article_ver desc;");
					if (mysql_num_rows($article_precedent) > 0) {
						$idarticle_precedent = mysql_result($article_precedent,0,0);
						$ordrearticle_precedent = mysql_result($article_precedent,0,1);

						$order_this_article = mysql_query("update `" . $tblprefix . "articles` set ordre_article_ver = $ordrearticle_precedent where id_article = $id_article;");
						$order_article_precedent = mysql_query("update `" . $tblprefix . "articles` set ordre_article_ver = $ordre_article where id_article = $idarticle_precedent;");
					}
					locationhref_admin("?inc=vertical_menu");
    		} else locationhref_admin("?inc=vertical_menu");
			} else locationhref_admin("?inc=vertical_menu");
		} break;

	// ****************** orderdown_article **************************
		case "orderdown_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cet_article = mysql_query ("select id_menu_ver, ordre_article_ver from `" . $tblprefix . "articles` where id_article = $id_article;");
				if (mysql_num_rows($cet_article) == 1) {
					$id_menu = mysql_result($cet_article,0,0);
					$ordre_article = mysql_result($cet_article,0,1);

    			$article_suivant = mysql_query ("select id_article, ordre_article_ver from `" . $tblprefix . "articles` where id_menu_ver = $id_menu and ordre_article_ver > $ordre_article order by ordre_article_ver;");
					if (mysql_num_rows($article_suivant) > 0) {
						$idarticle_suivant = mysql_result($article_suivant,0,0);
						$ordrearticle_suivant = mysql_result($article_suivant,0,1);

						$order_this_article = mysql_query("update `" . $tblprefix . "articles` set ordre_article_ver = $ordrearticle_suivant where id_article = $id_article;");
						$order_article_suivant = mysql_query("update `" . $tblprefix . "articles` set ordre_article_ver = $ordre_article where id_article = $idarticle_suivant;");
					}
					locationhref_admin("?inc=vertical_menu");
    		} else locationhref_admin("?inc=vertical_menu");
			} else locationhref_admin("?inc=vertical_menu");
		} break;

	// ****************** activer_menu **************************
		case "activer_menu" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$activer_menu = mysql_query("update `" . $tblprefix . "vermenu` set active_vermenu = '1' where id_vermenu = $id_menu;");
			}
			locationhref_admin("?inc=vertical_menu");
		} break;
		
	// ****************** desactiver_menu **************************
		case "desactiver_menu" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$desactiver_menu = mysql_query("update `" . $tblprefix . "vermenu` set active_vermenu = '0' where id_vermenu = $id_menu;");
			}
			locationhref_admin("?inc=vertical_menu");
		} break;

   	// ****************** liste_menu **************************
		default : {

			echo "<table border=\"0\"><tr><td><a href=\"?inc=vertical_menu&do=add_menu\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=vertical_menu&do=add_menu\"><b>".ajouter_menu."</b></a></td></tr></table><br />";

			confirmer();
			$max_len = 70;
			$max_len2 = 30;

			if (isset($_GET['l']) && ctype_digit($_GET['l']))
				$page = intval($_GET['l']);
			else $page = 1;

			$select_menu = mysql_query("select * from `" . $tblprefix . "vermenu` order by ordre_vermenu;");
			$nbr_trouve = mysql_num_rows($select_menu);
  		if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page <= $page_max && $page > 1 && $page_max > 1)
					$limit = ($page - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page = 1;
				}
	
				$select_menu_limit = mysql_query("select * from `" . $tblprefix . "vermenu` order by ordre_vermenu limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".elements_menu."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".contenu_element_menu."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>\n";
				
				$i_ordre = ($page - 1) * $nbr_resultats + 1;
				while($menu = mysql_fetch_row($select_menu_limit)){
					
					$id_menu = $menu[0];
					$titre_menu = html_ent($menu[1]);
					$titre_menu = readmore($titre_menu,$max_len);
					
					$type_menu = $menu[2];
		 			$lien_menu = html_ent($menu[3]);
		 		
					$active_menu = $menu[4];
					$ordre_menu = $menu[5];
					
					if ($active_menu == 1)
						$color = "green";
					else $color = "red";
					
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$titre_menu."</b></font></td>";
					echo "\n<td class=\"affichage_table\">";
				
				if ($type_menu == "article"){
		 			$select_articles = mysql_query("select id_article, titre_article, contenu_article, ordre_article_ver from `" . $tblprefix . "articles` where id_menu_ver = $id_menu order by ordre_article_ver;");
		 			if (mysql_num_rows($select_articles) > 0){
		 				echo "<table width=\"100%\" align=\"center\" border=\"1\">";
						$j_ordre = 1;
						while($article = mysql_fetch_row($select_articles)){
							$id_article = $article[0];
							$titre_article = html_ent(trim($article[1]));
							$contenu_article = html_ent($article[2]);
							if (empty($titre_article))
								$titre_article = $contenu_article;
							$titre_article = readmore($titre_article,$max_len2);
							$ordre_article = $article[3];
							echo "<tr>\n";
							echo "\n<td><b>".$titre_article."</b></td>";
							echo "\n<td nowrap=\"nowrap\">";
							$article_precedent = mysql_query ("select id_article from `" . $tblprefix . "articles` where ordre_article_ver < $ordre_article and id_menu_ver = $id_menu order by ordre_article_ver desc;");
							if (mysql_num_rows($article_precedent) > 0)
								echo "<a href=\"?inc=vertical_menu&do=orderup_article&id_article=".$id_article."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
							else echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
							echo "<b> ".$j_ordre." </b>";
							$j_ordre++;
							$article_suivant = mysql_query ("select id_article from `" . $tblprefix . "articles` where ordre_article_ver > $ordre_article and id_menu_ver = $id_menu order by ordre_article_ver;");
							if (mysql_num_rows($article_suivant) > 0)
								echo "<a href=\"?inc=vertical_menu&do=orderdown_article&id_article=".$id_article."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
							else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
							echo "</td>";
							echo "</tr>\n";
						}
						echo "\n</table>";
		 			} else echo aucun_article_menu;
		 		}
		 		else if ($type_menu == "module"){
		 			echo "<u>".module." :</u> ".$lien_menu;
		 			if (!file_exists("../modules/".$lien_menu))
		 				echo "<br /><font color=\"red\">".module_introuvable." modules/".$lien_menu."</font>";
		 		}
		 		else {
		 			echo "<u>".lien." :</u> ".$lien_menu;
		 		}
		 		
					echo "</td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=vertical_menu&do=edit_menu&id_menu=".$id_menu."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=vertical_menu&do=delete_menu&id_menu=".$id_menu."&key=".$key."','".confirm_supprimer_menu."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$menu_precedent = mysql_query ("select id_vermenu from `" . $tblprefix . "vermenu` where ordre_vermenu < $ordre_menu order by ordre_vermenu desc;");
					if (mysql_num_rows($menu_precedent) > 0)
						echo "<a href=\"?inc=vertical_menu&do=orderup_menu&id_menu=".$id_menu."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$i_ordre." </b>";
					$i_ordre++;
					$menu_suivant = mysql_query ("select id_vermenu from `" . $tblprefix . "vermenu` where ordre_vermenu > $ordre_menu order by ordre_vermenu;");
					if (mysql_num_rows($menu_suivant) > 0)
						echo "<a href=\"?inc=vertical_menu&do=orderdown_menu&id_menu=".$id_menu."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";
							
					if ($active_menu == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=vertical_menu&do=desactiver_menu&id_menu=".$id_menu."&key=".$key."\"><b>".desactiver."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=vertical_menu&do=activer_menu&id_menu=".$id_menu."&key=".$key."\"><b>".activer."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=vertical_menu&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=vertical_menu&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=vertical_menu&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=vertical_menu&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=vertical_menu&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_element_menu."<br /><br />";
		}
	}
} else echo restricted_access;

?>