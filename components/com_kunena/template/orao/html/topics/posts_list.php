<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->headerText ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

			<div class="kdetailsbox krec-posts" id="kposts-detailsbox">
				<ul class="kposts">
					<?php if (empty($this->messages )) : ?>
					<li class="row tk-nopost-info" style="padding:5px !important;">
						<span><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></span>
					</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
			<div class="clr"></div>
				<?php if ($this->postActions) : ?>
				<ul class="topiclist forums">
				<li class="tk-modbox">
					<?php echo JText::_('COM_KUNENA_TEMPLATE_SELECT_ALL'); ?>
					<input id="kcheckall" type="checkbox" value="0" name="toggle" class="tk-kmoderate" />
					<?php echo JHTML::_('select.genericlist', $this->postActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
					<input type="submit" name="" class="tk-go-button" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
				</li>
				</ul>
				<?php endif ?>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>