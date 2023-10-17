<?php
/*
 * 	Manhali - Free Learning Management System
 *	felder_ils.php
 *	2012-08-20 22:17
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

if (!empty($id_user_session) && isset($_SESSION['log'])){
goback_button();
function degre_felder($degre){
	if ($degre == 1 || $degre == 3) return degre_incertain;
	else if ($degre == 5 || $degre == 7) return degre_modere;
	else if ($degre == 9 || $degre == 11) return degre_fort;
	else return undefined;
}
if (!empty($_POST['random'])){
	if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
		$_SESSION['random_key'] = $_POST['random'];
		$reflexion = array(1,5,9,13,17,21,25,29,33,37,41);
		$raisonnement = array(2,6,10,14,18,22,26,30,34,38,42);
		$sensorielle = array(3,7,11,15,19,23,27,31,35,39,43);
		$progression = array(4,8,12,16,20,24,28,32,36,40,44);
		$actif = 0;
		$reflechi = 0;
		$sensoriel = 0;
		$intuitif = 0;
		$visuel = 0;
		$verbal = 0;
		$sequentiel = 0;
		$global = 0;
		$err = 0;
		for ($i=1;$i<45;$i++){
			if (!empty($_POST['felder_'.$i]) && ($_POST['felder_'.$i] == 1 || $_POST['felder_'.$i] == 2)){
				if (in_array($i,$reflexion)) {
					if ($_POST['felder_'.$i]== 1) $actif += 1;
					else $reflechi += 1;
				}
				if (in_array($i,$raisonnement)) {
					if ($_POST['felder_'.$i]== 1) $sensoriel  += 1;
					else $intuitif += 1;
				}
				if (in_array($i,$sensorielle)) {
					if ($_POST['felder_'.$i]== 1) $visuel += 1;
					else $verbal += 1;
				}
				if (in_array($i,$progression)) {
					if ($_POST['felder_'.$i]== 1) $sequentiel += 1;
					else $global += 1;
				}
			} else $err = 1;
		}
		if ($err == 0){
			echo "<b>".felder_learning_style." :</b> <a href=\"?felder\"><b>".details."...</b></a><br />";
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
			if ($_SESSION['log'] == 2){
				$chaine_style = $actif."-".$reflechi."-".$sensoriel."-".$intuitif."-".$visuel."-".$verbal."-".$sequentiel."-".$global;
				$update_style = mysql_query("update `" . $tblprefix . "apprenants` set style_apprenant = '$chaine_style' where id_apprenant = $id_user_session;");
			}
		}	else goback(tous_champs,2,"error",0);
	} else goback(err_data_saved,2,"error",0);
}
else {
	echo "<h3><center>".felder_title."</center></h3>";
	echo "<b>".felder_directions."</b><br />";
	echo "<form method=\"POST\" action=\"\">";
	for ($i=1;$i<45;$i++){
		echo "\n<br />".$i.". ".constant("felder_".$i."_q")."<br />";
		echo "\n<input name=\"felder_".$i."\" type=\"radio\" value=\"1\"><b>(a)</b> ".constant("felder_".$i."_1")."<br />";
		echo "\n<input name=\"felder_".$i."\" type=\"radio\" value=\"2\"><b>(b)</b> ".constant("felder_".$i."_2")."<br />";
	}
	echo "<br /><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><center><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></center></form>";
}
} else accueil();
?>