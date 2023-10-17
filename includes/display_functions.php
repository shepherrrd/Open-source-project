<?php
/*
 * 	Manhali - Free Learning Management System
 *	display_functions.php
 *	2009-01-04 21:02
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

function accueil() {
	echo "<script type=\"text/javascript\">window.location.href = \"?\";</script>";
}
function locationhref_admin($link) {
	echo "<script type=\"text/javascript\">window.location.href = \"".$link."\";</script>";
}
function goback_button() {
	if (file_exists("../images/others/back.png"))
		$back_img = "../images/others/back.png";
	else if (file_exists("images/others/back.png"))
		$back_img = "images/others/back.png";
  echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
  if (!empty($back_img))
  	echo "<tr><td><a href=\"javascript:history.back()\" title=\"".retour."\"><img src=\"".$back_img."\" border=\"0\" width=\"48\" height=\"48\" /></a></td></tr>";
  echo "<tr><td><a href=\"javascript:history.back()\" title=\"".retour."\"><b>".retour."</b></a></td></tr></table><br />";
}
function goback_lien($lien_back) {
	if (file_exists("../images/others/back.png"))
		$back_img = "../images/others/back.png";
	else if (file_exists("images/others/back.png"))
		$back_img = "images/others/back.png";
  echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
  if (!empty($back_img))
  	echo "<tr><td><a href=\"".$lien_back."\" title=\"".retour."\"><img src=\"".$back_img."\" border=\"0\" width=\"48\" height=\"48\" /></a></td></tr>";
  echo "<tr><td><a href=\"".$lien_back."\" title=\"".retour."\"><b>".retour."</b></a></td></tr></table><br />";
}
function confirmer() {
	echo "<script type=\"text/javascript\">
				function confirmer(lien,message){
					Check = confirm(message);
					if(Check == true) location.href = lien;
				}
				</script>
			 ";
}
function redirection($msglang,$lienredir,$nbrsec,$typemsg, $path) {
	if ($path == 1) $folder = "../";
	else $folder = "";
	
	$textredir = "<br /><br /><table class=\"redirection\" cellspacing=\"2\" cellpadding=\"10\" align=\"center\" style=\"border: 2px solid #000000;\"><tr><td align=\"center\" style=\"border: 1px solid #000000;\"><h3><img border=\"0\" width=\"50\" height=\"50\" src=\"".$folder."images/icones/" .$typemsg. ".png\" />&nbsp;&nbsp;&nbsp;";
	$textredir .= $msglang. "</h3>";
	$textredir .= "<br /><h4>".autoredir." ".$nbrsec." ".sec;
	$textredir .= "<br /><br />".reload." <a href=\"" .$lienredir. "\">" .click. "</a></h4></td></tr></table>";
	$textredir .= "<script type=\"text/javascript\">window.setTimeout(\"location=('" .$lienredir. "');\"," .$nbrsec. "000)</script>";
	echo $textredir;
}
function goback($msglang,$nbrsec,$typemsg, $path) {
	if ($path == 1) $folder = "../";
	else $folder = "";
	$textredir = "<br /><br /><table class=\"redirection\" cellspacing=\"2\" cellpadding=\"10\" align=\"center\" style=\"border: 2px solid #000000;\"><tr><td align=\"center\" style=\"border: 1px solid #000000;\"><h3><img border=\"0\" width=\"50\" height=\"50\" src=\"".$folder."images/icones/" .$typemsg. ".png\" />&nbsp;&nbsp;&nbsp;";
	$textredir .= $msglang. "</h3>";
	$textredir .= "<br /><h4>".autoredir." ".$nbrsec." ".sec;
	$textredir .= "<br /><br />".reload." <a href=\"javascript:history.back()\">" .click. "</a></h4></td></tr></table>";
	$textredir .= "<script type=\"text/javascript\">window.setTimeout(\"history.back();\"," .$nbrsec. "000)</script>";
	echo $textredir;
}
function readmore($string,$length) {
	if(strlen($string) > $length && strpos($string," ",$length-3) && strpos($string,"&lt;",$length-3)===false && strpos($string,"&gt;",$length-3)===false)
		$string = substr($string,0,strpos($string," ",$length-3))."...";
	return $string;
}
function special_chars($in) {
	mb_regex_encoding('utf-8');
	$search = array ('@(é|è|ê|ë|Ê|Ë)@i','@(à|â|ä|Â|Ä)@i','@(î|ï|Î|Ï)@i','@(û|ù|ü|Û|Ü)@i','@(ô|ö|Ô|Ö)@i','@(ç)@i','@( )@i','@[^a-zA-Z0-9_\-.]@');
	$replace = array ('e','a','i','u','o','c','_','');
	return preg_replace($search, $replace, $in);
}
function no_br($value){
	$value = preg_replace("/<br \/>/", " ", $value);
	$value = preg_replace("/<p>/", " ", $value);
	$value = preg_replace("/<\/p>/", " ", $value);
	return $value;
}
function bbcode_br($value){
	$value = preg_replace("/[\r\n]+/", "<br />", $value);
	return $value;
}
function br_bbcode($value){
	$value = preg_replace("/<br \/>/", "\n", $value);
	return $value;
}
function licence_const($thelicense){
	switch ($thelicense) {
		case "by":
    	return by;
    	break;
		case "by-nc":
    	return by_nc;
   	 	break;
		case "by-nc-nd":
 			return by_nc_nd;
 			break;
		case "by-nc-sa":
 			return by_nc_sa;
			break;
		case "by-nd":
 			return by_nd;
 			break;
		case "by-sa":
  		return by_sa;
			break;
	}
}
function mail_antispam($mail_auteur,$path){

	if ($path == 1)
		$add = "../";
	else $add = "";
	
	$mail_auteur = strtolower($mail_auteur);
	$mail_sep = explode("@",$mail_auteur,2);

	if (count($mail_sep) == 2){

		$mail_sep2 = explode(".",$mail_sep[1],2);

		if (count($mail_sep2) == 2){
	
			$fournisseur = $mail_sep2[0];
			$tab_mail1 = array('gmail','hotmail','yahoo','msn','live','aol','mail');
			$tab_mail2 = array('gmail' => '<img border="0" src="'.$add.'images/others/imgn1.jpg" width="36" height="15" />','hotmail' => '<img border="0" src="'.$add.'images/others/imgn2.jpg" width="48" height="12" />','yahoo' => '<img border="0" src="'.$add.'images/others/imgn3.jpg" width="38" height="15" />','msn' => '<img border="0" src="'.$add.'images/others/imgn4.jpg" width="28" height="9" />','live' => '<img border="0" src="'.$add.'images/others/imgn5.jpg" width="24" height="12" />','aol' => '<img border="0" src="'.$add.'images/others/imgn6.jpg" width="20" height="12" />','mail' => '<img border="0" src="'.$add.'images/others/imgn7.jpg" width="28" height="12" />');
			
			if (in_array($fournisseur, $tab_mail1))
				$fournisseur = $tab_mail2[$fournisseur];

			$domainmail = $mail_sep2[1];
			
			if ($domainmail == "com")
				$domainmail = '<img border="0" src="'.$add.'images/others/imgn8.jpg" width="26" height="9" />';

			$mail_auteur = $mail_sep[0]."<img border=\"0\" src=\"".$add."images/others/imgn.jpg\" width=\"14\" height=\"15\" />".$fournisseur."<img border=\"0\" src=\"".$add."images/others/imgn0.jpg\" width=\"4\" height=\"4\" />".$domainmail;

		} else $mail_auteur = "";
	} else $mail_auteur = "";
	return $mail_auteur;
}
function set_date($dateformat1,$value) {
	if (isset($dateformat1) && is_numeric($dateformat1)){
		if ($dateformat1 == 1) $dateformat2 = "d/m/Y - H:i";
		else if ($dateformat1 == 2) $dateformat2 = "d-m-Y - H:i";
		else if ($dateformat1 == 3) $dateformat2 = "m/d/Y - H:i";
		else if ($dateformat1 == 4) $dateformat2 = "m-d-Y - H:i";
		else $dateformat2 = "d/m/Y - H:i";
	} else $dateformat2 = "d/m/Y - H:i";
	return date($dateformat2,$value);
}
function calcule_duree($duree_1) {
	if (isset($duree_1) && is_numeric($duree_1)){
		$duree_1 = intval($duree_1);
		if ($duree_1 >= 3600){
			$heures = (int)($duree_1 / 3600);
			$minutes_0 = $duree_1 % 3600;
			$minutes = (int)($minutes_0 / 60);
			$secondes = $minutes_0 % 60;
			$duree_2 = $heures."h ".$minutes."m ".$secondes."s";
		}
		else {
			if ($duree_1 >= 60){
				$minutes = (int)($duree_1 / 60);
				$secondes = $duree_1 % 60;
				$duree_2 = $minutes."m ".$secondes."s";
			} else $duree_2 = $duree_1."s";
		}
		return $duree_2;
	} else return 0;
}

$licence_tab = @array("by","by-nd","by-nc-nd","by-nc","by-nc-sa","by-sa");
$grade_tab = array('trainer', 'supervisor', 'admin', 'superadmin');
$month_tab = array(
    1 => 'january',
    2 => 'february',
    3 => 'march',
    4 => 'april',
    5 => 'may',
    6 => 'june',
    7 => 'july',
    8 => 'august',
    9 => 'september',
    10 => 'october',
    11 => 'november',
    12 => 'december'
);?>