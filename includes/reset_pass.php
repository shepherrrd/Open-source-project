<?php
/*
 * 	Manhali - Free Learning Management System
 *	reset_pass.php
 *	2009-01-03 00:26
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
if  (!isset($_SESSION['log']) && !isset($_SESSION['key'])){

$select_identification = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'identification';");
if (mysql_num_rows($select_identification) == 1) {
 $identification = mysql_result($select_identification,0);
 if ($identification == 1){
			
	require("includes/class.phpmailer.php");
	echo "<br /><div id=\"titre\">".title_regener."</div><br /><br />";

	$seladminmail = "SELECT identifiant_user, email_user from `" . $tblprefix . "users` WHERE grade_user = '3';";
	$adminmailres = mysql_query($seladminmail,$connect);
	$adminnom = mysql_result($adminmailres, 0, "identifiant_user");
	$adminmail = mysql_result($adminmailres, 0, "email_user");
								
	if (!empty($_GET['reset_key'])) {
		$reset_key = escape_string($_GET['reset_key']);
		$sel_rest_key = mysql_query("SELECT * from `" . $tblprefix . "reset_pass` WHERE key_reset = '".$reset_key."';");
		if (mysql_num_rows($sel_rest_key) > 0) {
			$type_user_res = mysql_result($sel_rest_key,0,1);
			$id_user_res = mysql_result($sel_rest_key,0,2);
			$date_reset = mysql_result($sel_rest_key,0,4);
			if (time() - $date_reset < 86400){
				if ($type_user_res == "u")
					$mail_select = mysql_query("SELECT nom_user, email_user from `" . $tblprefix . "users` WHERE id_user = ".$id_user_res.";");
				else
					$mail_select = mysql_query("SELECT nom_apprenant, email_apprenant from `" . $tblprefix . "apprenants` WHERE id_apprenant = ".$id_user_res.";");
				$nom_user_res = mysql_result($mail_select,0,0);
				$mail_user_res = mysql_result($mail_select,0,1);

				$rndmpass = fonc_rand(8);

				$mail = new PHPMailer();
				$mail->From = $adminmail;
				$mail->FromName = $adminnom;
				$mail->AddAddress($mail_user_res, $nom_user_res);
				$mail->WordWrap = 80;
				$mail->IsHTML(true);
				$mail->CharSet = 'UTF-8';
				$mail->Subject = $title." - ".pass_oublie." !";
				$mail->Body    = mailmsg1." ".$nom_user.",<br /><br />".mailmsg2." ".$title." ".mailmsg3."<br />".mailmsg4."<br /><br />".mailmsg7." : <b>".$rndmpass."</b><br />".mailmsg8."<br /><br />".$url_site. "<br />" .$title. "<br />";
				$mail->AltBody = mailmsg1." ".$nom_user.",\n\n".mailmsg2." ".$title." ".mailmsg3."\n".mailmsg4."\n\n".mailmsg7." : ".$rndmpass."\n".mailmsg8."\n\n".$url_site. "\n" .$title. "\n";

     		if(!$mail->Send())
					redirection(mailerror,"?reset_pass",3,"error",0);
				else {
					$rndm = fonc_rand(8);
	  			$rndm1 = substr($rndm,0,4);
	  			$rndm2 = substr($rndm,4,4);
	  			$crypt = md5($rndm2.$rndmpass.$rndm1);
	  			$newpass = $crypt.$rndm;
					
					if ($type_user_res == "u")
  					$update_pass = "update `" . $tblprefix . "users` SET mdp_user = '" . $newpass . "' WHERE id_user = ".$id_user_res.";";
					else
						$update_pass = "update `" . $tblprefix . "apprenants` SET mdp_apprenant = '" . $newpass . "' WHERE id_apprenant = ".$id_user_res.";";
					mysql_query($update_pass,$connect);
					redirection(mailconfirm,"index.php",3,"tips",0);
				}
			} else redirection(mail_expire_key,"?reset_pass",3,"error",0);
		} else redirection(mailerror,"?reset_pass",3,"error",0);
	}
	else if (!isset($_POST['envoyer'])) {

		echo text_regener;
	  echo "<form method=\"POST\" action=\"\">";
	  echo "<p><b><font color=\"red\">*</font> " .identifiant. " : </b><br /><input name=\"login\" type=\"text\" size=\"30\" maxlength=\"30\" value=\"\"></p>";
	  echo "<p><b><font color=\"red\">*</font> " .email. " : </b><br /><input name=\"email\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"\"></p>";
	  echo "<p><input type=\"hidden\" name=\"envoyer\" value=\"ok\">";
	  echo "<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
	}
	else {
			if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['email']) && !empty($_POST['email'])) {

					$login = escape_string($_POST['login']);
					$email = escape_string($_POST['email']);

					$resultat = mysql_query("SELECT * from `" . $tblprefix . "users` WHERE identifiant_user = '".$login."';");
					$resultat_apps = mysql_query("SELECT * from `" . $tblprefix . "apprenants` WHERE identifiant_apprenant = '".$login."';");
					
					$userfound = "";
					
					if (mysql_num_rows($resultat) == 1) {
						
						$userfound = "usr";
						$iduser = mysql_result($resultat, 0, "id_user");
						$nom_user = mysql_result($resultat, 0, "identifiant_user");
						$emailbd = mysql_result($resultat, 0, "email_user");
						$statut = mysql_result($resultat, 0, "active_user");
						
					} else if (mysql_num_rows($resultat_apps) == 1) {
						
						$userfound = "app";
						$iduser = mysql_result($resultat_apps, 0, "id_apprenant");
						$nom_user = mysql_result($resultat_apps, 0, "identifiant_apprenant");
						$emailbd = mysql_result($resultat_apps, 0, "email_apprenant");
						$statut = mysql_result($resultat_apps, 0, "active_apprenant");

					} else goback(login_invalide,2,"error",0);
					
					if (!empty($userfound) && ($userfound == "usr" || $userfound = "app")){
						
						if ($email == $emailbd) {

							if ($statut == 1) {

								$reset_key = fonc_rand(16);
								$link_reset = $_SERVER['HTTP_REFERER']."=&reset_key=".$reset_key;
								
								$mail = new PHPMailer();
								$mail->From = $adminmail;
								$mail->FromName = $adminnom;
								$mail->AddAddress($emailbd, $nom_user);
								$mail->WordWrap = 80;
								$mail->IsHTML(true);
								$mail->CharSet = 'UTF-8';
								$mail->Subject = $title." - ".pass_oublie." !";
								$mail->Body    = mailmsg1." ".$nom_user.",<br /><br />".mailmsg2." ".$title." ".mailmsg3."<br />".mailmsg4."<br /><br />".mailmsg5."<br /><br /><a href=\"".$link_reset."\">".$link_reset."</a><br /><br />".mailmsg6."<br /><br />".$url_site. "<br />" .$title. "<br />";
								$mail->AltBody = mailmsg1." ".$nom_user.",\n\n".mailmsg2." ".$title." ".mailmsg3."\n".mailmsg4."\n\n".mailmsg5."\n\n".$link_reset."\n\n".mailmsg6."\n\n".$url_site. "\n" .$title. "\n";

     						if(!$mail->Send())
									redirection(mailerror,"index.php",3,"error",0);
								else {
									if ($userfound == "usr")
										$reset_usr = "u";
									else $reset_usr = "l";
									$insert_reset = mysql_query("INSERT INTO `" . $tblprefix . "reset_pass` VALUES (NULL,'$reset_usr',$iduser,'$reset_key',".time().");");

	             		redirection(mailconfirm,"index.php",3,"tips",0);
								}
							} else goback(compte_desac,2,"error",0);
						} else goback(mail_invalide,2,"error",0);
					}
			} else goback(champ_manq,2,"error",0);
	}
 } else accueil();
} else accueil();
} else accueil();
?>