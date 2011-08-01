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
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon flat">
						<dt><span class="ktitle"><?php echo $this->headerText ?></span></dt>
						<?php if ($this->params->get('countcolumnShow') != 0):?>
							<dd class="topics"><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?></dd>
							<dd class="posts"><?php echo JText::_('COM_KUNENA_GEN_HITS');?></dd>
						<?php endif;?>
							<dd class="lastpost"><span><?php echo JText::_('COM_KUNENA_GEN_LAST_POST');?></span></dd>
					</dl>
				</li>
			</ul>
			<?php if ($this->category->headerdesc) : ?>
			<div class="tksection-desc">
				<?php echo $this->parse($this->category->headerdesc) ?>
			</div>
			<?php endif ?>
				<ul class="topiclist forums">
					<?php if (empty($this->topics) && empty($this->subcategories)) : ?>

					<li class="row tk-nopost-info">
						<span><?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS'); ?></span>
					</li>
					<?php else : $this->displayRows(); endif ?>
					<?php if ($this->topicActions) : ?>
					<li class="tk-modbox">
						<?php echo JText::_('COM_KUNENA_TEMPLATE_SELECT_ALL'); ?>
						<input type="checkbox" value="0" name="" class="tk-kmoderate" />
						<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
						<input type="submit" name="" class="tk-go-button" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					</li>
					<?php endif ?>
				</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>