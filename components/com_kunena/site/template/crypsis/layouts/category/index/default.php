<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Category
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$mmm=0;
/** @var KunenaForumCategory $section */
foreach ($this->sections as $section) :
	$markReadUrl = $section->getMarkReadUrl();
?>
<div class="btn-toolbar pull-right">

	<?php if ($this->me->exists()) : ?>
	<div class="btn-group">

		<?php if ($markReadUrl) : ?>
		<a class="btn btn-small" href="<?php echo $markReadUrl; ?>">
			<?php echo JText::_('COM_KUNENA_MARK_CATEGORIES_READ') ?>
		</a>
		<?php endif; ?>

		<?php if ($this->me->isAdmin($section)) : ?>
		<?php // FIXME: translate and implement. ?>
		<button class="btn btn-small">Approve Posts</button>
		<?php endif; ?>

	</div>
	<?php endif; ?>

	<?php if (count($this->sections) > 0) : ?>
	<div class="btn btn-small" data-toggle="collapse" data-target="#section<?php echo $section->id; ?>">&times;</div>
	<?php endif; ?>

</div>
<h2>

	<?php if ($section->parent_id) : ?>
	<?php echo $this->getCategoryLink($section->getParent(), $this->escape($section->getParent()->name)); ?> /
	<?php endif; ?>

	<?php echo $this->getCategoryLink($section, $this->escape($section->name)); ?>
	<small class="hidden-phone">(<?php echo JText::plural('COM_KUNENA_X_TOPICS',
			$this->formatLargeNumber($section->getTopics())); ?>)</small>
</h2>

<div class="row-fluid collapse in section<?php echo $this->escape($section->class_sfx); ?>" id="section<?php echo $section->id; ?>">
	<table class="table table-striped table-hover table-bordered table-condensed">

		<?php if (!empty($section->description)) : ?>
		<thead class="hidden-phone">
			<tr>
				<td colspan="3">
					<div><?php echo $section->displayField('description'); ?></div>
				</td>
			</tr>
		</thead>
		<?php endif; ?>

		<?php if (empty($this->categories[$section->id])) : ?>

		<?php if ($section->isSection()) : ?>
		<tr>
			<td>
				<?php echo JText::_('COM_KUNENA_GEN_NOFORUMS'); ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php else : ?>
		<?php
			/** @var KunenaForumCategory $category */
			foreach ($this->categories[$section->id] as $category) : ?>
		<tr class="category<?php echo $this->escape($category->class_sfx); ?>" id="category<?php echo $category->id; ?>">
			<?php /* FIXME: implement category icons.
			<td class="span1">
				<?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), ''); ?>
			</td> */ ?>
			<td class="span8">
				<div>
					<h3>
						<?php echo $this->getCategoryLink($category); ?>
						<small class="hidden-phone">(<?php echo JText::plural('COM_KUNENA_X_TOPICS',
								$this->formatLargeNumber($category->getTopics())); ?>)</small>
					</h3>
					<span>
						<?php
						if (($new = $category->getNewCount()) > 0) {
							echo '<sup class="knewchar">(' . $new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>';
						}
						if ($category->locked) {
							echo $this->getIcon('kforumlocked', JText::_('COM_KUNENA_LOCKED_CATEGORY'));
						}
						if ($category->review) {
							echo $this->getIcon('kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED'));
						}
						?>
					</span>
				</div>

				<?php if (!empty($category->description)) : ?>
					<div class="hidden-phone"><?php echo $category->displayField('description'); ?></div>
				<?php endif; ?>

				<?php
				// Display subcategories
				if (!empty($this->categories[$category->id])) : ?>
				<ul class="inline">

					<?php foreach ($this->categories[$category->id] as $subcategory) : ?>
					<li>
						<?php
						// FIXME: Implement small category icons.
						//echo $this->getCategoryIcon($subcategory, true);
						echo $this->getCategoryLink($subcategory) . '<small class="hidden-phone muted"> ('
							. JText::plural('COM_KUNENA_X_TOPICS', $this->formatLargeNumber($subcategory->getTopics()))
							. ')</small>';
						?>
					</li>
					<?php endforeach; ?>

				</ul>
				<?php endif; ?>

				<?php if (!empty($this->pending[$category->id])) : ?>
				<div class="alert">
					<?php echo JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=topics&layout=posts&mode=unapproved&userid=0&catid='.intval($category->id),
						intval($this->pending[$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'),
						'', '', 'nofollow'); ?>
				</div>
				<?php endif; ?>

			</td>

			<?php $last = $category->getLastTopic(); ?>

			<?php if (!$last->exists()) : ?>
			<td colspan="2" class="span4 hidden-phone"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></td>
			<?php else :
				$author = $last->getLastPostAuthor();
				$time = $last->getLastPostTime();
				$avatar = $this->config->avataroncat > 0 ? $author->getAvatarImage('img-polaroid', 48) : null;
			?>

			<?php if ($avatar) : ?>
			<td class="span1 center hidden-phone">
				<?php echo $author->getLink($avatar); ?>
			</td>
			<?php endif; ?>

			<td class="span3 hidden-phone">
				<div>
					<?php echo $this->getLastPostLink($category) ?>
				</div>
				<div>
					<?php echo JText::sprintf('COM_KUNENA_BY_X', $author->getLink()); ?>
				</div>
				<div title="<?php echo $time->toKunena('config_post_dateformat_hover'); ?>">
					<?php echo $time->toKunena('config_post_dateformat'); ?>
				</div>
			</td>
			<?php endif; ?>

		</tr>
		<?php endforeach; ?>

		<?php endif; ?>

	</table>
</div>

<!-- Begin: Category Module Position -->
<?php echo $this->subLayout('Page/Module')->set('position', 'kunena_section_' . ++$mmm); ?>
<!-- Finish: Category Module Position -->
<?php endforeach; ?>
