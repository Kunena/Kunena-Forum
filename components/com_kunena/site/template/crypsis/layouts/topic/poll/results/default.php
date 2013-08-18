<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$row = 0;
?>

<div class="row-fluid">
	<a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a>
	<h2><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaHtmlParser::parseText ($this->poll->title); ?></h2>
</div>

<table class="table table-striped">
	<?php foreach ( $this->poll->getOptions() as $option ) : ?>
	<tr>
		<td><?php echo KunenaHtmlParser::parseText ($option->text); ?></td>
		<td class="span8">
			<div class="progress progress-striped">
				<div class="bar" style="height:30px;width: <?php echo intval(($option->votes*100)/max($this->poll->getTotal(),1)); ?>%;"></div>
			</div>
		</td>
		<td>
			<?php if(isset($option->votes) && ($option->votes > 0)) { echo $option->votes; } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?>
		</td>
		<td><?php echo round(($option->votes*100)/max($this->poll->getTotal(),1),1)."%"; ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="row-fluid">
	<?php
	echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')." <b>".$this->usercount."</b> ";
	if ( !empty($this->users_voted_list)) echo " ( ".implode(', ', $this->users_voted_list)." ) "; ?>
	<?php if ( $this->usercount > '5' ) : ?>
		<a href="#" id="kpoll-moreusers"><?php echo JText::_('COM_KUNENA_POLLUSERS_MORE')?></a>
		<div style="display: none;" id="kpoll-moreusers-div"><?php echo implode(', ', $this->users_voted_morelist); ?></div>
	<?php endif; ?>
</div>

<div class="row-fluid">
	<?php if (!$this->me->exists()) : ?>
		<?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?>
	<?php elseif ($this->voted && !$this->config->pollallowvoteone) : ?>
		<a href="<?php echo $this->getPollURL('vote', $this->topic->id, $this->category->id);?>"> <?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?> </a>
	<?php endif; ?>
	<?php if( $this->me->isModerator($this->topic->getCategory()) ) : ?>
		<a href="<?php echo KunenaRoute::_("index.php?option=com_kunena&view=topic&id={$this->topic->id}&catid={$this->category->id}&pollid={$this->poll->id}&task=resetvotes&".JSession::getFormToken() .'=1') ?>"><?php echo JText::_('COM_KUNENA_TOPIC_VOTE_RESET'); ?></a>
	<?php endif; ?>
</div>
