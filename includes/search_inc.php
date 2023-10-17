<?php
/*
 * 	Manhali - Free Learning Management System
 *	search_inc.php
 *	2011-01-27 12:42
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

	function didyoumean($lang,$string) {

		$search = str_replace(" ","+",$string);

		$host = "www.google.com";
		$req = "/search?hl=".$lang."&q=".$search;
		$port = 80;

		$keystringend = "</i></b></a>";
		
		if ($socket = @fsockopen($host, $port, $errno, $errstr, 5)){
			fputs($socket, "GET ".$req." HTTP/1.1\r\n");
			fputs($socket, "Host: ".$host."\r\n");
			fputs($socket, "Connection: close\r\n\r\n");
			stream_set_timeout($socket, 5);
			$line = "";
			while (!feof($socket)){
				$line0 = fgets($socket,4096);
				$line .= $line0;
				if (strpos($line0,$keystringend) !== false)
					break;
			}
			fclose($socket);

			preg_match('@class="spell([\s\S]*?)><b><i>([\s\S]*?)</i></b></a>@',$line, $search2);
			$search3 = @mb_convert_encoding($search2[2], 'UTF-8', mb_detect_encoding($search2[2], 'UTF-8, ISO-8859-1'));
			return $search3;
		} else return false;
	}

	$select_statut_search = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'search';");
	if (mysql_num_rows($select_statut_search) == 1) {
		$statut_search = mysql_result($select_statut_search,0);
		if ($statut_search == 1) {
			$search = trim($_GET['search']);
			
			if (isset($_GET['exact']) && $_GET['exact'] == "no")
				$exact_phrase = "no";
			else
				$exact_phrase = "yes";
				
		echo "<form method=\"GET\" name=\"form1\">";
		
		echo "<input name=\"search\" size=\"40\" type=\"text\" maxlength=\"50\" value=\"";
		if(isset($_GET['search']))
			echo html_ent($_GET['search']);
		echo "\">";
		
		echo "\n<br /><input name=\"exact\" type=\"radio\" value=\"yes\"";
		if ($exact_phrase == "yes")
			echo "checked=\"checked\"";
		echo "> ".exact_phrase;
		
		echo "\n<input name=\"exact\" type=\"radio\" value=\"no\"";
		if ($exact_phrase == "no")
			echo "checked=\"checked\"";
		echo "> ".any_word;

		$lien_exact = "&exact=".$exact_phrase;

		echo "\n<br /><input type=\"submit\" class=\"button\" value=\"".rechercher."\"></form>";
		
		echo "<div id=\"titre\">".search_title." ".html_ent($search)."</div>\n";
		
		if (!isset($_GET['t']) && !isset($_GET['a'])){
			if ($didyoumean = didyoumean($language,$search))
				echo "<br /><b>".youmean." : <a href=\"?search=".$didyoumean.$lien_exact."\">".$didyoumean."</a></b>";
		}
			if (strlen($search) > 2 && strlen($search) < 51) {

				$search = escape_string($search);
				$search = addcslashes($search,"%_");

				$yadresultat = 0;

				$tab_search = explode(" ",$search);
				$chaine_search = "";

				$tab_search2 = array();
				foreach ($tab_search as $elem) {
    			if (strlen($elem) > 2)
						$tab_search2[] = $elem;
				}
				$nbr_elems = count($tab_search2);
				
				$search_chaine_tuto = "";
				$search_chaine_article = "";
				foreach ($tab_search2 as $elem){
					$search_chaine_tuto .= "titre_chapitre like '%".$elem."%' or ";
					$search_chaine_tuto .= "objectifs_chapitre like '%".$elem."%' or ";
					$search_chaine_tuto .= "titre_bloc like '%".$elem."%' or ";
					$search_chaine_tuto .= "contenu_bloc like '%".$elem."%' or ";
					
					$search_chaine_article .= "titre_article like '%".$elem."%' or ";
					$search_chaine_article .= "contenu_article like '%".$elem."%' or ";
				}
				if (substr($search_chaine_tuto,-3,2) == "or")
					$search_chaine_tuto = substr($search_chaine_tuto,0,-4);
				
				if (substr($search_chaine_article,-3,2) == "or")
					$search_chaine_article = substr($search_chaine_article,0,-4);
					
				if (isset($_GET['t']) && ctype_digit($_GET['t']))
					$t = intval($_GET['t']);
				else
					$t = 1;
		
				if (isset($_GET['a']) && ctype_digit($_GET['a']))
					$a = intval($_GET['a']);
				else
					$a = 1;
				
				if ($exact_phrase == "yes" || $nbr_elems < 2){
					// phrase exacte
					$bloc_search_req = "select distinct `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, `" . $tblprefix . "chapitres`.id_chapitre, titre_chapitre from `" . $tblprefix . "blocs`, `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties`, `" . $tblprefix . "tutoriels` where (titre_chapitre like '%$search%' or objectifs_chapitre like '%$search%' or titre_bloc like '%$search%' or contenu_bloc like '%$search%') and `" . $tblprefix . "blocs`.id_chapitre = `" . $tblprefix . "chapitres`.id_chapitre and `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and `" . $tblprefix . "parties`.id_tutoriel = `" . $tblprefix . "tutoriels`.id_tutoriel and publie_bloc = '1' and publie_chapitre = '1' and publie_partie = '1' and publie_tutoriel = '2' order by ordre_tutoriel, ordre_partie, ordre_chapitre, ordre_bloc;";
					$article_search_req = "select id_article, titre_article, contenu_article from `" . $tblprefix . "articles` where (titre_article like '%$search%' or contenu_article like '%$search%') and publie_article = '1' order by id_article desc;";
				}
				else {
					//n importe quel mot
					$bloc_search_req = "select distinct `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, `" . $tblprefix . "chapitres`.id_chapitre, titre_chapitre from `" . $tblprefix . "blocs`, `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties`, `" . $tblprefix . "tutoriels` where (".$search_chaine_tuto.") and `" . $tblprefix . "blocs`.id_chapitre = `" . $tblprefix . "chapitres`.id_chapitre and `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and `" . $tblprefix . "parties`.id_tutoriel = `" . $tblprefix . "tutoriels`.id_tutoriel and publie_bloc = '1' and publie_chapitre = '1' and publie_partie = '1' and publie_tutoriel = '2' order by ordre_tutoriel, ordre_partie, ordre_chapitre, ordre_bloc;";
					$article_search_req ="select id_article, titre_article, contenu_article from `" . $tblprefix . "articles` where (".$search_chaine_article.") and publie_article = '1' order by id_article desc;";
				}
				
				// *** tutoriels
				echo "<a name=\"tutoriel\"><div id=\"titre\"><p align=\"left\">".tutoriels."</p></div></a>\n";
				
				$bloc_search = mysql_query($bloc_search_req);
				$num_rows_tuto = mysql_num_rows($bloc_search);
	
				if ($num_rows_tuto > 0) {
					
					$yadresultat = 1;
					
					$t_page_max = ceil($num_rows_tuto / $nbr_resultats);
					if ($t <= $t_page_max && $t > 1 && $t_page_max > 1)
						$t_limit = ($t - 1) * $nbr_resultats;
					else {
						$t_limit = 0;
						$t = 1;
					}
					
					$compteur_tutos = 0;
					$dernier_tutoriel = 0;

					while($bloc_trouve = mysql_fetch_row($bloc_search)){
						$id_tutoriel = $bloc_trouve[0];
						if ($id_tutoriel != $dernier_tutoriel){
							$compteur_tutos += 1;
							$dernier_tutoriel = $id_tutoriel;
						}
					}

					echo "<h4><u>".$num_rows_tuto." ".chapitre_trouve." ".$compteur_tutos." ".tutoriel_trouve."</u></h4>\n";

					if ($exact_phrase == "yes" || $nbr_elems < 2)
						// phrase exacte
						$bloc_search_limit_req = "select distinct `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, `" . $tblprefix . "chapitres`.id_chapitre, titre_chapitre from `" . $tblprefix . "blocs`, `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties`, `" . $tblprefix . "tutoriels` where (titre_chapitre like '%$search%' or objectifs_chapitre like '%$search%' or titre_bloc like '%$search%' or contenu_bloc like '%$search%') and `" . $tblprefix . "blocs`.id_chapitre = `" . $tblprefix . "chapitres`.id_chapitre and `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and `" . $tblprefix . "parties`.id_tutoriel = `" . $tblprefix . "tutoriels`.id_tutoriel and publie_bloc = '1' and publie_chapitre = '1' and publie_partie = '1' and publie_tutoriel = '2' order by ordre_tutoriel, ordre_partie, ordre_chapitre, ordre_bloc limit $t_limit, $nbr_resultats;";

					else 
						//n importe quel mot
						$bloc_search_limit_req = "select distinct `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, `" . $tblprefix . "chapitres`.id_chapitre, titre_chapitre from `" . $tblprefix . "blocs`, `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties`, `" . $tblprefix . "tutoriels` where (".$search_chaine_tuto.") and `" . $tblprefix . "blocs`.id_chapitre = `" . $tblprefix . "chapitres`.id_chapitre and `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and `" . $tblprefix . "parties`.id_tutoriel = `" . $tblprefix . "tutoriels`.id_tutoriel and publie_bloc = '1' and publie_chapitre = '1' and publie_partie = '1' and publie_tutoriel = '2' order by ordre_tutoriel, ordre_partie, ordre_chapitre, ordre_bloc limit $t_limit, $nbr_resultats;";

					$bloc_search_limit = mysql_query($bloc_search_limit_req);
					$dernier_tutoriel = 0;

					echo "<ul>";
					while($bloc_trouve = mysql_fetch_row($bloc_search_limit)){

						$id_tutoriel = $bloc_trouve[0];
						$titre_tutoriel = html_ent($bloc_trouve[1]);
						$id_chapitre = $bloc_trouve[2];
						$titre_chapitre = html_ent($bloc_trouve[3]);
						$titre_chapitre = readmore($titre_chapitre,$nombre_caracteres);

						if ($id_tutoriel != $dernier_tutoriel){
							echo "<hr /><h3><li><a href=\"?tutorial=".$id_tutoriel."\">".$titre_tutoriel." : </a></li></h3>\n";
							$dernier_tutoriel = $id_tutoriel;
						}
			
						echo "<ul type=\"circle\">";

						echo "<h4><li><a href=\"?chapter=".$id_chapitre."\">".$titre_chapitre."</a></li></h4>\n";
						echo "</ul>\n";
					}
					echo "</ul>";
		
					// limit links
					if ($t_page_max > 1){

						$t_page_precedente = $t - 1;
						$t_page_suivante = $t + 1;
  					echo "<table border=\"0\" align=\"center\"><tr>";
  			
						if ($t_page_precedente >= 1)
							echo "<td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t_page_precedente."&a=".$a."#tutoriel\"><img border=\"0\" src=\"images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t_page_precedente."&a=".$a."#tutoriel\"><b>".page_precedente."</b></a></td>";
				
						echo "<td>";
						for($i=1;$i<=$t_page_max;$i++){
							if ($i != $t)
								echo "<a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$i."&a=".$a."#tutoriel\">";
							echo "<b>".$i."</b>";
							if ($i != $t)
								echo "</a>";
							echo "&nbsp; ";
						}
						echo "</td>";
		
						if ($t_page_suivante <= $t_page_max)
							echo "<td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t_page_suivante."&a=".$a."#tutoriel\"><b>".page_suivante."</b></a></td><td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t_page_suivante."&a=".$a."#tutoriel\"><img border=\"0\" src=\"images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";

						echo "</tr></table>";
					}
				}
				else echo "<h4><u>".aucun_tutoriel."</u></h4>\n";
				echo "<hr /><hr />";
				
				// *** articles
				echo "<a name=\"article\"><div id=\"titre\"><p align=\"left\">".articles."</p></div></a>\n";

				$article_search = mysql_query($article_search_req);
				$num_rows_article = mysql_num_rows($article_search);
		
				if ($num_rows_article> 0) {
					
					$yadresultat = 1;
					
					$a_page_max = ceil($num_rows_article / $nbr_resultats);
					if ($a <= $a_page_max && $a > 1 && $a_page_max > 1)
						$a_limit = ($a - 1) * $nbr_resultats;
					else {
						$a_limit = 0;
						$a = 1;
					}
		
					echo "<h4><u>".$num_rows_article." ".article_trouve."</u></h4>\n";

					if ($exact_phrase == "yes" || $nbr_elems < 2)
						// phrase exacte
						$article_search_limit_req = "select id_article, titre_article, contenu_article from `" . $tblprefix . "articles` where (titre_article like '%$search%' or contenu_article like '%$search%') and publie_article = '1' order by id_article desc limit $a_limit, $nbr_resultats;";
					else 
						//n importe quel mot
						$article_search_limit_req = "select id_article, titre_article, contenu_article from `" . $tblprefix . "articles` where (".$search_chaine_article.") and publie_article = '1' order by id_article desc limit $a_limit, $nbr_resultats;";

					$article_search_limit = mysql_query($article_search_limit_req);

					while($article_trouve = mysql_fetch_row($article_search_limit)){
			
						$id_article = $article_trouve[0];
			
						$titre_article = html_ent($article_trouve[1]);
						$titre_article = readmore($titre_article,$nombre_caracteres);
			
						$contenu_article = trim($article_trouve[2]);
						$contenu_article = no_br($contenu_article);
						$contenu_article = readmore($contenu_article,$nombre_caracteres);
			
						echo "<h4><a href=\"?article=".$id_article."\">".$titre_article."</a></h4>\n";
						echo $contenu_article."<hr />\n";
					}

					// limit links
					if ($a_page_max > 1){

						$a_page_precedente = $a - 1;
						$a_page_suivante = $a + 1;
  					echo "<table border=\"0\" align=\"center\"><tr>";
  			
						if ($a_page_precedente >= 1)
							echo "<td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t."&a=".$a_page_precedente."#article\"><img border=\"0\" src=\"images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t."&a=".$a_page_precedente."#article\"><b>".page_precedente."</b></a></td>";
				
						echo "<td>";
						for($i=1;$i<=$a_page_max;$i++){
							if ($i != $a)
								echo "<a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t."&a=".$i."#article\">";
							echo "<b>".$i."</b>";
							if ($i != $a)
								echo "</a>";
							echo "&nbsp; ";
						}
						echo "</td>";
		
						if ($a_page_suivante <= $a_page_max)
							echo "<td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t."&a=".$a_page_suivante."#article\"><b>".page_suivante."</b></a></td><td><a href=\"?search=".stripcslashes($search).$lien_exact."&t=".$t."&a=".$a_page_suivante."#article\"><img border=\"0\" src=\"images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";

						echo "</tr></table>";
					}
				} else echo "<h4><u>".aucun_article."</u></h4>\n";
				if ($yadresultat == 0 && $exact_phrase == "yes" && $nbr_elems > 1)
					echo "<h4><center>".try_any_word."</center></h4>\n";
			} else echo "<b>".search_3_50."</b>";
 		} else accueil();
	} else accueil();

?>