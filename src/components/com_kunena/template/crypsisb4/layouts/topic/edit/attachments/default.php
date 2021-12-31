<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>
<ul class="thumbnails">
	<?php foreach ($this->attachments as $attachment)
		:
		?>
		<li class="col-md-6">
			<div class="thumbnail">
				<input type="hidden" name="attachments[<?php echo $attachment->id ?>]"
				       value="<?php echo $attachment->getFilename() ?>"/>
				<input type="checkbox" name="attachment[<?php echo $attachment->id ?>]" checked="checked"
				       value="<?php echo $attachment->id ?>"/>
				<?php echo $attachment->getLayout()->render('thumbnail'); ?>
				<span>
				<?php echo $attachment->getFilename(); ?>
				<?php echo '(' . number_format(intval($attachment->size) / 1024, 0, '', ',') . 'KB)'; ?>
			</span>
				<a href="#" class="btn border float-right">
					<?php echo Text::_('COM_KUNENA_EDITOR_INSERT'); ?>
				</a>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
