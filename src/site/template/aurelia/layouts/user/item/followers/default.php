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
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

// $this->followers: the rows (user_id, created_time) of the users who follow the user, the newest first.
$me    = KunenaUserHelper::getMyself();
$users = KunenaUserHelper::loadUsers(array_map(static fn ($row) => (int) $row->user_id, (array) $this->followers));
?>
<h3><?php echo Text::_('COM_KUNENA_FOLLOWERS_LIST'); ?></h3>

<?php if (empty($this->followers)) : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_FOLLOWERS_LIST_EMPTY'); ?></div>
<?php else : ?>
    <table class="table table-striped table-hover align-middle">
        <thead>
        <tr>
            <th scope="col"><?php echo Text::_('COM_KUNENA_USERNAME'); ?></th>
            <th scope="col"><?php echo Text::_('COM_KUNENA_FOLLOW_SINCE'); ?></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->followers as $row) :
            $follower = $users[(int) $row->user_id] ?? KunenaUserHelper::get((int) $row->user_id);
            ?>
            <tr>
                <td><?php echo $follower->getLink(); ?></td>
                <td><?php echo HTMLHelper::_('date', (int) $row->created_time, Text::_('DATE_FORMAT_LC4')); ?></td>
                <td class="text-end">
                    <?php if (KunenaUserFollow::canFollow($me, $follower)) :
                        $following = KunenaUserFollow::isFollowing($me->userid, $follower->userid);
                        ?>
                        <a class="btn btn-outline-secondary btn-sm" rel="nofollow"
                           href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=' . ($following ? 'unfollow' : 'follow') . '&userid=' . (int) $follower->userid . '&' . Session::getFormToken() . '=1'); ?>">
                            <?php echo Text::_($following ? 'COM_KUNENA_UNFOLLOW' : 'COM_KUNENA_FOLLOW'); ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
