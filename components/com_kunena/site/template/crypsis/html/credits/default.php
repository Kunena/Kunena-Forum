<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Credits
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="well well-small">
	<h2 class="page-header"><span><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></span></h2>
		<div class="row-fluid column-row">
			<div class="span12 column-item">
				<div> <img src="<?php echo $this->ktemplate->getImagePath('icons/kunena-logo-48-white.png');?>" alt="Kunena" style="margin: 18px;" /> </div>
				<div><?php echo $this->intro; ?></div>
				<div>
					<ul>
						<?php foreach ($this->memberList as $member) : ?>
						<li> <a href="<?php echo $member['url'] ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']) ?></a> - <?php echo $member['title'] ?> </li>
						<?php endforeach ?>
					</ul>
				</div>
				<div> <?php echo $this->thanks ?> </div>
				<div>
					<div> <?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?> <a href="javascript: history.go(-1)" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>"><?php echo JText::_('COM_KUNENA_USER_RETURN_B') ?></a> </div>
				</div>
				<div><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2013 <a href = "http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
			</div>
		</div>
</div>
