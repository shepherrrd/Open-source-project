<?php
/*
 * 	Manhali - Free Learning Management System
 *	documents.php
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1){

	echo "<div id=\"titre\">".gestion_electronique_documents."</div>";

	$select_statut_comp = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'documents';");
	if (mysqli_num_rows($select_statut_comp) == 1) {
 		$statut_comp = mysqli_result($select_statut_comp,0);
		if ($statut_comp == 0)
		 echo "<h3><img src=\"../images/icones/warning.png\" /><font color=\"red\">".component_disabled." ".enable_it_now." : </font><a href=\"?inc=components\"\">".gestion_composants."</a></h3>";
	}
	
	function file_size_convert($size_file) {
		if ($size_file >= 1048576)
			$size_file2 = round($size_file / 1048576,2)." ".mo;
		else if ($size_file >= 1024)
			$size_file2 = round($size_file / 1024,2)." ".ko;
		else
			$size_file2 = $size_file." ".octets;
		return $size_file2;
	}
	
	$initial_path = $_SERVER['PHP_SELF'];
	$initial_path2 = substr($initial_path,0,strrpos($initial_path,'/'.$adminfolder))."/docs/";
	
	if (isset($_GET['id_folder']) && ctype_digit($_GET['id_folder']))
		$id_folder = intval($_GET['id_folder']);
	else $id_folder = 0;
	
	confirmer();
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	switch ($do){
		

    // ****************** create_folder **************************
    case "create_folder" : {

    	if (isset($_POST['folder_name'])){
    		$folder_name = trim($_POST['folder_name']);
    		if (!empty($folder_name)){

    			$folder_name = escape_string($folder_name);
    			if ($_POST['acces'] == "learner")
    				$folder_acces = "0";
    			else if ($_POST['acces'] == "classe"){
    				if (!empty($_POST['classes']))
    					$folder_acces = "-".implode("-",$_POST['classes'])."-";
    				else $folder_acces = "0";
    			}
					else $folder_acces = "*";
    			
    			if (isset($_POST['apps_upload']) && ($_POST['apps_upload'] == 1 || $_POST['apps_upload'] == 0))
    				$apps_upload = $_POST['apps_upload'];
    			
    			$select_folder_name = $connect->query("select id_folder from `" . $tblprefix . "folders` where nom_folder = '$folder_name';");
 					if (mysqli_num_rows($select_folder_name) == 0) {
						
						$time_insert_folder = time();
 						$insertfolder = "INSERT INTO `" . $tblprefix . "folders` VALUES (NULL,$id_user_session,'$folder_name','$folder_acces',$time_insert_folder,'1','$apps_upload');";
	          $connect->query($insertfolder);

	          redirection(folder_cree,"?inc=documents",3,"tips",1);
 					} else goback(nom_dossier_existe,2,"error",1);
    		} else goback(remplir_champs_obligatoires,2,"error",1);
    	}
    	else {
    			goback_button();
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

    			echo "<form method=\"POST\" action=\"\">";
    			echo "<p><u><b><font color=\"red\">*</font> ".folder_name."</b></u><br /><br /><input name=\"folder_name\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";							

					echo "<p><u><b><font color=\"red\">*</font> ".acces_folder."</b></u><br /><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\" checked=\"checked\" /><b>".acces_ouvert."</b><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('classes',true)\" /><b>".acces_apprenants."</b><br />";
					echo "\n<input name=\"acces\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\" /><b>".acces_classes." :</b>";
    			$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
			 		if (mysqli_num_rows($select_classes) > 0){
					 	echo "<table border=\"0\"><tr><td align=\"center\">";
						echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
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
					
					echo "<p><u><b><font color=\"red\">*</font> ".autoriser_apps_upload."</b></u><input name=\"apps_upload\" type=\"radio\" checked=\"checked\" value=\"0\">".non;
					echo " <input name=\"apps_upload\" type=\"radio\" value=\"1\">".oui."</p>";
				
    			echo "<br /><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    			echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
   		}
    } break;

    // ****************** edit_folder **************************
    case "edit_folder" : {

    		$select_folder_complet = $connect->query("select * from `" . $tblprefix . "folders` where id_folder = $id_folder;");
    		if (mysqli_num_rows($select_folder_complet) == 1) {
    			$folder = mysqli_fetch_row($select_folder_complet);
    			if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $folder[1] == $id_user_session)){
    			$nom_folder_bd = html_ent($folder[2]);
					$acces_folder_bd = $folder[3];
					$apps_upload_bd = $folder[6];
					
    				if (!empty($_POST['send'])){

    					$folder_name = trim($_POST['folder_name']);
    					if (!empty($folder_name)){
        			
    						$folder_name = escape_string($folder_name);
    						if ($_POST['acces'] == "learner")
    							$folder_acces = "0";
    						else if ($_POST['acces'] == "classe"){
    							if (!empty($_POST['classes']))
    								$folder_acces = "-".implode("-",$_POST['classes'])."-";
    							else $folder_acces = "0";
    						}
								else $folder_acces = "*";
    						
    						if (isset($_POST['apps_upload']) && ($_POST['apps_upload'] == 1 || $_POST['apps_upload'] == 0))
    							$apps_upload = $_POST['apps_upload'];

    						$select_folder_name = $connect->query("select id_folder from `" . $tblprefix . "folders` where nom_folder = '$folder_name';");
 								if ((mysqli_num_rows($select_folder_name) == 0) || (mysqli_num_rows($select_folder_name) == 1 && mysqli_result($select_folder_name,0) == $folder[0])) {
 									$update_folder = "update `" . $tblprefix . "folders` SET nom_folder = '$folder_name', acces_folder = '$folder_acces', apps_upload = '$apps_upload' where id_folder = $id_folder;";
 									$connect->query($update_folder);
 									redirection(folder_modifie,"?inc=documents",3,"tips",1);
 								} else goback(nom_dossier_existe,2,"error",1);
    					} else goback(remplir_champs_obligatoires,2,"error",1);
    				}
    				else {
  						function checked_checked($var_bool,$value){
								if ($var_bool == $value)
									return " checked=\"checked\" ";
								else
									return " ";
							}
    					goback_button();
    					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";
    					
    					echo "<form method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> " .folder_name. "</b></u><br /><br /><input name=\"folder_name\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$nom_folder_bd."\"></p>";							
    					
							echo "<p><u><b><font color=\"red\">*</font> ".acces_folder."</b></u><br /><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_folder_bd == "*")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_ouvert."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_folder_bd == "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_apprenants."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\"";
							if ($acces_folder_bd != "*" && $acces_folder_bd != "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_classes." :</b>";
							$tab_classes = explode("-",$acces_folder_bd);
    					$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
					 		if (mysqli_num_rows($select_classes) > 0){
					 			echo "<table border=\"0\"><tr><td align=\"center\">";
					 			echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
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

							echo "<p><u><b><font color=\"red\">*</font> ".autoriser_apps_upload."</b></u><input name=\"apps_upload\" type=\"radio\"".checked_checked($apps_upload_bd,0)."value=\"0\">".non;
							echo " <input name=\"apps_upload\" type=\"radio\"".checked_checked($apps_upload_bd,1)."value=\"1\">".oui."</p>";
					
    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
    					echo "<br /><br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";

    					if ($acces_folder_bd == "*" || $acces_folder_bd == "0")
    						echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
    				}
    			} else locationhref_admin("?inc=documents");
    		} else locationhref_admin("?inc=documents");
    } break;

		// ****************** delete_folder **************************
		case "delete_folder" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_owner = $connect->query("select id_user from `" . $tblprefix . "folders` where id_folder = $id_folder;");
    		if (mysqli_num_rows($select_owner) == 1) {
    			$owner_folder = mysqli_result($select_owner,0);
    			if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $owner_folder == $id_user_session)){
    				$select_count_files = $connect->query("select count(id_file) from `" . $tblprefix . "files` where id_folder = $id_folder;");
    				if (mysqli_num_rows($select_count_files) == 1 && mysqli_result($select_count_files,0) == 0)
							$delete_folder = $connect->query("delete from `" . $tblprefix . "folders` where id_folder = $id_folder;");
					}
				}
			}
			locationhref_admin("?inc=documents");
		} break;

    // ****************** publier_folder *************************
    case "publier_folder" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_owner = $connect->query("select id_user from `" . $tblprefix . "folders` where id_folder = $id_folder;");
    		if (mysqli_num_rows($select_owner) == 1) {
    			$owner_folder = mysqli_result($select_owner,0);
    			if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $owner_folder == $id_user_session)){
    				$publier_folder = $connect->query("update `" . $tblprefix . "folders` set publie_folder = '1' where id_folder = $id_folder;");
    			}
    		}
    	}
    	locationhref_admin("?inc=documents");
    } break;

    // ****************** depublier_folder ***********************
    case "depublier_folder" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_owner = $connect->query("select id_user from `" . $tblprefix . "folders` where id_folder = $id_folder;");
    		if (mysqli_num_rows($select_owner) == 1) {
    			$owner_folder = mysqli_result($select_owner,0);
    			if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $owner_folder == $id_user_session)){
    				$depublier_folder = $connect->query("update `" . $tblprefix . "folders` set publie_folder = '0' where id_folder = $id_folder;");
    			}
    		}
    	}
    	locationhref_admin("?inc=documents");
    } break;

	// ****************** upload_file **************************
  case "upload_file" : {

		$extensions = array("swf","pdf","bmp","jpg","gif","png","flv","mp4","mp3","txt","doc","docx","xls","xlsx","ppt","pptx","pps","ppsx","zip","rar","rtf","odt","ods");

		$upload_max_filesize = @ini_get('upload_max_filesize');
		
		goback_lien("?inc=documents&do=open_folder&id_folder=".$id_folder);
		
		if(!empty($_FILES["uploaded_file"]) && !empty($_POST['random'])){
			if (!isset($_SESSION['upload_key']) || $_SESSION['upload_key'] != $_POST['random']){
				$_SESSION['upload_key'] = $_POST['random'];
				$filename = escape_string($_FILES['uploaded_file']['name']);
				$file_size = $_FILES["uploaded_file"]["size"];
 				if ($_FILES['uploaded_file']['error'] == 0) {
  				$ext = substr($filename, strrpos($filename, '.') + 1);
  				$ext = strtolower($ext);
  				if (in_array($ext, $extensions) && $_FILES['uploaded_file']['type'] != "application/octet-stream"){
  					$select_this_file = $connect->query("select id_file from `" . $tblprefix . "files` where nom_file = '$filename' and taille_file = $file_size and id_user = $id_user_session;");
						if (mysqli_num_rows($select_this_file) == 0) {
  						$new_file = fonc_rand(24).".".$ext;
  						while (file_exists("../docs/".$new_file))
  							$new_file = fonc_rand(24).".".$ext;
  						$destination = "../docs/".$new_file;
							if ((@move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$destination))) {

								if (strpos($_FILES["uploaded_file"]["type"],"image")===0)
									$is_image = 1;
								else $is_image = 0;

								$time_upload = time();
 								$insertfile = "INSERT INTO `" . $tblprefix . "files` VALUES (NULL,$id_user_session,'$filename',$file_size,'$new_file',$time_upload,'$is_image','u',$id_folder);";
	        			$connect->query($insertfile,$connect);
	        			redirection(fichier_uploade,"?inc=documents&do=open_folder&id_folder=".$id_folder,3,"tips",1);
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
  } break;

  // ****************** delete_file **************************
  case "delete_file" : {
  	if (isset($_GET['key']) && $_GET['key'] == $key){
  		if (isset($_GET['id_file']) && ctype_digit($_GET['id_file']))
				$id_file = intval($_GET['id_file']);
			else $id_file = 0;

			$select_file = $connect->query("select id_user,lien_file,type_user from `" . $tblprefix . "files` where id_file = $id_file;");
			if (mysqli_num_rows($select_file) == 1){
				
				$user_file = html_ent(mysqli_result($select_file,0,0));
		  	$lien_fichier = html_ent(mysqli_result($select_file,0,1));
		  	$type_usr_file = html_ent(mysqli_result($select_file,0,2));
		  	
		  	if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || ($type_usr_file == 'u' && $user_file == $id_user_session) || $type_usr_file == 'l')){
		  		$delete_file = $connect->query("delete from `" . $tblprefix . "files` where id_file = $id_file;");
		  		@unlink("../docs/".$lien_fichier);
		  	}
    	}
		}
    locationhref_admin("?inc=documents&do=open_folder&id_folder=".$id_folder);
  } break;

  // ****************** rename_file **************************
  case "rename_file" : {
			if (!empty($_POST['id_file_edit']) && ctype_digit($_POST['id_file_edit']) && !empty($_POST['name_file_edit'])){
				$id_file_edit = escape_string($_POST['id_file_edit']);
				$name_file_edit = escape_string($_POST['name_file_edit']);
				
				$select_file_edit = $connect->query("select id_user,nom_file,type_user from `" . $tblprefix . "files` where id_file = $id_file_edit;");
				if (mysqli_num_rows($select_file_edit) > 0) {
					$user_file = html_ent(mysqli_result($select_file_edit,0,0));
		  		$file_edit = html_ent(mysqli_result($select_file_edit,0,1));
		  		$type_usr_file = html_ent(mysqli_result($select_file_edit,0,2));
		  		
		  		if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || ($type_usr_file == 'u' && $user_file == $id_user_session) || $type_usr_file == 'l')){
						$ext_file_edit = substr($file_edit, strrpos($file_edit, '.') + 1);
						$file_edit2 = substr($file_edit, 0, strrpos($file_edit, '.'));
						if ($file_edit2 != $name_file_edit){
							$name_file_edit = $name_file_edit.".".$ext_file_edit;
							$update_component = $connect->query("update `" . $tblprefix . "files` set nom_file = '$name_file_edit' where id_file = $id_file_edit;");
						}
					}
				}
			}
    	locationhref_admin("?inc=documents&do=open_folder&id_folder=".$id_folder);
  } break;
  
  // ****************** open_folder **************************
  case "open_folder" : {
   	goback_lien("?inc=documents");
  	
  	$select_folder_name = $connect->query("select nom_folder from `" . $tblprefix . "folders` where id_folder = $id_folder;");
    if (mysqli_num_rows($select_folder_name) == 1){
    	$nom_dossier = html_ent(mysqli_result($select_folder_name,0));
    	echo "<center><h3>".$nom_dossier."</h3></center>";
		}

   echo "<table border=\"0\"><tr><td><a href=\"?inc=documents&do=upload_file&id_folder=".$id_folder."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=documents&do=upload_file&id_folder=".$id_folder."\"><b>".upload_file."</b></a></td></tr></table><br />";

   $max_len = 30;

	 function readmorefile($string,$length) {
	 	if(strlen($string) > $length)
	  	$string = substr($string,0,$length-7)."...".substr($string,-4,4);
		return $string;
	 }

	 if (isset($_GET['l']) && ctype_digit($_GET['l']))
	 		$page = intval($_GET['l']);
	 else
			$page = 1;

		echo "<script type=\"text/javascript\">function DisplayHideDiv(id_div){var lay=document.getElementById(id_div);if(lay.style.display=='none'){lay.style.display='block';}else{lay.style.display='none';}}</script>";

			
    $select_files = $connect->query("select * from `" . $tblprefix . "files` where id_folder = $id_folder order by date_file desc;");
    
    $nbr_trouve = mysqli_num_rows($select_files);
    if ($nbr_trouve > 0){
			$page_max = ceil($nbr_trouve / $nbr_resultats);
			if ($page <= $page_max && $page > 1 && $page_max > 1)
				$limit = ($page - 1) * $nbr_resultats;
			else {
				$limit = 0;
				$page = 1;
			}
    	$select_files_limit = $connect->query("select * from `" . $tblprefix . "files` where id_folder = $id_folder order by date_file desc limit $limit, $nbr_resultats;");

    	echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
			echo "\n<td class=\"affichage_table\"><b>".fichier."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".added_by."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".taille_fichier."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".lien_fichier."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".date_ajout."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".miniature."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".renommer."</b></td>";
			echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
			echo "</tr>";

			while($fichier = mysqli_fetch_row($select_files_limit)){
					
				$nom_fichier = html_ent($fichier[2]);
				$nom_fichier = substr($nom_fichier, 0, strrpos($nom_fichier, '.'));
				$nom_fichier = readmorefile($nom_fichier,$max_len);
				
				$taille_fichier = $fichier[3];
				$lien_fichier = html_ent($fichier[4]);
				$date_ajout = set_date($dateformat,$fichier[5]);
				
				$type_usr_file = $fichier[7];
				
				if ($fichier[1] == $id_user_session && $type_usr_file == 'u')
					echo "<tr bgcolor=\"#cccccc\">\n";
				else echo "<tr>\n";
				
				echo "\n<td class=\"affichage_table\"><b><a href=\"../includes/download.php?f=".$lien_fichier."\" title=\"".download."\">".$nom_fichier."</a></b></td>";
				
				if ($type_usr_file == 'l') {
					$select_user = $connect->query("select identifiant_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $fichier[1];");
					$lien_profile = "s_profiles";
					$is_learner = " (".learner.")";
				}
				else {
    			$select_user = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $fichier[1];");
    			$lien_profile = "profiles";
    			$is_learner = "";
    		}
    		if (mysqli_num_rows($select_user) == 1)
    			$user = html_ent(mysqli_result($select_user,0));
    		else $user = inconnu;
    		$user = wordwrap($user,15,"<br />",true);
				echo "\n<td class=\"affichage_table\"><a href=\"../?".$lien_profile."=".$fichier[1]."\" title=\"".user_profile."\"><b>".$user."</b></a>".$is_learner."</td>";
				
				echo "\n<td class=\"affichage_table\">".file_size_convert($taille_fichier)."</td>";
				echo "\n<td class=\"affichage_table\"><form><input type=\"text\" size=\"40\" onClick=\"select();\" value=\"".$initial_path2.$lien_fichier."\" readonly=\"readonly\"></form></td>";
				echo "\n<td class=\"affichage_table\">".$date_ajout."</td>";
					
				echo "\n<td class=\"affichage_table\">";
				if (file_exists("../docs/".$lien_fichier)) {
					if ($fichier[6] == 1)
						echo "<img border=\"0\" src=\"../docs/".$lien_fichier."\" alt=\"".$nom_fichier."\" width=\"70\" height=\"70\" />";
					else
						echo substr($lien_fichier, strrpos($lien_fichier, '.') + 1);
				} else
					echo "<font color=\"red\">".introuvable."</font>";
				echo "</td>";
				
				if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || ($type_usr_file == 'u' && $fichier[1] == $id_user_session) || $type_usr_file == 'l')){
					echo "\n<td class=\"affichage_table\"><a href=\"javascript:DisplayHideDiv(".$fichier[0].")\" title=\"".renommer."\"><b>".renommer."</b></a>";
					echo "<div style=\"display: none;\" id=\"".$fichier[0]."\">";
					echo "<form method=\"POST\" action=\"?inc=documents&do=rename_file&id_folder=".$id_folder."\"><input type=\"text\" size=\"15\" name=\"name_file_edit\" value=\"".$nom_fichier."\"><br /><input type=\"hidden\" name=\"id_file_edit\" value=\"".$fichier[0]."\"><input type=\"submit\" class=\"button\" value=\"".btnsend."\"></form></div></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=documents&do=delete_file&id_folder=".$id_folder."&id_file=".$fichier[0]."&key=".$key."','".confirm_supprimer_file."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
				}
				else {
					echo "\n<td class=\"affichage_table\">---</td>";
					echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";
				}
				echo "</tr>\n";
			}
			echo "\n</table>";
			
			if ($page_max > 1){
				$page_precedente = $page - 1;
				$page_suivante = $page + 1;
  			echo "<br /><table border=\"0\" align=\"center\"><tr>";
				if ($page_precedente >= 1)
					echo "<td><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
				echo "<td>";
				for($i=1;$i<=$page_max;$i++){
					if ($i != $page)
						echo "<a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."&l=".$i."\">";
					echo "<b>".$i."</b>";
					if ($i != $page)
						echo "</a>";
					echo "&nbsp; ";
				}
				echo "</td>";
				if ($page_suivante <= $page_max)
					echo "<td><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";

				echo "</tr></table>";
			}
    } else echo aucun_fichier_trouve;
  } break;

   	// ****************** liste_folder **************************	
		default : {

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	if (isset($_GET['t']) && ctype_digit($_GET['t']))
		$page2 = intval($_GET['t']);
	else $page2 = 1;
	
    	echo "<table border=\"0\"><tr><td><a href=\"?inc=documents&do=create_folder\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=documents&do=create_folder\"><b>".create_folder."</b></a></td></tr></table>";

    // publi�s
    	echo "<hr /><a name=\"published\"><b><u>- ".published_folders." : </u></b></a><br /><br />";

  $select_published_folders = $connect->query("select * from `" . $tblprefix . "folders` where publie_folder = '1' order by nom_folder ;");
	$nbr_trouve = mysqli_num_rows($select_published_folders);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    	$select_published_folders_limit = $connect->query("select * from `" . $tblprefix . "folders` where publie_folder = '1' order by nom_folder limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".folder_name."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".owner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".acces_folder."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nbr_files."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".apps_upload."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($folder = mysqli_fetch_row($select_published_folders_limit)){
					
					$id_folder = $folder[0];

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $folder[1];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);

					$nom_folder = html_ent(trim($folder[2]));
					$nom_folder = readmore($nom_folder,70);
					
					if ($folder[3] == "*")
						$acces_folder = acces_ouvert;
					else if ($folder[3] == "0")
						$acces_folder = all_registered_learners;
					else {
						$acces_folder = classe." : ";
						$tab_acces_folder = explode("-",trim($folder[3],"-"));
						if (!empty($tab_acces_folder[0])){
							$chaine_acces_folder = implode(",",$tab_acces_folder);
							$select_classes = $connect->query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_folder.");");
							if (mysqli_num_rows($select_classes) > 0){
    						while($classe = mysqli_fetch_row($select_classes))
    							$acces_folder .= "<u>".$classe[1]."</u>, ";
    						$acces_folder = substr($acces_folder,0,-2);
    					}
    				}
					}
					
					$date_creation = set_date($dateformat,$folder[4]);

    			$select_count_files = $connect->query("select count(id_file) from `" . $tblprefix . "files` where id_folder = $id_folder;");
    			if (mysqli_num_rows($select_count_files) == 1)
    				$count_files = mysqli_result($select_count_files,0);
    			else $count_files = "---";
    			
					if ($folder[6] == "1")
						$apps_upload = oui;
					else $apps_upload = non;
					
					if ($folder[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><b><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."\" title=\"".open_folder."\">".$nom_folder."</a></b></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$folder[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$acces_folder."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$date_creation."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$count_files."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$apps_upload."</b></td>";
					
					if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $folder[1] == $id_user_session)){
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=documents&do=edit_folder&id_folder=".$id_folder."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
						if ($count_files == 0)
							echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=documents&do=delete_folder&id_folder=".$id_folder."&key=".$key."','".confirm_supprimer_folder."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
						else echo "\n<td class=\"affichage_table\">".not_empty."</td>";
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=documents&do=depublier_folder&id_folder=".$id_folder."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					}
					else {
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";
						echo "\n<td class=\"affichage_table\">---</td>";
					}
					echo "</tr>\n";
				}
				echo "\n</table>";
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=documents&l=".$page_precedente."&t=".$page2."#published\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=documents&l=".$page_precedente."&t=".$page2."#published\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=documents&l=".$i."&t=".$page2."#published\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=documents&l=".$page_suivante."&t=".$page2."#published\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=documents&l=".$page_suivante."&t=".$page2."#published\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo no_published_folder."<br />";

    // non publi�s
    	echo "<br /><hr /><a name=\"unpublished\"><b><u>- ".unpublished_folders." : </u></b></a><br /><br />";

    	$select_unpublished_folders = $connect->query("select * from `" . $tblprefix . "folders` where publie_folder = '0' order by nom_folder ;");
			 $nbr_trouve = mysqli_num_rows($select_unpublished_folders);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

    	$select_unpublished_folders_limit = $connect->query("select * from `" . $tblprefix . "folders` where publie_folder = '0' order by nom_folder limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".folder_name."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".owner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".acces_folder."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nbr_files."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".apps_upload."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($folder = mysqli_fetch_row($select_unpublished_folders_limit)){
					
					$id_folder = $folder[0];

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $folder[1];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);

					$nom_folder = html_ent(trim($folder[2]));
					$nom_folder = readmore($nom_folder,70);
					
					if ($folder[3] == "*")
						$acces_folder = acces_ouvert;
					else if ($folder[3] == "0")
						$acces_folder = all_registered_learners;
					else {
						$acces_folder = classe." : ";
						$tab_acces_folder = explode("-",trim($folder[3],"-"));
						if (!empty($tab_acces_folder[0])){
							$chaine_acces_folder = implode(",",$tab_acces_folder);
							$select_classes = $connect->query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_folder.");");
							if (mysqli_num_rows($select_classes) > 0){
    						while($classe = mysqli_fetch_row($select_classes))
    							$acces_folder .= "<u>".$classe[1]."</u>, ";
    						$acces_folder = substr($acces_folder,0,-2);
    					}
    				}
					}
					
					$date_creation = set_date($dateformat,$folder[4]);

    			$select_count_files = $connect->query("select count(id_file) from `" . $tblprefix . "files` where id_folder = $id_folder;");
    			if (mysqli_num_rows($select_count_files) == 1)
    				$count_files = mysqli_result($select_count_files,0);
    			else $count_files = "---";
    			
					if ($folder[6] == "1")
						$apps_upload = oui;
					else $apps_upload = non;
					
					if ($folder[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><b><a href=\"?inc=documents&do=open_folder&id_folder=".$id_folder."\" title=\"".open_folder."\">".$nom_folder."</a></b></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$folder[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$acces_folder."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$date_creation."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$count_files."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$apps_upload."</b></td>";
					
					if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $folder[1] == $id_user_session)){
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=documents&do=edit_folder&id_folder=".$id_folder."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
						if ($count_files == 0)
							echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=documents&do=delete_folder&id_folder=".$id_folder."&key=".$key."','".confirm_supprimer_folder."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
						else echo "\n<td class=\"affichage_table\">".not_empty."</td>";
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=documents&do=publier_folder&id_folder=".$id_folder."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";
					}
					else {
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";
						echo "\n<td class=\"affichage_table\">---</td>";
					}
					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=documents&t=".$page_precedente."&l=".$page."#unpublished\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=documents&t=".$page_precedente."&l=".$page."#unpublished\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=documents&t=".$i."&l=".$page."#unpublished\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=documents&t=".$page_suivante."&l=".$page."#unpublished\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=documents&t=".$page_suivante."&l=".$page."#unpublished\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo no_unpublished_folder."<br />";
		}
	}
} else echo restricted_access;

?>