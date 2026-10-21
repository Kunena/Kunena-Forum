<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

// The card of a link in a message: the picture, the title and the description of the page (of the other site).
?>
<div class="card mb-2 kbbcode-linkcard" style="max-width: 32rem;">
    <a class="text-decoration-none text-reset" href="<?php echo $this->escape($this->url); ?>" target="_blank" rel="nofollow noopener noreferrer">
        <?php if (!empty($this->image)) : ?>
            <img src="<?php echo $this->escape($this->image); ?>" alt="" class="card-img-top" style="max-height: 14rem; object-fit: cover;"
                loading="lazy" referrerpolicy="no-referrer">
        <?php endif; ?>
        <div class="card-body py-2">
            <div class="small text-muted"><?php echo $this->escape($this->site); ?></div>
            <h6 class="card-title mb-1"><?php echo $this->escape($this->title); ?></h6>
            <?php if (!empty($this->description)) : ?>
                <p class="card-text small mb-0"><?php echo $this->escape($this->description); ?></p>
            <?php endif; ?>
        </div>
    </a>
</div>
