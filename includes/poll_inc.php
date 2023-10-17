<?php
/*
 * 	Manhali - Free Learning Management System
 *	poll_inc.php
 *	2011-01-27 12:46
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

	$select_statut_poll = mysql_query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'poll';");
	if (mysql_num_rows($select_statut_poll) == 1) {
		$statut_poll = mysql_result($select_statut_poll,0,1);
		if ($statut_poll == 1) {
			$titre_poll = mysql_result($select_statut_poll,0,0);
			$titre_poll = html_ent($titre_poll);
			echo "<div id=\"titre\">".$titre_poll."</div><br />\n";
			
			if (isset($_GET['vote']) && !empty($_GET['vote']) && ctype_digit($_GET['vote'])) {
				$vote = intval($_GET['vote']);
				echo "<h3>";
				if ($vote == 2)
					echo vote_valide;
				else if ($vote == 1)
					echo deja_vote;
				else echo vote_invalide;
				echo "</h3>";
			}
			
	$select_questions = mysql_query("select * from `" . $tblprefix . "sondage_questions` where active_question = '1';");
	
	// analyse simple ***
	
	if (mysql_num_rows($select_questions) == 1){
		$id_question1 = mysql_result($select_questions,0,0);
		$question1 = mysql_result($select_questions,0,2);
		$question1 = html_ent($question1);
		$question1 = bbcode_br($question1);
		$select_reponses1 = mysql_query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");

		// sum votes
		$array_rep1 = array();
		
 		while($reponse1 = mysql_fetch_row($select_reponses1))
 			$array_rep1[] = $reponse1[0];
 					
		$select_sum_votes = "select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 in (";
 		
 		foreach ($array_rep1 as $id_rep)
			$select_sum_votes .= $id_rep.",";
		
		$select_sum_votes = substr($select_sum_votes,0,-1) . ") and id_reponse2 = 0;";
		
		$req_sum_votes = mysql_query($select_sum_votes);
		if ($req_sum_votes)
			$sum = mysql_result($req_sum_votes,0);
		else $sum = 0;
		@mysql_data_seek($select_reponses1,0);
		
		if (mysql_num_rows($req_sum_votes) > 0){

			// table resultats

			echo "\n<table border=\"1\" class=\"infos\" cellpadding=\"3\" cellspacing=\"2\" align=\"center\" width=\"100%\">\n";
			echo "\n<tr><td align=\"center\" colspan=\"2\" class=\"header\"><div id=\"horizontalmenu_text\"><h3>".$question1."</h3></div></td></tr>";
			while($reponse1 = mysql_fetch_row($select_reponses1)){
				$this_reponse = html_ent($reponse1[2]);
				echo "\n<tr><td align=\"center\" class=\"verticalmenu\"><div id=\"verticalmenu_text\"><b>".$this_reponse."</b></div></td>";
 				$select_votes = mysql_query("select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 = $reponse1[0];");
 				$vote = mysql_result($select_votes,0);
 				if ($sum != 0)
 					$resultat = round($vote*100/$sum,1);
 				else $resultat = 0;
 				echo "\n<td align=\"center\" class=\"bgr\"><b>".$resultat." %</b></td>";
 				echo "</tr>";
			}
			echo "\n<tr><td align=\"center\" colspan=\"2\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".total_votes." : ".$sum."</b></div></td></tr>";
			echo "</table>";
		} else accueil();
	}
	
	// crosstab ***
	
	else if (mysql_num_rows($select_questions) > 1) {
		
		$select_question1 = mysql_query("select * from `" . $tblprefix . "sondage_questions` where active_question = '1' and id_conjoint != 0;");
		
		$id_question1 = mysql_result($select_question1,0,0);
		$id_conjoint1 = mysql_result($select_question1,0,1);
		$question1 = mysql_result($select_question1,0,2);
		$question1 = html_ent($question1);
		$question1 = bbcode_br($question1);
		
		$select_question2 = mysql_query("select * from `" . $tblprefix . "sondage_questions` where id_question = $id_conjoint1;");
		$id_question2 = mysql_result($select_question2,0,0);
		$question2 = mysql_result($select_question2,0,2);
		$question2 = html_ent($question2);
		$question2 = bbcode_br($question2);
		
		$select_reponses1 = mysql_query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question1;");
		$select_reponses2 = mysql_query("select * from `" . $tblprefix . "sondage_reponses` where id_question = $id_question2;");
		$nbr_reponses1 = mysql_num_rows($select_reponses1)+2;
		$nbr_reponses2 = mysql_num_rows($select_reponses2)+2;

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
		$req_sum_votes = mysql_query($select_sum_votes);
		if ($req_sum_votes)
			$sum = mysql_result($req_sum_votes,0);
		else $sum = 0;
		@mysql_data_seek($select_reponses1,0);
		
		if (mysql_num_rows($req_sum_votes) > 0) {
		
			// boucle results
		
 			while($reponse1 = mysql_fetch_row($select_reponses1)){
 				@mysql_data_seek($select_reponses2,0);
 				$this_reponse1 = html_ent($reponse1[2]);
 				echo "\n<tr><td align=\"center\" class=\"verticalmenu\"><div id=\"verticalmenu_text\"><b>".$this_reponse1."</b></div></td>";
 				$sum_row = 0;
 				while($reponse2 = mysql_fetch_row($select_reponses2)){
 				
 					$select_votes = mysql_query("select SUM(nbr_votes) from `" . $tblprefix . "sondage_votes` where id_reponse1 = $reponse1[0] and id_reponse2 = $reponse2[0];");
 					$vote = mysql_result($select_votes,0);
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
			$req_votes_column = mysql_query($select_votes_column);
			
			$total_sum = 0;
			while($vote_column = mysql_fetch_row($req_votes_column)){
				if ($sum != 0)
					$sum_column = round($vote_column[0]*100/$sum,1);
				else $sum_column = 0;
				echo "<td align=\"center\" class=\"bgr\"><b>".$sum_column." %</b></td>";
				$total_sum += $sum_column;
			}
			if ($total_sum > 100) $total_sum = 100;
 			echo "<td align=\"center\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".$total_sum." %</b></div></td>";
 			echo "</tr>";
 			echo "\n<tr>";
 			echo "\n<td align=\"center\" class=\"header\">&nbsp;</td>";
 			echo "\n<td align=\"center\" colspan=\"".$nbr_reponses2."\" class=\"header\"><div id=\"horizontalmenu_text\"><b>".total_votes." : ".$sum."</b></div></td>";
 			echo "\n</tr>";
 			echo "\n</table>";
 		} else accueil();
	 }
	}  else accueil();
 }  else accueil();

?>