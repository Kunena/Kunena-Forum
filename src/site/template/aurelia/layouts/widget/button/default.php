<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

// A ready-made label (already escaped) replaces the one of the language key.
$label = isset($this->label) ? $this->label : Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}");
$title = Text::_("COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG");

if ($title == "COM_KUNENA_BUTTON_{$this->scope}_{$this->name}_LONG") {
    $title = '';
}

// A button which opens a dialog of the page (its url is "#" and the id of the dialog): a button with an invoker command.
$dialog        = isset($this->modal) ? ltrim((string) $this->url, '#') : '';
$right         = isset($this->pullright) ? ' float-end' : '';
$id            = isset($this->id) ? ' ' . $this->id : '';
$success       = !empty($this->success) ? ' btn-outline-success' : '';
$primary       = !empty($this->primary) ? ' btn-outline-primary' : '';
$normal        = !empty($this->normal) ? 'btn-small dropdown-item' : 'btn btn-outline-primary border';
$icon          = $this->icon;
$confirm       = !empty($this->confirm) ? ' onclick="return confirm(' . htmlspecialchars(json_encode((string) $this->confirm), ENT_QUOTES, 'UTF-8') . ');"' : '';
?>

<?php if ($dialog !== '') : ?>
<button type="button" class="<?php echo $normal . $primary . $success . $right . ' kbutton-' . $this->name . $id; ?>"
        command="show-modal" commandfor="<?php echo htmlspecialchars($dialog, ENT_QUOTES, 'UTF-8'); ?>"
        title="<?php echo $title; ?>"<?php echo $confirm; ?>>
    <?php if (!empty($icon)) : ?>
        <?php echo $icon; ?>
    <?php endif; ?>
    <?php echo $label; ?>
</button>
<?php else : ?>
<a class="<?php echo $normal . $primary . $success . $right . ' kbutton-' . $this->name . $id; ?>"
                      href="<?php echo $this->url; ?>" rel="nofollow"
                      title="<?php echo $title; ?>" <?php echo $confirm; ?>>
    <?php
    if (!empty($icon)) : ?>
        <?php echo $icon; ?>
    <?php endif; ?>
    <?php echo $label; ?>
</a>
<?php endif; ?>
