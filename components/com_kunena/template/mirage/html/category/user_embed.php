<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule topics-posts_list">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category') ?>" method="post">
		<input type="hidden" name="view" value="category" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="kbox-wrapper">
		<div class="topics-posts_list-kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a title="Recent Posts" rel="kposts-detailsbox"><?php echo $this->header ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer box-full">
				<div class="rec-posts posts-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="post-list list-unstyled">
							<li class="header kbox-hover_header-row">
								<dl>
									<!--<dd class="category-icon">
									</dd>-->
									<dd class="post-category">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('Category') ?></span>
										</div>
									</dd>
									<?php if (!empty($this->categoryActions)) : ?>
									<dd><span class="kcheckbox select-toggle"><input class="kcheckall" type="checkbox" name="toggle" value="" /></span></dd>
									<?php endif; ?>
								</dl>
							</li>
						</ul>
					<ul class="list-unstyled post-list">
						<?php if (!count ( $this->categories ) ) : ?>
							<li class="post-row">
								<dl class="list-unstyled">
									<dd class="post-none">
										<div class="innerspacer-column">
											<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE'); ?>
										</div>
									</dd>
								</dl>
							</li>
							<?php else : foreach ($this->categories as $this->category) {
								$this->displayTemplateFile('category', 'user', 'row');
							}

							if ( !empty($this->categoryActions) || !empty($this->embedded) ) : ?>
								<!-- Bulk Actions -->
								<tr class="krow1">
									<td colspan="<?php echo empty($this->categoryActions) ? 5 : 6 ?>" class="kcol-first krowmoderation">
										<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
										<?php if (!empty($this->categoryActions)) : ?>
											<?php echo JHtml::_('select.genericlist', $this->categoryActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
											<input type="submit" name="kcheckgo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
										<?php endif; ?>
									</td>
								</tr>
								<!-- /Bulk Actions -->
							<?php endif;
							endif ?>
					</ul>
				</div>
			</div>

		</div>
	</div>
	</form>
</div>