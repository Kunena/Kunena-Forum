<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div class="ksearch-adv">
			<h2 class="kheader"><a title="Advanced Search" rel="kadvsearch-detailsbox">Advanced Search</a></h2>
			<div class="kdetailsbox kadvsearch" id="kadvsearch-detailsbox">

				<form name="adminForm" id="searchform" method="post" action="#">
					<div class="ksearchform-leftcol">
						<fieldset class="fieldset">
							<legend>Search by Keyword</legend>
							<label for="keywords" class="searchlabel">Keywords:</label><br />
							<input type="text" value="" size="30" name="q" class="ks input" id="keywords" />
							<select name="titleonly" class="ks" id="keywordfilter">
								<option selected="selected" value="0">Search entire posts</option>
								<option value="1">Search titles only</option>
							</select>
						</fieldset>
					</div>

					<div class="ksearchform-rightcol">
						<fieldset class="fieldset">
							<legend>Search by User Name</legend>
							<label class="searchlabel">User Name:
								<input type="text" value="" name="searchuser" class="ks input" id="kusername" autocomplete="off" />
							</label>
							<br />
							<label class="searchlabel">
								Exact Name:
								<input type="checkbox" value="1" name="exactname" />
							</label>
						</fieldset>
					</div>

					<div class="clrline"></div>

					<div class="ksearchform-leftcol">
						<fieldset id="search-posts-date" class="fieldset">
							<legend>Find Posts from</legend>
							<select class="ks" id="searchdate" name="searchdate">
								<option value="lastvisit">Last visit</option>
								<option value="1">Yesterday</option>
								<option value="7">A week ago</option>
								<option value="14">2 weeks ago</option>
								<option value="30">A month ago</option>
								<option value="90">3 months ago</option>
								<option value="180">6 months ago</option>
								<option selected="selected" value="365">A year ago</option>
								<option value="all">Any date</option>
							</select>
							<select class="ks" id="beforeafter" name="beforeafter">
								<option selected="selected" value="after">And newer</option>
								<option value="before">And older</option>
							</select>
						</fieldset>
						<fieldset id="search-posts-sort" class="fieldset">
							<legend>Sort Results by</legend>
							<select class="ks" id="sortby" name="sortby">
								<option value="title">Title</option>
								<option value="views">Number of views</option>
								<option selected="selected" value="lastpost">Posting date</option>
								<option value="forum">Forum</option>
							</select>
							<select name="order" class="ks">
								<option value="inc">Increasing order</option>
								<option selected="selected" value="dec">Decreasing order</option>
							</select>
						</fieldset>

						<fieldset id="search-posts-start" class="fieldset">
							<legend>Jump to Result Number</legend>
							<input type="text" size="5" value="0" name="limitstart" class="ks input" />
							<select class="ks" id="limit" name="limit">
								<option value="5">Show 5 Search Results</option>
								<option value="10">Show 10 Search Results</option>
								<option selected="selected" value="15">Show 15 Search Results</option>
								<option value="20">Show 20 Search Results</option>
							</select>
						</fieldset>
					</div>



					<div class="ksearchform-rightcol">
						<fieldset class="fieldset">
							<legend>Search in Categories</legend>
							<select multiple="multiple" size="8" class="kinputbox" id="catids" name="catids[]">
								<option selected="selected" value="0">All Categories</option>
								<option value="94"> Kunena - To Speak!</option>
								<option value="76">-  Official Announcements and News about Kunena</option>
								<option value="77">-  General Talk about Kunena</option>
								<option value="5">- -  General Talk about Kunena Archive</option>
								<option value="184">-  Discuss Articles</option>
								<option value="119">-  Feature Requests</option>
								<option value="163">-  Feature Playground</option>
								<option value="164">- -  Fairground</option>
								<option value="155"> Kunena 1.6</option>
								<option value="159">-  K 1.6 Common Questions</option>
								<option value="160">- -  K 1.6 Common Questions Archive</option>
								<option value="172">-  K 1.6 Installation and Upgrade</option>
								<option value="173">- -  K 1.6 Installation and Upgrade Archive</option>
								<option value="168">-  K 1.6 Support</option>
								<option value="169">- -  K 1.6 Support Archive</option>
								<option value="156">-  K 1.6 test reports</option>
								<option value="167">- -  K 1.6 test reports - Joomla 1.6 issues</option>
								<option value="158">- -  K 1.6 test reports archive</option>
								<option value="170">-  K 1.6 Templates and Design</option>
								<option value="171">- -  K 1.6 Templates and Design Archive</option>
								<option value="166">-  K 1.6 Kunena Extensions Directory</option>
								<option value="177"> Kunena Add-ons</option>
								<option value="178">-  Kunena Discuss (P)</option>
								<option value="180">- -  Kunena Discuss (P) Archive</option>
								<option value="179">-  Kunena Latest Posts (M)</option>
								<option value="181">- -  Kunena Latest Posts (M) Archive</option>
								<option value="187">-  Kunena Login (M)</option>
								<option value="188">- -  Kunena Login (M) Archive</option>
								<option value="185">-  Kunena Search (P)</option>
								<option value="186">- -  Kunena Search (P) Archive</option>
								<option value="189">-  Kunena Stats (M)</option>
								<option value="190">- -  Kunena Stats (M) Archive</option>
								<option value="182">-  jFirePHP (T)</option>
								<option value="183">- -  jFirePHP (T) Archive</option>
								<option value="131"> Kunena 1.5</option>
								<option value="132">-  K 1.5 Common Questions</option>
								<option value="133">- -  K 1.5 Common Questions Archive</option>
								<option value="134">-  K 1.5 Installation,  Upgrade and Migration</option>
								<option value="135">- -  K 1.5 Installation, Upgrade and Migration Archive</option>
								<option value="138">-  K 1.5 Support</option>
								<option value="139">- -  K 1.5 Support Archive</option>
								<option value="136">-  K 1.5 Templates and Design</option>
								<option value="137">- -  K 1.5 Templates and Design Archive</option>
								<option value="144">-  K 1.5 Hacks, Tricks and Tips</option>
								<option value="146">- -  K 1.5 Hacks, Tricks and Tips Archive</option>
								<option value="106"> Kunena User Contributions and Third-Party Options</option>
								<option value="9">-  Extensions, Modules, and Plugins</option>
								<option value="12">- -  Community Builder</option>
								<option value="11">- -  JomSocial</option>
								<option value="13">- -  sh404SEF</option>
								<option value="165">- -  uddeIM</option>
								<option value="21">-  Language Specific</option>
								<option value="22">- -  Arabic</option>
								<option value="117">- -  Catalan</option>
								<option value="42">- -  Dutch</option>
								<option value="29">- -  Finnish</option>
								<option value="24">- -  French</option>
								<option value="36">- -  German</option>
								<option value="176">- - -  German K 1.5 Archiv</option>
								<option value="118">- - -  Hacks, Specials, Downloads</option>
								<option value="175">- - -  K 1.0 und Fireboard Archiv</option>
								<option value="25">- -  Greek</option>
								<option value="26">- -  Indonesian</option>
								<option value="38">- -  Italian</option>
								<option value="27">- -  Polish</option>
								<option value="33">- -  Romanian</option>
								<option value="28">- -  Serbian</option>
								<option value="47">- -  Spanish</option>
								<option value="41">- -  Turkish</option>
								<option value="174">- -  Ukrainian</option>
								<option value="107">-  Translations</option>
								<option value="109">- -  Translations Archive</option>
								<option value="143">- -  Translations prior to Kunena 1.5.7</option>
								<option value="110">-  User Contributions</option>
								<option value="111">- -  User-written Templates</option>
								<option value="112">- -  User-written Modules</option>
								<option value="113">- -  User-written Plugins</option>
								<option value="114">- -  User-written hacks</option>
								<option value="153"> Miscellaneous, off-topic and general Joomla</option>
								<option value="154">-  Miscellaneous, off-topic and general Joomla</option>
								<option value="115"> Forum Archive</option>
								<option value="95">-  Kunena 1.0 and Fireboard</option>
								<option value="96">- -  K 1.0 Common Questions</option>
								<option value="97">- - -  K 1.0 Common Questions Archive</option>
								<option value="100">- -  K 1.0 Installation,  Upgrade and Migration</option>
								<option value="101">- - -  K 1.0 Installation, Upgrade and Migration Archive</option>
								<option value="102">- -  K 1.0 Templates and Design</option>
								<option value="103">- - -  K 1.0 Template and Design Archive</option>
								<option value="104">- -  K 1.0 Support</option>
								<option value="105">- - -  K 1.0 Support Archive</option>
								<option value="145">- -  K 1.0 Hacks, Tricks and Tips</option>
								<option value="147">- - -  K 1.0 Hacks, Tricks and Tips Archive</option>
								<option value="1">-  Kunena Archive</option>
								<option value="4">- -  ARCHIVE Support</option>
								<option value="19">- - -  ARCHIVE Fireboard</option>
								<option value="16">- - -  ARCHIVE Installation</option>
								<option value="17">- - -  ARCHIVE Migration</option>
								<option value="18">- - -  ARCHIVE Templates</option>
								<option value="8">- -  ARCHIVE Testing</option>
								<option value="120">- -  Solved Problems in Beta Releases</option>
							</select>
							<br />
							<label id="childforums-lbl">
								<input type="checkbox" value="1" name="childforums" />
								Also search in child forums
							</label>
						</fieldset>

					</div>
					<div class="clr"></div>

					<div class="kpost-buttons">
						<button title="Click here to search" type="submit" class="kbutton"> Search </button>
						<button onclick="window.location='/forum';" title="Click here to cancel" type="button" class="kbutton"> Cancel </button>
					</div>

					<input type="hidden" value="com_kunena" name="option" />
					<input type="hidden" value="search" name="view" />
					<input type="hidden" value="results" name="task" />
					<input type="hidden" value="1" name="2f7bbdb3f3dd90b1b25caafd62098198" />
				</form>

			</div>
			<div class="clr"></div>
		</div>