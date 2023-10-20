<?php
/*
 * 	Manhali - Free Learning Management System
 *	poll.php
 *	2009-11-29 14:11
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

function mysqli_result($res, $row, $field=0) {

    $res->data_seek($row);

    $datarow = $res->fetch_array();

    return $datarow[$field];

}

$select_statut_poll = $connect->query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'poll';");
if (mysqli_num_rows($select_statut_poll) == 1) {
	$titre_poll = mysqli_result($select_statut_poll,0,0);
	$statut_poll = mysqli_result($select_statut_poll,0,1);
	if ($statut_poll == 1) {
		
	 $select_questions = $connect->query("select * from `" . $tblprefix . "sondage_questions` where active_question = '1';");
	 if (mysqli_num_rows($select_questions) > 0){
		$titre_poll = html_ent($titre_poll);
		echo "<h3><u>".$titre_poll."</u></h3>";

		$select_identification = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'identification';");
		if (mysqli_num_rows($select_identification) == 1)
			$identification = mysqli_result($select_identification,0);
		else $identification = 0;

		if (!empty($_SESSION['log']) && !empty($_SESSION['id']) && $identification == 1){
			if($_SESSION['log'] == 1)
				$user_poll_id = "u".$id_user_session;
			else if($_SESSION['log'] == 2)
				$user_poll_id = "s".$id_user_session;
			else $user_poll_id = "-".$id_user_session;
		}
		else $user_poll_id = "---";

		echo "\n<form method=\"POST\" action=\"\">";
		
		//*********** One question *******************
		
		if (mysqli_num_rows($select_questions) == 1){
			$id_question1 = mysqli_result($select_questions,0,0);
			$question1 = html_ent(mysqli_result($select_questions,0,2));
			$question1 = bbcode_br($question1);
			$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
			if (mysqli_num_rows($select_reponses1) > 0){
				if (!empty($_POST['send']) && !empty($_POST['choix_sondage'])) {
					$choix_sondage = intval($_POST['choix_sondage']);
					$array_rep1 = array();
					while($reponse1 = mysqli_fetch_row($select_reponses1))
 						$array_rep1[] = $reponse1[0];
 						
 					if (in_array($choix_sondage, $array_rep1)){
						
						$cookie_question = "poll".$id_question1;
						$ip_user = $_SERVER['REMOTE_ADDR'];
						$select_ip = $connect->query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$ip_user' and HOUR(heure_vote)=".date('H',time())." and id_question = $id_question1;");
						$select_id = $connect->query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$user_poll_id' and heure_vote = '00:00:00' and id_question = $id_question1;");						
						if (mysqli_num_rows($select_ip) == 0 && mysqli_num_rows($select_id) == 0 && !isset($_COOKIE[$cookie_question])) {
							$connect->query("update `" . $tblprefix . "sondage_votes` set nbr_votes=nbr_votes+1 where id_reponse1 = $choix_sondage and id_reponse2 = 0;");
							if (!empty($_SESSION['log']) && !empty($_SESSION['id']))
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$user_poll_id."','00:00:00',$id_question1)");
							else
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$ip_user."',Now(),$id_question1)");
							locationhref_admin("?poll&vote=2");
						} else locationhref_admin("?poll&vote=1");
 					} else locationhref_admin("?poll&vote=0");
				}
				else{
					
					if (!empty($_POST['send']))
						echo "<font color=\"red\"><b>".answer_1question."</b></font><br />";

					echo "\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"100%\">\n";
					echo "\n<tr><td align=\"left\" width=\"100%\"><b>".$question1."</b></td></tr>";
					while($reponse1 = mysqli_fetch_row($select_reponses1)){
						$id_reponse1 = $reponse1[0];
						$text_reponse1 = html_ent($reponse1[2]);
						echo "\n<tr><td align=\"left\" width=\"100%\">";
						echo "<input name=\"choix_sondage\" type=\"radio\" value=\"".$id_reponse1."\">";
						echo "<b><font size=\"2\"> ".$text_reponse1."</font></b>";
 						echo "</td></tr>";
					}
					echo "</table>";
				}
			}
		}
		
		//*********** Two question *******************
		
		else if (mysqli_num_rows($select_questions) > 1){
			$select_question1 = $connect->query("select * from `" . $tblprefix . "sondage_questions` where active_question = '1' and id_conjoint != 0;");
			
			$id_question1 = mysqli_result($select_question1,0,0);
			$id_conjoint1 = mysqli_result($select_question1,0,1);
			$question1 = html_ent(mysqli_result($select_question1,0,2));
			$question1 = bbcode_br($question1);
			
			$select_question2 = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_conjoint1;");
			$id_question2 = mysqli_result($select_question2,0,0);
			$question2 = html_ent(mysqli_result($select_question2,0,2));
			$question2 = bbcode_br($question2);
		
			$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
			$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question2;");

			if (mysqli_num_rows($select_reponses1) > 0 && mysqli_num_rows($select_reponses2) > 0){
				if (!empty($_POST['send']) && !empty($_POST['choix_sondage1']) && !empty($_POST['choix_sondage2'])) {

					$choix_sondage1 = intval($_POST['choix_sondage1']);
					$choix_sondage2 = intval($_POST['choix_sondage2']);
					$array_rep1 = array();
					$array_rep2 = array();
					
					while($reponse1 = mysqli_fetch_row($select_reponses1))
 						$array_rep1[] = $reponse1[0];
 					
 					while($reponse2 = mysqli_fetch_row($select_reponses2))
 						$array_rep2[] = $reponse2[0];
 						
 					if (in_array($choix_sondage1, $array_rep1) && in_array($choix_sondage2, $array_rep2)){
	
						$cookie_question1 = "poll".$id_question1;
						$cookie_question2 = "poll".$id_question2;
						$ip_user = $_SERVER['REMOTE_ADDR'];
						$select_ip = $connect->query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$ip_user' and HOUR(heure_vote)=".date('H',time())." and (id_question = $id_question1 or id_question = $id_question2);");
						$select_id = $connect->query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$user_poll_id' and heure_vote = '00:00:00' and (id_question = $id_question1 or id_question = $id_question2);");
						if (mysqli_num_rows($select_ip) == 0 && mysqli_num_rows($select_id) == 0 && !isset($_COOKIE[$cookie_question1]) && !isset($_COOKIE[$cookie_question2])) {
							
							$connect->query("update `" . $tblprefix . "sondage_votes` set nbr_votes=nbr_votes+1 where id_reponse1 = $choix_sondage1 and id_reponse2 = $choix_sondage2;");
							
							if (!empty($_SESSION['log']) && !empty($_SESSION['id'])){
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$user_poll_id."','00:00:00',$id_question1)");
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$user_poll_id."','00:00:00',$id_question2)");
							}
							else{
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$ip_user."',Now(),$id_question1)");
								$connect->query("INSERT INTO `" . $tblprefix . "sondage_ip` VALUES(NULL,'".$ip_user."',Now(),$id_question2)");
							}
							locationhref_admin("?poll&vote=2");
						} else locationhref_admin("?poll&vote=1");
 					} else locationhref_admin("?poll&vote=0");
				}
				else{
					
					if (!empty($_POST['send']))
						echo "<font color=\"red\"><b>".answer_2question."</b></font><br />";

					echo "\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"100%\">\n";
					
					//question1 form
					echo "\n<tr><td align=\"left\" width=\"100%\"><b>".$question1."</b></td></tr>";
					while($reponse1 = mysqli_fetch_row($select_reponses1)){
						$id_reponse1 = $reponse1[0];
						$text_reponse1 = html_ent($reponse1[2]);
						echo "\n<tr><td align=\"left\" width=\"100%\">";
						echo "<input name=\"choix_sondage1\" type=\"radio\" value=\"".$id_reponse1."\">";
						echo "<b><font size=\"2\"> ".$text_reponse1."</font></b>";
 						echo "</td></tr>";
					}
					
					echo "<tr><td>&nbsp;</td></tr>";
					
					//question2 form
					echo "\n<tr><td align=\"left\" width=\"100%\"><b>".$question2."</b></td></tr>";
					while($reponse2 = mysqli_fetch_row($select_reponses2)){
						$id_reponse2 = $reponse2[0];
						$text_reponse2 = html_ent($reponse2[2]);
						echo "\n<tr><td align=\"left\" width=\"100%\">";
						echo "<input name=\"choix_sondage2\" type=\"radio\" value=\"".$id_reponse2."\">";
						echo "<b><font size=\"2\"> ".$text_reponse2."</font></b>";
 						echo "</td></tr>";
					}
					echo "</table>";
				}
			}
		}
		echo "\n<br /><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"searchbtn\" value=\"" .btnvote. "\">";
		echo "&nbsp;&nbsp;<input type=\"button\" class=\"searchbtn\" value=\"".resultats."\" onclick=\"window.location.href='?poll'\" /></form>";
	 }
	}
}
?>