<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;
use StdClass;

/**
 * Class \Kunena\Forum\Libraries\Integration\Avatar
 *
 * @since   Kunena 6.0
 */
class KunenaAvatar
{
    /**
     * @var     ?Registry
     * @since   Kunena 7.0
     */
    protected ?Registry $params = \null;

    /**
     * @var     ?KunenaAvatar
     * @since   Kunena 6.0
     */
    protected static ?KunenaAvatar $instance = \null;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    public bool $css = \false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected bool $resize = \false;

    /**
     * Function to get the Avatar image for the user
     *
     * @param   KunenaUser  $user   user
     * @param   integer     $sizex  sizex
     * @param   integer     $sizey  sizey
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
    {
        return '';
    }

    /**
     * Function to get the active KunenaAvatar instance from the integration
     *
     * @return  KunenaAvatar
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function getInstance(): KunenaAvatar
    {
        if (\null === self::$instance) {
            PluginHelper::importPlugin('kunena');

            $avatarEvent = new KunenaGetAvatarEvent('onKunenaGetAvatar');
            Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetAvatar', $avatarEvent);

            $avatar = $avatarEvent->getAvatar();

            if ($avatar instanceof KunenaAvatar) {
                self::$instance = $avatar;
            } elseif (\is_array($avatar) && !empty($avatar) && $avatar[0] instanceof KunenaAvatar) {
                self::$instance = $avatar[0];
            } else {
                self::$instance = new self();
            }
        }

        return self::$instance;
    }

    /**
     * Function to get the Avatar image with link for the user
     *
     * @param   KunenaUser  $user   user
     * @param   string      $class  class
     * @param   int         $sizex  sizex
     * @param   int         $sizey  sizey
     * @param   string      $online The status of the user (green, yellow, red, grey...), added as the class status-<online> of the icon
     *
     * @return  string|false
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getLink(KunenaUser $user, $class = 'kavatar', $sizex = 90, $sizey = 90, string $online = ''): string|false
    {
        $size   = $this->getSize($sizex, $sizey);
        $avatar = $this->getURL($user, $size->x, $size->y);

        if (!$avatar) {
            return \false;
        }

        $ktemplate     = KunenaFactory::getTemplate();
        $topicicontype = $ktemplate->params->get('topicicontype');

        // A user without an avatar (Icontype): the person icon of the icon set of the template, at the size of the avatar.
        if (($topicicontype == 'fa' || $topicicontype == 'svg') && $avatar == Uri::root() . 'media/kunena/core/svg/person.svg') {
            return $this->getIcon($user, $class, $size, $topicicontype, $online);
        }

        if ($class == 'none') {
            $class = ' class="kavatar"';
        } elseif ($class) {
            $class = ' class="' . $class . '"';
        }

        if ($topicicontype == 'svg') {
            $link = '<span' . $class . ' data-bs-toggle="tooltip" title="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '">
				<img alt="" loading=lazy src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"></span>';
        } else {
            $link = '<img loading=lazy' . $class . ' src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"
			  alt="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '" />';
        }

        return $link;
    }

    /**
     * Function to get the person icon of a user without an avatar, in the icon set of the template
     *
     * @param   KunenaUser  $user           user
     * @param   string      $class          class
     * @param   StdClass    $size           the size of the avatar
     * @param   string      $topicicontype  fa or svg
     * @param   string      $online         the status of the user
     *
     * @return  string
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function getIcon(KunenaUser $user, $class, StdClass $size, string $topicicontype, string $online): string
    {
        $classes = $class == 'none' ? 'kavatar' : trim((string) $class);

        if ($online !== '') {
            $classes = trim($classes . ' status-' . $online);
        }

        $title = Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName());
        $span  = '<span' . ($classes !== '' ? ' class="' . $classes . '"' : '');

        // The box of the icon has the size of the avatar (line-height 1: no taller line box), and the icon fills that box, so
        // that a stylesheet which makes the avatar smaller makes the icon smaller too.
        $style = 'display: inline-block; width: ' . $size->x . 'px; height: ' . $size->y . 'px; line-height: 1';

        if ($topicicontype == 'fa') {
            return $span . ' style="' . $style . '; font-size: ' . $size->y . 'px" data-bs-toggle="tooltip" title="' . $title . '" role="img" aria-label="' . $title . '">'
                . '<i class="fas fa-user-circle" style="display: block; width: 100%; height: 100%" aria-hidden="true"></i></span>';
        }

        // The SVG is 1em wide and high: give it the size of the avatar.
        $svg = preg_replace_callback(
            '/<svg\b[^>]*>/',
            function ($tag) use ($size) {
                $tag = preg_replace('/\s(width|height|style)="[^"]*"/', '', $tag[0]);

                return '<svg width="' . $size->x . '" height="' . $size->y . '" style="display: block; width: 100%; height: 100%"' . substr($tag, 4);
            },
            KunenaSvgIcons::loadsvg('person'),
            1
        );

        return $span . ' style="' . $style . '" data-bs-toggle="tooltip" title="' . $title . '" role="img" aria-label="' . $title . '">' . $svg . '</span>';
    }

    /**
     * Function to get the Avatar image sizes
     *
     * @param   int  $sizex  sizex
     * @param   int  $sizey  sizey
     *
     * @return  StdClass
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getSize($sizex = 90, $sizey = 90): StdClass
    {
        $size    = new StdClass();
        $size->x = \intval($sizex);
        $size->y = \intval($sizey);

        if (!\intval($sizex)) {
            $template = KunenaFactory::getTemplate();
            $name     = ucfirst(strtolower($sizex));
            $size->x  = \intval($template->params->get('avatarSizeX' . $name, 90));
            $size->y  = \intval($template->params->get('avatarSizeY' . $name, 90));
        }

        return $size;
    }

    /**
     * Function to get the Avatar image for the user
     *
     * @param   KunenaUser  $user   user
     * @param   int         $sizex  sizex
     * @param   int         $sizey  sizey
     *
     * @return  string
     * @since   Kunena 6.0
     * @throws  Exception
     */
    public function getURL(KunenaUser $user, $sizex = 90, int $sizey = 90): string
    {
        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
        $size = $this->getSize($sizex, $sizey);

        if (!$size->x || !$size->y) {
            return false;
        }

        $result = $this->_getURL($user, $size->x, $size->y);
        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

        return $result;
    }

    /**
     * function to (pre) load the Avatars
     *
     * @param   array  $userlist  userlist
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function load(array $userlist): void
    {
    }
}
