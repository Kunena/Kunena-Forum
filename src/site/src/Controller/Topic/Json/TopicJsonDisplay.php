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
        $catid    = $this->input->getInt('catid', 0);

        $category = KunenaCategoryHelper::get($catid);

        // The template text of a category is only for those who may read the category (it can be a private one): for anybody else the
        // answer is empty, like it is for a category that does not exist.
        $this->response = '';

        try {
            $category->tryAuthorise('read');
            $this->response = (string) $category->topictemplate;
        } catch (\Exception $e) {
            // No access.
        }
    }
}
