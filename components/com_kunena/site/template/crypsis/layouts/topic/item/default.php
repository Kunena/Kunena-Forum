<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

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

<h3>
	<?php echo $this->topic->getIcon(); ?>
	<?php echo $this->topic->displayField('subject'); ?>
</h3>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true); ?>
</div>
<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('id', $this->topic->id)
		->set('title', JText::_('COM_KUNENA_SEARCH_TOPIC')); ?>
</div>

<div class="clearfix"></div>

<?php
echo $this->subLayout('Widget/Module')->set('position', 'kunena_topictitle');
echo $this->subRequest('Topic/Poll')->set('id', $this->topic->id);
echo $this->subLayout('Widget/Module')->set('position', 'kunena_poll');
if($this->me->exists) echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id);

foreach ($this->messages as $id => $message)
{
	echo $this->subRequest('Topic/Item/Message')
		->set('mesid', $message->id)
		->set('location', $id);
}
?>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true);; ?>
</div>
<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('id', $this->topic->id)
		->set('title', JText::_('COM_KUNENA_SEARCH_TOPIC')); ?>
</div>

<?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?>
<div class="clearfix"></div>

<?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?>
