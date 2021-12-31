<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if ($this->config->enableforumjump)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
}

$mmm    = 0;
$config = $this->ktemplate->params;

if ($config->get('socialshare') == 1)
{
    echo "<div>" . $this->subLayout('Widget/Social')->set('me', $this->me)->set('ktemplate', $this->ktemplate) . "</div>";
}

if ($config->get('socialshare') == 2)
{
	echo "<div>" . $this->subLayout('Widget/Socialcustomtag') . "</div>";
}

if ($config->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_index_top');
}

foreach ($this->sections as $section) :
	$markReadUrl = $section->getMarkReadUrl();

	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_section_top_' . ++$mmm);
	}
	?>
	<div class="kfrontend">
		<h2 class="btn-toolbar pull-right">
			<!--			<?php /*if ($this->me->isAdmin()) :*/
			?>
				<a class="btn btn-small" href="<?php /*echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage'); */
			?>"><?php /*echo Text::_('COM_KUNENA_MENU_CATEGORY_MANAGE'); */
			?></a>
			--><?php /*endif; */
			?>

			<?php if (count($this->sections) > 1) : ?>
				<div class="btn btn-small" data-toggle="collapse"
				     data-target="#section<?php echo $section->id; ?>"></div>
			<?php endif; ?>
		</h2>

		<h1>
			<?php echo $this->getCategoryLink($section, $this->escape($section->name), null, KunenaTemplate::getInstance()->tooltips(), true, false); ?>
			<small class="hidden-phone nowrap" id="ksection-count<?php echo $section->id; ?>">
				<?php echo KunenaForumCategory::getInstance()->totalCount($section->getTopics()); ?>
			</small>
		</h1>

		<div class="row-fluid section <?php if (!empty($section->class)) : ?>section<?php echo $this->escape($section->class_sfx); ?><?php endif; ?> in collapse"
		     id="section<?php echo $section->id; ?>">
			<table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?>">
				<?php if (!empty($section->description)) : ?>
					<thead class="hidden-phone">
					<tr>
						<td colspan="3">
							<div class="header-desc"><?php echo $section->displayField('description'); ?></div>
						</td>
					</tr>
					</thead>
				<?php endif; ?>

				<?php if ($section->isSection() && empty($this->categories[$section->id]) && empty($this->more[$section->id])) : ?>
					<tr>
						<td>
							<h4>
								<?php echo Text::_('COM_KUNENA_GEN_NOFORUMS'); ?>
							</h4>
						</td>
					</tr>
				<?php else : ?>
					<?php if (!empty($this->categories[$section->id])) : ?>
						<tr>
							<td colspan="2" class="hidden-phone">
								<div class="header-desc"><?php echo Text::_('COM_KUNENA_GEN_CATEGORY'); ?></div>
							</td>
							<td colspan="1" class="hidden-phone post-info">
								<?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					foreach ($this->categories[$section->id] as $category) : ?>
						<tr class="category<?php echo $this->escape($category->class_sfx); ?>"
						    id="category<?php echo $category->id; ?>">
							<td class="span1 center hidden-phone">
								<?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), '', null, true, false); ?>
							</td>
							<td class="span8">
								<div>
									<h3>
										<?php echo $this->getCategoryLink($category, $category->name, null, KunenaTemplate::getInstance()->tooltips(), true, false); ?>
										<small class="nowrap" id="kcategory-count<?php echo $category->id; ?>">
											<span id="kcatcount<?php echo $category->id; ?>"><?php echo KunenaForumCategory::getInstance()->totalCount($category->getTopics()); ?></span>
											<span>
												<?php if (($new = $category->getNewCount()) > 0) : ?>
													<sup class="knewchar"> (<?php echo $new . ' ' . Text::_('COM_KUNENA_A_GEN_NEWCHAR'); ?>
														)</sup>
												<?php endif; ?>
												<?php if ($category->locked) : ?>
													<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
															title="<?php echo Text::_('COM_KUNENA_LOCKED_CATEGORY') ?>"><?php echo KunenaIcons::lock(); ?></span>
												<?php endif; ?>
												<?php if ($category->review) : ?>
													<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
															title="<?php echo Text::_('COM_KUNENA_GEN_MODERATED') ?>"><?php echo KunenaIcons::shield(); ?></span>
												<?php endif; ?>

												<?php if (KunenaFactory::getConfig()->enablerss) : ?>
													<a href="<?php echo $this->getCategoryRSSURL($category->id); ?>"
													   rel="alternate" type="application/rss+xml">
														 <?php echo KunenaIcons::rss(); ?>
													</a>
												<?php endif; ?>
											</span>
										</small>
									</h3>
								</div>

								<?php if (!empty($category->description)) : ?>
									<div class="hidden-phone header-desc"><?php echo $category->displayField('description'); ?></div>
								<?php endif; ?>

								<?php
								// Display subcategories
								if (!empty($this->categories[$category->id])) : ?>
									<div class="subcategories">
										<ul class="inline">

											<?php foreach ($this->categories[$category->id] as $subcategory_index=>$subcategory) : ?>
												<li>
													<?php $totaltopics = KunenaForumCategory::getInstance()->totalCount($subcategory->getTopics()); ?>

													<?php if (KunenaConfig::getInstance()->showchildcaticon) : ?>
														<?php echo $this->getCategoryLink($subcategory, $this->getSmallCategoryIcon($subcategory), '', null, true, false) . $this->getCategoryLink($subcategory, '', null, KunenaTemplate::getInstance()->tooltips(), true, false) . '<small class="hidden-phone muted" id="ksubcategory-count' . $subcategory_index . '"> ('
															. $totaltopics . ')</small>';
													else : ?>
														<?php echo $this->getCategoryLink($subcategory, '', null, KunenaTemplate::getInstance()->tooltips(), true, false) . '<small class="hidden-phone muted" id="ksubcategory-count' . $subcategory_index . '"> ('
															. $totaltopics . ')</small>';
													endif;

													if (($new = $subcategory->getNewCount()) > 0)
													{
														echo '<sup class="knewchar">(' . $new . ' ' . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>';
													}
													?>
												</li>
											<?php endforeach; ?>

											<?php if (!empty($this->more[$category->id])) : ?>
												<li>
													<?php echo $this->getCategoryLink($category, Text::_('COM_KUNENA_SEE_MORE'), null, KunenaTemplate::getInstance()->tooltips(), true, false); ?>
													<small class="hidden-phone muted">
														(<?php echo Text::sprintf('COM_KUNENA_X_HIDDEN', (int) $this->more[$category->id]); ?>
														)
													</small>
												</li>
											<?php endif; ?>

										</ul>
									</div>
									<div class="clearfix"></div>
								<?php endif; ?>

								<?php if ($category->getmoderators() && KunenaConfig::getInstance()->listcat_show_moderators) : ?>
									<br/>
									<div class="moderators">
										<?php
										// get the Moderator list for display
										$modslist = array();
										foreach ($category->getmoderators() as $moderator)
										{
											$modslist[] = KunenaFactory::getUser($moderator)->getLink(null, null, '', null, KunenaTemplate::getInstance()->tooltips());
										}

										echo Text::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
										?>
									</div>
								<?php endif; ?>

								<?php if (!empty($this->pending[$category->id])) : ?>
									<div class="alert" style="margin-top:20px;">
										<a class="alert-link <?php echo KunenaTemplate::getInstance()->tooltips(); ?>"
										   href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=posts&mode=unapproved&userid=0&catid=' . intval($category->id)); ?>"
										   title="<?php echo Text::_('COM_KUNENA_SHOWCAT_PENDING') ?>"
										   rel="nofollow"><?php echo intval($this->pending[$category->id]) . ' ' . Text::_('COM_KUNENA_SHOWCAT_PENDING') ?></a>
									</div>
								<?php endif; ?>
							</td>

							<?php $last = $category->getLastTopic(); ?>

							<?php if ($last->exists()) :
								$author = $last->getLastPostAuthor();
								$time = $last->getLastPostTime();
								$avatar = $this->config->avataroncat ? $author->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb') : null;
								?>

								<td class="span3 hidden-phone">
									<div class="container-fluid">
										<div class="row-fluid">
											<?php if ($avatar) : ?>
											<div class="span3" id="kavatar-index<?php echo $category->id; ?>">
												<?php echo $author->getLink($avatar, null, '', '', KunenaTemplate::getInstance()->tooltips(), $category->id, KunenaConfig::getInstance()->avataredit); ?>
											</div>
											<div class="span9" id="kpost-index<?php echo $category->id; ?>">
												<?php else : ?>
												<div class="span12">
													<?php endif; ?>
													<span class="lastpostlink"><?php echo $this->getLastPostLink($category, null, null, KunenaTemplate::getInstance()->tooltips(), 30, false, true) ?></span>
													<br>
													<span class="lastpostby"><?php echo Text::sprintf('COM_KUNENA_BY_X', $author->getLink(null, null, '', '', KunenaTemplate::getInstance()->tooltips(), $category->id)); ?></span>
													<br>
													<span class="datepost"><?php echo $time->toKunena('config_post_dateformat'); ?></span>
												</div>
											</div>
										</div>
								</td>
							<?php else : ?>
								<td class="span3 hidden-phone">
									<div class="last-post-message">
										<?php echo Text::_('COM_KUNENA_X_TOPICS_0'); ?>
									</div>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if (!empty($this->more[$section->id])) : ?>
					<tr>
						<td colspan="3">
							<h4>
								<?php echo $this->getCategoryLink($section, Text::sprintf('COM_KUNENA_SEE_ALL_SUBJECTS')); ?>
								<small>
									(<?php echo Text::sprintf('COM_KUNENA_X_HIDDEN', (int) $this->more[$section->id]); ?>
									)
								</small>
							</h4>
						</td>
					</tr>
				<?php endif; ?>

			</table>
		</div>
	</div>
	<!-- Begin: Category Module Position -->
	<?php
	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_section_' . ++$mmm);
	} ?>
	<!-- Finish: Category Module Position -->
<?php endforeach;

if ($config->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_index_bottom');
}
