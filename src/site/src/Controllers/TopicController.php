<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Http\HttpFactory;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Email\KunenaMailTemplate;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Draft\KunenaDraftHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Response\KunenaResponseJson;
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
    private const int STOPFORUMSPAM_CACHE_TTL = 600;

    private const int STOPFORUMSPAM_TIMEOUT = 3;

    public mixed $catid = null;

    public mixed $return = null;

    public $id;

    public mixed $mesid = null;

    /**
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->catid  = $this->app->getInput()->getInt('catid', 0);
        $this->return = $this->app->getInput()->getInt('return', $this->catid);

        // A list in the request (catid[]=1) is no category.
        $this->catid  = \is_array($this->catid) ? 0 : $this->catid;
        $this->return = \is_array($this->return) ? 0 : $this->return;
        $this->id     = $this->app->getInput()->getInt('id', 0);
        $this->mesid  = $this->app->getInput()->getInt('mesid', 0);
    }

    /**
     * Get attachments on edit which was attached to a message with AJAX.
     *
     * @return  void
     *
     * @throws  Exception
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

        // Check if the user is allowed to read the message with the given mes_id
        $message = KunenaMessageHelper::get($mes_id);

        if (!$message->isAuthorised('read')) {
            throw new RuntimeException(Text::_('Not allowed'), 403);
        }

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
        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $attachs_id = $this->input->getString('files_id', '');
        $attachs_id = json_decode($attachs_id);

        if (!\is_array($attachs_id)) {
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

        // Only the owner of an attachment, or a moderator, may change it.
        foreach ($instances as $instance) {
            $this->checkpermissions($instance);
        }

        $this->changeinline($instances, '1');
    }

    /**
     * Set private on the attachment object.
     *
     * @return void
     * @throws Exception
     * @since Kunena 6.3
     */
    public function setprivate()
    {
        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('JINVALID_TOKEN'), 403);
        }

        if (!$this->me->exists()) {
            throw new RuntimeException(Text::_('COM_KUNENA_LOGIN_NOTIFICATION'), 401);
        }

        $attachs_id = $this->input->getString('files_id', '');
        $attachs_id = json_decode($attachs_id);

        if (!\is_array($attachs_id)) {
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

        foreach ($attach_ids_final as $id) {
            $attachment = KunenaAttachmentHelper::get($id);

            if ($attachment->isAuthorised('private')) {
                $attachment->protected = KunenaAttachment::PROTECTION_PRIVATE;
                $attachment->save();
            }
        }
    }

    /**
     * Set inline to 0 or 1 on the attachment object.
     *
     * @param   KunenaAttachment|KunenaAttachment[]  $attachments  The attachment, or the attachments.
     * @param   integer                              $inline       1 to show the attachment in the text, 0 not.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 5.1
     */
    protected function changeinline($attachments, $inline)
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
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
        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $attach_id = $this->input->getInt('file_id', 0);

        $instance = KunenaAttachmentHelper::get($attach_id);

        $this->checkpermissions($instance);

        // changeinline() gives back the text of the editor for one attachment, it does not change the attachment.
        $instance->setInline(0);

        $this->changeinline($instance, '0');
    }

    /**
     * Check that the user may change or remove the attachment: the rule of the attachment itself (its uploader when he is not
     * banned, or a moderator of the category of its message).
     *
     * @param   KunenaAttachment  $attachment  The attachment.
     *
     * @return void
     * @throws Exception
     * @since Kunena 5.1
     */
    protected function checkpermissions(KunenaAttachment $attachment)
    {
        if (!$attachment->isAuthorised('delete')) {
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

        $attach_id = $this->input->getInt('files_id_delete', 0);
        $success   = [];
        $instance  = KunenaAttachmentHelper::get($attach_id);

        // The rule of the attachment itself: its uploader (not banned) or a moderator of the category of its message.
        if ($instance->isAuthorised('delete')) {
            $editor_text = $this->app->getInput()->get->get('editor_text', '', 'raw');

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
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $catid    = $this->app->getInput()->getInt('catid', 0);

        $category = KunenaCategoryHelper::get($catid);

        // The template is the start of a new topic: only for a category in which the user may write one (not of a category
        // which he may not even see).
        $template = $category->exists() && $category->isAuthorised('topic.create') ? (string) $category->topictemplate : '';

        // Set the MIME type and header for JSON output.
        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo new KunenaResponseJson($template);

        jexit();
    }

    /**
     * Find the topics with a similar subject while a new topic is written (JSON).
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function similar()
    {
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $topics = [];
        $params = KunenaFactory::getTemplate()->params;

        if ($params->get('similarTopics', 0)) {
            $subject = $this->app->getInput()->getString('subject', '');

            // The words of the subject, without the very short ones (the, a, is, ...).
            $words = array_unique(array_filter(
                preg_split('/[\s\p{P}]+/u', mb_strtolower($subject), -1, PREG_SPLIT_NO_EMPTY),
                static fn ($word) => mb_strlen($word) >= 3
            ));
            $words = \array_slice($words, 0, 6);

            if ($words) {
                $finder = new KunenaTopicFinder();
                $finder->filterByUserAccess($this->me)
                    ->filterByHold([0])
                    ->filterByMoved(false)
                    ->filterBySimilarSubject($words)
                    ->order('last_post_time', -1)
                    ->limit(30);

                if (KunenaMultilanguage::isEnabled()) {
                    $finder->filterByLanguage(KunenaMultilanguage::getCurrent());
                }

                $found = [];

                foreach ($finder->find() as $topic) {
                    $subjectLower = mb_strtolower($topic->subject);
                    $matches      = 0;

                    foreach ($words as $word) {
                        if (mb_strpos($subjectLower, $word) !== false) {
                            $matches++;
                        }
                    }

                    $found[] = ['matches' => $matches, 'time' => $topic->last_post_time, 'topic' => $topic];
                }

                // The most similar first, the most recent first when they are equal.
                usort($found, static fn ($a, $b) => [$b['matches'], $b['time']] <=> [$a['matches'], $a['time']]);

                foreach (\array_slice($found, 0, (int) $params->get('similarTopicsLimit', 5)) as $item) {
                    $topic    = $item['topic'];
                    $topics[] = [
                        'id'       => (int) $topic->id,
                        'subject'  => $topic->subject,
                        'url'      => $topic->getUrl(),
                        'category' => $topic->getCategory()->name,
                        'replies'  => max(0, (int) $topic->posts - 1),
                    ];
                }
            }
        }

        // Set the MIME type and header for JSON output.
        header('Content-type: application/json');
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo new KunenaResponseJson($topics);

        jexit();
    }

    /**
     * Report the author of a message to stopforumspam.com (admins and moderators): the user name, the e-mail address and the IP
     * address of the author, with the address and the start of the message as evidence.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function reportspam()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message  = KunenaMessageHelper::get($this->mesid);
        $category = $message->exists() ? $message->getCategory() : null;
        $author   = $message->userid ? KunenaUserHelper::get($message->userid) : null;

        // Only admins and moderators, only for the message of another user who is no admin or moderator, and only with the key.
        if (
            !$category || empty($this->config->stopForumSpamKey) || !$this->me->exists() || !$this->me->isModerator($category)
            || $message->userid == $this->me->userid || ($author && $author->isModerator())
        ) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORTSPAM_NOT_ALLOWED'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ip = (string) $message->ip;

        if ($ip === '') {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_NO_IP_GIVEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        // A private or reserved address is of no use for the service.
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) && !$this->config->debug) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORTSPAM_PRIVATE_IP'), 'error');
            $this->setRedirectBack();

            return;
        }

        // The evidence: where the message is, and the start of it.
        $url      = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $message->getPermaUrl(null, false);
        $text     = trim(preg_replace('/\s+/', ' ', strip_tags(KunenaParser::stripBBCode((string) $message->message, 0, false))));
        $evidence = $url . ' - ' . (\function_exists('mb_substr') ? mb_substr($text, 0, 800) : substr($text, 0, 800));

        $data = [
            'username'         => $author ? $author->username : (string) $message->name,
            'ip'               => $ip,
            'email'            => $author ? $author->email : (string) $message->email,
            'stopForumSpamKey' => $this->config->stopForumSpamKey,
            'evidence'         => $evidence,
        ];

        try {
            $result = KunenaUserHelper::storeCheckStopforumspam($data, 'add');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($result && $result->getStatusCode() == 200) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_SUCCESS'), 'success');
        } else {
            // The reason of a refusal is in the answer of the service.
            $reasons = [];
            preg_match('/<p>.*<\/p>/', $result ? (string) $result->getBody() : '', $reasons);
            $reason = isset($reasons[0]) ? strip_tags($reasons[0]) : ($result ? 'HTTP ' . $result->getStatusCode() : 'no answer');
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_STOPFORUMSPAM_REPORT_FAILED', $reason), 'error');
        }

        $this->setRedirectBack();
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
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $user = $this->app->getIdentity();

        $topicid  = $this->app->getInput()->get('topic_id', 0, 'int');
        $topic    = KunenaTopicHelper::get($topicid);

        // The rating of a topic which the user may not see (unapproved, deleted, in a closed category) is not given.
        if (!$topic->isAuthorised('read')) {
            throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 403);
        }

        if ($user->id == 0 || $topic->first_post_userid == $this->me->userid) {
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

        echo new KunenaResponseJson($response);

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
        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('JINVALID_TOKEN'), 403);
        }
        $starid  = $this->app->getInput()->get('starid', 0, 'int');
        $topicid = $this->app->getInput()->get('topic_id', 0, 'int');

        // Use the category of the topic, not the one sent by the client.
        $ratedTopic = KunenaTopicHelper::get($topicid);

        if (!$ratedTopic->exists()) {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        // The rule of the rating of a topic: the topic can be read and is not locked.
        if (!$ratedTopic->isAuthorised('rate')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $catid = $ratedTopic->category_id;

        // To use rating feature it should be enabled at same time in Kunena configuration and in the category
        if (!$this->config->ratingEnabled || !KunenaCategoryHelper::get($catid)->allowRatings) {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        $response = [];

        if ($this->me->exists()) {
            $rate           = KunenaRateHelper::get($topicid);
            $rate->rate     = $starid;
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

        // The answer is JSON: the object of the rating, or the list (empty for a guest).
        echo \is_array($response) ? json_encode($response) : $response;

        jexit();
    }

    /**
     * Upload files with AJAX.
     *
     * @return  void
     *
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

            $catid = $this->input->getInt('catid', 0);
            $mesid = $this->input->getInt('mesid', 0);

            if ($mesid) {
                $message = KunenaMessageHelper::get($mesid);
                // The actions of a message are attachment.createimage and attachment.createfile ("attachment.create" is no
                // action: it gave an error 500 for every upload with the number of a message).
                $message->tryAuthorise(stripos($this->input->getString('mime'), 'image/') !== false ? 'attachment.createimage' : 'attachment.createfile');
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
            $upload->addExtensions(KunenaAttachmentHelper::getExtensions($category->id, $this->me->userid));
            $response = (object) $upload->ajaxUpload($options);

            if (!empty($response->completed)) {
                // We have it all, lets create the attachment.
                $uploadFile                 = $upload->getProtectedFile();
                list($basename, $extension) = $upload->splitFilename();
                $attachment                 = new KunenaAttachment();
                $attachment->bind(
                    [
                        'mesid'         => 0,
                        'userid'        => (int) $this->me->userid,
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

                    if ($imageInfo->width > $this->config->imageWidth || $imageInfo->height > $this->config->imageHeight) {
                        // Calculate quality for both JPG and PNG.
                        $quality = $this->config->imageQuality;

                        if ($quality < 1 || $quality > 100) {
                            $quality = 70;
                        }

                        if ($imageInfo->type == IMAGETYPE_PNG) {
                            $quality = \intval(($quality - 1) / 10);
                        }

                        $image = new KunenaImage($uploadFile);
                        $image->correctImageOrientation();
                        $image->resize($this->config->imageWidth, $this->config->imageHeight, false);

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

        echo new KunenaResponseJson($response);

        jexit();
    }

    /**
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function post()
    {
        // The editor has a second submit button which keeps the message as a draft instead of posting it.
        if ($this->app->getInput()->post->getInt('savedraft', 0)) {
            $this->savedraft();

            return;
        }

        $this->id = $this->app->getInput()->getInt('parentid', 0);
        $fields   = [
            'catid'             => $this->catid,
            'name'              => $this->app->getInput()->getString('authorname', $this->me->getName()),
            'email'             => $this->app->getInput()->getString('email', null),
            'subject'           => $this->postedText('subject'),
            'message'           => $this->postedText('message'),
            'icon_id'           => $this->app->getInput()->getInt('topic_emoticon', null),
            'anonymous'         => $this->app->getInput()->getInt('anonymous', 0),
            'poll_title'        => $this->app->getInput()->getString('poll_title', ''),
            'poll_options'      => $this->app->getInput()->post->get('polloptionsID', [], 'array'),
            'poll_time_to_live' => $this->app->getInput()->getString('poll_time_to_live', 0),
            'subscribe'         => $this->app->getInput()->getInt('subscribeMe', 0),
            'private'           => (string) $this->app->getInput()->getRaw('message_private'),
            'rating'            => 0,
            'params'            => '',
            'quote'             => 0,
        ];

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$this->catid) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'), 'error');
            $this->setRedirectBack();

            return;
        }

        $this->app->setUserState('com_kunena.postfields', $fields);

        // Need to do to the replacement in case of VBA code is inserted without the bbcode tags [code][/code]
        // Optimized: only perform replacement if the pattern exists in the message
        if (strpos($fields['message'], '"",""') !== false) {
            $fields['message'] = str_replace('"",""', '"", ""', $fields['message']);
        }

        if (!$this->id) {
            // Create topic
            $category = KunenaCategoryHelper::get($this->catid);

            try {
                $category->tryAuthorise('topic.create');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            list($topic, $message) = $category->newTopic($fields);

            // On a multilingual site a topic has a language: that of its category when the category has one, else the
            // chosen one (or all languages).
            if (KunenaMultilanguage::isEnabled()) {
                // The template decides if the language is chosen (pick), the one of the page (hide) or the one of the category.
                $mode = KunenaFactory::getTemplate()->params->get('topicLanguage', 'pick');

                if ($mode === 'hide') {
                    $chosen = KunenaMultilanguage::getCurrent();
                } elseif ($mode === 'category') {
                    $chosen = $category->language ?: KunenaMultilanguage::ALL;
                } else {
                    $chosen = $this->app->getInput()->post->getString('topic_language', KunenaMultilanguage::ALL);
                }

                if ($category->language && $category->language !== KunenaMultilanguage::ALL) {
                    $chosen = $category->language;
                } elseif (!\in_array($chosen, array_keys(KunenaMultilanguage::getLanguages()), true)) {
                    $chosen = KunenaMultilanguage::ALL;
                }

                $topic->language = $chosen;
            }
        } else {
            // Reply topic
            $parent = KunenaMessageHelper::get($this->id);

            try {
                $parent->tryAuthorise('reply');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            list($topic, $message) = $parent->newReply($fields);
            $category              = $topic->getCategory();
        }

        if ($this->me->canDoCaptcha()) {
            $app    = Factory::getApplication();
            $plugin = $app->get('captcha', '0');

            if ($plugin !== '0' && $plugin !== '' && $plugin !== null) {
                if (PluginHelper::isEnabled('captcha', $plugin)) {
                    try {
                        $captcha = Captcha::getInstance((string) $plugin, ['namespace' => 'kunena_captcha']);

                        if (!$captcha->checkAnswer($app->getInput()->post->getString('captcha', ''))) {
                            $this->setRedirectBack();

                            return;
                        }
                    } catch (RuntimeException $e) {
                        $app->enqueueMessage($e->getMessage(), 'error');
                        $this->setRedirectBack();

                        return;
                    }
                }
            }
        }

        $isNew = !$topic->exists();

        // Redirect to full reply instead.
        if ($this->app->getInput()->getString('fullreply')) {
            if (isset($parent)) {
                $fullUrl = "index.php?option=com_kunena&view=topic&layout=reply&catid={$this->catid}&id={$parent->getTopic()->id}&mesid={$parent->id}";
            } else {
                // There is no topic to reply to: open the form for a new topic.
                $fullUrl = "index.php?option=com_kunena&view=topic&layout=create&catid={$this->catid}";
            }

            $this->setRedirect(KunenaRoute::_($fullUrl, false));

            return;
        }

        $dateNowUnix = Factory::getDate()->toUnix();

        // Flood protection
        if ($this->config->floodProtection && !$this->me->isModerator($category) && $isNew) {
            $timelimit = $dateNowUnix - $this->config->floodProtection;
            $ip        = KunenaUserHelper::getUserIp();

            $query = $this->db->createQuery();
            $query->select('COUNT(*)')
                ->from($this->db->quoteName('#__kunena_messages'))
                ->where('ip=' . $this->db->quote($ip) . ' AND time > ' . $this->db->quote($timelimit));
            $this->db->setQuery($query);

            $count = 0;

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
        $duplicatetimewindow = $dateNowUnix - 1 * 60;
        $lastTopic           = $topic->getCategory()->getLastTopic();

        if (
            $lastTopic->subject == $topic->subject && $lastTopic->last_post_time >= $duplicatetimewindow
            && $lastTopic->category_id == $topic->category_id && $lastTopic->last_post_id == $topic->last_post_id
            && $lastTopic->id == $topic->id && $lastTopic->last_post_message == $message->message
        ) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'), 'error');

            return $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$lastTopic->id}&mesid={$lastTopic->last_post_id}", false));
        }

        // Set topic icon if permitted
        if ($this->config->topicIcons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null, false)) {
            $topic->icon_id = $fields['icon_id'];
        }

        // Check for guest user if the IP, username or email are blacklisted
        if ($category->allowAnonymous && !$this->me->userid) {
            if ($this->checkIfBlacklisted($message)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_BLACKLISTED'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // Remove IP address
        if (!$this->config->ipTracking) {
            $message->ip = '';
        }

        // If requested: Make message to be anonymous
        if ($fields['anonymous'] && $category->allowAnonymous) {
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
        $attachments = $this->app->getInput()->get('attachments', [], 'post', 'array');
        $attachment  = $this->app->getInput()->get('attachment', [], 'post', 'array');
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

        // Mark attachments being attached to this message as active.
        foreach ($message->getAttachmentsToAdd() as $attachmentToAdd) {
            $attachmentToAdd->status = 1;

            try {
                $attachmentToAdd->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
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

        if (!$this->me->checkUserAllowedLinksImages()) {
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
                // Allow empty message if private message part has been filled up by setting a timestapm to avoid the identical message error.
                $date             = new Date();
                $message->message = trim($message->message) ? $message->message : "[PRIVATE={$message->userid}{$date->getTimestamp()}]";
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
        try {
            $message->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // The tags of a new topic.
        if ($isNew && KunenaTopicTags::isEnabled()) {
            KunenaTopicTags::save((int) $topic->id, $this->app->getInput()->post->get('topic_tags', [], 'array'));
        }

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

        // Message has been sent, we can now clear saved form
        $this->app->setUserState('com_kunena.postfields', null);

        // Create Poll
        $poll_title   = $fields['poll_title'];
        $poll_options = $fields['poll_options'];

        $pollAllowed = !empty($poll_options) && !empty($poll_title);

        if ($pollAllowed) {
            try {
                $topic->tryAuthorise('poll.create');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $pollAllowed = false;
            }
        }

        if ($pollAllowed) {
            $poll        = $topic->getPoll();
            $poll->title = $poll_title;

            if (!empty($fields['poll_time_to_live'])) {
                $poll->polltimetolive = $this->pollEndDate((string) $fields['poll_time_to_live']) ?? $poll->polltimetolive;
            }

            $poll->setOptions($poll_options);
            $pollCreated = true;

            try {
                $poll->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $pollCreated = false;
            }

            $topic->poll_id = $poll->id;

            try {
                $topic->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $pollCreated = false;
            }

            if ($pollCreated) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'), 'success');
            }
        }

        // Removed orphaned attachments (one has added on the form and removed after before to submit)
        $this->removeOrphanedAttachments();

        // Post Private message
        try {
            $this->postPrivate($message);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        $message->processNotification();

        // The users who are mentioned in the message get an e-mail.
        $message->sendMentionNotifications();

        // Now try adding any new subscriptions if asked for by the poster
        $usertopic = $topic->getUserTopic();

        if ($fields['subscribe'] && !$usertopic->subscribed) {
            try {
                $topic->subscribe(1);

                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'), 'success');

                // Activity integration
                $activity = KunenaFactory::getActivityIntegration();
                $activity->onAfterSubscribe($topic, 1);
            } catch (Exception $e) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $e->getMessage(), 'error');
            }
        }

        if ($message->hold == 1) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCES_REVIEW'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_POSTED'), 'success');
        }

        // A message which was posted from a draft is not a draft anymore.
        if ($draftId = $this->app->getInput()->post->getInt('draftid', 0)) {
            KunenaDraftHelper::delete($draftId, $this->me->userid);
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
     * Remove orhpaned attachments when submit (which have mesid=0).
     *
     * @return  void
     *
     * @throws  Exception
     *
     * @since   Kunena 6.4
     */
    protected function removeOrphanedAttachments()
    {
        $params['file']  = '1';
        $params['image'] = '1';
        $params['mesid'] = '0';

        $attachments = KunenaAttachmentHelper::getByUserid($this->me, $params);

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
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
        $ip    = trim((string) $message->ip);
        $name  = trim((string) $message->name);
        $email = trim((string) $message->email);

        // Prepare the request to stopforumspam
        if (KunenaUserHelper::isIPv6($ip)) {
            $ip = '[' . $ip . ']';
        }

        $url         = 'https://api.stopforumspam.org/api';
        $requestData = [
            'ip' => $ip,
        ];

        if (!empty($name)) {
            $requestData['username'] = $name;
        }

        if (!empty($email)) {
            $requestData['email'] = $email;
        }

        // Keep a stable field order so identical lookups share the same cache key.
        ksort($requestData);

        $payload = json_encode($requestData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            return false;
        }

        $cacheKey = 'stopforumspam.' . sha1($payload);
        $options  = [
            'defaultgroup' => 'com_kunena_stopforumspam',
            'caching'      => true,
            'lifetime'     => self::STOPFORUMSPAM_CACHE_TTL,
        ];
        $cache    = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('callback', $options);

        return (bool) $cache->get(
            function () use ($payload, $url) {
                $options = new Registry();
                $options->set('userAgent', KunenaForum::version());

                $http = (new HttpFactory())->getHttp($options);

                try {
                    $response = $http->post($url, $payload, [
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ], self::STOPFORUMSPAM_TIMEOUT);
                } catch (RuntimeException $e) {
                    return false;
                }

                if ($response->getStatusCode() !== 200) {
                    return false;
                }

                $result = json_decode((string) $response->getBody(), true);

                if (!\is_array($result) || empty($result['success'])) {
                    return false;
                }

                return (isset($result['ip']['appears']) && $result['ip']['appears'])
                    || (isset($result['username']['appears']) && $result['username']['appears'])
                    || (isset($result['email']['appears']) && $result['email']['appears']);
            },
            [],
            $cacheKey
        );
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

        preg_match_all('/<div id=\"kunena_x_widget\"(.*?)>(.*?)<\/div>/s', $text, $x_social_matches);

        foreach ($x_social_matches as $match) {
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

        $body      = (string) $this->input->getRaw('message_private');
        $attachIds = $this->input->get('attachment_private', [], 'array');

        if (!trim($body) && !$attachIds) {
            return;
        }

        $attachIds          = isset($attachIds[0]) && $attachIds[0] !== '' ? explode(',', $attachIds[0]) : [];
        $attachIds          = $this->ownPrivateAttachments($attachIds, $message);
        $moderator          = $this->me->isModerator($message->getCategory());
        $parent             = $message->getParent();
        $author             = $message->getAuthor();
        $pAuthor            = $parent->getAuthor();
        $private            = new KunenaPrivateMessage();
        $private->author_id = $author->userid;
        $private->subject   = $message->subject;

        if (!trim($body)) {
            $private->body      = Text::_('COM_KUNENA_POST_WITH_PRIVATE_ATTACHMENTS_SAVED');
        } else {
            $private->body      = $body;
        }

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
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function edit()
    {
        // The editor has a second submit button which keeps the changes as a draft instead of saving them.
        if ($this->app->getInput()->post->getInt('savedraft', 0)) {
            $this->savedraft();

            return;
        }

        $this->id = $this->app->getInput()->getInt('mesid', 0);

        $message = KunenaMessageHelper::get($this->id);
        $topic   = $message->getTopic();
        $fields  = [
            'name'              => $this->app->getInput()->getString('authorname', $message->name),
            'email'             => $this->app->getInput()->getString('email', $message->email),
            'subject'           => $this->postedText('subject'),
            'message'           => $this->postedText('message'),
            'modified_reason'   => $this->app->getInput()->getString('modified_reason', $message->modified_reason),
            'icon_id'           => $this->app->getInput()->getInt('topic_emoticon', $topic->icon_id),
            'anonymous'         => $this->app->getInput()->getInt('anonymous', 0),
            'poll_title'        => $this->app->getInput()->getString('poll_title', null),
            'poll_options'      => $this->app->getInput()->get('polloptionsID', [], 'array'),
            'poll_time_to_live' => $this->app->getInput()->getString('poll_time_to_live', 0),
            'subscribe'         => $this->app->getInput()->getInt('subscribeMe', 0),
            'private'           => (string) $this->input->getRaw('private_message'),
            'params'            => '',
        ];

        if (!Session::checkToken('post')) {
            $this->app->setUserState('com_kunena.postfields', $fields);
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        try {
            $message->tryAuthorise('edit');
        } catch (Exception $e) {
            $this->app->setUserState('com_kunena.postfields', $fields);
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Load language file from the template.
        KunenaFactory::getTemplate()->loadLanguage();

        // Update message contents (the old text: a user who was mentioned before does not get a second e-mail)
        $oldMessageText = (string) $message->message;
        $message->edit($fields);
        $message->sendMentionNotifications($oldMessageText, false);

        // If requested: Make message to be anonymous
        if ($fields['anonymous'] && $message->getCategory()->allowAnonymous) {
            $message->makeAnonymous();
        }

        // Prevent user abort from this point in order to maintain data integrity.
        @ignore_user_abort(true);

        // Mark attachments to be added or deleted.
        $attachments = $this->app->getInput()->get('attachments', [], 'post', 'array');
        $attachment  = $this->app->getInput()->get('attachment', [], 'post', 'array');

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

        // Mark attachments being attached to this message as active.
        foreach ($message->getAttachmentsToAdd() as $attachmentToAdd) {
            $attachmentToAdd->status = 1;

            try {
                $attachmentToAdd->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
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
        if ($topic->first_post_id == $message->id || $this->config->allowChangeSubject && $topic->first_post_userid == $message->userid || $this->me->isModerator()) {
            $topic->subject = $fields['subject'];
        }

        if (!$this->me->checkUserAllowedLinksImages()) {
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

                $deleted = false;

                try {
                    $deleted = $message->publish(KunenaForum::DELETED);
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }

                if ($deleted) {
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

        // The tags of the topic are changed with its first message, by the author of the topic or a moderator.
        if (
            KunenaTopicTags::isEnabled() && $topic->first_post_id == $message->id
            && $this->app->getInput()->post->getInt('topic_tags_present', 0)
            && ($this->me->userid == $topic->first_post_userid || $this->me->isModerator($topic->getCategory()))
        ) {
            KunenaTopicTags::save((int) $topic->id, $this->app->getInput()->post->get('topic_tags', [], 'array'));
        }

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

        $subscribe = $this->app->getInput()->getInt('subscribeMe');

        // Loaded again: the save of the message has counted the posts of the user in the topic anew (a message which was made
        // anonymous is no longer his), and the save below writes all the values of the row.
        $usertopic = KunenaTopicUserHelper::get($topic, null, true);

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
                    $poll->polltimetolive = $this->pollEndDate((string) $fields['poll_time_to_live']) ?? $poll->polltimetolive;
                }

                $poll->setOptions($poll_options);

                if (!$topic->poll_id) {
                    // Create a new poll
                    try {
                        $topic->tryAuthorise('poll.create');
                        $poll->save();
                        $topic->poll_id = $poll->id;
                        $topic->save();
                        $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'), 'success');
                    } catch (Exception $e) {
                        $this->app->enqueueMessage($e->getMessage(), 'error');
                    }
                } else {
                    if ($this->config->allowUserEditPoll || (!$this->config->allowUserEditPoll && !$poll->getUserCount())) {
                        // Edit existing poll
                        try {
                            $topic->tryAuthorise('poll.edit');
                            $poll->save();
                            $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_EDITED'), 'success');
                        } catch (Exception $e) {
                            $this->app->enqueueMessage($e->getMessage(), 'error');
                        }
                    }
                }
            } elseif ($poll->exists() && $topic->isAuthorised('poll.edit')) {
                // Delete poll
                try {
                    $topic->tryAuthorise('poll.delete');
                    $poll->delete();
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_DELETED'), 'success');
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        // Removed orphaned attachments (one has added on the form and removed after before to submit)
        $this->removeOrphanedAttachments();

        // Edit Private message.
        $this->editPrivate($message);

        $activity->onAfterEdit($message);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_EDIT'), 'success');

        // A message which was saved from a draft is not a draft anymore.
        if ($draftId = $this->app->getInput()->post->getInt('draftid', 0)) {
            KunenaDraftHelper::delete($draftId, $this->me->userid);
        }

        if ($message->hold == 1) {
            // If user cannot approve message by himself, send email to moderators.
            if (!$topic->isAuthorised('approve')) {
                $message->processNotification();
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
    /**
     * The end of a poll, from the free text field of the poll dialog: a date and time which PHP reads, or a date in the format of
     * the date picker (the end of that day). A text which is no date is no end (and a message says so): it was an error of the
     * date parser, after the topic was saved already.
     *
     * @param   string  $value  The text of the field.
     *
     * @return  string|null  The end as SQL date, null for a text which is no date.
     *
     * @since   Kunena 8.0
     */
    protected function pollEndDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return (new Date($value))->toSql();
        } catch (Exception $e) {
            $date = KunenaDate::fromPicker($value);

            if ($date !== null) {
                return (new Date($date . ' 23:59:59'))->toSql();
            }
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_ERROR_END_DATE'), 'warning');

        return null;
    }

    /**
     * The files which may go into the private part of a message: the files of the message itself, and the files which the user
     * has just uploaded (not yet in a message). The numbers come from the form: a file of somebody else would become a private
     * file of this message (gone for the readers of its own message, readable for the users of this private part).
     *
     * @param   array          $ids      The numbers of the files from the form.
     * @param   KunenaMessage  $message  The message.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function ownPrivateAttachments(array $ids, KunenaMessage $message): array
    {
        $ids = array_filter(array_map('intval', $ids));

        if (!$ids) {
            return [];
        }

        $own = [];

        foreach (KunenaAttachmentHelper::getById($ids, 'none') as $attachment) {
            if ((int) $attachment->mesid === (int) $message->id || (!$attachment->mesid && (int) $attachment->userid === (int) $this->me->userid)) {
                $own[] = (int) $attachment->id;
            }
        }

        return $own;
    }

    /**
     * Save the private part of an edited message: create it, change it, or delete it when it is empty.
     *
     * @param   KunenaMessage  $message  The edited message.
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

        $body      = (string) $this->input->getRaw('message_private');
        $attachIds = $this->input->get('attachment_private', [], 'array');

        if (!$message->message && !trim($body) && !$attachIds) {
            return;
        }

        $attachIds = isset($attachIds[0]) && $attachIds[0] !== '' ? explode(',', $attachIds[0]) : [];
        $attachIds = $this->ownPrivateAttachments($attachIds, $message);

        $finder    = new KunenaPrivateMessageFinder();
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
        $attachs          = $message->getAttachments();

        if ($message->message && trim($body)) {
            $private->body      = $body;
        } else {
            $private->body      = '';
        }

        if (!empty($attachIds)) {
            $private->attachments()->setMapped($attachIds);
        }

        if (!$private->body && !$private->attachments) {
            try {
                $private->delete();
            } catch (Exception $e) {
                KunenaError::displayDatabaseError($e);
            }

            return;
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
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function thankyou()
    {
        $type = $this->app->getInput()->getString('task');
        $this->setThankyou($type);
    }

    /**
     * @param   string  $type  type
     *
     * @return  void
     *
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

        try {
            $message->tryAuthorise($type);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
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

            // The author finds the thanks in his inbox (when an inbox is installed).
            if ($message->userid && $message->userid != $this->me->userid) {
                KunenaPrivate::getInstance()->notify($message, 'thanks', [(int) $message->userid], (int) $this->me->userid);
            }
        } else {
            try {
                $thankyou->delete($this->me);
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
                    ['mesid' => $message->id, 'userid' => $message->userid],
                    $category,
                    $message->getTopic(),
                    $message->getAuthor()
                );
            }

            $activityIntegration->onAfterUnThankyou($this->me->userid, $message->userid, $message);
        }

        $this->setRedirect($message->getUrl($category->exists() ? $category->id : $message->catid, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function unthankyou()
    {
        $type = $this->app->getInput()->getString('task');
        $this->setThankyou($type);
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            // The rules of the action (switched off, banned), not only the right to read the topic.
            $topic->tryAuthorise('subscribe');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- aborts
        }

        try {
            if (!$topic->subscribe(1)) {
                throw new Exception(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC'));
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSubscribe($topic, 1);
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- aborts
        }

        try {
            if (!$topic->subscribe(0)) {
                throw new Exception(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC'));
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNSUBSCRIBED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterSubscribe($topic, 0);
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC') . ' ' . $e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            // The rules of the action (switched off, banned), not only the right to read the topic.
            $topic->tryAuthorise('favorite');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- aborts
        }

        try {
            if (!$topic->favorite(1)) {
                throw new Exception(Text::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC'));
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_FAVORITED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterFavorite($topic, 1);
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC') . ' ' . $e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- aborts
        }

        try {
            if (!$topic->favorite(0)) {
                throw new Exception(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC'));
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNFAVORITED_TOPIC'), 'success');

            // Activity integration
            $activity = KunenaFactory::getActivityIntegration();
            $activity->onAfterFavorite($topic, 0);
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC') . ' ' . $e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('sticky');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- aborts
        }

        try {
            if (!$topic->sticky(1)) {
                throw new Exception(Text::_('COM_KUNENA_POST_STICKY_NOT_SET'));
            }

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
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_NOT_SET') . $e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('sticky');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        if ($topic->sticky(0)) {
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
     * Mark the topic as solved (the topic icon solved), or back to not solved: for the author of the topic and the moderators.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function solved()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topic    = KunenaTopicHelper::get($this->id);
        $category = $topic->getCategory();

        try {
            $topic->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        // The author of the topic (when it is not locked and he is not banned) and the moderators of the category.
        $moderator = $this->me->isModerator($category);
        $owner     = $this->me->exists() && (int) $topic->first_post_userid === (int) $this->me->userid && !$topic->locked
            && !$this->me->isBanned();
        $icons     = KunenaFactory::getTemplate()->getUserTopicIcons();

        if (!$this->config->topicIcons || !isset($icons[KunenaTopic::ICON_SOLVED]) || (!$moderator && !$owner)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        $solved         = (int) $topic->icon_id === KunenaTopic::ICON_SOLVED;
        $topic->icon_id = $solved ? 0 : KunenaTopic::ICON_SOLVED;

        try {
            $topic->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($this->config->logModeration) {
            KunenaLog::log(
                $moderator ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                KunenaLog::LOG_TOPIC_ICON,
                ['icon_id' => (int) $topic->icon_id],
                $category,
                $topic
            );
        }

        $this->app->enqueueMessage(Text::_($solved ? 'COM_KUNENA_TOPIC_MARKED_NOT_SOLVED' : 'COM_KUNENA_TOPIC_MARKED_SOLVED'), 'success');
        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('lock');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        if ($topic->lock(1)) {
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

        try {
            $topic->tryAuthorise('lock');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        if ($topic->lock(0)) {
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

        try {
            $target->tryAuthorise('delete');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        try {
            $target->publish($hold);

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
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
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

        try {
            $target->tryAuthorise('undelete');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        try {
            $target->publish(KunenaForum::PUBLISHED);

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
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $topic->tryAuthorise('permdelete');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        try {
            $target->delete();

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
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if (isset($url)) {
            $this->setRedirect($url);
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $target->tryAuthorise('approve');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($target->getUrl($this->return, false));

            return; // -- abort
        }

        try {
            $target->publish(KunenaForum::PUBLISHED);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($target->getUrl($this->return, false));

            return;
        }

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
            // A topic sends the notification of its first post; a message (a reply) sends its own (KunenaMessage has no
            // sendNotification() anymore).
            if ($target instanceof KunenaMessage) {
                $target->processNotification(null, true, true);
            } else {
                $target->sendNotification(null, true, true);
            }
        }

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function move()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topicId        = $this->app->getInput()->getInt('id', 0);
        $messageId      = $this->app->getInput()->getInt('mesid', 0);
        $targetCategory = $this->app->getInput()->getInt('targetcategory', 0);
        $targetTopic    = $this->app->getInput()->getInt('targettopic', 0);

        if ($targetTopic < 0) {
            $targetTopic = $this->app->getInput()->getInt('targetid', 0);
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

        if ($targetobject) {
            $redirect = $targetobject->getUrl($this->return, false, 'last');
        } else {
            $redirect = $topic->getUrl($this->return, false, 'first');
        }

        try {
            $object->tryAuthorise('move');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($redirect);

            return; // -- abort
        }

        try {
            $target->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($redirect);

            return; // -- abort
        }

        $changesubject  = $this->app->getInput()->getBool('changesubject', false);
        $subject        = $this->app->getInput()->getString('subject', '');
        $shadow         = $this->app->getInput()->getBool('shadow', false);
        $topic_emoticon = $this->app->getInput()->getInt('topic_emoticon', null);
        $keep_poll      = $this->app->getInput()->getInt('keep_poll', false);

        if ($object instanceof KunenaMessage) {
            $mode = $this->app->getInput()->getWord('mode', 'selected');

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

        try {
            $targetobject = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($redirect);

            return;
        }

        // Show the moved topic (or the topic which received the posts) instead of the place where they came from.
        if ($targetobject) {
            $redirect = $targetobject->getUrl($this->return, false, 'last');
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

        if ($error) {
            $this->app->enqueueMessage($error, 'error');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE'), 'success');
        }

        $this->setRedirect($redirect);

    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        try {
            $target->tryAuthorise('read');
        } catch (Exception $e) {
            // Deny access if user cannot read target
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        $reason = $this->app->getInput()->getString('reason');
        $text   = $this->app->getInput()->getString('text');

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
        }

        // The flood protection of the forum is for the reports too (except for the moderators): each report sends the
        // moderators an e-mail and a notification. The time of the last report of the user is kept in a cache of its own.
        $flood = (int) $this->config->floodProtection;

        if ($flood > 0 && !$this->me->isModerator()) {
            $floodCache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController(
                'output',
                ['defaultgroup' => 'kunena_report_flood', 'caching' => true, 'lifetime' => (int) ceil($flood / 60) + 1]
            );
            $floodKey   = 'user' . (int) $this->me->userid;

            if ((int) $floodCache->get($floodKey) > time() - $flood) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_REPORT_FLOOD', $flood), 'error');
                $this->setRedirectBack();

                return;
            }

            $floodCache->store((string) time(), $floodKey);
        }
        $acl         = KunenaAccess::getInstance();
        $emailToList = $acl->getSubscribers($topic->category_id, $topic->id, false, true, false);
        $msglink     = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $target->getPermaUrl(null, false);

        // A plugin can handle the report (a topic in a category of the moderators, a message to them, ...).
        PluginHelper::importPlugin('kunena');

        $reportEvent = new GenericEvent(
            'onKunenaReportMessage',
            ['message' => $message, 'topic' => $topic, 'target' => $target, 'reporter' => $this->me, 'reason' => (string) $reason, 'text' => (string) $text, 'url' => $msglink, 'handled' => false]
        );
        $this->app->getDispatcher()->dispatch('onKunenaReportMessage', $reportEvent);
        $handled = (bool) $reportEvent->getArgument('handled', false);

        // An inbox stores the report for the moderators, and tells which of them do not want the e-mail as well.
        $moderatorIds = array_values(array_filter(array_map(static fn ($moderator) => (int) ($moderator->id ?? 0), (array) $emailToList)));

        if ($moderatorIds) {
            $noMail = KunenaPrivate::getInstance()->notify($message, 'report', $moderatorIds, (int) $this->me->userid);

            if ($noMail) {
                $emailToList = array_values(array_filter((array) $emailToList, static fn ($moderator) => !\in_array((int) ($moderator->id ?? 0), $noMail, true)));
            }
        }

        // The e-mail to the moderators (it is an option) needs working mail settings.
        $mailWanted = !empty($this->config->reportMail);
        $canMail    = $mailWanted && $this->config->sendEmails && $this->config->email && MailHelper::isEmailAddress($this->config->email);

        if (!$handled && !$canMail) {
            $this->app->enqueueMessage(Text::_(!$this->config->sendEmails ? 'COM_KUNENA_EMAIL_DISABLED' : 'COM_KUNENA_EMAIL_INVALID'), 'warning');
            $this->setRedirectBack();

            return;
        }

        $mailSent = false;

        if ($canMail && !empty($emailToList)) {
            $mailnamesender = !empty($this->config->emailSenderName) ? MailHelper::cleanAddress($this->config->emailSenderName) : MailHelper::cleanAddress($this->config->boardTitle . ': ' . $this->me->getName());
            $mailsubject    = "[" . $this->config->boardTitle . " " . Text::_('COM_KUNENA_FORUM') . "] " . Text::_('COM_KUNENA_REPORT_MSG') . ": ";

            if ($reason) {
                $mailsubject .= $reason;
            } else {
                $mailsubject .= $topic->subject;
            }

            $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            $mail->setSender([$this->config->email, $mailnamesender]);
            $mail->setSubject($mailsubject);
            $mail->addReplyTo($this->me->email, $this->me->username);

            if (KunenaMailTemplate::isUsed('com_kunena.reportmessage')) {
                // The mail template of Joomla (Users, Mail Templates): one mail for each moderator.
                $poster = $message->getAuthor();
                $common = [
                    'sitename'   => $this->app->get('sitename'),
                    'boardtitle' => $this->config->boardTitle,
                    'title'      => $reason ?: $topic->subject,
                    'sender'     => $this->me->username . ' (' . $this->me->name . ')',
                    'reason'     => (string) $reason,
                    'report'     => (string) $text,
                    'poster'     => $poster->username . ' (' . $poster->name . ')',
                    'subject'    => $topic->subject,
                    'url'        => $msglink,
                ];
                $html   = $common + ['message' => '<div style="border-left:3px solid #dddddd;padding:4px 14px;margin:14px 0">' . $message->displayField('message') . '</div>'];
                $plain  = $common + ['message' => $message->displayField('message', false)];

                foreach ($emailToList as $emailTo) {
                    if (!MailHelper::isEmailAddress($emailTo->email)) {
                        continue;
                    }

                    $language = !empty($emailTo->id) ? (string) KunenaUserHelper::get((int) $emailTo->id)->language : '';

                    if (KunenaMailTemplate::send('com_kunena.reportmessage', $language, $mail, $html, $plain, ['sitename', 'boardtitle', 'title', 'sender', 'reason', 'report', 'poster', 'subject', 'url'], (string) $emailTo->email)) {
                        $mailSent = true;
                    }
                }
            } else {
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
                    }
                    $receivers[] = $emailTo->email;

                }

                KunenaEmail::send($mail, $receivers);
                $mailSent = true;
            }
        }

        if ($handled || $mailSent) {
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

        $this->setRedirect($target->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
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

        $vote  = $this->app->getInput()->getInt('kpollradio', 0);
        $id    = $this->app->getInput()->getInt('id', 0);

        $topic = KunenaTopicHelper::get($id);
        $poll  = $topic->getPoll();

        try {
            $topic->tryAuthorise('poll.vote', null, true); // throw on failure
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($topic->getUrl($this->return, false));

            return; // -- abort, do not vote
        }

        if (!$poll->getMyVotes()) {
            try {
                // Give a new vote
                $poll->saveVote($vote, $topic);
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_SUCCESS'), 'success');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
        } elseif (!$this->config->pollAllowVoteOne) {
            try {
                // Change existing vote
                $poll->saveVote($vote, $topic, true);
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_CHANGED_SUCCESS'), 'success');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $this->setRedirect($topic->getUrl($this->return, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        // Only moderators of the category may reset the votes of a poll.
        if (!$topic->exists() || !$this->me->isModerator($topic->getCategory())) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

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

    /**
     * The text of a field of the form as it was posted (a list in the request, subject[]=x, is no text).
     *
     * @param   string  $name  Name of the field.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected function postedText(string $name): string
    {
        $value = $this->app->getInput()->post->get($name, '', 'raw');

        return \is_array($value) ? '' : (string) $value;
    }

    /**
     * Returns the topic icons set corresponding at the category selected
     *
     @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.3
     */
    public function topicicons()
    {
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $catid = $this->app->getInput()->getInt('catid', 0);

        $category        = KunenaCategoryHelper::get($catid);
        $categoryIconset = $category->iconset;

        if (empty($categoryIconset)) {
            // A category without a set of icons has no icons: an empty list, and nothing more to do.
            if (ob_get_length()) {
                ob_end_clean();
            }

            echo json_encode([]);

            jexit();
        }

        $topicIcons = [];

        $template = KunenaFactory::getTemplate();

        $xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $categoryIconset . '/topicicons.xml';

        if (is_file($xmlfile)) {
            $xml = simplexml_load_file($xmlfile);

            foreach ($xml->icons as $icons) {
                $type   = (string) $icons->attributes()->type;
                $width  = (int) $icons->attributes()->width;
                $height = (int) $icons->attributes()->height;

                foreach ($icons->icon as $icon) {
                    $attributes = $icon->attributes();
                    $icon       = new stdClass();
                    $icon->id   = (int) $attributes->id;
                    $icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
                    $icon->name = (string) $attributes->name;

                    if ($icon->type != 'user') {
                        $icon->id = $icon->type . '_' . $icon->name;
                    }

                    $icon->iconset   = $categoryIconset;
                    $icon->published = (int) $attributes->published;
                    $icon->title     = (string) $attributes->title;
                    $icon->fa        = (string) $attributes->fa;
                    $icon->svg       = (string) KunenaSvgIcons::loadsvg($attributes->svg);
                    $icon->filename  = (string) $attributes->src;
                    $icon->width     = (int) $attributes->width ? (int) $attributes->width : $width;
                    $icon->height    = (int) $attributes->height ? (int) $attributes->height : $height;
                    $icon->path      = Uri::root() . 'media/kunena/topic_icons/' . $categoryIconset . '/' . $icon->filename;
                    $icon->relpath   = $template->getTopicIconPath("{$icon->filename}", false);
                    $topicIcons[]    = $icon;
                }
            }
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        echo json_encode($topicIcons);

        jexit();
    }

    /**
     * Save the posted message as a draft, to finish and post it later.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function savedraft()
    {
        if (empty($this->config->enableDrafts)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$this->me->exists()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        $input     = $this->app->getInput();
        $parentId  = $input->getInt('parentid', 0);
        $messageId = $input->getInt('mesid', 0);
        $draftId   = $input->getInt('draftid', 0);
        $topicId   = 0;
        $catid     = $this->catid;

        // A list in the request (mesid[]=1) is no id.
        $parentId  = \is_array($parentId) ? 0 : $parentId;
        $messageId = \is_array($messageId) ? 0 : $messageId;
        $draftId   = \is_array($draftId) ? 0 : $draftId;

        try {
            if ($messageId) {
                // Edit: the draft holds the changes to an existing message.
                $edited = KunenaMessageHelper::get($messageId);
                $edited->tryAuthorise('edit');
                $topicId = (int) $edited->thread;
                $catid   = (int) $edited->catid;
            } elseif ($parentId) {
                // Reply: the draft belongs to the topic of the message which is replied to.
                $parent = KunenaMessageHelper::get($parentId);
                $parent->tryAuthorise('reply');
                $topicId = (int) $parent->thread;
                $catid   = (int) $parent->catid;
            } else {
                if (!$catid) {
                    throw new Exception(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'));
                }

                KunenaCategoryHelper::get($catid)->tryAuthorise('topic.create');
            }
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        $fields = [
            'catid'             => $catid,
            'name'              => $input->getString('authorname', $this->me->getName()),
            'email'             => $input->getString('email', null),
            'subject'           => $this->postedText('subject'),
            'message'           => $this->postedText('message'),
            'modified_reason'   => $input->getString('modified_reason', ''),
            'icon_id'           => $input->getInt('topic_emoticon', null),
            'anonymous'         => $input->getInt('anonymous', 0),
            'poll_title'        => $input->getString('poll_title', ''),
            'poll_options'      => $input->post->get('polloptionsID', [], 'array'),
            'poll_time_to_live' => $input->getString('poll_time_to_live', 0),
            'subscribe'         => $input->getInt('subscribeMe', 0),
            'private'           => (string) ($input->getRaw('message_private') ?: $input->getRaw('private_message')),
            'rating'            => 0,
            'params'            => '',
            'quote'             => 0,
        ];

        if (trim((string) $fields['subject']) === '' && trim(strip_tags((string) $fields['message'])) === '') {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_DRAFT_EMPTY'), 'warning');
            $this->setRedirectBack();

            return;
        }

        try {
            $draftId = KunenaDraftHelper::save($this->me->userid, $fields, $parentId, $topicId, $draftId, $messageId);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!$draftId) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_DRAFT_LIMIT', KunenaDraftHelper::MAX_DRAFTS), 'error');
            $this->setRedirectBack();

            return;
        }

        // Do not keep the posted fields in the session: the draft holds them now.
        $this->app->setUserState('com_kunena.postfields', null);
        $this->app->enqueueMessage(Text::_('COM_KUNENA_DRAFT_SAVED'), 'success');
        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=drafts', false));
    }

    /**
     * Open a draft in the editor.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function loaddraft()
    {
        if (empty($this->config->enableDrafts)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $draftId = $this->app->getInput()->getInt('draftid', 0);
        $draft   = KunenaDraftHelper::get($draftId, $this->me->userid);

        if (!$draft) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_DRAFT_NOT_FOUND'), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=drafts', false));

            return;
        }

        // The editor takes its values from the fields of the last post.
        $this->app->setUserState('com_kunena.postfields', $draft->fields);

        if ($draft->message_id) {
            $url = "index.php?option=com_kunena&view=topic&layout=edit&catid={$draft->catid}&id={$draft->topic_id}&mesid={$draft->message_id}&draftid={$draft->id}";
        } elseif ($draft->parent_id) {
            $url = "index.php?option=com_kunena&view=topic&layout=reply&catid={$draft->catid}&id={$draft->topic_id}&mesid={$draft->parent_id}&draftid={$draft->id}";
        } else {
            $url = "index.php?option=com_kunena&view=topic&layout=create&catid={$draft->catid}&draftid={$draft->id}";
        }

        $this->setRedirect(KunenaRoute::_($url, false));
    }

    /**
     * Delete a draft.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function deletedraft()
    {
        if (!Session::checkToken('post') && !Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $draftId = $this->app->getInput()->getInt('draftid', 0);

        // A list in the request (draftid[]=1) is no draft.
        if (KunenaDraftHelper::delete(\is_array($draftId) ? 0 : $draftId, $this->me->userid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_DRAFT_DELETED'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_DRAFT_NOT_FOUND'), 'error');
        }

        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=drafts', false));
    }

    /**
     * Unsubscribe from a topic or category with the link of a notification email.
     *
     * The link is signed, so the user does not need to log in. A request which only reads the link (GET) shows
     * the confirmation page, only a post (the confirmation form or the one-click unsubscribe of mail programs) changes anything.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function unsubscribemail()
    {
        $input = $this->app->getInput();
        $type  = $input->getCmd('type', '');
        $id    = $input->getInt('id', 0);
        $uid   = $input->getInt('uid', 0);
        $sig   = $input->getString('sig', '');

        // A list in the request (id[]=1) is no id, no type and no signature.
        if (\in_array(true, array_map('is_array', [$type, $id, $uid, $sig]), true) || !KunenaEmailUnsubscribe::verify($type, $id, $uid, $sig)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_INVALID'), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));

            return;
        }

        if (strtoupper($input->getMethod()) !== 'POST') {
            $query = http_build_query(['type' => $type, 'id' => $id, 'uid' => $uid, 'sig' => $sig]);
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=unsubscribe&' . $query, false));

            return;
        }

        try {
            $done = KunenaEmailUnsubscribe::unsubscribe($type, $id, $uid);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));

            return;
        }

        if ($done) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_DONE'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_INVALID'), 'error');
        }

        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
    }
}
