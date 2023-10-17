<?php
/*
 * 	Manhali - Free Learning Management System
 *	register_inc.php
 *	2011-01-27 12:46
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
	
	$select_identification = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'identification';");
	if (mysql_num_rows($select_identification) == 1) {
		$identification = mysql_result($select_identification,0);
		if ($identification == 1){
			$select_inscription = mysql_query("select inscription from `" . $tblprefix . "site_infos`;");
			if (mysql_num_rows($select_inscription) == 1) {
				$inscription = mysql_result($select_inscription,0);
				if ($inscription == 1 && !isset($_SESSION['log']) && !isset($_SESSION['key'])){
					echo "<div id=\"titre\">".registration."</div>\n";
					
					// need classe
					$select_demande_classe = mysql_query("select demander_classe from `" . $tblprefix . "site_infos`;");
					if (mysql_num_rows($select_demande_classe) == 1) {
						$select_classes = mysql_query("select * from `" . $tblprefix . "classes`;");
						if (mysql_num_rows($select_classes) > 0 && mysql_result($select_demande_classe,0) == 1)
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
				if (!empty($name) && !empty($login) && !empty($password) && !empty($pass_conf) && !empty($email) && ($_POST['sexe'] == "M" || $_POST['sexe'] == "F")) {
				 if ($need_classe == 0 || ($need_classe == 1 && ctype_digit($_POST['classe_app']))){
				 	if ($need_classe == 1)
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
					$langue_home = escape_string($_POST['langue_home']);
					
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
  						while (file_exists("docs/".$new_file))
  							$new_file = fonc_rand(24).".".$ext;
  						$destination = "docs/".$new_file;
							if ((@move_uploaded_file($_FILES['photo']['tmp_name'],$destination)))
								$photo_app = $new_file;
        			else goback(erreur_upload,2,"error",0);
  					} else goback(erreur_upload_type,2,"error",0);
 					}
 					
					//auto activation
					$select_active = mysql_query("select activation_apprenants from `" . $tblprefix . "site_infos`;");
					if (mysql_num_rows($select_active) == 1) {
						$activation = mysql_result($select_active,0);
						if ($activation == 1)
							$active_app = 1;
						else $active_app = 0;
					} else $active_app = 0;
				
					if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && ctype_digit($yyyy) && $yyyy >= (date("Y",time()) - 65) && $yyyy <= (date("Y",time()) - 5)){
					 $naissance_app = $jj."/".$mm."/".$yyyy;
    			 $select_app_login = mysql_query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login';");
    			 $select_user_login = mysql_query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 					 if (mysql_num_rows($select_app_login) == 0 && mysql_num_rows($select_user_login) == 0) {
						if (mail_valide($email)) {
	          	if ($password == $pass_conf) {
	            	if (strlen($password) >= 5) {
	              	$rndm = fonc_rand(8);
	                $rndm1 = substr($rndm,0,4);
	                $rndm2 = substr($rndm,4,4);
	                $crypt = md5($rndm2.$password.$rndm1);
	                $mdp = $crypt.$rndm;
	                mysql_query ("INSERT INTO `" . $tblprefix . "apprenants` VALUES (NULL,".$classe_app.",'".$name."','".$login."','".$mdp."','".$email."','".$naissance_app."','".$active_app."','".$photo_app."','".$sexe."',".time().",0,'0','-','-','".$langue_home."',0,0,0,0,0,0,0,'-','E','','-');");
	               	redirection(user_ajoute."<br />".identifiant." : ".html_ent($login),"?",10,"tips",0);
	              } else goback(pass_court,2,"error",0);
	            } else goback(confirm_pass_err,2,"error",0);
	          } else goback(format_mail_err,2,"error",0);
	         } else goback(login_existe,2,"error",0);
	        } else goback(date_naissance_invalide,2,"error",0);
	       } else goback(champs_obligatoires_sauf_photo,2,"error",0);
	      } else goback(champs_obligatoires_sauf_photo,2,"error",0);
			 } else goback(err_data_saved,2,"error",0);
			}
			else {
				echo "\n<script type=\"text/javascript\">
					function mail_valid(mail_value){
						var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,5})$/;
						if(reg.test(mail_value)) return true;
						else return false;
					}
					function valider(){
						var name = document.getElementById('name').value;
						var login = document.getElementById('login').value;
						var password = document.getElementById('password').value;
						var pass_conf = document.getElementById('pass_conf').value;
						var email = document.getElementById('email').value;
						var sexe = document.getElementById('sexe').value;
						
						if (name=='' || login=='' || password=='' || pass_conf=='' || email=='' || sexe==0)
							alert (\"".champs_obligatoires_sauf_photo."\");
						else if (!mail_valid(email))
							alert (\"".format_mail_err."\");
						else if (password.length<5)
							alert (\"".pass_court."\");
						else if (password != pass_conf)
							alert(\"".confirm_pass_err."\");
						else document.form_registr.submit();
					}
				</script>";
    				echo "<form method=\"POST\" name=\"form_registr\" id=\"form_registr\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<br /><table width=\"100%\" align=\"center\"><tr><td width=\"50%\" valign=\"top\"><fieldset>";
    				echo "<p><b><font color=\"red\">*</font> " .nom_complet. " : </b><br /><input name=\"name\" id=\"name\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b><font color=\"red\">*</font> " .identifiant. " : </b><br /><input name=\"login\" id=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b><font color=\"red\">*</font> " .password. " : </b><br /><input name=\"password\" id=\"password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b><font color=\"red\">*</font> " .confirmpassword. " : </b><br /><input name=\"pass_conf\" id=\"pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b><font color=\"red\">*</font> " .email. " : </b><br /><input name=\"email\" id=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"\"></p>";

						echo "</fieldset></td><td width=\"50%\" valign=\"top\"><fieldset>";
						
	      		if ($need_classe == 1){
	      			echo "<p><b><font color=\"red\">*</font> " .classe. " : </b><br /><select name=\"classe_app\" id=\"classe_app\">";
	      			echo "<option value=\"0\"></option>";
    					while($classe = mysql_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "</select></p>";
						}
						echo "<p><b><font color=\"red\">*</font> " .date_naissance." : </b><br /><select name=\"jj\">";
						for ($day = 1; $day <= 31; $day++)
							echo "<option value=\"".$day."\">".$day."</option>";
						echo "</select> <select name=\"mm\">";
						foreach ($month_tab as $key_m => $month)
    					echo "<option value=\"".$key_m."\">".$month."</option>";
						echo "</select> <select name=\"yyyy\">";
						for ($year = date("Y",time()) - 5; $year >= date("Y",time()) - 65; $year--)
							echo "<option value=\"".$year."\">".$year."</option>";
						echo "</select></p>";

						echo "<p><b><font color=\"red\">*</font> " .select_sex." : </b><br /><select name=\"sexe\" id=\"sexe\">";
						echo "<option value=\"0\"></option>";
						echo "<option value=\"F\">".female."</option>";
						echo "<option value=\"M\">".male."</option>";
						echo "</select>";

						if($dir = opendir("language")){
							echo "\n<p><b><font color=\"red\">*</font> " .language_user. " : </b><br /><select name=\"langue_home\">";
							while($lang = readdir($dir)) {
								if ($lang != ".." && $lang != "." && strtolower(substr($lang,0,5) != "index")) {
									if ($fd = @fopen("language/".$lang."/home.ini","r")){
										while (!feof($fd)) {
											$line = fgets($fd);
											if (strpos($line,"language=")===0 || strpos($line,"language="))
												break;
										}
										@fclose($fd);
										$line = substr($line,strpos($line,"=")+1);
									}
									else $line = "";
									if ($language == $lang)
										echo "<option  value=\"".$lang."\" selected=\"selected\">".$line." (".$lang.")</option>";
									else echo "<option  value=\"".$lang."\">".$line." (".$lang.")</option>";
								}
							}
							echo "</select>";
							closedir($dir);
						}
						
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
						
						echo "</fieldset></td></tr></table>";
						
						echo "<p align=\"center\"><font color=\"red\"><b>".champs_obligatoires_sauf_photo."</b></font><br /><br />";
	      		echo "<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"button\" class=\"button\" onClick=\"valider()\" value=\"" .btnsend. "\"></form></p>";
	    		}
				} else accueil();
			} else accueil();			
		} else accueil();
	} else accueil();

?>