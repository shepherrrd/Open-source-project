<?php
/*
 * 	Manhali - Free Learning Management System
 *	index.php
 *	2009-01-01 23:37
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

	// error_reporting(0);

	define("access_const","access_const");
	
	header('Content-type: text/html; charset=UTF-8');
	mb_internal_encoding("UTF-8");
	include_once ("includes/dbconfig.php");
	include_once ("includes/security_functions.php");
	open_session("");

	// if (!$db || !$setnames || !$bd_test_req) {
	// 	close_session();
	// 	if (file_exists("install/next_install.php"))
	// 		header("Location: install.php");
	// 	else
	// 		header("Location: error.php?err=db");
	// }
	
	include_once ("includes/language.php");
	include_once ("includes/anti_aspiration.php");
	include_once ("includes/polls_scan.php");
	include_once ("includes/tpl.php");
	include_once ("includes/display_functions.php");
	include_once ("includes/site_infos.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<title><?php echo $title_site; ?></title>
<link rel="stylesheet" href="styles/style<?php echo $tpl; ?>.css" type="text/css" />
<link rel="shortcut icon" href="styles/favicon.ico" type="image/x-icon" />
<link rel="icon" href="styles/favicon.gif" type="image/gif" />

<?php include_once ("includes/meta.php"); ?>

<!--[if lt IE 7]>
<script defer type="text/javascript" src="styles/pngfix.js"></script>
<![endif]-->

<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shCore.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushCpp.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushCSharp.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushCss.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushDelphi.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushJava.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushPerl.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushPhp.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushPython.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushRuby.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushSql.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushVb.js"></script>
<script type="text/javascript" src="jscripts/syntaxhighlighter_3.0.83/scripts/shBrushXml.js"></script>
<link type="text/css" rel="stylesheet" href="jscripts/syntaxhighlighter_3.0.83/styles/shCoreDefault.css"/>
<script type="text/javascript">SyntaxHighlighter.all();</script>
</head>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">

<table align="center" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3" align="center" class="header" width="100%" height="130" valign="bottom">
			<table align="center" width="100%" height="120" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" height="120">
						<img src="images/tpl_img<?php echo $tpl; ?>/manhali_logo.gif" border="0" width="120" height="120" alt="" />
						<img src="images/tpl_img<?php echo $tpl; ?>/header.jpg" border="0" width="620" height="120" alt="" />
					</td>
					<td align="center" height="120" width="30" valign="top">
						<table align="center" width="30" height="120" border="0" cellpadding="0" cellspacing="0">
							<tr><td valign="top" width="30" height="25">
								<a href="?tpl=1"><img src="images/others/color1.jpg" border="0" width="20" height="20" /></a>
							</td></tr>
							<tr><td valign="top" width="30" height="25">
								<a href="?tpl=2"><img src="images/others/color2.jpg" border="0" width="20" height="20" /></a>
							</td></tr>
							<tr><td valign="top" width="30" height="25">
								<a href="?tpl=3"><img src="images/others/color3.jpg" border="0" width="20" height="20" /></a>
							</td></tr>
							<tr><td valign="top" width="30" height="25">
								<a href="?tpl=4"><img src="images/others/color4.jpg" border="0" width="20" height="20" /></a>
							</td></tr>
							<tr><td valign="top" width="30" height="20">
								<a href="?tpl=5"><img src="images/others/color5.jpg" border="0" width="20" height="20" /></a>
							</td></tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center" class="header" width="100%" height="40">
			<div id="hormenu">
				<?php include_once ("includes/horizontal_menu.php"); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="2%" height="100%" background="images/tpl_img<?php echo $tpl; ?>/bgleftright.gif" style="background-repeat: repeat-x;"></td>
		<td width="96%" height="100%">
			<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="62">
						<table width="100%" height="62" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="24" height="62" background="images/tpl_img<?php echo $tpl; ?>/tpl_03.gif"></td>
								<td height="62" background="images/tpl_img<?php echo $tpl; ?>/tpl_04.gif" style="background-repeat: repeat-x;" align="center">
									<table width="97%" height="62" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td align="left" height="100%">
												<div id="breadcrumbs">&nbsp;
													<?php include_once ("includes/breadcrumbs.php"); ?>
												</div>
											</td>
											<td width="260" align="right" height="100%">
        									<?php include_once ("includes/search.php"); ?>
											</td>
										</tr>
									</table>
								</td>
								<td width="24" height="62" background="images/tpl_img<?php echo $tpl; ?>/tpl_05.gif">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="232" valign="top" bgcolor="#FFFFFF" background="images/tpl_img<?php echo $tpl; ?>/tpl_leftborder.gif" style="background-repeat: repeat-y;">
									<table width="232" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="3" width="232" height="12" background="images/tpl_img<?php echo $tpl; ?>/tpl_07.gif"></td>
										</tr>
										<tr>
											<td width="24" height="100%" background="images/tpl_img<?php echo $tpl; ?>/tpl_10.gif" style="background-repeat: repeat-y;"></td>
											<td class="verticalmenu" width="181" align="center" valign="top">
												<div id="verticalmenu_text">
													<?php include_once ("includes/vertical_panel.php"); ?>
												</div>
											</td>
											<td width="27" height="100%" background="images/tpl_img<?php echo $tpl; ?>/tpl_12.gif" style="background-repeat: repeat-y;"></td>
										</tr>
										<tr>
											<td colspan="3" width="232" height="12" background="images/tpl_img<?php echo $tpl; ?>/tpl_13.gif">
											</td>
										</tr>
									</table>
								</td>
								<td bgcolor="#FFFFFF" valign="top" height="300"><br />
									<?php 
									//if(!isset($_GET['article']) || isset($_GET['profiles'])) include_once ("includes/body.php"); 
										 if (isset($_GET['chapter']) && ctype_digit($_GET['chapter'])) {
											include_once ("includes/chaps.php");
										}
										
										//***************************************************
										// *** traitement recherche ***
										else if (isset($_GET['search'])) {
											include_once ("includes/search_inc.php");
										}
										
										//***************************************************
										// *** traitement articles ***
										else if (isset($_GET['article'])) {
											echo "<script>console.log('article');</script>";
											include_once ("includes/articles_inc.php");
										}
										
										//***************************************************
										// *** traitement documents ***
										else if (isset($_GET['documents'])) {
											include_once ("includes/documents_inc.php");
										}
										
										//***************************************************
										// *** traitement questionnaire ***
										else if (isset($_GET['questionnaire'])) {
											include_once ("includes/felder_ils.php");
										}
										
										//***************************************************
										// *** traitement kolb ***
										else if (isset($_GET['kolb'])) {
											include_once ("includes/kolb.php");
										}
										
										//***************************************************
										// *** traitement felder ***
										else if (isset($_GET['felder'])) {
											include_once ("includes/felder.php");
										}
										
										//***************************************************
										// *** traitement sondage resultats ***
										else if (isset($_GET['poll'])) {
											include_once ("includes/poll_inc.php");
										}
										
										//***************************************************
										// *** traitement inscription ***
										else if (isset($_GET['register'])) {
											include_once ("includes/register_inc.php");
										}
										
										//***************************************************
										// *** traitement reset pass ***
										else if (isset($_GET['reset_pass'])) {
											include_once ("includes/reset_pass.php");
										}
										
										//***************************************************
										// *** traitement profiles ***
										else if (isset($_GET['profiles'])) {
											include_once ("includes/profiles.php");
										}
										
										//***************************************************
										// *** traitement s_profiles ***
										else if (isset($_GET['s_profiles'])) {
											include_once ("includes/s_profiles.php");
										}
										
										//***************************************************
										// *** traitement s_messages ***
										else if (isset($_GET['s_messages'])) {
											include_once ("includes/s_messages.php");
										}
									
									?>
								</td>
								<td width="24" height="100%" background="images/tpl_img<?php echo $tpl; ?>/tpl_09.gif" style="background-repeat: repeat-y;"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="13">
						<table width="100%" height="13" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="24" height="13" background="images/tpl_img<?php echo $tpl; ?>/tpl_15left.gif"></td>
								<td height="13" background="images/tpl_img<?php echo $tpl; ?>/tpl_14.gif" style="background-repeat: repeat-x; font-size: 1px">&nbsp;</td>
								<td width="24" height="13" background="images/tpl_img<?php echo $tpl; ?>/tpl_15.gif"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="40" width="100%" align="center">
						<?php
							echo $footer_site;
							$connect->close();
						?>
					</td>
				</tr>
			</table>
		</td>
		<td width="2%" background="images/tpl_img<?php echo $tpl; ?>/bgleftright.gif" style="background-repeat: repeat-x;"></td>
	</tr>
</table>

</body>

</html>