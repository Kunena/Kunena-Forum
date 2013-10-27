<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Page
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<h4>
		<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(),
			$this->announcement->displayField('title'), JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST'),
			null, 'follow'); ?>

		<?php if ($this->announcement->showdate) : ?>
		<small>(<?php echo $this->announcement->displayField('created', 'date_today'); ?>)</small>
		<?php endif; ?>

	</h4>
	<h6>
		<?php echo $this->announcement->displayField('sdescription'); ?>

		<?php if (!empty($this->announcement->description)) : ?>
		<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), JText::_('COM_KUNENA_ANN_READMORE'),
				null, 'follow'); ?>
		<?php endif; ?>

	</h6>
</div>
