<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaModuleContent;

/**
 * KunenaLayoutWidgetModule
 *
 * @since   Kunena 4.0
 */
class WidgetModule extends KunenaLayout
{
    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $position = null;

    public mixed $cols = null;

    /**
     * The modules of a position of this page (or of the positions which start the same: "kunena_msg_*") are run through the Joomla
     * content plugins of a target, when the system plugin of Kunena has that target chosen ("Run plugins on"). KunenaModuleContent.
     *
     * @param   string  $position  The module position, or its start with an asterisk.
     * @param   string  $target    The target.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function prepareWith(string $position, string $target): void
    {
        KunenaModuleContent::prepareWith($position, $target);
    }

    /**
     * Whether the position has modules (a topic page asks for about seven positions for each message, which are nearly always empty).
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function hasModules(): bool
    {
        return $this->position && Factory::getApplication()->getDocument()->countModules($this->position);
    }

    /**
     * An empty position renders nothing: its layout file is not looked for nor included (Kunena 8.0, a few milliseconds on every
     * topic page).
     *
     * @param   string|null  $layout  The layout.
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function render($layout = null): string
    {
        if (!$this->hasModules()) {
            return '';
        }

        return parent::render($layout);
    }

    /**
     * Renders module position.
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function renderPosition()
    {
        if ($this->hasModules()) {
            $renderer = Factory::getApplication()->getDocument()->loadRenderer('modules');
            $options  = ['style' => 'xhtml'];
            $html     = (string) $renderer->render($this->position, $options, null);

            // The content plugins of the targets of the position, when the system plugin runs them on one of them.
            return KunenaModuleContent::prepare((string) $this->position, $html);
        }

        return '';
    }
}
