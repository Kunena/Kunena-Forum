<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$me    = KunenaUserHelper::getMyself();
$state = $this->state;
?>

<div class="kunena-search">
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>" method="post" name="usrlform" id="usrlform">
        <input type="hidden" name="view" value="user" />
        <?php if ($me->exists()) :
            ?>
            <input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>" />
        <?php endif; ?>
        <?php echo HTMLHelper::_('form.token'); ?>
        <div class="input-group search">
            <input id="kusersearch" class="form-control input-sm search-query" type="text" name="search" value="<?php echo !empty($state) ? $this->escape($state) : ''; ?>" placeholder="<?php echo Text::_('COM_KUNENA_USRL_SEARCH'); ?>" />
            <button class="btn btn-outline-primary" type="submit">
                <?php echo KunenaIcons::search(); ?>
            </button>
        </div>
    </form>
</div>