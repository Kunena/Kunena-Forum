<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$cols            = !empty($this->actions) ? 6 : 7;
$colspan         = !empty($this->actions) ? 4 : 3;
$view            = Factory::getApplication()->input->getWord('view');
$layout          = Factory::getApplication()->input->getWord('layout');
$this->ktemplate = KunenaFactory::getTemplate();
$social          = $this->ktemplate->params->get('socialshare');
$me              = KunenaUserHelper::getMyself();

if (KunenaConfig::getInstance()->ratingenabled)
{
	$this->addStyleSheet('assets/css/rating.css');
}
?>
<div class="row">
	<div class="col-md-12">
		<?php if ($social == 1 && $me->socialshare != 0) : ?>
			<div><?php echo $this->subLayout('Widget/Social')->set('me', $me)->set('ktemplate', $this->ktemplate); ?></div>
		<?php endif; ?>
		<?php if ($social == 2 && $me->socialshare != 0) : ?>
			<div><?php echo $this->subLayout('Widget/Socialcustomtag'); ?></div>
		<?php endif; ?>
		<div class="float-left">
			<h1>
				<?php echo $this->escape($this->headerText); ?>

				<?php if ($layout != 'unread') : ?>
					<small class="hidden-xs-down">
						(<?php echo KunenaForumCategory::getInstance()->totalCount($this->pagination->total); ?>)
					</small>
				<?php endif; ?>
				<?php // ToDo:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
			</h1>
		</div>

		<?php if ($view != 'user') : ?>
			<div class="float-right" id="filter-time">
				<h2 class="filter-sel float-right">
					<form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>"
					      id="timeselect" name="timeselect"
					      method="post" target="_self" class="form-inline hidden-xs-down">
						<?php $this->displayTimeFilter('sel', 'class="form-control filter custom-select" onchange="this.form.submit()"'); ?>
						<?php echo HTMLHelper::_('form.token'); ?>
					</form>
				</h2>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
if ($this->config->enableforumjump && !$this->embedded && $this->topics)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
} ?>

<div class="float-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('catid', 'all')
		->setLayout('topic'); ?>
</div>

<div class="float-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<div class="kfrontend shadow-lg rounded mt-4 border">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post" name="ktopicsform"
	      id="ktopicsform">
		<?php echo HTMLHelper::_('form.token'); ?>
		<?php if ($view == 'user'): ?>
			<input type="hidden" name="userid" value="<?php echo $this->user->userid; ?>"/>
		<?php endif; ?>
		<table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?> shadow-lg rounded">
			<thead>
			<tr>
				<th scope="col" class="center hidden-xs-down">
					<a id="forumtop"> </a>
					<a href="#forumbottom" rel="nofollow">
						<?php echo KunenaIcons::arrowdown(); ?>
					</a>
				</th>
				<th scope="col" class="hidden-xs-down"><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></th>
				<th scope="col" class="hidden-xs-down"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>
					/ <?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></th>
				<th scope="col" class="hidden-xs-down"><?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?></th>

				<?php if (!empty($this->actions)) : ?>
					<th scope="col" class="center"><input class="kcheckall" type="checkbox" name="toggle" value=""/></th>
				<?php endif; ?>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="center hidden-xs-down">
					<a id="forumbottom"> </a>
					<a href="#forumtop" rel="nofollow">
						<span class="dropdown-divider"></span>
						<?php echo KunenaIcons::arrowup(); ?>
					</a>
				</th>
				<?php if (!empty($this->actions) || !empty($this->moreUri)) : ?>
					<th scope="col" class="hidden-xs-down">
						<div class="form-group">
							<div class="input-group" role="group">
								<div class="input-group-btn">
									<label>
										<?php if (!empty($this->topics) && !empty($this->moreUri))
										{
											echo HTMLHelper::_('kunenaforum.link', $this->moreUri, Text::_('COM_KUNENA_MORE'), null, 'btn btn-outline-primary float-left', 'follow');
										} ?>
										<?php if (!empty($this->actions)) : ?>
											<?php echo HTMLHelper::_('select.genericlist', $this->actions, 'task', 'class="form-control kchecktask custom-select" ', 'value', 'text', 0, 'kchecktask'); ?>
											<?php if (isset($this->actions['move'])) :
												$options = [HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_BULK_CHOOSE_DESTINATION'))];
												echo HTMLHelper::_('kunenaforum.categorylist', 'target', 0, $options, [], 'class="form-control fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
											endif; ?>
											<button type="submit" name="kcheckgo"
											        class="btn btn-outline-primary border"><?php echo Text::_('COM_KUNENA_GO') ?></button>
										<?php endif; ?>
									</label>
								</div>
							</div>
						</div>
					</th>
				<?php endif; ?>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
			</tfoot>
			<tbody class="topic-list">
			<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
				<tr>
					<th scope="col"><?php echo Text::_('COM_KUNENA_VIEW_NO_TOPICS') ?></th>
				</tr>
			<?php else : ?>
				<?php $counter = 2; ?>

				<?php foreach ($this->topics as $i => $topic)
				{
					echo $this->subLayout('Topic/Row')
						->set('topic', $topic)
						->set('position', 'kunena_topic_' . $i)
						->set('checkbox', !empty($this->actions));

					if ($this->ktemplate->params->get('displayModule'))
					{
						echo $this->subLayout('Widget/Module')
							->set('position', 'kunena_topic_' . $counter++)
							->set('cols', $cols)
							->setLayout('table_row');
					}
				} ?>
			<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>

<div class="float-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<div class="clearfix"></div>

