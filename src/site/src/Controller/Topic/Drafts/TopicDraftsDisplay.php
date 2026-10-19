<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Drafts;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Draft\KunenaDraftHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class TopicDraftsDisplay: the drafts of the current user.
 *
 * @since   Kunena 7.2
 */
class TopicDraftsDisplay extends KunenaControllerDisplay
{
    /**
     * @var     array
     * @since   Kunena 7.2
     */
    public $drafts = [];

    /**
     * @var     string
     * @since   Kunena 7.2
     */
    protected $name = 'Topic/Drafts';

    /**
     * Load the drafts of the current user.
     *
     * @return  void
     *
     * @throws  KunenaExceptionAuthorise
     * @since   Kunena 7.2
     */
    protected function before()
    {
        parent::before();

        $me = KunenaUserHelper::getMyself();

        if (empty($this->config->enableDrafts)) {
            // Drafts have been switched off in the configuration.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
        }

        if (!$me->exists()) {
            // Drafts belong to a user.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
        }

        $this->drafts = KunenaDraftHelper::getAll($me->userid);
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    protected function prepareDocument()
    {
        $this->setTitle(Text::_('COM_KUNENA_DRAFTS'));
        $this->setDescription(Text::_('COM_KUNENA_DRAFTS'));
    }
}
