<?php
/*
 * 	Manhali - Free Learning Management System
 *	comments.php
 *	2011-11-27 19:17
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

 		$select_statut_comments = $connect->query("select active_composant, titre_composant from `" . $tblprefix . "composants` where nom_composant = 'comments';");
 		if (mysqli_num_rows($select_statut_comments) == 1) {
  		$statut_comments = mysqli_result($select_statut_comments,0,0);
  		$titre_comments = mysqli_result($select_statut_comments,0,1);
  		if ($statut_comments == 1) {

				if (isset($_GET['l']) && ctype_digit($_GET['l']))
					$page = intval($_GET['l']);
				else $page = 1;

				if (isset($_GET['o']) && ctype_digit($_GET['o']) && $_GET['o'] == 1){
					$order_num = 1;
					$order_com = "asc";
				}
				else {
					$order_num = 0;
					$order_com = "desc";
				}

				$maxlen_area_comment = 500;
// ************* delete_com
			 if (isset($_GET['do']) && $_GET['do'] == "delete_com"){
				if (isset($_GET['id_com']) && ctype_digit($_GET['id_com'])){
					if (isset($_GET['key']) && $_GET['key'] == $key){
						$id_comment = $_GET['id_com'];
						$select_user = $connect->query("select type_user, id_user from `" . $tblprefix . "commentaires` where id_post = $id_comment;");
    				if (mysqli_num_rows($select_user) == 1 && !empty($_SESSION['log'])){
    					$id_user_com = mysqli_result($select_user,0,1);
							if (mysqli_result($select_user,0,0) == "u")
								$type_auteur = 1;
							else if (mysqli_result($select_user,0,0) == "l")
								$type_auteur = 2;
							else $type_auteur = 0;
    					if(($type_auteur == $_SESSION['log'] && $id_user_com == $id_user_session) || (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1" || $id_user == $id_user_session)))
								$delete_com = $connect->query("delete from `" . $tblprefix . "commentaires` where id_post = $id_comment;");
						}
					}
				}
				locationhref_admin("?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."#comments");
			 }
// ************* edit_com
			 else if (isset($_GET['do']) && $_GET['do'] == "edit_com"){
				if (isset($_GET['id_com']) && ctype_digit($_GET['id_com']) && isset($_POST['contenu_comment'])){
					if (isset($_GET['key']) && $_GET['key'] == $key){
						$id_comment = $_GET['id_com'];
						$select_user = $connect->query("select type_user, id_user from `" . $tblprefix . "commentaires` where id_post = $id_comment;");
    				if (mysqli_num_rows($select_user) == 1 && !empty($_SESSION['log'])){
    					$id_user_com = mysqli_result($select_user,0,1);
							if (mysqli_result($select_user,0,0) == "u")
								$type_auteur = 1;
							else if (mysqli_result($select_user,0,0) == "l")
								$type_auteur = 2;
							else $type_auteur = 0;
							$contenu_comment = trim($_POST['contenu_comment']);
						 	if($type_auteur == $_SESSION['log'] && $id_user_com == $id_user_session && !empty($contenu_comment)){
						 		$contenu_comment = escape_string($contenu_comment);
						 		if (strlen($contenu_comment) > $maxlen_area_comment)
						 			$contenu_comment = substr($contenu_comment,0,$maxlen_area_comment);
 								$update_com = $connect->query("update `" . $tblprefix . "commentaires` SET contenu_post = '$contenu_comment', date_modification = ".time()." where id_post = $id_comment;");
							}
						}
					}
				}
				locationhref_admin("?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."#comments");
			 }
// ************* add_com
			 else if (isset($_GET['do']) && $_GET['do'] == "add_com"){
				if (isset($_POST['contenu_comment'])){
					if (isset($_GET['key']) && $_GET['key'] == $key){
    				if (!empty($_SESSION['log'])){
    					if ($_SESSION['log'] == 1)
    						$type_user = "u";
    					else $type_user = "l";
							$contenu_comment = trim($_POST['contenu_comment']);
						 	if(!empty($contenu_comment)){
						 		$contenu_comment = escape_string($contenu_comment);
						 		if (strlen($contenu_comment) > $maxlen_area_comment)
						 			$contenu_comment = substr($contenu_comment,0,$maxlen_area_comment);
 								$insert_com = $connect->query("INSERT INTO `" . $tblprefix . "commentaires` VALUES (NULL,'$type_objet',$id_objet,'$type_user',$id_user_session,'$contenu_comment',".time().",".time().");");
							}
						}
					}
				}
				locationhref_admin("?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."#comments");
			 }
// ************* liste
			 else {
				confirmer();
				echo "<script type=\"text/javascript\">function DisplayHideDiv(id_div){var lay=document.getElementById(id_div);var lay2=document.getElementById(id_div+'_edit');if(lay.style.display=='none'){lay.style.display='block';lay2.style.display='none';}else{lay.style.display='none';lay2.style.display='block';document.getElementById(id_div+'_area').focus();}}</script>";

				$select_comments = $connect->query("select * from `" . $tblprefix . "commentaires` where type_objet = '$type_objet' and id_objet = $id_objet order by date_creation ".$order_com.";");
				$nbr_trouve = mysqli_num_rows($select_comments);
				if ($nbr_trouve > 0){
					$page_max =  $nbr_resultats <= 0 ? 1 : ceil($nbr_trouve / $nbr_resultats);
					if ($page <= $page_max && $page > 1 && $page_max > 1)
						$limit = ($page - 1) * $nbr_resultats;
					else {
						$limit = 0;
						$page = 1;
					}
					echo "<hr /><table border=\"0\"><tr><td><h3><a name=\"comments\" href=\"?".$path_objet."=".$id_objet."&o=".$order_num."#comments\">".$titre_comments."</a></h3></td>";
					echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
					if (isset($order_num) && $order_num == 1){
						echo "<td><b><a href=\"?".$path_objet."=".$id_objet."&l=".$page."&o=0#comments\">".newest_first."</a></b> <a href=\"?".$path_objet."=".$id_objet."&l=".$page."&o=0#comments\"><img border=\"0\" src=\"images/others/up.png\" width=\"15\" height=\"15\" /></a></td>";
						echo "<td><b>&nbsp;".oldest_first."</b> <img border=\"0\" src=\"images/others/down2.png\" width=\"15\" height=\"15\" /></td>";
					}
					else {
						echo "<td><b>".newest_first."</b> <img border=\"0\" src=\"images/others/up2.png\" width=\"15\" height=\"15\" /></td>";
						echo "<td><b>&nbsp;<a href=\"?".$path_objet."=".$id_objet."&l=".$page."&o=1#comments\">".oldest_first."</a></b> <a href=\"?".$path_objet."=".$id_objet."&l=".$page."&o=1#comments\"><img border=\"0\" src=\"images/others/down.png\" width=\"15\" height=\"15\" /></a></td>";
					}
					if ($page_max > 1){
						$page_precedente = $page - 1;
						$page_suivante = $page + 1;
						echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
						if ($page_precedente >= 1)
							echo "<td><a href=\"?".$path_objet."=".$id_objet."&l=".$page_precedente."&o=".$order_num."#comments\"><img border=\"0\" src=\"images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?".$path_objet."=".$id_objet."&l=".$page_precedente."&o=".$order_num."#comments\"><b>".page_precedente."</b></a></td>";
						echo "<td>";
						for($i=1;$i<=$page_max;$i++){
							if ($i != $page) echo "<a href=\"?".$path_objet."=".$id_objet."&l=".$i."&o=".$order_num."#comments\">";
							echo "<b>".$i."</b>";
							if ($i != $page) echo "</a>";
							echo "&nbsp; ";
						}
						echo "</td>";
						if ($page_suivante <= $page_max)
							echo "<td><a href=\"?".$path_objet."=".$id_objet."&l=".$page_suivante."&o=".$order_num."#comments\"><b>".page_suivante."</b></a></td><td><a href=\"?".$path_objet."=".$id_objet."&l=".$page_suivante."&o=".$order_num."#comments\"><img border=\"0\" src=\"images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
					}
					echo "</tr></table>";

					if (!empty($_SESSION['log'])){
						echo "<form method=\"POST\" action=\"?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."&do=add_com&key=".$key."\">";
						echo "<textarea name=\"contenu_comment\" maxlength=\"".$maxlen_area_comment."\" cols=\"50\" rows=\"5\"></textarea><br /><input type=\"submit\" class=\"button\" value=\"".ajouter."\">";
						echo "</form><hr />";
					}
						
				  $select_comments_limit = $connect->query("select * from `" . $tblprefix . "commentaires` where type_objet = '$type_objet' and id_objet = $id_objet order by date_creation ".$order_com." limit $limit, 100");
					while($comment = mysqli_fetch_row($select_comments_limit)){

						$id_comment = $comment[0];
						$contenu_comment = bbcode_br(html_ent(trim($comment[5])));
						
						$date_creation_comment = $comment[6];
						$date_creation_comment = set_date($dateformat,$date_creation_comment);
						
						$date_modification_comment = $comment[7];
						$date_modification_comment = set_date($dateformat,$date_modification_comment);

						if ($comment[3] == "u")
							$type_auteur = 1;
						else if ($comment[3] == "l")
							$type_auteur = 2;
						else $type_auteur = 0;
						
						if ($type_auteur == 1){
							$selectauteur = $connect->query("select identifiant_user, photo_profil from `" . $tblprefix . "users` where id_user = $comment[4];");
							$lien_profile = "profiles";
						}
						else {
							$selectauteur = $connect->query("select identifiant_apprenant, photo_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $comment[4];");
							$lien_profile = "s_profiles";
						}
						if (mysqli_num_rows($selectauteur) == 1) {
							$auteur = html_ent(mysqli_result($selectauteur,0,0));
							$photo_profil = html_ent(mysqli_result($selectauteur,0,1));
						}
						else {
							$auteur = inconnu;
							$photo_profil = "man.jpg";
						}
						
						echo "\n<div id=\"write_by\"><table border=\"0\"><tr><td rowspan=\"2\">";

						if ($afficher_profil == 1)
							echo "<a href=\"?".$lien_profile."=".$comment[4]."\" title=\"".user_profile."\"><img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$auteur."\" width=\"40\" height=\"40\" /></a></td><td><a href=\"?".$lien_profile."=".$comment[4]."\" title=\"".user_profile."\">".$auteur."</a>";
						else
							echo "<img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$auteur."\" width=\"40\" height=\"40\" /></td><td>".$auteur;

						if (!empty($_SESSION['log'])){
						 if($type_auteur == $_SESSION['log'] && $comment[4] == $id_user_session)
						 	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:DisplayHideDiv('comment".$id_comment."')\"><img border=\"0\" src=\"images/others/edit.png\" width=\"20\" height=\"20\" /></a>";
						}

						if (!empty($_SESSION['log'])){
						 if(($type_auteur == $_SESSION['log'] && $comment[4] == $id_user_session) || (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1" || $id_user == $id_user_session)))
						 	echo "&nbsp;&nbsp;&nbsp;<a href=\"#\" onClick=\"confirmer('?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."&do=delete_com&id_com=".$id_comment."&key=".$key."','".confirm_supprimer_comment."')\" title=\"".supprimer."\"><img border=\"0\" src=\"images/others/delete.png\" width=\"20\" height=\"20\" /></a>";
						}
						
						echo "</td></tr><tr><td>".$date_creation_comment." | ".modifie." ".$date_modification_comment;
						echo "</td></tr></table></div>";

						echo "\n<div id=\"comment".$id_comment."\">".$contenu_comment."</div>";

						if (!empty($_SESSION['log'])){
						 if($type_auteur == $_SESSION['log'] && $comment[4] == $id_user_session){
								echo "<div style=\"display: none;\" id=\"comment".$id_comment."_edit\"><form method=\"POST\" action=\"?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."&do=edit_com&id_com=".$id_comment."&key=".$key."\">";
								echo "<textarea name=\"contenu_comment\" id=\"comment".$id_comment."_area\" maxlength=\"".$maxlen_area_comment."\" cols=\"100\" rows=\"5\">".br_bbcode($contenu_comment)."</textarea><br /><input type=\"submit\" class=\"button\" value=\"".editer."\"> <input type=\"button\" class=\"button\" value=\"".annuler."\" onClick=\"DisplayHideDiv('comment".$id_comment."')\">";
								echo "</form></div>";
							}
						}
						echo "<hr />";
					}
				}
				else {
					if (!empty($_SESSION['log'])){
						echo "<hr /><h3><a name=\"comments\" href=\"?".$path_objet."=".$id_objet."&o=".$order_num."#comments\">".$titre_comments."</a></h3>";
						echo "<form method=\"POST\" action=\"?".$path_objet."=".$id_objet."&l=".$page."&o=".$order_num."&do=add_com&key=".$key."\">";
						echo "<textarea name=\"contenu_comment\" maxlength=\"".$maxlen_area_comment."\" cols=\"50\" rows=\"5\"></textarea><br /><input type=\"submit\" class=\"button\" value=\"".ajouter."\">";
						echo "</form><br />";
					}
				}
			 }
  		}
		}
 
?>