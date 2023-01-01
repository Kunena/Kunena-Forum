<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\Edit;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentTopicControllerFormEditDisplay
 *
 * @since   Kunena 4.0
 */
class TopicFormEditDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Topic/Edit';

    /**
     * Prepare topic edit form.
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $this->catid = $this->input->getInt('catid');
        $mesid       = $this->input->getInt('mesid');
        $saved       = $this->app->getUserState('com_kunena.postfields');

        $this->me        = KunenaUserHelper::getMyself();
        $this->ktemplate = KunenaFactory::getTemplate();
        $this->message   = KunenaMessageHelper::get($mesid);
        $this->message->tryAuthorise('edit');

        $this->topic    = $this->message->getTopic();
        $this->category = $this->topic->getCategory();

        $this->ktemplate->setCategoryIconset($this->topic->getCategory()->iconset);

        if ($this->config->topicIcons && $this->topic->isAuthorised('edit')) {
            $this->topicIcons = $this->ktemplate->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
        }

        if ($this->config->readOnly) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
        }

        $categories        = KunenaCategoryHelper::getCategories();
        $arrayanynomousbox = [];
        $arraypollcatid    = [];

        foreach ($categories as $category) {
            if (!$category->isSection() && $category->allowAnonymous) {
                $arrayanynomousbox[$category->id] = $category->postAnonymous;
            }

            if ($this->config->pollEnabled) {
                if (!$category->isSection() && $category->allowPolls) {
                    $arraypollcatid[$category->id] = 1;
                }
            }
        }

        $this->ktemplate->addScriptOptions('com_kunena.arrayanynomousbox', json_encode($arrayanynomousbox));
        $this->ktemplate->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

        $doc = Factory::getApplication()->getDocument();
        $doc->setMetaData('robots', 'nofollow, noindex');

        foreach ($doc->_links as $key => $value) {
            if (\is_array($value)) {
                if (\array_key_exists('relation', $value)) {
                    if ($value['relation'] == 'canonical') {
                        $canonicalUrl               = $this->topic->getUrl();
                        $doc->_links[$canonicalUrl] = $value;
                        unset($doc->_links[$key]);
                        break;
                    }
                }
            }
        }

        // Run onKunenaPrepare event.
        $params = new Registry();
        $params->set('ksource', 'kunena');
        $params->set('kunena_view', 'topic');
        $params->set('kunena_layout', 'reply');

        PluginHelper::importPlugin('kunena');

        Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);

        $this->action = 'edit';

        // Get attachments.
        $this->attachments = $this->message->getAttachments();

        // Get poll.
        if (
            $this->message->parent == 0
            && $this->topic->isAuthorised(!$this->topic->poll_id ? 'poll.create' : 'poll.edit')
        ) {
            $this->poll = $this->topic->getPoll();
        }

        $this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

        if ($saved) {
            // Update message contents.
            $this->message->edit($saved);
        }

        $finder = new KunenaFinder();
        $finder
            ->filterByMessage($this->message)
            ->where('parent_id', '=', 0)
            ->where('author_id', '=', $this->message->userid)
            ->order('id')
            ->limit(1);
        $privateMessage       = $finder->firstOrNew();
        $privateMessage->body = $saved ? $saved['private'] : $privateMessage->body;

        $this->postAnonymous        = isset($saved['anonymous']) ? $saved['anonymous'] : !empty($this->category->postAnonymous);
        $this->subscriptionsChecked = false;
        $this->canSubscribe         = false;
        $usertopic                  = $this->topic->getUserTopic();

        if ($this->config->allowSubscriptions) {
            $this->canSubscribe = true;
        }

        if ($this->topic->isAuthorised('subscribe') && $this->topic->exists()) {
            if ($usertopic->subscribed == 1 || $this->config->subscriptionsChecked == 1 || $this->category->getSubscribed($this->me->userid)) {
                $this->subscriptionsChecked = true;
            }
        } else {
            $this->canSubscribe = false;
        }

        $this->modified_reason = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';
        $this->app->setUserState('com_kunena.postfields', null);

        $this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;

        $this->editorType = $this->ktemplate->params->get('editorType');

        $this->selectcatlist = false;

        $this->UserCanPostImage = true;

        if ($this->config->new_users_prevent_post_url_images && $this->me->posts < $this->config->minimal_user_posts_add_url_image) {
            $this->UserCanPostImage = false;
        }

        /** @var HtmlDocument $doc */
        $this->doc = Factory::getApplication()->getDocument();
        $this->wa  = $this->doc->getWebAssetManager();
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $menu_item = $this->app->getMenu()->getActive();

        $this->setMetaData('robots', 'nofollow, noindex');

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');
            $params_robots      = $params->get('robots');

            if (!empty($params_title)) {
                $title = $params->get('page_title');
                $this->setTitle($title);
            } else {
                $this->setTitle($this->headerText);
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $this->setDescription($this->headerText);
            }

            if (!empty($params_robots)) {
                $robots = $params->get('robots');
                $this->setMetaData('robots', $robots);
            }
        }
    }

    /**
     * Can user subscribe to the topic?
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function canSubscribe()
    {
        if (
            !$this->me->userid || !$this->config->allowSubscriptions
            || $this->config->topicSubscriptions == 'disabled'
        ) {
            return false;
        }

        if ($this->message->userid != $this->me->userid && $this->me->isModerator()) {
            return false;
        }

        return true;
    }
}
