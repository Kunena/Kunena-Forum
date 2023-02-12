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

namespace Kunena\Forum\Site\Controller\Application\Category\Raw;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\CategoryModel;

/**
 * CategoryDisplay
 *
 * @since   Kunena 6.0
 */
class CategoryDisplay extends KunenaControllerDisplay
{
    /**
     * Return true if layout exists.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function exists()
    {
        return KunenaFactory::getTemplate()->isHmvc();
    }

    /**
     * Prepare the json content with topics list.
     *
     * @return  void
     *
     * @throws  null
     * @since   Kunena 6.0
     */
    protected function before()
    {
        $response              = [];
        $response['topiclist'] = [];

        $this->me        = KunenaUserHelper::getMyself();
        $document = Factory::getApplication()->getDocument();
        $model = new CategoryModel;

        if ($this->me->exists()) {
            $category = $model->getCategory();

            if (!$category->isAuthorised('read')) {
                $response['error'] = $category->getError();
            } else {
                $topics = $model->getTopics();

                foreach ($topics as $topic) {
                    $item                    = new \StdClass();
                    $item->id                = $topic->id;
                    $item->subject           = $topic->subject;
                    $response['topiclist'][] = $item;
                }
            }
        }

        // Set the MIME type and header for JSON output.
        $document->setMimeEncoding('application/json');
        Factory::getApplication()->setHeader(
            'Content-Disposition',
            'attachment; filename=categorydisplayraw.json'
            );
        Factory::getApplication()->sendHeaders();

        echo json_encode($response);
    }

    /**
     * Prepare document.
     *
     * @return void
     *
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {

    }
}
