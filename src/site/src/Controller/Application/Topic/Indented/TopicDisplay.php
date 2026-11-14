<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Topic\Indented;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The topic in the indented layout: the page of the topic, after the layout is saved as the choice of the user.
 *
 * @since   Kunena 4.0
 */
class TopicDisplay extends Display
{
    /**
     * Return true if layout exists: the indented layout uses the page of the topic.
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function exists(): bool
    {
        if ($this->input->getWord('format', 'html') != 'html') {
            return false;
        }

        $this->page = KunenaPage::factory("{$this->input->getCmd('view')}/default");

        return (bool) $this->page->getPath();
    }

    /**
     * Change topic layout to indented.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function before()
    {
        $layout = $this->input->getWord('layout');
        KunenaUserHelper::getMyself()->setTopicLayout($layout);

        parent::before();
    }
}
