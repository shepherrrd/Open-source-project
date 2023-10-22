<?php
/*
 * 	Manhali - Free Learning Management System
 *	perso_infos.php
 *	2009-05-07 23:48
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "1" || $grade_user_session == "0")){

			echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/radio_div.js\"></script>";
			
			echo "<div id=\"titre\">".modifier_perso."</div><br />";
			
			echo "<a href=\"../?profiles\" title=\"".user_profile."\"><b>".user_profile."</b></a><br />";
			
			$err_comp = 0;
			
			$select_user = $connect->query("select * from `" . $tblprefix . "users` where id_user = $id_user_session;");
    	if (mysqli_num_rows($select_user) == 1){
    		$user = mysqli_fetch_row($select_user);
					
					$id_user = $user[0];
					$nom_user = html_ent($user[1]);
					$identifiant_user = html_ent($user[2]);
					$mdp_user = $user[3];
					$email_user = html_ent($user[4]);
					$photo_profil = $user[7];
					$sexe_user = $user[8];
					
					if (!empty($_POST['send']) && !empty($_POST['random'])){
					 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
					 	$_SESSION['random_key'] = $_POST['random'];
					 	
// update identifiant
						$login = trim($_POST['login']);
						if (!empty($login)){
							$login = escape_string($login);
							if ($login != $identifiant_user){
								$select_app_login = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
								$select_user_id = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login' and id_user != $id_user;");
 								if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_id) == 0) {
 									$update_login = $connect->query("update `" . $tblprefix . "users` set identifiant_user = '$login' where id_user = $id_user;");
 								}
 								else {
 									$err_comp = 1;
 									goback(login_existe,2,"error",1);
 								} 							
 							}
 						}

// update nom
						$name = trim($_POST['name']);
						$name = escape_string($name);
						if ($name != $nom_user){
 							$update_name = $connect->query("update `" . $tblprefix . "users` set nom_user = '$name' where id_user = $id_user;");
 						}

// update sexe
						$sexe = trim($_POST['sexe']);
						if ($sexe != $sexe_user && ($sexe == "M" || $sexe == "F")){
							$danew_photo_profil = $photo_profil;
							if ($sexe == "M"){
								if ($photo_profil == "woman.jpg")
									$danew_photo_profil = "man.jpg";
 							}
 							else if ($sexe == "F"){
 								if ($photo_profil == "man.jpg")
 									$danew_photo_profil = "woman.jpg"; 
 							} 
 							$update_name = $connect->query("update `" . $tblprefix . "users` set photo_profil = '$danew_photo_profil', sexe_user = '$sexe' where id_user = $id_user;");
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
				$update_photo = $connect->query("update `" . $tblprefix . "users` set photo_profil = '$new_file' where id_user = $id_user;");
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
		if ($sexe_user == "M") $photo_remove = "man.jpg";
		else if ($sexe_user == "F") $photo_remove = "woman.jpg";
	}
 	$update_photo = $connect->query("update `" . $tblprefix . "users` set photo_profil = '".$photo_remove."' where id_user = $id_user;");
	@unlink("../docs/".$photo_profil);
}
// update email
						$email = trim($_POST['email']);
						$email = escape_string($email);
						if ($email != $email_user){
							if (mail_valide($email) || $email == ""){
 								$update_email = $connect->query("update `" . $tblprefix . "users` set email_user = '$email' where id_user = $id_user;");
 							}
 							else {
 								$err_comp = 1;
 								goback(format_mail_err,2,"error",1);
 							} 							
 						}
 						
// update pass
						$new_password = trim($_POST['new_password']);
						$new_pass_conf = trim($_POST['new_pass_conf']);
						$old_password = trim($_POST['old_password']);
 						if (!empty($new_password) && !empty($new_pass_conf) && !empty($old_password)){
 							$new_password = escape_string($new_password);
	      			$new_pass_conf = escape_string($new_pass_conf);
	      			$old_password = escape_string($old_password);
 							if ($new_password == $new_pass_conf) {
 							 if (strlen($new_password) >= 5){
										$passpart1 = substr($mdp_user,0,32);
										$passpart2 = substr($mdp_user,32,4);
										$passpart3 = substr($mdp_user,36,4);
										if ($passpart1 == md5($passpart3.$old_password.$passpart2)){
											$rndm = fonc_rand(8);
	                    $rndm1 = substr($rndm,0,4);
	                    $rndm2 = substr($rndm,4,4);
	                    $crypt = md5($rndm2.$new_password.$rndm1);
	                    $mdp = $crypt.$rndm;
	                    $update_mdp = $connect->query("update `" . $tblprefix . "users` set mdp_user = '$mdp' where id_user = $id_user;");
										}
 							 			else {
											$err_comp = 1;
											goback(old_mdp_invalide,2,"error",1);
										}
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
						
						if ($err_comp == 0) redirection(infos_modifies,"?inc=perso_infos",3,"tips",1);
					 
					 } else goback(err_data_saved,2,"error",1);
					}
					else {

    				echo "<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<p><b>" .nom_complet. " : </b><br /><input name=\"name\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$nom_user."\"></p>";
	      		echo "<p><b>" .identifiant. " : </b><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"".$identifiant_user."\"></p>";
	      		echo "<p><b>" .old_password. " : </b><br /><input name=\"old_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .new_password. " : </b><br /><input name=\"new_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b>" .confirmpassword. " : </b><br /><input name=\"new_pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .email. " : </b><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$email_user."\"></p>";

						if ($sexe_user == "M") $chaine_m = " selected=\"selected\""; else $chaine_m = "";
						if ($sexe_user == "F") $chaine_f = " selected=\"selected\""; else $chaine_f = "";
						echo "<p><b><font color=\"red\">*</font> " .select_sex." : </b><br /><select name=\"sexe\">";
						echo "<option value=\"0\"></option>";
						echo "<option value=\"F\"".$chaine_f.">".female."</option>";
						echo "<option value=\"M\"".$chaine_m.">".male."</option>";
						echo "</select>";
						
	      		$upload_max_filesize = @ini_get('upload_max_filesize');
						echo "<p><b>" .photo_profil. " : </b><br />";
						echo "<img border=\"0\" src=\"../docs/".$photo_profil."\" alt=\"".$nom_user."\" width=\"100\" height=\"100\" /><br /><br />";
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
			} else locationhref_admin("?inc=perso_infos");
} else echo restricted_access;

?>