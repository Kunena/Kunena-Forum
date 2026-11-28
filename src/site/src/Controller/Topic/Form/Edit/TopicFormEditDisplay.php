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

namespace Kunena\Forum\Site\Controller\Topic\Form\Edit;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Event\KunenaPrepareEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
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
    protected mixed $name = 'Topic/Edit';

    public mixed $wa = null;

    public mixed $doc = null;

    public mixed $UserCanPostImage = null;

    public mixed $selectcatlist = null;

    public mixed $editorType = null;

    public mixed $modified_reason = null;

    public mixed $canSubscribe = null;

    public mixed $subscriptionsChecked = null;

    public mixed $postAnonymous = null;

    public mixed $privateMessage = null;

    public mixed $allowedExtensions = null;

    public mixed $attachments = null;

    public mixed $action = null;

    public mixed $topicIcons = null;

    public mixed $category = null;

    public mixed $topic = null;

    public $message;

    public mixed $ktemplate = null;

    public mixed $me = null;

    public mixed $catid = null;

    public mixed $poll = null;

    public mixed $unsentFields = null;

    /**
     * Prepare topic edit form.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $this->catid = $this->input->getInt('catid');
        $mesid       = $this->input->getInt('mesid');
        $saved       = $this->app->getUserState('com_kunena.postfields');

        // Only the fields of an edit of this message: the session has the fields of the last form which was refused, a reply (a
        // quick reply refused by the flood protection) or the edit of another message, which took the place of the text of this one,
        // and a save wrote them over it.
        if ($saved && (int) ($saved['mesid'] ?? 0) !== $mesid) {
            $saved = null;
            $this->app->setUserState('com_kunena.postfields', null);
        }

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

        $this->ktemplate->addScriptOptions('com_kunena.arrayanynomousbox', $arrayanynomousbox);
        $this->ktemplate->addScriptOptions('com_kunena.pollcategoriesid', $arraypollcatid);

        $doc = $this->app->getDocument();
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

        $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
            'context' => 'kunena.topic',
            'subject' => $this->topic,
            'params'  => $params,
            'page'    => 0,
        ]);
        $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);

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

        // The private part of the message, as the save finds it (TopicController::editPrivate()): also of a message which was made
        // anonymous (its user is 0, the private part keeps its author).
        $finder = new KunenaPrivateMessageFinder();
        $finder
            ->filterByMessage($this->message)
            ->where('parent_id', '=', 0)
            ->order('id')
            ->limit(1);
        $this->privateMessage       = $finder->firstOrNew();
        $this->privateMessage->body = $saved ? $saved['private'] : $this->privateMessage->body;

        // The post exists: "anonymous by default" is for a new post, the edit of a post of a member does not tick the box.
        $this->postAnonymous        = isset($saved['anonymous']) ? $saved['anonymous'] : false;
        $this->subscriptionsChecked = false;
        $this->canSubscribe         = false;
        $usertopic                  = $this->topic->getUserTopic();

        if ($this->config->allowSubscriptions) {
            $this->canSubscribe = true;
        }

        if ($this->topic->isAuthorised('subscribe') && $this->topic->exists()) {
            // Checked when the user follows the topic now (or its category): the default of the forum checked it always, and the
            // save of a small correction subscribed again a user who had left the topic.
            if ($usertopic->subscribed == 1 || $this->category->getSubscribed($this->me->userid)) {
                $this->subscriptionsChecked = true;
            }
        } else {
            $this->canSubscribe = false;
        }

        $this->modified_reason = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';

        // Fields restored after a failed post exist only in this form; a draft keeps its own copy.
        $this->unsentFields = !empty($saved) && $this->input->getInt('draftid', 0) === 0;

        $this->app->setUserState('com_kunena.postfields', null);

        $this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;

        $this->editorType = $this->ktemplate->params->get('editorType');

        $this->selectcatlist = false;

        $this->UserCanPostImage = $this->me->checkUserAllowedLinksImages();

        /** @var HtmlDocument $doc */
        $this->doc = $this->app->getDocument();
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
