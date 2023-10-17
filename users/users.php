<?php
/*
 * 	Manhali - Free Learning Management System
 *	users.php
 *	2009-04-19 13:34
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

	echo "<div id=\"titre\">".gestion_utilisateurs."</div>";

	if (isset($_GET['id_user']) && ctype_digit($_GET['id_user']))
		$id_user = intval($_GET['id_user']);
	else $id_user = 0;

	if (!empty($_REQUEST['fonction_ad']) && ($_REQUEST['fonction_ad'] == "a" || $_REQUEST['fonction_ad'] == "b" || $_REQUEST['fonction_ad'] == "c" || $_REQUEST['fonction_ad'] == "d"))
		$fonction_ad = $_REQUEST['fonction_ad'];
	else $fonction_ad = "";

	if (isset($_REQUEST['search_usr']) && !empty($_REQUEST['search_usr']))
		$search_usr = escape_string($_REQUEST['search_usr']);
	else $search_usr = "";
	
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";

	switch ($do){

		// ****************** add_user **************************
		case "add_user" : {
			if (!empty($_POST['send']) && !empty($_POST['random'])){
			 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
				$_SESSION['random_key'] = $_POST['random'];
				$name = trim($_POST['name']);
				$login = trim($_POST['login']);
				$password = trim($_POST['password']);
				$pass_conf = trim($_POST['pass_conf']);
				$email = trim($_POST['email']);
				$groupe = trim($_POST['groupe']);
				if (!empty($login) && !empty($password) && !empty($pass_conf) && ctype_digit($groupe) && ($_POST['sexe'] == "M" || $_POST['sexe'] == "F")) {
	      	$name = escape_string($name);
	      	$login = special_chars($login);
	      	$login = escape_string($login);
	      	$password = escape_string($password);
	      	$pass_conf = escape_string($pass_conf);
	      	$email = escape_string($email);
	      	$groupe = escape_string($groupe);
					$sexe = $_POST['sexe'];
					
					//photo
					if ($sexe == "M") $photo_user = "man.jpg";
					else if ($sexe == "F") $photo_user = "woman.jpg";
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
								$photo_user = $new_file;
        			else goback(erreur_upload,2,"error",1);
  					} else goback(erreur_upload_type,2,"error",1);
 					}
    			$select_app_login = mysql_query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
    			$select_user_login = mysql_query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 					if (mysql_num_rows($select_app_login) == 0 && mysql_num_rows($select_user_login) == 0) {
						if (mail_valide($email) || empty($email)) {
	          	if ($password == $pass_conf) {
	            	if (strlen($password) >= 5) {
	              	$rndm = fonc_rand(8);
	                $rndm1 = substr($rndm,0,4);
	                $rndm2 = substr($rndm,4,4);
	                $crypt = md5($rndm2.$password.$rndm1);
	                $mdp = $crypt.$rndm;
	                
	                if (($grade_user_session == "3" && ($groupe == "2" || $groupe == "1" || $groupe == "0")) || ($grade_user_session == "2" && ($groupe == "1" || $groupe == "0"))){
										$selectlanguage_site_info = mysql_query("select langue_site from `" . $tblprefix . "site_infos`;");
										if (mysql_num_rows($selectlanguage_site_info) > 0)
											$language_site_info = escape_string(mysql_result($selectlanguage_site_info,0));
										else $language_site_info = $language;
	                	$insertuser = "INSERT INTO `" . $tblprefix . "users` VALUES (NULL, '".$name."', '".$login."', '".$mdp."', '".$email."','1','".$groupe."','".$photo_user."','".$sexe."',".time().",0,'0','".$language_site_info."','-','-',0,0,0,0);";
	               		mysql_query($insertuser,$connect);
	               		redirection(user_ajoute."<br />".identifiant." : ".html_ent($login),"?inc=users",10,"tips",1);
	               	} else goback(groupe_invalide,2,"error",1);
	              } else goback(pass_court,2,"error",1);
	            } else goback(confirm_pass_err,2,"error",1);
	          } else goback(format_mail_err,2,"error",1);
	        } else goback(login_existe,2,"error",1);
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
	     	
	     	echo "<p><b><font color=\"red\">*</font> " .groupe. " : </b><br /><select name=\"groupe\">";
	     	echo "<option selected=\"selected\" value=\"0\">".trainer."</option>";
	     	echo "<option value=\"1\">".supervisor."</option>";
	     	if ($grade_user_session == "3")
	     		echo "<option value=\"2\">".admin."</option>";
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

   	// ****************** update_user **************************
		case "update_user" : {
			
			echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/radio_div.js\"></script>";
			$err_comp = 0;
			$select_user = mysql_query("select * from `" . $tblprefix . "users` where id_user = $id_user;");
    	if (mysql_num_rows($select_user) == 1){
    		$user = mysql_fetch_row($select_user);
				if ($grade_user_session == "3" || $id_user == $id_user_session || $user[6] == "0" || $user[6] == "1") {
					
					$nom_user = html_ent($user[1]);
					$identifiant_user = html_ent($user[2]);
					$mdp_user = $user[3];
					$email_user = html_ent($user[4]);
					$grade_user = $user[6];
					$photo_profil = $user[7];
					$sexe_user = $user[8];
					
					if (!empty($_POST['send']) && !empty($_POST['random'])){
					 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
					 	$_SESSION['random_key'] = $_POST['random'];
					 	
// update identifiant
						$login = trim($_POST['login']);
						if (!empty($login)){
							$login = special_chars($login);
							$login = escape_string($login);
							if ($login != $identifiant_user){
								$select_app_login = mysql_query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
								$select_user_id = mysql_query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login' and id_user != $id_user;");
 								if (mysql_num_rows($select_app_login) == 0 && mysql_num_rows($select_user_id) == 0) {
 									$update_login = mysql_query("update `" . $tblprefix . "users` set identifiant_user = '$login' where id_user = $id_user;");
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
 							$update_name = mysql_query("update `" . $tblprefix . "users` set nom_user = '$name' where id_user = $id_user;");
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
 							$update_name = mysql_query("update `" . $tblprefix . "users` set photo_profil = '$danew_photo_profil', sexe_user = '$sexe' where id_user = $id_user;");
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
				$update_photo = mysql_query("update `" . $tblprefix . "users` set photo_profil = '$new_file' where id_user = $id_user;");
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
 	$update_photo = mysql_query("update `" . $tblprefix . "users` set photo_profil = '".$photo_remove."' where id_user = $id_user;");
	@unlink("../docs/".$photo_profil);
}

// update email
						$email = trim($_POST['email']);
						$email = escape_string($email);
						if ($email != $email_user){
							if (mail_valide($email) || $email == ""){
 								$update_email = mysql_query("update `" . $tblprefix . "users` set email_user = '$email' where id_user = $id_user;");
 							}
 							else {
 								$err_comp = 1;
 								goback(format_mail_err,2,"error",1);
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
 								if ($id_user == $id_user_session){
 									$old_password = trim($_POST['old_password']);
 									if (!empty($old_password)){
 										$old_password = escape_string($old_password);
										$passpart1 = substr($mdp_user,0,32);
										$passpart2 = substr($mdp_user,32,4);
										$passpart3 = substr($mdp_user,36,4);
										if ($passpart1 == md5($passpart3.$old_password.$passpart2)){
											$rndm = fonc_rand(8);
	                    $rndm1 = substr($rndm,0,4);
	                    $rndm2 = substr($rndm,4,4);
	                    $crypt = md5($rndm2.$new_password.$rndm1);
	                    $mdp = $crypt.$rndm;
	                    $update_mdp = mysql_query("update `" . $tblprefix . "users` set mdp_user = '$mdp' where id_user = $id_user;");
										}
										else {
											$err_comp = 1;
											goback(old_mdp_invalide,2,"error",1);
										}
 									}
 									else {
 										$err_comp = 1;
 										goback(old_mdp_invalide,2,"error",1);
 									}
 								}
 								else {
 									$rndm = fonc_rand(8);
	                $rndm1 = substr($rndm,0,4);
	                $rndm2 = substr($rndm,4,4);
	                $crypt = md5($rndm2.$new_password.$rndm1);
	              	$mdp = $crypt.$rndm;
 									$update_mdp = mysql_query("update `" . $tblprefix . "users` set mdp_user = '$mdp' where id_user = $id_user;");
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

// update grade
						if ($id_user != $id_user_session && ctype_digit($_POST['groupe'])){
							$groupe = escape_string($_POST['groupe']);
							if ($groupe != $grade_user) {
								if ($grade_user_session == "3"){
									if ($groupe == "2" || $groupe == "1" || $groupe == "0") {
										$update_grade = mysql_query("update `" . $tblprefix . "users` set grade_user = '$groupe' where id_user = $id_user;");
									}
									else {
										$err_comp = 1;
										goback(groupe_invalide,2,"error",1);
									}
	            	}
	            	else {
									if ($groupe == "1" || $groupe == "0") {
										$update_grade = mysql_query("update `" . $tblprefix . "users` set grade_user = '$groupe' where id_user = $id_user;");
									}
									else {
										$err_comp = 1;
										goback(groupe_invalide,2,"error",1);
									}
	            	}
	          	}
						}
						if ($err_comp == 0) redirection(user_modifie."<br />".identifiant." : ".html_ent($login),"?inc=users",10,"tips",1);
					 } else goback(err_data_saved,2,"error",1);
					}
					else {
						function select_grade($grade,$champ){
							if ($grade == $champ)
								return " selected=\"selected\"";
						}
						
						goback_button();

    				echo "<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<p><b>" .nom_complet. " : </b><br /><input name=\"name\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$nom_user."\"></p>";	      		
	      		echo "<p><b>" .identifiant. " : </b><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"".$identifiant_user."\"></p>";
	      		
	      		if ($id_user == $id_user_session)
	      			echo "<p><b>" .old_password. " : </b><br /><input name=\"old_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";

	      		echo "<p><b>" .new_password. " : </b><br /><input name=\"new_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b>" .confirmpassword. " : </b><br /><input name=\"new_pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .email. " : </b><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$email_user."\"></p>";

						if ($sexe_user == "M") $chaine_m = " selected=\"selected\""; else $chaine_m = "";
						if ($sexe_user == "F") $chaine_f = " selected=\"selected\""; else $chaine_f = "";
						echo "<p><b>" .select_sex." : </b><br /><select name=\"sexe\">";
						echo "<option value=\"0\"></option>";
						echo "<option value=\"F\"".$chaine_f.">".female."</option>";
						echo "<option value=\"M\"".$chaine_m.">".male."</option>";
						echo "</select>";
						
						if ($id_user != $id_user_session) {
	     				echo "<p><b>" .groupe. " : </b><br /><select name=\"groupe\">";
	     				echo "<option value=\"0\"".select_grade($grade_user,"0").">".trainer."</option>";
	     				echo "<option value=\"1\"".select_grade($grade_user,"1").">".supervisor."</option>";
	     				if ($grade_user_session == "3")
	     					echo "<option value=\"2\"".select_grade($grade_user,"2").">".admin."</option>";
	    				echo "</select></p>";
						}

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
				} else locationhref_admin("?inc=users");
			} else locationhref_admin("?inc=users");
		} break;

   	// ****************** delete_user **************************
		case "delete_user" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_grade_user = mysql_query("select grade_user from `" . $tblprefix . "users` where id_user = $id_user;");
    		if (mysql_num_rows($select_grade_user) == 1){
    			$grade_user = mysql_result($select_grade_user,0,0);
    			if (($grade_user_session == "3" && $id_user != $id_user_session) || $grade_user == "0" || $grade_user == "1")
    				$delete_user = mysql_query("delete from `" . $tblprefix . "users` where id_user = $id_user;");
				}
			}
			locationhref_admin("?inc=users&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;

   	// ****************** activer_user **************************
		case "activer_user" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_grade_user = mysql_query("select grade_user from `" . $tblprefix . "users` where id_user = $id_user;");
    		if (mysql_num_rows($select_grade_user) == 1){
    			$grade_user = mysql_result($select_grade_user,0,0);
    			if (($grade_user_session == "3" && $id_user != $id_user_session) || $grade_user == "0" || $grade_user == "1")
    				$activer_user = mysql_query("update `" . $tblprefix . "users` set active_user = '1' where id_user = $id_user;");
				}
			}
			locationhref_admin("?inc=users&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;

   	// ****************** desactiver_user **************************
		case "desactiver_user" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_grade_user = mysql_query("select grade_user from `" . $tblprefix . "users` where id_user = $id_user;");
    		if (mysql_num_rows($select_grade_user) == 1){
    			$grade_user = mysql_result($select_grade_user,0,0);
    			if (($grade_user_session == "3" && $id_user != $id_user_session) || $grade_user == "0" || $grade_user == "1")
    				$desactiver_user = mysql_query("update `" . $tblprefix . "users` set active_user = '0' where id_user = $id_user;");
				}
			}
			locationhref_admin("?inc=users&fonction_ad=".$fonction_ad."&search_usr=".$search_usr);
		} break;
		
   	// ****************** liste_users **************************	
		default : {
			
			confirmer();

			function return_selected($value, $champ){
					if ($champ == $value)
						return " selected=\"selected\"";
					else
						return "";
			}
    	echo "<table border=\"0\" align=\"center\" width=\"100%\"><tr><td align=\"right\" width=\"10%\"><a href=\"?inc=users&do=add_user\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td width=\"40%\"><a href=\"?inc=users&do=add_user\"><b>".ajouter_utilisateur."</b></a></td>";
    	echo "<td width=\"50%\" align=\"center\"><form method=\"POST\" action=\"\"><b>".groupe." : </b><select name=\"fonction_ad\" onchange=\"this.form.submit();\">";
	    echo "<option value=\"\">".all."</option>";
	    echo "<option value=\"a\"".return_selected('a',$fonction_ad).">".trainer."</option>";
	   	echo "<option value=\"b\"".return_selected('b',$fonction_ad).">".supervisor."</option>";
	   	echo "<option value=\"c\"".return_selected('c',$fonction_ad).">".admin."</option>";
	    echo "<option value=\"d\"".return_selected('d',$fonction_ad).">".superadmin."</option>";
	  	echo "</select></form><br />";
	  	echo "<form method=\"POST\" action=\"\"><b>".identifiant_nom." : </b><input name=\"search_usr\" type=\"text\" maxlength=\"30\" size=\"10\" value=\"".$search_usr."\"> <input type=\"submit\" class=\"button\" value=\"" .rechercher. "\"></form>";
    	echo "</td></tr></table><br /><br />";

			if ($fonction_ad == "a") $fonction_grade_ad = "and grade_user = '0'";
			else if ($fonction_ad == "b") $fonction_grade_ad = "and grade_user = '1'";
			else if ($fonction_ad == "c") $fonction_grade_ad = "and grade_user = '2'";
			else if ($fonction_ad == "d") $fonction_grade_ad = "and grade_user = '3'";
			else $fonction_grade_ad = "";

			if (!empty($search_usr))
				$req_search_usr = "and (nom_user like '%".$search_usr."%' or identifiant_user like '%".$search_usr."%')";
			else $req_search_usr = "";
			
	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	if (isset($_GET['t']) && ctype_digit($_GET['t']))
		$page2 = intval($_GET['t']);
	else $page2 = 1;
	
			// activés
    	echo "<hr /><a name=\"active\"><font color=\"black\"><b><u>- ".utilisateurs_actives." : </u></b></font></a><br /><br />";

	$select_users = mysql_query("select * from `" . $tblprefix . "users` where active_user = '1' ".$fonction_grade_ad." ".$req_search_usr." order by grade_user desc;");
	$nbr_trouve = mysql_num_rows($select_users);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}
		$select_users_limit = mysql_query("select * from `" . $tblprefix . "users` where active_user = '1' ".$fonction_grade_ad." ".$req_search_usr." order by grade_user desc limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".photo_profil."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nom_complet."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".groupe."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".last_connect."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".online."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				
				while($user = mysql_fetch_row($select_users_limit)){
					
					$id_user = $user[0];
					$nom_user = html_ent($user[1]);
					$identifiant_user = html_ent($user[2]);
					$identifiant_user = wordwrap($identifiant_user,20,"<br />",true);
					
					$active_user = $user[5];
					$groupe_user = $grade_tab[$user[6]];

					if (file_exists("../docs/".$user[7]))
						$photo_user = $user[7];
					else {
						if ($user[8] == "F") $photo_user = "woman.jpg";
						else $photo_user = "man.jpg";	
					}
					
					if ($user[10] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$user[10]);
					
					$online = $user[11];
					
					if ($id_user == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					if ($active_user == 1)
						$color = "green";
					else $color = "red";
					
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\"><b>".$identifiant_user."</b></a></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\"><img border=\"0\" src=\"../docs/".$photo_user."\" alt=\"".$identifiant_user."\" width=\"40\" height=\"40\" /></a></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$nom_user."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$groupe_user."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$last_connect."</b></font></td>";
					
					if ($online == 1)
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/valide.png\" width=\"32\" height=\"32\" /></td>";
					else
						echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>---</b></font></td>";
					
					if ($grade_user_session == "3" || $id_user == $id_user_session || $user[6] == "0" || $user[6] == "1")
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=update_user&id_user=".$id_user."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
					
					if (($grade_user_session == "3" && $id_user != $id_user_session) || $user[6] == "0" || $user[6] == "1")
						echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=users&do=delete_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."','".confirm_supprimer_user."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";

					if (($grade_user_session == "3" && $id_user != $id_user_session) || $user[6] == "0" || $user[6] == "1") {
						if ($active_user == 1)
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=desactiver_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".desactiver."</b></a></td>";
						else
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=activer_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".activer."</b></a></td>";
					}
					else
							echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>---</b></font></td>";
					
					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=users&l=".$page_precedente."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=users&l=".$page_precedente."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=users&l=".$i."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=users&l=".$page_suivante."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=users&l=".$page_suivante."&t=".$page2."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#active\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_utilisateur."<br />";

			// désactivés
			echo "<br /><hr /><a name=\"desactive\"><font color=\"black\"><b><u>- ".utilisateurs_desactives." : </u></b></font></a><br /><br />";

			$select_users = mysql_query("select * from `" . $tblprefix . "users` where active_user = '0' ".$fonction_grade_ad." ".$req_search_usr." order by grade_user desc;");
			 $nbr_trouve = mysql_num_rows($select_users);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

			$select_users_limit = mysql_query("select * from `" . $tblprefix . "users` where active_user = '0' ".$fonction_grade_ad." ".$req_search_usr." order by grade_user desc limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".photo_profil."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".nom_complet."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".groupe."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".last_connect."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".online."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				
				while($user = mysql_fetch_row($select_users_limit)){
					
					$id_user = $user[0];
					$nom_user = html_ent($user[1]);
					$identifiant_user = html_ent($user[2]);
					$identifiant_user = wordwrap($identifiant_user,20,"<br />",true);
					
					$active_user = $user[5];
					$groupe_user = $grade_tab[$user[6]];

					if (file_exists("../docs/".$user[7]))
						$photo_user = $user[7];
					else {
						if ($user[8] == "F") $photo_user = "woman.jpg";
						else $photo_user = "man.jpg";	
					}
					
					if ($user[10] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$user[10]);
					
					$online = $user[11];
					
					if ($id_user == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					if ($active_user == 1)
						$color = "green";
					else $color = "red";
					
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\"><b>".$identifiant_user."</b></a></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\"><img border=\"0\" src=\"../docs/".$photo_user."\" alt=\"".$identifiant_user."\" width=\"40\" height=\"40\" /></a></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$nom_user."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$groupe_user."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$last_connect."</b></font></td>";
					
					if ($online == 1)
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/valide.png\" width=\"32\" height=\"32\" /></td>";
					else
						echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>---</b></font></td>";
					
					if ($grade_user_session == "3" || $id_user == $id_user_session || $user[6] == "0" || $user[6] == "1")
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=update_user&id_user=".$id_user."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/noedit.png\" width=\"32\" height=\"32\" /></td>";
					
					if (($grade_user_session == "3" && $id_user != $id_user_session) || $user[6] == "0" || $user[6] == "1")
						echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=users&do=delete_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."','".confirm_supprimer_user."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><img border=\"0\" src=\"../images/others/delete2.png\" width=\"32\" height=\"32\" /></td>";

					if (($grade_user_session == "3" && $id_user != $id_user_session) || $user[6] == "0" || $user[6] == "1") {
						if ($active_user == 1)
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=desactiver_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".desactiver."</b></a></td>";
						else
							echo "\n<td class=\"affichage_table\"><a href=\"?inc=users&do=activer_user&id_user=".$id_user."&key=".$key."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."\"><b>".activer."</b></a></td>";
					}
					else
							echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>---</b></font></td>";
					
					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=users&t=".$page_precedente."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=users&t=".$page_precedente."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=users&t=".$i."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=users&t=".$page_suivante."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=users&t=".$page_suivante."&l=".$page."&fonction_ad=".$fonction_ad."&search_usr=".$search_usr."#desactive\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_utilisateur."<br />";
		}
	}
} else echo restricted_access;

?>