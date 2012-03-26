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
	<div class="kbox-wrapper">
		<div class="topics-posts_list-kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a title="Recent Posts" rel="kposts-detailsbox"><?php echo $this->headerText ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer box-full">
				<div class="rec-posts posts-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled post-list">
						<?php if (empty($this->messages )) : ?>
							<li class="post-row">
								<dl class="list-unstyled">
									<dd class="post-none">
										<?php echo JText::_('COM_KUNENA_NO_POSTS'); ?>
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
						<button class="kbutton button-type-standard fr" type="submit"><span><?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?></span></button>
						<?php echo JHTML::_('select.genericlist', $this->postActions, 'task', 'class="kinputbox form-horizontal fr" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
						<!--<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />-->
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="spacer"></div>