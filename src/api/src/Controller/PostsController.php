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

use Joomla\CMS\MVC\Controller\ApiController;
use RuntimeException;

/**
 * The posts of the forum. Read only: the routes of the plugin Web Services - Kunena have the method GET only, and the tasks which change data
 * answer that the method is not allowed.
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
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function add()
    {
        throw new RuntimeException('Method Not Allowed', 405);
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
