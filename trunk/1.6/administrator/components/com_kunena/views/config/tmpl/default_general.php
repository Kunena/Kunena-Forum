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

$settings  = JHtml::_('kconfig.setting', $this, 'forum_title', 'Forum Title::The name of your forum.', 'Forum Title', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $this, 'forum_offline', 'Forum eMail::The forum\'s email address. Make this a valid email address', 'Forum eMail', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $this, 'forum_email', 'Forum Offline::...', 'Forum Offline', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $this, 'forum_offline_msg', 'Forum Offline Message::The message displayed instead of the forum when it is set offline', 'Forum Offline Message', 'textarea', 30, 5);

echo JHtml::_('kconfig.section', 'Global', $settings );

?>
<fieldset>
	<legend><?php echo JText::_('Layout Settings'); ?></legend>
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
</fieldset>
