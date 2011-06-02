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
<form enctype="multipart/form-data" name="categoryform" method="post" id="categoryform" class="categoryform form-validate" action="#">
	<div class="ksection">
		<h2 class="kheader"><span>New Category</span></h2>
		<ul class="kposthead clearfix">
			<li><h3>Basic Information</h3></li>
		</ul>
		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="catid">Parent</label>
				</div>
				<div class="kform-field">
					<select class="kinputbox hasTip" id="catid" name="catid" title="Parent :: Select Parent Category">
						<option value="0">Main Forum</option>
						<option value="94">Kunena - To Speak!</option>
						<option value="77">- General Talk about Kunena</option>
						<option value="184">- Discuss Articles</option>
						<option value="119">- Feature Requests</option>
						<option value="163">- Feature Playground</option>
						<option value="164">- - Fairground</option>
						<option value="155">Kunena 1.6</option>
						<option value="159">- K 1.6 Common Questions</option>
						<option value="160">- - K 1.6 Common Questions Archive</option>
						<option value="172">- K 1.6 Installation and Upgrade</option>
						<option value="173">- - K 1.6 Installation and Upgrade Archive</option>
						<option value="168">- K 1.6 Support</option>
						<option value="169">- - K 1.6 Support Archive</option>
						<option value="156">- K 1.6 test reports</option>
						<option value="167">- - K 1.6 test reports - Joomla 1.6 issues</option>
						<option value="158">- - K 1.6 test reports archive</option>
						<option value="170">- K 1.6 Templates and Design</option>
						<option value="171">- - K 1.6 Templates and Design Archive</option>
						<option value="166">- K 1.6 Kunena Extensions Directory</option>
						<option value="177">Kunena Add-ons</option>
						<option value="178">- Kunena Discuss (P)</option>
						<option value="180">- - Kunena Discuss (P) Archive</option>
						<option value="179">- Kunena Latest Posts (M)</option>
						<option value="181">- - Kunena Latest Posts (M) Archive</option>
						<option value="187">- Kunena Login (M)</option>
						<option value="188">- - Kunena Login (M) Archive</option>
						<option value="185">- Kunena Search (P)</option>
						<option value="186">- - Kunena Search (P) Archive</option>
						<option value="189">- Kunena Stats (M)</option>
						<option value="190">- - Kunena Stats (M) Archive</option>
						<option value="182">- jFirePHP (T)</option>
						<option value="183">- - jFirePHP (T) Archive</option>
						<option value="131">Kunena 1.5</option>
						<option value="132">- K 1.5 Common Questions</option>
						<option value="133">- - K 1.5 Common Questions Archive</option>
						<option value="134">- K 1.5 Installation, Upgrade and Migration</option>
						<option value="135">- - K 1.5 Installation, Upgrade and Migration Archive</option>
						<option value="138">- K 1.5 Support</option>
						<option value="139">- - K 1.5 Support Archive</option>
						<option value="136">- K 1.5 Templates and Design</option>
						<option value="137">- - K 1.5 Templates and Design Archive</option>
						<option value="144">- K 1.5 Hacks, Tricks and Tips</option>
						<option value="146">- - K 1.5 Hacks, Tricks and Tips Archive</option>
						<option value="106">Kunena User Contributions and Third-Party Options</option>
						<option value="9">- Extensions, Modules, and Plugins</option>
						<option value="12">- - Community Builder</option>
						<option value="11">- - JomSocial</option>
						<option value="13">- - sh404SEF</option>
						<option value="165">- - uddeIM</option>
						<option value="21">- Language Specific</option>
						<option value="22">- - Arabic</option>
						<option value="117">- - Catalan</option>
						<option value="42">- - Dutch</option>
						<option value="29">- - Finnish</option>
						<option value="24">- - French</option>
						<option value="36">- - German</option>
						<option value="176">- - - German K 1.5 Archiv</option>
						<option value="118">- - - Hacks, Specials, Downloads</option>
						<option value="25">- - Greek</option>
						<option value="26">- - Indonesian</option>
						<option value="38">- - Italian</option>
						<option value="27">- - Polish</option>
						<option value="33">- - Romanian</option>
						<option value="28">- - Serbian</option>
						<option value="47">- - Spanish</option>
						<option value="41">- - Turkish</option>
						<option value="174">- - Ukrainian</option>
						<option value="107">- Translations</option>
						<option value="110">- User Contributions</option>
						<option value="111">- - User-written Templates</option>
						<option value="112">- - User-written Modules</option>
						<option value="113">- - User-written Plugins</option>
						<option value="114">- - User-written hacks</option>
						<option value="153">Miscellaneous, off-topic and general Joomla</option>
						<option value="154">- Miscellaneous, off-topic and general Joomla</option>
						<option value="115">Forum Archive</option>
						<option value="95">- Kunena 1.0 and Fireboard</option>
						<option value="96">- - K 1.0 Common Questions</option>
						<option value="100">- - K 1.0 Installation, Upgrade and Migration</option>
						<option value="102">- - K 1.0 Templates and Design</option>
						<option value="104">- - K 1.0 Support</option>
						<option value="145">- - K 1.0 Hacks, Tricks and Tips</option>
						<option value="147">- - - K 1.0 Hacks, Tricks and Tips Archive</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="catname">Name</label>
				</div>
				<div class="kform-field">
					<input type="text" value="" maxlength="100" size="35" id="catname" name="catname" class="kinputbox postinput required hasTip" title="Name :: Enter Category Name" />
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="kbbcode-message">Description</label><br/>
					<span style="cursor: pointer;" onclick="kGrowShrinkMessage(100);" class="ks">Enlarge</span>&nbsp;/&nbsp;
					<span style="cursor: pointer;" onclick="kGrowShrinkMessage(-100);" class="ks">Shrink</span>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="10" id="kbbcode-message" name="message" class="ktxtarea required hasTip" title="Description :: Enter category description"></textarea>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="catheader">Category Header</label>
				</div>
				<div class="kform-field">
					<textarea cols="50" rows="5" id="catheader" name="catheader" class="ktxtarea required hasTip" title="Category Header :: Enter Category Header"></textarea>
				</div>
			</li>
		</ul>

		<ul class="kposthead clearfix">
			<li><h3>Settings</h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="locked">Locked</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="locked" name="locked" title="Locked :: Select 'Yes' for locking this category">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="pub_access">Public Access Level</label>
				</div>
				<div class="kform-field">
					<select size="4" class="kinputbox hasTip" id="pub_access" name="pub_access" title="Public Access Level :: Select Public Access Level">
						<option value="1">Nobody</option>
						<option selected="selected" value="0">Everybody</option>
						<option value="-1">All Registered</option>
						<option value="18">-&nbsp;Registered</option>
						<option value="19">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Author</option>
						<option value="20">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Editor</option>
						<option value="21">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Publisher</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="pub_recurse">Include Child Groups</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="pub_recurse" name="pub_recurse" title="Include Child Groups :: Select 'Yes' for including child groups">
						<option value="0">No</option>
						<option selected="selected" value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="admin_access">Admin Access Level</label>
				</div>
				<div class="kform-field">
					<select size="4" class="kinputbox hasTip" id="admin_access" name="admin_access" title="Admin Access Level :: Select Admin Access Level">
						<option value="30">-&nbsp; Public Back-end</option>
						<option value="23">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Manager</option>
						<option value="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Administrator</option>
						<option value="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Super Administrator</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="admin_recurse">Include Child Groups</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="admin_recurse" name="admin_recurse" title="Include Child Groups :: Select 'Yes' for including child groups">
						<option value="0">No</option>
						<option selected="selected" value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="review">Review posts</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="review" name="review" title="Review posts :: Select 'Yes' for reviewing posts">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="allow_anonymous">Allow anonymous messages</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="allow_anonymous" name="allow_anonymous" title="Allow anonymous messages :: Select 'Yes' to allow anonymous messages">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="post_anonymous">By default post replies as</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="post_anonymous" name="post_anonymous" title="By default post replies as :: Select how to post replies by default">
						<option value="0">Registered user</option>
						<option value="1">Anonymous user</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="allow_polls">Enable poll in these categories</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="allow_polls" name="allow_polls" title="Enable poll in these categories :: Select 'Yes' for enabling poll in these categories">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
			</li>
			<li class="kpostcategory-row krow-odd">
				<div class="kform-label">
					<label for="class_sfx">Forum CSS Class Suffix</label>
				</div>
				<div class="kform-field">
					<input type="text" value="" maxlength="20" size="20" id="class_sfx" name="class_sfx" class="kinputbox postinput required hasTip" title="Forum CSS Class Suffix :: Enter Forum CSS Class Suffix" />
				</div>
			</li>
		</ul>

		<ul class="kposthead clearfix">
			<li><h3>Moderation</h3></li>
		</ul>

		<ul class="kform kpostcategory clearfix">
			<li class="kpostcategory-row krow-even">
				<div class="kform-label">
					<label for="moderated">Moderated</label>
				</div>
				<div class="kform-field">
					<select size="1" class="kinputbox hasTip" id="moderated" name="moderated" title="Moderated :: Set to Yes if you want to be able to assign Moderators to this category. ">
						<option value="0">No</option>
						<option selected="selected" value="1">Yes</option>
					</select>
					<div class="kform-note">
						Set to Yes if you want to be able to assign Moderators to this category.
					</div>
					<button class="kbutton" type="submit" title="Click here to add moderators"> Add Moderator(s) </button>
				</div>
			</li>

			<li class="kpostcategory-row krow-even">
				<ul class="ksubhead clearfix">
					<li><h4>Moderators assigned to this category:</h4></li>
				</ul>

				<div class="kuserlist-items">
					<p>There are no Moderators assigned to this category</p>
				</div>

			</li>


		</ul>

		<div class="kpost-buttons">
		<button class="kbutton" type="submit" title="Click here to apply your changes"> Apply </button>
			<button class="kbutton" type="submit" title="Click here to submit your category"> Save </button>
			<button class="kbutton" type="button" title="Click here to cancel" onclick="javascript:window.history.back();"> Cancel </button>
		</div>
	</div>
</form>