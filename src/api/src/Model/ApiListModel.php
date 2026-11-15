<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      Model
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\Model;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\ListModel;

/**
 * A list of the REST API of Kunena: the items of a page and their total come from the finders of Kunena (with the permissions of the
 * forum), loaded once. The start is read from the state: ListModel::getStart() asks for the total, which loads the items.
 *
 * @since   Kunena 8.0
 */
abstract class ApiListModel extends ListModel
{
    /**
     * The items of the page, once they are loaded.
     *
     * @var    array|null
     * @since  Kunena 8.0
     */
    protected ?array $apiItems = null;

    /**
     * The number of all the items.
     *
     * @var    integer
     * @since  Kunena 8.0
     */
    protected int $apiTotal = 0;

    /**
     * Load the items of a page and the number of all of them.
     *
     * @param   int  $start  The first item.
     * @param   int  $limit  The number of items.
     *
     * @return  array{0: object[], 1: int}
     *
     * @since   Kunena 8.0
     */
    abstract protected function load(int $start, int $limit): array;

    /**
     * @return  object[]
     *
     * @since   Kunena 8.0
     */
    public function getItems()
    {
        if ($this->apiItems === null) {
            $limit = (int) $this->getState('list.limit', 20);
            $limit = $limit > 0 ? min($limit, 100) : 20;

            [$this->apiItems, $this->apiTotal] = $this->load(max(0, (int) $this->getState('list.start', 0)), $limit);
        }

        return $this->apiItems;
    }

    /**
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public function getTotal()
    {
        $this->getItems();

        return $this->apiTotal;
    }

    /**
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public function getStart()
    {
        return max(0, (int) $this->getState('list.start', 0));
    }
}
