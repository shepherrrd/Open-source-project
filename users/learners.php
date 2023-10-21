<?php
/*
 * 	Manhali - Free Learning Management System
 *	learners.php
 *	2011-01-31 00:54
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session)){
	
	echo "<div id=\"titre\">".gestion_apprenants."</div>";

	if (isset($_GET['id_apprenant']) && ctype_digit($_GET['id_apprenant']))
		$id_apprenant = intval($_GET['id_apprenant']);
	else $id_apprenant = 0;

	if (!empty($_REQUEST['fonction_ad']) && (ctype_digit($_REQUEST['fonction_ad']) || $_REQUEST['fonction_ad'] == 'no'))
		$fonction_ad = $_REQUEST['fonction_ad'];
	else $fonction_ad = "---";

	if (isset($_REQUEST['search_usr']) && !empty($_REQUEST['search_usr']))
		$search_usr = escape_string($_REQUEST['search_usr']);
	else $search_usr = "";

	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){
		
		// ****************** add_apprenant **************************
		case "add_apprenant" : {
					
			// need classe
			$select_demande_classe = $connect->query("select demander_classe from `" . $tblprefix . "site_infos`;");
			if (mysqli_num_rows($select_demande_classe) == 1) {
				$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
				if (mysqli_num_rows($select_classes) > 0 && mysqli_result($select_demande_classe,0) == 1)
					$need_classe = 1;
				else $need_classe = 0;
			} else $need_classe = 0;
					
			if (!empty($_POST['send']) && !empty($_POST['random'])){
			 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
				$_SESSION['random_key'] = $_POST['random'];
				$name = trim($_POST['name']);
				$login = trim($_POST['login']);
				$password = trim($_POST['password']);
				$pass_conf = trim($_POST['pass_conf']);
				$email = trim($_POST['email']);
				if (!empty($login) && !empty($password) && !empty($pass_conf) && ($_POST['sexe'] == "M" || $_POST['sexe'] == "F")) {
				 if (!empty($_POST['classe_app']) && ctype_digit($_POST['classe_app']))
				 		$classe_app = $_POST['classe_app'];
				 	else $classe_app = 0;
				 	
	      	$name = escape_string($name);
	      	$login = special_chars($login);
	      	$login = escape_string($login);
	      	$password = escape_string($password);
	      	$pass_conf = escape_string($pass_conf);
	      	$email = escape_string($email);
					$jj = escape_string($_POST['jj']);
					$mm = escape_string($_POST['mm']);
					$yyyy = escape_string($_POST['yyyy']);
					$sexe = $_POST['sexe'];
					
					//photo
					if ($sexe == "M") $photo_app = "man.jpg";
					else if ($sexe == "F") $photo_app = "woman.jpg";
 					if(!empty($_FILES["photo"]) && $_FILES['photo']['error'] == 0) {
						$extensions = array("bmp","jpg","gif","png");
						$type_mime = array("bmp" => "image/bmp", "jpg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
						$type_mime2 = array("jpg" => "image/pjpeg", "png" => "image/x-png");
						$filename = $_FILES['photo']['name'];
  					$ext = substr($filename, strrpos($filename, '.') + 1);
  					$ext = strtolower($ext);
  					if (in_array($ext, $extensions) && ($_FILES["photo"]["type"] == $type_mime[$ext] || $_FILES["photo"]["type"] == $type_mime2[$ext])){
  						$new_file = fonc_rand(24).".".$ext;
  						while (file_exists("../docs/".$new_file))
  							$new_file = fonc_rand(24).".".$ext;
  						$destination = "../docs/".$new_file;
							if ((@move_uploaded_file($_FILES['photo']['tmp_name'],$destination)))
								$photo_app = $new_file;
        			else goback(erreur_upload,2,"error",1);
  					} else goback(erreur_upload_type,2,"error",1);
 					}
 					
					//auto activation
					$select_active = $connect->query("select activation_apprenants from `" . $tblprefix . "site_infos`;");
					if (mysqli_num_rows($select_active) == 1) {
						$activation = mysqli_result($select_active,0);
						if ($activation == 1)
							$active_app = 1;
						else $active_app = 0;
					} else $active_app = 0;
				
					if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && ctype_digit($yyyy) && $yyyy >= (date("Y",time()) - 65) && $yyyy <= (date("Y",time()) - 5)){
					 $naissance_app = $jj."/".$mm."/".$yyyy;
    			 $select_app_login = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
    			 $select_user_login = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 					 if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_login) == 0) {
						if (mail_valide($email) || empty($email)) {
	          	if ($password == $pass_conf) {
	            	if (strlen($password) >= 5) {
	              	$rndm = fonc_rand(8);
	                $rndm1 = substr($rndm,0,4);
	                $rndm2 = substr($rndm,4,4);
	                $crypt = md5($rndm2.$password.$rndm1);
	                $mdp = $crypt.$rndm;
	                $selectlanguage_site_info = $connect->query("select langue_site from `" . $tblprefix . "site_infos`;");
									if (mysqli_num_rows($selectlanguage_site_info) > 0)
										$language_site_info = escape_string(mysqli_result($selectlanguage_site_info,0));
									else $language_site_info = $language;
	                $connect->query ("INSERT INTO `" . $tblprefix . "apprenants` VALUES (NULL,".$classe_app.",'".$name."','".$login."','".$mdp."','".$email."','".$naissance_app."','".$active_app."','".$photo_app."','".$sexe."',".time().",0,'0','-','-','".$language_site_info."',0,0,0,0,0,0,".$id_user_session.",'-','E','','-');");
	               	redirection(apprenant_ajoute."<br />".identifiant." : ".html_ent($login),"?inc=learners",10,"tips",1);
	              } else goback(pass_court,2,"error",1);
	            } else goback(confirm_pass_err,2,"error",1);
	          } else goback(format_mail_err,2,"error",1);
	         } else goback(login_existe,2,"error",1);
	        } else goback(date_naissance_invalide,2,"error",1);
	      } else goback(remplir_champs_obligatoires,2,"error",1);
			 } else goback(err_data_saved,2,"error",1);
			}
			else {
						goback_button();
    				echo "<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<p><b>" .nom_complet. " : </b><br /><input name=\"name\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b><font color=\"red\">*</font> " .identifiant. " : </b><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b><font color=\"red\">*</font> " .password. " : </b><br /><input name=\"password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b><font color=\"red\">*</font> " .confirmpassword. " : </b><br /><input name=\"pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .email. " : </b><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"\"></p>";
	      		if ($need_classe == 1){
	      			echo "<p><b>" .classe. " : </b><br /><select name=\"classe_app\">";
	      			echo "<option value=\"0\"></option>";
    					while($classe = mysqli_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "</select></p>";
						}
						echo "<p><b>" .date_naissance." : </b><br /><select name=\"jj\">";
						for ($day = 1; $day <= 31; $day++)
							echo "<option value=\"".$day."\">".$day."</option>";
						echo "</select> <select name=\"mm\">";
						foreach ($month_tab as $key_m => $month)
    					echo "<option value=\"".$key_m."\">".$month."</option>";
						echo "</select> <select name=\"yyyy\">";
						for ($year = date("Y",time()) - 5; $year >= date("Y",time()) - 65; $year--)
							echo "<option value=\"".$year."\">".$year."</option>";
						echo "</select></p>";

						echo "<p><b>" .select_sex." : </b><br /><select name=\"sexe\">";
						echo "<option value=\"F\">".female."</option>";
						echo "<option value=\"M\">".male."</option>";
						echo "</select>";
						
						$upload_max_filesize = @ini_get('upload_max_filesize');
						echo "<p><b>" .photo_profil. " : </b><br />";
						echo "<input name=\"photo\" type=\"file\" />";
						echo "<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" />";
						echo "<br /><ul>";
						if (!empty($upload_max_filesize))
							echo "<li><b>".taille_max." ".$upload_max_filesize."</b></li>";
						echo "<li><b>".extentions_autorisees." : ".type_file1."</b></li>";
						echo "<li><b>".dimensions_recommandees."</b></li>";
						echo "</ul></p>";
						echo "<p><font color=\"red\"><b>* ".champs_obligatoires."</b></font><br /><br />";
	      		echo "<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
	    		}
		} break;

		// ****************** add_learners **************************
		case "add_learners" : {

			// need classe
			$select_demande_classe = $connect->query("select demander_classe from `" . $tblprefix . "site_infos`;");
			if (mysqli_num_rows($select_demande_classe) == 1 && mysqli_result($select_demande_classe,0) == 1)
					$need_classe = 1;
			else $need_classe = 0;
			$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
					
			if (!empty($_POST['send']) && !empty($_POST['random'])){
			 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
				$_SESSION['random_key'] = $_POST['random'];
				$liste_apprenants = $_POST['liste_apprenants'];
				if (!empty($liste_apprenants) && (isset($_POST['classe_app']) || $need_classe == 0)) {
					
					if (!empty($_POST['classe_app']) && ctype_digit($_POST['classe_app']))
				 		$classe_app = $_POST['classe_app'];
					else $classe_app = 0;
					
					//auto activation
					$select_active = $connect->query("select activation_apprenants from `" . $tblprefix . "site_infos`;");
					if (mysqli_num_rows($select_active) == 1) {
						$activation = mysqli_result($select_active,0);
						if ($activation == 1)
							$active_app = 1;
						else $active_app = 0;
					} else $active_app = 0;
					
				 	$liste = explode("\r\n",$liste_apprenants);
				 	$login_tab = array();
				 	$pass_tab = array();
				 	$apps_non_ajoute = array();
					foreach ($liste as $login){
						$login = trim($login);
						if (!empty($login)){
							$login = special_chars($login);
	            $login = escape_string($login);
							$password = fonc_rand(8);
							$rndm = fonc_rand(8);
	            $rndm1 = substr($rndm,0,4);
	            $rndm2 = substr($rndm,4,4);
	            $crypt = md5($rndm2.$password.$rndm1);
	            $mdp = $crypt.$rndm;
    					$select_app_login = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
    			 		$select_user_login = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 					 		if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_login) == 0) {
 					 			$selectlanguage_site_info = $connect->query("select langue_site from `" . $tblprefix . "site_infos`;");
								if (mysqli_num_rows($selectlanguage_site_info) > 0)
									$language_site_info = escape_string(mysqli_result($selectlanguage_site_info,0));
								else $language_site_info = $language;
 					 			if ($connect->query ("INSERT INTO `" . $tblprefix . "apprenants` VALUES (NULL,".$classe_app.",'','".$login."','".$mdp."','','','".$active_app."','man.jpg','M',".time().",0,'0','-','-','".$language_site_info."',0,0,0,0,0,0,".$id_user_session.",'-','E','','-');")){
 					 				$login_tab[] = $login;
 					 				$pass_tab[] = $password;
 					 			} else $apps_non_ajoute[] = $login;
 					 		} else $apps_non_ajoute[] = $login;
 						}
					}
					$chaine_logins = implode(";",$login_tab);
					$chaine_pwds = implode(";",$pass_tab);
					echo "<h3>".learners_added."</h3>";
					echo "<table width=\"100%\" align=\"center\" cellpadding=\"10\" style=\"border: 1px solid #000000;\">";
					$i = 0;
					while ($i < count($login_tab)){
						echo "<tr>";
						$j = 0;
						while ($i < count($login_tab) && $j < 3){
							echo "<td width=\"33%\" style=\"border: 1px solid #000000;\">".identifiant." : <b>".html_ent($login_tab[$i])."</b><br />".password." : <b>".$pass_tab[$i]."</b></td>";
							$i++;
							$j++;
						}
						echo "</tr>";
					}
					echo "</table><br />";
					echo "<form method=\"POST\" action=\"print.php\" TARGET=\"_BLANK\">";
	      	echo "<input type=\"hidden\" name=\"learners_login\" value=\"".$chaine_logins."\">";
	      	echo "<input type=\"hidden\" name=\"learners_pass\" value=\"".$chaine_pwds."\">";
	     		echo "<center><input type=\"submit\" class=\"button\" value=\"".print_save."\"></center></form>";
					if (count($apps_non_ajoute) > 0){
						echo "<hr /><h3>".learners_not_added."</h3>";
						foreach ($apps_non_ajoute as $login)
							echo "<b>- ".$login."</b><br />";
					}
	      } else goback(tous_champs,2,"error",1);
			 } else goback(err_data_saved,2,"error",1);
			}
			else {
						goback_button();
    				echo "<form method=\"POST\" action=\"\">";

	      		if ($need_classe == 1){
	      			echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
	      			echo "<b>" .classe. " : </b><br /><select name=\"classe_app\">";
	      			echo "<option value=\"0\">--------------</option>";
    					while($classe = mysqli_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "</select>";
						}
	      		echo "<p><b><u>" .learners_logins. " :</u></b><br />john_doe<br />jane_doe<br />...<br /><textarea name=\"liste_apprenants\" id=\"liste_apprenants\" rows=\"10\" cols=\"50\"></textarea></p>";
						echo "<p><font color=\"red\"><b>".tous_champs_obligatoires."</b></font><br /><br />";
	      		echo "<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
	    }
		} break;
		
   	// ****************** update_apprenant **************************
		case "update_apprenant" : {
				echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/radio_div.js\"></script>";
				$err_comp = 0;
				if($grade_user_session == "3" || $grade_user_session == "2")
					$select_apprenant = $connect->query("select * from `" . $tblprefix . "apprenants` where id_apprenant = $id_apprenant;");
				else $select_apprenant = $connect->query("select * from `" . $tblprefix . "apprenants` where id_apprenant = $id_apprenant and cree_par = $id_user_session;");
				
    		if (mysqli_num_rows($select_apprenant) == 1){
    			$apprenant = mysqli_fetch_row($select_apprenant);
					
					$classe_apprenant = $apprenant[1];
					$nom_apprenant = html_ent($apprenant[2]);
					$identifiant_apprenant = html_ent($apprenant[3]);
					$email_apprenant = html_ent($apprenant[5]);
					$naissance_apprenant = explode("/",$apprenant[6]);
					$photo_profil = $apprenant[8];
					$sexe_apprenant = $apprenant[9];
					
					// need classe
					$select_demande_classe = $connect->query("select demander_classe from `" . $tblprefix . "site_infos`;");
					if (mysqli_num_rows($select_demande_classe) == 1) {
						$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
						if (mysqli_num_rows($select_classes) > 0 && mysqli_result($select_demande_classe,0) == 1)
							$need_classe = 1;
						else $need_classe = 0;
					} else $need_classe = 0;
					
					if (!empty($_POST['send']) && !empty($_POST['random'])){
					 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
					 	$_SESSION['random_key'] = $_POST['random'];
					 	
// update identifiant
						$login = trim($_POST['login']);
						if (!empty($login)){
							$login = special_chars($login);
							$login = escape_string($login);
							if ($login != $identifiant_apprenant){
								$select_app_login = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login' and id_apprenant != $id_apprenant;");
								$select_user_id = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 								if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_id) == 0) {
 									$update_login = $connect->query("update `" . $tblprefix . "apprenants` set identifiant_apprenant = '$login' where id_apprenant = $id_apprenant;");
 								}
 								else {
 									$err_comp = 1;
 									goback(login_existe,2,"error",1);
 								} 							
 							}
 						}

// update nom
						$name = trim($_POST['name']);
 						if (!empty($name)){
							$name = escape_string($name);
							if ($name != $nom_apprenant){
 								$update_name = $connect->query("update `" . $tblprefix . "apprenants` set nom_apprenant = '$name' where id_apprenant = $id_apprenant;");
 							}
 						}

// update sexe
						$sexe = trim($_POST['sexe']);
						if ($sexe != $sexe_apprenant && ($sexe == "M" || $sexe == "F")){
							$danew_photo_profil = $photo_profil;
							if ($sexe == "M"){
								if ($photo_profil == "woman.jpg")
									$danew_photo_profil = "man.jpg";
 							}
 							else if ($sexe == "F"){
 								if ($photo_profil == "man.jpg")
 									$danew_photo_profil = "woman.jpg"; 
 							} 
 							$update_name = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '$danew_photo_profil', sexe_apprenant = '$sexe' where id_apprenant = $id_apprenant;");
 						}
 						
// update photo

if ($photo_profil == "man.jpg" || $photo_profil == "woman.jpg" || (isset($_POST['avatar']) && $_POST['avatar'] == "modifier")){
 	if(!empty($_FILES["uploaded_file"]) && $_FILES['uploaded_file']['error'] == 0) {
		$extensions = array("bmp","jpg","gif","png");
		$type_mime = array("bmp" => "image/bmp", "jpg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
		$type_mime2 = array("jpg" => "image/pjpeg", "png" => "image/x-png");
		$filename = $_FILES['uploaded_file']['name'];
  	$ext = substr($filename, strrpos($filename, '.') + 1);
  	$ext = strtolower($ext);
 		if (in_array($ext, $extensions) && ($_FILES["uploaded_file"]["type"] == $type_mime[$ext] || $_FILES["uploaded_file"]["type"] == $type_mime2[$ext])){
  		$new_file = fonc_rand(24).".".$ext;
  		while (file_exists("../docs/".$new_file))
				$new_file = fonc_rand(24).".".$ext;
  		$destination = "../docs/".$new_file;
			if ((@move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$destination))){
				$update_photo = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '$new_file' where id_apprenant = $id_apprenant;");
				if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg")
					@unlink("../docs/".$photo_profil);
    	}
      else {
	  		$err_comp = 1;
        goback(erreur_upload,2,"error",1);
     	}
  	}
  	else {
  		$err_comp = 1;
			goback(erreur_upload_type,2,"error",1);
  	}
	}
} else if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg" && isset($_POST['avatar']) && $_POST['avatar'] == "supprimer"){
	if ($sexe == "M") $photo_remove = "man.jpg";
	else if ($sexe == "F") $photo_remove = "woman.jpg";
	else {
		if ($sexe_apprenant == "M") $photo_remove = "man.jpg";
		else if ($sexe_apprenant == "F") $photo_remove = "woman.jpg";
	}
 	$update_photo = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '".$photo_remove."' where id_apprenant = $id_apprenant;");
	@unlink("../docs/".$photo_profil);
}
// update email
						$email = trim($_POST['email']);
 						if (!empty($email)){
							$email = escape_string($email);
							if ($email != $email_apprenant){
								if (mail_valide($email)){
 									$update_email = $connect->query("update `" . $tblprefix . "apprenants` set email_apprenant = '$email' where id_apprenant = $id_apprenant;");
 								}
 								else {
 									$err_comp = 1;
 									goback(format_mail_err,2,"error",1);
 								} 							
 							}
 						}

//update classe
				if ($need_classe == 1 && ctype_digit($_POST['classe_app'])){
				 	if ($_POST['classe_app'] != $classe_apprenant){
				 		$classe_app = $_POST['classe_app'];
				 		$update_classe = $connect->query("update `" . $tblprefix . "apprenants` set id_classe = $classe_app where id_apprenant = $id_apprenant;");
				 	}
				}

//update naissance
				if ($_POST['jj'] != $naissance_apprenant[0] || $_POST['mm'] != $naissance_apprenant[1] || $_POST['yyyy'] != $naissance_apprenant[2]){
					$jj = escape_string($_POST['jj']);
					$mm = escape_string($_POST['mm']);
					$yyyy = escape_string($_POST['yyyy']);
					if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && ctype_digit($yyyy) && $yyyy >= (date("Y",time()) - 65) && $yyyy <= (date("Y",time()) - 5)){
					 	$naissance_app = $jj."/".$mm."/".$yyyy;
						$update_naissance = $connect->query("update `" . $tblprefix . "apprenants` set naissance_apprenant = '$naissance_app' where id_apprenant = $id_apprenant;");
					}
					else {
						$err_comp = 1;
						goback(date_naissance_invalide,2,"error",1);
					}
				}

// update pass
						$new_password = trim($_POST['new_password']);
						$new_pass_conf = trim($_POST['new_pass_conf']);
 						if (!empty($new_password) && !empty($new_pass_conf)){
 							$new_password = escape_string($new_password);
	      			$new_pass_conf = escape_string($new_pass_conf);
 							if ($new_password == $new_pass_conf) {
 							 if (strlen($new_password) >= 5){
									$rndm = fonc_rand(8);
									$rndm1 = substr($rndm,0,4);
	                $rndm2 = substr($rndm,4,4);
									$crypt = md5($rndm2.$new_password.$rndm1);
									$mdp = $crypt.$rndm;
									$update_mdp = $connect->query("update `" . $tblprefix . "apprenants` set mdp_apprenant = '$mdp' where id_apprenant = $id_apprenant;");
 							 }
 							 else {
 							 	$err_comp = 1;
 							 	goback(pass_court,2,"error",1);
 							 }
 							}
 							else {
 								$err_comp = 1;
 								goback(confirm_pass_err,2,"error",1);
 							}
 						}
						
						if ($err_comp == 0) redirection(apprenant_modifie."<br />".identifiant." : ".html_ent($login),"?inc=learners",10,"tips",1);
					 
					 } else goback(err_data_saved,2,"error",1);
					}
					else {
						goback_button();
    				echo "<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<p><b>" .nom_complet. " : </b><br /><input name=\"name\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$nom_apprenant."\"></p>";
	      		echo "<p><b>" .identifiant. " : </b><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"".$identifiant_apprenant."\"></p>";
	      		echo "<p><b>" .new_password. " : </b><br /><input name=\"new_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b>" .confirmpassword. " : </b><br /><input name=\"new_pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .email. " : </b><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$email_apprenant."\"></p>";

	      		if ($need_classe == 1){
	      			echo "<p><b>" .classe. " : </b><br /><select name=\"classe_app\">";
	      			echo "<option value=\"0\"></option>";
    					while($classe = mysqli_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option ";
								if ($classe_apprenant==$id_classe) echo "selected=\"selected\" ";
								echo "value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "</select></p>";
						}
						echo "<p><b>" .date_naissance." : </b><br /><select name=\"jj\">";
						for ($day = 1; $day <= 31; $day++){
							echo "<option ";
							if (isset($naissance_user[0]) && $naissance_apprenant[0]==$day) echo "selected=\"selected\" ";
							echo "value=\"".$day."\">".$day."</option>";
						}
						echo "</select> <select name=\"mm\">";
						foreach ($month_tab as $key_m => $month){
    					echo "<option ";
    					if (isset($naissance_user[1]) && $naissance_apprenant[1]==$key_m) echo "selected=\"selected\" ";
    					echo "value=\"".$key_m."\">".$month."</option>";
    				}
						echo "</select> <select name=\"yyyy\">";
						for ($year = date("Y",time()) - 5; $year >= date("Y",time()) - 65; $year--){
							echo "<option ";
							if (isset($naissance_user[2]) && $naissance_apprenant[2]==$year) echo "selected=\"selected\" ";
							echo "value=\"".$year."\">".$year."</option>";
						}
						echo "</select></p>";
						
						if ($sexe_apprenant == "M") $chaine_m = " selected=\"selected\""; else $chaine_m = "";
						if ($sexe_apprenant == "F") $chaine_f = " selected=\"selected\""; else $chaine_f = "";
						echo "<p><b>" .select_sex." : </b><br /><select name=\"sexe\">";
						echo "<option value=\"F\"".$chaine_f.">".female."</option>";
						echo "<option value=\"M\"".$chaine_m.">".male."</option>";
						echo "</select>";
						
	      		$upload_max_filesize = @ini_get('upload_max_filesize');
						echo "<p><b>" .photo_profil. " : </b><br />";
						echo "<img border=\"0\" src=\"../docs/".$photo_profil."\" alt=\"".$nom_apprenant."\" width=\"100\" height=\"100\" /><br /><br />";
						if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg"){
							echo "\n<b><input name=\"avatar\" type=\"radio\" value=\"modifier\" onclick=\"DisplayHide('avatar_div', 'edit')\"> " .modifier_photo. "</b>";
    					echo "<div style=\"display: none; margin-left: 20px;\" class=\"avatar_div\" id=\"edit\">";
						}
						echo "<input name=\"uploaded_file\" type=\"file\" />";
						echo "<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" />";
						echo "<br /><ul>";
						if (!empty($upload_max_filesize))
							echo "<li><b>".taille_max." ".$upload_max_filesize."</b></li>";
						echo "<li><b>".extentions_autorisees." : ".type_file1."</b></li>";
						echo "<li><b>".dimensions_recommandees."</b></li>";
						echo "</ul>";
						if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg"){
							echo "</div>";
							echo "\n<b><input name=\"avatar\" type=\"radio\" value=\"supprimer\" onclick=\"DisplayHide('avatar_div', 'delete')\"> " .supprimer_photo. "</b>";
    					echo "<div style=\"display: none; margin-left: 20px;\" class=\"avatar_div\" id=\"delete\">";
							echo "</div>";
						}	
						echo "</p>";
	    			echo "<p><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   				}
			 } else locationhref_admin("?inc=learners");
		} break;

   	// ****************** delete_apprenant **************************
		case "delete_apprenant" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				if($grade_user_session == "3" || $grade_user_session == "2")
    			$delete_learner = $connect->query("delete from `" . $tblprefix . "apprenants` where id_apprenant = $id_apprenant;");
			}
			locationhref_admin("?inc=learners&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;

   	// ****************** activer_apprenant **************************
		case "activer_apprenant" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				if($grade_user_session == "3" || $grade_user_session == "2")
					$activer_learner = $connect->query("update `" . $tblprefix . "apprenants` set active_apprenant = '1' where id_apprenant = $id_apprenant;");
				else
    			$activer_learner = $connect->query("update `" . $tblprefix . "apprenants` set active_apprenant = '1' where id_apprenant = $id_apprenant and cree_par = $id_user_session;");
			}
			locationhref_admin("?inc=learners&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;

   	// ****************** desactiver_apprenant **************************
		case "desactiver_apprenant" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				if($grade_user_session == "3" || $grade_user_session == "2")
    			$desactiver_learner = $connect->query("update `" . $tblprefix . "apprenants` set active_apprenant = '0' where id_apprenant = $id_apprenant;");
    		else
    			$desactiver_learner = $connect->query("update `" . $tblprefix . "apprenants` set active_apprenant = '0' where id_apprenant = $id_apprenant and cree_par = $id_user_session;");
			}
			locationhref_admin("?inc=learners&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;
		
   	// ****************** liste_apprenants **************************	
		default : {
			confirmer();
			function return_selected($value, $champ){
					if ($champ == $value)
						return " selected=\"selected\"";
					else
						return "";
			}
			
			echo "<br /><table border=\"0\" align=\"center\" width=\"100%\"><tr><td align=\"right\" width=\"10%\"><a href=\"?inc=learners&do=add_apprenant\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td width=\"40%\"><a href=\"?inc=learners&do=add_apprenant\"><b>".ajouter_apprenant."</b></a></td>";
			echo "<td width=\"50%\" align=\"center\">";
    	$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
			if (mysqli_num_rows($select_classes) > 0){
    		echo "<form method=\"POST\" action=\"\"><b>".classe." : </b><select name=\"fonction_ad\" onchange=\"this.form.submit();\">";
	    	echo "<option value=\"\">".all."</option>";
	    	echo "<option value=\"no\"".return_selected("no",$fonction_ad).">---".no_class."---</option>";
	    	while($classe = mysqli_fetch_row($select_classes)){
    			$id_classe = $classe[0];
    			$nom_classe = $classe[1];
					echo "<option value=\"".$id_classe."\"".return_selected($id_classe,$fonction_ad).">".$nom_classe."</option>";
				}
	  		echo "</select></form>";
	  	}
    	echo "</td></tr><tr><td align=\"right\" width=\"10%\"><a href=\"?inc=learners&do=add_learners\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td width=\"40%\"><a href=\"?inc=learners&do=add_learners\"><b>".ajouter_liste_apprenant."</b></a></td>";
			echo "<td width=\"50%\" align=\"center\"><form method=\"POST\" action=\"\"><b>".identifiant_nom." : </b><input name=\"search_usr\" type=\"text\" maxlength=\"30\" size=\"10\" value=\"".$search_usr."\"> <input type=\"submit\" class=\"button\" value=\"" .rechercher. "\"></form></td></tr></table><br />";
			
			if ($fonction_ad == "no") $fonction_grade_ad = "and id_classe = 0";
			else if (ctype_digit($fonction_ad)) $fonction_grade_ad = "and id_classe = $fonction_ad";
			else $fonction_grade_ad = "";
			
			if (!empty($search_usr))
				$req_search_usr = "and (nom_apprenant like '%".$search_usr."%' or identifiant_apprenant like '%".$search_usr."%')";
			else $req_search_usr = "";
			
			// pour csv
			if ($fonction_ad == "no") $class_csv = "_no_class";
			else if (ctype_digit($fonction_ad)) {
				$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $fonction_ad;");
    	  if (mysqli_num_rows($select_classe) == 1)
    			$class_csv = special_chars("_".html_ent(mysqli_result($select_classe,0)));
    		else $class_csv = "_all";
			}
			else $class_csv = "_all";

			if (isset($_GET['l']) && ctype_digit($_GET['l']))
				$page = intval($_GET['l']);
			else $page = 1;
			if (isset($_GET['t']) && ctype_digit($_GET['t']))
				$page2 = intval($_GET['t']);
			else $page2 = 1;
	
			// activ�s
			echo "<hr /><a name=\"active\"><font color=\"black\"><b><u>- ".apprenants_actives." : </u></b></font></a><br /><br />";

	$select_apprenants = $connect->query("select * from `" . $tblprefix . "apprenants` where active_apprenant = '1' ".$fonction_grade_ad." ".$req_search_usr." order by id_classe;");
	$nbr_trouve = mysqli_num_rows($select_apprenants);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}
		
		include ("calculate_behavior_note.php");

		$select_apprenants_limit = $connect->query("select * from `" . $tblprefix . "apprenants` where active_apprenant = '1' ".$fonction_grade_ad." ".$req_search_usr." order by id_classe limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".photo_profil."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nom_complet."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".classe."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".behavior_score."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".last_connect."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".online."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				if($grade_user_session == "3" || $grade_user_session == "2")
					echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				$apprenants_actives_titre = "enabled_learners".$class_csv."_".$page.".csv";
				$apprenants_actives_file = fopen("../docs/".$apprenants_actives_titre,"w");
    		$apprenants_actives1  = "\"".identifiant."\";\"".nom_complet."\";\"".classe."\";\"".behavior_score."\";\"".behavior_grade."\";";
				$apprenants_actives1 = mb_convert_encoding($apprenants_actives1, 'ISO-8859-1', 'UTF-8');
				fwrite($apprenants_actives_file,$apprenants_actives1."\r\n");
			
				while($apprenant = mysqli_fetch_row($select_apprenants_limit)){
					
					$id_apprenant = $apprenant[0];
					
					$classe = $apprenant[1];
					$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $classe;");
    	  	if (mysqli_num_rows($select_classe) == 1)
    				$classe_apprenant = html_ent(mysqli_result($select_classe,0));
    			else $classe_apprenant = "---";
    			
					$nom_apprenant = html_ent($apprenant[2]);
					$identifiant_apprenant = html_ent($apprenant[3]);
					$identifiant_apprenant = wordwrap($identifiant_apprenant,15,"<br />",true);
					
					$active_apprenant = $apprenant[7];

					if (file_exists("../docs/".$apprenant[8]))
						$photo_apprenant = $apprenant[8];
					else {
						if ($apprenant[9] == "F") $photo_apprenant = "woman.jpg";
						else $photo_apprenant = "man.jpg";	
					}

					if ($apprenant[10] == 0)
						$date_inscription = never;
					else
						$date_inscription = set_date($dateformat,$apprenant[10]);
						
					if ($apprenant[11] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$apprenant[11]);
					
					$online = $apprenant[12];

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $apprenant[22];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = none;
    			$auteur = wordwrap($auteur,15,"<br />",true);
    			
    			$grade_behavior = html_ent($apprenant[24]);
    			
					$this_month = date("m",time());
					$this_year = date("Y",time());
					
   				$select_note_behavior = $connect->query("select behavior_note from `" . $tblprefix . "behavior_notes` where id_apprenant = $id_apprenant and mois_note = $this_month and annee_note = $this_year;");
      		if (mysqli_num_rows($select_note_behavior) > 0)
      			$note_finale_behavior = round(mysqli_result($select_note_behavior,0),2);
   				else $note_finale_behavior = 0;

    			$apprenants_actives2  = "\"".$identifiant_apprenant."\";\"".$nom_apprenant."\";\"".$classe_apprenant."\";\"".$note_finale_behavior." %\";\"".$grade_behavior."\";";
					$apprenants_actives2 = mb_convert_encoding($apprenants_actives2, 'ISO-8859-1', 'UTF-8');
					fwrite($apprenants_actives_file,$apprenants_actives2."\r\n");

					echo "<tr>\n";

					echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_apprenant."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_apprenant."\" title=\"".learner_profile."\"><img border=\"0\" src=\"../docs/".$photo_apprenant."\" alt=\"".$identifiant_apprenant."\" width=\"40\" height=\"40\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$nom_apprenant."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$classe_apprenant."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$note_finale_behavior." % (".$grade_behavior.")</b></td>";
					echo "\n<td class=\"affichage_table\"><b><a href=\"../?profiles=".$apprenant[22]."\" title=\"".user_profile."\">".$auteur."</a><br />".$date_inscription."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$last_connect."</b></td>";
					
					if ($online == 1)
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/valide.png\" width=\"32\" height=\"32\" /></td>";
					else
						echo "\n<td class=\"affichage_table\"><b>---</b></td>";
						
					if($grade_user_session == "3" || $grade_user_session == "2" || $apprenant[22] == $id_user_session)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=update_apprenant&id_apprenant=".$id_apprenant."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
					
					if($grade_user_session == "3" || $grade_user_session == "2")
						echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=learners&do=delete_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."','".confirm_supprimer_apprenant."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					
					if($grade_user_session == "3" || $grade_user_session == "2" || $apprenant[22] == $id_user_session){
						if ($active_apprenant == 1)
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=desactiver_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".desactiver."</b></a></td>";
						else
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=activer_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".activer."</b></a></td>";
					} else echo "\n<td class=\"affichage_table\">---</td>";
					
					echo "</tr>\n";
				}
				fclose ($apprenants_actives_file);
				echo "\n</table>";

				if (file_exists("../docs/".$apprenants_actives_titre))
					echo "<p align=\"center\"><a href=\"../includes/download.php?f=".$apprenants_actives_titre."\"><b>".download_learner_csv."</b></a></p>";
					
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=learners&l=".$page_precedente."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=learners&l=".$page_precedente."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=learners&l=".$i."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=learners&l=".$page_suivante."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=learners&l=".$page_suivante."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_apprenant."<br />";

			// d�sactiv�s
			echo "<hr /><a name=\"desactive\"><font color=\"black\"><b><u>- ".apprenants_desactives." : </u></b></font></a><br /><br />";

			$select_apprenants = $connect->query("select * from `" . $tblprefix . "apprenants` where active_apprenant = '0' ".$fonction_grade_ad." ".$req_search_usr." order by id_classe;");
			 $nbr_trouve = mysqli_num_rows($select_apprenants);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

			$select_apprenants_limit = $connect->query("select * from `" . $tblprefix . "apprenants` where active_apprenant = '0' ".$fonction_grade_ad." ".$req_search_usr." order by id_classe limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".photo_profil."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nom_complet."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".classe."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".behavior_score."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".last_connect."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".online."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				if($grade_user_session == "3" || $grade_user_session == "2")
					echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				$apprenants_desactives_titre = "disabled_learners".$class_csv."_".$page2.".csv";
				$apprenants_desactives_file = fopen("../docs/".$apprenants_desactives_titre,"w");
    		$apprenants_desactives1  = "\"".identifiant."\";\"".nom_complet."\";\"".classe."\";\"".behavior_score."\";\"".behavior_grade."\";";
				$apprenants_desactives1 = mb_convert_encoding($apprenants_desactives1, 'ISO-8859-1', 'UTF-8');
				fwrite($apprenants_desactives_file,$apprenants_desactives1."\r\n");
				
				while($apprenant = mysqli_fetch_row($select_apprenants_limit)){
					
					$id_apprenant = $apprenant[0];
					
					$classe = $apprenant[1];
					$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $classe;");
    	  	if (mysqli_num_rows($select_classe) == 1)
    				$classe_apprenant = html_ent(mysqli_result($select_classe,0));
    			else $classe_apprenant = "---";
    			
					$nom_apprenant = html_ent($apprenant[2]);
					$identifiant_apprenant = html_ent($apprenant[3]);
					$identifiant_apprenant = wordwrap($identifiant_apprenant,15,"<br />",true);
					
					$active_apprenant = $apprenant[7];

					if (file_exists("../docs/".$apprenant[8]))
						$photo_apprenant = $apprenant[8];
					else {
						if ($apprenant[9] == "F") $photo_apprenant = "woman.jpg";
						else $photo_apprenant = "man.jpg";	
					}

					if ($apprenant[10] == 0)
						$date_inscription = never;
					else
						$date_inscription = set_date($dateformat,$apprenant[10]);
						
					if ($apprenant[11] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$apprenant[11]);
					
					$online = $apprenant[12];

    			$select_auteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $apprenant[22];");
    			if (mysqli_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysqli_result($select_auteur,0));
    			else $auteur = none;
    			$auteur = wordwrap($auteur,15,"<br />",true);
    			
    			$grade_behavior = html_ent($apprenant[24]);
    			
					$this_month = date("m",time());
					$this_year = date("Y",time());
					
   				$select_note_behavior = $connect->query("select behavior_note from `" . $tblprefix . "behavior_notes` where id_apprenant = $id_apprenant and mois_note = $this_month and annee_note = $this_year;");
      		if (mysqli_num_rows($select_note_behavior) > 0)
      			$note_finale_behavior = round(mysqli_result($select_note_behavior,0),2);
					else $note_finale_behavior = 0;

    			$apprenants_desactives2  = "\"".$identifiant_apprenant."\";\"".$nom_apprenant."\";\"".$classe_apprenant."\";\"".$note_finale_behavior." %\";\"".$grade_behavior."\";";
					$apprenants_desactives2 = mb_convert_encoding($apprenants_desactives2, 'ISO-8859-1', 'UTF-8');
					fwrite($apprenants_desactives_file,$apprenants_desactives2."\r\n");
					
					echo "<tr>\n";

					echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_apprenant."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_apprenant."\" title=\"".learner_profile."\"><img border=\"0\" src=\"../docs/".$photo_apprenant."\" alt=\"".$identifiant_apprenant."\" width=\"40\" height=\"40\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$nom_apprenant."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$classe_apprenant."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$note_finale_behavior." % (".$grade_behavior.")</b></td>";
					echo "\n<td class=\"affichage_table\"><b><a href=\"../?profiles=".$apprenant[22]."\" title=\"".user_profile."\">".$auteur."</a><br />".$date_inscription."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$last_connect."</b></td>";
					
					if ($online == 1)
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/valide.png\" width=\"32\" height=\"32\" /></td>";
					else
						echo "\n<td class=\"affichage_table\"><b>---</b></td>";
					
					if($grade_user_session == "3" || $grade_user_session == "2" || $apprenant[22] == $id_user_session)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=update_apprenant&id_apprenant=".$id_apprenant."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
					
					if($grade_user_session == "3" || $grade_user_session == "2")
						echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=learners&do=delete_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."','".confirm_supprimer_apprenant."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					
					if($grade_user_session == "3" || $grade_user_session == "2" || $apprenant[22] == $id_user_session){
						if ($active_apprenant == 1)
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=desactiver_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".desactiver."</b></a></td>";
						else
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=learners&do=activer_apprenant&id_apprenant=".$id_apprenant."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".activer."</b></a></td>";
					} else echo "\n<td class=\"affichage_table\">---</td>";
					
					echo "</tr>\n";
				}
				fclose ($apprenants_desactives_file);
				echo "\n</table>";
				
				if (file_exists("../docs/".$apprenants_desactives_titre))
					echo "<p align=\"center\"><a href=\"../includes/download.php?f=".$apprenants_desactives_titre."\"><b>".download_learner_csv."</b></a></p>";
					
				if ($page_max > 1){
					$page_precedente = $page2 - 1;
					$page_suivante = $page2 + 1;
					echo "<table border=\"0\" align=\"center\"><tr>";
					if ($page_precedente >= 1)
						echo "<td><a href=\"?inc=learners&t=".$page_precedente."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=learners&t=".$page_precedente."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><b>".page_precedente."</b></a></td>";
					echo "<td>";
					for($i=1;$i<=$page_max;$i++){
						if ($i != $page2) echo "<a href=\"?inc=learners&t=".$i."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\">";
						echo "<b>".$i."</b>";
						if ($i != $page2) echo "</a>";
						echo "&nbsp; ";
					}
					echo "</td>";
					if ($page_suivante <= $page_max)
						echo "<td><a href=\"?inc=learners&t=".$page_suivante."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=learners&t=".$page_suivante."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "</tr></table>";
				}
  		 } else echo aucun_apprenant."<br />";
		}
	}
} else echo restricted_access;

?>