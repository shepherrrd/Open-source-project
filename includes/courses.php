<?php
/*
 * 	Manhali - Free Learning Management System
 *	courses.php
 *	2009-01-02 00:07
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
error_reporting(E_ERROR | E_PARSE);
echo "<script type=\"text/javascript\" src=\"styles/dynMenu.js\"></script>";
echo "<script type=\"text/javascript\" src=\"styles/browserdetect.js\"></script>";

// ******** titre menu vertical ********
$selmenucourses = $connect->query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'courses';");
if (mysqli_num_rows($selmenucourses) == 1) {
	$titre_courses = $selmenucourses->fetch_row()[0];
	$statut_courses = $selmenucourses->fetch_row()[1];
	if ($statut_courses == 1) {

// ******** tutoriels ********
$selecttutoriel = $connect->query("select * from `" . $tblprefix . "tutoriels` where publie_tutoriel = '2' order by ordre_tutoriel;");

if (mysqli_num_rows($selecttutoriel)> 0) {
 echo "<h3><u>".html_ent($titre_courses)."</u></h3>";
 echo "<ul id=\"menu_courses\">\n";

 while($tutoriel = mysqli_fetch_row($selecttutoriel)){

	$idtutoriel = $tutoriel[0];

	// acces tutoriel
	$acces = $tutoriel[13];
	$acces_valide = 0;
	if ($acces == "*")
		$acces_valide = 1;
	else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
		$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
		$select_classe = $connect->query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    if (mysqli_num_rows($select_classe) == 1){
			$id_classe = $select_classe->fetch_row()[0];
			$tab_classes = explode("-",trim($acces,"-"));
			if (in_array($id_classe,$tab_classes))
				$acces_valide = 1;
		}
	}
	if ($acces_valide == 1){
		$tutoriel_name = html_ent($tutoriel[2]);
		$tutoriel_name = wordwrap($tutoriel_name,25,"<br />",true);
		echo "\t<li><a href=\"?tutorial=".$idtutoriel."\">".$tutoriel_name."</a>\n";

// ******** parties ********
		$selectpartie = $connect->query("select * from `" . $tblprefix . "parties` where id_tutoriel = $idtutoriel and publie_partie = '1' order by ordre_partie;");
	
		if (mysqli_num_rows($selectpartie)> 0) {
	
			echo "\t\t<ul>\n";
	
			while($partie = mysqli_fetch_row($selectpartie)){
			
				$idpartie = $partie[0];
				
				$partie_name = html_ent($partie[2]);
				$partie_name = wordwrap($partie_name,25,"<br />",true);
				echo "\t\t\t<li><a href=\"?tutorial=".$idtutoriel."#".$idpartie."\">".$partie_name."</a>\n";

// ******** chapitres ********

				if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
					$selectchapitre = $connect->query("select * from `" . $tblprefix . "chapitres` where id_partie = $idpartie and publie_chapitre = '1' order by ordre_chapitre;");
				else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2)
					$selectchapitre = $connect->query("select * from `" . $tblprefix . "chapitres` where id_partie = $idpartie and publie_chapitre = '1' and (grade_chapitre = '*' or grade_chapitre = '0' or grade_chapitre like '%-$grade_app_session-%') order by ordre_chapitre;");
				else
					$selectchapitre = $connect->query("select * from `" . $tblprefix . "chapitres` where id_partie = $idpartie and publie_chapitre = '1' and grade_chapitre = '*' order by ordre_chapitre;");

				if (mysqli_num_rows($selectchapitre)> 0) {
	
					echo "\t\t\t\t<ul>\n";
	
					while($chapitre = mysqli_fetch_row($selectchapitre)){
			
						$idchapitre = $chapitre[0];
						
						$chapitre_name = html_ent($chapitre[2]);
						$chapitre_name = wordwrap($chapitre_name,25,"<br />",true);
						echo "\t\t\t\t\t<li><a href=\"?chapter=".$idchapitre."\">".$chapitre_name."</a>\n";

// ******** blocs ********
						
						$selectbloc = $connect->query("select * from `" . $tblprefix . "blocs` where id_chapitre = $idchapitre and publie_bloc = '1' order by ordre_bloc;");
	
						if (mysqli_num_rows($selectbloc)> 0) {
							
							echo "\t\t\t\t\t\t<ul>\n";
							
							while($bloc = mysqli_fetch_row($selectbloc)){
			
								$idbloc = $bloc[0];
								
								$bloc_name = html_ent($bloc[2]);
								$bloc_name = wordwrap($bloc_name,25,"<br />",true);
								echo "\t\t\t\t\t\t\t<li><a href=\"?chapter=".$idchapitre."#".$idbloc."\">".$bloc_name."</a></li>\n";
							}
						}

// ******** devoir ********

						$selectdevoir = $connect->query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $idchapitre and publie_devoir = '1' and date_publie_devoir < ".time()." and date_expire_devoir > ".time().";");
						if (mysqli_num_rows($selectdevoir)> 0){
							if (mysqli_num_rows($selectbloc)== 0 && mysqli_num_rows($selectqcm)== 0) echo "\t\t\t\t\t\t<ul>\n";
							echo "\t\t\t\t\t\t\t<li><a class=\"bloc\" href=\"?chapter=".$idchapitre."#devoir\">"."devoir"."</a></li>\n";
						}
						
// ******** qcm ********

						$selectqcm = $connect->query("select id_qcm from `" . $tblprefix . "qcm` where id_chapitre = $idchapitre and publie_qcm = '1';");
						if (mysqli_num_rows($selectqcm)> 0){
							if (mysqli_num_rows($selectbloc)== 0) echo "\t\t\t\t\t\t<ul>\n";
							echo "\t\t\t\t\t\t\t<li><a class=\"bloc\" href=\"?chapter=".$idchapitre."#qcm\">"."qcm"."</a></li>\n";
						}

//***********************

						if (mysqli_num_rows($selectbloc)> 0 || mysqli_num_rows($selectqcm)> 0 || mysqli_num_rows($selectdevoir)> 0)
							echo "\t\t\t\t\t\t</ul>\n";
						
						echo "\t\t\t\t\t</li>\n";
					}
					echo "\t\t\t\t</ul>\n";
				}
				echo "\t\t\t</li>\n";
			}
			echo "\t\t</ul>\n";
		}
		echo "\t</li>\n";
	}
 }
 echo "</ul>\n";
	}
 }
}
echo "<script type=\"text/javascript\">initMenu('menu_courses');</script>";

?>