<?php
/*
 * 	Manhali - Free Learning Management System
 *	poll_manager.php
 *	2009-11-29 14:23
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")){

	echo "<div id=\"titre\">".gestion_sondage."</div>";
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/radio_div.js\"></script>";

	$select_statut_comp = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'poll';");
	if (mysqli_num_rows($select_statut_comp) == 1) {
 		$statut_comp = mysqli_result($select_statut_comp,0);
		if ($statut_comp == 0)
		 echo "<h3><img src=\"../images/icones/warning.png\" /><font color=\"red\">".component_disabled." ".enable_it_now." : </font><a href=\"?inc=components\"\">".gestion_composants."</a></h3>";
	}
	
	if (isset($_GET['id_poll']) && ctype_digit($_GET['id_poll']))
		$id_poll = intval($_GET['id_poll']);
	else $id_poll = 0;
	
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	switch ($do){
		
    // ****************** add_poll **************************
    case "add_poll" : {
    	goback_button();
    	
    	// step 2
    	if (!empty($_POST['send']) && !empty($_POST['poll_options'])) {
    	 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 	$_SESSION['random_key'] = $_POST['random'];
    		if ($_POST['poll_options'] == "simplepoll"){
    			// simple poll
    			$titre_simple = trim($_POST['titre_simple']);
    			if (!empty($titre_simple)){
    				$titre_simple = escape_string($titre_simple);
    				if (isset($_POST['nbr_votes_simple']) && ctype_digit($_POST['nbr_votes_simple']) && $_POST['nbr_votes_simple'] > 1 && $_POST['nbr_votes_simple'] <= 50){
    					$nbr_votes_simple = intval($_POST['nbr_votes_simple']);
    					$insert_question = $connect->query("INSERT INTO `" . $tblprefix . "sondage_questions` VALUES (NULL,0,'$titre_simple','0');");
    					$select_this_question = "select id_question, question from `" . $tblprefix . "sondage_questions` where question = '$titre_simple';";
							$req_select_this_question = $connect->query($select_this_question);
							if (mysqli_num_rows($req_select_this_question) == 1){
								$id_last_question = mysqli_result($req_select_this_question,0,0);
								$question_text = mysqli_result($req_select_this_question,0,1);
								$question_text = html_ent($question_text);
    						echo "\n<form method=\"POST\" action=\"\">";
 	  						echo "<br /><b><font color=\"red\">*</font> ".question." : </b><br /><textarea name=\"titre_simple2\" id=\"titre_simple2\" rows=\"3\" cols=\"50\">".$question_text."</textarea>";
    						for ($i=1;$i<= $nbr_votes_simple;$i++)
    							echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"reponse".$i."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"\">";
    						echo "\n<br /><br /><input type=\"hidden\" name=\"lastquestion\" value=\"".$id_last_question."\"><input type=\"hidden\" name=\"polltype\" value=\"simplepoll\"><input type=\"hidden\" name=\"nbr_reponses\" value=\"".$nbr_votes_simple."\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    					} else goback(erreur_traitement_question,2,"error",1);
    				} else goback(nbr_reponses_invalide,2,"error",1);
    			} else goback(remplir_question,2,"error",1);
    		} else if ($_POST['poll_options'] == "crosstab"){
					// crosstab
    			$titre1_crosstab = trim($_POST['titre1_crosstab']);
    			$titre2_crosstab = trim($_POST['titre2_crosstab']);
    			if (!empty($titre1_crosstab) && !empty($titre2_crosstab)){
    				$titre1_crosstab = escape_string($titre1_crosstab);
    				$titre2_crosstab = escape_string($titre2_crosstab);
    				if (isset($_POST['nbr_votes1_crosstab']) && ctype_digit($_POST['nbr_votes1_crosstab']) && $_POST['nbr_votes1_crosstab'] > 1 && $_POST['nbr_votes1_crosstab'] <= 25 && isset($_POST['nbr_votes2_crosstab']) && ctype_digit($_POST['nbr_votes2_crosstab']) && $_POST['nbr_votes2_crosstab'] > 1 && $_POST['nbr_votes2_crosstab'] <= 25){
    					$nbr_votes1_crosstab = intval($_POST['nbr_votes1_crosstab']);
    					$nbr_votes2_crosstab = intval($_POST['nbr_votes2_crosstab']);
    					$insert_question2 = $connect->query("INSERT INTO `" . $tblprefix . "sondage_questions` VALUES (NULL,0,'$titre2_crosstab','0');");
							$select_this_question2 = "select id_question, question from `" . $tblprefix . "sondage_questions` where question = '$titre2_crosstab';";
							$req_select_this_question2 = $connect->query($select_this_question2);
							if (mysqli_num_rows($req_select_this_question2) == 1){
								$id_question2 = mysqli_result($req_select_this_question2,0,0);
								$question2_text = html_ent(mysqli_result($req_select_this_question2,0,1));
								$insert_question1 = $connect->query("INSERT INTO `" . $tblprefix . "sondage_questions` VALUES (NULL,$id_question2,'$titre1_crosstab','0');");
	    					$select_this_question1 = "select id_question, question from `" . $tblprefix . "sondage_questions` where question = '$titre1_crosstab' and id_conjoint = $id_question2;";
	    					$req_select_this_question1 = $connect->query($select_this_question1);
								if (mysqli_num_rows($req_select_this_question1) == 1){
									$id_question1 = mysqli_result($req_select_this_question1,0,0);
	    						$question1_text = html_ent(mysqli_result($req_select_this_question1,0,1));
	   							echo "\n<form method=\"POST\" action=\"\">";
	   							echo "<table width=\"100%\" border=\"0\"><tr><td width=\"50%\" valign=\"top\">";
    							echo "<br /><b><font color=\"red\">*</font> ".question1."</b><br /><textarea name=\"titre1_crosstab2\" id=\"titre1_crosstab2\" rows=\"3\" cols=\"50\">".$question1_text."</textarea>";
   								for ($i=1;$i<= $nbr_votes1_crosstab;$i++)
    								echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"1reponse".$i."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"\">";
    							echo "</td><td width=\"50%\" valign=\"top\">";
  	  						echo "<br /><b><font color=\"red\">*</font> ".question2."</b><br /><textarea name=\"titre2_crosstab2\" id=\"titre2_crosstab2\" rows=\"3\" cols=\"50\">".$question2_text."</textarea>";
    							for ($i=1;$i<= $nbr_votes2_crosstab;$i++)
    								echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"2reponse".$i."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"\">";
   		 						echo "</td></tr><tr><td width=\"50%\" colspan=\"2\" align=\"center\" valign=\"top\">";
    							echo "\n<br /><br /><input type=\"hidden\" name=\"lastquestion\" value=\"".$id_question1."\"><input type=\"hidden\" name=\"polltype\" value=\"crosstab\"><input type=\"hidden\" name=\"nbr_reponses1\" value=\"".$nbr_votes1_crosstab."\"><input type=\"hidden\" name=\"nbr_reponses2\" value=\"".$nbr_votes2_crosstab."\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\">";
    							echo "</td></tr></table></form>";
    						} else goback(erreur_traitement_question,2,"error",1);
   						} else goback(erreur_traitement_question,2,"error",1);
    				} else goback(nbr_reponses_invalide2,2,"error",1);
    			} else goback(remplir_2question,2,"error",1);
    		} else goback(type_sondage_invalide,2,"error",1);
    	 } else goback(err_data_saved,2,"error",1);
    	}
    	
    	// step 3
    	else if (!empty($_POST['polltype'])) {
    	 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
			 $_SESSION['random_key'] = $_POST['random'];
    		if ($_POST['polltype'] == "simplepoll"){
    			// simple poll
    			$titre_simple2 = trim($_POST['titre_simple2']);
    			if (isset($_POST['lastquestion']) && ctype_digit($_POST['lastquestion']) && isset($_POST['nbr_reponses']) && ctype_digit($_POST['nbr_reponses'])){
    				$this_id_question = intval($_POST['lastquestion']);
    				$nbr_reponses = intval($_POST['nbr_reponses']);
    				if (!empty($titre_simple2)){
    					$titre_simple2 = escape_string($titre_simple2);
    					$update_question_simple = $connect->query("update `" . $tblprefix . "sondage_questions` set question = '$titre_simple2' where id_question = $this_id_question;");
    				}
    				for ($i=1;$i<= $nbr_reponses;$i++){
    					$variable_rep = "reponse".$i;
    					$this_reponse = escape_string(trim($_POST[$variable_rep]));
     					if (!empty($this_reponse)){
    						$insert_reponse = $connect->query("INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (NULL,$this_id_question,'$this_reponse');");
    						$select_this_reponse = "select id_reponse from `" . $tblprefix . "sondage_reponses` where reponse = '$this_reponse' and id_question = $this_id_question;";
    						$req_select_id_reponse = $connect->query($select_this_reponse);
    						if (mysqli_num_rows($req_select_id_reponse) == 1){
    							$id_this_reponse = mysqli_result($req_select_id_reponse,0);
    							$insert_vote = $connect->query("INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (NULL,$id_this_reponse,0,0);");
    						}
    					}
    				}
    				redirection(sondage_cree,"?inc=poll_manager",3,"tips",1);
    			} else goback(erreur_traitement_question,2,"error",1);
    		}
    		else if ($_POST['polltype'] == "crosstab"){
    			// crosstab
    			$titre1_crosstab2 = trim($_POST['titre1_crosstab2']);
    			$titre2_crosstab2 = trim($_POST['titre2_crosstab2']);
    			if (isset($_POST['lastquestion']) && ctype_digit($_POST['lastquestion']) && isset($_POST['nbr_reponses1']) && ctype_digit($_POST['nbr_reponses1']) && isset($_POST['nbr_reponses2']) && ctype_digit($_POST['nbr_reponses2'])){
    				$this_id_question = intval($_POST['lastquestion']);
    				$select_this_question2 = $connect->query("select id_conjoint from `" . $tblprefix . "sondage_questions` where id_question = $this_id_question;");
    				$this_id_question2 = mysqli_result($select_this_question2,0);
    				$nbr_reponses1 = intval($_POST['nbr_reponses1']);
    				$nbr_reponses2 = intval($_POST['nbr_reponses2']);
    				if (!empty($titre1_crosstab2)){
    					$titre1_crosstab2 = escape_string($titre1_crosstab2);
    					$update_question1 = $connect->query("update `" . $tblprefix . "sondage_questions` set question = '$titre1_crosstab2' where id_question = $this_id_question;");
    				}
    				if (!empty($titre2_crosstab2)){
    					$titre2_crosstab2 = escape_string($titre2_crosstab2);
    					$update_question2 = $connect->query("update `" . $tblprefix . "sondage_questions` set question = '$titre2_crosstab2' where id_question = $this_id_question2;");
    				}
    				for ($i=1;$i<= $nbr_reponses1;$i++){
    					$variable_rep1 = "1reponse".$i;
    					$this_reponse1 = escape_string(trim($_POST[$variable_rep1]));
     					if (!empty($this_reponse1))
    						$insert_reponse1 = $connect->query("INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (NULL,$this_id_question,'$this_reponse1');");
    				}
    				for ($i=1;$i<= $nbr_reponses2;$i++){
    					$variable_rep2 = "2reponse".$i;
    					$this_reponse2 = escape_string(trim($_POST[$variable_rep2]));
     					if (!empty($this_reponse2))
    						$insert_reponse2 = $connect->query("INSERT INTO `" . $tblprefix . "sondage_reponses` VALUES (NULL,$this_id_question2,'$this_reponse2');");
    				}
    				$select_reponses1_votes = $connect->query("select id_reponse from `" . $tblprefix . "sondage_reponses` where id_question = $this_id_question;");
						$select_reponses2_votes = $connect->query("select id_reponse from `" . $tblprefix . "sondage_reponses` where id_question = $this_id_question2;");
    				if (mysqli_num_rows($select_reponses1_votes) > 0 && mysqli_num_rows($select_reponses2_votes) > 0) {
 							while($reponse1_vote = mysql_fetch_row($select_reponses1_votes)){
 								@mysql_data_seek($select_reponses2_votes,0);
 								while($reponse2_vote = mysql_fetch_row($select_reponses2_votes))
 									$insert_votes = $connect->query("INSERT INTO `" . $tblprefix . "sondage_votes` VALUES (NULL,$reponse1_vote[0],$reponse2_vote[0],0);");
 							}
 							redirection(sondage_cree,"?inc=poll_manager",3,"tips",1);
    				} else goback(erreur_traitement_question,2,"error",1);
    			} else goback(erreur_traitement_question,2,"error",1);
    		} else goback(type_sondage_invalide,2,"error",1);
    	 } else goback(err_data_saved,2,"error",1);
    	}
    	// form
    	
    	else {
    		
    		echo "\n<form method=\"POST\" action=\"\">";
				echo "\n<p><b><u>".poll_type." :</u></b></p>";
    		echo "\n<b><input name=\"poll_options\" type=\"radio\" value=\"simplepoll\" onclick=\"DisplayHide('poll_options', 'simple_poll')\">".simple_analysis." :</b>";
    	  echo "<div style=\"display: none; margin-left: 20px;\" class=\"poll_options\" id=\"simple_poll\">";
    		echo "<br /><b><font color=\"red\">*</font> ".question." : </b><br /><textarea name=\"titre_simple\" id=\"titre_simple\" rows=\"3\" cols=\"50\"></textarea>";
    		echo "<br /><b><font color=\"red\">*</font> ".responses_nbr." : </b><input name=\"nbr_votes_simple\" type=\"text\" maxlength=\"2\" size=\"2\" value=\"\">";
    		echo "</div><br /><br />";
    		
    		echo "\n<b><input name=\"poll_options\" type=\"radio\" value=\"crosstab\" onclick=\"DisplayHide('poll_options', 'cross_tab')\">".crosstab." :</b>";
    	  echo "<div style=\"display: none; margin-left: 20px;\" class=\"poll_options\" id=\"cross_tab\">";
    		echo "<br /><b><font color=\"red\">*</font> ".question1." :</b><br /><textarea name=\"titre1_crosstab\" id=\"titre1_crosstab\" rows=\"3\" cols=\"50\"></textarea>";
    		echo "<br /><b><font color=\"red\">*</font> ".question1_nbr." : </b><input name=\"nbr_votes1_crosstab\" type=\"text\" maxlength=\"2\" size=\"2\" value=\"\">";
    		echo "<br /><br /><b><font color=\"red\">*</font> ".question2." :</b><br /><textarea name=\"titre2_crosstab\" id=\"titre2_crosstab\" rows=\"3\" cols=\"50\"></textarea>";
    		echo "<br /><b><font color=\"red\">*</font> ".question2_nbr." : </b><input name=\"nbr_votes2_crosstab\" type=\"text\" maxlength=\"2\" size=\"2\" value=\"\">";
    		echo "</div><br /><br />";
    		echo "\n<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    	}
    } break;

    // ****************** view_poll **************************
    case "view_poll" : {
			goback_button();
    	$select_questions = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    	if (mysqli_num_rows($select_questions) == 1){
    		$id_question1 = mysqli_result($select_questions,0,0);
    		$id_conjoint = mysqli_result($select_questions,0,1);
				$question1 = mysqli_result($select_questions,0,2);
				$question1 = html_ent($question1);
				$question1 = bbcode_br($question1);

    		if ($id_conjoint == 0){
	
	// analyse simple ***

		$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");

		// sum votes
		$array_rep1 = array();
		
 		while($reponse1 = mysql_fetch_row($select_reponses1))
 			$array_rep1[] = $reponse1[0];
 					
		$select_sum_votes = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 		
 		foreach ($array_rep1 as $id_rep)
			$select_sum_votes .= $id_rep.",";
		
		$select_sum_votes = substr($select_sum_votes,0,-1) . ") and id_reponse2 = 0;";
		
		$req_sum_votes = $connect->query($select_sum_votes);
		if ($req_sum_votes)
			$sum = mysqli_result($req_sum_votes,0);
		else $sum = 0;
		@mysql_data_seek($select_reponses1,0);
		
		if (mysqli_num_rows($req_sum_votes) > 0){

			// table resultats

			echo "\n<table border=\"1\" class=\"infos\" cellpadding=\"3\" cellspacing=\"2\" align=\"center\" width=\"100%\">\n";
			echo "\n<tr><td align=\"center\" colspan=\"2\" class=\"header\"><div id=\"horizontalmenu_text\"><h3>".$question1."</h3></div></td></tr>";
			while($reponse1 = mysql_fetch_row($select_reponses1)){
				$this_reponse = html_ent($reponse1[2]);
				echo "\n<tr><td align=\"center\" class=\"verticalmenu\"><div id=\"verticalmenu_text\"><b>".$this_reponse."</b></div></td>";
 				$select_votes = $connect->query("select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 = $reponse1[0];");
 				$vote = mysqli_result($select_votes,0);
 				if ($sum != 0)
 					$resultat = round($vote*100/$sum,1);
 				else $resultat = 0;
 				echo "\n<td align=\"center\" class=\"bgr\"><b>".$resultat." %</b></td>";
 				echo "</tr>";
			}
			echo "\n<tr><td align=\"center\" colspan=\"2\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".total_votes." : ".$sum."</b></div></td></tr>";
			echo "</table>";
		} else locationhref_admin("?inc=poll_manager");
	}
	
	// crosstab ***
	
	else {

		$select_question2 = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_conjoint;");
		$id_question2 = mysqli_result($select_question2,0,0);
		$question2 = mysqli_result($select_question2,0,2);
		$question2 = html_ent($question2);
		$question2 = bbcode_br($question2);
		
		$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
		$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question2;");
		$nbr_reponses1 = mysqli_num_rows($select_reponses1)+2;
		$nbr_reponses2 = mysqli_num_rows($select_reponses2)+2;

		// crosstab table

 		echo "\n<table border=\"1\" class=\"infos\" cellpadding=\"3\" cellspacing=\"2\" align=\"center\" width=\"100%\">\n";
 		echo "\n<tr>";
 		echo "\n<td align=\"center\" class=\"header\">&nbsp;</td>";
 		echo "\n<td align=\"center\" colspan=\"".$nbr_reponses2."\" class=\"header\"><div id=\"horizontalmenu_text\"><h3>".$question2."</h3></div></td>";
 		echo "\n</tr>";
 		echo "\n<tr>";
 		echo "\n<td align=\"center\" rowspan=\"".$nbr_reponses1."\" class=\"header\"><div id=\"horizontalmenu_text\"><h3>".$question1."</h3></div></td>";
 		echo "\n<td align=\"center\" class=\"header\">&nbsp;</td>";

		$array_rep1 = array();
		$array_rep2 = array();
 		while($reponse1 = mysql_fetch_row($select_reponses1))
 			$array_rep1[] = $reponse1[0];
 			
 		while($reponse2 = mysql_fetch_row($select_reponses2)){
 			$this_reponse2 = html_ent($reponse2[2]);
 			echo "\n<td align=\"center\" class=\"verticalmenu\"><div id=\"verticalmenu_text\"><b>".$this_reponse2."</b></div></td>";
 			$array_rep2[] = $reponse2[0];
 		}
 		echo "\n<td align=\"center\" class=\"bgr\"><b>".row_total."</b></td>";
 		echo "\n</tr>";
		
		// sum votes
		
		$select_sum_votes = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 		
 		foreach ($array_rep1 as $id_rep)
			$select_sum_votes .= $id_rep.",";
			
		$select_sum_votes = substr($select_sum_votes,0,-1) . ") and id_reponse2 in (";
			
 		foreach ($array_rep2 as $id_rep)
			$select_sum_votes .= $id_rep.",";
		
		$select_sum_votes = substr($select_sum_votes,0,-1) . ");";
		$req_sum_votes = $connect->query($select_sum_votes);
		if ($req_sum_votes)
			$sum = mysqli_result($req_sum_votes,0);
		else $sum = 0;
		@mysql_data_seek($select_reponses1,0);
		
		if (mysqli_num_rows($req_sum_votes) > 0) {

			// boucle results
		
 			while($reponse1 = mysql_fetch_row($select_reponses1)){
 				@mysql_data_seek($select_reponses2,0);
 				$this_reponse1 = html_ent($reponse1[2]);
 				echo "\n<tr><td align=\"center\" class=\"verticalmenu\"><div id=\"verticalmenu_text\"><b>".$this_reponse1."</b></div></td>";
 				$sum_row = 0;
 				while($reponse2 = mysql_fetch_row($select_reponses2)){
 				
 					$select_votes = $connect->query("select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 = $reponse1[0] and id_reponse2 = $reponse2[0];");
 					$vote = mysqli_result($select_votes,0);
 					if ($sum != 0)
 						$resultat = round($vote*100/$sum,1);
 					else $resultat = 0;
 					echo "\n<td align=\"center\"><b>".$resultat." %</b></td>";
 				
 					$sum_row += $resultat;
 				}
 				echo "<td align=\"center\" class=\"bgr\"><b>".$sum_row." %</b></td>";
 				echo "\n</tr>";
 			}
 		
 			// Column total
 		
 			echo "<tr><td align=\"center\" class=\"bgr\"><b>".column_total."</b></td>";
 		
 			$select_votes_column = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse2 in (";
 		
 			foreach ($array_rep2 as $id_rep)
				$select_votes_column .= $id_rep.",";
			
			$select_votes_column = substr($select_votes_column,0,-1) . ") group by id_reponse2;";
			$req_votes_column = $connect->query($select_votes_column);
			
			$total_sum = 0;
			while($vote_column = mysql_fetch_row($req_votes_column)){
				if ($sum != 0)
					$sum_column = round($vote_column[0]*100/$sum,1);
				else $sum_column = 0;
				echo "<td align=\"center\" class=\"bgr\"><b>".$sum_column." %</b></td>";
				$total_sum += $sum_column;
			}
 		
 			echo "<td align=\"center\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".$total_sum." %</b></div></td>";
 			echo "</tr>";
 			echo "\n<tr>";
 			echo "\n<td align=\"center\" class=\"header\">&nbsp;</td>";
 			echo "\n<td align=\"center\" colspan=\"".$nbr_reponses2."\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".total_votes." : ".$sum."</b></div></td>";
 			echo "\n</tr>";
 			echo "\n</table>";
 		} else locationhref_admin("?inc=poll_manager");
	}
	}	
    } break;

    // ****************** edit_poll **************************
    case "edit_poll" : {
    		$select_question1 = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    		if (mysqli_num_rows($select_question1) == 1){
    			$id_question1 = mysqli_result($select_question1,0,0);
    			$id_conjoint = mysqli_result($select_question1,0,1);
					$question1 = mysqli_result($select_question1,0,2);
					$question1 = br_bbcode(bbcode_br(html_ent($question1)));
    			if ($id_conjoint == 0){
    				$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
    				// traitement simple poll
    				if (isset($_POST['polltype']) && $_POST['polltype'] == "simplepoll"){
    					if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
								$_SESSION['random_key'] = $_POST['random'];
								$titre_simple = escape_string(trim($_POST['titre_simple']));
		   					if (!empty($titre_simple) && $titre_simple != $question1)
 									$update_question = $connect->query("update `" . $tblprefix . "sondage_questions` SET question = '$titre_simple' where id_question = $id_question1;");
		  					while($reponse1 = mysql_fetch_row($select_reponses1)){
    							$id_reponse = $reponse1[0];
									$this_reponse = html_ent($reponse1[2]);
									$variable_rep = "reponse".$id_reponse;
    							$reponse_post = escape_string(trim($_POST[$variable_rep]));
    	 						if (!empty($reponse_post) && $reponse_post != $this_reponse)
 										$update_reponse = $connect->query("update `" . $tblprefix . "sondage_reponses` SET reponse = '$reponse_post' where id_reponse = $id_reponse;");
								}
								redirection(poll_edited,"?inc=poll_manager",3,"tips",1);
						 } else goback(err_data_saved,2,"error",1);
    				}
    				// form simple poll
    				else {
    					goback_button();
    					echo "\n<form method=\"POST\" action=\"\">";
    					echo "<br /><b>".question." : </b><br /><textarea name=\"titre_simple\" id=\"titre_simple\" rows=\"3\" cols=\"50\">".$question1."</textarea>";
    					$i = 0;
    					while($reponse1 = mysql_fetch_row($select_reponses1)){
    						$i++;
    						$id_reponse = $reponse1[0];
								$this_reponse = html_ent($reponse1[2]);
								echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"reponse".$id_reponse."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"".$this_reponse."\">";
							}
			    		echo "\n<br /><br /><input type=\"hidden\" name=\"polltype\" value=\"simplepoll\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    				}
    			}
    			else{
    				$select_question2 = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_conjoint;");
						$id_question2 = mysqli_result($select_question2,0,0);
						$question2 = mysqli_result($select_question2,0,2);
						$question2 = br_bbcode(bbcode_br(html_ent($question2)));
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
						$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question2;");
    				// traitement crosstab
    				if (isset($_POST['polltype']) && $_POST['polltype'] == "crosstab"){
    					if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
								$_SESSION['random_key'] = $_POST['random'];

								$titre1_crosstab = escape_string(trim($_POST['titre1_crosstab']));
		   					if (!empty($titre1_crosstab) && $titre1_crosstab != $question1)
 									$update_question1 = $connect->query("update `" . $tblprefix . "sondage_questions` SET question = '$titre1_crosstab' where id_question = $id_question1;");

								$titre2_crosstab = escape_string(trim($_POST['titre2_crosstab']));
		   					if (!empty($titre2_crosstab) && $titre2_crosstab != $question2)
 									$update_question2 = $connect->query("update `" . $tblprefix . "sondage_questions` SET question = '$titre2_crosstab' where id_question = $id_question2;");

		  					while($reponse1 = mysql_fetch_row($select_reponses1)){
    							$id_reponse = $reponse1[0];
									$this_reponse = html_ent($reponse1[2]);
									$variable_rep = "1reponse".$id_reponse;
    							$reponse_post = escape_string(trim($_POST[$variable_rep]));
   	  						if (!empty($reponse_post) && $reponse_post != $this_reponse)
 										$update_reponse = $connect->query("update `" . $tblprefix . "sondage_reponses` SET reponse = '$reponse_post' where id_reponse = $id_reponse;");
								}

		  					while($reponse2 = mysql_fetch_row($select_reponses2)){
    							$id_reponse2 = $reponse2[0];
									$this_reponse2 = html_ent($reponse2[2]);
									$variable_rep2 = "2reponse".$id_reponse2;
    							$reponse_post2 = escape_string(trim($_POST[$variable_rep2]));
   	  						if (!empty($reponse_post2) && $reponse_post2 != $this_reponse2)
 										$update_reponse2 = $connect->query("update `" . $tblprefix . "sondage_reponses` SET reponse = '$reponse_post2' where id_reponse = $id_reponse2;");
								}
								redirection(poll_edited,"?inc=poll_manager",3,"tips",1);
							} else goback(err_data_saved,2,"error",1);
    				}
    				// form crosstab
    				else {
    					goback_button();
    					echo "\n<form method=\"POST\" action=\"\">";
    					echo "<table width=\"100%\" border=\"0\"><tr><td width=\"50%\" valign=\"top\">";
    					echo "<br /><b>".question1."</b><br /><textarea name=\"titre1_crosstab\" id=\"titre1_crosstab\" rows=\"3\" cols=\"50\">".$question1."</textarea>";
    					$i = 0;
    					while($reponse1 = mysql_fetch_row($select_reponses1)){
    						$i++;
    						$id_reponse = $reponse1[0];
								$this_reponse = html_ent($reponse1[2]);
								echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"1reponse".$id_reponse."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"".$this_reponse."\">";
							}
							echo "</td><td width=\"50%\" valign=\"top\">";
    					echo "<br /><b>".question2."</b><br /><textarea name=\"titre2_crosstab\" id=\"titre2_crosstab\" rows=\"3\" cols=\"50\">".$question2."</textarea>";
    					$i = 0;
    					while($reponse2 = mysql_fetch_row($select_reponses2)){
    						$i++;
    						$id_reponse2 = $reponse2[0];
								$this_reponse2 = html_ent($reponse2[2]);
								echo "\n<br /><br /><b>".reponse." ".$i." : </b><input name=\"2reponse".$id_reponse2."\" type=\"text\" maxlength=\"200\" size=\"52\" value=\"".$this_reponse2."\">";
							}
							echo "</td></tr><tr><td width=\"50%\" colspan=\"2\" align=\"center\" valign=\"top\">";
			    		echo "\n<br /><br /><input type=\"hidden\" name=\"polltype\" value=\"crosstab\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\">";
							echo "</td></tr></table></form>";
    				}
    			}
    		} else locationhref_admin("?inc=poll_manager");
    } break;

    // ****************** delete_poll **************************
    case "delete_poll" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_question1 = $connect->query("select id_conjoint from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    		if (mysqli_num_rows($select_question1) == 1){
					$array_rep1 = array();
					$array_rep2 = array();
    			$id_conjoint = mysqli_result($select_question1,0);
    			if ($id_conjoint == 0){
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
						$delete_all_votes = "delete from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$delete_all_votes .= $id_rep.",";
						$delete_all_votes = substr($delete_all_votes,0,-1) . ") and id_reponse2 = 0;";
						$req_delete_all_votes = $connect->query($delete_all_votes);
						$delete_reponses = $connect->query("delete from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll;");
						$delete_question = $connect->query("delete from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    			}
    			else{
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll;");
						$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_conjoint;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
 						while($reponse2 = mysql_fetch_row($select_reponses2))
 							$array_rep2[] = $reponse2[0];
						$delete_all_votes2 = "delete from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$delete_all_votes2 .= $id_rep.",";
						$delete_all_votes2 = substr($delete_all_votes2,0,-1) . ") and id_reponse2 in (";
 						foreach ($array_rep2 as $id_rep)
							$delete_all_votes2 .= $id_rep.",";
						$delete_all_votes2 = substr($delete_all_votes2,0,-1) . ");";
						$req_delete_all_votes2 = $connect->query($delete_all_votes2);
						$delete_reponses2 = $connect->query("delete from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll or id_question = $id_conjoint;");
						$delete_question2 = $connect->query("delete from `" . $tblprefix . "sondage_questions` where id_question = $id_poll or id_question = $id_conjoint;");
    			}
    		}
    	}
			locationhref_admin("?inc=poll_manager");
    } break;
		
    // ****************** activer_poll **************************
    case "activer_poll" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_question1 = $connect->query("select id_conjoint from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    		if (mysqli_num_rows($select_question1) == 1){
    			$id_conjoint = mysqli_result($select_question1,0);
    			if ($id_conjoint == 0){
    				$desactiver_others = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '0' where id_question != $id_poll;");
    				$activer_poll = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '1' where id_question = $id_poll;");
    			}
    			else{
    				$desactiver_others = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '0' where id_question != $id_poll and id_question != $id_conjoint;");
    				$activer_poll = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '1' where id_question = $id_poll or id_question = $id_conjoint;");
    			}
    		}
    	}
			locationhref_admin("?inc=poll_manager");
    } break;

    // ****************** desactiver_poll **************************
    case "desactiver_poll" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_question1 = $connect->query("select id_conjoint from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    		if (mysqli_num_rows($select_question1) == 1){
    			$id_conjoint = mysqli_result($select_question1,0);
    			if ($id_conjoint == 0)
    				$desactiver_poll = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '0' where id_question = $id_poll;");
    			else
    				$desactiver_poll = $connect->query("update `" . $tblprefix . "sondage_questions` set active_question = '0' where id_question = $id_poll or id_question = $id_conjoint;");
    		}
    	}
			locationhref_admin("?inc=poll_manager");
    } break;

    // ****************** reset_poll **************************
    case "reset_poll" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_question1 = $connect->query("select id_conjoint from `" . $tblprefix . "sondage_questions` where id_question = $id_poll;");
    		if (mysqli_num_rows($select_question1) == 1){
					$array_rep1 = array();
					$array_rep2 = array();
    			$id_conjoint = mysqli_result($select_question1,0);
    			if ($id_conjoint == 0){
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
						$reset_all = "update `" . $tblprefix . "sondage_votes` set nbr_votes = 0 where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$reset_all .= $id_rep.",";
						$reset_all = substr($reset_all,0,-1) . ") and id_reponse2 = 0;";
						$req_reset_all = $connect->query($reset_all);
    			}
    			else{
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_poll;");
						$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_conjoint;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
 						while($reponse2 = mysql_fetch_row($select_reponses2))
 							$array_rep2[] = $reponse2[0];
						$reset_all2 = "update `" . $tblprefix . "sondage_votes` set nbr_votes = 0 where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$reset_all2 .= $id_rep.",";
						$reset_all2 = substr($reset_all2,0,-1) . ") and id_reponse2 in (";
 						foreach ($array_rep2 as $id_rep)
							$reset_all2 .= $id_rep.",";
						$reset_all2 = substr($reset_all2,0,-1) . ");";
						$req_reset_all2 = $connect->query($reset_all2);
    			}
    		}
    	}
			locationhref_admin("?inc=poll_manager");
    } break;

   	// ****************** liste_polls **************************	
		default : {
			
			echo "<table border=\"0\"><tr><td><a href=\"?inc=poll_manager&do=add_poll\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=poll_manager&do=add_poll\"><b>".creer_sondage."</b></a></td></tr></table><br />";

			confirmer();
			$max_len = 50;

			if (isset($_GET['l']) && ctype_digit($_GET['l']))
				$page = intval($_GET['l']);
			else $page = 1;
			
			$select_questions = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_conjoint = 0 order by active_question desc;");
			$nbr_trouve = mysqli_num_rows($select_questions);
  		if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
			if ($page <= $page_max && $page > 1 && $page_max > 1)
				$limit = ($page - 1) * $nbr_resultats;
			else {
				$limit = 0;
				$page = 1;
			}
			
			$select_questions_limit = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_conjoint = 0 order by active_question desc limit $limit, $nbr_resultats;");

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".poll_title."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".total_votes."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".raz."</b></td>";
				echo "</tr>\n";

				while($poll = mysql_fetch_row($select_questions_limit)){
					
					$array_rep1 = array();
					$array_rep2 = array();
					
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\" style=\"text-align: left\"><ul>";
					
					$select_other_question = $connect->query("select * from `" . $tblprefix . "sondage_questions` where id_conjoint = $poll[0];");
					if (mysqli_num_rows($select_other_question) > 0){
						
						// crosstab ***
						
						$id_question = mysqli_result($select_other_question,0,0);
						$question1 = mysqli_result($select_other_question,0,2);
						$question1 = html_ent($question1);
						$question1 = readmore($question1,$max_len);
						$active_question = mysqli_result($select_other_question,0,3);
						
						$id_question2 = $poll[0];
						$question2 = $poll[2];
						$question2 = html_ent($question2);
						$question2 = readmore($question2,$max_len);

						if ($active_question == 1)
							$color = "green";
						else $color = "red";
					
						// sum votes
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question;");
						$select_reponses2 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question2;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
 						while($reponse2 = mysql_fetch_row($select_reponses2))
 							$array_rep2[] = $reponse2[0];
						$select_sum_votes = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$select_sum_votes .= $id_rep.",";
						$select_sum_votes = substr($select_sum_votes,0,-1) . ") and id_reponse2 in (";
 						foreach ($array_rep2 as $id_rep)
							$select_sum_votes .= $id_rep.",";
						$select_sum_votes = substr($select_sum_votes,0,-1) . ");";
						$req_sum_votes1 = $connect->query($select_sum_votes);
						if ($req_sum_votes1)
							$sum = mysqli_result($req_sum_votes1,0);
						else $sum = 0;
						
						echo "<li><font color=\"".$color."\">".$question1."</font></li><li><font color=\"".$color."\">".$question2."</font></li>";
					}
					else {
						
						// sondage simple ***
						
						$id_question = $poll[0];
						$question1 = $poll[2];
						$question1 = html_ent($question1);
						$question1 = readmore($question1,$max_len);
						$active_question = $poll[3];
						
						if ($active_question == 1)
							$color = "green";
						else $color = "red";
						
						// sum votes
						$select_reponses1 = $connect->query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question;");
 						while($reponse1 = mysql_fetch_row($select_reponses1))
 							$array_rep1[] = $reponse1[0];
						$select_sum_votes = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 						foreach ($array_rep1 as $id_rep)
							$select_sum_votes .= $id_rep.",";
						$select_sum_votes = substr($select_sum_votes,0,-1) . ") and id_reponse2 = 0;";
						$req_sum_votes2 = $connect->query($select_sum_votes);
						
						if ($req_sum_votes2)
							$sum = mysqli_result($req_sum_votes2,0);
						else $sum = 0;
						
						echo "<li><font color=\"".$color."\">".$question1."</font></li>";
					}
					echo "</ul></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=poll_manager&do=view_poll&id_poll=".$id_question."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=poll_manager&do=edit_poll&id_poll=".$id_question."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=poll_manager&do=delete_poll&id_poll=".$id_question."&key=".$key."','".confirm_supprimer_poll."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					if ($active_question == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=poll_manager&do=desactiver_poll&id_poll=".$id_question."&key=".$key."\"><b>".desactiver."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=poll_manager&do=activer_poll&id_poll=".$id_question."&key=".$key."\"><b>".activer."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$sum."</b></font></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=poll_manager&do=reset_poll&id_poll=".$id_question."&key=".$key."','".confirm_raz_poll."')\" title=\"".raz."\"><img border=\"0\" src=\"../images/others/reset.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "</tr>\n";
				}
				echo "\n</table>";
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=poll_manager&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=poll_manager&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=poll_manager&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=poll_manager&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=poll_manager&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_sondage."<br /><br />";
		}
	}
} else echo restricted_access;

?>