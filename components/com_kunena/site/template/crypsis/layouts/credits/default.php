<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Credits
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<h2>
	<?php echo JText::_('COM_KUNENA').' - '.JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?>
</h2>

<div class="well well-small" id="credits">
	<div class="container-fluid pull-left">
		<img src="<?php echo $this->logo; ?>" alt="Kunena" />
	</div>
	<p class="intro">
		<?php echo $this->intro; ?>
	</p>
	<div class="clearfix"></div>

	<div class="credits">

		<dl class="dl-horizontal">
			<?php foreach($this->memberList as $member) : ?>
			<dt>
				<a href="<?php echo $member['url']; ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']); ?></a>
			</dt>
			<dd>
				<?php echo $member['title']; ?>
			</dd>
			<hr class="hr-condensed">
			<?php endforeach ?>
		</dl>

		<p>
			<?php echo $this->thanks; ?>
		</p>

		<p>
			<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK'); ?>
			<a href="javascript:window.history.back()" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK'); ?>">
				<?php echo JText::_('COM_KUNENA_USER_RETURN_B'); ?>
			</a>
		</p>

		<p class="center">
			<?php echo JText::_('COM_KUNENA_COPYRIGHT'); ?> &copy; 2008 - 2016 <a href = "http://www.kunena.org" target = "_blank">Kunena</a>,
			<?php echo JText::_('COM_KUNENA_LICENSE'); ?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a>
		</p>
	</div>
</div>
