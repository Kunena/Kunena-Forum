<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

if(!class_exists('JPane')) {
   jimport('joomla.html.pane');
   $pane =& JPane::getInstance('sliders');
}


$pane =& JPane::getInstance('Tabs');
echo $pane->startPane('myPane');
{
echo $pane->startPanel('Basics', 'Basics');?>
<fieldset>
	<legend><?php echo JText::_('Setup'); ?>
    </legend>

	<legend>Forum Settings</legend>
	<table class="admintable" cellspacing="1">
	<tbody>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_title" class="hasTip" title="Board Title::The name of your board.">Board Title</label>
			</td>
			<td>
				<input type="text" name="config[board_title]" id="config_board_title" value="<?php echo $this->options->get('board_title'); ?>" size="30" />
 
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_email" class="hasTip" title="Board Email::This is the board's e-mail address. Make this a valid e-mail address.">Board Email</label>
			</td>
			<td>
				<input type="text" name="config[board_email]" id="config_board_email" value="<?php echo $this->options->get('board_email'); ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_avatar_src" class="hasTip" title="Forum Offline::...">Forum Offline</label>
			</td>
			<td>
				<select name="config[board_offline]" id="config_board_offline">
					<option value="no"<?php echo (($this->options->get('board_offline', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>>No</option>
					<option value="yes"<?php echo (($this->options->get('board_offline', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>

	</tbody>
	</table>
	<legend>Layout Settings</legend>
	<table class="admintable" cellspacing="1">
	<tbody>

		<tr>
			<td width="40%" class="key">
				<label for="config_threads_per_page" class="hasTip" title="Threads per Page::Number of threads to be displayed per page.">Threads per Page</label>
			</td>
			<td>
				<input type="text" name="config[threads_per_page]" id="config_threads_per_page" value="<?php echo $this->options->get('threads_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_messages_per_page" class="hasTip" title="Messages per Page::Number of messages to be displayed per page.">Messages per Page</label>
			</td>
			<td>
				<input type="text" name="config[messages_per_page]" id="config_messages_per_page" value="<?php echo $this->options->get('messages_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_new_indicator" class="hasTip" title="New Indicator::If set, characters will highlight new messages.">New Indicator</label>
			</td>
			<td>
				<input type="text" name="config[new_indicator]" id="config_new_indicator" value="<?php echo $this->options->get('new_indicator'); ?>" size="5" />
			</td>
		</tr>
        </tbody>
	</table>
<legend>Extra Settings</legend>
<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_enabled" class="hasTip" title="Met de RSS feed optie kunnen derden de laatste berichten bekijken op hun desktop/RSS aplicatie (zie bijvoorbeeld rssreader.com">Enable rss</label>
			</td>
			<td>
				<select name="config[rss_enabled]" id="rss_enabled">
					<option value="0"<?php echo (($this->options->get('rss_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('rss_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
<table width="100%" class="admintable" cellspacing="1" align="right">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_default" class="hasTip" title="Kies tussen RSS feeds op basis van onderwerp of post. Op basis van onderwerp betekent dat enkel 1 inschrijving per onderwerp getoond zal worden in de RSS feed, onafhankelijk van hoeveel posts er zijn gemaakt binnen het onderwerp. Door draad maakt een kortere meer compacte RSS feed maar zal niet iedere gemaakt antwoord laten zien.">Standaard RSS type</label>
			</td>
			<td>
				<select name="config[rss_default]" id="rss_default">
					<option value="0"<?php echo (($this->options->get('rss_default', 1) == 0) ? ' selected="selected"' : ''); ?>>topic</option>
					<option value="1"<?php echo (($this->options->get('rss_default', 1) == 1) ? ' selected="selected"' : ''); ?>>post</option>
				</select>
			</td>
		</tr>
        
        <table width="100%" class="admintable" cellspacing="1" align="right">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_history" class="hasTip" title="Met de RSS feed optie kunnen derden de laatste berichten bekijken op hun desktop/RSS aplicatie (zie bijvoorbeeld rssreader.com">history rss</label>
			</td>
			<td>
				<select name="config[rss_history]" id="rss_history">
					<option value="0"<?php echo (($this->options->get('rss_history', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('rss_history', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key" align="right">
				<label for="config_pdf_enabled" class="hasTip" title=" Schakel dit op Ja als u gebruikers wilt toestaan om een PDF document te genereren van het forumonderwerp.
Het is een simpel PDF document; zonder opmaak of layout maar het bevat alle tekst van het forumonderwerp.">Enable pdf</label>
			</td>
			<td>
				<select name="config[pdf_enabled]" id="pdf_enabled">
					<option value="0"<?php echo (($this->options->get('pdf_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('pdf_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>


	</tbody>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Frontend', 'Frontend');?>
<fieldset>
	<legend><?php echo JText::_('Frontend'); ?>
    </legend>

	<legend>Uiterlijk</legend>
	<table class="admintable" cellspacing="1">
	<tbody>
		<tr>
			<td width="40%" class="key">
				<label for="config_thread_by_page" class="hasTip" title="thread_by_page.">thread_by_page</label>
			</td>
			<td>
				<input type="text" name="config[thread_by_page]" id="thread_by_page" value="<?php echo $this->options->get('thread_by_page'); ?>" size="30" />
 
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_email" class="hasTip" title="Board Email::This is the board's e-mail address. Make this a valid e-mail address.">Board Email</label>
			</td>
			<td>
				<input type="text" name="config[board_email]" id="config_board_email" value="<?php echo $this->options->get('board_email'); ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_avatar_src" class="hasTip" title="Forum Offline::...">Forum Offline</label>
			</td>
			<td>
				<select name="config[board_offline]" id="config_board_offline">
					<option value="no"<?php echo (($this->options->get('board_offline', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>>No</option>
					<option value="yes"<?php echo (($this->options->get('board_offline', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>

	</tbody>
	</table>
	<legend>Layout Settings</legend>
	<table class="admintable" cellspacing="1">
	<tbody>

		<tr>
			<td width="40%" class="key">
				<label for="config_threads_per_page" class="hasTip" title="Threads per Page::Number of threads to be displayed per page.">Threads per Page</label>
			</td>
			<td>
				<input type="text" name="config[threads_per_page]" id="config_threads_per_page" value="<?php echo $this->options->get('threads_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_messages_per_page" class="hasTip" title="Messages per Page::Number of messages to be displayed per page.">Messages per Page</label>
			</td>
			<td>
				<input type="text" name="config[messages_per_page]" id="config_messages_per_page" value="<?php echo $this->options->get('messages_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_new_indicator" class="hasTip" title="New Indicator::If set, characters will highlight new messages.">New Indicator</label>
			</td>
			<td>
				<input type="text" name="config[new_indicator]" id="config_new_indicator" value="<?php echo $this->options->get('new_indicator'); ?>" size="5" />
			</td>
		</tr>
        </tbody>
	</table>
<legend>Extra Settings</legend>
<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_enabled" class="hasTip" title="Met de RSS feed optie kunnen derden de laatste berichten bekijken op hun desktop/RSS aplicatie (zie bijvoorbeeld rssreader.com">Enable rss</label>
			</td>
			<td>
				<select name="config[rss_enabled]" id="rss_enabled">
					<option value="0"<?php echo (($this->options->get('rss_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('rss_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
<table width="100%" class="admintable" cellspacing="1" align="right">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_default" class="hasTip" title="Kies tussen RSS feeds op basis van onderwerp of post. Op basis van onderwerp betekent dat enkel 1 inschrijving per onderwerp getoond zal worden in de RSS feed, onafhankelijk van hoeveel posts er zijn gemaakt binnen het onderwerp. Door draad maakt een kortere meer compacte RSS feed maar zal niet iedere gemaakt antwoord laten zien.">Standaard RSS type</label>
			</td>
			<td>
				<select name="config[rss_default]" id="rss_default">
					<option value="0"<?php echo (($this->options->get('rss_default', 1) == 0) ? ' selected="selected"' : ''); ?>>topic</option>
					<option value="1"<?php echo (($this->options->get('rss_default', 1) == 1) ? ' selected="selected"' : ''); ?>>post</option>
				</select>
			</td>
		</tr>
        
        <table width="100%" class="admintable" cellspacing="1" align="right">
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_history" class="hasTip" title="Met de RSS feed optie kunnen derden de laatste berichten bekijken op hun desktop/RSS aplicatie (zie bijvoorbeeld rssreader.com">history rss</label>
			</td>
			<td>
				<select name="config[rss_history]" id="rss_history">
					<option value="0"<?php echo (($this->options->get('rss_history', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('rss_history', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key" align="right">
				<label for="config_pdf_enabled" class="hasTip" title=" Schakel dit op Ja als u gebruikers wilt toestaan om een PDF document te genereren van het forumonderwerp.
Het is een simpel PDF document; zonder opmaak of layout maar het bevat alle tekst van het forumonderwerp.">Enable pdf</label>
			</td>
			<td>
				<select name="config[pdf_enabled]" id="pdf_enabled">
					<option value="0"<?php echo (($this->options->get('pdf_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('pdf_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>


	</tbody>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Avatars', 'Avatars');?>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Media', 'Media');?>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Security', 'Security');?>
<fieldset>
	<legend><?php echo JText::_('Setup'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_regonly" class="hasTip" title="Registered Users Only::Set to 'Yes' to allow only registered users to use the Forum (view &amp; post). Set to 'No' to allow any visitor to use the Forum.">Registered Users Only</label>
			</td>
			<td>
				<select name="config[regonly]" id="config_regonly">
					<option value="0"<?php echo (($this->options->get('regonly', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('regonly', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
	</table>
</fieldset>
<?php

echo $pane->endPanel();
echo $pane->startPanel('Integration', 'Integration');
?>
<fieldset>
	<legend><?php echo JText::_('Setup'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_avatar_src" class="hasTip" title="Avatars::...">Avatars</label>
			</td>
			<td>
				<select name="config[avatar_src]" id="config_avatar_src">
					<option value="kunena"<?php echo (($this->options->get('avatar_src', 'kunena') == 'kunena') ? ' selected="selected"' : ''); ?>>Kunena</option>
					<option value="cb"<?php echo (($this->options->get('avatar_src', 'kunena') == 'cb') ? ' selected="selected"' : ''); ?>>Community Builder</option>
					<option value="jomsocial"<?php echo (($this->options->get('avatar_src', 'kunena') == 'jomsocial') ? ' selected="selected"' : ''); ?>>JomSocial</option>
					<option value="gravatar"<?php echo (($this->options->get('avatar_src', 'kunena') == 'gravatar') ? ' selected="selected"' : ''); ?>>Gravatar</option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Plugins', 'Plugins');?>
<?php
echo $pane->endPanel();
}
echo $pane->endPane();

?>