<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Json;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class TopicJsonDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public $response;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Topic/Json';

    /**
     * Prepare category display.
     *
     * @return  void
     *
     * @throws  null
     * @since   Kunena 6.0
     */
    protected function before()
    {
        $catid    = $this->app->input->getInt('catid', 0);

        $category = KunenaCategoryHelper::get($catid);

        $this->response = $category->topictemplate;
    }
}
