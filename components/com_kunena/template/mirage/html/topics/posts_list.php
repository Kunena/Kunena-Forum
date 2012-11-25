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
?>
<div class="kmodule topics-posts_list">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics') ?>" method="post">
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="kbox-wrapper">
		<div class="topics-posts_list-kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a title="Recent Posts" rel="kposts-detailsbox"><?php echo $this->headerText ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer box-full">
				<div class="rec-posts posts-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="post-list list-unstyled">
							<li class="header kbox-hover_header-row">
								<dl>
									<!--<dd class="category-icon">
									</dd>-->
									<dd class="post-topic">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_TOPICS') ?></span>
										</div>
									</dd>
									<dd class="post-category">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('Category') ?></span>
										</div>
									</dd>
									<?php if ($this->postActions) : ?>
										<dd class="post-checkbox">
											<div class="innerspacer-header">
												<input type="checkbox" value="0" name="" class="kcheckall kmoderate-topic-checkall" />
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</li>
						</ul>
					<ul class="post-list list-unstyled list-row">
						<?php if (empty($this->messages )) : ?>
							<li class="post-row">
								<dl class="list-unstyled">
									<dd class="post-none">
										<div class="innerspacer-column">
											<?php echo JText::_('COM_KUNENA_NO_POSTS'); ?>
										</div>
									</dd>
								</dl>
							</li>
						<?php else : $this->displayRows(); endif ?>
					</ul>
				</div>
			</div>
			<?php if ($this->postActions) : ?>
				<div class="modbox-wrapper innerspacer-bottom">
					<div class="modbox">
						<button class="kbutton button-type-mod fr" type="submit"><span><?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?></span></button>
						<?php echo JHtml::_('select.genericlist', $this->postActions, 'task', 'class="kinputbox form-horizontal fr" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	</form>
</div>
