<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

?>
<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_announcement'); ?>
<div class="collapse in" id="announcement<?php echo $this->announcement->id; ?>">
	<div class="alert alert-info">
		<div class="close" data-toggle="collapse" data-target="#announcement<?php echo $this->announcement->id; ?>">&times;</div>
		<h5>
			<a class="btn-link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=list'); ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST')?>" rel="follow">
				<?php echo $this->announcement->displayField('title') ;?>
			</a>

			<?php if ($this->announcement->showdate) : ?>
				<small>(<?php echo $this->announcement->displayField('created', 'date_today'); ?>)</small>
			<?php endif; ?>
		</h5>

		<div>
			<p><?php echo $this->announcement->displayField('sdescription'); ?></p>

			<?php if (!empty($this->announcement->description)) : ?>
				<p>
					<a class="btn-link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=default&id=' . $this->announcement->id); ?>" title="<?php echo  $this->announcement->displayField('title')?>" rel="follow">
						<?php echo JText::_('COM_KUNENA_ANN_READMORE') ;?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</div>

