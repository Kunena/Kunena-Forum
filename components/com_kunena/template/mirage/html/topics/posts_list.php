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
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><a title="Recent Posts" rel="kposts-detailsbox"><?php echo $this->headerText ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="rec-posts posts-detailsbox detailsbox box-full box-border box-border_radius box-shadow">
					<ul class="post-list">
						<?php if (empty($this->messages )) : ?>
							<li class="post-row">
								<dl>
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
				<div class="modbox-wrapper">
					<div class="modbox">
						<?php echo JHTML::_('select.genericlist', $this->postActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
						<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="spacer"></div>