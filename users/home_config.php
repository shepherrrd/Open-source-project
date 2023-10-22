<?php
/*
 * 	Manhali - Free Learning Management System
 *	home_config.php
 *	2009-05-14 23:38
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

	echo "<div id=\"titre\">".gestion_accueil."</div><br /><br />";

	if (isset($_GET['id_article']) && ctype_digit($_GET['id_article']))
		$id_article = intval($_GET['id_article']);
	else $id_article = 0;

	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){

	// ****************** orderup_article **************************
		case "orderup_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cet_article = $connect->query ("select ordre_accueil from `" . $tblprefix . "articles` where id_article = $id_article;");
				if (mysqli_num_rows($cet_article) == 1) {
					$ordre_accueil = mysqli_result($cet_article,0);

    			$article_precedent = $connect->query ("select id_article, ordre_accueil from `" . $tblprefix . "articles` where accueil_article = '1' and ordre_accueil < $ordre_accueil order by ordre_accueil desc;");
					if (mysqli_num_rows($article_precedent) > 0) {
						$idarticle_precedent = mysqli_result($article_precedent,0,0);
						$ordrearticle_precedent = mysqli_result($article_precedent,0,1);

						$order_this_article = $connect->query("update `" . $tblprefix . "articles` set ordre_accueil = $ordrearticle_precedent where id_article = $id_article;");
						$order_article_precedent = $connect->query("update `" . $tblprefix . "articles` set ordre_accueil = $ordre_accueil where id_article = $idarticle_precedent;");
					}
    		}
			}
			locationhref_admin("?inc=home_config");
		} break;

	// ****************** orderdown_article **************************
		case "orderdown_article" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cet_article = $connect->query ("select ordre_accueil from `" . $tblprefix . "articles` where id_article = $id_article;");
				if (mysqli_num_rows($cet_article) == 1) {
					$ordre_accueil = mysqli_result($cet_article,0);

    			$article_suivant = $connect->query ("select id_article, ordre_accueil from `" . $tblprefix . "articles` where accueil_article = '1' and ordre_accueil > $ordre_accueil order by ordre_accueil;");
					if (mysqli_num_rows($article_suivant) > 0) {
						$idarticle_suivant = mysqli_result($article_suivant,0,0);
						$ordrearticle_suivant = mysqli_result($article_suivant,0,1);

						$order_this_article = $connect->query("update `" . $tblprefix . "articles` set ordre_accueil = $ordrearticle_suivant where id_article = $id_article;");
						$order_article_suivant = $connect->query("update `" . $tblprefix . "articles` set ordre_accueil = $ordre_accueil where id_article = $idarticle_suivant;");
					}
    		}
			}
			locationhref_admin("?inc=home_config");
		} break;

	// ****************** multi_colonnes **************************
		case "multi_colonnes" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				
				$select_colonnes = $connect->query("select accueil_multicolonnes from `" . $tblprefix . "site_infos`;");
				if (mysqli_num_rows($select_colonnes) > 0) {
					
					$multicolonnes = mysqli_result($select_colonnes,0);
					if ($multicolonnes == 0) $edit_colonnes = 1;
					else $edit_colonnes = 0;

    			$update_multicolonnes = $connect->query("update `" . $tblprefix . "site_infos` set accueil_multicolonnes = '$edit_colonnes';");
				}
			}
			locationhref_admin("?inc=home_config");
		} break;

	// ****************** open_menu **************************
		default : {

			$select_colonnes = $connect->query("select accueil_multicolonnes from `" . $tblprefix . "site_infos`;");
			
			if (mysqli_num_rows($select_colonnes) > 0) {
					echo "<h4><u>- ".afficher_multicolonnes." :</u>&nbsp;&nbsp;&nbsp;<font size=\"4\">";
					
					$multicolonnes = mysqli_result($select_colonnes,0);
					if ($multicolonnes == 1) echo oui;
					else echo non;
					
					echo "</font>&nbsp;&nbsp;&nbsp;<a href=\"?inc=home_config&do=multi_colonnes&key=".$key."\" title=\"".modifier_affichage_accueil."\">".modifier."</a></h4>";
					
			}
			
			echo "<h4><u>- ".ordonner_articles_accueil." : </u></h4>";
			
			$max_len = 100;
		 	$select_articles = $connect->query("select id_article, titre_article, contenu_article, ordre_accueil from `" . $tblprefix . "articles` where accueil_article = '1' order by ordre_accueil;");
		 	
		 	if (mysqli_num_rows($select_articles) > 0){
		 		
		 		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td style=\"border: 1px solid #000000;\" align=\"center\" width=\"75%\"><b>".articles."</b></td>";
				echo "\n<td style=\"border: 1px solid #000000;\" align=\"center\" width=\"25%\"><b>".ordonner."</b></td>";
				echo "</tr>\n";

				$i_ordre = 1;
				while($article = mysql_fetch_row($select_articles)){
					
					$id_article = $article[0];
					$titre_article = html_ent($article[1]);
					$contenu_article = html_ent($article[2]);
					if (empty($titre_article)) $titre_article = $contenu_article;
					
					$titre_article = readmore($titre_article,$max_len);

					$ordre_accueil = $article[3];
							
							echo "<tr>\n";
							echo "\n<td class=\"affichage_table\"><b>".$titre_article."</b></td>";
							
							echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
							$article_precedent = $connect->query ("select id_article from `" . $tblprefix . "articles` where ordre_accueil < $ordre_accueil and accueil_article = '1' order by ordre_accueil desc;");
							if (mysqli_num_rows($article_precedent) > 0)
								echo "<a href=\"?inc=home_config&do=orderup_article&id_article=".$id_article."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
							else echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
							echo "<b> ".$i_ordre." </b>";
							$i_ordre++;
							$article_suivant = $connect->query ("select id_article from `" . $tblprefix . "articles` where ordre_accueil > $ordre_accueil and accueil_article = '1' order by ordre_accueil;");
							if (mysqli_num_rows($article_suivant) > 0)
								echo "<a href=\"?inc=home_config&do=orderdown_article&id_article=".$id_article."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
							else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
							echo "</td>";
							
							echo "</tr>\n";
						}
						echo "\n</table>";
		 			} else echo aucun_article_home."<br /><br />";
		}
	}
} else echo restricted_access;

?>