<?php
/*
 * 	Manhali - Free Learning Management System
 *	documents_inc.php
 *	2013-03-18 18:22
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

	$select_statut_docs = $connect->query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'documents';");
	if (mysqli_num_rows($select_statut_docs) == 1) {
		$statut_docs = mysqli_result($select_statut_docs,0,1);
		if ($statut_docs == 1) {
			$titre_docs = mysqli_result($select_statut_docs,0,0);
			$titre_docs = html_ent($titre_docs);
			echo "<div id=\"titre\">".$titre_docs."</div><br />\n";

	if (isset($_SESSION['log']) && $_SESSION['log'] == 1){
		$link_edit = $adminfolder."/admin_home.php?inc=documents";
		echo "<center><a href=\"".$link_edit."\"><b>".manage_folders."</b></a></center>";
	}
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"styles/radio_div.js\"></script>";
	confirmer();
	
	function file_size_convert($size_file) {
		if ($size_file >= 1048576)
			$size_file2 = round($size_file / 1048576,2)." ".mo;
		else if ($size_file >= 1024)
			$size_file2 = round($size_file / 1024,2)." ".ko;
		else
			$size_file2 = $size_file." ".octets;
		return $size_file2;
	}

 if (isset($_GET['do'])) $do = $_GET['do'];
 else $do="";
 switch ($do){
		
	// ****************** upload_file **************************
  case "upload_file" : {

		if (isset($_GET['id_folder']) && ctype_digit($_GET['id_folder']))
			$id_folder = intval($_GET['id_folder']);
		else $id_folder = 0;
		
		goback_lien("?documents");
		
		$select_folder_apps_up = $connect->query("select acces_folder,apps_upload from `" . $tblprefix . "folders` where id_folder = $id_folder;");
		if (mysqli_num_rows($select_folder_apps_up) > 0) {
			$acces_folder = mysqli_result($select_folder_apps_up,0,0);
			$apps_upload = mysqli_result($select_folder_apps_up,0,1);
			
			if ((isset($_SESSION['log']) && $_SESSION['log'] == 2 && $apps_upload == "1") || (isset($_SESSION['log']) && $_SESSION['log'] == 1)){
				if (isset($_SESSION['log']) && $_SESSION['log'] == 1)
					$type_usr_file = "u";
				else
					$type_usr_file = "l";
		
						// acces folder
				$acces = $acces_folder;
				$acces_valide = 0;
				if ($acces == "*")
					$acces_valide = 1;
				else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
					$acces_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$acces_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
					$select_classe = $connect->query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
				  if (mysqli_num_rows($select_classe) == 1){
						$id_classe = mysqli_result($select_classe,0);
						$tab_classes = explode("-",trim($acces,"-"));
						if (in_array($id_classe,$tab_classes))
							$acces_valide = 1;
					}
				}
				if ($acces_valide == 1){
					
		$extensions = array("swf","pdf","bmp","jpg","gif","png","flv","mp4","mp3","txt","doc","docx","xls","xlsx","ppt","pptx","pps","ppsx","zip","rar","rtf","odt","ods");
		$upload_max_filesize = @ini_get('upload_max_filesize');

		if(!empty($_FILES["uploaded_file"]) && !empty($_POST['random'])){
			if (!isset($_SESSION['upload_key']) || $_SESSION['upload_key'] != $_POST['random']){
				$_SESSION['upload_key'] = $_POST['random'];
				$filename = escape_string($_FILES['uploaded_file']['name']);
				$file_size = $_FILES["uploaded_file"]["size"];
 				if ($_FILES['uploaded_file']['error'] == 0) {
  				$ext = substr($filename, strrpos($filename, '.') + 1);
  				$ext = strtolower($ext);
  				if (in_array($ext, $extensions) && $_FILES['uploaded_file']['type'] != "application/octet-stream"){
  					$select_this_file = $connect->query("select id_file from `" . $tblprefix . "files` where nom_file = '$filename' and taille_file = $file_size and id_user = $id_user_session and type_user = '$type_usr_file';");
						if (mysqli_num_rows($select_this_file) == 0) {
  						$new_file = fonc_rand(24).".".$ext;
  						while (file_exists("docs/".$new_file))
  							$new_file = fonc_rand(24).".".$ext;
  						$destination = "docs/".$new_file;
							if ((@move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$destination))) {

								if (strpos($_FILES["uploaded_file"]["type"],"image")===0)
									$is_image = 1;
								else $is_image = 0;

								$time_upload = time();
 								$insertfile = "INSERT INTO `" . $tblprefix . "files` VALUES (NULL,$id_user_session,'$filename',$file_size,'$new_file',$time_upload,'$is_image','$type_usr_file',$id_folder);";
	        			$connect->query($insertfile,$connect);
	        			redirection(fichier_uploade,"?documents",3,"tips",0);
        			} else echo "<font color=\"red\"><b>".erreur_upload."</b></font><br />";
						} else echo "<font color=\"red\"><b>".erreur_upload_key."</b></font><br />";
  				} else echo "<font color=\"red\"><b>".erreur_upload_type."</b></font><br />";
 				}
				else {
						switch ($_FILES['uploaded_file']['error']){
  						case 1 : echo "<font color=\"red\"><b>".erreur_upload_1." : ".$upload_max_filesize."</b></font><br />";
								break;
  						case 2 : echo "<font color=\"red\"><b>".erreur_upload_2."</b></font><br />";
								break;
  						case 3 : echo "<font color=\"red\"><b>".erreur_upload_3."</b></font><br />";
								break;
  						case 4 : echo "<font color=\"red\"><b>".erreur_upload_4."</b></font><br />";
								break;
  						case 6 : echo "<font color=\"red\"><b>".erreur_upload_6."</b></font><br />";
								break;
  						case 7 : echo "<font color=\"red\"><b>".erreur_upload_7."</b></font><br />";
								break;
  						case 8 : echo "<font color=\"red\"><b>".erreur_upload_8."</b></font><br />";
								break;
  						default : echo "<font color=\"red\"><b>".erreur_upload_default."</b></font><br />";
						}
					}
			}	else echo "<font color=\"red\"><b>".erreur_upload_key."</b></font><br />";
		 }
		 else {
			echo "<br /><form enctype=\"multipart/form-data\" action=\"\" method=\"post\">";
			echo "<input name=\"uploaded_file\" type=\"file\" />";
			echo "<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" />";
			echo "<input type=\"submit\" value=\"".btnsend."\" /></form>";
			echo "<br /><ul>";
			if (!empty($upload_max_filesize))
				echo "<li><b>".taille_max." ".$upload_max_filesize."</b></li>";
			echo "<br /><li><b>".extentions_autorisees." : </b>";
			echo "<br />- ".type_file1;
			echo "<br />- ".type_file2;
			echo "<br />- ".type_file3;
			echo "<br />- ".type_file4;
			echo "<br />- ".type_file5;
			echo "<br />- ".type_file6;
			echo "</li></ul>";
		 }
		} else locationhref_admin("?documents");
		} else locationhref_admin("?documents");
	 } else locationhref_admin("?documents");
  } break;

  // ****************** delete_file **************************
  case "delete_file" : {
   if (isset($_SESSION['log'])){
  	if (isset($_GET['key']) && $_GET['key'] == $key){
  		if (isset($_GET['id_file']) && ctype_digit($_GET['id_file']))
				$id_file = intval($_GET['id_file']);
			else $id_file = 0;

			$select_file = $connect->query("select id_user,lien_file,type_user from `" . $tblprefix . "files` where id_file = $id_file;");
			if (mysqli_num_rows($select_file) == 1){
				
				$user_file = html_ent(mysqli_result($select_file,0,0));
		  	$lien_fichier = html_ent(mysqli_result($select_file,0,1));
		  	$type_user = html_ent(mysqli_result($select_file,0,2));

				if ($type_user == "u")
					$type_usr_file = 1;
				else if ($type_user == "l")
					$type_usr_file = 2;
				else $type_usr_file = 0;
				
				if ((isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")) || ($type_usr_file == $_SESSION['log'] && $user_file == $id_user_session) || ($_SESSION['log'] == 1 && $type_usr_file == 2)){
		  		$delete_file = $connect->query("delete from `" . $tblprefix . "files` where id_file = $id_file;");
		  		@unlink("docs/".$lien_fichier);
		  	}
    	}
		}
	 }
   locationhref_admin("?documents");
  } break;

  // ****************** rename_file **************************
  case "rename_file" : {
  	if (isset($_SESSION['log'])){
			if (!empty($_POST['id_file_edit']) && ctype_digit($_POST['id_file_edit']) && !empty($_POST['name_file_edit'])){
				$id_file_edit = escape_string($_POST['id_file_edit']);
				$name_file_edit = escape_string($_POST['name_file_edit']);
				
				$select_file_edit = $connect->query("select id_user,nom_file,type_user from `" . $tblprefix . "files` where id_file = $id_file_edit;");
				if (mysqli_num_rows($select_file_edit) > 0) {
					$user_file = html_ent(mysqli_result($select_file_edit,0,0));
		  		$file_edit = html_ent(mysqli_result($select_file_edit,0,1));
		  		$type_user = html_ent(mysqli_result($select_file_edit,0,2));

					if ($type_user == "u")
						$type_usr_file = 1;
					else if ($type_user == "l")
						$type_usr_file = 2;
					else $type_usr_file = 0;
				
				if ((isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")) || ($type_usr_file == $_SESSION['log'] && $user_file == $id_user_session) || ($_SESSION['log'] == 1 && $type_usr_file == 2)){
						$ext_file_edit = substr($file_edit, strrpos($file_edit, '.') + 1);
						$file_edit2 = substr($file_edit, 0, strrpos($file_edit, '.'));
						if ($file_edit2 != $name_file_edit){
							$name_file_edit = $name_file_edit.".".$ext_file_edit;
							$update_component = $connect->query("update `" . $tblprefix . "files` set nom_file = '$name_file_edit' where id_file = $id_file_edit;");
						}
					}
				}
			}
		}
    locationhref_admin("?documents");
  } break;
  
  // ****************** liste **************************
  default : {
  
  	$select_published_folders = $connect->query("select * from `" . $tblprefix . "folders` where publie_folder = '1' order by nom_folder ;");
		if (mysqli_num_rows($select_published_folders)> 0) {
			echo "<script type=\"text/javascript\">function DisplayHideDiv(id_div){var lay=document.getElementById(id_div);if(lay.style.display=='none'){lay.style.display='block';}else{lay.style.display='none';}}</script>";
			while($published_folder = mysqli_fetch_row($select_published_folders)){
				$id_folder = $published_folder[0];

    		$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $published_folder[1];");
    		if (mysqli_num_rows($select_auteur) == 1)
    			$auteur = html_ent(mysqli_result($select_auteur,0));
    		else $auteur = inconnu;

				$nom_folder = html_ent(trim($published_folder[2]));
				$date_creation = set_date($dateformat,$published_folder[4]);

				// acces folder
				$acces = $published_folder[3];
				$acces_valide = 0;
				if ($acces == "*")
					$acces_valide = 1;
				else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
					$acces_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$acces_valide = 1;
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
					$select_classe = $connect->query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
				  if (mysqli_num_rows($select_classe) == 1){
						$id_classe = mysqli_result($select_classe,0);
						$tab_classes = explode("-",trim($acces,"-"));
						if (in_array($id_classe,$tab_classes))
							$acces_valide = 1;
					}
				}
				if ($acces_valide == 1){

				echo "\n<br /><table border=\"0\"><tr><td><a href=\"javascript:DisplayHideDivImg('fol".$id_folder."')\" title=\"".open_folder."\"><img id=\"open_close_fol".$id_folder."\" border=\"0\" src=\"images/others/open.png\" width=\"32\" height=\"32\" /></a></td>";
				echo "<td><font size=\"4\"><b><a href=\"javascript:DisplayHideDivImg('fol".$id_folder."')\" title=\"".open_folder."\">".$nom_folder."</a></b></font></td></tr></table>";
				if ($afficher_profil == 1)
					echo "\n<div id=\"write_by\">".owner." : <a href=\"?profiles=".$published_folder[1]."\" title=\"".user_profile."\">".$auteur."</a>, ".cree." ".$date_creation."</div>";
				else echo "\n<div id=\"write_by\">".owner." : ".$auteur.", ".cree." ".$date_creation."</div>";
				echo "<div style=\"display: none;\" id=\"fol".$id_folder."\">";
				
				if (!empty($_SESSION['log']) && ($_SESSION['log'] == 1 || ($_SESSION['log'] == 2 && $published_folder[6] == "1")))
   				echo "<table border=\"0\" align=\"center\"><tr><td><a href=\"?documents&do=upload_file&id_folder=".$id_folder."\"><img border=\"0\" src=\"images/others/add.png\" /></a></td><td><a href=\"?documents&do=upload_file&id_folder=".$id_folder."\"><b>".upload_file."</b></a></td></tr></table>";

    $select_files = $connect->query("select * from `" . $tblprefix . "files` where id_folder = $id_folder order by date_file desc;");
    if (mysql_num_rows($select_files)> 0) {
    	echo "<br /><table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
			echo "\n<td class=\"affichage_table\"><b>".fichier."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".added_by."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".taille_fichier."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".date_ajout."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".miniature."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".download."</b></td>";
			if (isset($_SESSION['log'])){
				echo "\n<td class=\"affichage_table\"><b>".renommer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
			}
			echo "</tr>";

			while($fichier = mysql_fetch_row($select_files)){
				
				$id_owner = $fichier[1];
				$nom_fichier = html_ent($fichier[2]);
				$nom_fichier = substr($nom_fichier, 0, strrpos($nom_fichier, '.'));
				
				$taille_fichier = $fichier[3];
				$lien_fichier = html_ent($fichier[4]);
				$date_ajout = set_date($dateformat,$fichier[5]);

				if ($fichier[7] == "u")
					$type_usr_file = 1;
				else if ($fichier[7] == "l")
					$type_usr_file = 2;
				else $type_usr_file = 0;

				if(isset($_SESSION['log']) && $type_usr_file == $_SESSION['log'] && $id_owner == $id_user_session)
					echo "<tr bgcolor=\"#cccccc\">\n";
				else echo "<tr>\n";
				
				echo "\n<td class=\"affichage_table\"><b>".$nom_fichier."</b></td>";
				
				if ($type_usr_file == 2) {
					$select_user = $connect->query("select identifiant_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_owner;");
					$lien_profile = "s_profiles";
					$is_learner = " (".learner.")";
				}
				else {
    			$select_user = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $id_owner;");
    			$lien_profile = "profiles";
    			$is_learner = "";
    		}
    		if (mysqli_num_rows($select_user) == 1)
    			$user = html_ent(mysqli_result($select_user,0));
    		else $user = inconnu;
    		$user = wordwrap($user,15,"<br />",true);
    		if ($afficher_profil == 1)
					echo "\n<td class=\"affichage_table\"><a href=\"?".$lien_profile."=".$id_owner."\" title=\"".user_profile."\"><b>".$user."</b></a>".$is_learner."</td>";
				else echo "\n<td class=\"affichage_table\"><b>".$user."</b>".$is_learner."</td>";
				
				echo "\n<td class=\"affichage_table\">".file_size_convert($taille_fichier)."</td>";
				echo "\n<td class=\"affichage_table\">".$date_ajout."</td>";
					
				echo "\n<td class=\"affichage_table\">";
				if (file_exists("docs/".$lien_fichier)) {
					if ($fichier[6] == 1)
						echo "<img border=\"0\" src=\"docs/".$lien_fichier."\" alt=\"".$nom_fichier."\" width=\"70\" height=\"70\" />";
					else
						echo substr($lien_fichier, strrpos($lien_fichier, '.') + 1);
				} else
					echo "<font color=\"red\">".introuvable."</font>";
				echo "</td>";
				
				echo "\n<td class=\"affichage_table\"><a href=\"includes/download.php?f=".$lien_fichier."\" title=\"".download."\">".download."</a></td>";
				
			if (isset($_SESSION['log'])){
				if ((isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")) || ($type_usr_file == $_SESSION['log'] && $id_owner == $id_user_session) || ($_SESSION['log'] == 1 && $type_usr_file == 2)){
					echo "\n<td class=\"affichage_table\"><a href=\"javascript:DisplayHideDiv(".$fichier[0].")\" title=\"".renommer."\"><b>".renommer."</b></a>";
					echo "<div style=\"display: none;\" id=\"".$fichier[0]."\">";
					echo "<form method=\"POST\" action=\"?documents&do=rename_file\"><input type=\"text\" size=\"15\" name=\"name_file_edit\" value=\"".$nom_fichier."\"><br /><input type=\"hidden\" name=\"id_file_edit\" value=\"".$fichier[0]."\"><input type=\"submit\" class=\"button\" value=\"".btnsend."\"></form></div></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?documents&do=delete_file&id_file=".$fichier[0]."&key=".$key."','".confirm_supprimer_file."')\" title=\"".supprimer."\"><img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
				}
				else {
					echo "\n<td class=\"affichage_table\">---</td>";
					echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";
				}
			}
				echo "</tr>\n";
			}
			echo "\n</table>";
    } else echo aucun_fichier_trouve;
   	echo"</div><hr />";
   	
				}
			}
		} else echo no_published_folder."<br />";
  }
 }
} else accueil();
} else accueil();
?>