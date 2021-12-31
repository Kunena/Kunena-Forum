<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
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

$id      = isset($this->id) ? ' id="' . $this->id . '"' : '';
$primary = !empty($this->primary) ? ' btn-primary' : '';
?>
<a<?php echo $id; ?> class="btn <?php echo $primary; ?>" href="<?php echo $this->url; ?>"
                     title="<?php echo $title; ?>">
	<span class="<?php echo $this->name; ?>"></span>
	<?php echo $label; ?>
</a>
