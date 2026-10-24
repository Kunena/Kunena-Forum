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

namespace Kunena\Forum\Site\Controller\Topic\Form\Create;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Class ComponentTopicControllerFormCreateDisplay
 *
 * @since   Kunena 4.0
 */
class TopicFormCreateDisplay extends KunenaControllerDisplay
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $captchaHtml = null;
    public mixed $headerText  = null;
    public mixed $subscribed  = null;

    public mixed $wa = null;

    public mixed $doc = null;

    public mixed $UserCanPostImage = null;

    public mixed $editorType = null;

    public mixed $canSubscribe = null;

    public mixed $subscriptionsChecked = null;

    public mixed $postAnonymous = null;

    public mixed $privateMessage = null;

    public mixed $poll = null;

    public mixed $allowedExtensions = null;

    public mixed $action = null;

    public mixed $selectcatlist = null;

    public mixed $selected = null;

    public mixed $captchaEnabled = null;

    public mixed $topicIcons = null;

    public $message;

    public mixed $topic = null;

    public mixed $category = null;

    public mixed $ktemplate = null;

    public mixed $me = null;

    public mixed $captchaDisplay = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Topic/Edit';

    /**
     * Prepare topic creation form.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $catid = $this->input->getInt('catid', 0);
        $saved = $this->app->getUserState('com_kunena.postfields');

        $Itemid = $this->input->getCmd('Itemid');
        $format = $this->input->getCmd('format');

        if (!$Itemid && $format != 'feed' && $this->config->sefRedirect) {
            try {
                if ($this->config->searchId) {
                    $itemid = $this->config->searchId;
                } else {
                    $menu      = $this->app->getMenu();
                    $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topic&layout=create"));
                    $itemid    = $getid->id;
                }

                if (!$itemid) {
                    $itemid = KunenaRoute::fixMissingItemID();
                }

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'topic',
                    'layout' => 'create',
                    'Itemid' => $itemid,
                ];

                if ($catid) {
                    $params['catid'] = $catid;
                }

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false));
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        $this->me        = KunenaUserHelper::getMyself();
        $this->ktemplate = KunenaFactory::getTemplate();

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

        if ($this->config->readOnly) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
        }

        // FIXME: We need to proxy this...
        $this->ktemplate->addScriptOptions('com_kunena.arrayanynomousbox', json_encode($arrayanynomousbox));
        $this->ktemplate->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

        $this->category                    = KunenaCategoryHelper::get($catid);
        list($this->topic, $this->message) = $this->category->newTopic($saved);

        // When no categoy set, use default icon set
        $iconset = $catid ? $this->topic->getCategory()->iconset : 'default';
        $this->ktemplate->setCategoryIconset($iconset);

        // Get topic icons if they are enabled.
        if ($this->config->topicIcons) {
            $this->topicIcons = $this->ktemplate->getTopicIcons(false, $saved ? $saved['icon_id'] : 0);
        }

        if ($this->topic->isAuthorised('create') && $this->me->canDoCaptcha()) {
            $this->captchaDisplay = $this->ktemplate->getCaptcha();
            $this->captchaEnabled = true;
        } else {
            $this->captchaEnabled = false;
        }

        if (!$this->topic->category_id) {
            throw new KunenaExceptionAuthorise(Text::_(
                'COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS'
            ), $this->me->exists() ? 403 : 401);
        }

        $options        = [];
        $this->selected = $this->topic->category_id;

        if ($this->config->pickupCategory) {
            $options[]      = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_SELECT_CATEGORY'), 'value', 'text');
            $this->selected = '';
        }

        if ($saved) {
            $this->selected = $saved['catid'];
        }

        $catParams = [
            'ordering'    => 'ordering',
            'toplevel'    => 0,
            'sections'    => 0,
            'direction'   => 1,
            'hide_lonely' => 1,
            'action'      => 'topic.create',
        ];

        $this->selectcatlist = HTMLHelper::_(
            'kunenaforum.categorylist',
            'catid',
            $catid,
            $options,
            $catParams,
            'class="form-select inputbox required"',
            'value',
            'text',
            $this->selected,
            'postcatid'
        );

        $this->action = 'post';

        $this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

        if ($arraypollcatid) {
            $this->poll = $this->topic->getPoll();
        }

        $this->privateMessage       = new KunenaPrivateMessage();
        $this->privateMessage->body = $saved ? $saved['private'] : $this->privateMessage->body;

        $this->postAnonymous        = $saved ? $saved['anonymous'] : !empty($this->category->postAnonymous);
        $this->subscriptionsChecked = $saved ? $saved['subscribe'] : $this->config->subscriptionsChecked == 1;

        $this->app->setUserState('com_kunena.postfields', null);

        $this->canSubscribe = $this->canSubscribe();

        $this->headerText = Text::_('COM_KUNENA_NEW_TOPIC');

        $this->editorType = $this->ktemplate->params->get('editorType');

        $this->UserCanPostImage = $this->me->checkUserAllowedLinksImages();

        /** @var HtmlDocument $doc */
        $this->doc = $this->app->getDocument();
        $this->wa  = $this->doc->getWebAssetManager();

        return true;
    }

    /**
     * Can user subscribe to the topic?
     *
     * @return  boolean
     *
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

        return !$this->topic->getUserTopic()->subscribed;
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
}
