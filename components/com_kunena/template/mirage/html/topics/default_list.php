<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div class="block">
			<div class="headerbox-wrapper">
				<div class="header">
					<h2 class="header"><a class="section link-header2" rel="topic-detailsbox"><?php echo $this->headerText ?></a>(<strong><?php echo intval($this->total) ?></strong>
					<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>)</h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="topic detailsbox box-full box-border box-border_radius box-shadow" id="topic-detailsbox">
					<ul class="topic-list">
						<li class="header box-hover_header-row clear">
							<dl>
								<dd class="topic-icon">
								</dd>
								<dd class="topic-subject">
									<span class="bold"><?php echo JText::_('Subject') ?></span>
								</dd>
								<dd class="topic-replies">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
								</dd>
								<dd class="topic-views">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
								</dd>
								<dd class="topic-lastpost">
									<spanclass="bold" ><?php echo JText::_('Last Post') ?></span>
								</dd>
								<?php if ($this->topicActions) : ?>
									<dd class="topic-checkbox">
										<span><input id="kcheckbox-all" type="checkbox" value="0" name="" class="moderate-topic-checkall" /></span>
									</dd>
								<?php endif; ?>
							</dl>
						</li>
					</ul>
					<ul class="topic-list">
						<?php if (empty($this->topics )) : ?>
							<li class="topic-row">
								<dl>
									<dd>
										<?php echo JText::_('COM_KUNENA_VIEW_RECENT_NO_TOPICS'); ?>
									</dd>
								</dl>
							</li>
						<?php else : $this->displayRows(); endif ?>
					</ul>
				</div>
			</div>
		</div>
		<?php if ($this->topicActions) : ?>
			<div class="modbox-wrapper">
				<div class="modbox">
					<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="inputbox" size="1"', 'value', 'text', 0, 'kmoderate-select');
					$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
					echo JHTML::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox" size="1" style="display:none;"', 'value', 'text', 0, 'kcategorytarget'); ?>
					<input name="submit" class="button" type="submit" value="<?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?>"/>
				</div>
			</div>
		<?php endif ?>
	</div>
	<div class="spacer"></div>
	<input type="hidden" name="view" value="topics" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>