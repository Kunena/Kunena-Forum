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

<div class="fb_forum-headerdesc">
	<b>TODO: ADD LOGIC FOR HEADERDESC</b>
</div>

<!-- B: List Actions -->
<div class="fb_list_actions">
	<div class="fb_list_actions_goto">
		<a name="forumtop"></a>
		<?php echo JHtml::_('klink.pageAnker', 'atag', 'forumbottom', '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/bottom_arrow.gif" border="0" alt="Go to bottom" title="Go to bottom" /></a>'); ?>
	</div>

	<div class="fb_list_actions_forum"></div>

	<div class="pagination_box">
		<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div class="discussions">
		<?php echo $this->pagination->getResultsCounter(); ?>
	</div>
</div>
<!-- F: List Actions -->

<table class="fb_blocktable">
	<thead>
		<tr>
			<th colspan="2">
			<h1>TOPIC: <?php echo $this->escape($this->messages[0]->subject); ?></h1>
			<!-- Begin: Total Favorite -->
			<div class="fb_totalfavorite"></div>
			<!-- Finish: Total Favorite --></th>
		</tr>
	</thead>
	<tbody>

<?php foreach ($this->messages as $current=>$message): ?>
		<tr class="fb_sth">
			<th colspan="2" class="view-th fb_sectiontableheader">					
				<a name="<?php echo $message->id; ?>"></a>
				<?php echo JHtml::_('klink.pageAnker', 'atag', $message->id, '#'.$message->id); ?>
			</th>
		</tr>

		<tr>
			<td class="fb-msgview-right">
				<h2><?php echo $this->escape($this->messages[$current]->subject); ?></h2>
				<span class="msgdate" title="<?php echo JHTML::_('date', $this->messages[$current]->time); ?>"><?php echo JHTML::_('date', $this->messages[$current]->time); ?></span>
				<span class="msgkarma"> <strong>Karma:</strong> 0</span>
				<div class="message_text"><?php echo $this->messages[$current]->message; ?></div>
			</td>

			<td class="fb-msgview-left">
<?php echo $this->loadCommonTemplate('profilebox'); ?>
			</td>
		</tr>

		<tr>
			<td class="fb-msgview-right-b">
				<div class="fb_message_buttons">The administrator has disabled public write access.</div>
			</td>

			<td class="fb-msgview-left-b">&nbsp;</td>
		</tr>
<?php endforeach; ?>

	</tbody>
</table>


<!-- B: List Actions Bottom -->
<div class="fb_list_actions_bottom">
	<div class="fb_list_actions_goto">
	<a name="forumbottom"></a> 
	<?php echo JHtml::_('klink.pageAnker', 'atag', 'forumtop', '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/top_arrow.gif" border="0" alt="Go to top" title="Go to top" />'); ?>
	</div>
	<div class="fb_list_actions_forum"></div>
	<div class="fb_list_pages_all">
		<div class="pagination_box">
			<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<div class="discussions">
			<span><?php echo $this->pagination->getResultsCounter(); ?></span>
		</div>
	</div>
</div>
<?php echo $this->loadCommonTemplate('pathway'); ?>
<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->
<div class="fb_list_bottom">
	<span class="fb_list_moderators"><!-- Mod List --> <!-- /Mod List --></span>
<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
	</div>