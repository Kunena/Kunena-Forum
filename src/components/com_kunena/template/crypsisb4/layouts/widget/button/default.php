<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$label = Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}");
$title = Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG");

if ($title == "COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG")
{
	$title = '';
}

$modal   = isset($this->modal) ? 'data-toggle="modal" data-backdrop="false"' : '';
$right   = isset($this->pullright) ? ' float-right' : '';
$id      = isset($this->id) ? 'id="' . $this->id . '"' : '';
$success = !empty($this->success) ? ' btn-outline-success' : '';
$primary = !empty($this->primary) ? ' btn-outline-primary' : '';
$normal  = !empty($this->normal) ? 'btn-small dropdown-item' : 'btn btn-outline-primary border';
$icon    = $this->icon;
$ktemplate     = KunenaFactory::getTemplate();
$topicicontype = $ktemplate->params->get('topicicontype');
?>

<a <?php echo $id; ?> class="<?php echo $normal . $primary . $success . $right . ' kbutton-' . $this->name; ?>"
					  href="<?php echo $this->url; ?>" rel="nofollow"
					  title="<?php echo $title; ?>" name="<?php echo $this->name; ?>" <?php echo $modal; ?>>
	<?php
	if (!empty($icon))
	:
		?>
		<?php if ($topicicontype != 'B4')
		:
			?>
			<i class="<?php echo $icon; ?>"></i>
		<?php else

		:
			?>
			<img src="<?php echo Uri::root(); ?>/media/kunena/core/svg/<?php echo $icon;?>.svg" />
		<?php endif; ?>
	<?php endif; ?>
	<?php echo $label; ?>
</a>
