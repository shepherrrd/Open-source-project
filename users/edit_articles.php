<?php
/*
 * 	Manhali - Free Learning Management System
 *	edit_articles.php
 *	2009-05-14 15:19
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1")){

	echo "<div id=\"titre\">".gestion_articles."</div>";

	include_once ("ckeditor_init.php");
	
	if (isset($_GET['id_article']) && ctype_digit($_GET['id_article']))
		$id_article = intval($_GET['id_article']);
	else $id_article = 0;
	
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	switch ($do){
		

    // ****************** create_article **************************
    case "create_article" : {

    	if (isset($_POST['article_titre']) && isset($_POST['article_contenu'])){
    		$article_titre = trim($_POST['article_titre']);
    		$article_contenu = trim($_POST['article_contenu']);
    		if (!empty($article_titre) && !empty($article_contenu)){

    			$article_titre = escape_string($article_titre);
    			$article_contenu = escape_string($article_contenu);

    			if ($_POST['acces'] == "learner")
    				$article_acces = "0";
    			else if ($_POST['acces'] == "classe"){
    				if (!empty($_POST['classes']))
    					$article_acces = "-".implode("-",$_POST['classes'])."-";
    				else $article_acces = "0";
    			}
					else $article_acces = "*";
    						
    			$select_article_titre = $connect->query("select id_article from `" . $tblprefix . "articles` where titre_article = '$article_titre';");
 					if (mysqli_num_rows($select_article_titre) == 0) {
 						$select_max_orderaccueil = $connect->query("select max(ordre_accueil) from `" . $tblprefix . "articles`;");
 						if (mysqli_num_rows($select_max_orderaccueil) == 1)
 							$ordre_accueil = mysqli_result($select_max_orderaccueil,0) + 1;
 						else $ordre_accueil = 1;
						
						$time_insert_article = time();
 						$insertarticle = "INSERT INTO `" . $tblprefix . "articles` VALUES (NULL,$id_user_session,0,'$article_titre','$article_contenu','0','0',1,$ordre_accueil,0,$time_insert_article,$time_insert_article,'$article_acces',0,1);";
	          $connect->query($insertarticle,$connect);

	          redirection(article_cree,"?inc=edit_articles",3,"tips",1);
 					} else goback(titre_existe,2,"error",1);
    		} else goback(titre_contenu_vide,2,"error",1);
    	}
    	else {
    			goback_button();
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

    			echo "<form method=\"POST\" action=\"\">";
    			echo "<p><u><b><font color=\"red\">*</font> " .titre_article. "</b></u><br /><br /><input name=\"article_titre\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";							

					echo "<p><u><b><font color=\"red\">*</font> ".acces_article."</b></u><br /><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\" checked=\"checked\" /><b>".acces_ouvert."</b><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('classes',true)\" /><b>".acces_apprenants."</b><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\" /><b>".acces_classes." :</b>";
    			$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
			 		if (mysqli_num_rows($select_classes) > 0){
					 	echo "<table border=\"0\"><tr><td align=\"center\">";
						echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
						echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
 						while($classe = mysql_fetch_row($select_classes)){
    					$id_classe = $classe[0];
    					$nom_classe = html_ent($classe[1]);
    					echo "\n<option value=\"".$id_classe."\">".$nom_classe."</option>";
   					}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes',false)\" />";
						echo "<br />".hold_down_ctrl."</td></tr></table>";
			 		}
					else echo aucune_classe;

    			echo "<br /><p><u><b><font color=\"red\">*</font> " .contenu_article. "</b></u><br /><br /><textarea name=\"article_contenu\" cols=\"100\" rows=\"30\"></textarea></p>";
    			echo "<br /><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    			ckeditor_replace($language,"article_contenu");
    			echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
   		}
    } break;

    // ****************** edit_article **************************
    case "edit_article" : {
     	
    		$select_article_complet = $connect->query("select * from `" . $tblprefix . "articles` where id_article = $id_article;");
    		if (mysqli_num_rows($select_article_complet) == 1) {
    			$article = mysql_fetch_row($select_article_complet);
    			
    			$titre_article = html_ent($article[3]);
					$contenu_article = html_ent($article[4]);
					$acces_article = $article[12];
					
    				if (!empty($_POST['send'])){
    					$article_titre = trim($_POST['article_titre']);
    					$article_contenu = trim($_POST['article_contenu']);
    					if (!empty($article_titre) && !empty($article_contenu)){
    						$article_titre = escape_string($article_titre);
    						$article_contenu = escape_string($article_contenu);
    						
    						if ($_POST['acces'] == "learner")
    							$article_acces = "0";
    						else if ($_POST['acces'] == "classe"){
    							if (!empty($_POST['classes']))
    								$article_acces = "-".implode("-",$_POST['classes'])."-";
    							else $article_acces = "0";
    						}
    						else $article_acces = "*";

    						$select_article_titre = $connect->query("select id_article from `" . $tblprefix . "articles` where titre_article = '$article_titre';");
 								if ((mysqli_num_rows($select_article_titre) == 0) || (mysqli_num_rows($select_article_titre) == 1 && mysqli_result($select_article_titre,0) == $article[0])) {
 									$update_article = "update `" . $tblprefix . "articles` SET titre_article = '$article_titre', contenu_article = '$article_contenu', date_modification_article = ".time().", acces_article = '$article_acces' where id_article = $id_article;";
 									$connect->query($update_article);
 									redirection(article_modifie,"?inc=edit_articles",3,"tips",1);
 								} else goback(titre_existe,2,"error",1);
    					} else goback(titre_contenu_vide,2,"error",1);
    				}
    				else {
    					goback_button();
    					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";
    					
    					echo "<form method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> " .titre_article. "</b></u><br /><br /><input name=\"article_titre\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$titre_article."\"></p>";							
    					
							echo "<p><u><b><font color=\"red\">*</font> ".acces_article."</b></u><br /><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_article == "*")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_ouvert."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_article == "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_apprenants."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\"";
							if ($acces_article != "*" && $acces_article != "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_classes." :</b>";
							$tab_classes = explode("-",$acces_article);
    					$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
					 		if (mysqli_num_rows($select_classes) > 0){
					 			echo "<table border=\"0\"><tr><td align=\"center\">";
					 			echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
								echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
    						while($classe = mysql_fetch_row($select_classes)){
    							$id_classe = $classe[0];
    							$nom_classe = html_ent($classe[1]);
    							echo "\n<option value=\"".$id_classe."\"";
    							if (in_array($id_classe,$tab_classes))
    								echo " selected=\"selected\"";
    							echo ">".$nom_classe."</option>";
    						}
								echo "\n</select><br />";
								echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes',false)\" />";
								echo "<br />".hold_down_ctrl."</td></tr></table>";
					 		}
					 		else echo aucune_classe;
    					
    					echo "<br /><p><u><b><font color=\"red\">*</font> " .contenu_article. "</b></u><br /><br /><textarea name=\"article_contenu\" cols=\"100\" rows=\"30\">".$contenu_article."</textarea></p>";
    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
    					echo "<br /><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
							ckeditor_replace($language,"article_contenu");
    					if ($acces_article == "*" || $acces_article == "0")
    						echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
    				}
    		} else locationhref_admin("?inc=edit_articles");
    } break;

		// ****************** delete_article **************************
		case "delete_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$delete_article = $connect->query("delete from `" . $tblprefix . "articles` where id_article = $id_article;");
			}
			locationhref_admin("?inc=edit_articles");
		} break;

    // ****************** publier_article *************************
    case "publier_article" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$publier_article = $connect->query("update `" . $tblprefix . "articles` set publie_article = '1', id_validateur = $id_user_session where id_article = $id_article;");
    	}
    	locationhref_admin("?inc=edit_articles");
    } break;

    // ****************** depublier_article ***********************
    case "depublier_article" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$depublier_article = $connect->query("update `" . $tblprefix . "articles` set publie_article = '0' where id_article = $id_article;");
    	}
    	locationhref_admin("?inc=edit_articles");
    } break;

	// ****************** home_article **************************
		case "home_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				
				$select_accueil = $connect->query("select accueil_article from `" . $tblprefix . "articles` where id_article = $id_article;");
				if (mysqli_num_rows($select_accueil) > 0) {
					
					$accueil_article = mysqli_result($select_accueil,0);
					if ($accueil_article == 0) $edit_accueil_article = 1;
					else $edit_accueil_article = 0;

    			$update_accueil_article = $connect->query("update `" . $tblprefix . "articles` set accueil_article = '$edit_accueil_article' where id_article = $id_article;");
				}
			}
			locationhref_admin("?inc=edit_articles");
		} break;

   	// ****************** liste_article **************************	
		default : {
			
			confirmer();

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	if (isset($_GET['t']) && ctype_digit($_GET['t']))
		$page2 = intval($_GET['t']);
	else $page2 = 1;
	
    	echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_articles&do=create_article\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_articles&do=create_article\"><b>".creer_article."</b></a></td></tr></table>";

    // En attente de validation
    	echo "<hr /><a name=\"en_attente\"><b><u>- ".articles_non_valides." : </u></b></a><br /><br />";

  $select_articles_nonvalides = $connect->query("select * from `" . $tblprefix . "articles` where publie_article = '0' order by ordre_article;");
	$nbr_trouve = mysqli_num_rows($select_articles_nonvalides);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    	$select_articles_nonvalides_limit = $connect->query("select * from `" . $tblprefix . "articles` where publie_article = '0' order by ordre_article limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".article."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($article = mysql_fetch_row($select_articles_nonvalides_limit)){
					
					$id_article = $article[0];
					$titre_article = html_ent(trim($article[3]));
					$contenu_article = html_ent(trim($article[4]));
					if (empty($titre_article)) $titre_article = $contenu_article;
					
					$titre_article = readmore($titre_article,70);
					
					if ($article[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><b>".$titre_article."</b></td>";

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $article[1];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$article[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=edit_article&id_article=".$id_article."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_articles&do=delete_article&id_article=".$id_article."&key=".$key."','".confirm_supprimer_article."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?article=".$id_article."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=publier_article&id_article=".$id_article."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_articles&l=".$page_precedente."&t=".$page2."#en_attente\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_articles&l=".$page_precedente."&t=".$page2."#en_attente\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=edit_articles&l=".$i."&t=".$page2."#en_attente\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_articles&l=".$page_suivante."&t=".$page2."#en_attente\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_articles&l=".$page_suivante."&t=".$page2."#en_attente\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo aucun_article_non_valide."<br />";

    // valid�s
    	echo "<br /><hr /><a name=\"valides\"><b><u>- ".articles_valides." : </u></b></a><br /><br />";

    	$select_articles_valides = $connect->query("select * from `" . $tblprefix . "articles` where publie_article = '1' order by ordre_article;");
			 $nbr_trouve = mysqli_num_rows($select_articles_valides);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

    	$select_articles_valides_limit = $connect->query("select * from `" . $tblprefix . "articles` where publie_article = '1' order by ordre_article limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".article."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".valide_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".home_page."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($article = mysql_fetch_row($select_articles_valides_limit)){
					
					$id_article = $article[0];
					$titre_article = html_ent(trim($article[3]));
					$contenu_article = html_ent(trim($article[4]));
					if (empty($titre_article)) $titre_article = $contenu_article;
					
					$titre_article = readmore($titre_article,70);
    			
					if ($article[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><b>".$titre_article."</b></td>";

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $article[1];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$article[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";

    			$select_validateur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $article[9];");
    			if (mysqli_num_rows($select_validateur) == 1)
    				$validateur = html_ent(mysqli_result($select_validateur,0));
    			else $validateur = inconnu;
    			$validateur = wordwrap($validateur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$article[9]."\" title=\"".user_profile."\"><b>".$validateur."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=edit_article&id_article=".$id_article."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_articles&do=delete_article&id_article=".$id_article."&key=".$key."','".confirm_supprimer_article."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?article=".$id_article."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					if ($article[6] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=home_article&id_article=".$id_article."&key=".$key."\" title=\"".afficher_accueil."\"><b>".oui."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=home_article&id_article=".$id_article."&key=".$key."\" title=\"".afficher_accueil."\"><b>".non."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_articles&do=depublier_article&id_article=".$id_article."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_articles&t=".$page_precedente."&l=".$page."#valides\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_articles&t=".$page_precedente."&l=".$page."#valides\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=edit_articles&t=".$i."&l=".$page."#valides\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_articles&t=".$page_suivante."&l=".$page."#valides\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_articles&t=".$page_suivante."&l=".$page."#valides\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo aucun_article_valide."<br />";
		}
	}
	
} else echo restricted_access;

?>