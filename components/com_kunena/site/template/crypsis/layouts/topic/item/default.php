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

$pagination = $this->getPaginationObject(5);

$this->document->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');
?>
<div class="row-fluid">
	<div class="pull-right">
		<?php echo KunenaLayout::factory('Pagination/List')->set('pagination', $pagination); ?>
	</div>
	<h3><?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->topic->subject) ?></h3>
	<?php echo KunenaLayout::factory('Page/Module')->set('position', 'kunena_topictitle'); ?>
</div>
<div class="clearfix"></div>

<?php
echo KunenaRequest::factory('Topic/Poll/Display')->set('id', $this->topic->id);
echo KunenaLayout::factory('Page/Module')->set('position', 'kunena_poll');
echo KunenaRequest::factory('Topic/Actions/Display')->set('id', $this->topic->id);
foreach ($this->messages as $id=>$message) {
	$this->displayMessage($id, $message);
}
?>

<div class="row-fluid">
	<div class="pull-right">
		<?php echo KunenaLayout::factory('Pagination/List')->set('pagination', $pagination); ?>
	</div>
	<?php echo KunenaRequest::factory('Topic/Actions/Display')->set('id', $this->topic->id); ?>
</div>
<div class="clearfix"></div>

<?php echo KunenaLayout::factory('Category/Moderators')->set('moderators', $this->moderators); ?>
