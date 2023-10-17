<?php
/*
 * 	Manhali - Free Learning Management System
 *	felder.php
 *	2012-08-22 03:08
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
goback_button();
if (!empty($id_user_session) && isset($_SESSION['log'])){
	echo "<a href=\"?questionnaire\"><b>".discover_learning_style."</b></a>";
}
echo "
<h3><center>LEARNING STYLES AND STRATEGIES</center></h3>
<b><center>
Richard M. Felder
<br />Hoechst Celanese Professor of Chemical Engineering
<br />North Carolina State University
<br />
<br />Barbara A. Soloman
<br />Coordinator of Advising, First Year College
<br />North Carolina State University
</center></b>
<br /><b><u>ACTIVE AND REFLECTIVE LEARNERS</u></b>
<ul>
<li>Active learners tend to retain and understand information best by doing something active with it--discussing or applying it or explaining it to others. Reflective learners prefer to think about it quietly first.</li>
<li>\"Let's try it out and see how it works\" is an active learner's phrase; \"Let's think it through first\" is the reflective learner's response.</li>
<li>Active learners tend to like group work more than reflective learners, who prefer working alone.</li>
<li>Sitting through lectures without getting to do anything physical but take notes is hard for both learning types, but particularly hard for active learners.</li>
</ul>
Everybody is active sometimes and reflective sometimes. Your preference for one category or the other may be strong, moderate, or mild. A balance of the two is desirable. If you always act before reflecting you can jump into things prematurely and get into trouble, while if you spend too much time reflecting you may never get anything done.
<br /><br /><b>How can active learners help themselves?</b>
<br />If you are an active learner in a class that allows little or no class time for discussion or problem-solving activities, you should try to compensate for these lacks when you study. Study in a group in which the members take turns explaining different topics to each other. Work with others to guess what you will be asked on the next test and figure out how you will answer. You will always retain information better if you find ways to do something with it.
<br /><br /><b>How can reflective learners help themselves?</b>
<br />If you are a reflective learner in a class that allows little or no class time for thinking about new information, you should try to compensate for this lack when you study. Don't simply read or memorize the material; stop periodically to review what you have read and to think of possible questions or applications. You might find it helpful to write short summaries of readings or class notes in your own words. Doing so may take extra time but will enable you to retain the material more effectively.
<br /><br /><b><u>SENSING AND INTUITIVE LEARNERS</u></b>
<ul>
<li>Sensing learners tend to like learning facts, intuitive learners often prefer discovering possibilities and relationships.</li>
<li>Sensors often like solving problems by well-established methods and dislike complications and surprises; intuitors like innovation and dislike repetition. Sensors are more likely than intuitors to resent being tested on material that has not been explicitly covered in class.</li>
<li>Sensors tend to be patient with details and good at memorizing facts and doing hands-on (laboratory) work; intuitors may be better at grasping new concepts and are often more comfortable than sensors with abstractions and mathematical formulations.</li>
<li>Sensors tend to be more practical and careful than intuitors; intuitors tend to work faster and to be more innovative than sensors.</li>
<li>Sensors don't like courses that have no apparent connection to the real world; intuitors don't like \"plug-and-chug\" courses that involve a lot of memorization and routine calculations.</li>
</ul>
Everybody is sensing sometimes and intuitive sometimes. Your preference for one or the other may be strong, moderate, or mild. To be effective as a learner and problem solver, you need to be able to function both ways. If you overemphasize intuition, you may miss important details or make careless mistakes in calculations or hands-on work; if you overemphasize sensing, you may rely too much on memorization and familiar methods and not concentrate enough on understanding and innovative thinking.
<br /><br /><b>How can sensing learners help themselves?</b>
<br />Sensors remember and understand information best if they can see how it connects to the real world. If you are in a class where most of the material is abstract and theoretical, you may have difficulty. Ask your instructor for specific examples of concepts and procedures, and find out how the concepts apply in practice. If the teacher does not provide enough specifics, try to find some in your course text or other references or by brainstorming with friends or classmates.
<br /><br /><b>How can intuitive learners help themselves?</b>
<br />Many college lecture classes are aimed at intuitors. However, if you are an intuitor and you happen to be in a class that deals primarily with memorization and rote substitution in formulas, you may have trouble with boredom. Ask your instructor for interpretations or theories that link the facts, or try to find the connections yourself. You may also be prone to careless mistakes on test because you are impatient with details and don't like repetition (as in checking your completed solutions). Take time to read the entire question before you start answering and be sure to check your results
<br /><br /><b><u>VISUAL AND VERBAL LEARNERS</u></b>
<br />Visual learners remember best what they see--pictures, diagrams, flow charts, time lines, films, and demonstrations. Verbal learners get more out of words--written and spoken explanations. Everyone learns more when information is presented both visually and verbally.
<br /><br />In most college classes very little visual information is presented: students mainly listen to lectures and read material written on chalkboards and in textbooks and handouts. Unfortunately, most people are visual learners, which means that most students do not get nearly as much as they would if more visual presentation were used in class. Good learners are capable of processing information presented either visually or verbally.
<br /><br /><b>How can visual learners help themselves?</b>
<br />If you are a visual learner, try to find diagrams, sketches, schematics, photographs, flow charts, or any other visual representation of course material that is predominantly verbal. Ask your instructor, consult reference books, and see if any videotapes or CD-ROM displays of the course material are available. Prepare a concept map by listing key points, enclosing them in boxes or circles, and drawing lines with arrows between concepts to show connections. Color-code your notes with a highlighter so that everything relating to one topic is the same color.
<br /><br /><b>How can verbal learners help themselves?</b>
<br />Write summaries or outlines of course material in your own words. Working in groups can be particularly effective: you gain understanding of material by hearing classmates' explanations and you learn even more when you do the explaining.
<br /><br /><b><u>SEQUENTIAL AND GLOBAL LEARNERS</u></b>
<ul>
<li>Sequential learners tend to gain understanding in linear steps, with each step following logically from the previous one. Global learners tend to learn in large jumps, absorbing material almost randomly without seeing connections, and then suddenly \"getting it.\"</li>
<li>Sequential learners tend to follow logical stepwise paths in finding solutions; global learners may be able to solve complex problems quickly or put things together in novel ways once they have grasped the big picture, but they may have difficulty explaining how they did it.</li>
</ul>
Many people who read this description may conclude incorrectly that they are global, since everyone has experienced bewilderment followed by a sudden flash of understanding. What makes you global or not is what happens before the light bulb goes on. Sequential learners may not fully understand the material but they can nevertheless do something with it (like solve the homework problems or pass the test) since the pieces they have absorbed are logically connected. Strongly global learners who lack good sequential thinking abilities, on the other hand, may have serious difficulties until they have the big picture. Even after they have it, they may be fuzzy about the details of the subject, while sequential learners may know a lot about specific aspects of a subject but may have trouble relating them to different aspects of the same subject or to different subjects.
<br /><br /><b>How can sequential learners help themselves?</b>
<br />Most college courses are taught in a sequential manner. However, if you are a sequential learner and you have an instructor who jumps around from topic to topic or skips steps, you may have difficulty following and remembering. Ask the instructor to fill in the skipped steps, or fill them in yourself by consulting references. When you are studying, take the time to outline the lecture material for yourself in logical order. In the long run doing so will save you time. You might also try to strengthen your global thinking skills by relating each new topic you study to things you already know. The more you can do so, the deeper your understanding of the topic is likely to be.
<br /><br /><b>How can global learners help themselves?</b>
<br />If you are a global learner, it can be helpful for you to realize that you need the big picture of a subject before you can master details. If your instructor plunges directly into new topics without bothering to explain how they relate to what you already know, it can cause problems for you. Fortunately, there are steps you can take that may help you get the big picture more rapidly. Before you begin to study the first section of a chapter in a text, skim through the entire chapter to get an overview. Doing so may be time-consuming initially but it may save you from going over and over individual parts later. Instead of spending a short time on every subject every night, you might find it more productive to immerse yourself in individual subjects for large blocks. Try to relate the subject to things you already know, either by asking the instructor to help you see connections or by consulting references. Above all, don't lose faith in yourself; you will eventually understand the new material, and once you do your understanding of how it connects to other topics and disciplines may enable you to apply it in ways that most sequential thinkers would never dream of.
";
?>