<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php echo $this->loadCommonTemplate('pathway'); ?>

<table class="forum_body">
	<thead>
		<tr>
			<th colspan="2">
				<h1><?php echo $this->escape($this->title); ?></h1>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="170"><img src="<?php echo JURI::Base(); ?>components/com_kunena/media/images/logo.png" alt="<?php echo JText::_('K_LOGO'); ?>" title="<?php echo JText::_('K_LOGO'); ?>" /></td>
			<td>
				<div>
					An open source project like Kunena requires the dedication and investment of personal time from various contributors.
					This version of Kunena Forum has been made possible by the following contributors (in alphabetical order):
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<ul>
<?php foreach ($this->contributors as $contributor): ?>
					<li><a href="<?php echo $contributor['url']; ?>" target='_blank' rel='follow'><?php echo $contributor['name']; ?></a>
					<?php echo $contributor['title']; ?></li>
<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<div>
					In addition many members of <a href="http://www.kunena.com" target='_blank' rel='follow'>www.Kunena.com</a> have contributed and helped make this a more stable and bugfree version.
					Our Thanks go out to all contributors of Kunena! Greetings from the global Kunena forum team!
					<br />
					<br />
<?php
// Add a link to go back to the latest category we where viewing...
//echo '<div>To return to the forum ' . CKunenaLink::GetCategoryLink('showcat', $catid, _USER_RETURN_B, $rel='nofollow') . '<div>';
?>
				</div>
				<div>
				<?php echo JText::_('K_COPYRIGHT'); ?>: © 2008-2009 <a href="http://www.kunena.com" target='_blank' rel='follow'>Kunena Team</a>. All rights reserved.
				<br />
				<?php echo JText::_('K_LICENSE'); ?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU General Public License</a>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<!-- B: Category List Bottom -->
<div class="bottom_info_box">
	<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
</div>