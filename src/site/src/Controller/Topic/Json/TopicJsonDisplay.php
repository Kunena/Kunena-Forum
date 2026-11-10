<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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
    public mixed $response = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Topic/Json';

    /**
     * Prepare category display.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function before()
    {
        $catid    = $this->input->getInt('catid', 0);

        $category = KunenaCategoryHelper::get($catid);

        // The template text of a category is only for those who may start a topic there (it can be a private category), as in
        // the task categorytemplate of the topic controller. Anybody else, or no category, gets an empty template.
        $this->response = $category->exists() && $category->isAuthorised('topic.create') ? (string) $category->topictemplate : '';
    }
}
