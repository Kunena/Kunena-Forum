<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      Controller
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\Controller;

\defined('_JEXEC') or die();

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\CMS\MVC\Controller\Exception\ResourceNotFound;
use Joomla\CMS\MVC\Controller\Exception\Save;
use Joomla\CMS\Response\JsonResponse;
use Kunena\Forum\Libraries\Api\KunenaApiReply;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * The posts of the forum: read them, and post a reply (POST, with the API token of a user). A post is not changed or deleted through
 * the API: these tasks answer that the method is not allowed.
 *
 * @since   Kunena 8.0
 */
class PostsController extends ApiController
{
    /**
     * @var    string
     * @since  Kunena 8.0
     */
    protected $contentType = 'posts';

    /**
     * @var    string
     * @since  Kunena 8.0
     */
    protected $default_view = 'posts';

    /**
     * The list, with its filter (filter[topic]).
     *
     * @return  static
     *
     * @since   Kunena 8.0
     */
    public function displayList()
    {
        $filter = (array) $this->input->get('filter', [], 'array');

        if (\array_key_exists('topic', $filter)) {
            $this->modelState->set('filter.topic', (int) $filter['topic']);
        }

        return parent::displayList();
    }

    /**
     * @param   mixed  $id  Not used.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function delete($id = null)
    {
        throw new RuntimeException('Method Not Allowed', 405);
    }

    /**
     * Posts a reply of the user of the API token, with the rules of the form of the site (KunenaApiReply). The answer is the post (201),
     * or 202 when the reply waits for a moderator (it is not shown yet).
     *
     * @return  void
     *
     * @throws  NotAllowed|ResourceNotFound|Save  The status of the error: 403, 404, or 400, 409 and 429.
     * @since   Kunena 8.0
     */
    public function add()
    {
        KunenaFactory::loadLanguage('com_kunena');
        KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

        try {
            $message = KunenaApiReply::reply(
                KunenaUserHelper::getMyself(),
                KunenaApiReply::fields(json_decode((string) $this->input->json->getRaw(), true))
            );
        } catch (RuntimeException $e) {
            throw match ($e->getCode()) {
                403      => new NotAllowed($e->getMessage(), 403),
                404      => new ResourceNotFound($e->getMessage(), 404),
                409, 429 => new Save($e->getMessage(), $e->getCode()),
                default  => new Save($e->getMessage(), 400),
            };
        }

        if ($message->hold) {
            $this->app->setHeader('status', 202, true);
            $this->app->setHeader('Content-Type', 'application/json');
            $this->app->sendHeaders();

            echo new JsonResponse(['id' => (int) $message->id, 'status' => 'held'], Text::_('COM_KUNENA_POST_SUCCES_REVIEW'));

            $this->app->close();
        }

        $this->app->setHeader('status', 201, true);
        $this->displayItem((int) $message->id);
    }

    /**
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function edit()
    {
        throw new RuntimeException('Method Not Allowed', 405);
    }
}
