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
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="block">
			<div class="headerbox-wrapper">
				<div class="header">
					<h2 class="header"><a rel="topic-detailsbox"><?php echo $this->headerText ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="topic detailsbox" id="topic-detailsbox">
					<ul class="topic-list">
						<li class="header">
							<dl>
								<dd class="topic-icon">
								</dd>
								<dd class="topic-subject">
									<span><?php echo JText::_('Subject') ?></span>
								</dd>
								<dd class="topic-replies">
									<span><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
								</dd>
								<dd class="topic-views">
									<span><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
								</dd>
								<dd class="topic-lastpost">
									<span><?php echo JText::_('Last Post') ?></span>
								</dd>
							</dl>
						</li>
					</ul>
					<ul class="topic-list">
						<?php if (empty($this->topics )) : ?>
						<li class="topic-row">
							<?php echo JText::_('COM_KUNENA_VIEW_RECENT_NO_TOPICS'); ?>
						</li>
						<?php else : $this->displayRows(); endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="spacer"></div>
		<?php if ($this->topicActions) : ?>
		<div id="section-modbox">
			<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select');
			$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
			echo JHTML::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="kinputbox" size="1" style="display:none;"', 'value', 'text', 0, 'kcategorytarget'); ?>
			<input id="kchecbox-all" type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
			<input name="submit" class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?>"/>
		</div>
		<?php endif ?>
		<input type="hidden" name="view" value="topics" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>