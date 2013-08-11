<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule category-user">
	<div class="kbox-wrapper kbox-full">
		<div class="category-user-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a title="<?php echo $this->header ?>" rel="kposts-detailsbox"><?php echo $this->header ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="kdetailsbox krec-posts" id="kposts-detailsbox">
					<ul class="list-unstyled topic-list">
						<li class="header kbox-hover_header-row kbox-full">
							<dl class="list-unstyled">
								<dd class="subscription-icon">
								</dd>
								<dd class="subscription-subject">
									<span class="bold"><?php echo JText::_('Subject') ?></span>
								</dd>
								<dd class="subscription-replies">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
								</dd>
								<dd class="subscription-views">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
								</dd>
								<dd class="subscription-lastpost">
									<span class="bold"><?php echo JText::_('Last Post') ?></span>
								</dd>
								<?php if ($this->topicActions) : ?>
									<dd class="topic-checkbox">
										<input type="checkbox" value="0" name="" class="kcheckall moderate-topic-checkall" />
									</dd>
								<?php endif ?>
							</dl>
						</li>
					</ul>
					<ul class="list-unstyled subscription-list">
						<?php if (empty($this->categories )) : ?>
							<li class="topics-row kbox-hover kbox-hover_list-row">
								<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE'); ?>
							</li>
						<?php
						else :
							foreach ($this->categories as $this->category) {
								$this->displayTemplateFile('category', 'user', 'row');
							}
						endif
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

