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

<div class="forum-description">
<?php echo $this->description; ?>
</div>

<!-- B: Actions Box -->
<div class="top_actions_box">
	<div class="jump">
		<a name="forumtop"></a>
		<?php echo JHtml::_('klink.pageAnker', 'atag', 'forumbottom', '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/bottom_arrow.gif" border="0" alt="Go to bottom" title="Go to bottom" /></a>'); ?>
	</div>
	<div class="actions"></div>
	<div class="counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<div class="pagination"><?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?></div>
</div>
<!-- F: Actions Box -->

<table class="forum_body">
	<thead>
		<tr>
			<th colspan="2">
			<h1>TOPIC: <?php echo $this->escape($this->messages[0]->subject); ?></h1>
			<!-- Begin: Total Favorite -->
			<div class="favorites"></div>
			<!-- Finish: Total Favorite --></th>
		</tr>
	</thead>
	<tbody>

<?php foreach ($this->messages as $current=>$message): ?>
		<tr>
			<th class="fcol msgheader" colspan="2">					
				<a name="<?php echo $message->id; ?>"></a>
				<?php echo JHtml::_('klink.pageAnker', 'atag', $message->id, '#'.$message->id); ?>
			</th>
		</tr>

		<tr>
			<td class="lcol message">
				<div class="message_header">
					<h2 class="msgsubject"><?php echo $this->escape($this->messages[$current]->subject); ?></h2>
					<span class="msgdate" title="<?php echo JHTML::_('date', $this->messages[$current]->time); ?>"><?php echo JHTML::_('date', $this->messages[$current]->time); ?></span>
					<span class="msgkarma"> <strong>Karma:</strong> 0</span>
				</div>
				<div class="message_text"><?php echo $this->messages[$current]->message; ?></div>
			</td>

			<td class="rcol profile" rowspan="2">
<?php 
$this->profile =& $message;
echo $this->loadCommonTemplate('profilebox'); 
?>
			</td>
		</tr>

		<tr>
			<td class="lcol message">
				<div class="fb_message_buttons">The administrator has disabled public write access.</div>
			</td>
		</tr>
<?php endforeach; ?>

	</tbody>
</table>


<!-- B: Actions Box -->
<div class="bottom_actions_box">
	<div class="jump">
		<a name="forumtop"></a>
		<?php echo JHtml::_('klink.pageAnker', 'atag', 'forumbottom', '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/bottom_arrow.gif" border="0" alt="Go to bottom" title="Go to bottom" /></a>'); ?>
	</div>
	<div class="actions"></div>
	<div class="counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<div class="pagination"><?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?></div>
</div>
<!-- F: Actions Box -->

<?php echo $this->loadCommonTemplate('pathway'); ?>
<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->
<div class="bottom_info_box">
	<div class="moderators">Moderators:</div>
	<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
	</div>