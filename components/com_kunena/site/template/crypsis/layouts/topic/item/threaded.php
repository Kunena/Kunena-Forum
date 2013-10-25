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
?>
<?php
$this->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');
?>
<?php if ($this->category->headerdesc) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->category->displayField('headerdesc'); ?>
</div>
<?php endif; ?>

<div class="pull-right">
	<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
</div>

<h3>
	<?php echo $this->topic->getIcon(); ?>
	<?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->topic->displayField('subject') ?>
</h3>

<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_topictitle'); ?>
<div class="clearfix"></div>

<?php
echo $this->subRequest('Topic/Poll')->set('id', $this->topic->id);
echo $this->subLayout('Page/Module')->set('position', 'kunena_poll');
echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id);
echo $this->subRequest('Topic/Item/Message')->set('mesid', $this->message->id)->set('location', $this->message->replynum);
?>

<h3>
	<?php echo JText::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->escape($this->topic->subject)) ?>
</h3>

<table class="table table-striped table-bordered table-hover">
<?php foreach ($this->messages as $id=>$message) {
	echo $this->subLayout('Topic/Item/Message')
		->set('message', $message)
		->set('selected', $this->message->id)
		->setLayout('row');
}
?>
</table>

<div class="pull-right">
	<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
</div>
<?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?>
<div class="clearfix"></div>

<?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?>
