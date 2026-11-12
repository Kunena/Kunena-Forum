<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User\Item;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUserRankPolicy;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserItemSummary extends KunenaLayout
{
    public mixed $profile = null;

    public mixed $config = null;

    public mixed $candisplaymail = null;

    public mixed $me = null;

    public mixed $private = null;

    public mixed $points = null;

    public mixed $medals = null;

    public mixed $socials = null;

    public mixed $avatar = null;

    public mixed $banInfo = null;

    /**
     * The ranks the viewer may give to the user of the profile: 0 (no rank given) first, then the special ranks.
     *
     * @return  array  rankId => translated title, or an empty array when the viewer may not change the rank.
     *
     * @since   Kunena 8.0
     */
    public function rankChoices(): array
    {
        $refusal = KunenaUserRankPolicy::refusal(
            (int) $this->me->userid,
            (int) $this->profile->userid,
            $this->me->isAdmin(),
            $this->me->isModerator(),
            $this->profile->exists(),
            $this->profile->isAdmin(),
            0,
            [0]
        );

        if ($refusal !== null) {
            return [];
        }

        $choices = [0 => Text::_('COM_KUNENA_USER_RANK_NONE')];

        foreach (KunenaUserRankPolicy::getAssignableRanks() as $rankId => $rankTitle) {
            $choices[$rankId] = Text::_($rankTitle);
        }

        return $choices;
    }
}
