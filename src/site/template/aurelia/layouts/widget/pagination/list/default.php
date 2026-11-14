<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Pagination
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

$display = isset($this->display);

$data = $this->pagination->getData();

$count = count($data->pages);

if ($count == 0) {
    return;
} elseif ($count == 1 && empty($display)) {
    return;
}

$last = 0;
?>

<nav>
    <ul class="pagination ms-0">
        <?php
        echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->start)->set('extraClass', 'd-none d-sm-block');
        echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->previous)->set('extraClass', 'd-none d-sm-block');

        foreach ($data->pages as $k => $item) {
            if ($last + 1 != $k) {
                echo '<li class="page-item disabled d-none d-sm-block"><a class="">...</a></li>';
            }

            $last = $k;

            echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
        }

        echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->next)->set('extraClass', 'd-none d-sm-block');
        echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->end)->set('extraClass', 'd-none d-sm-block');
        ?>
    </ul>
</nav>
