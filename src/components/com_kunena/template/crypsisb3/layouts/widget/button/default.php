<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$label = Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}");
$title = Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG");

if ($title == "COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG")
{
	$title = '';
}

$modal   = isset($this->modal) ? 'data-toggle="modal" data-backdrop="false"' : '';
$right   = isset($this->pullright) ? ' pull-right' : '';
$id      = isset($this->id) ? 'id="' . $this->id . '"' : '';
$success = !empty($this->success) ? ' btn-success' : '';
$primary = !empty($this->primary) ? ' btn-primary' : '';
$normal  = !empty($this->normal) ? 'btn-small' : 'btn btn-default';
$icon    = $this->icon;
?>

<a <?php echo $id; ?> class="<?php echo $normal . $primary . $success . $right . ' kbutton-' . $this->name; ?>"
                      href="<?php echo $this->url; ?>" rel="nofollow"
                      title="<?php echo $title; ?>" name="<?php echo $this->name; ?>" <?php echo $modal; ?>>
	<?php
	if (!empty($icon))
		:
		?>
		<i class="<?php echo $icon; ?>"></i>
	<?php endif; ?>
	<?php echo $label; ?>
</a>
