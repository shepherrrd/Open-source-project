<?php
/*
 * 	Manhali - Free Learning Management System
 *	login.php
 *	2010-12-25 00:03
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
	define("access_const","access_const");

	header('Content-type: text/html; charset=UTF-8');
	mb_internal_encoding("UTF-8");
	include_once ("includes/dbconfig.php");
	include_once ("includes/security_functions.php");
	open_session("");
	include_once ("includes/language.php");
	include_once ("includes/display_functions.php");
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali</title>
<link rel="stylesheet" href="styles/style1.css" type="text/css" />
<link rel="shortcut icon" href="styles/favicon.gif" type="image/x-icon" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

<!--[if lt IE 7]>
<script defer type="text/javascript" src="styles/pngfix.js"></script>
<![endif]-->

</head>
<body>
<br />

<?php

function mysqli_result($res, $row, $field=0) {

    $res->data_seek($row);

    $datarow = $res->fetch_array();

    return $datarow[$field];

}

$select_statut_identification = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'identification';");
if (mysqli_num_rows($select_statut_identification) == 1) {
 $statut_identification = mysqli_result($select_statut_identification,0);
 if ($statut_identification == 1) {
	if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['password']) && !empty($_POST['password'])) {

		$login = escape_string($_POST['login']);
		$password = escape_string($_POST['password']);

		$seluser = "SELECT * from `" . $tblprefix . "users` WHERE identifiant_user = '" . $login . "';";
		$resultat = $connect->query($seluser);

		if ($resultat && mysqli_num_rows($resultat) == 1) {
			
			$id = mysqli_result($resultat, 0, "id_user");
			$pass = mysqli_result($resultat, 0, "mdp_user");
			$statut = mysqli_result($resultat, 0, "active_user");
			$grade = mysqli_result($resultat, 0, "grade_user");

			if ($statut == 1) {
						$passpart1 = substr($pass,0,32);
						$passpart2 = substr($pass,32,4);
						$passpart3 = substr($pass,36,4);

						if ($passpart1 == md5($passpart3.$password.$passpart2)) {
								$_SESSION['log'] = 1;
								$_SESSION['id'] = $id;
								$_SESSION['grade'] = $grade;
								$_SESSION['key'] = fonc_rand(16);
								
								$update_conn = $connect->query("update `" . $tblprefix . "users` set last_connect = ".time().", connected_now = '1', last_duration = 0, nbr_connexion = nbr_connexion + 1 where id_user = $id;");
								
								$ip_user = escape_string($_SERVER['REMOTE_ADDR']);
								$select_acces = $connect->query("select id_acces from `" . $tblprefix . "infos_acces` where type_user = 'u' and id_user = $id and ip_user = '$ip_user';");
								if (mysqli_num_rows($select_acces) > 0){
									$id_this_acces = mysqli_result($select_acces,0);
									$update_acces = $connect->query("update `" . $tblprefix . "infos_acces` set date_acces = ".time()." where id_acces = $id_this_acces;");
								}
								else {
									$insert_acces = $connect->query("INSERT INTO `" . $tblprefix . "infos_acces` VALUES (NULL,'u',$id,'$ip_user',".time().");");
								}
								
								if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['SERVER_NAME']))
									$redir_link = $_SERVER['HTTP_REFERER'];
								else $redir_link = "index.php";
								redirection(merci_connexion,$redir_link,3,"tips",0);
						} else goback(mdp_invalide,2,"error",0);
			} else goback(compte_desac,2,"error",0);
		}
		else {
			$selapp = "SELECT * from `" . $tblprefix . "apprenants` WHERE identifiant_apprenant = '" . $login . "';";
			$resultat2 = $connect->query($selapp);

			if ($resultat2 && mysqli_num_rows($resultat2) == 1) {
				$id = mysql_result($resultat2, 0, "id_apprenant");
				$pass = mysql_result($resultat2, 0, "mdp_apprenant");
				$statut = mysql_result($resultat2, 0, "active_apprenant");
    		$id_classe_access = mysql_result($resultat2, 0, "id_classe");
    		
				if ($statut == 1) {
						$passpart1 = substr($pass,0,32);
						$passpart2 = substr($pass,32,4);
						$passpart3 = substr($pass,36,4);

						if ($passpart1 == md5($passpart3.$password.$passpart2)) {
							$_SESSION['log'] = 2;
							$_SESSION['id'] = $id;
							$_SESSION['key'] = fonc_rand(16);

							$update_conn = mysql_query("update `" . $tblprefix . "apprenants` set last_connect_apprenant = ".time().", connected_now_apprenant = '1', last_duration = 0, nbr_connexion = nbr_connexion + 1 where id_apprenant = $id;");
							
							$machine_app = escape_string($_SERVER['HTTP_USER_AGENT']);
							if (!empty($machine_app))
								$update_machine = mysql_query("update `" . $tblprefix . "apprenants` set machine_apprenant = '$machine_app' where id_apprenant = $id;");
							
							$ip_app = escape_string($_SERVER['REMOTE_ADDR']);
							$select_acces = mysql_query("select id_acces from `" . $tblprefix . "infos_acces` where type_user = 'l' and id_user = $id and ip_user = '$ip_app';");
							if (mysql_num_rows($select_acces) > 0){
								$id_this_acces = mysql_result($select_acces,0);
								$update_acces = mysql_query("update `" . $tblprefix . "infos_acces` set date_acces = ".time()." where id_acces = $id_this_acces;");
							}
							else {
								$insert_acces = mysql_query("INSERT INTO `" . $tblprefix . "infos_acces` VALUES (NULL,'l',$id,'$ip_app',".time().");");
							}
 							
 							$select_grade_app = mysql_query("select grade_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id;");
							if (mysql_num_rows($select_grade_app) == 1)
								$grade_app_session = mysql_result($select_grade_app,0);
							else $grade_app_session = "None";

							if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['SERVER_NAME']))
								$redir_link = $_SERVER['HTTP_REFERER'];
							else $redir_link = "index.php";

				// *********** rappel devoirs ***************
						$nbr_devoirs = 0;
						$chaine_devoir = "";
						$select_devoir_app = mysql_query("select * from `" . $tblprefix . "devoirs` where publie_devoir = '1' and date_publie_devoir < ".time()." and date_expire_devoir > ".time()." order by date_expire_devoir;");
						if (mysql_num_rows($select_devoir_app)> 0) {
							while($devoir = mysql_fetch_row($select_devoir_app)) {
								$id_this_devoir = $devoir[0];
								$id_chap_devoir = $devoir[1];
								$acces_devoir = $devoir[2];
								$titre_devoir = $devoir[3];
								$expiration_devoir = round(($devoir[6] - time()) / 60 / 60 / 24);
								$expiration_chaine = $expiration_devoir." ".jours;

								$select_grade_chap = mysql_query("select grade_chapitre from `" . $tblprefix . "chapitres` where id_chapitre = $id_chap_devoir;");
								if (mysql_num_rows($select_grade_chap) == 1) {
									$grade_chap = mysql_result($select_grade_chap,0);
									$tab_acces_chap = explode("-",trim($grade_chap,"-"));
									if ($grade_chap == "*" || $grade_chap == "0" || in_array($grade_app_session,$tab_acces_chap)){
									
     							 $select_access_tuto = mysql_query("select acces_tutoriel from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap_devoir;");
									 if (mysql_num_rows($select_access_tuto) == 1) {
										$access_tuto = mysql_result($select_access_tuto,0);
										$tab_acces_tuto = explode("-",trim($access_tuto,"-"));
										if ($access_tuto == "*" || $access_tuto == "0" || in_array($id_classe_access,$tab_acces_tuto)){
										
											$select_devoir_rendu = mysql_query("select * from `" . $tblprefix . "devoirs_rendus` where id_devoir = $id_this_devoir and id_apprenant = $id;");
											if (mysql_num_rows($select_devoir_rendu) == 0){
												$acces_devoir_valide = 0;
												if ($acces_devoir == "*")
													$acces_devoir_valide = 1;
												else {
													$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id;");
													if (mysql_num_rows($select_classe) == 1){
														$id_classe = mysql_result($select_classe,0);
														$tab_classes = explode("-",$acces_devoir);
														if (in_array($id_classe,$tab_classes))
															$acces_devoir_valide = 1;
													}
												}
												if ($acces_devoir_valide == 1){
													$nbr_devoirs += 1;
													$chaine_devoir .= "<br /><a href=\"index.php?chapter=".$id_chap_devoir."#devoir".$id_this_devoir."\">".$titre_devoir."</a> - ".jours_restants." : ".$expiration_chaine;
													if ($nbr_devoirs == 1)
														$redir_link = "index.php?chapter=".$id_chap_devoir."#devoir".$id_this_devoir;
												}
											}
										}
									 }
									}
								}
							}
						}
									// **********************************
									
							if ($nbr_devoirs > 0)
								redirection(you_have." ".$nbr_devoirs." ".new_homeworks." : ".$chaine_devoir,$redir_link,10,"warning",0);
							else
								redirection(merci_connexion,$redir_link,3,"tips",0);

						} else goback(mdp_invalide,2,"error",0);
				} else goback(compte_desac,2,"error",0);
			} else goback(login_invalide,2,"error",0);
		}
	} else goback(champ_manq,2,"error",0);
 }
}
mysqli_close($connect);
?>

</body>
</html>