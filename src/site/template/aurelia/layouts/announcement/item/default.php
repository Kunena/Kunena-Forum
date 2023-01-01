<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

?>
<h3>
    <?php echo $this->announcement->displayField('title'); ?>

    <?php if ($this->announcement->showdate) :
        ?>
        <small data-bs-toggle="tooltip" title="<?php echo $this->announcement->displayField('created', 'ago'); ?>">
            <?php echo $this->announcement->displayField('created', 'date_today'); ?>
        </small>
    <?php endif; ?>
</h3>

<?php if (!empty($this->actions)) :
    ?>
    <div>
        <?php echo implode(' ', $this->actions); ?>
    </div>
    <br>
<?php endif; ?>

<div class="shadow-lg rounded">
    <div><?php echo $this->announcement->displayField('sdescription'); ?></div>
    <div><?php echo $this->announcement->displayField('description'); ?></div>
</div>
