<?php
/*
 * 	Manhali - Free Learning Management System
 *	auth.php
 *	2009-01-02 23:41
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
	include_once ("../includes/dbconfig.php");
	include_once ("../includes/security_functions.php");
	open_session($adminfolder);
	include_once ("admin_language.php");
	include_once ("../includes/display_functions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali administration</title>
<link rel="stylesheet" href="../styles/style1.css" type="text/css" />
<link rel="shortcut icon" href="../styles/favicon.gif" type="image/x-icon" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

<!--[if lt IE 7]>
<script defer type="text/javascript" src="../styles/pngfix.js"></script>
<![endif]-->

</head>
<body>
<br />

<?php

if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['password']) && !empty($_POST['password'])) {

		$login = escape_string($_POST['login']);
		$password = escape_string($_POST['password']);

		$seluser = "SELECT * from `" . $tblprefix . "users` WHERE identifiant_user = '" . $login . "';";
		$resultat = $connect->query($seluser);

		if ($resultat && mysqli_num_rows($resultat) == 1) {
			$row= $resultat->fetch_assoc();
			$id =$row["id_user"];
			$pass = $row["mdp_user"];
			$statut = $row["active_user"];
			$grade = $row["grade_user"];

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

								$ip_user = $_SERVER['REMOTE_ADDR'];
								$select_acces = $connect->query("select id_acces from `" . $tblprefix . "infos_acces` where type_user = 'u' and id_user = $id and ip_user = '$ip_user';");
								if (mysqli_num_rows($select_acces) > 0){
									$id_this_acces = $select_acces->fetch_assoc();
									$update_acces = $connect->query("update `" . $tblprefix . "infos_acces` set date_acces = ".time()." where id_acces = $id_this_acces;");
								}
								else {
									$insert_acces = $connect->query("INSERT INTO `" . $tblprefix . "infos_acces` VALUES (NULL,'u',$id,'$ip_user',".time().");");
								}
								
								redirection("logged in","admin_home.php",3,"tips",1);
						} else goback(mdp_invalide,2,"error",1);
			} else goback(compte_desac,2,"error",1);
		} else goback(login_invalide,2,"error",1);
} else goback(champ_manq,2,"error",1);
?>

</body>
</html>