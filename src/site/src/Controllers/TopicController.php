<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Http\Transport\StreamTransport;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaFinder;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use stdClass;

/**
 * Kunena Topic Controller
 *
 * @property int catid
 * @property int id
 * @property int mesid
 * @property int return
 *
 * @since   Kunena 2.0
 */
class TopicController extends KunenaController
{
    /**
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->catid  = $this->app->input->getInt('catid', 0);
        $this->return = $this->app->input->getInt('return', $this->catid);
        $this->id     = $this->app->input->getInt('id', 0);
        $this->mesid  = $this->app->input->getInt('mesid', 0);
    }

    /**
     * Get attachments on edit which was attached to a message with AJAX.
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function loadattachments()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $mes_id      = $this->input->getInt('mes_id', 0);
        $attachments = KunenaAttachmentHelper::getByMessage($mes_id);
        $list        = [];

        foreach ($attachments as $attach) {
            $object            = new stdClass();
            $object->id        = $attach->id;
            $object->size      = round($attach->size / '1024', 0);
            $object->name      = $attach->filename;
            $object->protected = $attach->protected;
            $object->folder    = $attach->folder;
            $object->caption   = $attach->caption;
            $object->type      = $attach->filetype;
            $object->path      = $attach->getUrl();
            $object->image     = $attach->isImage();
            $object->inline    = $attach->isInline();
            $list['files'][]   = $object;
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo json_encode($list);

        jexit();
    }

    /**
     * Set inline to 1 on the attachment object.
     *
     * @return void
     * @throws Exception
     * @since Kunena 5.1
     */
    public function setinline()
    {
        $attachs_id = $this->input->getString('files_id', '');
        $attachs_id = json_decode($attachs_id);

        if ($attachs_id === null) {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        $attach_ids_final = [];

        foreach ($attachs_id as $attach) {
            if (\is_array($attach)) {
                $attach_ids_final[] = $attach['0'];
            } else {
                $attach_ids_final[] = $attach;
            }
        }

        $instances = KunenaAttachmentHelper::getById($attach_ids_final, 'none');

        $this->changeinline($instances, '1');
    }

    /**
     * Set inline to 0 or 1 on the attachment object.
     *
     * @return void
     * @throws Exception
     * @since Kunena 5.1
     */
    protected function changeinline($attachments, $inline)
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $response = [];

        if (\is_object($attachments)) {
            $editor_text               = $this->input->get->get('editor_text', '', 'raw');
            $find                      = ['/\[attachment=' . $attachments->id . '\](.*?)\[\/attachment\]/su'];
            $replace                   = '';
            $text                      = preg_replace($find, $replace, $editor_text);
            $response['text_prepared'] = $text;
        } else {
            foreach ($attachments as $instance) {
                $response['result'] = $instance->setInline($inline);
                $response['value']  = $inline;
            }
        }

        unset($attachments);

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if (ob_get_length()) {
            ob_end_clean();
        }

        echo json_encode($response);

        jexit();
    }

    /**
     * Set inline to 0 on one attachment object.
     *
     * @return void
     * @throws Exception
     * @since Kunena 5.1
     */
    public function removeinlineonattachment()
    {
        $attach_id = $this->input->getInt('file_id', 0);

        $instance = KunenaAttachmentHelper::get($attach_id);

        $this->checkpermissions($instance->userid);

        $this->changeinline($instance, '0');
    }

