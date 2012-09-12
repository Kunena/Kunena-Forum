<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->topic_interval = 0;
$this->topic_rowclass = array ("even", "odd" );
?>
<div class="kmodule topics_default_list">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics') ?>" method="post">
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="kbox-wrapper kbox-full">
			<div class="topics-default_list-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
				<div class="headerbox-wrapper kbox-full">
					<div class="header">
						<h2 class="header"><a class="section link-header2" rel="topic-detailsbox"><?php echo $this->headerText ?></a> (<strong><?php echo intval($this->total) ?></strong>
						<?php echo JText::_('COM_KUNENA_TOPICS')?>)</h2>
					</div>
				</div>
				<?php if ($this->getPagination(7)) : ?>
					<div class="innerbox-wrapper innerspacer-top">
						<div class="topics_default_list-kpagination kpagination">
							<?php echo $this->getPagination(7) ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="detailsbox-wrapper innerspacer kbox-full">
					<div class="topics-default_list-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
						<ul class="topic-list list-unstyled list-row">
							<li class="header kbox-hover_header-row kbox-full item-row">
								<dl class="list-unstyled list-column">
									<!--<dd class="topic-icon">
									</dd>-->
									<dd class="topic-subject item-column">
										<div class="innerspacer-header">
											<?php //FIXME: Translate ?>
											<span class="bold"><?php echo JText::_('Subject') ?></span>
										</div>
									</dd>
									<dd class="topic-replies item-column">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
										</div>
									</dd>
									<dd class="topic-views item-column">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
										</div>
									</dd>
									<dd class="topic-lastpost item-column">
										<div class="innerspacer-header">
											<?php //FIXME: Translate ?>
											<span class="bold"><?php echo JText::_('Last Post') ?></span>
										</div>
									</dd>
									<?php if ($this->topicActions) : ?>
										<dd class="topic-checkbox item-column">
											<div class="innerspacer-header">
												<span><input id="kcheckbox-all" type="checkbox" value="" name="toggle" class="kcheckall" /></span>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</li>
						</ul>
						<ul class="topic-list list-unstyled">
							<?php if (empty($this->topics )) : ?>
								<li class="topic-row">
									<dl class="list-unstyled">
										<dd class="topic-none">
											<div class="innerspacer-column">
												<?php echo JText::_('COM_KUNENA_VIEW_RECENT_NO_TOPICS'); ?>
											</div>
										</dd>
									</dl>
								</li>
							<?php else : $this->displayRows(); endif ?>
						</ul>
					</div>
				</div>
				<?php if ($this->topicActions) : ?>
					<div class="modbox-wrapper innerspacer-bottom">
						<div class="modbox">
							<button class="kbutton button-type-mod fr" type="submit"><span><?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?></span></button>
							<?php echo JHtml::_('select.genericlist', $this->topicActions, 'task', 'class="form-horizontal fr" size="1"', 'value', 'text', 0, 'kmoderate-select');
							$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
							echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="form-horizontal" size="1" style="display:none;"', 'value', 'text', 0, 'kcategorytarget'); ?>
						</div>
					</div>
				<?php endif ?>
			</div>
		</div>
	</form>
</div>

