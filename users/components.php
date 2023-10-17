<?php
/*
 * 	Manhali - Free Learning Management System
 *	components.php
 *	2009-05-09 01:35
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

	echo "<div id=\"titre\">".gestion_composants."</div><br /><br />";

	if (isset($_GET['id_com']) && ctype_digit($_GET['id_com']))
		$id_com = intval($_GET['id_com']);
	else $id_com = 0;
	
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){

   	// ****************** activer_component **************************
		case "activer_component" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$activer_component = mysql_query("update `" . $tblprefix . "composants` set active_composant = '1' where id_composant = $id_com;");
			}
			locationhref_admin("?inc=components");
		} break;
	
   	// ****************** desactiver_component **************************
		case "desactiver_component" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$desactiver_component = mysql_query("update `" . $tblprefix . "composants` set active_composant = '0' where id_composant = $id_com;");
			}
			locationhref_admin("?inc=components");
		} break;

	// ****************** orderup_component **************************
		case "orderup_component" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_composant = mysql_query ("select ordre_composant from `" . $tblprefix . "composants` where id_composant = $id_com;");
				if (mysql_num_rows($ce_composant) == 1) {
					$ordre_composant = mysql_result($ce_composant,0);

    			$composant_precedent = mysql_query ("select id_composant, ordre_composant from `" . $tblprefix . "composants` where ordre_composant != 0 and ordre_composant < $ordre_composant order by ordre_composant desc;");
					if (mysql_num_rows($composant_precedent) > 0) {
						$idcomposant_precedent = mysql_result($composant_precedent,0,0);
						$ordrecomposant_precedent = mysql_result($composant_precedent,0,1);

						$order_this_composant = mysql_query("update `" . $tblprefix . "composants` set ordre_composant = $ordrecomposant_precedent where id_composant = $id_com;");
						$order_composant_precedent = mysql_query("update `" . $tblprefix . "composants` set ordre_composant = $ordre_composant where id_composant = $idcomposant_precedent;");
					}
    		}
			}
			locationhref_admin("?inc=components");
		} break;

	// ****************** orderdown_component **************************
		case "orderdown_component" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_composant = mysql_query ("select ordre_composant from `" . $tblprefix . "composants` where id_composant = $id_com;");
				if (mysql_num_rows($ce_composant) == 1) {
					$ordre_composant = mysql_result($ce_composant,0);

    			$composant_suivant = mysql_query ("select id_composant, ordre_composant from `" . $tblprefix . "composants` where ordre_composant != 0 and ordre_composant > $ordre_composant order by ordre_composant;");
					if (mysql_num_rows($composant_suivant) > 0) {
						$idcomposant_suivant = mysql_result($composant_suivant,0,0);
						$ordrecomposant_suivant = mysql_result($composant_suivant,0,1);

						$order_this_composant = mysql_query("update `" . $tblprefix . "composants` set ordre_composant = $ordrecomposant_suivant where id_composant = $id_com;");
						$order_composant_suivant = mysql_query("update `" . $tblprefix . "composants` set ordre_composant = $ordre_composant where id_composant = $idcomposant_suivant;");
					}
    		}
			}
			locationhref_admin("?inc=components");
		} break;
		
   	// ****************** liste_composants **************************	
		default : {
			
			$select_composant = mysql_query("select * from `" . $tblprefix . "composants` where ordre_composant = 0;");
			if (mysql_num_rows($select_composant) > 0){
				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".nom_composant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".titre_composant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				
				$i_ordre = 1;
				
				while($composant = mysql_fetch_row($select_composant)){
					
					$id_composant = $composant[0];
					$nom_composant = html_ent($composant[1]);
					$titre_composant = html_ent($composant[2]);
					$active_composant = $composant[4];
					$ordre_composant = $composant[5];
					
					if ($active_composant == 1)
						$color = "green";
					else $color = "red";
					
					echo "<tr>\n";
			
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$nom_composant."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$titre_composant."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&edit_com=".$id_composant."#edit\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					
					if ($active_composant == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&do=desactiver_component&id_com=".$id_composant."&key=".$key."\"><b>".desactiver."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&do=activer_component&id_com=".$id_composant."&key=".$key."\"><b>".activer."</b></a></td>";
							
					echo "</tr>\n";
				}
				echo "\n</table>";
			}

// vertical panel

    	echo "<hr /><a name=\"vertical_panel\"><b><u>- ".vertical_panel_comp." : </u></b></a><br /><br />";
    	
			$select_composant = mysql_query("select * from `" . $tblprefix . "composants` where ordre_composant != 0 order by ordre_composant;");
			if (mysql_num_rows($select_composant) > 0){
				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".nom_composant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".titre_composant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "</tr>";
				
				$i_ordre = 1;
				
				while($composant = mysql_fetch_row($select_composant)){
					
					$id_composant = $composant[0];
					$nom_composant = html_ent($composant[1]);
					$titre_composant = html_ent($composant[2]);
					$active_composant = $composant[4];
					$ordre_composant = $composant[5];
					
					if ($active_composant == 1)
						$color = "green";
					else $color = "red";
					
					echo "<tr>\n";
			
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$nom_composant."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$titre_composant."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&edit_com=".$id_composant."#edit\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					
					if ($active_composant == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&do=desactiver_component&id_com=".$id_composant."&key=".$key."\"><b>".desactiver."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=components&do=activer_component&id_com=".$id_composant."&key=".$key."\"><b>".activer."</b></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";

					$composant_precedent = mysql_query ("select id_composant from `" . $tblprefix . "composants` where ordre_composant < $ordre_composant and ordre_composant != 0 order by ordre_composant desc;");
					if (mysql_num_rows($composant_precedent) > 0)
						echo "<a href=\"?inc=components&do=orderup_component&id_com=".$id_composant."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$i_ordre." </b>";
					$i_ordre++;
					$composant_suivant = mysql_query ("select id_composant from `" . $tblprefix . "composants` where ordre_composant > $ordre_composant and ordre_composant != 0 order by ordre_composant;");
					if (mysql_num_rows($composant_suivant) > 0)
						echo "<a href=\"?inc=components&do=orderdown_component&id_com=".$id_composant."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";

					echo "</td>";		
					echo "</tr>\n";
				}
				echo "\n</table>";
			}

			if (isset($_GET['edit_com']) && ctype_digit($_GET['edit_com'])){
				$edit_com = intval($_GET['edit_com']);
				
				$select_component = mysql_query("select * from `" . $tblprefix . "composants` where id_composant = $edit_com;");

				if (mysql_num_rows($select_component) > 0) {
					$component = mysql_fetch_row($select_component);

					$nom_component = $component[1];
					$titre_component = html_ent($component[2]);
					$contenu_component = html_ent($component[3]);
					
					echo "<h3><a name=\"edit\"><u>".editer_composant."</u></a></h3>";
					
					if (!empty($_POST['send'])){
						$titre_com = escape_string($_POST['titre_com']);
						$titre_com = trim($titre_com);
						if (!empty($titre_com) && $titre_com != $titre_component)
							$update_component = mysql_query("update `" . $tblprefix . "composants` set titre_composant = '$titre_com' where id_composant = $edit_com;");

						if ($nom_component == "additional_block"){
							$contenu_com = escape_string($_POST['contenu_com']);
							if (!empty($contenu_com) && $contenu_com != $contenu_component)
								$update_component2 = mysql_query("update `" . $tblprefix . "composants` set contenu_composant = '$contenu_com' where id_composant = $edit_com;");
						}
						redirection(composant_modifie,"?inc=components",3,"tips",1);
					}
					else {
    				echo "\n<form method=\"POST\" action=\"\">";
    				echo "\n<p><b>" .titre_composant. " : </b><br /><input name=\"titre_com\" type=\"text\" maxlength=\"100\" size=\"50\" value=\"".$titre_component."\"></p>";
					  
					  if ($nom_component == "additional_block"){
					  	
					  	echo "<script type=\"text/javascript\">function preview() {document.getElementById('prev').innerHTML = document.getElementById('contenu_com').value;}</script>";
					  	
					  	echo "\n<b>" .contenu_composant. " : </b><br /><textarea onkeyup=\"preview();\" name=\"contenu_com\" id=\"contenu_com\" rows=\"7\" cols=\"38\">".$contenu_component."</textarea>";
					  	echo "<fieldset style=\"width: 303px;\"><legend><b>".previsualisation."</b></legend>\n";
					  	echo "<div id=\"prev\">";
					  	echo "<script type=\"text/javascript\">preview();</script>";
					  	echo "</div></fieldset><br />\n";
					  }
					  echo "\n<p><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
					}
				}
			}
		}
	}
} else echo restricted_access;

?>