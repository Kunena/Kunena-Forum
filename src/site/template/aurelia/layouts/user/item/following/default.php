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

// $this->following: the rows (followed_id, created_time) of the follow list of the user, the newest first.
// $this->feed: the latest topics of these users (already rendered), empty when nobody is followed.
$users = KunenaUserHelper::loadUsers(array_map(static fn ($row) => (int) $row->followed_id, (array) $this->following));
?>
<h3><?php echo Text::_('COM_KUNENA_FOLLOWING_LIST'); ?></h3>
<p class="text-muted"><?php echo Text::_('COM_KUNENA_FOLLOWING_LIST_DESC'); ?></p>

<?php if (empty($this->following)) : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_FOLLOWING_LIST_EMPTY'); ?></div>
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
        <?php foreach ($this->following as $row) :
            $followedUser = $users[(int) $row->followed_id] ?? KunenaUserHelper::get((int) $row->followed_id);
            ?>
            <tr>
                <td><?php echo $followedUser->getLink(); ?></td>
                <td><?php echo HTMLHelper::_('date', (int) $row->created_time, Text::_('DATE_FORMAT_LC4')); ?></td>
                <td class="text-end">
                    <a class="btn btn-outline-secondary btn-sm" rel="nofollow"
                       href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=unfollow&userid=' . (int) $row->followed_id . '&' . Session::getFormToken() . '=1'); ?>">
                        <?php echo Text::_('COM_KUNENA_UNFOLLOW'); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3><?php echo Text::_('COM_KUNENA_FOLLOWING_LATEST'); ?></h3>
    <?php echo $this->feed; ?>
<?php endif; ?>
