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
		<div class="ksection">
			<?php if (!empty($this->rssURL)) : ?>
			<a href="<?php echo $this->rssURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_RSS_TITLE', $this->headerText) ?>"><?php echo $this->getIcon('krss-icon') ?></a>
			<?php endif ?>
			<a href="#" class="ksection-headericon"><?php echo $this->getImage('icon-section.png') ?></a>
			<h2 class="kheader"><a rel="klatest-detailsbox"><?php echo $this->headerText ?></a></h2>
			<div class="kdetailsbox" id="klatest-detailsbox">
				<ul class="klatest">
					<?php if (empty($this->topics )) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo JText::_('COM_KUNENA_VIEW_RECENT_NO_TOPICS'); ?>
					</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>
		<?php if ($this->topicActions) : ?>
		<div id="ksection-modbox">
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