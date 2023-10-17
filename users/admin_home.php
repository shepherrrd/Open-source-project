<?php
/*
 * 	Manhali - Free Learning Management System
 *	admin_home.php
 *	2009-01-02 23:27
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
	include_once ("../includes/site_infos.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali administration</title>
<link rel="stylesheet" href="../styles/style1.css" type="text/css" />
<link rel="stylesheet" href="../styles/bbcode.css" type="text/css" />
<link rel="stylesheet" href="../styles/calendar.css" type="text/css" />
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

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<?php

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($_SESSION['id']) && isset($_SESSION['key'])){
  $id_user_session = escape_string($_SESSION['id']);
  $key = $_SESSION['key'];
	$grade_user_session = $_SESSION['grade'];
	$select_pseudo_user = mysql_query("select identifiant_user, last_connect from `" . $tblprefix . "users` where id_user = $id_user_session;");
  if (mysql_num_rows($select_pseudo_user) == 1){
    $pseudo = mysql_result($select_pseudo_user,0,0);
    $last_connect = mysql_result($select_pseudo_user,0,1);
  }
	else {
    $pseudo = "";
    $last_connect = time();
  }
	$update_connectednow1 = mysql_query("update `" . $tblprefix . "users` set connected_now = '0' where last_connect + 900 < ".time().";");
	$update_connectednow2 = mysql_query("update `" . $tblprefix . "users` set connected_now = '1', nbr_pages = nbr_pages + 1 where id_user = $id_user_session;");
	
	if (isset($_GET['task']) && $_GET['task'] == "logout") {
				
        $update_connectednow = mysql_query("update `" . $tblprefix . "users` set connected_now = '0' where id_user = $id_user_session;");
        close_session();
        if (isset($_GET['ses']))
        	redirection(exsession,"../index.php",3,"forbidden",1);
        else
        	redirection(deconnect,"../index.php",3,"tips",1);
	}
	else {
				if (!isset($_SESSION['timeout'])) $_SESSION['timeout']=time();
				if(time() - $_SESSION['timeout'] > 900) locationhref_admin("?task=logout&ses=1");
				else {
					$last_duration = time() - $_SESSION['timeout'];
					$update_connectednow = mysql_query("update `" . $tblprefix . "users` set last_duration = last_duration + $last_duration, total_duration = total_duration + $last_duration where id_user = $id_user_session;");
					$_SESSION['timeout']=time();
				}
?>
<table align="center" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3" align="center" bgcolor="#f7a568" width="100%" height="90" valign="bottom">
			<img src="../images/tpl_img1/manhali_logo_admin.gif" border="0" width="80" height="80" alt="" />
			<img src="../images/tpl_img1/header_admin.jpg" border="0" width="520" height="80" alt="" />
		</td>
	</tr>
	<tr>
		<td width="2%" height="100%" background="../images/tpl_img1/bgleftright.gif" style="background-repeat: repeat-x;"></td>
		<td width="96%" height="100%">
			<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="62">
						<table width="100%" height="62" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="24" height="62" background="../images/tpl_img1/tpl_03.gif"></td>
								<td height="62" background="../images/tpl_img1/tpl_04.gif" style="background-repeat: repeat-x;" align="center">
									<table width="98%" height="62" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="15%" align="left">
											<?php echo "<b>".page_administration."</b>"; ?>
											</td>
											<td width="15%" align="center">
												<?php echo "<a href=\"../\" class=\"menu_admin\" target=\"_blank\">".previsualisation."</a>"; ?>
											</td>
											<td width="25%" align="center">
												<?php
													if (!empty($_POST['langue'])){
														$langue = escape_string($_POST['langue']);
														if (file_exists("../language/".$langue."/admin.ini"))
															$update_site = mysql_query("update `" . $tblprefix . "users` SET langue_user = '$langue' where id_user = $id_user_session;");
														if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['SERVER_NAME']))
															$redir_link = $_SERVER['HTTP_REFERER'];
														else $redir_link = "admin_home.php";
														locationhref_admin($redir_link);
													}
													if($dir = opendir("../language")){
														echo "\n<form method=\"POST\" action=\"\"><b>".change_language."</b> ";
    												echo "<select name=\"langue\" onchange=\"this.form.submit();\">";
    												while($lang = readdir($dir)) {
															if ($lang != ".." && $lang != "." && strtolower(substr($lang,0,5) != "index")) {
																if ($fd = @fopen("../language/".$lang."/admin.ini","r")){
																	while (!feof($fd)) {
																		$line = fgets($fd);
  																	if (strpos($line,"language=")===0 || strpos($line,"language="))
  																		break;
  																}
  																@fclose($fd);
  																$line = substr($line,strpos($line,"=")+1);
																} else $line = "";
																if ($language == $lang)
																	echo "<option  value=\"".$lang."\" selected=\"selected\">".$line." (".$lang.")</option>";
																else echo "<option  value=\"".$lang."\">".$line." (".$lang.")</option>";
															}
														}
    												echo "</select></form>";
    												closedir($dir);
    											}
												?>
											</td>
											<td width="45%" align="right">
												<?php
        									echo "<b>".$pseudo."</b> (";
													echo $grade_tab[$grade_user_session];
        									echo ") <a href=\"?task=logout\" class=\"menu_admin\">" .linkdeconnection. "</a>";
												?>
											</td>
										</tr>
									</table>
								</td>
								<td width="24" height="62" background="../images/tpl_img1/tpl_05.gif">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="259" valign="top" bgcolor="#FFFFFF" background="../images/tpl_img1/tpl_leftborder.gif" style="background-repeat: repeat-y;">
									<table width="259" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="3" width="259" height="12" background="../images/tpl_img1/tpl_07_admin.gif"></td>
										</tr>
										<tr>
											<td width="24" height="100%" background="../images/tpl_img1/tpl_10.gif" style="background-repeat: repeat-y;"></td>
											<td bgcolor="#c89664" width="208" align="center" valign="top">
												<table width="208" height="100%" border="0" cellpadding="5" cellspacing="5">
													<tr>
														<td width="100%" valign="top" align="left">
															<?php include_once ("admin_menu.php"); ?>
														</td>
													</tr>
												</table>
											</td>
											<td width="27" height="100%" background="../images/tpl_img1/tpl_12.gif" style="background-repeat: repeat-y;"></td>
										</tr>
										<tr>
											<td colspan="3" width="259" height="12" background="../images/tpl_img1/tpl_13_admin.gif">
											</td>
										</tr>
									</table>
								</td>
								<td bgcolor="#FFFFFF" valign="top" height="430">
									<br />
									<?php
										$select_nombre_elements_page = mysql_query("select nombre_elements_page from `" . $tblprefix . "site_infos`;");
										if (mysql_num_rows($select_nombre_elements_page) == 1 && mysql_result($select_nombre_elements_page,0) > 0)
											$nbr_resultats = mysql_result($select_nombre_elements_page,0);
										else $nbr_resultats = 10;
										
										if (file_exists("../install/next_install.php")) {
											echo "<h3><center><img src=\"../images/icones/critical.png\" /><font color=\"red\">".del_admin_folder." install</font></center></h3>";
										}
										$pages_interdites = array("admin_home","admin_language","admin_menu","auth","calculate_behavior_note","index","ckeditor_init","print","ziplib");
										if (isset($_GET['inc']) && file_exists($_GET['inc'].".php") && substr($_GET['inc'],0,1) != "." && !in_array($_GET['inc'],$pages_interdites))
											include_once ($_GET['inc'].".php");
										else include_once ("statistics.php");
										echo "<br /><br />";
									?>
								</td>
								<td width="24" height="100%" background="../images/tpl_img1/tpl_09.gif" style="background-repeat: repeat-y;"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="13">
						<table width="100%" height="13" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="24" height="13" background="../images/tpl_img1/tpl_15left.gif"></td>
								<td height="13" background="../images/tpl_img1/tpl_14.gif" style="background-repeat: repeat-x; font-size: 1px">&nbsp;</td>
								<td width="24" height="13" background="../images/tpl_img1/tpl_15.gif"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="40" width="100%" align="center">
						<?php echo $footer_site; @mysql_close($connect); ?>
					</td>
				</tr>
			</table>
		</td>
		<td width="2%" background="../images/tpl_img1/bgleftright.gif" style="background-repeat: repeat-x;"></td>
	</tr>
</table>
<?php
	}
} else redirection(exsession,"../index.php",3,"forbidden",1);
?>
</body>

</html>