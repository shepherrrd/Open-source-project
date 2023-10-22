<?php
/*
 * 	Manhali - Free Learning Management System
 *	articles.php
 *	2009-05-16 00:12
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && $grade_user_session == "0"){

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

	          redirection(article_cree,"?inc=articles",3,"tips",1);
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
						echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
 						while($classe = mysqli_fetch_row($select_classes)){
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
    			$article = mysqli_fetch_row($select_article_complet);
    			
    			$titre_article = html_ent($article[3]);
					$contenu_article = html_ent($article[4]);
					$acces_article = $article[12];
					
					$id_user = $article[1];
					$publie_article = $article[5];
					
					if ($id_user == $id_user_session && $publie_article == 0) {
					
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
 									redirection(article_modifie,"?inc=articles",3,"tips",1);
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
								echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
    						while($classe = mysqli_fetch_row($select_classes)){
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
    			} else locationhref_admin("?inc=articles");
    		} else locationhref_admin("?inc=articles");
    } break;

		// ****************** delete_article **************************
		case "delete_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_user = $connect->query("select id_user from `" . $tblprefix . "articles` where id_article = $id_article;");
    		if (mysqli_num_rows($select_user) == 1 && mysqli_result($select_user,0) == $id_user_session){
					$delete_article = $connect->query("delete from `" . $tblprefix . "articles` where id_article = $id_article;");
				}
			}
			locationhref_admin("?inc=articles");
		} break;

    // ****************** depublier_article ***********************
    case "depublier_article" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user = $connect->query("select id_user from `" . $tblprefix . "articles` where id_article = $id_article;");
    		if (mysqli_num_rows($select_user) == 1 && mysqli_result($select_user,0) == $id_user_session){
    			$depublier_article = $connect->query("update `" . $tblprefix . "articles` set publie_article = '0' where id_article = $id_article;");
    		}
    	}
    	locationhref_admin("?inc=articles");
    } break;

   	// ****************** liste_article **************************	
		default : {

			confirmer();

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;

  echo "<table border=\"0\"><tr><td><a href=\"?inc=articles&do=create_article\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=articles&do=create_article\"><b>".creer_article."</b></a></td></tr></table><br />";

  $select_my_articles = $connect->query("select * from `" . $tblprefix . "articles` where id_user = $id_user_session;");
	$nbr_trouve = mysqli_num_rows($select_my_articles);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    $select_my_articles_limit = $connect->query("select * from `" . $tblprefix . "articles` where id_user = $id_user_session limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".article."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".etat_article."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($article = mysqli_fetch_row($select_my_articles_limit)){
					
					$id_article = $article[0];
					$titre_article = html_ent(trim($article[3]));
					$contenu_article = html_ent(trim($article[4]));
					$publie_article = $article[5];
					
					if (empty($titre_article)) $titre_article = $contenu_article;
					$titre_article = readmore($titre_article,70);

					echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\" width=\"45%\"><b>".$titre_article."</b></td>";

					if ($publie_article == 1)
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=articles&do=edit_article&id_article=".$id_article."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=articles&do=delete_article&id_article=".$id_article."&key=".$key."','".confirm_supprimer_article."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?article=".$id_article."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					if ($publie_article == 1)
						echo "\n<td class=\"affichage_table\">".valide2."</td>\n<td class=\"affichage_table\"><a href=\"?inc=articles&do=depublier_article&id_article=".$id_article."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\">".valide1."</td>\n<td class=\"affichage_table\">---</td>";
					
					
					echo "</tr>\n";
				}
				echo "\n</table>";
				
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=articles&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=articles&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=articles&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=articles&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=articles&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}

				echo "<br /><img src=\"../images/icones/info.png\" /> <b>".remarque_article."</b>";
    	} else echo pas_encore_article."<br /><br />";
		}
	}
} else echo restricted_access;

?>