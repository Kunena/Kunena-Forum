<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Announcement
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncementHelper;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$row          = $this->row;
$announcement = $this->announcement;
?>

<tr>
    <td class="nowrap d-none d-md-table-cell">
        <?php echo $announcement->displayField('created', 'date_today'); ?>
    </td>

    <td class="nowrap">
        <div class="overflow">
            <?php echo HTMLHelper::_(
    'kunenaforum.link',
    $announcement->getUri(),
    $announcement->displayField('title'),
    null,
    'follow'
); ?>
        </div>
    </td>

    <?php if ($this->checkbox) :
    ?>
        <td class="center">
            <?php $isBanner = (int) KunenaAnnouncementHelper::getBanner()?->id === (int) $announcement->id; ?>
            <?php if (KunenaUserHelper::getMyself()->isAdmin()) : ?>
                <a href="#" onclick="Joomla.listItemTask('cb<?php echo (int) $row; ?>', 'banner'); return false;"
                    title="<?php echo Text::_($isBanner ? 'COM_KUNENA_ANN_BANNER_NEWEST_ACTION' : 'COM_KUNENA_ANN_BANNER_ACTION'); ?>">
                    <?php echo $isBanner ? '<span class="text-warning">' . KunenaIcons::star() . '</span>' : KunenaIcons::starOpen(); ?>
                </a>
            <?php elseif ($isBanner) : ?>
                <span class="text-warning"><?php echo KunenaIcons::star(); ?></span>
            <?php endif; ?>
        </td>
        <td class="center">
            <?php if ($this->canPublish()) {
                echo HTMLHelper::_('kunenagrid.published', $row, $announcement->published, '', true);
            } ?>
        </td>
        <td class="center">
            <?php if ($this->canEdit()) {
                echo HTMLHelper::_(
                    'kunenagrid.task',
                    $row,
                    'tick.png',
                    Text::_('COM_KUNENA_ANN_EDIT'),
                    'edit',
                    '',
                    true
                );
            } ?>
        </td>
        <td class="center">
            <?php if ($this->canDelete()) {
                echo HTMLHelper::_(
                    'kunenagrid.task',
                    $row,
                    'publish_x.png',
                    Text::_('COM_KUNENA_ANN_DELETE'),
                    'delete',
                    '',
                    true
                );
            } ?>
        </td>
        <td>
        <?php // The name which the forum shows (login name, real name or nickname): the login name was taken for the nicknames too. ?>
        <?php echo $this->escape($announcement->getAuthor()->getName('', false)); ?>
        </td>
    <?php endif; ?>

    <td class="center d-none d-md-table-cell">
        <?php echo $announcement->displayField('id'); ?>
    </td>

    <?php if ($this->checkbox) :
    ?>
        <td class="center">
            <?php echo HTMLHelper::_('kunenagrid.id', $row, $announcement->id); ?>
        </td>
    <?php endif; ?>

</tr>