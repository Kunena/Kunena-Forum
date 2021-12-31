<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

/** @var KunenaForumCategory $section */
/** @var KunenaForumCategory $category */
/** @var KunenaForumCategory $subcategory */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if ($this->config->enableforumjump)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
}

$mmm        = 0;
$config     = $this->ktemplate->params;

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
	$Itemid = KunenaRoute::getCategoryItemid($section);

	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_section_top_' . ++$mmm);
	} ?>
	<div class="kfrontend shadow-lg rounded border section">
		<h2 class="btn-toolbar float-right">
			<?php if (count($this->sections) > 0) : ?>
				<?php //if ($this->me->isAdmin()) : ?>
					<!--  <a class="btn btn-outline-primary btn-sm"
					   href="<?php //echo Route::_('index.php?option=com_kunena&view=category&catid=' . (int) $section->id . '&layout=manage&Itemid=' . $Itemid); ?>"><?php //echo KunenaIcons::pencil(); ?></a>-->
				<?php //endif; ?>
				<button class="btn btn-outline-primary btn-sm" type="button"
				        data-toggle="collapse"
				        data-target="#section<?php echo $section->id; ?>" aria-expanded="false"
				        aria-controls="section<?php echo $section->id; ?>"><?php echo KunenaIcons::collapse(); ?></button>
			<?php endif; ?>
		</h2>

		<h1 class="card-header">
			<?php echo $this->getCategoryLink($section, $this->escape($section->name), null, KunenaTemplate::getInstance()->tooltips(), true, false); ?>
			<small class="hidden-xs-down nowrap">
				<?php echo KunenaForumCategory::getInstance()->totalCount($section->getTopics()); ?>
			</small>
		</h1>

		<div class="<?php if (!empty($section->class)) : ?>section<?php echo $this->escape($section->class_sfx); ?><?php endif; ?> collapse show shadow-lg rounded"
		     id="section<?php echo $section->id; ?>">
			<table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?> table-responsive w-100 d-block d-md-table">
				<?php if (!empty($section->description)) : ?>
					<thead>
					<tr>
						<td colspan="12">
							<div class="bg-faded p-2"><?php echo $section->displayField('description'); ?></div>
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
							<td colspan="1">
								<div class="p-2"></div>
							</td>
							<td colspan="7">
								<div class="p-2"><?php echo Text::_('COM_KUNENA_GEN_CATEGORY'); ?></div>
							</td>
							<td colspan="4">
								<div class="p-2"><?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?></div>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					foreach ($this->categories[$section->id] as $category) : ?>
						<tr class="category<?php echo $this->escape($category->class_sfx); ?>"
						    id="category<?php echo $category->id; ?>">
							<td colspan="1" id="kcat-icon">
								<?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), '', null, true, false); ?>
							</td>
							<td colspan="6">
								<div>
									<h3>
										<?php echo $this->getCategoryLink($category, $category->name, null, KunenaTemplate::getInstance()->tooltips(), true, false); ?>
										<small class="nowrap">
											<span id="kcatcount"><?php echo KunenaForumCategory::getInstance()->totalCount($category->getTopics()); ?></span>
											<span>
												<?php if (($new = $category->getNewCount()) > 0) : ?>
													<sup class="knewchar"> (<?php echo $new . Text::_('COM_KUNENA_A_GEN_NEWCHAR') ?>
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
									<div class="hidden-xs-down header-desc"><?php echo $category->displayField('description'); ?></div>
								<?php endif; ?>

								<?php
								// Display subcategories
								if (!empty($this->categories[$category->id])) : ?>
									<div>
										<ul class="list-inline">

											<?php foreach ($this->categories[$category->id] as $subcategory) : ?>
												<li>
													<?php $totaltopics = KunenaForumCategory::getInstance()->totalCount($subcategory->getTopics()); ?>

													<?php if (KunenaConfig::getInstance()->showchildcaticon) : ?>
														<?php echo $this->getCategoryLink($subcategory, $this->getSmallCategoryIcon($subcategory), '', null, true, false) . $this->getCategoryLink($subcategory, '', null, KunenaTemplate::getInstance()->tooltips(), true, false) . '<small class="hidden-xs-down muted"> ('
															. $totaltopics . ')</small>';
													else : ?>
														<?php echo $this->getCategoryLink($subcategory, '', null, KunenaTemplate::getInstance()->tooltips(), true, false) . '<small class="hidden-xs-down muted"> ('
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
													<small class="hidden-xs-down muted">
														(<?php echo Text::sprintf('COM_KUNENA_X_HIDDEN', (int) $this->more[$category->id]); ?>
														)
													</small>
												</li>
											<?php endif; ?>

										</ul>
									</div>
								<?php endif; ?>

								<?php if ($category->getmoderators() && KunenaConfig::getInstance()->listcat_show_moderators) : ?>
									<br/>
									<div class="moderators">
										<?php
										// get the Moderator list for display
										$modslist = [];
										foreach ($category->getmoderators() as $moderator)
										{
											$modslist[] = KunenaFactory::getUser($moderator)->getLink(null, null, '', null, KunenaTemplate::getInstance()->tooltips());
										}

										echo Text::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
										?>
									</div>
								<?php endif; ?>

								<?php if (!empty($this->pending[$category->id])) : ?>
									<div class="alert alert-warning" role="alert" style="margin-top:10px;">
										<a class="alert-link"
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
								$this->ktemplate = KunenaFactory::getTemplate();
								$avatar = $this->config->avataroncat ? $author->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb') : null;
								?>

								<td colspan="5" class="hidden-xs-down">
									<div class="row">
										<?php if ($avatar) : ?>
										<div class="col-xs-6 col-md-3" id="kcat-avatar">
											<?php echo $author->getLink($avatar, null, '', '', KunenaTemplate::getInstance()->tooltips(), $category->id, KunenaConfig::getInstance()->avataredit); ?>
										</div>
										<div class="col-xs-6 col-md-9" id="kcat-last">
											<?php else : ?>
											<div class="col-md-12" id="kcat-last">
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
								<td colspan="5">
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
						<td>
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
