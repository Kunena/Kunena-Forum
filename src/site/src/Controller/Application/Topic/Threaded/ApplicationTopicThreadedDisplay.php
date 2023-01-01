<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Topic\Threaded;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentKunenaControllerApplicationTopicThreadedDisplay
 *
 * @since   Kunena 4.0
 */
class ApplicationTopicThreadedDisplay extends KunenaControllerDisplay
{
    /**
     * Return true if layout exists.
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function exists()
    {
        $page = KunenaPage::factory("{$this->input->getCmd('view')}/default");

        return (bool) $page->getPath();
    }

    /**
     * Change topic layout to threaded.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    protected function before()
    {
        $layout = $this->input->getWord('layout');
        KunenaUserHelper::getMyself()->setTopicLayout($layout);

        parent::before();
    }
}
