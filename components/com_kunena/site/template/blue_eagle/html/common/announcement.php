<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kannouncement">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kannouncement"></a></span>
		<h2><?php echo JHtml::_('kunenaforum.link', $this->annListUrl, $this->announcement->displayField('title'), JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST'), null, 'follow'); ?></h2>
	</div>
	<div class="kcontainer" id="kannouncement">
		<div class="kbody">
			<div class="kanndesc">
				<?php if ($this->showdate) : ?>
				<div class="anncreated"><?php echo $this->announcement->displayField('created', 'date_today') ?></div>
				<?php endif; ?>
				<div class="anndesc">
					<?php echo $this->announcement->displayField('sdescription') ?>
					<?php if (!empty($this->announcement->description)) : ?>
					...<br /><?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), JText::_('COM_KUNENA_ANN_READMORE'), null, 'follow'); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