    /**
     * Check permissions.
     *
     * @return void
     * @throws Exception
     * @since Kunena 5.1
     */
    protected function checkpermissions($attachment_userid)
    {
        if (KunenaUserHelper::getMyself()->userid != $attachment_userid || !KunenaUserHelper::getMyself()->isAdmin() || !KunenaUserHelper::getMyself()->isModerator()) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }
    }

    /**
     * Remove files with AJAX.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function removeattachments()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $attach_id = $this->input->getInt('file_id', 0);
        $success   = [];
        $instance  = KunenaAttachmentHelper::get($attach_id);

        if (
            KunenaUserHelper::getMyself()->userid == $instance->userid || KunenaUserHelper::getMyself()->isAdmin()
            || KunenaUserHelper::getMyself()->isModerator()
        ) {
            $editor_text = $this->app->input->get->get('editor_text', '', 'raw');

            $success['text_prepared'] = $instance->removeBBCodeInMessage($editor_text);

            $success['result'] = $instance->delete();
            unset($instance);
        } else {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo json_encode($success);

        jexit();
    }

    /**
     * Load categorytemplate from the category with given catid
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function categorytemplate()
    {
        $catid    = $this->app->input->getInt('catid', 0);

        $category = KunenaCategoryHelper::get($catid);

        // Set the MIME type and header for JSON output.
        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo new JsonResponse($category->topictemplate);

        jexit();
    }

    /**
     * Load global rate for the topic
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function loadrate()
    {
        $user = Factory::getApplication()->getIdentity();

        $topicid  = $this->app->input->get('topic_id', 0, 'int');

        if ($user->id == 0 || KunenaTopicHelper::get($topicid)->first_post_userid == $this->me->userid) {
            $response = KunenaRateHelper::getSelected($topicid);
        } else {
            $response = KunenaRateHelper::getRate($topicid, $user->id);
        }

        // Set the MIME type and header for JSON output.
        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo new JsonResponse($response);

        jexit();
    }

    /**
     * Save rate for user logged in by JSON call
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function setrate()
    {
        $starid   = $this->app->input->get('starid', 0, 'int');
        $topicid  = $this->app->input->get('topic_id', 0, 'int');
        $response = [];
        $user     = KunenaUserHelper::getMyself();

        if ($user->exists() || $this->config->ratingEnabled) {
            $rate           = KunenaRateHelper::get($topicid);
            $rate->stars    = $starid;
            $rate->topic_id = $topicid;

            $response = $rate->save($this->me);

            $selected = KunenaRateHelper::getSelected($topicid);

            $topic         = KunenaTopicHelper::get($topicid);
            $topic->rating = $selected;
            $topic->save();
        }

        // Set the MIME type and header for JSON output.
        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo $response;

        jexit();
    }

    /**
     * Upload files with AJAX.
     *
     * @return  void
     *
     * @throws  null
     * @since   Kunena 6.0
     */
    public function upload()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        $upload = KunenaUpload::getInstance();

        // We are converting all exceptions into JSON.
        try {
            if (!Session::checkToken('request')) {
                throw new RuntimeException(Text::_('Forbidden'), 403);
            }

            $me    = KunenaUserHelper::getMyself();
            $catid = $this->input->getInt('catid', 0);
            $mesid = $this->input->getInt('mesid', 0);

            if ($mesid) {
                $message = KunenaMessageHelper::get($mesid);
                $message->tryAuthorise('attachment.create');
                $category = $message->getCategory();
            } else {
                $category = KunenaCategoryHelper::get($catid);

                if ($category->id) {
                    if (stripos($this->input->getString('mime'), 'image/') !== false) {
                        $category->tryAuthorise('topic.post.attachment.createimage');
                    } else {
                        $category->tryAuthorise('topic.post.attachment.createfile');
                    }
                }
            }

            $caption = $this->input->getString('caption');
            $options = [
                'filename'   => $this->input->getString('filename'),
                'size'       => $this->input->getInt('size'),
                'mime'       => $this->input->getString('mime'),
                'hash'       => $this->input->getString('hash'),
                'chunkStart' => $this->input->getInt('chunkStart', 0),
                'chunkEnd'   => $this->input->getInt('chunkEnd', 0),
                'image_type' => 'attachment',
            ];

            // Upload!
            $upload->addExtensions(KunenaAttachmentHelper::getExtensions($category->id, $me->userid));
            $response = (object) $upload->ajaxUpload($options);

            if (!empty($response->completed)) {
                // We have it all, lets create the attachment.
                $uploadFile = $upload->getProtectedFile();
                list($basename, $extension) = $upload->splitFilename();
                $attachment = new KunenaAttachment();
                $attachment->bind(
                    [
                        'mesid'         => 0,
                        'userid'        => (int) $me->userid,
                        'protected'     => null,
                        'hash'          => $response->hash,
                        'size'          => $response->size,
                        'folder'        => null,
                        'filetype'      => $response->mime,
                        'filename'      => null,
                        'filename_real' => $response->filename,
                        'caption'       => $caption,
                        'inline'        => null,
                    ]
                );

                // Resize image if needed.
                if ($attachment->isImage()) {
                    $imageInfo = KunenaImage::getImageFileProperties($uploadFile);
                    $config    = KunenaConfig::getInstance();

                    if ($imageInfo->width > $config->imageWidth || $imageInfo->height > $config->imageHeight) {
                        // Calculate quality for both JPG and PNG.
                        $quality = $config->imageQuality;

                        if ($quality < 1 || $quality > 100) {
                            $quality = 70;
                        }

                        if ($imageInfo->type == IMAGETYPE_PNG) {
                            $quality = \intval(($quality - 1) / 10);
                        }

                        $image = new KunenaImage($uploadFile);
                        $image = $image->resize($config->imageWidth, $config->imageHeight, false);

                        $options = ['quality' => $quality];
                        $image->toFile($uploadFile, $imageInfo->type, $options);

                        unset($image);

                        $attachment->hash = md5_file($uploadFile);
                        $attachment->size = fileSize($uploadFile);
                    }
                }

                $attachment->saveFile($uploadFile, $basename, $extension, true);

                // Set id and override response variables just in case if attachment was modified.
                $response->id       = $attachment->id;
                $response->hash     = $attachment->hash;
                $response->size     = $attachment->size;
                $response->mime     = $attachment->filetype;
                $response->filename = $attachment->filename_real;
                $response->inline   = $attachment->inline;
            }
        } catch (Exception $response) {
            $upload->cleanup();

            // Use the exception as the response.
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo new JsonResponse($response);

        jexit();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function post()
    {
        $this->id = $this->app->input->getInt('parentid', 0);
        $fields   = [
            'catid'             => $this->catid,
            'name'              => $this->app->input->getString('authorname', $this->me->getName()),
            'email'             => $this->app->input->getString('email', null),
            'subject'           => $this->app->input->post->get('subject', '', 'raw'),
            'message'           => $this->app->input->post->get('message', '', 'raw'),
            'icon_id'           => $this->app->input->getInt('topic_emoticon', null),
            'anonymous'         => $this->app->input->getInt('anonymous', 0),
            'poll_title'        => $this->app->input->getString('poll_title', ''),
            'poll_options'      => $this->app->input->post->get('polloptionsID', [], 'array'),
            'poll_time_to_live' => $this->app->input->getString('poll_time_to_live', 0),
            'subscribe'         => $this->app->input->getInt('subscribeMe', 0),
            'private'           => (string) $this->app->input->getRaw('private'),
            'rating'            => 0,
            'params'            => '',
            'quote'             => 0,
        ];

        $this->app->setUserState('com_kunena.postfields', $fields);

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$this->id) {
            // Create topic
            $category = KunenaCategoryHelper::get($this->catid);

            try {
                $category->isAuthorised('topic.create');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            list($topic, $message) = $category->newTopic($fields);
        } else {
            // Reply topic
            $parent = KunenaMessageHelper::get($this->id);

            try {
                $parent->isAuthorised('reply');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            list($topic, $message) = $parent->newReply($fields);
            $category = $topic->getCategory();
        }

        if ($this->me->canDoCaptcha()) {
            if (PluginHelper::isEnabled('captcha')) {
                $plugin = PluginHelper::getPlugin('captcha');
                $params = new Registry($plugin[0]->params);

                $captcha_pubkey  = $params->get('public_key');
                $captcha_privkey = $params->get('private_key');

                if (!empty($captcha_pubkey) && !empty($captcha_privkey)) {
                    PluginHelper::importPlugin('captcha');

                    $captcha_response = $this->app->input->getString('g-recaptcha-response');

                    if (!empty($captcha_response)) {
                        // For ReCaptcha API 2.0
                        $res = $this->app->triggerEvent('onCheckAnswer', [$this->app->input->getString('g-recaptcha-response')]);
                    }

                    if (!$res[0]) {
                        $this->setRedirectBack();

                        return;
                    }
                }
            }
        }

        $isNew = !$topic->exists();

        // Redirect to full reply instead.
        if ($this->app->input->getString('fullreply')) {
            $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=reply&catid={$fields->catid}&id={$parent->getTopic()->id}&mesid={$parent->id}", false));

            return;
        }

        // Flood protection
        if ($this->config->floodProtection && !$this->me->isModerator($category) && $isNew) {
            $timelimit = Factory::getDate()->toUnix() - $this->config->floodProtection;
            $ip        = KunenaUserHelper::getUserIp();

            $query = $this->db->getQuery(true);
            $query->select('COUNT(*)')
                ->from($this->db->quoteName('#__kunena_messages'))
                ->where('ip=' . $this->db->quote($ip) . ' AND time > ' . $this->db->quote($timelimit));
            $this->db->setQuery($query);

            try {
                $count = $this->db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }

            if ($count) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_POST_TOPIC_FLOOD', $this->config->floodProtection), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // Ignore identical for 5 minutes
        $duplicatetimewindow = Factory::getDate()->toUnix() - 1 * 60;
        $lastTopic           = $topic->getCategory()->getLastTopic();

        if (
            $lastTopic->subject == $topic->subject && $lastTopic->last_post_time >= $duplicatetimewindow
            && $lastTopic->category_id == $topic->category_id && $lastTopic->last_post_id == $topic->last_post_id
            && $lastTopic->id == $topic->id && $lastTopic->last_post_message == $message->message
        ) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'), 'error');

            return $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$topic->getCategory()->id}&id={$lastTopic->id}&mesid={$lastTopic->last_post_id}", false));
        }

        // Set topic icon if permitted
        if ($this->config->topicIcons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null, false)) {
            $topic->icon_id = $fields['icon_id'];
        }

        // Check for guest user if the IP, username or email are blacklisted
        if ($message->getCategory()->allowAnonymous && !$this->me->userid) {
            if ($this->checkIfBlacklisted($message)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_BALCKLISTED'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // Remove IP address
        if (!$this->config->ipTracking) {
            $message->ip = '';
        }

        // If requested: Make message to be anonymous
        if ($fields['anonymous'] && $message->getCategory()->allowAnonymous) {
            $message->makeAnonymous();
        }

        // If configured: Hold posts from guests
        if (!$this->me->userid && $this->config->holdGuestPosts) {
            $message->hold = 1;
        }

        // If configured: Hold posts from users
        if ($this->me->userid && !$this->me->isModerator($category) && $this->me->posts < $this->config->holdNewUsersPosts) {
            $message->hold = 1;
        }

        // Prevent user abort from this point in order to maintain data integrity.
        @ignore_user_abort(true);

        // Mark attachments to be added or deleted.
        $attachments = $this->app->input->get('attachments', [], 'post', 'array');
        $attachment  = $this->app->input->get('attachment', [], 'post', 'array');
        $message->addAttachments(array_keys(array_intersect_key($attachments, $attachment)));
        $message->removeAttachments(array_keys(array_diff_key($attachments, $attachment)));

        // Upload new attachments
        foreach ($_FILES as $key => $file) {
            $intkey = 0;

            if (preg_match('/\D*(\d+)/', $key, $matches)) {
                $intkey = (int) $matches[1];
            }

            if ($file['error'] != UPLOAD_ERR_NO_FILE) {
                $message->uploadAttachment($intkey, $key, $this->catid);
            }
        }

        if ($this->config->urlSubjectTopic) {
            $url_subject = $this->checkURLInSubject($message->subject);

            if ($url_subject) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        if ($this->me->checkUserAllowedLinksImages()) {
            $message->message = $this->removeLinksInMessage($message->message);

            if (!$message->message) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGE_EMPTY_LINKS_IMAGES_REMOVED_NOT_ALLOWED'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // Make sure that message has visible content (text, images or objects) to be shown.
        $text = KunenaParser::parseBBCode($message->message);

        if (!preg_match('!(<img |<object |<iframe )!', $text)) {
            $text = trim(OutputFilter::cleanText($text));
        }

        if (!$text) {
            if (trim($fields['private'])) {
                // Allow empty message if private message part has been filled up.
                $message->message = trim($message->message) ? $message->message : "[PRIVATE={$message->userid}]";
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        $maxlinks = $this->checkMaxLinks($text, $topic);

        if (!$maxlinks) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$this->catid) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Activity integration
        $activity = KunenaFactory::getActivityIntegration();

        if ($message->hold == 0) {
            if (!$topic->exists()) {
                $activity->onBeforePost($message);
            } else {
                $activity->onBeforeReply($message);
            }
        } else {
            $activity->onBeforeHold($message);
        }

        // Save message
        $success = $message->save();

        // Save IP address of user
        if ($this->config->ipTracking) {
            $this->me->ip = $message->ip;
            $this->me->save();
        }

        if ($this->me->isModerator($category) && $this->config->logModeration) {
            KunenaLog::log(
                KunenaLog::TYPE_ACTION,
                $isNew ? KunenaLog::LOG_TOPIC_CREATE : KunenaLog::LOG_POST_CREATE,
                ['mesid' => $message->id, 'parentid' => $this->id],
                $category,
                $topic
            );
        }

        if (!$success) {
            $this->app->enqueueMessage($message->getError(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Message has been sent, we can now clear saved form
        $this->app->setUserState('com_kunena.postfields', null);

        // Display possible warnings (upload failed etc)
        foreach ($message->getErrors() as $warning) {
            $this->app->enqueueMessage($warning, 'error');
        }

        // Create Poll
        $poll_title   = $fields['poll_title'];
        $poll_options = $fields['poll_options'];

        if (!empty($poll_options) && !empty($poll_title)) {
            if ($topic->isAuthorised('poll.create', null, false)) {
                $poll        = $topic->getPoll();
                $poll->title = $poll_title;

                if (!empty($fields['poll_time_to_live'])) {
                    $polltimetolive       = new Date($fields['poll_time_to_live']);
                    $poll->polltimetolive = $polltimetolive->toSql();
                }

                $poll->setOptions($poll_options);

                if (!$poll->save()) {
                    $this->app->enqueueMessage($poll->getError(), 'error');
                } else {
                    $topic->poll_id = $poll->id;
                    $topic->save();
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'), 'success');
                }
            } else {
                $this->app->enqueueMessage($topic->getError(), 'error');
            }
        }

        // Post Private message
        $this->postPrivate($message);

        $message->sendNotification();

        // Now try adding any new subscriptions if asked for by the poster

        $usertopic = $topic->getUserTopic();

        if ($fields['subscribe'] && !$usertopic->subscribed) {
            if ($topic->subscribe(1)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'), 'success');

                // Activity integration
                $activity = KunenaFactory::getActivityIntegration();
                $activity->onAfterSubscribe($topic, 1);
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'error');
            }
        }

        if ($message->hold == 1) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCES_REVIEW'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_POSTED'), 'success');
        }

        $category = KunenaCategoryHelper::get($this->return);

        if ($message->isAuthorised('read', null, false) && $this->id) {
            $this->setRedirect($message->getUrl($category, false));
        } elseif ($topic->isAuthorised('read', null, false)) {
            $this->setRedirect($topic->getUrl($category, false));
        } else {
            $this->setRedirect($category->getUrl(null, false));
        }
    }

    /**
     * Check if the IP, username or email address given are blacklisted
     *
     * @param   string  $message  message
     *
     * @return  boolean
     *
     * @since   Kunena 6
     */
    protected function checkIfBlacklisted($message)
    {
        $ip    = $message->ip;
        $name  = $message->name;
        $email = $message->email;

        // Prepare the request to stopforumspam
        if (KunenaUserHelper::isIPv6($message->ip)) {
            $ip = '[' . $message->ip . ']';
        }

        $data = 'ip=' . $ip;

        if (!empty($name)) {
            $data .= '&username=' . $name;
        }

        if (!empty($email)) {
            $data .= '&email=' . $email;
        }

        $options = new Registry();

        $transport = new StreamTransport($options);

        // Create a 'stream' transport.
        $http = new Http($options, $transport);

        $response = $http->post('https://api.stopforumspam.org/api', $data . '&json');

        if ($response->code == '200') {
            // The query has worked
            $result = json_decode($response->body);

            if ($result->success) {
                if ($result->ip->appears) {
                    return true;
                } elseif (!empty($result->username)) {
                    if ($result->username->appears) {
                        return true;
                    } else {
                        return false;
                    }
                } elseif (!empty($result->email)) {
                    if ($result->email->appears) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                // TODO : log the result or display something in debug mode

                return false;
            }
        } else {
            // The query has failed or has been refused

            // TODO : log the result or display something in debug mode

            return false;
        }
    }

    /**
     * Check if title of topic or message contains URL to limit part of spam
     *
     * @param   string  $subject  subject
     *
     * @return  boolean
     *
     * @internal param string $usbject
     *
     * @since    Kunena 6.0
     */
    protected function checkURLInSubject($subject)
    {
        if ($this->config->urlSubjectTopic) {
            preg_match_all('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $subject, $matches);

            $ignore = false;

            foreach ($matches as $match) {
                if (!empty($match)) {
                    $ignore = true;
                }
            }

            return $ignore;
        }

        return true;
    }

    /**
     * Remove links in message content
     *
     * @param $text
     *
     * @return array|string|string[]|null
     * @since Kunena 5.2.0
     */
    protected function removeLinksInMessage($text)
    {
        $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $text);
        $text = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $text);

        // When the bbcode urls and images are removed just remove the others links
        $text = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $text);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SAVED_WITHOUT_LINKS_AND_IMAGES'), 'success');

        return $text;
    }

    /**
     * Check in the text the max links
     *
     * @param   string  $text   text
     * @param   object  $topic  topic
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function checkMaxLinks($text, $topic)
    {
        $category = $topic->getCategory();

        if ($this->me->isAdmin() || $this->me->isModerator($category)) {
            return true;
        }

        preg_match_all('/<div class=\"kunena_ebay_widget\"(.*?)>(.*?)<\/div>/s', $text, $ebay_matches);

        $ignore = false;

        foreach ($ebay_matches as $match) {
            if (!empty($match)) {
                $ignore = true;
            }
        }

        preg_match_all('/<div id=\"kunena_twitter_widget\"(.*?)>(.*?)<\/div>/s', $text, $twitter_matches);

        foreach ($twitter_matches as $match) {
            if (!empty($match)) {
                $ignore = true;
            }
        }

        if (!$ignore) {
            preg_match_all('@\(((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)\)@', $text, $matches);

            if (empty($matches[0])) {
                preg_match_all("/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU", $text, $matches);
            }

            $countlink = \count($matches[0]);

            // Ignore internal links
            foreach ($matches[1] as $link) {
                $uri  = Uri::getInstance($link);
                $host = $uri->getHost();

                // The cms will catch most of these well
                if (empty($host) || Uri::isInternal($link)) {
                    $countlink--;
                }
            }

            if (!$topic->isAuthorised('approve') && $countlink >= $this->config->maxLinks + 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save private data from message
     *
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function postPrivate(KunenaMessage $message)
    {
        if (!$this->me->userid) {
            return;
        }

        $body      = (string) $this->input->getRaw('private');
        $attachIds = $this->input->get('attachment_private', [], 'array');

        if (!trim($body) && !$attachIds) {
            return;
        }

        $moderator          = $this->me->isModerator($message->getCategory());
        $parent             = $message->getParent();
        $author             = $message->getAuthor();
        $pAuthor            = $parent->getAuthor();
        $private            = new KunenaPrivateMessage();
        $private->author_id = $author->userid;
        $private->subject   = $message->subject;
        $private->body      = $body;

        // Attach message.
        $private->posts()->add($message->id);

        // Attach author of the message.
        if ($author->exists()) {
            $private->users()->add($author->userid);
        }

        if ($pAuthor->exists() && ($moderator || $pAuthor->isModerator($message->getCategory()))) {
            // Attach receiver (but only if moderator either posted or replied parent post).
            if ($pAuthor->exists()) {
                $private->users()->add($pAuthor->userid);
            }
        }

        $private->attachments()->setMapped($attachIds);

        try {
            $private->save();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }

        KunenaLog::log(
            KunenaLog::TYPE_ACTION,
            KunenaLog::LOG_PRIVATE_POST_CREATE,
            ['id' => $private->id, 'mesid' => $message->id],
            $message->getCategory(),
            $message->getTopic(),
            $pAuthor
        );
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function edit()
    {
        $this->id = $this->app->input->getInt('mesid', 0);

        $message = KunenaMessageHelper::get($this->id);
        $topic   = $message->getTopic();
        $fields  = [
            'name'              => $this->app->input->getString('authorname', $message->name),
            'email'             => $this->app->input->getString('email', $message->email),
            'subject'           => $this->app->input->post->get('subject', '', 'raw'),
            'message'           => $this->app->input->post->get('message', '', 'raw'),
            'modified_reason'   => $this->app->input->getString('modified_reason', $message->modified_reason),
            'icon_id'           => $this->app->input->getInt('topic_emoticon', $topic->icon_id),
            'anonymous'         => $this->app->input->getInt('anonymous', 0),
            'poll_title'        => $this->app->input->getString('poll_title', null),
            'poll_options'      => $this->app->input->get('polloptionsID', [], 'post', 'array'),
            'poll_time_to_live' => $this->app->input->getString('poll_time_to_live', 0),
            'subscribe'         => $this->app->input->getInt('subscribeMe', 0),
            'private'           => (string) $this->input->getRaw('private'),
            'params'            => '',
        ];

        if (!Session::checkToken('post')) {
            $this->app->setUserState('com_kunena.postfields', $fields);
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        try {
            $message->isAuthorised('edit');
        } catch (Exception $e) {
            $this->app->setUserState('com_kunena.postfields', $fields);
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Load language file from the template.
        KunenaFactory::getTemplate()->loadLanguage();

        // Update message contents
        $message->edit($fields);

        // If requested: Make message to be anonymous
        if ($fields['anonymous'] && $message->getCategory()->allowAnonymous) {
            $message->makeAnonymous();
        }

        // Prevent user abort from this point in order to maintain data integrity.
        @ignore_user_abort(true);

        // Mark attachments to be added or deleted.
        $attachments = $this->app->input->get('attachments', [], 'post', 'array');
        $attachment  = $this->app->input->get('attachment', [], 'post', 'array');

        $addList    = array_keys(array_intersect_key($attachments, $attachment));
        $addList    = ArrayHelper::toInteger($addList);
        $removeList = array_keys(array_diff_key($attachments, $attachment));
        $removeList = ArrayHelper::toInteger($removeList);

        $message->addAttachments($addList);
        $message->removeAttachments($removeList);

        // Upload new attachments
        foreach ($_FILES as $key => $file) {
            $intkey = 0;

            if (preg_match('/\D*(\d+)/', $key, $matches)) {
                $intkey = (int) $matches[1];
            }

            if ($file['error'] != UPLOAD_ERR_NO_FILE) {
                $message->uploadAttachment($intkey, $key, $this->catid);
            }
        }

        $url_subject = $this->checkURLInSubject($message->subject);

        if ($url_subject && $this->config->urlSubjectTopic) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Set topic icon if permitted
        if ($this->config->topicIcons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null)) {
            $topic->icon_id = $fields['icon_id'];
        }

        // Check if we are editing first post and update topic if we are!
        if ($topic->first_post_id == $message->id || KunenaConfig::getInstance()->allowChangeSubject && $topic->first_post_userid == $message->userid || KunenaUserHelper::getMyself()->isModerator()) {
            $topic->subject = $fields['subject'];
        }

        if ($this->me->checkUserAllowedLinksImages()) {
            $message->message = $this->removeLinksInMessage($message->message);

            if (!$message->message) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGE_EMPTY_LINKS_IMAGES_REMOVED_NOT_ALLOWED'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // If user removed all the text and message doesn't contain images or objects, delete the message instead.
        $text = KunenaParser::parseBBCode($message->message);

        if (!preg_match('!(<img |<object |<iframe )!', $text)) {
            $text = trim(OutputFilter::cleanText($text));
        }

        if (!$text && $this->config->userDeleteMessage == 1) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');

            return;
        } elseif (!$text) {
            if (trim($fields['private'])) {
                // Allow empty message if private message part has been filled up.
                $message->message = trim($message->message) ? $message->message : "[PRIVATE={$message->userid}]";
            } else {
                // Reload message (we don't want to change it).
                $message->load();

                try {
                    $message->publish(KunenaForum::DELETED);
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }

                if ($message->publish(KunenaForum::DELETED)) {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'), 'success');
                }

                $this->setRedirect($message->getUrl($this->return, false));

                return;
            }
        }

        $maxlinks = $this->checkMaxLinks($text, $topic);

        if (!$maxlinks) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Activity integration
        $activity = KunenaFactory::getActivityIntegration();
        $activity->onBeforeEdit($message);

        // Save message
        try {
            $message->save();
        } catch (Exception $e) {
            $this->app->setUserState('com_kunena.postfields', $fields);
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        $isMine = $this->me->userid == $message->userid;

        if ($this->config->logModeration) {
            KunenaLog::log(
                $isMine ? KunenaLog::TYPE_ACTION : KunenaLog::TYPE_MODERATION,
                KunenaLog::LOG_POST_EDIT,
                ['mesid' => $message->id, 'reason' => $fields['modified_reason']],
                $topic->getCategory(),
                $topic,
                !$isMine ? $message->getAuthor() : null
            );
        }

        // Display possible warnings (upload failed etc)
        foreach ($message->getErrors() as $warning) {
            $this->app->enqueueMessage($warning, 'error');
        }

        $subscribe = $this->app->input->getInt('subscribeMe');
        $usertopic = $topic->getUserTopic();

        if ($topic->isAuthorised('subscribe')) {
            if ($subscribe) {
                $usertopic->subscribed = 1;
            } else {
                $usertopic->subscribed = 0;
            }

            $usertopic->save();
        }

        $poll_title = $fields['poll_title'];

        if ($poll_title !== null) {
            // Save changes into poll
            $poll_options = $fields['poll_options'];
            $poll         = $topic->getPoll();

            if (!empty($poll_options) && !empty($poll_title)) {
                $poll->title = $poll_title;

                if (!empty($fields['poll_time_to_live'])) {
                    $polltimetolive       = new Date($fields['poll_time_to_live']);
                    $poll->polltimetolive = $polltimetolive->toSql();
                }

                $poll->setOptions($poll_options);

                if (!$topic->poll_id) {
                    // Create a new poll
                    if (!$topic->isAuthorised('poll.create')) {
                        $this->app->enqueueMessage($topic->getError(), 'error');
                    } elseif (!$poll->save()) {
                        $this->app->enqueueMessage($poll->getError(), 'error');
                    } else {
                        $topic->poll_id = $poll->id;
                        $topic->save();
                        $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'), 'success');
                    }
                } else {
                    if ($this->config->allowEditPoll || (!$this->config->allowEditPoll && !$poll->getUserCount())) {
                        // Edit existing poll
                        if (!$topic->isAuthorised('poll.edit')) {
                            $this->app->enqueueMessage($topic->getError(), 'error');
                        } elseif (!$poll->save()) {
                            $this->app->enqueueMessage($poll->getError(), 'error');
                        } else {
                            $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_EDITED'), 'success');
                        }
                    }
                }
            } elseif ($poll->exists() && $topic->isAuthorised('poll.edit')) {
                // Delete poll
                if (!$topic->isAuthorised('poll.delete')) {
                    // Error: No permissions to delete poll
                    $this->app->enqueueMessage($topic->getError(), 'error');
                } elseif (!$poll->delete()) {
                    $this->app->enqueueMessage($poll->getError(), 'error');
                } else {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_DELETED'), 'success');
                }
            }
        }

        // Edit Private message.
        $this->editPrivate($message);

        $activity->onAfterEdit($message);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_EDIT'), 'success');

        if ($message->hold == 1) {
            // If user cannot approve message by himself, send email to moderators.
            if (!$topic->isAuthorised('approve')) {
                $message->sendNotification();
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_MODERATED'), 'success');
        }

        // Redirect edit first message when category is under review
        if ($message->hold == 1 && $message->getCategory()->review && $topic->first_post_id == $message->id && !$this->me->isModerator()) {
            $this->setRedirect($message->getCategory()->getUrl($this->return, false));
        } else {
            $this->setRedirect($message->getUrl($this->return, false));
        }
    }

    /**
     * Load private data information when edit message
     *
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function editPrivate(KunenaMessage $message)
    {
        if (!$this->me->userid) {
            return;
        }

        $body      = (string) $this->input->getRaw('private');
        $attachIds = $this->input->get('attachment_private', [], 'array');
        $finder    = new KunenaFinder();
        $finder
            ->filterByMessage($message)
            ->where('parent_id', '=', 0)
            ->where('author_id', '=', $message->userid)
            ->order('id')
            ->limit(1);
        $private = $finder->firstOrNew();

        if (!$private->exists()) {
            $this->postPrivate($message);

            return;
        }

        $private->subject = $message->subject;
        $private->body    = $body;

        if (!empty($attachIds)) {
            $private->attachments()->setMapped($attachIds);
        }

        if (!$private->body && !$private->attachments) {
            try {
                $private->delete();
            } catch (Exception $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        try {
            $private->save();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function thankyou()
    {
        $type = $this->app->input->getString('task');
        $this->setThankyou($type);
    }

    /**
     * @param   string  $type  type
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function setThankyou($type)
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message = KunenaMessageHelper::get($this->mesid);

        if (!$message->isAuthorised($type)) {
            $this->app->enqueueMessage($message->getError(), 'error');
            $this->setRedirectBack();

            return;
        }

        $category            = KunenaCategoryHelper::get($this->catid);
        $thankyou            = KunenaMessageThankyouHelper::get($this->mesid);
        $activityIntegration = KunenaFactory::getActivityIntegration();

        if ($type == 'thankyou') {
            try {
                $thankyou->save($this->me);
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_THANKYOU_SUCCESS'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_ACTION,
                    KunenaLog::LOG_POST_THANKYOU,
                    ['mesid' => $message->id],
                    $category,
                    $message->getTopic(),
                    $message->getAuthor()
                );
            }

            $activityIntegration->onAfterThankyou($this->me->userid, $message->userid, $message);
        } else {
            $userid = $this->app->input->getInt('userid', '0');

            try {
                $thankyou->delete($userid);
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_THANKYOU_REMOVED_SUCCESS'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    KunenaLog::LOG_POST_UNTHANKYOU,
                    ['mesid' => $message->id, 'userid' => $userid],
                    $category,
                    $message->getTopic(),
                    $message->getAuthor()
                );
            }

            $activityIntegration->onAfterUnThankyou($this->me->userid, $userid, $message);
        }

        $this->setRedirect($message->getUrl($category->exists() ? $category->id : $message->catid, false));
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function unthankyou()
    {
        $type = $this->app->input->getString('task');
        $this->setThankyou($type);
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function subscribe()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if ($topic->isAuthorised('read') && $topic->subscribe(1)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSubscribe($topic, 1);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function unsubscribe()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if ($topic->isAuthorised('read')) {
            try {
                $topic->subscribe(0);
            } catch (Exception $e) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'error');
                $this->setRedirectBack();
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNSUBSCRIBED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSubscribe($topic, 0);
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function favorite()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if ($topic->isAuthorised('read') && $topic->favorite(1)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_FAVORITED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterFavorite($topic, 1);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC') . ' ' . $topic->getError(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function unfavorite()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if ($topic->isAuthorised('read') && $topic->favorite(0)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNFAVORITED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterFavorite($topic, 0);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC') . ' ' . $topic->getError(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function sticky()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if (!$topic->isAuthorised('sticky')) {
            $this->app->enqueueMessage($topic->getError(), 'error');
        } elseif ($topic->sticky(1)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_SET'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    KunenaLog::LOG_TOPIC_STICKY,
                    [],
                    $topic->getCategory(),
                    $topic
                );
            }

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSticky($topic, 1);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_NOT_SET'), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function unsticky()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if (!$topic->isAuthorised('sticky')) {
            $this->app->enqueueMessage($topic->getError(), 'error');
        } elseif ($topic->sticky(0)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_UNSET'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    KunenaLog::LOG_TOPIC_UNSTICKY,
                    [],
                    $topic->getCategory(),
                    $topic
                );
            }

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSticky($topic, 0);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_NOT_UNSET'), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function lock()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if (!$topic->isAuthorised('lock')) {
            $this->app->enqueueMessage($topic->getError(), 'error');
        } elseif ($topic->lock(1)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_SET'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    KunenaLog::LOG_TOPIC_LOCK,
                    [],
                    $topic->getCategory(),
                    $topic
                );
            }

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterLock($topic, 1);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_NOT_SET'), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function unlock()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);

        if (!$topic->isAuthorised('lock')) {
            $this->app->enqueueMessage($topic->getError(), 'error');
        } elseif ($topic->lock(0)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_UNSET'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    KunenaLog::LOG_TOPIC_UNLOCK,
                    [],
                    $topic->getCategory(),
                    $topic
                );
            }

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterLock($topic, 0);
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_NOT_UNSET'), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function delete()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($this->mesid) {
            // Delete message
            $message = $target = KunenaMessageHelper::get($this->mesid);
            $topic   = $message->getTopic();
            $log     = KunenaLog::LOG_POST_DELETE;
            $hold    = KunenaForum::DELETED;
            $msg     = Text::_('COM_KUNENA_POST_SUCCESS_DELETE');
        } else {
            // Delete topic
            $topic = $target = KunenaTopicHelper::get($this->id);
            $log   = KunenaLog::LOG_TOPIC_DELETE;
            $hold  = KunenaForum::TOPIC_DELETED;
            $msg   = Text::_('COM_KUNENA_TOPIC_SUCCESS_DELETE');
        }

        $category = $topic->getCategory();

        if ($target->isAuthorised('delete') && $target->publish($hold)) {
            if ($this->config->logModeration) {
                KunenaLog::log(
                    $this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                    $log,
                    isset($message) ? ['mesid' => $message->id] : [],
                    $category,
                    $topic
                );
            }

            $this->app->enqueueMessage($msg, 'success');
        } else {
            $this->app->enqueueMessage($target->getError(), 'error');
        }

        if (!$target->isAuthorised('read')) {
            if ($target instanceof KunenaMessage && $target->getTopic()->isAuthorised('read')) {
                $target = $target->getTopic();
                $target = KunenaMessageHelper::get($target->last_post_id);
            } else {
                $target = $target->getCategory();
            }
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function undelete()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($this->mesid) {
            // Undelete message
            $message = $target = KunenaMessageHelper::get($this->mesid);
            $topic   = $message->getTopic();
            $log     = KunenaLog::LOG_POST_UNDELETE;
            $msg     = Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
        } else {
            // Undelete topic
            $topic = $target = KunenaTopicHelper::get($this->id);
            $log   = KunenaLog::LOG_TOPIC_UNDELETE;
            $msg   = Text::_('COM_KUNENA_TOPIC_SUCCESS_UNDELETE');
        }

        $category = $topic->getCategory();

        if ($target->isAuthorised('undelete') && $target->publish(KunenaForum::PUBLISHED)) {
            if ($this->config->logModeration) {
                KunenaLog::log(
                    $this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                    $log,
                    isset($message) ? ['mesid' => $message->id] : [],
                    $category,
                    $topic
                );
            }

            $this->app->enqueueMessage($msg, 'success');
        } else {
            $this->app->enqueueMessage($target->getError(), 'error');
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function permdelete()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($this->mesid) {
            // Delete message
            $message = $target = KunenaMessageHelper::get($this->mesid);
            $log     = KunenaLog::LOG_POST_DESTROY;
            $topic   = KunenaTopicHelper::get($target->getTopic());

            if ($topic->attachments > 0) {
                $topic->attachments = $topic->attachments - 1;
                $topic->save(false);
            }
        } else {
            // Delete topic
            $topic = $target = KunenaTopicHelper::get($this->id);
            $log   = KunenaLog::LOG_TOPIC_DESTROY;
        }

        $category = $topic->getCategory();

        if ($topic->isAuthorised('permdelete') && $target->delete()) {
            if ($this->config->logModeration) {
                KunenaLog::log(
                    $this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                    $log,
                    isset($message) ? ['mesid' => $message->id] : [],
                    $category,
                    $topic
                );
            }

            if ($topic->exists()) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'), 'success');
                $url = $topic->getUrl($this->return, false);
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SUCCESS_DELETE'), 'success');
                $url = $topic->getCategory()->getUrl($this->return, false);
            }
        } else {
            $this->app->enqueueMessage($target->getError(), 'error');
        }

        if (isset($url)) {
            $this->setRedirect($url);
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function approve()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Load language file from the template.
        KunenaFactory::getTemplate()->loadLanguage();

        if ($this->mesid) {
            // Approve message
            $target  = KunenaMessageHelper::get($this->mesid);
            $message = $target;
            $log     = KunenaLog::LOG_POST_APPROVE;
        } else {
            // Approve topic
            $target  = KunenaTopicHelper::get($this->id);
            $message = KunenaMessageHelper::get($target->first_post_id);
            $log     = KunenaLog::LOG_TOPIC_APPROVE;
        }

        $topic    = $message->getTopic();
        $category = $topic->getCategory();

        if ($target->isAuthorised('approve') && $target->publish(KunenaForum::PUBLISHED)) {
            if ($this->config->logModeration) {
                KunenaLog::log(
                    $this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                    $log,
                    ['mesid' => $message->id],
                    $category,
                    $topic,
                    $message->getAuthor()
                );
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'), 'success');

            // Only email if message wasn't modified by the author before approval
            // TODO: this is just a workaround for #1862, we need to find better solution.

            $modifiedByAuthor = ($message->modified_by == $message->userid);

            if (!$modifiedByAuthor) {
                $target->sendNotification(null, true);
            }
        } else {
            $this->app->enqueueMessage($target->getError(), 'error');
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function move()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topicId        = $this->app->input->getInt('id', 0);
        $messageId      = $this->app->input->getInt('mesid', 0);
        $targetCategory = $this->app->input->getInt('targetcategory', 0);
        $targetTopic    = $this->app->input->getInt('targettopic', 0);

        if ($targetTopic < 0) {
            $targetTopic = $this->app->input->getInt('targetid', 0);
        }

        if ($messageId) {
            $message = $object = KunenaMessageHelper::get($messageId);
            $topic   = $message->getTopic();
        } else {
            $topic   = $object = KunenaTopicHelper::get($topicId);
            $message = KunenaMessageHelper::get($topic->first_post_id);
        }

        if ($targetTopic) {
            $target = KunenaTopicHelper::get($targetTopic);
        } else {
            $target = KunenaCategoryHelper::get($targetCategory);
        }

        $error        = null;
        $targetobject = null;

        if (!$object->isAuthorised('move')) {
            $error = $object->getError();
        } elseif (!$target->isAuthorised('read')) {
            $error = $target->getError();
        } else {
            $changesubject  = $this->app->input->getBool('changesubject', false);
            $subject        = $this->app->input->getString('subject', '');
            $shadow         = $this->app->input->getBool('shadow', false);
            $topic_emoticon = $this->app->input->getInt('topic_emoticon', null);
            $keep_poll      = $this->app->input->getInt('keep_poll', false);

            if ($object instanceof KunenaMessage) {
                $mode = $this->app->input->getWord('mode', 'selected');

                switch ($mode) {
                    case 'newer':
                        $ids = new Date($object->time);
                        break;
                    case 'selected':
                    default:
                        $ids = $object->id;
                        break;
                }
            } else {
                $ids = false;
            }

            $targetobject = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll);

            if (!$targetobject) {
                $error = $topic->getError();
            }

            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    $messageId ? KunenaLog::LOG_POST_MODERATE : KunenaLog::LOG_TOPIC_MODERATE,
                    [
                        'move'    => ['id' => $topicId, 'mesid' => $messageId, 'mode' => isset($mode) ? $mode : 'topic'],
                        'target'  => ['category_id' => $targetCategory, 'topic_id' => $targetTopic],
                        'options' => ['emo' => $topic_emoticon, 'subject' => $subject, 'changeAll' => $changesubject, 'shadow' => $shadow],
                    ],
                    $topic->getCategory(),
                    $topic,
                    $message->getAuthor()
                );
            }
        }

        if ($error) {
            $this->app->enqueueMessage($error, 'error');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE'), 'success');
        }

        if ($targetobject) {
            $this->setRedirect($targetobject->getUrl($this->return, false, 'last'));
        } else {
            $this->setRedirect($topic->getUrl($this->return, false, 'first'));
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function report()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$this->me->exists() || $this->config->reportMsg == 0) {
            // Deny access if report feature has been disabled or user is guest
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'warning');
            $this->setRedirectBack();

            return;
        }

        if (!$this->config->get('sendEmails')) {
            // Emails have been disabled
            $this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_DISABLED'), 'warning');
            $this->setRedirectBack();

            return;
        }

        if (!$this->config->getEmail() || !MailHelper::isEmailAddress($this->config->getEmail())) {
            // Error: email address is invalid
            $this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_INVALID'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Get target object for the report
        if ($this->mesid) {
            $message = $target = KunenaMessageHelper::get($this->mesid);
            $topic   = $target->getTopic();
            $log     = KunenaLog::LOG_POST_REPORT;
        } else {
            $topic   = $target = KunenaTopicHelper::get($this->id);
            $message = KunenaMessageHelper::get($topic->first_post_id);
            $log     = KunenaLog::LOG_TOPIC_REPORT;
        }

        if (!$target->isAuthorised('read')) {
            // Deny access if user cannot read target
            $this->app->enqueueMessage($target->getError(), 'error');
            $this->setRedirectBack();

            return;
        }

        $reason = $this->app->input->getString('reason');
        $text   = $this->app->input->getString('text');

        $template = KunenaTemplate::getInstance();

        if (method_exists($template, 'reportMessage')) {
            $template->reportMessage($message, $reason, $text);
        }

        // Load language file from the template.
        KunenaFactory::getTemplate()->loadLanguage();

        if (empty($reason) && empty($text)) {
            // Do nothing: empty subject or reason is empty
            $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_FORG0T_SUB_MES'), 'error');
            $this->setRedirectBack();

            return;
        } else {
            $acl         = KunenaAccess::getInstance();
            $emailToList = $acl->getSubscribers($topic->category_id, $topic->id, false, true, false);

            if (!empty($emailToList)) {
                $mailnamesender = !empty($this->config->email_sender_name) ? MailHelper::cleanAddress($this->config->email_sender_name) : MailHelper::cleanAddress($this->config->boardTitle . ': ' . $this->me->getName());
                $mailsubject    = "[" . $this->config->boardTitle . " " . Text::_('COM_KUNENA_FORUM') . "] " . Text::_('COM_KUNENA_REPORT_MSG') . ": ";

                if ($reason) {
                    $mailsubject .= $reason;
                } else {
                    $mailsubject .= $topic->subject;
                }

                $msglink = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $target->getPermaUrl(null, false);

                $mail = Factory::getMailer();
                $mail->setSender([$this->config->getEmail(), $mailnamesender]);
                $mail->setSubject($mailsubject);
                $mail->addReplyTo($this->me->email, $this->me->username);

                // Render the email.
                $layout = KunenaLayout::factory('Email/Report')->debug(false)
                    ->set('mail', $mail)
                    ->set('message', $message)
                    ->set('me', $this->me)
                    ->set('title', $reason)
                    ->set('content', $text)
                    ->set('messageLink', $msglink);

                try {
                    $body = trim($layout->render());
                    $mail->setBody($body);
                } catch (Exception $e) {
                    // TODO: display exception
                }

                $receivers = [];

                foreach ($emailToList as $emailTo) {
                    if (!MailHelper::isEmailAddress($emailTo->email)) {
                        continue;
                    } else {
                        $receivers[] = $emailTo->email;
                    }
                }

                KunenaEmail::send($mail, $receivers);

                if ($this->config->logModeration) {
                    KunenaLog::log(
                        KunenaLog::TYPE_REPORT,
                        $log,
                        [
                            'mesid'   => $message->id,
                            'reason'  => $reason,
                            'message' => $text,
                        ],
                        $topic->getCategory(),
                        $topic,
                        $message->getAuthor()
                    );
                }

                $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_SUCCESS'), 'success');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_NOT_SEND'), 'success');
            }
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function vote()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $vote  = $this->app->input->getInt('kpollradio', '');
        $id    = $this->app->input->getInt('id', 0);

        $topic = KunenaTopicHelper::get($id);
        $poll  = $topic->getPoll();

        if (!$topic->isAuthorised('poll.vote')) {
            $this->app->enqueueMessage($topic->getError(), 'error');
        } elseif (!$poll->getMyVotes()) {
            // Give a new vote
            $success = $poll->vote($vote);

            if (!$success) {
                $this->app->enqueueMessage($poll->getError(), 'error');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_SUCCESS'), 'success');
            }
        } elseif (!$this->config->pollAllowVoteOne) {
            // Change existing vote
            $success = $poll->vote($vote, true);

            if (!$success) {
                $this->app->enqueueMessage($poll->getError(), 'error');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_CHANGED_SUCCESS'), 'success');
            }
        }

        $this->setRedirect($topic->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function resetvotes()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic = KunenaTopicHelper::get($this->id);
        $topic->resetvotes();

        if ($this->config->logModeration) {
            KunenaLog::log(
                KunenaLog::TYPE_MODERATION,
                KunenaLog::LOG_POLL_MODERATE,
                [],
                $topic->getCategory(),
                $topic,
                null
            );
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_RESET_SUCCESS'), 'success');
        $this->setRedirect($topic->getUrl($this->return, false));
    }
}
