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

/** @var KunenaViewTopic $this */
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
	<?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->topic->subject) ?>
</h3>

<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_topictitle'); ?>
<div class="clearfix"></div>

<?php
echo $this->subRequest('Topic/Poll')->set('id', $this->topic->id);
echo $this->subLayout('Page/Module')->set('position', 'kunena_poll');
echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id);
foreach ($this->messages as $id=>$message) {
	echo $this->subRequest('Topic/Item/Message')->set('mesid', $message->id)->set('location', $id);
}
?>

<div class="pull-right">
	<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
</div>
<?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?>
<div class="clearfix"></div>

<?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?>
