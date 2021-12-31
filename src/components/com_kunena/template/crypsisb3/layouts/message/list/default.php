<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Message
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

$colspan = !empty($this->actions) ? 4 : 3;
$cols    = empty($this->checkbox) ? 4 : 5;
$view    = Factory::getApplication()->input->getWord('view');
?>

<div class="row">
	<div class="col-md-12">
		<div class="pull-left">
			<h1>
				<?php echo $this->escape($this->headerText); ?>
				<small class="hidden-xs">
					(<?php echo Text::sprintf($this->messagemore, $this->formatLargeNumber($this->pagination->total)); ?>
					)
				</small>

				<?php // ToDo:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
			</h1>
		</div>

		<?php if ($view != 'user')
			:
			?>
			<h2 class="filter-time pull-right" id="filter-time">
				<div class="filter-sel pull-right">
					<form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>"
					      id="timeselect" name="timeselect"
					      method="post" target="_self" class="form-inline hidden-xs">
						<?php $this->displayTimeFilter('sel'); ?>
						<?php echo HTMLHelper::_('form.token'); ?>
					</form>
				</div>
			</h2>
		<?php endif; ?>
	</div>
</div>

<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('catid', 'all')
		->setLayout('topic'); ?>
</div>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post"
      name="ktopicsform" id="ktopicsform">
	<?php echo HTMLHelper::_('form.token'); ?>

	<table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?>">
		<thead>
		<?php if (empty($this->messages))
			:
			?>
			<tr>
				<td colspan="<?php echo $colspan; ?>">
					<?php echo Text::_('COM_KUNENA_NO_POSTS') ?>
				</td>
			</tr>
		<?php else
			:
			?>

			<tr class="category">
				<td class="col-md-1 center hidden-xs">
					<a id="forumtop"> </a>
					<a href="#forumbottom" rel="nofollow">
						<?php echo KunenaIcons::arrowdown(); ?>
					</a>
				</td>
				<td class="col-md-<?php echo $cols; ?>">
					<?php echo Text::_('COM_KUNENA_GEN_MESSAGE'); ?>
					/ <?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?>
				</td>
				<td class="col-md-2 hidden-xs">
					<?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo Text::_('COM_KUNENA_GEN_HITS'); ?>
				</td>
				<td class="col-md-3">
					<?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?>
				</td>
				<?php if (!empty($this->actions))
					:
					?>
					<td class="col-md-1 center">
						<label>
							<input class="kcheckall" type="checkbox" name="toggle" value=""/>
						</label>
					</td>
				<?php endif; ?>
			</tr>
		<?php endif; ?>
		</thead>

		<tfoot>
		<?php if (!empty($this->messages))
			:
			?>
			<tr>
				<td class="center hidden-xs">
					<a id="forumbottom"> </a>
					<a href="#forumtop" rel="nofollow">
						<?php echo KunenaIcons::arrowup(); ?>
					</a>
				</td>
				<td colspan="<?php echo $colspan; ?>">
					<div class="form-group">
						<div class="input-group" role="group">
							<div class="input-group-btn">
								<?php if (!empty($this->moreUri))
								{
									echo HTMLHelper::_('kunenaforum.link', $this->moreUri, Text::_('COM_KUNENA_MORE'), null, 'btn btn-primary pull-left', 'nofollow');
								} ?>
								<?php
								if (!empty($this->actions))
									:
									?>
									<?php echo HTMLHelper::_('select.genericlist', $this->actions, 'task', 'class="form-control kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
									<?php
									if (isset($this->actions['move']))
										:
										$options = array(HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_BULK_CHOOSE_DESTINATION')));
										echo HTMLHelper::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="form-control fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
									endif; ?>
									<input type="submit" name="kcheckgo" class="btn btn-default"
									       value="<?php echo Text::_('COM_KUNENA_GO') ?>"/>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</td>
			</tr>
		<?php endif; ?>
		</tfoot>

		<tbody class="message-list">
		<?php
		foreach ($this->messages as $i => $message)
		{
			echo $this->subLayout('Message/Row')
				->set('message', $message)
				->set('position', $i)
				->set('checkbox', !empty($this->actions));
		}
		?>
		</tbody>
	</table>
</form>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<?php if ($view != 'user')
	:
	?>
	<form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>" id="timeselect"
	      name="timeselect"
	      method="post" target="_self" class="timefilter pull-right">
		<?php $this->displayTimeFilter('sel'); ?>
	</form>
<?php endif; ?>

<div class="clearfix"></div>
