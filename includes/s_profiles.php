<?php
/*
 * 	Manhali - Free Learning Management System
 *	s_profiles.php
 *	2011-01-27 12:52
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

function degre_felder($degre){
	if ($degre == 1 || $degre == 3) return degre_incertain;
	else if ($degre == 5 || $degre == 7) return degre_modere;
	else if ($degre == 9 || $degre == 11) return degre_fort;
	else return undefined;
}

function calcul_age ($birth) {
	$naissance = explode("/",$birth);
	$day = $naissance[0];
	$month = $naissance[1];
	$year = $naissance[2];
	$now = time();
	$thisday = date("d",$now);
	$thismonth = date("m",$now);
	$thisyear = date("Y",$now);
	if ($thismonth >= $month){
		$mois = $thismonth - $month;
		$annee = $thisyear - $year;
	} else {
		$mois = 12 + $thismonth - $month;
		$annee = $thisyear - $year - 1;
	}
	return $annee."-".$mois;
}

	if (!empty($_GET['s_profiles']) && ctype_digit($_GET['s_profiles']))
		$id_stu = intval($_GET['s_profiles']);
	else if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 2)
		$id_stu = $id_user_session;

	if (isset($id_stu) && !empty($id_stu)){
		if (!empty($_GET['action']) && $_GET['action'] == "edit"){
			if (isset($_SESSION['log']) && $_SESSION['log'] == 2 && $id_stu == $id_user_session){

				echo "<script language=\"javascript\" type=\"text/javascript\" src=\"styles/radio_div.js\"></script>";
				echo "<div id=\"titre\">".modifier_perso."</div><br />";
				goback_button();
				$err_comp = 0;

				$select_user = $connect->query("select * from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    		if (mysqli_num_rows($select_user) == 1){
    			$user = mysqli_fetch_row($select_user);
					
					$id_user = $user[0];
					$classe_user = $user[1];
					$nom_user = html_ent($user[2]);
					$identifiant_user = html_ent($user[3]);
					$mdp_user = $user[4];
					$email_user = html_ent($user[5]);
					$naissance_user = explode("/",$user[6]);
					$photo_profil = $user[8];
					$sexe_user = $user[9];
					$langue_user = $user[15];
					
					// need classe
					$select_demande_classe = $connect->query("select demander_classe from `" . $tblprefix . "site_infos`;");
					if (mysqli_num_rows($select_demande_classe) == 1) {
						$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
						if (mysqli_num_rows($select_classes) > 0 && mysqli_result($select_demande_classe,0) == 1)
							$need_classe = 1;
						else $need_classe = 0;
					} else $need_classe = 0;
					
					//modifier classe
					$autoriser_modification_classe = $connect->query("select autoriser_modification_classe from `" . $tblprefix . "site_infos`;");
					if (mysqli_num_rows($autoriser_modification_classe) == 1) {
						if (mysqli_result($autoriser_modification_classe,0) == 1)
							$edit_classe = 1;
						else $edit_classe = 0;
					} else $edit_classe = 0;
											
					if (!empty($_POST['send']) && !empty($_POST['random'])){
					 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
					 	$_SESSION['random_key'] = $_POST['random'];
					 	
// update identifiant
						$login = trim($_POST['login']);
						if (!empty($login)){
							$login = special_chars($login);
							$login = escape_string($login);
							if ($login != $identifiant_user){
								$select_app_login = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$login' and id_apprenant != $id_user;");
								$select_user_id = $connect->query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$login';");
 								if (mysqli_num_rows($select_app_login) == 0 && mysqli_num_rows($select_user_id) == 0) {
 									$update_login = $connect->query("update `" . $tblprefix . "apprenants` set identifiant_apprenant = '$login' where id_apprenant = $id_user;");
 								}
 								else {
 									$err_comp = 1;
 									goback(login_existe,2,"error",0);
 								} 							
 							}
 						}

// update nom
						$name = trim($_POST['name']);
						$name = escape_string($name);
						if ($name != $nom_user){
 							$update_name = $connect->query("update `" . $tblprefix . "apprenants` set nom_apprenant = '$name' where id_apprenant = $id_user;");
 						}

// update langue
						$langue_home = trim($_POST['langue_home']);
 						if (!empty($langue_home)){
							$langue_home = escape_string($langue_home);
							if ($langue_home != $langue_user){
 								$update_langue = $connect->query("update `" . $tblprefix . "apprenants` set langue_apprenant = '$langue_home' where id_apprenant = $id_user;");
 							}
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
 							$update_name = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '$danew_photo_profil', sexe_apprenant = '$sexe' where id_apprenant = $id_user;");
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
  		while (file_exists("docs/".$new_file))
				$new_file = fonc_rand(24).".".$ext;
  		$destination = "docs/".$new_file;
			if ((@move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$destination))){
				$update_photo = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '$new_file' where id_apprenant = $id_user;");
				if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg")
					@unlink("docs/".$photo_profil);
    	}
      else {
	  		$err_comp = 1;
        goback(erreur_upload,2,"error",0);
     	}
  	}
  	else {
  		$err_comp = 1;
			goback(erreur_upload_type,2,"error",0);
  	}
	}
} else if ($photo_profil != "man.jpg" && $photo_profil != "woman.jpg" && isset($_POST['avatar']) && $_POST['avatar'] == "supprimer"){
	if ($sexe == "M") $photo_remove = "man.jpg";
	else if ($sexe == "F") $photo_remove = "woman.jpg";
	else {
		if ($sexe_user == "M") $photo_remove = "man.jpg";
		else if ($sexe_user == "F") $photo_remove = "woman.jpg";
	}
 	$update_photo = $connect->query("update `" . $tblprefix . "apprenants` set photo_apprenant = '".$photo_remove."' where id_apprenant = $id_user;");
	@unlink("docs/".$photo_profil);
}

// update email
						$email = trim($_POST['email']);
						$email = escape_string($email);
						if ($email != $email_user){
							if (mail_valide($email) || $email == ""){
 								$update_email = $connect->query("update `" . $tblprefix . "apprenants` set email_apprenant = '$email' where id_apprenant = $id_user;");
 							}
 							else {
 								$err_comp = 1;
 								goback(format_mail_err,2,"error",0);
 							} 							
 						}

//update classe
				if ($need_classe == 1 && $edit_classe == 1 && ctype_digit($_POST['classe_app'])){
				 	if ($_POST['classe_app'] != $classe_user){
				 		$classe_app = $_POST['classe_app'];
				 		$update_classe = $connect->query("update `" . $tblprefix . "apprenants` set id_classe = $classe_app where id_apprenant = $id_user;");
				 	}
				}

//update naissance
				if ($_POST['jj'] != $naissance_user[0] || $_POST['mm'] != $naissance_user[1] || $_POST['yyyy'] != $naissance_user[2]){
					$jj = escape_string($_POST['jj']);
					$mm = escape_string($_POST['mm']);
					$yyyy = escape_string($_POST['yyyy']);
					if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && ctype_digit($yyyy) && $yyyy >= (date("Y",time()) - 65) && $yyyy <= (date("Y",time()) - 5)){
					 	$naissance_app = $jj."/".$mm."/".$yyyy;
						$update_naissance = $connect->query("update `" . $tblprefix . "apprenants` set naissance_apprenant = '$naissance_app' where id_apprenant = $id_user;");
					}
					else {
						$err_comp = 1;
						goback(date_naissance_invalide,2,"error",0);
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
	                    $update_mdp = $connect->query("update `" . $tblprefix . "apprenants` set mdp_apprenant = '$mdp' where id_apprenant = $id_user;");
										}
 							 			else {
											$err_comp = 1;
											goback(old_mdp_invalide,2,"error",0);
										}
 							 }
 							 else {
 							 	$err_comp = 1;
 							 	goback(pass_court,2,"error",0);
 							 }
 							}
 							else {
 								$err_comp = 1;
 								goback(confirm_pass_err,2,"error",0);
 							}
 						}
						
						if ($err_comp == 0) redirection(infos_modifies."<br />".identifiant." : ".html_ent($login),"?s_profiles",10,"tips",0);
					 } else goback(err_data_saved,2,"error",0);
					}
					else {
					// ****** formulaire edit profile
    				echo "<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">";
    				echo "<br /><table width=\"100%\" align=\"center\"><tr><td width=\"50%\" valign=\"top\"><fieldset>";
    				echo "<p><b>" .nom_complet. " : </b><br /><input name=\"name\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"".$nom_user."\"></p>";
	      		echo "<p><b>" .identifiant. " : </b><br /><input name=\"login\" type=\"text\" maxlength=\"30\" size=\"30\" value=\"".$identifiant_user."\"></p>";
	      		echo "<p><b>" .old_password. " : </b><br /><input name=\"old_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .new_password. " : </b><br /><input name=\"new_password\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"> " .carac5_min. "</p>";
	      		echo "<p><b>" .confirmpassword. " : </b><br /><input name=\"new_pass_conf\" type=\"password\" maxlength=\"30\" size=\"30\" value=\"\"></p>";
	      		echo "<p><b>" .email. " : </b><br /><input name=\"email\" type=\"text\" maxlength=\"50\" size=\"30\" value=\"".$email_user."\"></p>";
						
	      		if ($need_classe == 1){
	      			echo "<p><b>" .classe. " : </b><br /><select name=\"classe_app\"";
	      			if ($edit_classe == 0)
	      				echo " disabled=\"disabled\"";
	      			echo "><option value=\"0\"></option>";
    					while($classe = mysqli_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option ";
								if ($classe_user==$id_classe) echo "selected=\"selected\" ";
								echo "value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "</select></p>";
						}

						echo "</fieldset></td><td width=\"50%\" valign=\"top\"><fieldset>";

						echo "<p><b>" .date_naissance." : </b><br /><select name=\"jj\">";
						for ($day = 1; $day <= 31; $day++){
							echo "<option ";
							if (isset($naissance_user[0]) && $naissance_user[0]==$day) echo "selected=\"selected\" ";
							echo "value=\"".$day."\">".$day."</option>";
						}
						echo "</select> <select name=\"mm\">";
						foreach ($month_tab as $key_m => $month){
    					echo "<option ";
    					if (isset($naissance_user[1]) && $naissance_user[1]==$key_m) echo "selected=\"selected\" ";
    					echo "value=\"".$key_m."\">".$month."</option>";
    				}
						echo "</select> <select name=\"yyyy\">";
						for ($year = date("Y",time()) - 5; $year >= date("Y",time()) - 65; $year--){
							echo "<option ";
							if (isset($naissance_user[2]) && $naissance_user[2]==$year) echo "selected=\"selected\" ";
							echo "value=\"".$year."\">".$year."</option>";
						}
						echo "</select></p>";

						if ($sexe_user == "M") $chaine_m = " selected=\"selected\""; else $chaine_m = "";
						if ($sexe_user == "F") $chaine_f = " selected=\"selected\""; else $chaine_f = "";
						echo "<p><b>" .select_sex." : </b><br /><select name=\"sexe\">";
						echo "<option value=\"0\"></option>";
						echo "<option value=\"F\"".$chaine_f.">".female."</option>";
						echo "<option value=\"M\"".$chaine_m.">".male."</option>";
						echo "</select>";

						if($dir = opendir("language")){
							echo "\n<p><b>" .language_user. " : </b><br /><select name=\"langue_home\">";
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
						echo "<img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$nom_user."\" width=\"100\" height=\"100\" /><br /><br />";
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
						echo "</fieldset></td></tr></table>";
	    			echo "<p align=\"center\"><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   				}
			 } else accueil();
			} else accueil();
		}
		else {
			
			// ********** afficher profil **********
			
			echo "<div id=\"titre\">".learner_profile."</div>";
			goback_button();
			$select_stu = $connect->query("select * from `" . $tblprefix . "apprenants` where id_apprenant = $id_stu;");
    	if (mysqli_num_rows($select_stu) == 1){
    			$user = mysqli_fetch_row($select_stu);

					$id_classe = $user[1];
    		
					$nom_user = html_ent($user[2]);
					$identifiant_user = html_ent($user[3]);
					
					$email_user = html_ent($user[5]);
					$email_user = mail_antispam($email_user,0);
					
					$naissance_user = explode("/",$user[6]);
					
					$active_user = $user[7];
					$photo_profil = $user[8];
					
					if ($user[9] == "F") $sexe_user = female;
					else $sexe_user = male;
					
					$date_inscription = set_date($dateformat,$user[10]);
					
					if ($user[11] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$user[11]);
						
					$online = $user[12];

					$derniere_duree = calcule_duree($user[16]);
					$duree_totale = calcule_duree($user[17]);
					$nbr_of_connexion = $user[18];
					$total_essais_app = $user[19];
					$total_correct_app = $user[20];
					$nbr_pages = $user[21];
					$machine_app = html_ent($user[25]);

					// ******** modifier compte & envoyer message
					if (isset($_SESSION['log'])){
						if ($id_stu == $id_user_session && $_SESSION['log'] == 2){
							echo "<a href=\"?s_profiles&action=edit\"><b>".modifier_perso."</b></a>";
							echo "<br /><br /><a href=\"?questionnaire\"><b>".discover_learning_style."</b></a>";
						} else {
							if ($_SESSION['log'] == 1 && isset($adminfolder)){
								if (substr($adminfolder,-1,1)=="/")
									$adminfolder = substr($adminfolder,0,strlen($adminfolder)-1);
								$link_edit = $adminfolder."/admin_home.php?inc=messages&do=new_msg&tolearner=".$id_stu;
							}
							else if ($_SESSION['log'] == 2)
								$link_edit = "?s_messages&do=new_msg&tolearner=".$id_stu;
							echo "<a href=\"".$link_edit."\"><b>".send_msg_to_learner."</b></a>";
						}
					}
					
					// ******** infos
					if (!empty($photo_profil))
						echo "<p align=\"center\"><img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$nom_user."\" width=\"100\" height=\"100\" /></p>";
					
					echo "<h3 align=\"center\">".$identifiant_user." (".$sexe_user.")</h3>";
					echo "<h3 align=\"center\"><font color=\"green\">" .learner. "</font></h3>";

					echo "<table width=\"100%\" align=\"center\"><tr><td width=\"50%\" valign=\"top\"><fieldset>";
					echo "<legend><b>".personal_information."</b></legend><ul>";
					
					echo "<li><p><b>" .nom_complet. " : ".$nom_user."</b></p></li>";
					echo "<li><p><b>" .email. " : ".$email_user."</b></p></li>";
					
					$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $id_classe;");
    	  	if (mysqli_num_rows($select_classe) == 1){
    				$classe = html_ent(mysqli_result($select_classe,0));
						echo "<li><p><b>" .classe. " : ".$classe."</b></p></li>";
					}
					echo "<li><p><b>" .date_naissance. " : </b>";
					if (!empty($user[6])){
						echo "<b>".$naissance_user[0]." ".$month_tab[$naissance_user[1]]." ".$naissance_user[2]."</b>";
						$naissance = explode ("-",calcul_age($user[6]));
						echo " (".$naissance[0]." ".years." ".et." ".$naissance[1]." ".months.")";
					}
					echo "</p></li>";

					echo "<li><p><b>" .online. " : </b>";
					if ($online == 1)
						echo "<img border=\"0\" src=\"images/others/valide.png\" width=\"32\" height=\"32\" />";
					else
						echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
					echo "</p></li>";

				if (!empty($id_user_session) && isset($_SESSION['log']) && ($_SESSION['log'] == 1 || ($_SESSION['log'] == 2 && $id_stu == $id_user_session))){

					echo "<li><p><b>" .date_inscription. " : ".$date_inscription."</b></p></li>";
					echo "<li><p><b>" .last_connect. " : ".$last_connect."</b></p></li>";
					echo "<li><p><b>" .duration_last. " : ".$derniere_duree."</b></p></li>";
					echo "<li><p><b>" .active. " : </b>";
					if ($active_user == 1)
						echo "<img border=\"0\" src=\"images/others/valide.png\" width=\"32\" height=\"32\" />";
					else
						echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
					echo "</p></li>";
				}
				echo "</ul></fieldset>";
				if (!empty($id_user_session) && isset($_SESSION['log']) && ($_SESSION['log'] == 1 || ($_SESSION['log'] == 2 && $id_stu == $id_user_session))){
    			$select_devoirs_cet_apprenant = $connect->query("select * from `" . $tblprefix . "devoirs_notes` where id_apprenant = $user[0];");
					if (mysqli_num_rows($select_devoirs_cet_apprenant) > 0){
						echo "<fieldset><legend><b>".homework_assignments."</b></legend><ul>";
						while($devoir_cet_apprenant = mysqli_fetch_row($select_devoirs_cet_apprenant)){
							$id_devoir_cet_apprenant = $devoir_cet_apprenant[1];
							$note_devoir_cet_apprenant = $devoir_cet_apprenant[3];
							
							$select_devoir_titre_cet_apprenant = $connect->query("select titre_devoir, date_expire_devoir from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir_cet_apprenant;");
    	  			if (mysqli_num_rows($select_devoir_titre_cet_apprenant) == 1){
    						$titre_devoir_cet_apprenant = html_ent(mysqli_result($select_devoir_titre_cet_apprenant,0,0));
    						$exp_devoir_cet_apprenant = set_date($dateformat,mysqli_result($select_devoir_titre_cet_apprenant,0,1));
    					} else {
    						$titre_devoir_cet_apprenant = "---";
    						$exp_devoir_cet_apprenant = "---";
    					}
							echo "<li><b>".$titre_devoir_cet_apprenant." : <u>".$note_devoir_cet_apprenant."</u> /20 (".$exp_devoir_cet_apprenant.")</b></li>";
						}
						echo "</ul></fieldset>";
					}
					if (!empty($machine_app)){
						echo "<fieldset><legend><b>".browser_os."</b></legend><ul>";
						echo "<li><b>".$machine_app."</b></li>";
						echo "</ul></fieldset>";
					}
				}
				if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 1){
					echo "</td>";
					echo "<td width=\"50%\" valign=\"top\">";
					
					echo "<fieldset><legend><b>".behavior_statistics."</b></legend><ul>";

					echo "<li><b>" .time_spent. " : ".$duree_totale."</b></li>";
					echo "<li><b>" .number_connections. " : ".$nbr_of_connexion."</b></li>";
					echo "<li><b>" .number_visited_pages. " : ".$nbr_pages."</b></li>";
					
					$select_count_comments = $connect->query("select count(id_post) from `" . $tblprefix . "commentaires` where type_user = 'l' and id_user = $user[0];");
					$nbr_comments = mysqli_result($select_count_comments,0);
					echo "<li><b>" .number_comments. " : ".$nbr_comments."</b></li>";

					$select_count_messages = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where id_emetteur_app = $user[0];");
					$nbr_messages = mysqli_result($select_count_messages,0);
					echo "<li><b>" .number_messages. " : ".$nbr_messages."</b></li>";

					if ($total_essais_app != 0 && $total_correct_app <= $total_essais_app)
						$reponses_correctes = round(100 * $total_correct_app / $total_essais_app,2);
					else {
						$reponses_correctes = 0;
						$total_essais_app = 0;
					}
					echo "<li><b>".$total_essais_app." ".essais_qcm." ".$reponses_correctes." % ".reponses_sont_correctes."</b></li>";

					$select_count_devoirs = $connect->query("select count(id_devoir_rendu) from `" . $tblprefix . "devoirs_rendus` where id_apprenant = $user[0];");
					$nbr_devoirs = mysqli_result($select_count_devoirs,0);
					echo "<li><b>" .number_achieved_assignments. " : ".$nbr_devoirs."</b></li>";

					echo "</ul>";
					echo "</fieldset>";

					// ************* behavior note
					$note_totale = 0;
					$nbr_notes = 0;
					$count_apps_stats = 0;
					$select_count_apps_same_classe = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $user[1];");
					$select_count_apps_active = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1';");
					$select_count_apps = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0;");
					if (mysqli_num_rows($select_count_apps_same_classe) > 0 && mysqli_result($select_count_apps_same_classe,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps_same_classe,0);
						$req_apps_stats = "where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $user[1]";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $user[1];");
					}
					else if (mysqli_num_rows($select_count_apps_active) > 0 && mysqli_result($select_count_apps_active,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps_active,0);
						$req_apps_stats = "where nbr_connexion > 0 and active_apprenant = '1'";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1';");
					}
					else if (mysqli_num_rows($select_count_apps) > 0 && mysqli_result($select_count_apps,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps,0);
						$req_apps_stats = "where nbr_connexion > 0";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0;");
					}

					if (!empty($count_apps_stats) && isset($req_apps_stats)){
						$count_apps_10 = $count_apps_stats / 10;
						$tab_apps_stats = array();
						while ($one_apps_stats = mysqli_fetch_row($select_apps_stats))
							$tab_apps_stats[] = $one_apps_stats[0];
							$chaine_apps_stats = implode(",",$tab_apps_stats);
						//******* total_duration
						$select_sum_total_duration = $connect->query("select sum(total_duration) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_duration) > 0 && mysqli_result($select_sum_total_duration,0) > 0 && mysqli_result($select_sum_total_duration,0) > $count_apps_10){
							$sum_total_duration = mysqli_result($select_sum_total_duration,0);
							$note_total_duration = round(100*$user[17]/$sum_total_duration,5);
							if ($note_total_duration < 0) $note_total_duration = 0;
							if ($note_total_duration > 100) $note_total_duration = 100;
							$note_totale += $note_total_duration;
							$nbr_notes += 1;
						}
						//******* nbr_connexion
						$select_sum_nbr_connexion = $connect->query("select sum(nbr_connexion) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_nbr_connexion) > 0 && mysqli_result($select_sum_nbr_connexion,0) > 0 && mysqli_result($select_sum_nbr_connexion,0) > $count_apps_10){
							$sum_nbr_connexion = mysqli_result($select_sum_nbr_connexion,0);
							$note_nbr_connexion = round(100*$nbr_of_connexion/$sum_nbr_connexion,5);
							if ($note_nbr_connexion < 0) $note_nbr_connexion = 0;
							if ($note_nbr_connexion > 100) $note_nbr_connexion = 100;
							$note_totale += $note_nbr_connexion;
							$nbr_notes += 1;
						}
						//******* nbr_pages
						$select_sum_nbr_pages = $connect->query("select sum(nbr_pages) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_nbr_pages) > 0 && mysqli_result($select_sum_nbr_pages,0) > 0 && mysqli_result($select_sum_nbr_pages,0) > $count_apps_10){
							$sum_nbr_pages = mysqli_result($select_sum_nbr_pages,0);
							$note_nbr_pages = round(100*$nbr_pages/$sum_nbr_pages,5);
							if ($note_nbr_pages < 0) $note_nbr_pages = 0;
							if ($note_nbr_pages > 100) $note_nbr_pages = 100;
							$note_totale += $note_nbr_pages;
							$nbr_notes += 1;
						}
						//******* total_essais_qcm
						$select_sum_total_essais = $connect->query("select sum(total_essais) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_essais) > 0 && mysqli_result($select_sum_total_essais,0) > 0 && mysqli_result($select_sum_total_essais,0) > $count_apps_10){
							$sum_total_essais = mysqli_result($select_sum_total_essais,0);
							$note_total_essais = round(100*$total_essais_app/$sum_total_essais,5);
							if ($note_total_essais < 0) $note_total_essais = 0;
							if ($note_total_essais > 100) $note_total_essais = 100;
							$note_totale += $note_total_essais;
							$nbr_notes += 1;
						}
						//******* total_reponses_correctes
						$select_sum_total_reponses_correctes = $connect->query("select sum(total_reponses_correctes) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_reponses_correctes) > 0 && mysqli_result($select_sum_total_reponses_correctes,0) > 0 && mysqli_result($select_sum_total_reponses_correctes,0) > $count_apps_10){
							$sum_total_reponses_correctes = mysqli_result($select_sum_total_reponses_correctes,0);
							$note_total_reponses_correctes = round(100*$total_correct_app/$sum_total_reponses_correctes,5);
							if ($note_total_reponses_correctes < 0) $note_total_reponses_correctes = 0;
							if ($note_total_reponses_correctes > 100) $note_total_reponses_correctes = 100;
							$note_totale += $note_total_reponses_correctes;
							$nbr_notes += 1;
						}
						//******* total_commentaires
						$select_count_apps_comments = $connect->query("select count(id_post) from `" . $tblprefix . "commentaires` where type_user = 'l' and id_user in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_comments) > 0 && mysqli_result($select_count_apps_comments,0) > 0 && mysqli_result($select_count_apps_comments,0) > $count_apps_10){
							$count_apps_comments = mysqli_result($select_count_apps_comments,0);
							$note_apps_comments = round(100*$nbr_comments/$count_apps_comments,5);
							if ($note_apps_comments < 0) $note_apps_comments = 0;
							if ($note_apps_comments > 100) $note_apps_comments = 100;
							$note_totale += $note_apps_comments;
							$nbr_notes += 1;
						}
						//******* total_messages
						$select_count_apps_messages = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where id_emetteur_app in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_messages) > 0 && mysqli_result($select_count_apps_messages,0) > 0 && mysqli_result($select_count_apps_messages,0) > $count_apps_10){
							$count_apps_messages = mysqli_result($select_count_apps_messages,0);
							$note_apps_messages = round(100*$nbr_messages/$count_apps_messages,5);
							if ($note_apps_messages < 0) $note_apps_messages = 0;
							if ($note_apps_messages > 100) $note_apps_messages = 100;
							$note_totale += $note_apps_messages;
							$nbr_notes += 1;
						}
						//******* total_devoirs
						$select_count_apps_devoirs = $connect->query("select count(id_devoir_rendu) from `" . $tblprefix . "devoirs_rendus` where id_apprenant in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_devoirs) > 0 && mysqli_result($select_count_apps_devoirs,0) > 0 && mysqli_result($select_count_apps_devoirs,0) > $count_apps_10){
							$count_apps_devoirs = mysqli_result($select_count_apps_devoirs,0);
							$note_apps_devoirs = round(100*$nbr_devoirs/$count_apps_devoirs,5);
							if ($note_apps_devoirs < 0) $note_apps_devoirs = 0;
							if ($note_apps_devoirs > 100) $note_apps_devoirs = 100;
							$note_totale += $note_apps_devoirs;
							$nbr_notes += 1;
						}
					}
					$this_month = date("m",time());
					$this_year = date("Y",time());
					//************ update insert note
					if ($note_totale >= 0 && $nbr_notes > 0){
					 $note_finale_behavior = round($note_totale / $nbr_notes,5);
					 if ($note_finale_behavior >= 0 && $note_finale_behavior <= 100){

   					$select_note_finale_behavior = $connect->query("select id_behavior_note, behavior_note from `" . $tblprefix . "behavior_notes` where id_apprenant = $user[0] and mois_note = $this_month and annee_note = $this_year;");
      			if (mysqli_num_rows($select_note_finale_behavior) > 0){
   						$id_note_finale_behavior = mysqli_result($select_note_finale_behavior,0,0);
   						$ancien_note_finale_behavior = mysqli_result($select_note_finale_behavior,0,1);
   						if ($ancien_note_finale_behavior != $note_finale_behavior){
   							$update_note_finale_behavior = $connect->query("update `" . $tblprefix . "behavior_notes` set behavior_note = $note_finale_behavior where id_behavior_note = $id_note_finale_behavior;");
   							locationhref_admin("?s_profiles=".$user[0]);
   						}
   					}
   					else {
   						$insert_note_finale_behavior = $connect->query("INSERT INTO `" . $tblprefix . "behavior_notes` VALUES (NULL,$this_month,$this_year,$user[0],$note_finale_behavior);");
   						locationhref_admin("?s_profiles=".$user[0]);
   					}
   				 }
   				} else $note_finale_behavior = 0;
   				//************
					$select_this_app_note = $connect->query("select behavior_note from `" . $tblprefix . "behavior_notes` where id_apprenant = $user[0] and mois_note = $this_month and annee_note = $this_year;");
					if (mysqli_num_rows($select_this_app_note) > 0) {
						$note_apps_final = mysqli_result($select_this_app_note,0);
						if ($count_apps_stats > 0){
							$coef_apps = 100 / $count_apps_stats;
							$note_for_grade = $note_apps_final / $coef_apps;

      				if ($note_for_grade < 0.5)
   							$grade_behavior_final = "E";
   						else if ($note_for_grade >= 0.5 && $note_for_grade < 1)
   							$grade_behavior_final = "D";
   						else if ($note_for_grade >= 1 && $note_for_grade < 2)
   							$grade_behavior_final = "C";
   						else if ($note_for_grade >= 2 && $note_for_grade < 4)
   							$grade_behavior_final = "B";
   						else if ($note_for_grade >= 4)
   							$grade_behavior_final = "A";
							else $grade_behavior_final = "E";

							$update_grade = $connect->query("update `" . $tblprefix . "apprenants` set grade_apprenant = '$grade_behavior_final' where id_apprenant = $user[0];");
						} else $grade_behavior_final = "E";
					}

					echo "<fieldset><legend><b>".note_grade."</b></legend><ul>";
					echo "<li><b>".behavior_score." : ".round($note_finale_behavior,2)." %</b></li>";
					echo "<li><b>".behavior_grade." : ".$grade_behavior_final."</b></li>";
					echo "</ul></fieldset>";
				}

//********************* learning style
				if (!empty($id_user_session) && isset($_SESSION['log']) && ($_SESSION['log'] == 1 || ($_SESSION['log'] == 2 && $id_stu == $id_user_session))){
					echo "<fieldset><legend><b>".learning_style."</b></legend><ul>";
					echo "<li><b>".felder_learning_style." :</b> <a href=\"?felder\"><b>".details."...</b></a><br />";
					if (strlen($user[26]) > 5){
						$style_app = explode("-",$user[26]);
						$actif = $style_app[0];
						$reflechi = $style_app[1];
						$sensoriel = $style_app[2];
						$intuitif = $style_app[3];
						$visuel = $style_app[4];
						$verbal = $style_app[5];
						$sequentiel = $style_app[6];
						$global = $style_app[7];
						if ($actif > $reflechi){
							$dim1 = $actif - $reflechi;
							echo "<br />".reflexion." : ".actif." (".degre_felder($dim1).")";
						} else {
							$dim1 = $reflechi - $actif;
							echo "<br />".reflexion." : ".reflechi." (".degre_felder($dim1).")";
						}
						if ($sensoriel > $intuitif){
							$dim2 = $sensoriel - $intuitif;
							echo "<br />".raisonnement." : ".sensoriel." (".degre_felder($dim2).")";
						} else {
							$dim2 = $intuitif - $sensoriel;
							echo "<br />".raisonnement." : ".intuitif." (".degre_felder($dim2).")";
						}
						if ($visuel > $verbal){
							$dim3 = $visuel - $verbal;
							echo "<br />".sensorielle." : ".visuel." (".degre_felder($dim3).")";
						} else {
							$dim3 = $verbal - $visuel;
							echo "<br />".sensorielle." : ".verbal." (".degre_felder($dim3).")";
						}
						if ($sequentiel > $global){
							$dim4 = $sequentiel - $global;
							echo "<br />".progression." : ".sequentiel." (".degre_felder($dim4).")";
						} else {
							$dim4 = $global - $sequentiel;
							echo "<br />".progression." : ".global_dim." (".degre_felder($dim4).")";
						}
						echo "</li>";
					}
					else {
						if ($id_stu == $id_user_session && $_SESSION['log'] == 2)
							echo "<br /><a href=\"?questionnaire\"><b>".discover_learning_style."</b></a><br />";
						else echo "<br />".undefined_style."<br />";
					}
					echo "<br /><li><b>".kolb_learning_style." :</b> <a href=\"?kolb\"><b>".details."...</b></a><br /><br />";
					if (!empty($note_total_essais) && !empty($note_apps_devoirs) && !empty($note_apps_comments) && !empty($note_total_duration) && !empty($note_nbr_connexion) && !empty($note_nbr_pages)){
						$experimentation = $note_total_essais + $note_apps_devoirs + $note_apps_comments;
						$observation = $note_total_duration + $note_nbr_connexion + $note_nbr_pages;					
						if ($experimentation > 0 || $observation > 0){
							if ($experimentation > $observation) echo active_experimentation;
							else if ($observation > $experimentation) echo reflective_observation;
							else echo undefined_style;
							echo "</li>";
						} else echo undefined_style;
					} else echo undefined_style;
					echo "</ul></fieldset>";
				}
				echo "</td></tr></table>";

				if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 1){
					$select_all_notes = $connect->query("select * from `" . $tblprefix . "behavior_notes` where id_apprenant = $user[0] order by annee_note desc,mois_note desc;");
					if (mysqli_num_rows($select_all_notes) > 0) {
						$last_note_app = 0;
						
						echo "\n<table width=\"50%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
						echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".year."</b></td>";
						echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".month."</b></td>";
						echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".behavior_score."</b></td>";
						echo "\n<td class=\"affichage_table\" width=\"20%\"><b>".increase_decrease."</b></td>";
						echo "</tr>\n";
						while($note_this_app = mysqli_fetch_row($select_all_notes)){
							$mois_note = $month_tab[$note_this_app[1]];
							$annee_note = $note_this_app[2];
							$this_note = round($note_this_app[4],2);
							if ($last_note_app > 0){
								if ($this_note < $last_note_app)
									$lien_img_note = "<img border=\"0\" src=\"images/others/increase.png\" width=\"22\" height=\"32\" />";
								else $lien_img_note = "<img border=\"0\" src=\"images/others/decrease.png\" width=\"22\" height=\"32\" />";
								echo "\n<td class=\"affichage_table\">".$lien_img_note."</td>\n</tr>";
							}
							$last_note_app = $this_note;
							echo "\n<tr>";
							echo "\n<td class=\"affichage_table\"><b>".$annee_note."</b></td>";
							echo "\n<td class=\"affichage_table\"><b>".$mois_note."</b></td>";
							echo "\n<td class=\"affichage_table\"><b><u>".$this_note." %</u></b></td>";

						}
						echo "\n<td class=\"affichage_table\">---</td></tr></table>";
					}
				}

			} else accueil();
		}
	} else accueil();

?>