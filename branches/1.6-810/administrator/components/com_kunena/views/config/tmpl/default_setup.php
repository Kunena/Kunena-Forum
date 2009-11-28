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
	<legend><?php echo JText::_('K_CONFIG_BASICS'); ?>
    </legend>

	<legend><?php echo JText::_('K_CONFIG_FORUM_SETTINGS'); ?></legend>
	<table width="50%" class="admintable" cellspacing="1" align="left">
	<tbody>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_title" class="hasTip" title="<?php echo JText::_('K_CONFIG_BOARD_TITLE_DESC'); ?>"><?php echo JText::_('K_CONFIG_BOARD_TITLE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[board_title]" id="config_board_title" value="<?php echo $this->options->get('board_title'); ?>" size="30" />

			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_email" class="hasTip" title="<?php echo JText::_('K_CONFIG_BOARD_EMAIL_DESC'); ?>"><?php echo JText::_('K_CONFIG_BOARD_EMAIL'); ?></label>
			</td>
			<td>
				<input type="text" name="config[board_email]" id="config_board_email" value="<?php echo $this->options->get('board_email'); ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_offline" class="hasTip" title="<?php echo JText::_('K_CONFIG_FORUM_OFFLINE_DESC'); ?>"><?php echo JText::_('K_CONFIG_FORUM_OFFLINE'); ?></label>
			</td>
			<td>
				<select name="config[board_offline]" id="config_board_offline">
					<option value="no"<?php echo (($this->options->get('board_offline', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('board_offline', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
			<td width="40%" class="key">
				<label for="config_board_timeoffset" class="hasTip" title="<?php echo JText::_('K_CONFIG_BOARD_TIMEOFFSET_DESC'); ?>"><?php echo JText::_('K_CONFIG_BOARD_TIMEOFFSET'); ?></label>
			</td>
			<td>
				<input type="text" name="config[board_timeoffset]" id="config_board_timeoffset" value="<?php echo $this->options->get('board_timeoffset'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_board_lifetime" class="hasTip" title="<?php echo JText::_('K_CONFIG_BOARD_LIFETIME_DESC'); ?>"><?php echo JText::_('K_CONFIG_BOARD_LIFETIME'); ?></label>
			</td>
			<td>
				<input type="text" name="config[board_lifetime]" id="config_board_lifetime" value="<?php echo $this->options->get('board_lifetime'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_board_offlinemessage" class="hasTip" title="<?php echo JText::_('K_CONFIG_BOARD_OFFLINE_MESSAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_BOARD_OFFLINE_MESSAGE'); ?></label>
			</td>
			<td>

                <textarea name = "config[board_offlinemessage]" rows = "3" cols = "50"><?php echo $this->options->get('board_offlinemessage'); ?></textarea>
			</td>
		</tr>
		<tr>
	</tbody>
	</table>
<legend><?php echo JText::_('K_CONFIG_EXTRA_SETTINGS'); ?></legend>
<table width="50%" class="admintable" cellspacing="1" align="right" style="padding-bottom:10px;">
		<tbody>
        <tr>
			<td width="40%" class="key">
				<label for="config_rss_enabled" class="hasTip" title="<?php echo JText::_('K_CONFIG_RSS_ENABLED_DESC'); ?>"><?php echo JText::_('K_CONFIG_RSS_ENABLED'); ?></label>
			</td>
			<td>
				<select name="config[rss_enabled]" id="rss_enabled">
					<option value="0"<?php echo (($this->options->get('rss_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('rss_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_rss_default" class="hasTip" title="<?php echo JText::_('K_CONFIG_RSS_DEFAULT_DESC'); ?>"><?php echo JText::_('K_CONFIG_RSS_DEFAULT'); ?></label>
			</td>
			<td>
				<select name="config[rss_default]" id="rss_default">
					<option value="0"<?php echo (($this->options->get('rss_default', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_BY_THREAD'); ?></option>
					<option value="1"<?php echo (($this->options->get('rss_default', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_BY_POST'); ?></option>
				</select>
			</td>
		</tr>

		<tr>
			<td width="40%" class="key">
				<label for="config_rss_history" class="hasTip" title="<?php echo JText::_('K_CONFIG_RSS_HISTORY_DESC'); ?>"><?php echo JText::_('K_CONFIG_RSS_HISTORY'); ?></label>
			</td>
			<td>
				<select name="config[rss_history]" id="rss_history">
					<option value="0"<?php echo (($this->options->get('rss_history', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_1WEEK'); ?></option>
					<option value="1"<?php echo (($this->options->get('rss_history', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_1MONTH'); ?></option>
                    <option value="2"<?php echo (($this->options->get('rss_history', 1) == 2) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_1YEAR'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key" align="right">
				<label for="config_pdf_enabled" class="hasTip" title="<?php echo JText::_('K_CONFIG_PDF_ENABLED_DESC'); ?>"><?php echo JText::_('K_CONFIG_PDF_ENABLED'); ?></label>
			</td>
			<td>
				<select name="config[pdf_enabled]" id="pdf_enabled">
					<option value="0"<?php echo (($this->options->get('pdf_enabled', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('pdf_enabled', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
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
	<legend><?php echo JText::_('K_CONFIG_FRONTEND'); ?>
    </legend>


	<legend><?php echo JText::_('K_CONFIG_LOOK_AND_FEEL'); ?></legend>
	<table class="admintable" cellspacing="1" align="left" width="50%">
	<tbody>
		<tr>
			<td width="40%" class="key">
				<label for="config_thread_by_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_THREAD_BY_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_THREAD_BY_PAGE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[thread_by_page]" id="thread_by_page" value="<?php echo $this->options->get('thread_by_page'); ?>" size="30" />

			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_message_by_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_MESSAGE_BY_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_MESSAGE_BY_PAGE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[message_by_page]" id="message_by_page" value="<?php echo $this->options->get('message_by_page'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_search_results" class="hasTip" title="<?php echo JText::_('K_CONFIG_SEARCH_RESULTS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SEARCH_RESULTS'); ?></label>
			</td>
			<td>
				<input type="text" name="config[search_results]" id="search_results" value="<?php echo $this->options->get('search_results'); ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_show_history" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_HISTORY_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_HISTORY'); ?></label>
			</td>
			<td>
				<select name="config[show_history]" id="show_history">
					<option value="no"<?php echo (($this->options->get('show_history', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_history', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_history_limits" class="hasTip" title="<?php echo JText::_('K_CONFIG_HISTORY_LIMITS_DESC'); ?>"><?php echo JText::_('K_CONFIG_HISTORY_LIMITS'); ?></label>
			</td>
			<td>
				<input type="text" name="config[history_limits]" id="config_history_limits" value="<?php echo $this->options->get('history_limits'); ?>" size="30" />
			</td>
		</tr>
		<tr>
		<td width="40%" class="key">
				<label for="config_show_newpost" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_NEWPOST_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_NEWPOST'); ?></label>
			</td>
			<td>
				<select name="config[show_newpost]" id="show_newpost">
					<option value="no"<?php echo (($this->options->get('show_newpost', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_newpost', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_new_indicator" class="hasTip" title="<?php echo JText::_('K_CONFIG_NEW_INDICATOR_DESC'); ?>"><?php echo JText::_('K_CONFIG_NEW_INDICATOR'); ?></label>
			</td>
			<td>
				<input type="text" name="config[new_indicator]" id="config_new_indicator" value="<?php echo $this->options->get('new_indicator'); ?>" size="5" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_mambot" class="hasTip" title="<?php echo JText::_('K_CONFIG_MAMBOT_DESC'); ?>"><?php echo JText::_('K_CONFIG_MAMBOT'); ?></label>
			</td>
			<td>
				<select name="config[mambot]" id="mambot">
					<option value="no"<?php echo (($this->options->get('mambot', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('mambot', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_disable_emoticons" class="hasTip" title="<?php echo JText::_('K_CONFIG_DISABLE_EMOTICONS_DESC'); ?>"><?php echo JText::_('K_CONFIG_DISABLE_EMOTICONS'); ?></label>
			</td>
			<td>
				<select name="config[disable_emoticons]" id="disable_emoticons">
					<option value="no"<?php echo (($this->options->get('disable_emoticons', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('disable_emoticons', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
       <td width="40%" class="key">
				<label for="config_template" class="hasTip" title="<?php echo JText::_('K_CONFIG_TEMPLATE_DESC'); ?>"><?php echo JText::_('K_CONFIG_TEMPLATE'); ?></label>
			</td>
			<td>
				<select name="config[template]" id="template">
					<option value="Default"<?php echo (($this->options->get('template', 'kunena') == 'default') ? ' selected="selected"' : ''); ?>>Default</option>
					<option value="Default_ex"<?php echo (($this->options->get('template', 'kunena') == 'default_ex') ? ' selected="selected"' : ''); ?>>Default_ex</option>
                    <option value="Default_gray"<?php echo (($this->options->get('template', 'kunena') == 'default_gray') ? ' selected="selected"' : ''); ?>>Default_gray</option>
                    <option value="Default_green"<?php echo (($this->options->get('template', 'kunena') == 'default_green') ? ' selected="selected"' : ''); ?>>Default_green</option>
                    <option value="Default_red"<?php echo (($this->options->get('template', 'kunena') == 'default_red') ? ' selected="selected"' : ''); ?>>Default_red</option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_imageset" class="hasTip" title="<?php echo JText::_('K_CONFIG_IMAGE_SET_DESC'); ?>"><?php echo JText::_('K_CONFIG_IMAGE_SET'); ?></label>
			</td>
			<td>
				<select name="config[imageset]" id="imageset">
					<option value="Default"<?php echo (($this->options->get('imageset', 'kunena') == 'default') ? ' selected="selected"' : ''); ?>>Default</option>
					<option value="Default_ex"<?php echo (($this->options->get('imageset', 'kunena') == 'default_ex') ? ' selected="selected"' : ''); ?>>Default_ex</option>
                    <option value="Default_gray"<?php echo (($this->options->get('imageset', 'kunena') == 'default_gray') ? ' selected="selected"' : ''); ?>>Default_gray</option>
                    <option value="Default_green"<?php echo (($this->options->get('imageset', 'kunena') == 'default_green') ? ' selected="selected"' : ''); ?>>Default_green</option>
                    <option value="Default_red"<?php echo (($this->options->get('imageset', 'kunena') == 'default_red') ? ' selected="selected"' : ''); ?>>Default_red</option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_default_kunena_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_DEFAULT_KUNENA_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_DEFAULT_KUNENA_PAGE'); ?></label>
			</td>
			<td>
				<select name="config[default_kunena_page]" id="default_kunena_page">
					<option value="Recent Discussions"<?php echo (($this->options->get('default_kunena_page', 'kunena') == 'Recent Discussions') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_RECENT_DISCUSSIONS'); ?></option>
					<option value="My Discussions"<?php echo (($this->options->get('default_kunena_page', 'kunena') == 'My Discussions') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_MY_DISCUSSIONS'); ?></option>
                    <option value="Categories"<?php echo (($this->options->get('default_kunena_page', 'kunena') == 'Categories') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_CATEGORIES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_use_joomla_style" class="hasTip" title="<?php echo JText::_('K_CONFIG_USE_JOOMLA_STYLE_DESC'); ?>"><?php echo JText::_('K_CONFIG_USE_JOOMLA_STYLE'); ?></label>
			</td>
			<td>
				<select name="config[use_joomla_style]" id="use_joomla_style">
					<option value="no"<?php echo (($this->options->get('use_joomla_style', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('use_joomla_style', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_announcement" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_ANNOUCEMENT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_ANNOUCEMENT'); ?></label>
			</td>
			<td>
				<select name="config[use_joomla_style]" id="show_announcement">
					<option value="no"<?php echo (($this->options->get('show_announcement', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_announcement', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_avator_categories" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_AVATOR_CATEGORIES_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_AVATOR_CATEGORIES'); ?></label>
			</td>
			<td>
				<select name="config[show_avator_categories]" id="show_avator_categories">
					<option value="no"<?php echo (($this->options->get('show_avator_categories', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_avator_categories', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_category_image_path" class="hasTip" title="<?php echo JText::_('K_CONFIG_CATEGORIE_IMAGEPATH_DESC'); ?>"><?php echo JText::_('K_CONFIG_CATEGORIE_IMAGEPATH'); ?></label>
			</td>
			<td>
				<input type="text" name="config[category_image_path]" id="config_category_image_path" value="<?php echo $this->options->get('category_image_path'); ?>" size="30" />
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_number_child_category" class="hasTip" title="<?php echo JText::_('K_CONFIG_NUMBER_CHILD_CATEGORY_DESC'); ?>"><?php echo JText::_('K_CONFIG_NUMBER_CHILD_CATEGORY'); ?></label>
			</td>
			<td>
				<input type="text" name="config[number_child_category]" id="number_child_category" value="<?php echo $this->options->get('number_child_category'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_child_categorie_image" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_CHILD_CATEGORIE_IMAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_CHILD_CATEGORIE_IMAGE'); ?> </label>
			</td>
			<td>
				<select name="config[show_child_categorie_image]" id="show_child_categorie_image">
					<option value="no"<?php echo (($this->options->get('show_child_categorie_image', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_child_categorie_image', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_announcement_moderator_id" class="hasTip" title="<?php echo JText::_('K_CONFIG_ANNOUCEMENT_MODERATOR_ID_DESC'); ?>"><?php echo JText::_('K_CONFIG_ANNOUCEMENT_MODERATOR_ID'); ?></label>
			</td>
			<td>
				<input type="text" name="config[announcement_moderator_id]" id="announcement_moderator_id" value="<?php echo $this->options->get('announcement_moderator_id'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_textarea_width" class="hasTip" title="<?php echo JText::_('K_CONFIG_TEXTAREA_WIDTH_DESC'); ?>"><?php echo JText::_('K_CONFIG_TEXTAREA_WIDTH'); ?></label>
			</td>
			<td>
				<input type="text" name="config[textarea_width]" id="textarea_width" value="<?php echo $this->options->get('textarea_width'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_textarea_height" class="hasTip" title="<?php echo JText::_('K_CONFIG_TEXTAREA_HEIGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_TEXTAREA_HEIGHT'); ?></label>
			</td>
			<td>
				<input type="text" name="config[textarea_height]" id="textarea_height" value="<?php echo $this->options->get('textarea_height'); ?>" size="30" />
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_enable_rules_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_RULES_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_RULES'); ?></label>
			</td>
			<td>
				<select name="config[enable_rules_page]" id="enable_rules_page">
					<option value="no"<?php echo (($this->options->get('enable_rules_page', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('enable_rules_page', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_rules_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_RULES_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_RULES_PAGE'); ?></label>
			</td>
			<td>
				<select name="config[show_rules_page]" id="show_rules_page">
					<option value="no"<?php echo (($this->options->get('show_rules_page', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_rules_page', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_rules_content_id" class="hasTip" title="<?php echo JText::_('K_CONFIG_RULES_CONTENT_ID_DESC'); ?>"><?php echo JText::_('K_CONFIG_RULES_CONTENT_ID'); ?></label>
			</td>
			<td>
				<input type="text" name="config[rules_content_id]" id="rules_content_id" value="<?php echo $this->options->get('rules_content_id'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_rules_external_page_link" class="hasTip" title="<?php echo JText::_('K_CONFIG_RULES_EXTERNAL_PAGE_LINK_DESC'); ?>"><?php echo JText::_('K_CONFIG_RULES_EXTERNAL_PAGE_LINK'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[rules_external_page_link]" id="rules_external_page_link" value="<?php echo $this->options->get('rules_external_page_link'); ?>" size="30" />
			</td>
		</tr>
       <td width="40%" class="key">
				<label for="config_enable_help_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_HELP_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_HELP_PAGE'); ?></label>
			</td>
			<td>
				<select name="config[enable_help_page]" id="enable_help_page">
					<option value="no"<?php echo (($this->options->get('enable_help_page', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('enable_help_page', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_help_page" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_HELP_PAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_HELP_PAGE'); ?></label>
			</td>
			<td>
				<select name="config[show_help_page]" id="show_help_page">
					<option value="no"<?php echo (($this->options->get('show_help_page', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_help_page', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_help_content_id" class="hasTip" title="<?php echo JText::_('K_CONFIG_HELP_CONTENT_ID_DESC'); ?>"><?php echo JText::_('K_CONFIG_HELP_CONTENT_ID'); ?></label>
			</td>
			<td>
				<input type="text" name="config[help_content_id]" id="help_content_id" value="<?php echo $this->options->get('help_content_id'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_help_external_page_link" class="hasTip" title="<?php echo JText::_('K_CONFIG_HELP_EXTERNAL_PAGE_LINK_DESC'); ?>"><?php echo JText::_('K_CONFIG_HELP_EXTERNAL_PAGE_LINK'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[help_external_page_link]" id="help_external_page_link" value="<?php echo $this->options->get('help_external_page_link'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_enable_forum_jump" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_FORUM_JUMP_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_FORUM_JUMP'); ?></label>
			</td>
			<td>
				<select name="config[enable_forum_jump]" id="enable_forum_jump">
					<option value="no"<?php echo (($this->options->get('enable_forum_jump', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('enable_forum_jump', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_message_reporting" class="hasTip" title="<?php echo JText::_('K_CONFIG_MESSAGE_REPORTING_DESC'); ?>"><?php echo JText::_('K_CONFIG_MESSAGE_REPORTING'); ?> </label>
			</td>
			<td>
				<select name="config[message_reporting]" id="message_reporting">
					<option value="no"<?php echo (($this->options->get('message_reporting', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('message_reporting', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        </tbody>
	</table>

        <legend><?php echo JText::_('K_CONFIG_USER_RELATED'); ?></legend>
<table width="50%" class="admintable" cellspacing="1" align="right" style="padding-bottom:10px;">
		<tr>
        <td width="40%" class="key">
				<label for="config_username" class="hasTip" title="<?php echo JText::_('K_CONFIG_USERNAME_DESC'); ?>"><?php echo JText::_('K_CONFIG_USERNAME'); ?> </label>
			</td>
			<td>
				<select name="config[username]" id="username">
					<option value="no"<?php echo (($this->options->get('username', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('username', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_require_email" class="hasTip" title="<?php echo JText::_('K_CONFIG_REQUIRE_EMAIL_DESC'); ?>"><?php echo JText::_('K_CONFIG_REQUIRE_EMAIL'); ?></label>
			</td>
			<td>
				<select name="config[require_email]" id="require_email">
					<option value="no"<?php echo (($this->options->get('require_email', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('require_email', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_email" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_EMAIL_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_EMAIL'); ?> </label>
			</td>
			<td>
				<select name="config[show_email]" id="show_email">
					<option value="no"<?php echo (($this->options->get('show_email', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_email', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_userstats" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_USERSTATS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_USERSTATS'); ?></label>
			</td>
			<td>
				<select name="config[show_userstats]" id="show_userstats">
					<option value="no"<?php echo (($this->options->get('show_userstats', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_userstats', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_posts_statistics_bar" class="hasTip" title="<?php echo JText::_('K_CONFIG_POSTS_STATITICS_BAR_DESC'); ?>"><?php echo JText::_('K_CONFIG_POSTS_STATITICS_BAR'); ?></label>
			</td>
			<td>
				<select name="config[posts_statistics_bar]" id="posts_statistics_bar">
					<option value="no"<?php echo (($this->options->get('posts_statistics_bar', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('posts_statistics_bar', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_color_statistics_bar" class="hasTip" title="<?php echo JText::_('K_CONFIG_COLOR_STATISTICS_BAR_DESC'); ?>"><?php echo JText::_('K_CONFIG_COLOR_STATISTICS_BAR'); ?></label>
			</td>
			<td>
				<input type="text" name="config[color_statistics_bar]" id="color_statistics_bar" value="<?php echo $this->options->get('color_statistics_bar'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_karma_indicator" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_KARMA_INDICATOR_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_KARMA_INDICATOR'); ?></label>
			</td>
			<td>
				<select name="config[show_karma_indicator]" id="show_karma_indicator">
					<option value="no"<?php echo (($this->options->get('show_karma_indicator', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_karma_indicator', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_user_edits" class="hasTip" title="<?php echo JText::_('K_CONFIG_USER_EDITS_DESC'); ?>"><?php echo JText::_('K_CONFIG_USER_EDITS'); ?></label>
			</td>
			<td>
				<select name="config[user_edits]" id="user_edits">
					<option value="no"<?php echo (($this->options->get('user_edits', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('user_edits', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_user_edit_time" class="hasTip" title="<?php echo JText::_('K_CONFIG_USER_EDIT_TIME_DESC'); ?>"><?php echo JText::_('K_CONFIG_USER_EDIT_TIME'); ?></label>
			</td>
			<td>
				<input type="text" name="config[user_edit_time]" id="user_edit_time" value="<?php echo $this->options->get('user_edit_time'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_user_edit_grace_time" class="hasTip" title="<?php echo JText::_('K_CONFIG_USER_EDIT_GRACE_TIME_DESC'); ?>"><?php echo JText::_('K_CONFIG_USER_EDIT_GRACE_TIME'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[user_edit_grace_time]" id="user_edit_grace_time" value="<?php echo $this->options->get('user_edit_grace_time'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_edited_markup" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_EDITED_MARKUP_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_EDITED_MARKUP'); ?> </label>
			</td>
			<td>
				<select name="config[show_edited_markup]" id="show_edited_markup">
					<option value="no"<?php echo (($this->options->get('show_edited_markup', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_edited_markup', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_allow_subscriptions" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_SUBSCRIPTIONS_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_SUBSCRIPTIONS'); ?> </label>
			</td>
			<td>
				<select name="config[allow_subscriptions]" id="allow_subscriptions">
					<option value="no"<?php echo (($this->options->get('allow_subscriptions', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('allow_subscriptions', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_post_subscription_checked" class="hasTip" title="<?php echo JText::_('K_CONFIG_POST_SUBSCRIPTION_CHECKED_DESC'); ?>"><?php echo JText::_('K_CONFIG_POST_SUBSCRIPTION_CHECKED'); ?></label>
			</td>
			<td>
				<select name="config[post_subscription_checked]" id="post_subscription_checked">
					<option value="no"<?php echo (($this->options->get('post_subscription_checked', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('post_subscription_checked', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_allow_favorites" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_FAVORITES_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_FAVORITES'); ?></label>
			</td>
			<td>
				<select name="config[allow_favorites]" id="allow_favorites">
					<option value="no"<?php echo (($this->options->get('allow_favorites', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('allow_favorites', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>

        </tbody>
	</table>

        <tr>
        <legend><?php echo JText::_('K_CONFIG_VARIOUS_LENGTH_SETTINGS'); ?></legend>
        <table width="50%" class="admintable" cellspacing="1" align="right" style="padding-top:10px;">
        <td width="40%" class="key">
				<label for="config_wrap_words_longer_than" class="hasTip" title="<?php echo JText::_('K_CONFIG_WRAP_WORDS_LONGER_THAN_DESC'); ?>"><?php echo JText::_('K_CONFIG_WRAP_WORDS_LONGER_THAN'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[wrap_words_longer_than]" id="wrap_words_longer_than" value="<?php echo $this->options->get('wrap_words_longer_than'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_subject_lenght" class="hasTip" title="<?php echo JText::_('K_CONFIG_CONFIG_SUBJECT_LENGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_CONFIG_SUBJECT_LENGHT'); ?></label>
			</td>
			<td>
				<input type="text" name="config[subject_lenght]" id="subject_lenght" value="<?php echo $this->options->get('subject_lenght'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_signature_lenght" class="hasTip" title="<?php echo JText::_('K_CONFIG_SIGNATURE_LENGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SIGNATURE_LENGHT'); ?></label>
			</td>
			<td>
				<input type="text" name="config[signature_lenght]" id="signature_lenght" value="<?php echo $this->options->get('signature_lenght'); ?>" size="30" />
			</td>
		</tr>
	</tbody>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Avatars', 'Avatars');?>
<fieldset>
	<legend><?php echo JText::_('K_CONFIG_AVATARS'); ?></legend>
	<table class="admintable" cellspacing="1">
	<tbody>
		<tr>
        <td width="40%" class="key">
				<label for="config_allow_avatars" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_AVATARS_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_AVATARS'); ?> </label>
			</td>
			<td>
				<select name="config[allow_avatars]" id="allow_avatars">
					<option value="no"<?php echo (($this->options->get('allow_avatars', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('allow_avatars', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_allow_avatars_upload" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_AVATARS_UPLOAD_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_AVATARS_UPLOAD'); ?> </label>
			</td>
			<td>
				<select name="config[allow_avatars_upload]" id="allow_avatars_upload">
					<option value="no"<?php echo (($this->options->get('allow_avatars_upload', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('allow_avatars_upload', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_use_avatars_gallery" class="hasTip" title="<?php echo JText::_('K_CONFIG_USE_AVATARS_GALLERY_DESC'); ?>"><?php echo JText::_('K_CONFIG_USE_AVATARS_GALLERY'); ?></label>
			</td>
			<td>
				<select name="config[use_avatars_gallery]" id="use_avatars_gallery">
					<option value="no"<?php echo (($this->options->get('use_avatars_gallery', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('use_avatars_gallery', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_image_processor" class="hasTip" title="<?php echo JText::_('K_CONFIG_IMAGE_PROCESSOR_DESC'); ?>"><?php echo JText::_('K_CONFIG_IMAGE_PROCESSOR'); ?></label>
			</td>
			<td>
				<select name="config[image_processor]" id="image_processor">
					<option value="non"<?php echo (($this->options->get('image_processor', 'kunena') == 'non') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="gd2"<?php echo (($this->options->get('image_processor', 'kunena') == 'gd2') ? ' selected="selected"' : ''); ?>>GD2</option>
                    <option value="gd1"<?php echo (($this->options->get('image_processor', 'kunena') == 'gd1') ? ' selected="selected"' : ''); ?>>GD1</option>
				</select>
			</td>
		</tr>
        </tr>
        <td width="40%" class="key">
				<label for="config_small_image_height" class="hasTip" title="<?php echo JText::_('K_CONFIG_SMALL_IMAGE_HEIGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SMALL_IMAGE_HEIGHT'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[small_image_height]" id="small_image_height" value="<?php echo $this->options->get('small_image_height'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_small_image_width" class="hasTip" title="<?php echo JText::_('K_CONFIG_SMALL_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('K_CONFIG_SMALL_IMAGE_WIDTH'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[small_image_width]" id="small_image_width" value="<?php echo $this->options->get('small_image_width'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_medium_image_height" class="hasTip" title="<?php echo JText::_('K_CONFIG_MEDIUM_IMAGE_HEIGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_MEDIUM_IMAGE_HEIGHT'); ?></label>
			</td>
			<td>
				<input type="text" name="config[medium_image_height]" id="medium_image_height" value="<?php echo $this->options->get('medium_image_height'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_medium_image_width" class="hasTip" title="<?php echo JText::_('K_CONFIG_MEDIUM_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('K_CONFIG_MEDIUM_IMAGE_WIDTH'); ?></label>
			</td>
			<td>
				<input type="text" name="config[medium_image_width]" id="medium_image_width" value="<?php echo $this->options->get('medium_image_width'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_large_image_height" class="hasTip" title="<?php echo JText::_('K_CONFIG_LARGE_IMAGE_HEIGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_LARGE_IMAGE_HEIGHT'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[large_image_height]" id="large_image_height" value="<?php echo $this->options->get('large_image_height'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_large_image_width" class="hasTip" title="<?php echo JText::_('K_CONFIG_LARGE_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('K_CONFIG_LARGE_IMAGE_WIDTH'); ?></label>
			</td>
			<td>
				<input type="text" name="config[large_image_width]" id="large_image_width" value="<?php echo $this->options->get('large_image_width'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_avatar_max_size" class="hasTip" title="<?php echo JText::_('K_CONFIG_AVATAR_MAX_SIZE_DESC'); ?>"><?php echo JText::_('K_CONFIG_AVATAR_MAX_SIZE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[avatar_max_size]" id="avatar_max_size" value="<?php echo $this->options->get('avatar_max_size'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_avatar_quality" class="hasTip" title="<?php echo JText::_('K_CONFIG_AVATAR_QUALITY_DESC'); ?>"><?php echo JText::_('K_CONFIG_AVATAR_QUALITY'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[avatar_quality]" id="avatar_quality" value="<?php echo $this->options->get('avatar_quality'); ?>" size="30" />%
			</td>
		</tr>

        </tbody>
	</table>
    </fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Media', 'Media');?>
<fieldset>
	<legend><?php echo JText::_('K_CONFIG_MEDIA'); ?>
    </legend>
	<legend><?php echo JText::_('K_CONFIG_UPLOADS_IMAGES'); ?></legend>
	<table class="admintable" cellspacing="1" align="left" width="50%" style="padding:10px">
	<tbody>
		<tr>
        <td width="40%" class="key">
				<label for="config_public_upload_images" class="hasTip" title="<?php echo JText::_('K_CONFIG_PUBLIC_UPLOAD_IMAGES_DESC'); ?>"><?php echo JText::_('K_CONFIG_PUBLIC_UPLOAD_IMAGES'); ?> </label>
			</td>
			<td>
				<select name="config[public_upload_images]" id="public_upload_images">
					<option value="no"<?php echo (($this->options->get('public_upload_images', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('public_upload_images', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_registered_upload_images" class="hasTip" title="<?php echo JText::_('K_CONFIG_REGISTERED_UPLOAD_IMAGES_DESC'); ?>"><?php echo JText::_('K_CONFIG_REGISTERED_UPLOAD_IMAGES'); ?> </label>
			</td>
			<td>
				<select name="config[registered_upload_images]" id="registered_upload_images">
					<option value="no"<?php echo (($this->options->get('registered_upload_images', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('registered_upload_images', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_max_image_height" class="hasTip" title="<?php echo JText::_('K_CONFIG_MAX_IMAGE_HEIGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_MAX_IMAGE_HEIGHT'); ?></label>
			</td>
			<td>
				<input type="text" name="config[max_image_height]" id="max_image_height" value="<?php echo $this->options->get('max_image_height'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_max_image_width" class="hasTip" title="<?php echo JText::_('K_CONFIG_MAX_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('K_CONFIG_MAX_IMAGE_WIDTH'); ?></label>
			</td>
			<td>
				<input type="text" name="config[max_image_width]" id="max_image_width" value="<?php echo $this->options->get('max_image_width'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_max_filesize_image" class="hasTip" title="<?php echo JText::_('K_CONFIG_MAX_FILESIZE_IMAGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_MAX_FILESIZE_IMAGE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[max_filesize_image]" id="max_filesize_image" value="<?php echo $this->options->get('max_filesize_image'); ?>" size="30" />
			</td>
		</tr>
        </tbody>
	</table>
    <legend><?php echo JText::_('K_CONFIG_UPLOADS_FILES'); ?></legend>
        <table class="admintable" cellspacing="1" align="left" width="50%" style="padding-bottom:60px;">

	<tbody>
		<tr>
        <td width="40%" class="key">
				<label for="config_public_upload_files" class="hasTip" title="<?php echo JText::_('K_CONFIG_PUBLIC_UPLOAD_FILES_DESC'); ?>"><?php echo JText::_('K_CONFIG_PUBLIC_UPLOAD_FILES'); ?></label>
			</td>
			<td>
				<select name="config[public_upload_files]" id="public_upload_files">
					<option value="no"<?php echo (($this->options->get('public_upload_files', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('public_upload_files', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_registered_upload_files" class="hasTip" title="<?php echo JText::_('K_CONFIG_REGISTERED_UPLOAD_FILES_DESC'); ?>"><?php echo JText::_('K_CONFIG_REGISTERED_UPLOAD_FILES'); ?></label>
			</td>
			<td>
				<select name="config[registered_upload_files]" id="registered_upload_files">
					<option value="no"<?php echo (($this->options->get('registered_upload_files', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('registered_upload_files', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_file_type_allowed" class="hasTip" title="<?php echo JText::_('K_CONFIG_FILE_TYPE_ALLOWED_DESC'); ?>"><?php echo JText::_('K_CONFIG_FILE_TYPE_ALLOWED'); ?></label>
			</td>
			<td>
				<input type="text" name="config[file_type_allowed]" id="file_type_allowed" value="<?php echo $this->options->get('file_type_allowed'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_max_filesize_files" class="hasTip" title="<?php echo JText::_('K_CONFIG_MAX_FILESIZE_FILES_DESC'); ?>"><?php echo JText::_('K_CONFIG_MAX_FILESIZE_FILES'); ?></label>
			</td>
			<td>
				<input type="text" name="config[max_filesize_files]" id="max_filesize_files" value="<?php echo $this->options->get('max_filesize_files'); ?>" size="30" />
			</td>
		</tr>
          </tbody>
	</table>
    <legend><?php echo JText::_('K_CONFIG_BBCODE'); ?></legend>
	<table class="admintable" cellspacing="1" align="left" width="50%" style="padding-bottom:60px;">
	<tbody>
		<tr>
        <td width="40%" class="key">
				<label for="config_show_spoiler" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_SPOILER_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_SPOILER'); ?></label>
			</td>
			<td>
				<select name="config[show_spoiler]" id="show_spoiler">
					<option value="no"<?php echo (($this->options->get('show_spoiler', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_spoiler', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_video" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_VIDEO_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_VIDEO'); ?> </label>
			</td>
			<td>
				<select name="config[show_video]" id="show_video">
					<option value="no"<?php echo (($this->options->get('show_video', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_video', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_ebay" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_EBAY_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_EBAY'); ?> </label>
			</td>
			<td>
				<select name="config[show_ebay]" id="show_ebay">
					<option value="no"<?php echo (($this->options->get('show_ebay', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_ebay', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_ebay_language_code" class="hasTip" title="<?php echo JText::_('K_CONFIG_EBAY_LANGUAGE_CODE_DESC'); ?>"><?php echo JText::_('K_CONFIG_EBAY_LANGUAGE_CODE'); ?></label>
			</td>
			<td>
				<input type="text" name="config[ebay_language_code]" id="ebay_language_code" value="<?php echo $this->options->get('ebay_language_code'); ?>" size="30" />
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_trim_long_url" class="hasTip" title="<?php echo JText::_('K_CONFIG_TRIM_LONG_URL_DESC'); ?>"><?php echo JText::_('K_CONFIG_TRIM_LONG_URL'); ?></label>
			</td>
			<td>
				<select name="config[trim_long_url]" id="trim_long_url">
					<option value="no"<?php echo (($this->options->get('trim_long_url', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('trim_long_url', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_front_portion_trimmed_url" class="hasTip" title="<?php echo JText::_('K_CONFIG_FRONT_PORTION_TRIMMED_URL_DESC'); ?>"><?php echo JText::_('K_CONFIG_FRONT_PORTION_TRIMMED_URL'); ?></label>
			</td>
			<td>
				<input type="text" name="config[front_portion_trimmed_url]" id="front_portion_trimmed_url" value="<?php echo $this->options->get('front_portion_trimmed_url'); ?>" size="30" />
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_back_portion_trimmed_url" class="hasTip" title="<?php echo JText::_('K_CONFIG_BACK_PORTION_TRIMMED_URL_DESC'); ?>"><?php echo JText::_('K_CONFIG_BACK_PORTION_TRIMMED_URL'); ?></label>
			</td>
			<td>
				<input type="text" name="config[back_portion_trimmed_url]" id="back_portion_trimmed_url" value="<?php echo $this->options->get('back_portion_trimmed_url'); ?>" size="30" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_embed_youtube" class="hasTip" title="<?php echo JText::_('K_CONFIG_EMBED_YOUTUBE_DESC'); ?>"><?php echo JText::_('K_CONFIG_EMBED_YOUTUBE'); ?></label>
			</td>
			<td>
				<select name="config[embed_youtube]" id="embed_youtube">
					<option value="no"<?php echo (($this->options->get('embed_youtube', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('embed_youtube', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_embed_ebay" class="hasTip" title="<?php echo JText::_('K_CONFIG_EMBED_EBAY_DESC'); ?>"><?php echo JText::_('K_CONFIG_EMBED_EBAY'); ?> </label>
			</td>
			<td>
				<select name="config[embed_ebay]" id="embed_ebay">
					<option value="no"<?php echo (($this->options->get('embed_ebay', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('embed_ebay', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_code_highlighting" class="hasTip" title="<?php echo JText::_('K_CONFIG_CODE_HIGHLIGHTING_DESC'); ?>"><?php echo JText::_('K_CONFIG_CODE_HIGHLIGHTING'); ?></label>
			</td>
			<td>
				<select name="config[code_highlighting]" id="code_highlighting">
					<option value="no"<?php echo (($this->options->get('code_highlighting', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('code_highlighting', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        </tbody>

	</table>
    <legend><?php echo JText::_('K_CONFIG_RANKING'); ?></legend>
	<table class="admintable" cellspacing="1" align="right" width="50%" style="padding-bottom:60px;">
	<tbody>
		<tr>
        <td width="40%" class="key">
				<label for="config_enable_ranking" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_RANKING_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_RANKING'); ?> </label>
			</td>
			<td>
				<select name="config[enable_ranking]" id="enable_ranking">
					<option value="no"<?php echo (($this->options->get('enable_ranking', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('enable_ranking', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_use_ranking_images" class="hasTip" title="<?php echo JText::_('K_CONFIG_USE_RANKING_IMAGES_DESC'); ?>"><?php echo JText::_('K_CONFIG_USE_RANKING_IMAGES'); ?></label>
			</td>
			<td>
				<select name="config[use_ranking_images]" id="use_ranking_images">
					<option value="no"<?php echo (($this->options->get('use_ranking_images', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('use_ranking_images', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         </tbody>
	</table>
    </fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Security', 'Security');?>
<fieldset>
	<legend><?php echo JText::_('K_CONFIG_SECURITY_SETTINGS'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_regonly" class="hasTip" title="<?php echo JText::_('K_CONFIG_REGONLY_DESC'); ?>"><?php echo JText::_('K_CONFIG_REGONLY'); ?></label>
			</td>
			<td>
				<select name="config[regonly]" id="config_regonly">
					<option value="0"<?php echo (($this->options->get('regonly', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('regonly', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_allow_name_change" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_NAME_CHANGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_NAME_CHANGE'); ?> </label>
			</td>
			<td>
				<select name="config[allow_name_change]" id="config_allow_name_change">
					<option value="0"<?php echo (($this->options->get('allow_name_change', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('allow_name_change', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_public_read_write" class="hasTip" title="<?php echo JText::_('K_CONFIG_PUBLIC_READ_WRITE_DESC'); ?>"><?php echo JText::_('K_CONFIG_PUBLIC_READ_WRITE'); ?></label>
			</td>
			<td>
				<select name="config[public_read_write]" id="config_public_read_write">
					<option value="0"<?php echo (($this->options->get('public_read_write', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('public_read_write', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="<?php echo JText::_('K_CONFIG_FLOOD_PROTECTION_DESC'); ?>"><?php echo JText::_('K_CONFIG_FLOOD_PROTECTION'); ?></label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_email_moderators" class="hasTip" title="<?php echo JText::_('K_CONFIG_EMAIL_MODERATORS_DESC'); ?>"><?php echo JText::_('K_CONFIG_EMAIL_MODERATORS'); ?>   </label>
			</td>
			<td>
				<select name="config[email_moderators]" id="config_email_moderators">
					<option value="0"<?php echo (($this->options->get('email_moderators', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('email_moderators', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_email_administrators" class="hasTip" title="<?php echo JText::_('K_CONFIG_EMAIL_ADMINISTRATORS_DESC'); ?>"><?php echo JText::_('K_CONFIG_EMAIL_ADMINISTRATORS'); ?></label>
			</td>
			<td>
				<select name="config[email_administrators]" id="config_email_administrators">
					<option value="0"<?php echo (($this->options->get('email_administrators', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('email_administrators', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_enable_spam_protection_system" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_SPAM_PROTECTION_SYSTEM_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_SPAM_PROTECTION_SYSTEM'); ?> </label>
			</td>
			<td>
				<select name="config[enable_spam_protection_system]" id="config_enable_spam_protection_system">
					<option value="0"<?php echo (($this->options->get('enable_spam_protection_system', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('enable_spam_protection_system', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_send_complete_email_subscribers" class="hasTip" title="<?php echo JText::_('K_CONFIG_SEND_COMPLETE_EMAIL_SUBSCRIBERS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SEND_COMPLETE_EMAIL_SUBSCRIBERS'); ?></label>
			</td>
			<td>
				<select name="config[send_complete_email_subscribers]" id="send_complete_email_subscribers">
					<option value="0"<?php echo (($this->options->get('send_complete_email_subscribers', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('send_complete_email_subscribers', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
	</table>
</fieldset>
<?php

echo $pane->endPanel();
echo $pane->startPanel('Integration', 'Integration');
?>
<fieldset>
	<legend><?php echo JText::_('K_CONFIG_INTEGRATION'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_avatar_src" class="hasTip" title="<?php echo JText::_('K_CONFIG_AVATAR_SRC_DESC'); ?>"><?php echo JText::_('K_CONFIG_AVATAR_SRC'); ?></label>
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
        <td width="40%" class="key">
				<label for="config_profile" class="hasTip" title="<?php echo JText::_('K_CONFIG_PROFILE_DESC'); ?>"><?php echo JText::_('K_CONFIG_PROFILE'); ?> </label>
			</td>
			<td>
				<select name="config[profile]" id="config_profile">
					<option value="kunena"<?php echo (($this->options->get('profile', 'kunena') == 'kunena') ? ' selected="selected"' : ''); ?>>Kunena</option>
					<option value="cb"<?php echo (($this->options->get('profile', 'kunena') == 'cb') ? ' selected="selected"' : ''); ?>>Community Builder</option>
					<option value="jomsocial"<?php echo (($this->options->get('profile', 'kunena') == 'jomsocial') ? ' selected="selected"' : ''); ?>>JomSocial</option>
					<option value="gravatar"<?php echo (($this->options->get('profile', 'kunena') == 'gravatar') ? ' selected="selected"' : ''); ?>>Gravatar</option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_enable_private_messaging" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_PRIVATE_MESSAGING_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_PRIVATE_MESSAGING'); ?> </label>
			</td>
			<td>
				<select name="config[enable_private_messaging]" id="enable_private_messaging">
					<option value="no"<?php echo (($this->options->get('profile', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="cb"<?php echo (($this->options->get('profile', 'kunena') == 'cb') ? ' selected="selected"' : ''); ?>>Community Builder</option>
					<option value="jomsocial"<?php echo (($this->options->get('profile', 'kunena') == 'jomsocial') ? ' selected="selected"' : ''); ?>>JomSocial</option>
					<option value="uddheim"<?php echo (($this->options->get('profile', 'kunena') == 'uddheim') ? ' selected="selected"' : ''); ?>>Uddheim</option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_enable_discussbot" class="hasTip" title="<?php echo JText::_('K_CONFIG_ENABLE_DISCUSSBOT_DESC'); ?>"><?php echo JText::_('K_CONFIG_ENABLE_DISCUSSBOT'); ?> </label>
			</td>
			<td>
				<select name="config[enable_discussbot]" id="config_enable_discussbot">
					<option value="0"<?php echo (($this->options->get('enable_discussbot', 1) == 0) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="1"<?php echo (($this->options->get('enable_discussbot', 1) == 1) ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Plugins', 'Plugins');?>
<fieldset>


	<legend><?php echo JText::_('K_CONFIG_PLUGINS_SETTINGS'); ?></legend>
	<table width="50%" class="admintable" cellspacing="1" align="left">
		<tr>
        <td width="40%" class="key">
				<label for="config_number_userlist_row" class="hasTip" title="<?php echo JText::_('K_CONFIG_NUMBER_USERLIST_ROW_DESC'); ?>"><?php echo JText::_('K_CONFIG_NUMBER_USERLIST_ROW'); ?></label>
			</td>
			<td>
				<input type="text" name="config[number_userlist_row]" id="config_number_userlist_row" value="<?php echo $this->options->get('number_userlist_row', 10); ?>" size="5" />
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_online_status" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_ONLINE_STATUS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_ONLINE_STATUS'); ?></label>
			</td>
			<td>
				<select name="config[show_online_status]" id="show_online_status">
					<option value="no"<?php echo (($this->options->get('show_online_status', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_online_status', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_avatar" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_AVATAR_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_AVATAR'); ?></label>
			</td>
			<td>
				<select name="config[show_avatar]" id="show_avatar">
					<option value="no"<?php echo (($this->options->get('show_avatar', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_avatar', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_realname" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_REALNAME_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_REALNAME'); ?></label>
			</td>
			<td>
				<select name="config[show_realname]" id="show_realname">
					<option value="no"<?php echo (($this->options->get('show_realname', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_realname', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_username" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_USERNAME_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_USERNAME'); ?></label>
			</td>
			<td>
				<select name="config[show_username]" id="show_username">
					<option value="no"<?php echo (($this->options->get('show_username', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_username', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_usergroup" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_USERGROUP_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_USERGROUP'); ?></label>
			</td>
			<td>
				<select name="config[show_usergroup]" id="show_usergroup">
					<option value="no"<?php echo (($this->options->get('show_usergroup', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_usergroup', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_show_number_posts" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_NUMBER_POSTS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_NUMBER_POSTS'); ?></label>
			</td>
			<td>
				<select name="config[show_number_posts]" id="show_number_posts">
					<option value="no"<?php echo (($this->options->get('show_number_posts', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_number_posts', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_karma" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_KARMA_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_KARMA'); ?></label>
			</td>
			<td>
				<select name="config[show_karma]" id="show_karma">
					<option value="no"<?php echo (($this->options->get('show_karma', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_karma', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_email_plugin" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_EMAIL_PLUGIN_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_EMAIL_PLUGIN'); ?></label>
			</td>
			<td>
				<select name="config[show_email_plugin]" id="show_email_plugin">
					<option value="no"<?php echo (($this->options->get('show_email_plugin', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_email_plugin', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_user_type" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_USER_TYPE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_USER_TYPE'); ?> </label>
			</td>
			<td>
				<select name="config[show_user_type]" id="show_user_type">
					<option value="no"<?php echo (($this->options->get('show_user_type', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_user_type', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_show_join_date" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_JOINDATE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_JOINDATE'); ?> </label>
			</td>
			<td>
				<select name="config[show_join_date]" id="show_join_date">
					<option value="no"<?php echo (($this->options->get('show_join_date', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_join_date', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_show_last_visit_date" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_LAST_VISIT_DATE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_LAST_VISIT_DATE'); ?> </label>
			</td>
			<td>
				<select name="config[show_last_visit_date]" id="show_last_visit_date">
					<option value="no"<?php echo (($this->options->get('show_last_visit_date', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_last_visit_date', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_profile_hits" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_PROFILE_HITS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_PROFILE_HITS'); ?> </label>
			</td>
			<td>
				<select name="config[show_profile_hits]" id="show_profile_hits">
					<option value="no"<?php echo (($this->options->get('show_profile_hits', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_profile_hits', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_recent_posts" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_RECENT_POSTS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_RECENT_POSTS'); ?> </label>
			</td>
			<td>
				<select name="config[show_recent_posts]" id="show_recent_posts">
					<option value="no"<?php echo (($this->options->get('show_recent_posts', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_recent_posts', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_number_of_recent_posts" class="hasTip" title="<?php echo JText::_('K_CONFIG_NUMBER_OF_RECENT_POSTS_DESC'); ?>"><?php echo JText::_('K_CONFIG_NUMBER_OF_RECENT_POSTS'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[number_of_recent_posts]" id="number_of_recent_posts" value="<?php echo $this->options->get('number_of_recent_posts'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_count_per_tab" class="hasTip" title="<?php echo JText::_('K_CONFIG_COUNT_PER_TAB_DESC'); ?>"><?php echo JText::_('K_CONFIG_COUNT_PER_TAB'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[count_per_tab]" id="count_per_tab" value="<?php echo $this->options->get('count_per_tab'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_show_category" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_CATEGORY_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_CATEGORY'); ?>  </label>
			</td>
			<td>
				<input type="text" name="config[show_category]" id="show_category" value="<?php echo $this->options->get('show_category'); ?>" size="30" />
			</td>
		</tr>
		<tr>
         <td width="40%" class="key">
				<label for="config_show_single_subject" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_SINGLE_SUBJECT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_SINGLE_SUBJECT'); ?> </label>
			</td>
			<td>
				<select name="config[show_single_subject]" id="show_single_subject">
					<option value="no"<?php echo (($this->options->get('show_single_subject', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_single_subject', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_reply_subject" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_REPLY_SUBJECT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_REPLY_SUBJECT'); ?>   </label>
			</td>
			<td>
				<select name="config[show_reply_subject]" id="show_reply_subject">
					<option value="no"<?php echo (($this->options->get('show_reply_subject', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_reply_subject', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_subject_lenght" class="hasTip" title="<?php echo JText::_('K_CONFIG_SUBJECT_LENGHT_DESC'); ?>"><?php echo JText::_('K_CONFIG_SUBJECT_LENGHT'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[config_subject_lenght]" id="config_subject_lenght" value="<?php echo $this->options->get('config_subject_lenght'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_show_date" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_DATE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_DATE'); ?></label>
			</td>
			<td>
				<select name="config[show_date]" id="show_date">
					<option value="no"<?php echo (($this->options->get('show_date', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_date', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_hits" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_HITS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_HITS'); ?></label>
			</td>
			<td>
				<select name="config[show_hits]" id="show_hits">
					<option value="no"<?php echo (($this->options->get('show_hits', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_hits', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_author" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_AUTHOR_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_AUTHOR'); ?></label>
			</td>
			<td>
				<input type="text" name="config[show_author]" id="show_author" value="<?php echo $this->options->get('show_author'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        </tbody>
	</table>
        <legend><?php echo JText::_('K_CONFIG_USER_RELATED'); ?></legend>
        <table width="50%" class="admintable" cellspacing="1" align="right" style="padding-bottom:10px;">
        <tbody>
        <td width="40%" class="key">
				<label for="config_show_stats" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_STATS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_STATS'); ?></label>
			</td>
			<td>

				<select name="config[show_stats]" id="show_stats">
					<option value="no"<?php echo (($this->options->get('show_stats', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_stats', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_whoisonline" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_WHOISONLINE_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_WHOISONLINE'); ?></label>
			</td>
			<td>
				<select name="config[show_whoisonline]" id="show_whoisonline">
					<option value="no"<?php echo (($this->options->get('show_whoisonline', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_whoisonline', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_general_stats" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_GENERAL_STATS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_GENERAL_STATS'); ?> </label>
			</td>
			<td>
				<select name="config[show_general_stats]" id="show_general_stats">
					<option value="no"<?php echo (($this->options->get('show_general_stats', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_general_stats', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_show_popular_stats" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_POPULAR_STATS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_POPULAR_STATS'); ?></label>
			</td>
			<td>
				<select name="config[show_popular_stats]" id="show_popular_stats">
					<option value="no"<?php echo (($this->options->get('show_popular_stats', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_popular_stats', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        <td width="40%" class="key">
				<label for="config_number_popular_user" class="hasTip" title="<?php echo JText::_('K_CONFIG_NUMBER_POLULAR_USER_DESC'); ?>"><?php echo JText::_('K_CONFIG_NUMBER_POLULAR_USER'); ?></label>
			</td>
			<td>
				<input type="text" name="config[number_popular_user]" id="number_popular_user" value="<?php echo $this->options->get('number_popular_user'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        <td width="40%" class="key">
				<label for="config_show_popular_subjects_stats" class="hasTip" title="<?php echo JText::_('K_CONFIG_SHOW_POLULAR_SUBJECTS_STATS_DESC'); ?>"><?php echo JText::_('K_CONFIG_SHOW_POLULAR_SUBJECTS_STATS'); ?></label>
			</td>
			<td>
				<select name="config[show_popular_subjects_stats]" id="show_popular_subjects_stats">
					<option value="no"<?php echo (($this->options->get('show_popular_subjects_stats', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('show_popular_subjects_stats', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
         <td width="40%" class="key">
				<label for="config_number_popular_subject" class="hasTip" title="<?php echo JText::_('K_CONFIG_NUMBER_POLULAR_SUBJECTS_DESC'); ?>"><?php echo JText::_('K_CONFIG_NUMBER_POLULAR_SUBJECTS'); ?> </label>
			</td>
			<td>
				<input type="text" name="config[number_popular_subject]" id="nnumber_popular_subject" value="<?php echo $this->options->get('number_popular_subject'); ?>" size="30" />
			</td>
		</tr>
		<tr>
        </tbody>
	</table>
      <legend><?php echo JText::_('K_CONFIG_MY_PROFILE_PLUGIN_SETTINGS'); ?></legend>
        <table width="50%" class="admintable" cellspacing="1" align="right" style="padding-bottom:10px;">
        <td width="40%" class="key">
				<label for="config_allow_username_change" class="hasTip" title="<?php echo JText::_('K_CONFIG_ALLOW_USERNAME_CHANGE_DESC'); ?>"><?php echo JText::_('K_CONFIG_ALLOW_USERNAME_CHANGE'); ?> </label>
			</td>
			<td>
				<select name="config[allow_username_change]" id="allow_username_change">
					<option value="no"<?php echo (($this->options->get('allow_username_change', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_NO'); ?></option>
					<option value="yes"<?php echo (($this->options->get('allow_username_change', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>><?php echo JText::_('K_CONFIG_YES'); ?></option>
				</select>
			</td>
		</tr>
        </tbody>
	</table>
</fieldset>
<?php
echo $pane->endPanel();
}
echo $pane->endPane();

?>