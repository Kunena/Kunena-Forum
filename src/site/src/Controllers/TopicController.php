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
use Joomla\Database\ParameterType;
use Joomla\Http\HttpFactory;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Attachment\KunenaPendingAttachment;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Email\KunenaMailTemplate;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Event\KunenaCheckSpamEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Draft\KunenaDraftHelper;
use Kunena\Forum\Libraries\Forum\KunenaFloodPolicy;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageBookmarks;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageReactions;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageSchedule;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicAnswer;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicLockReason;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicNewReplies;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateStars;
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
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
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
     * The minutes for which an answer of StopForumSpam is kept (the cache of Joomla counts its lifetime in minutes: 600 kept it
     * 10 hours).
     */
    private const int STOPFORUMSPAM_CACHE_TTL = 10;

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
            // No folder: it is the folder of the user who uploaded the file, and the author of an anonymous message is not named.
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

        // And only while its message may be edited (as removeattachments()): the uploader changed how the files of a post are shown
        // after the edit time, in a locked topic, in read only mode. An upload which waits for its message has none.
        $message = (int) $attachment->mesid ? $attachment->getMessage() : null;

        if ($message && $message->exists() && !$message->isAuthorised('edit')) {
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

        // The file of a message is a part of that message: it is removed by who may edit the message. The rule of the attachment
        // alone (its uploader) let the author take the files out of a post which he may edit no more: after the edit time, in a
        // locked topic, in read only mode. An upload which waits for its message has no message to ask.
        $message  = (int) $instance->mesid ? $instance->getMessage() : null;
        $editable = !$message || !$message->exists() || $message->isAuthorised('edit');

        // The rule of the attachment itself: its uploader (not banned) or a moderator of the category of its message.
        if ($editable && $instance->isAuthorised('delete')) {
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
     * Remove with AJAX the files uploaded in a posting form which is cancelled: only the pending uploads of the user himself.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function removependingattachments()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $removed = 0;

        // A guest cannot prove that an upload is his: his uploads are left to the scheduled clean-up.
        if ($this->me->exists() && !$this->me->isBanned()) {
            $ids = array_map('intval', (array) $this->input->post->get('ids', [], 'array'));
            $ids = \array_slice(array_values(array_unique(array_filter($ids, fn ($id) => $id > 0))), 0, 100);

            foreach ($ids as $id) {
                $attachment = KunenaAttachmentHelper::get($id);

                if (
                    $attachment->exists()
                    && KunenaPendingAttachment::mayRemoveOnCancel((int) $attachment->mesid, (int) $attachment->userid, (int) $this->me->userid)
                    && $attachment->isAuthorised('delete')
                    && $attachment->delete()
                ) {
                    $removed++;
                }
            }
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo json_encode(['removed' => $removed]);

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
     * The new replies of a topic since the last message which the page shows (asked in the background by the topic page):
     * {count, url}, the url of the first new reply. Only a topic which the user may read; the template option of the background
     * reload must be on.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function newreplies()
    {
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $topic = KunenaTopicHelper::get($this->app->getInput()->getInt('id', 0));

        if (!$topic->exists() || !$topic->isAuthorised('read') || !KunenaFactory::getTemplate()->params->get('ajaxTopicList', 1)) {
            throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 403);
        }

        $new      = KunenaTopicNewReplies::find((int) $topic->id, $this->app->getInput()->getInt('after', 0), (int) $this->me->userid);
        $response = ['count' => $new['count'], 'url' => ''];

        if ($new['first']) {
            $response['url'] = KunenaMessageHelper::get($new['first'])->getUrl($topic->getCategory(), false);
        }

        header('Content-type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        while (@ob_end_clean()) {
        }

        echo new KunenaResponseJson($response);

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
        // One to five stars: the rating of the topic is the average of what is stored, so any other number of one member
        // (a million, a negative one) would decide the rating of the topic alone.
        $starid  = KunenaRateStars::clean($this->app->getInput()->get('starid', 0, 'int'));
        $topicid = $this->app->getInput()->get('topic_id', 0, 'int');

        if ($starid === null) {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

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
     * The limit of the files which wait for a message (mesid 0): twice the files of a message, 50 when those have no limit (else a
     * user uploads file after file without posting, and fills the disk).
     *
     * @return  void
     *
     * @throws  RuntimeException  The user has as many as the limit.
     * @since   Kunena 8.0
     */
    protected function checkPendingLimit(): void
    {
        $perMessage = (int) $this->config->attachmentLimit;
        $maxPending = $perMessage > 0 ? 2 * $perMessage : 50;
        $db         = Factory::getContainer()->get('DatabaseDriver');
        $query      = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_attachments'))
            ->where($db->quoteName('userid') . ' = ' . (int) $this->me->userid)
            ->where($db->quoteName('mesid') . ' = 0');
        $pending    = (int) $db->setQuery($query)->loadResult();
        $own        = $pending;

        // A guest: the uploads of his session. All the guests have the user id 0, so they shared one limit: the pending uploads of
        // a few guests stopped the uploads of every other guest (until the task removed them, a day later).
        if (!$this->me->exists()) {
            $mine = KunenaPendingAttachment::guestUploads();
            $own  = 0;

            if ($mine) {
                $query = $db->createQuery()
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__kunena_attachments'))
                    ->where($db->quoteName('userid') . ' = 0')
                    ->where($db->quoteName('mesid') . ' = 0')
                    ->whereIn($db->quoteName('id'), $mine);
                $own   = (int) $db->setQuery($query)->loadResult();
            }
        }

        if (KunenaPendingAttachment::overLimit($own, $pending, $maxPending, !$this->me->exists())) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_UPLOAD_ERROR_PENDING', $maxPending), 400);
        }
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
        // The address of the attachment which the upload made (the location of the answer): the name of the file in the list of
        // the form links to it.
        $location = null;

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

                if (!$category->exists()) {
                    throw new RuntimeException(Text::_('Forbidden'), 403);
                }
                    
                if ($category->id) {
                    if (stripos($this->input->getString('mime'), 'image/') !== false) {
                        $category->tryAuthorise('topic.post.attachment.createimage');
                    } else {
                        $category->tryAuthorise('topic.post.attachment.createfile');
                    }
                } elseif (!$this->me->exists() || $this->me->isBanned()) {
                    // No category yet (a new topic): no rule of a category stops a guest or a banned user.
                    throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 403);
                }

                if (stripos($this->input->getString('mime'), 'image/') !== false) {
                    $category->tryAuthorise('topic.post.attachment.createimage');
                } else {
                    $category->tryAuthorise('topic.post.attachment.createfile');
                }
            }

            // No file type is allowed to the user (the uploads are switched off for him).
            $extensions = KunenaAttachmentHelper::getExtensions($category->id, $this->me->userid);

            if ($extensions === false) {
                throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 403);
            }

            // The files which wait for a message (mesid 0) have a limit: checked at the start of a file (not in the middle of its
            // parts), and again when it is complete (below).
            if ($this->input->getInt('chunkStart', 0) === 0) {
                $this->checkPendingLimit();
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
            $upload->addExtensions($extensions);
            $response = (object) $upload->ajaxUpload($options);

            if (!empty($response->completed)) {
                // A file is counted only once it is complete: the first parts of several files (sent at once, or one after the
                // other before their last parts) all passed the check of their start, and the limit was passed. The temporary file
                // goes with the refusal (the catch below).
                $this->checkPendingLimit();

                // We have it all, lets create the attachment.
                $uploadFile                 = $upload->getProtectedFile();
                list($basename, $extension) = $upload->splitFilename();
                $attachment                 = new KunenaAttachment();

                // The name which the editor sent is kept as the upload without JavaScript keeps it: no folder, no control character,
                // and within its column (a longer name was an error of a strict database).
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
                        'filename_real' => KunenaAttachmentHelper::getClientFilename($response->filename),
                        'caption'       => $caption,
                        'inline'        => null,
                    ]
                );

                // Resize image if needed.
                if ($attachment->isImage()) {
                    $imageInfo = KunenaImage::getImageFileProperties($uploadFile);

                    // A picture whose pixels do not fit in the memory of PHP is not decoded (it ended the request in a fatal
                    // error, a picture of a few KB can have very many pixels): the file is removed.
                    if (!KunenaImage::canDecode((int) $imageInfo->width, (int) $imageInfo->height)) {
                        @unlink($uploadFile);

                        throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_IMAGE_TOO_MANY_PIXELS'), 400);
                    }

                    // A limit of 0 is no limit, as for the uploader of the editor: the picture was enlarged to the other limit (0 and
                    // 800 made a picture of 300 x 200 one of 1200 x 800), or the upload ended in a division by zero (both 0).
                    $maxWidth  = (int) $this->config->imageWidth > 0 ? (int) $this->config->imageWidth : (int) $imageInfo->width;
                    $maxHeight = (int) $this->config->imageHeight > 0 ? (int) $this->config->imageHeight : (int) $imageInfo->height;

                    if ($imageInfo->width > $maxWidth || $imageInfo->height > $maxHeight) {
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
                        $image->resize($maxWidth, $maxHeight, false);

                        $options = ['quality' => $quality];
                        $image->toFile($uploadFile, $imageInfo->type, $options);

                        unset($image);

                        $attachment->hash = md5_file($uploadFile);
                        $attachment->size = fileSize($uploadFile);
                    }
                }

                if ($attachment->saveFile($uploadFile, $basename, $extension, true)) {
                    $attachment->dispatchAfterUpload();
                }

                // Set id and override response variables just in case if attachment was modified.
                $response->id       = $attachment->id;
                $response->hash     = $attachment->hash;
                $response->size     = $attachment->size;
                $response->mime     = $attachment->filetype;
                $response->filename = $attachment->filename_real;
                $response->inline   = $attachment->inline;

                if ($attachment->id) {
                    $location = $attachment->getUrl(false, true, false);

                    // The upload of a guest belongs to his session: only he can put it into his message.
                    if (!$this->me->exists()) {
                        KunenaPendingAttachment::rememberGuestUpload((int) $attachment->id);
                    }
                }
            }
        } catch (Exception $response) {
            $upload->cleanup();
            $location = null;

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

        $json           = new KunenaResponseJson($response);
        $json->location = $location;

        echo $json;

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
            'name'              => $this->app->getInput()->getString('authorname', $this->me->getName('', false)),
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
            'scheduled_time'    => $this->id && $this->me->exists() ? $this->app->getInput()->post->getString('scheduled_time', '') : '',
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

        // A scheduled reply: the date is read in the time zone of the user (as the dates are shown to him), and is in the future.
        $scheduledTime = 0;

        if ($fields['scheduled_time'] !== '') {
            $timezone      = (string) $this->app->getIdentity()->getParam('timezone', $this->app->get('offset', 'UTC'));
            $scheduledTime = KunenaMessageSchedule::parse($fields['scheduled_time'], $timezone, Factory::getDate()->toUnix());

            if ($scheduledTime < 0) {
                $error = $scheduledTime === KunenaMessageSchedule::PAST ? 'COM_KUNENA_POST_SCHEDULE_ERROR_PAST' : 'COM_KUNENA_POST_SCHEDULE_ERROR_INVALID';
                $this->app->enqueueMessage(Text::_($error), 'error');
                $this->setRedirectBack();

                return;
            }

            // Not anonymous: an anonymous reply has no author, and the task which publishes the scheduled replies checks the author
            // (banned, blocked, deleted). It found none, took the date away and left the reply waiting for good, and no moderator
            // was told about it.
            if ($scheduledTime > 0 && $fields['anonymous']) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SCHEDULE_ERROR_ANONYMOUS'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

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

            if (!$this->isTopicIconOfForm($category, $fields['icon_id'])) {
                $fields['icon_id'] = null;
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

            // "Let users edit the subject" set to No: a reply has the subject of the message it answers (newReply()), the form has
            // no field for it. The posted subject was taken all the same.
            if (!$this->config->allowChangeSubject && !$this->me->isModerator($parent->getCategory())) {
                unset($fields['subject']);
            }

            list($topic, $message) = $parent->newReply($fields);
            $category              = $topic->getCategory();

            if (!$this->isTopicIconOfForm($category, $fields['icon_id'])) {
                $fields['icon_id'] = null;
            }
        }

        if ($this->me->canDoCaptcha()) {
            $app    = Factory::getApplication();
            $plugin = $app->get('captcha', '0');

            if ($plugin !== 0 && $plugin !== '0' && $plugin !== '' && $plugin !== null) {
                if (PluginHelper::isEnabled('captcha', $plugin)) {
                    try {
                        $captcha = Captcha::getInstance((string) $plugin, ['namespace' => 'kunena_captcha']);

                        if (!$captcha->checkAnswer($app->getInput()->post->getString('captcha', ''))) {
                            // The captcha of Joomla (and others) says false without a message of its own: the form came back
                            // without a word of why.
                            $app->enqueueMessage(Text::_('COM_KUNENA_POST_ERROR_CAPTCHA'), 'error');
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

        // Flood protection: the time between two consecutive posts, for a new topic and for a reply (Kunena 7.1). It was checked
        // for a new topic only, so the replies could be posted as fast as a script sends them.
        if ($this->config->floodProtection && !$this->me->isModerator($category)) {
            $wait = KunenaFloodPolicy::secondsToWait($dateNowUnix, (int) $this->config->floodProtection, $this->getLastPostTimes());

            if ($wait > 0) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_POST_TOPIC_FLOOD', $wait), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // A new topic may not take the subject of an existing topic (SEO): only the topics the user can see count.
        if ($this->config->uniqueTopicSubject && !$this->id) {
            $own = KunenaCategoryHelper::getOwnTopicsOnlyWhere($this->me, 'a.category_id', 'a.first_post_userid');

            if (KunenaTopicHelper::subjectExists($this->db, trim((string) $fields['subject']), (array) $this->me->getAllowedCategories(), $own)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_ERROR_SUBJECT_EXISTS'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        // A second send of the same post by the same poster within a minute is ignored (a double click). Only the text was compared:
        // another user who replied the same ("Thanks!") within the minute lost his reply. For a guest, also his name.
        $duplicatetimewindow = $dateNowUnix - 1 * 60;
        $lastTopic           = $topic->getCategory()->getLastTopic();

        if (
            $lastTopic->subject == $topic->subject && $lastTopic->last_post_time >= $duplicatetimewindow
            && $lastTopic->category_id == $topic->category_id && $lastTopic->last_post_id == $topic->last_post_id
            && $lastTopic->id == $topic->id && $lastTopic->last_post_message == $message->message
            && (int) $lastTopic->last_post_userid === (int) $this->me->userid
            && ($this->me->userid || (string) $lastTopic->last_post_guest_name === (string) $message->name)
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

        // The browser may give back the text without the list of the uploads (back and forward): take the own uploads it names.
        $message->addAttachmentsFromText(ArrayHelper::toInteger(array_keys($attachments)));

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

        // Spam check of the plugins: the post is rejected or held for a moderator
        $spamCheck = $message->dispatchSpamCheck($topic, $category, $this->me, $isNew, KunenaUserHelper::getUserIp());

        if ($spamCheck->getVerdict() === KunenaCheckSpamEvent::REJECT) {
            $reason = $spamCheck->getReason();
            $this->app->enqueueMessage($reason !== '' ? htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') : Text::_('COM_KUNENA_POST_ERROR_SPAM_REJECTED'), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($spamCheck->getVerdict() === KunenaCheckSpamEvent::HOLD) {
            $message->hold = 1;
        }

        // A scheduled reply waits unapproved for its time. When one of the rules above holds it for review, the task does not publish it.
        if ($scheduledTime > 0) {
            $message->scheduled_review = $message->hold == 1 ? 1 : 0;
            $message->hold             = 1;
            $message->scheduled_time   = $scheduledTime;
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

            // Remembered for the flood protection: a message of an anonymous post keeps no user and no IP address.
            $this->app->setUserState('com_kunena.lastposttime', $dateNowUnix);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // The uploads which the saved message took are active now. They were marked before the checks of the post: a refused post
        // left them active without a message, and the scheduled task, which removes the pending uploads (status 0), never did.
        foreach ($message->getAttachmentsToAdd() as $attachmentToAdd) {
            if ((int) $attachmentToAdd->mesid !== (int) $message->id || $attachmentToAdd->status) {
                continue;
            }

            $attachmentToAdd->status = 1;

            try {
                $attachmentToAdd->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        // The tags of a new topic.
        if ($isNew && KunenaTopicTags::isEnabled()) {
            KunenaTopicTags::save((int) $topic->id, $this->app->getInput()->post->get('topic_tags', [], 'array'));
        }

        // Save IP address of user. Not the empty address of an anonymous post (it is removed so that nobody can tell the author):
        // it replaced the address which the profile had kept.
        if ($this->config->ipTracking && $message->ip) {
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
        // Without the options whose text is empty: a poll of empty options was made without an option.
        $poll_options = array_filter((array) $fields['poll_options'], static fn ($option) => trim((string) $option) !== '');

        // A poll is created, never changed here: the poll of the topic is edited with the first post (poll.edit). A reply which sent
        // the fields of a poll wrote them over the poll of the topic and its votes, with the right to create a poll only.
        $pollAllowed = !empty($poll_options) && !empty($poll_title) && !(int) $topic->poll_id;

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

        // A scheduled reply notifies the subscribers when it is published (the moderators now when it is held for review).
        $scheduledOnly = $scheduledTime > 0 && !$message->scheduled_review;

        if (!$scheduledOnly) {
            $message->processNotification();
        }

        // The users who are mentioned in the message get an e-mail.
        $message->sendMentionNotifications();

        // Now try adding any new subscriptions if asked for by the poster
        $usertopic = $topic->getUserTopic();

        // Where the user may subscribe (as the box of the form is shown): a request subscribed with the subscriptions turned off, and a
        // guest got the error of the table.
        if ($fields['subscribe'] && !$usertopic->subscribed && $topic->isAuthorised('subscribe')) {
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

        if ($scheduledOnly) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_POST_SUCCESS_SCHEDULED', KunenaDate::getInstance($scheduledTime)->toKunena('datetime')), 'success');
        } elseif ($message->hold == 1) {
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
        // Not for a guest: user 0 are all the guests, and their pending uploads are all of his kind (the uploads of every guest who
        // was writing were deleted).
        if (!$this->me->exists()) {
            return;
        }

        $params['file']  = '1';
        $params['image'] = '1';
        $params['mesid'] = '0';

        $attachments = KunenaAttachmentHelper::getByUserid($this->me, $params);

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                // An upload of the last day may belong to another form which is still being written (another tab, another topic):
                // it stays, as the scheduled task of the pending uploads leaves it. Every pending upload of the user was deleted.
                $path     = $attachment->getPath();
                $fileTime = $path && is_file($path) ? (int) filemtime($path) : null;

                if (!KunenaPendingAttachment::mayRemove($fileTime, time())) {
                    continue;
                }

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
        $cache    = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('output', $options);
        $cached   = $cache->get($cacheKey);

        if ($cached === '1' || $cached === '0') {
            return $cached === '1';
        }

        // Only an answer of the service is kept: a failed lookup (a timeout, an error of the service) was kept as "not listed", and
        // the IP, the name and the e-mail posted without a check for as long as the cache kept it.
        $listed = $this->requestStopForumSpam($url, $payload);

        if ($listed === null) {
            return false;
        }

        $cache->store($listed ? '1' : '0', $cacheKey);

        return $listed;
    }

    /**
     * Ask StopForumSpam whether the IP, the name or the e-mail of a post is listed.
     *
     * @param   string  $url      The address of the API.
     * @param   string  $payload  The request (JSON).
     *
     * @return  boolean|null  Listed or not, null when the service gave no answer.
     *
     * @since   Kunena 8.0
     */
    protected function requestStopForumSpam(string $url, string $payload): ?bool
    {
        $options = new Registry();
        $options->set('userAgent', KunenaForum::version());

        $http = (new HttpFactory())->getHttp($options);

        try {
            $response = $http->post($url, $payload, [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ], self::STOPFORUMSPAM_TIMEOUT);
        } catch (RuntimeException $e) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $result = json_decode((string) $response->getBody(), true);

        if (!\is_array($result) || empty($result['success'])) {
            return null;
        }

        return (isset($result['ip']['appears']) && $result['ip']['appears'])
            || (isset($result['username']['appears']) && $result['username']['appears'])
            || (isset($result['email']['appears']) && $result['email']['appears']);
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
        // Every tag of a link, an image or an e-mail address, in any case and also without a value ([img]http://93.184.216.34/a[/img],
        // [URL=...]): only [url=...] and [img=...] in lower case went, and the pattern below finds no address of an IP or of a
        // domain in other letters, so those links and images were posted. Then every address with a scheme.
        $text = preg_replace('/\[(url|img|email)\b[^\]]*\].*?\[\/\1\s*\]/isu', '', $text);
        $text = preg_replace('#(?:https?|ftp)://\S+#iu', '', $text);
        $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $text);
        $text = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $text);

        // When the bbcode urls and images are removed just remove the others links
        $text = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $text);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SAVED_WITHOUT_LINKS_AND_IMAGES'), 'success');

        return $text;
    }

    /**
     * The times of the last post of the poster, from every place where it is known (KunenaFloodPolicy takes the latest): the
     * messages of the user (of the IP address for a guest), and the session, for a post which keeps no user and no address.
     *
     * @return  array
     *
     * @since   Kunena 7.1
     */
    protected function getLastPostTimes(): array
    {
        $times  = [(int) $this->app->getUserState('com_kunena.lastposttime', 0)];
        $userid = (int) $this->me->userid;
        $ip     = (string) KunenaUserHelper::getUserIp();

        if ($userid > 0 || $ip !== '') {
            $query = $this->db->createQuery()
                ->select('MAX(' . $this->db->quoteName('time') . ')')
                ->from($this->db->quoteName('#__kunena_messages'));

            if ($userid > 0) {
                $query->where($this->db->quoteName('userid') . ' = :userid')
                    ->bind(':userid', $userid, ParameterType::INTEGER);
            } else {
                $query->where($this->db->quoteName('userid') . ' = 0')
                    ->where($this->db->quoteName('ip') . ' = :ip')
                    ->bind(':ip', $ip);
            }

            $this->db->setQuery($query);

            try {
                $times[] = (int) $this->db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $times;
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

        // The links of the eBay and X widgets are not counted: the widgets are left out of the text. One widget skipped the whole
        // count, so a post with a widget could have any number of links.
        $text = preg_replace(['/<div class=\"kunena_ebay_widget\"(.*?)>(.*?)<\/div>/s', '/<div id=\"kunena_x_widget\"(.*?)>(.*?)<\/div>/s'], '', $text);

        // The text is the HTML of the message, or its plain text (without images) where a link is "text (url)". The links of the
        // HTML are counted when it has some: a "(domain.tld)" in the text of a post with an image counted that one instead of
        // all its links.
        if (preg_match('/<a\s[^>]*href=/i', $text)) {
            preg_match_all("/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU", $text, $matches);
        } else {
            preg_match_all('@\(((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)\)@', $text, $matches);
        }

        $countlink = \count($matches[0]);

        // Ignore internal links
        foreach ($matches[1] as $link) {
            $uri  = Uri::getInstance($link);
            $host = $uri->getHost();

            // The cms will catch most of these well
            if (empty($host) || KunenaRoute::isInternal($link)) {
                $countlink--;
            }
        }

        if (!$topic->isAuthorised('approve') && $countlink >= $this->config->maxLinks + 1) {
            return false;
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

        // Attach receiver: the author of the post replied to (none for the first post of a topic or a guest post).
        if ($pAuthor->exists()) {
            $private->users()->add($pAuthor->userid);
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

        // The private part is read from the field of the editors (message_private, as post() reads it): it was always empty here,
        // so an edit which kept only the private part was refused, or put the message in the trash. The reason is empty when the form
        // has no field of it (editMarkup off): the reason of the last edit (of a moderator) was saved again with the next edit of the
        // author, also in its history.
        $fields = [
            'name'              => $this->app->getInput()->getString('authorname', $message->name),
            'email'             => $this->app->getInput()->getString('email', $message->email),
            'subject'           => $this->postedText('subject'),
            'message'           => $this->postedText('message'),
            'modified_reason'   => $this->app->getInput()->getString('modified_reason', ''),
            'icon_id'           => $this->app->getInput()->getInt('topic_emoticon', $topic->icon_id),
            'anonymous'         => $this->app->getInput()->getInt('anonymous', 0),
            'poll_title'        => $this->app->getInput()->getString('poll_title', null),
            'poll_options'      => $this->app->getInput()->get('polloptionsID', [], 'array'),
            'poll_time_to_live' => $this->app->getInput()->getString('poll_time_to_live', 0),
            'subscribe'         => $this->app->getInput()->getInt('subscribeMe', 0),
            'private'           => (string) $this->input->getRaw('message_private'),
            'params'            => '',
            'mesid'             => $this->id,
        ];

        // A request without a valid token may come from another site: nothing of it is kept for the edit form, so a
        // forged request cannot put its own values in the form of the user (as in post()).
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        // Kept for the form, as in post(): an edit refused by a rule of the text (a link in the subject, too many links) went back
        // to the form with the saved text, and what the user typed was lost. The fields name their message (mesid): the form of
        // the edit of another message does not take them.
        $this->app->setUserState('com_kunena.postfields', $fields);

        try {
            $message->tryAuthorise('edit');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Load language file from the template.
        KunenaFactory::getTemplate()->loadLanguage();

        // "Let users edit the subject" set to No: the form shows the subject in a disabled field (or a hidden one), the post keeps it.
        // The posted subject was taken all the same, also for the topic when the post is its first one.
        if (!$this->config->allowChangeSubject && !$this->me->isModerator($message->getCategory())) {
            $fields['subject'] = (string) $message->subject;
        }

        // Update message contents (the old text: a user who was mentioned before does not get a second e-mail)
        $oldMessageText = (string) $message->message;
        $oldSubject     = (string) $message->subject;
        $message->edit($fields);

        // If requested by its author: make the message anonymous. A moderator who edited the post of a member made it anonymous.
        if ($fields['anonymous'] && $message->getCategory()->allowAnonymous && (int) $message->userid === (int) $this->me->userid) {
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

        // The browser may give back the text without the list of the uploads (back and forward): take the own uploads it names.
        $message->addAttachmentsFromText(ArrayHelper::toInteger(array_keys($attachments)));

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
        if (!$this->isTopicIconOfForm($topic->getCategory(), $fields['icon_id'])) {
            $fields['icon_id'] = null;
        }

        if ($this->config->topicIcons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null)) {
            $topic->icon_id = $fields['icon_id'];
        }

        // The edit of the first post sets the subject of the topic; the edit of a reply only when its subject was changed: a reply
        // keeps the subject the topic had when it was written, and the fix of a typo in an old reply renamed the topic back.
        $subjectChanged = trim((string) $message->subject) !== trim($oldSubject);

        // A moderator of the category too (as the post of a reply takes it): only the global moderators were.
        if ($topic->first_post_id == $message->id || $subjectChanged && ($this->config->allowChangeSubject && $topic->first_post_userid == $message->userid || $this->me->isModerator($topic->getCategory()))) {
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

        // Emptying the text deletes the message: only for a user who may delete it (userDeleteMessage, or a moderator). Only the
        // setting "when there are no replies" refused here, so with "never" or "all except the first message" a user deleted his
        // message, or the first message of his topic, by emptying its text.
        if (!$text && !trim((string) $fields['private']) && !$message->isAuthorised('delete')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');
            $this->setRedirectBack();

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
                    $this->app->setUserState('com_kunena.postfields', null);
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
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Saved: the fields are not for a form any more.
        $this->app->setUserState('com_kunena.postfields', null);

        // The uploads which the saved message took are active now. They were marked before the checks of the post: a refused post
        // left them active without a message, and the scheduled task, which removes the pending uploads (status 0), never did.
        foreach ($message->getAttachmentsToAdd() as $attachmentToAdd) {
            if ((int) $attachmentToAdd->mesid !== (int) $message->id || $attachmentToAdd->status) {
                continue;
            }

            $attachmentToAdd->status = 1;

            try {
                $attachmentToAdd->save();
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        // The mentions of the saved text (after the message was made anonymous: the e-mail names the author of the message). They were
        // sent before the checks of the edit: a refused edit still notified the users, and the corrected one notified them again.
        $message->sendMentionNotifications($oldMessageText, false);

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
            // Save changes into poll. Without the options whose text is empty: a poll whose options were all emptied was kept with
            // none (all its options and votes deleted); it is removed now, as a poll without options is.
            $poll_options = array_filter((array) $fields['poll_options'], static fn ($option) => trim((string) $option) !== '');
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
                    // Edit existing poll, the right poll.edit also refuses a poll with votes when allowUserEditPoll is off
                    try {
                        $topic->tryAuthorise('poll.edit');
                        $poll->save();
                        $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_EDITED'), 'success');
                    } catch (KunenaExceptionAuthorise $e) {
                        // The message is saved all the same: the refusal of the poll is a caveat, not a failure
                        $this->app->enqueueMessage($e->getMessage(), 'warning');
                    } catch (Exception $e) {
                        $this->app->enqueueMessage($e->getMessage(), 'error');
                    }
                }
            } elseif ($poll->exists()) {
                // Delete poll
                try {
                    $topic->tryAuthorise('poll.edit');
                    $topic->tryAuthorise('poll.delete');
                    $poll->delete();
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_DELETED'), 'success');
                } catch (KunenaExceptionAuthorise $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'warning');
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

        // A scheduled reply (not held for review) keeps waiting for its time: it is not for the moderators.
        if ($message->hold == 1 && !($message->scheduled_time > 0 && !$message->scheduled_review)) {
            // If user cannot approve message by himself, send email to moderators.
            if (!$topic->isAuthorised('approve')) {
                $message->processNotification();
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_MODERATED'), 'success');
        }

        // Redirect edit first message when category is under review and the editor cannot read the topic (its author can)
        if (
            $message->hold == 1 && $message->getCategory()->review && $topic->first_post_id == $message->id && !$this->me->isModerator()
            && !$topic->isAuthorised('read', null, false)
        ) {
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

        // The date which stands for "no end" (1000-01-01), as the poll dialog of CKEditor sends it back for a poll without an end:
        // it was the end of that day, so the poll was closed by any edit of its topic.
        if (preg_match('/^(\d{4})-/', $value, $year) && (int) $year[1] < 1970) {
            return null;
        }

        // A day without a time (the date field of the browser sends yyyy-mm-dd) is the end of that day, as a date of the date
        // picker is: PHP reads it as the start of the day, and the poll ended a day early.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $date = KunenaDate::fromPicker($value);

            if ($date !== null) {
                return (new Date($date . ' 23:59:59'))->toSql();
            }
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

        // The private part of the message, whoever is its author now: it was looked up by the user of the message, which an edit
        // that makes the message anonymous sets to 0, so a second private part was made (the first one stayed, both were shown,
        // and every later edit made another one). A message has one private part (postPrivate() makes it when there is none).
        $finder    = new KunenaPrivateMessageFinder();
        $finder
            ->filterByMessage($message)
            ->where('parent_id', '=', 0)
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
     * Refuse the task when the forum is in read only mode ("Allow users only to read messages"): a message, and back to the page.
     * The posting, the edit and the moderation ask the rights of the message or the topic, which refuse everything but reading
     * in that mode. A thank you, a reaction, a vote, a bookmark and the answer of a topic did not: their buttons are hidden, but
     * a request with a token still wrote them.
     *
     * @return  boolean  True when the task is refused.
     *
     * @since   Kunena 8.0
     */
    protected function refusedInReadOnlyMode(): bool
    {
        if (!$this->config->readOnly) {
            return false;
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_WARNING_READONLY'), 'error');
        $this->setRedirectBack();

        return true;
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
        if ($this->refusedInReadOnlyMode()) {
            return;
        }

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

        // The category of the message (as react() takes it), not the one of the request: a request with another catid wrote the log
        // of the thank you into that category (the moderators of it saw it, with the subject of a topic which they may not read).
        $category            = $message->getCategory();
        $thankyou            = KunenaMessageThankyouHelper::get($this->mesid);
        $activityIntegration = KunenaFactory::getActivityIntegration();

        if ($type == 'thankyou') {
            try {
                // Not saved (the thank you of another request of the user was stored in the meantime, a double click): the
                // success, the log, the points of the activity integrations and the inbox are not given a second time.
                if (!$thankyou->save($this->me)) {
                    $this->setRedirectBack();

                    return;
                }
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

            // The author finds the thanks in his inbox (when an inbox is installed): not from a user whom he ignores (as the mentions,
            // the private messages and the popups leave them out), whose thanks still came to his inbox.
            if (
                $message->userid && $message->userid != $this->me->userid
                && !KunenaUserIgnore::isIgnoring((int) $message->userid, (int) $this->me->userid)
            ) {
                KunenaPrivate::getInstance()->notify($message, 'thanks', [(int) $message->userid], (int) $this->me->userid);
            }
        } else {
            // The link of a moderator next to a thanker names that user (userid): his thank you is removed. The own one of the
            // moderator was removed instead (or "you did not thank" was the error), and the one of the user stayed.
            $whose = $this->me;
            $other = $this->app->getInput()->getInt('userid', 0);

            if ($other > 0 && $other !== (int) $this->me->userid) {
                if (!$this->me->isModerator($message->getCategory())) {
                    $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
                    $this->setRedirectBack();

                    return;
                }

                $whose = KunenaUserHelper::get($other);
            }

            try {
                $thankyou->delete($whose);
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
                    // The user whose thank you was removed (a moderator removes the one of another user), not the author of the post.
                    ['mesid' => $message->id, 'userid' => (int) $whose->userid],
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

        // A topic marked as not solved has no accepted answer, as the removal of the answer takes the icon away
        // (KunenaTopicAnswer::toggle()): the box "Accepted answer" stayed on a topic marked as not solved.
        if ($solved) {
            $topic->answer_id = 0;
        }

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
     * Accept a reply as the answer of its topic, or remove it when it is the answer: for the author of the topic (when it is not
     * locked) and the moderators. Accepting marks the topic as solved, removing marks it as not solved (with the topic icons).
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function answer()
    {
        if ($this->refusedInReadOnlyMode()) {
            return;
        }

        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message = KunenaMessageHelper::get($this->mesid);

        try {
            $message->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        $topic     = $message->getTopic();
        $category  = $topic->getCategory();
        $moderator = $this->me->isModerator($category);
        $enabled   = !isset($this->config->acceptedAnswers) || (bool) $this->config->acceptedAnswers;

        if (!KunenaTopicAnswer::mayChoose($topic, $message, $this->me, $moderator, $enabled)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        $solvedIcon = $this->config->topicIcons && isset(KunenaFactory::getTemplate()->getUserTopicIcons()[KunenaTopic::ICON_SOLVED]);
        $accepted   = KunenaTopicAnswer::toggle($topic, $message, $solvedIcon);

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
                $accepted ? KunenaLog::LOG_POST_ANSWER : KunenaLog::LOG_POST_UNANSWER,
                ['mesid' => (int) $message->id],
                $category,
                $topic,
                $message->getAuthor()
            );
        }

        $this->app->enqueueMessage(Text::_($accepted ? 'COM_KUNENA_TOPIC_ANSWER_ACCEPTED' : 'COM_KUNENA_TOPIC_ANSWER_REMOVED'), 'success');
        $this->setRedirect($message->getUrl($category, false));
    }

    /**
     * Give a reaction (an emoji of the list of the setting) to a message of another user: it replaces the reaction of the user on
     * the message, or removes it when it is the same.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function react()
    {
        if ($this->refusedInReadOnlyMode()) {
            return;
        }

        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message = KunenaMessageHelper::get($this->mesid);

        try {
            $message->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        $reactions = KunenaMessageReactions::parse(isset($this->config->reactions) ? (string) $this->config->reactions : '');
        $reaction  = KunenaMessageReactions::clean($this->app->getInput()->getString('reaction', ''), $reactions);
        $topic     = $message->getTopic();

        if ($reaction === null || !KunenaMessageReactions::mayReact($topic, $message, $this->me, $reactions)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        try {
            $saved = KunenaMessageReactions::toggle((int) $message->id, (int) $this->me->userid, $reaction);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        $this->app->enqueueMessage(Text::_($saved ? 'COM_KUNENA_REACTION_SAVED' : 'COM_KUNENA_REACTION_REMOVED'), 'success');
        $this->setRedirect($message->getUrl($topic->getCategory(), false));
    }

    /**
     * Bookmark a message for the user (the list My bookmarks), or remove his bookmark.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function bookmark()
    {
        if ($this->refusedInReadOnlyMode()) {
            return;
        }

        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $enabled = !isset($this->config->bookmarks) || (bool) $this->config->bookmarks;

        if (!KunenaMessageBookmarks::mayBookmark($this->me, $enabled)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message = KunenaMessageHelper::get($this->mesid);

        try {
            $message->tryAuthorise('read');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return; // -- abort
        }

        try {
            $bookmarked = KunenaMessageBookmarks::toggle((int) $message->id, (int) $this->me->userid);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        $this->app->enqueueMessage(Text::_($bookmarked ? 'COM_KUNENA_BOOKMARK_SAVED' : 'COM_KUNENA_BOOKMARK_REMOVED'), 'success');
        $this->setRedirect($message->getUrl($message->getTopic()->getCategory(), false));
    }

    /**
     * Lock the topic, with a reason of the list or without one: for the moderators, and for the author of the topic with a
     * reason when the setting lockByAuthor is on.
     *
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

        $topic   = KunenaTopicHelper::get($this->id);
        $value   = $this->input->getRaw('reason', '');
        $reasons = KunenaTopicLockReason::parse(isset($this->config->lockReasons) ? (string) $this->config->lockReasons : '');
        $reason  = '';

        try {
            $topic->tryAuthorise('lock');
            $moderator = true;
        } catch (Exception $e) {
            $moderator = false;
            $denied    = $e->getMessage();
        }

        if ($moderator) {
            // A moderator locks without a reason, or with one of the list.
            if (!\is_string($value) || trim($value) !== '') {
                $reason = KunenaTopicLockReason::clean($value, $reasons);

                if ($reason === null) {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_LOCK_REASON_INVALID'), 'error');
                    $this->setRedirectBack();

                    return;
                }
            }
        } else {
            // The author of the topic locks it only with a reason of the list.
            try {
                $topic->tryAuthorise('read');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return; // -- abort
            }

            $lockByAuthor = isset($this->config->lockByAuthor) && (bool) $this->config->lockByAuthor;

            if (!KunenaTopicLockReason::authorMayLock($topic, $this->me, $lockByAuthor, $reasons)) {
                $this->app->enqueueMessage($denied, 'error');
                $this->setRedirectBack();

                return; // -- abort
            }

            $reason = KunenaTopicLockReason::clean($value, $reasons);

            if ($reason === null) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        if ($topic->lock(1, $reason)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_SET'), 'success');

            if ($this->config->logModeration) {
                KunenaLog::log(
                    $moderator ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
                    KunenaLog::LOG_TOPIC_LOCK,
                    ['reason' => $reason],
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

        // Only what is deleted is restored: a message or a topic which waits for approval (an old link, a stale page) was published
        // without the approval, its notifications and the mails of its mentions.
        if ((int) $target->hold < KunenaForum::DELETED) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_UNDELETE_NOT_DELETED'), 'notice');
            $this->setRedirectBack();

            return;
        }

        // A message of a topic in the trash (the trash puts its messages there with it): restoring it published the topic again with
        // that message only, the other messages stayed in the trash and the topic had no button to restore them any more.
        if (isset($message) && (int) $topic->hold >= KunenaForum::DELETED) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_TOPIC_DELETED'), 'notice');
            $this->setRedirectBack();

            return;
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

        // The files of the message go with it: the topic counts them no more. One file was taken off the topic before the check
        // of the rights (also when the delete was refused), and whether the message had a file or not.
        if (isset($message)) {
            $files = \count($message->getAttachments(false, 'none'));

            if ($files) {
                $topic->attachments = max(0, (int) $topic->attachments - $files);
                $topic->save(false);
            }
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

        // Only an unapproved message tells its author that it was approved.
        $wasUnapproved = $target->hold == 1;

        // Only a message which waits for approval is approved: a deleted message came back published (an old link, a stale page),
        // and an approved one was approved again with its notifications.
        if (!$wasUnapproved) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_NOT_WAITING'), 'notice');
            $this->setRedirect($target->getUrl($this->return, false));

            return;
        }

        // A message of a topic in the trash: approving it published the topic again with that message only (its other messages
        // stayed in the trash). The topic is restored first.
        if ($target instanceof KunenaMessage && (int) $topic->hold >= KunenaForum::DELETED) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_TOPIC_DELETED'), 'notice');
            $this->setRedirect($target->getUrl($this->return, false));

            return;
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

        // The subscribers are told about a message once: a published message which an edit sent back to review (issue #1862) is
        // approved without telling them again. It was "not if its author changed it", which also left out the first approval of a
        // message which its author changed while it waited.
        if (!$message->notified) {
            // A topic sends the notification of its first post; a message (a reply) sends its own (KunenaMessage has no
            // sendNotification() anymore).
            if ($target instanceof KunenaMessage) {
                $target->processNotification(null, $wasUnapproved, true);
            } else {
                $target->sendNotification(null, $wasUnapproved, true);
            }
        }

        // The users mentioned in the message: a message which waits for approval tells none of them (sendMentionNotifications()).
        KunenaMessageHelper::get($message->id, true)->sendMentionNotifications();

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

        if ($messageId) {
            $message = $object = KunenaMessageHelper::get($messageId);
            $topic   = $message->getTopic();
        } else {
            $topic   = $object = KunenaTopicHelper::get($topicId);
            $message = KunenaMessageHelper::get($topic->first_post_id);
        }

        // A message stays in its topic unless the moderator chose a target topic: submitting the form without a choice must not split it.
        if ($messageId && $targetTopic == -2) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATION_NOTHING_CHANGED'), 'notice');
            $this->setRedirect($message->getUrl($this->return, false));

            return;
        }

        if ($targetTopic < 0) {
            $targetTopic = $this->app->getInput()->getInt('targetid', 0);

            // "Enter topic ID" without an id (or not a number): the message was moved into a new topic (a split which nobody asked
            // for), the topic into the category instead of the topic.
            if ($targetTopic <= 0) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATION_NO_TARGET_ID'), 'notice');
                $this->setRedirect($message->getUrl($this->return, false));

                return;
            }
        }

        if ($targetTopic) {
            $target = KunenaTopicHelper::get($targetTopic);
        } else {
            $target = KunenaCategoryHelper::get($targetCategory);
        }

        $redirect = $topic->getUrl($this->return, false, 'first');

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

        // The moderator may write in the target, or moderates it: else the move puts posts where he may not write (a locked
        // category, a topic which he may not answer in a category which he does not moderate).
        if ($target instanceof KunenaTopic) {
            $allowed = $target->isAuthorised('reply') || $target->getCategory()->isAuthorised('moderate');
        } else {
            $allowed = $target->isAuthorised('topic.create') || $target->isAuthorised('moderate');
        }

        if (!$allowed) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirect($redirect);

            return; // -- abort
        }

        $changesubject  = $this->app->getInput()->getBool('changesubject', false);
        $subject        = $this->app->getInput()->getString('subject', '');
        $shadow         = $this->app->getInput()->getBool('shadow', false);
        $topic_emoticon = $this->app->getInput()->getInt('topic_emoticon', null);
        $keep_poll      = $this->app->getInput()->getInt('keep_poll', false);

        // An icon which the forms offer, as for a post (an icon of the system, 500-505, or a number larger than the column was
        // taken from the form of the moderation): another one keeps the icon of the topic.
        if (!$this->isTopicIconOfForm($target instanceof KunenaTopic ? $target->getCategory() : $target, $topic_emoticon)) {
            $topic_emoticon = null;
        }

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

        // A topic which stays in its category and is not merged is only changed (icon, subject), not moved.
        $onlyChanged = !$messageId && !$targetTopic && $targetCategory == $topic->category_id;

        try {
            // The category which the topic leaves, for the log: a topic moved without a shadow is in the target category after it.
            $sourceCategory = $topic->getCategory();
            $targetobject   = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll);
        } catch (Exception $e) {
            // Nothing was moved: only the error is shown (it was followed by the message of success) and nothing is logged.
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect($redirect);

            return;
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
                // The category it came from (the target is in the data): the target was logged twice for a move without a shadow,
                // and the move was found in the logs of no source category.
                $sourceCategory,
                $topic,
                $message->getAuthor()
            );
        }

        $this->app->enqueueMessage(Text::_($onlyChanged ? 'COM_KUNENA_MODERATION_TOPIC_SAVED' : 'COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE'), 'success');

        // Go to where the messages are now (the new or merged topic), not to the topic they left.
        if ($targetobject instanceof KunenaTopic) {
            $redirect = $targetobject->getUrl($this->return, false, 'last');
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

        // A banned user may read, not write: his report is a topic or an e-mail for every moderator (he could send them one after
        // the other).
        if (!$this->me->exists() || $this->me->isBanned() || $this->config->reportMsg == 0) {
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

        // Not his own post when "User can send own report" is off (moderators may), as the button: only the button followed it.
        if (!$this->config->userReport && (int) $message->userid === (int) $this->me->userid && !$this->me->isModerator($topic->getCategory())) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'warning');
            $this->setRedirectBack();

            return;
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
        $noMail       = [];

        if ($moderatorIds) {
            $noMail = KunenaPrivate::getInstance()->notify($message, 'report', $moderatorIds, (int) $this->me->userid);

            if ($noMail) {
                $emailToList = array_values(array_filter((array) $emailToList, static fn ($moderator) => !\in_array((int) ($moderator->id ?? 0), $noMail, true)));
            }
        }

        // The e-mail to the moderators (it is an option) needs working mail settings.
        $mailWanted = !empty($this->config->reportMail);
        $canMail    = $mailWanted && $this->config->sendEmails && $this->config->email && MailHelper::isEmailAddress($this->config->email);

        // The moderators who have the report in the inbox and do not want the e-mail: it reached them. The reporter was told that it
        // could not be sent (and it was not logged) when they did not want it, or when no e-mail could be sent.
        $inboxed = $noMail !== [];

        if (!$handled && !$canMail && !$inboxed) {
            $this->app->enqueueMessage(Text::_(!$this->config->sendEmails ? 'COM_KUNENA_EMAIL_DISABLED' : 'COM_KUNENA_EMAIL_INVALID'), 'warning');
            $this->setRedirectBack();

            return;
        }

        $mailSent = false;

        if ($canMail && !empty($emailToList)) {
            // The name as text (a header, not HTML): getName() escapes it, so a reporter "Tom & Jerry" was "Tom &amp; Jerry".
            $mailnamesender = !empty($this->config->emailSenderName) ? MailHelper::cleanAddress($this->config->emailSenderName) : MailHelper::cleanAddress($this->config->boardTitle . ': ' . $this->me->getName('', false));
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
                // The addresses of the site with their host (emoticons, mentions): root-relative, they were broken in a mail program.
                $html   = $common + ['message' => '<div style="border-left:3px solid #dddddd;padding:4px 14px;margin:14px 0">' . KunenaParser::absoluteUrls((string) $message->displayField('message'), Uri::getInstance()->toString(['scheme', 'host', 'port'])) . '</div>'];
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
                    // No mail without its text: the report is not sent, and the reporter is told.
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_NOT_SEND') . ' ' . $e->getMessage(), 'error');
                    $this->setRedirectBack();

                    return;
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

        if ($handled || $mailSent || $inboxed) {
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
            $this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_NOT_SEND'), 'error');
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
        if ($this->refusedInReadOnlyMode()) {
            return;
        }

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

        // Only moderators of the category may reset the votes of a poll. Not through the shadow of a moved topic: it keeps the
        // poll, and a moderator of the old category reset the votes of a poll of a category which he does not moderate.
        if (!$topic->exists() || $topic->moved_id || !$this->me->isModerator($topic->getCategory())) {
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
     * Is the icon one which the form of a topic may give.
     *
     * @param   KunenaCategory  $category  The category of the topic.
     * @param   int|null        $iconId    The posted icon (null: none).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected function isTopicIconOfForm(KunenaCategory $category, ?int $iconId): bool
    {
        // The icon of a topic is one which the forms offer (topicicons()): a published icon of the users of the set of icons of the
        // category. Any number was taken: the icon of a system state (500-505: moved, deleted, locked...) or a number larger than
        // the column, a database error.
        if ($iconId === null || $iconId === 0) {
            return true;
        }

        $iconset = (string) $category->iconset;
        $xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $iconset . '/topicicons.xml';

        // As KunenaTemplate::getTopicIcon() shows them: without a set of its own, the default one.
        // With the D modifier: without it "$" also matches before a line break at the end of the name.
        if ($iconset === '' || preg_match('/^[\w\-]+$/D', $iconset) !== 1 || !is_file($xmlfile)) {
            $xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/default/topicicons.xml';
        }

        $xml = is_file($xmlfile) ? simplexml_load_file($xmlfile) : false;

        foreach ($xml ? $xml->icons : [] as $icons) {
            $type = (string) $icons->attributes()->type;

            foreach ($icons->icon as $icon) {
                $attributes = $icon->attributes();

                if ((int) $attributes->id === $iconId && (int) $attributes->published && ((string) $attributes->type ?: $type) === 'user') {
                    return true;
                }
            }
        }

        return false;
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
        $categoryIconset = (string) $category->iconset;

        // The name of the set becomes a part of the path of a file: it is the name of one folder (as where the icon of a topic
        // is checked), anything else ("../..", a path) is the default set.
        if ($categoryIconset !== '' && preg_match('/^[\w\-]+$/D', $categoryIconset) !== 1) {
            $categoryIconset = 'default';
        }

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

        // The titles of the icons are in the language files of the templates, which a task does not load.
        $template->loadLanguage();

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

                    $icon->published = (int) $attributes->published;

                    // The same icons as on the loading of the form: the published icons of the users.
                    if (!$icon->published || $icon->type !== 'user') {
                        continue;
                    }

                    $icon->iconset   = $categoryIconset;
                    $icon->title     = (string) $attributes->title;
                    $icon->label     = Text::_($icon->title);
                    $icon->fa        = (string) $attributes->fa;
                    $icon->svg       = (string) KunenaSvgIcons::loadsvg((string) $attributes->svg, 'usertopicIcons', $categoryIconset);
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
            'name'              => $input->getString('authorname', $this->me->getName('', false)),
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

        // A draft which may not be used any more (the edit time is over, the topic is locked, the category is closed): the form would
        // refuse it after the fields were put in the session, and they came back in the next form which the user opened.
        if ($draft->message_id) {
            $allowed = KunenaMessageHelper::get((int) $draft->message_id)->isAuthorised('edit');
        } elseif ($draft->parent_id) {
            $allowed = KunenaMessageHelper::get((int) $draft->parent_id)->isAuthorised('reply');
        } else {
            $allowed = KunenaCategoryHelper::get((int) $draft->catid)->isAuthorised('topic.create');
        }

        if (!$allowed) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=drafts', false));

            return;
        }

        // The editor takes its values from the fields of the last post (those of an edit name their message, see edit()).
        $this->app->setUserState('com_kunena.postfields', $draft->message_id ? ['mesid' => (int) $draft->message_id] + (array) $draft->fields : $draft->fields);

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
