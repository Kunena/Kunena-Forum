<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

// $this->ignored: the rows (ignored_id, created_time) of the ignore list of the user, the newest first.
$users = KunenaUserHelper::loadUsers(array_map(static fn ($row) => (int) $row->ignored_id, (array) $this->ignored));
?>
<h3><?php echo Text::_('COM_KUNENA_IGNORE_LIST'); ?></h3>
<p class="text-muted"><?php echo Text::_('COM_KUNENA_IGNORE_LIST_DESC'); ?></p>

<?php if (empty($this->ignored)) : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_IGNORE_LIST_EMPTY'); ?></div>
<?php else : ?>
    <table class="table table-striped table-hover align-middle">
        <thead>
        <tr>
            <th scope="col"><?php echo Text::_('COM_KUNENA_USERNAME'); ?></th>
            <th scope="col"><?php echo Text::_('COM_KUNENA_IGNORE_SINCE'); ?></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->ignored as $row) :
            $ignoredUser = $users[(int) $row->ignored_id] ?? KunenaUserHelper::get((int) $row->ignored_id);
            ?>
            <tr>
                <td><?php echo $ignoredUser->getLink(); ?></td>
                <td><?php echo HTMLHelper::_('date', (int) $row->created_time, Text::_('DATE_FORMAT_LC4')); ?></td>
                <td class="text-end">
                    <a class="btn btn-outline-secondary btn-sm" rel="nofollow"
                       href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=unignore&userid=' . (int) $row->ignored_id . '&' . Session::getFormToken() . '=1'); ?>">
                        <?php echo Text::_('COM_KUNENA_UNIGNORE'); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
