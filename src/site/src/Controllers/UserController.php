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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use KunenaProfileKunena;
use RuntimeException;
use stdClass;

require_once JPATH_PLUGINS . '/kunena/kunena/profile.php';

/**
 * Kunena User Controller
 *
 * @property KunenaUser|null user user
 *
 * @since   Kunena 2.0
 */
class UserController extends KunenaController
{
    /**
     * @param   bool  $cachable   catchable
     * @param   bool  $urlparams  urlparams
     *
     * @return \Joomla\CMS\MVC\Controller\BaseController
     *
     * @since   Kunena 6.0
     * @throws \Exception
     */
    public function display($cachable = false, $urlparams = false): BaseController
    {
        // Redirect profile to integrated component if profile integration is turned on
        $redirect = 1;
        $active   = $this->app->getMenu()->getActive();

        if (!empty($active)) {
            $params   = $active->getParams();
            $redirect = $params->get('integration', 1);
        }

        if ($redirect && $this->app->input->getCmd('format', 'html') == 'html') {
            $profileIntegration = KunenaFactory::getProfile();
            $layout             = $this->app->input->getCmd('layout', 'default');

            if ($profileIntegration instanceof KunenaProfileKunena) {
                // Continue
            } elseif ($layout == 'default') {
                $url = $this->me->getUrl(false);
            } elseif ($layout == 'list') {
                $url = $profileIntegration->getUserListURL('', false);
            }

            if (!empty($url)) {
                $this->setRedirect($url);

                throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
            }
        }

        $layout = $this->app->input->getCmd('layout', 'default');

        if ($layout == 'list') {
            if (!KunenaFactory::getConfig()->userlistAllowed && $this->app->getIdentity()->guest) {
                throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
            }
        }

        // Else the user does not exists.
        if (!$this->me) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_USER_UNKNOWN'), 404);
        }

        parent::display();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function search()
    {
        $model = $this->getModel('user');

        $uri = new Uri('index.php?option=com_kunena&view=user&layout=list');

        $state      = $model->getState();
        $search     = $state->get('list.search');
        $limitstart = $state->get('list.start');

        if ($search) {
            $uri->setVar('search', $search);
        }

        if ($limitstart) {
            $uri->setVar('limitstart', $search);
        }

        $this->setRedirect(KunenaRoute::_($uri, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function change()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $layout = $this->app->input->getString('topicLayout', 'default');
        $this->me->setTopicLayout($layout);
        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function karmaup()
    {
        $this->karma(1);
    }

    /**
     * @param   integer  $karmaDelta  karma delta
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    protected function karma($karmaDelta)
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        // 14400 seconds = 6 hours
        $karma_delay = '14400';

        $userid = $this->app->input->getInt('userid', 0);

        $target = KunenaFactory::getUser($userid);

        if (!$this->config->showKarma || !$this->me->exists() || !$target->exists() || $karmaDelta == 0) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_USER_ERROR_KARMA'), 'error');
            $this->setRedirectBack();

            return;
        }

        $now = Factory::getDate()->toUnix();

        if ($this->me->karma_time !== 0) {
            if (!$this->me->isModerator() && $now - $this->me->karma_time < $karma_delay) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_WAIT'), 'warning');
                $this->setRedirectBack();

                return;
            }
        }

        if ($karmaDelta > 0) {
            if ($this->me->userid == $target->userid) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_SELF_INCREASE'), 'warning');
                $karmaDelta = -10;
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_INCREASED'), 'success');
            }
        } else {
            if ($this->me->userid == $target->userid) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_SELF_DECREASE'), 'warning');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_DECREASED'), 'success');
            }
        }

        $this->me->karma_time = $now;

        if ($this->me->userid != $target->userid && !$this->me->save()) {
            $this->app->enqueueMessage($this->me->getError(), 'error');
            $this->setRedirectBack();

            return;
        }

        $target->karma += $karmaDelta;

        try {
            $target->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        // Activity integration
        $activity = KunenaFactory::getActivityIntegration();
        $activity->onAfterKarma($target->userid, $this->me->userid, $karmaDelta);
        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function karmadown()
    {
        $this->karma(-1);
    }

    /**
     * @return  array|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function save()
    {
        $return = null;
        $errors = 0;
        $userid = $this->app->input->getInt('userid');

        if (!Session::checkToken('post')) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ERROR_TOKEN'), 403);
        }

        // Check permission
        $moderator = KunenaUserHelper::getMyself()->isModerator();
        $my        = $this->app->getIdentity();

        if (!$moderator) {
            if ($userid != $my->id) {
                throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ERROR_TOKEN'), 403);
            }
        }

        // Make sure that the user exists.
        if (!$this->me->exists()) {
            throw new KunenaExceptionAuthorise(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
        }

        if (!$userid) {
            $this->user = $this->app->getIdentity();
        } else {
            $this->user = Factory::getUser($userid);
        }

        $success = $this->saveUser();

        if (!$success) {
            $errors++;
            $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_ACCOUNT_NOT_SAVED'), 'error');
        }

        // Save Kunena user.
        $this->saveProfile();
        $this->saveSettings();

        try {
            $success = $this->user->save();
        } catch (Exception $e) {
            $errors++;
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        PluginHelper::importPlugin('system');

        $this->app->triggerEvent('OnAfterKunenaProfileUpdate', [$this->user, $success]);

        if ($errors) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_PROFILE_SAVE_ERROR'), 500);
        }

        if ($this->user->userid == $this->me->userid) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED'), 'success');
            $edited_by_moderator = 0;
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED_BY_MODERATOR'), 'success');
            $edited_by_moderator = 1;
        }

        if ($this->config->logModeration) {
            $log = KunenaLog::LOG_USER_EDIT;

            KunenaLog::log(
                KunenaLog::TYPE_ACTION,
                $log,
                [
                    'edited_by_moderator' => $edited_by_moderator,
                ],
                null,
                null,
                $this->user
            );
        }

        if ($return) {
            return $return;
        }
    }

    /**
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function saveUser()
    {
        // We only allow users to edit few fields
        $allow = ['name', 'email', 'password', 'password2', 'params'];

        if (ComponentHelper::getParams('com_users')->get('change_login_name', 1)) {
            $allow[] = 'username';
        }

        // Clean request
        $post           = $this->app->input->post->getArray();
        $post_password  = $this->app->input->post->get('password', '', 'raw');
        $post_password2 = $this->app->input->post->get('password2', '', 'raw');

        if (empty($post_password) || empty($post_password2)) {
            unset($post['password'], $post['password2']);
        } else {
            // If we have parameters from com_users, use those instead.
            // Some of these may be empty for legacy reasons.
            $params = ComponentHelper::getParams('com_users');

            // Do a password safety check.
            if ($post_password != $post_password2) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_PASSWORD_MISMATCH'), 'error');

                return false;
            }

            if (\strlen($post_password) < $params->get('minimum_length')) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_PASSWORD_NOT_MINIMUM'), 'error');

                return false;
            }

            $value = $post_password;

            if (!empty($params)) {
                $minimumLengthp    = $params->get('minimum_length');
                $minimumIntegersp  = $params->get('minimum_integers');
                $minimumSymbolsp   = $params->get('minimum_symbols');
                $minimumUppercasep = $params->get('minimum_uppercase');
                $meterp            = $params->get('meter');
                $thresholdp        = $params->get('threshold');

                empty($minimumLengthp) ?: $minimumLength = (int) $minimumLengthp;
                empty($minimumIntegersp) ?: $minimumIntegers = (int) $minimumIntegersp;
                empty($minimumSymbolsp) ?: $minimumSymbols = (int) $minimumSymbolsp;
                empty($minimumUppercasep) ?: $minimumUppercase = (int) $minimumUppercasep;
            }

            // If the field is empty and not required, the field is valid.
            $valueLength = \strlen($value);

            // Load language file of com_users component
            Factory::getApplication()->getLanguage()->load('com_users');

            // We set a maximum length to prevent abuse since it is unfiltered.
            if ($valueLength > 4096) {
                $this->app->enqueueMessage(Text::_('COM_USERS_MSG_PASSWORD_TOO_LONG'), 'error');
            }

            // We don't allow white space inside passwords
            $valueTrim = trim($value);

            // Set a variable to check if any errors are made in password
            $validPassword = true;

            if (\strlen($valueTrim) != $valueLength) {
                $this->app->enqueueMessage(
                    Text::_('COM_USERS_MSG_SPACES_IN_PASSWORD'),
                    'error'
                );

                $validPassword = false;
            }

            // Minimum number of integers required
            if (!empty($minimumIntegers)) {
                $nInts = preg_match_all('/[0-9]/', $value, $imatch);

                if ($nInts < $minimumIntegers) {
                    $this->app->enqueueMessage(
                        Text::plural('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', $minimumIntegers),
                        'error'
                    );

                    $validPassword = false;
                }
            }

            // Minimum number of symbols required
            if (!empty($minimumSymbols)) {
                $nsymbols = preg_match_all('[\W]', $value, $smatch);

                if ($nsymbols < $minimumSymbols) {
                    $this->app->enqueueMessage(
                        Text::plural('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols),
                        'error'
                    );

                    $validPassword = false;
                }
            }

            // Minimum number of upper case ASCII characters required
            if (!empty($minimumUppercase)) {
                $nUppercase = preg_match_all('/[A-Z]/', $value, $umatch);

                if ($nUppercase < $minimumUppercase) {
                    $this->app->enqueueMessage(
                        Text::plural('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase),
                        'error'
                    );

                    $validPassword = false;
                }
            }

            // Minimum length option
            if (!empty($minimumLength)) {
                if (\strlen((string) $value) < $minimumLength) {
                    $this->app->enqueueMessage(
                        Text::plural('COM_USERS_MSG_PASSWORD_TOO_SHORT_N', $minimumLength),
                        'error'
                    );

                    $validPassword = false;
                }
            }

            // If valid has violated any rules above return false.
            if (!$validPassword) {
                return false;
            }
        }

        $post = array_intersect_key($post, array_flip($allow));

        if (empty($post)) {
            return true;
        }

        $username = $this->user->get('username');
        $user     = new User($this->user->id);

        // Bind the form fields to the user table and save.
        if (!$user->bind($post) || !$user->save(true)) {
            $this->app->enqueueMessage($user->getError(), 'error');

            return false;
        }

        // Reload the user.
        if (KunenaUserHelper::getMyself()->userid == $this->user->id) {
            $this->user->load($this->user->id);
            $session = Factory::getSession();
            $session->set('user', $this->user);

            // Update session if username has been changed
            // TODO : the table session isn't able to be loaded with that
            /* if ($username && $username != $this->user->username)
            {
                $table = Table::getInstance('session', 'JTable');
                $table->load($session->getId());

                $table->username = $this->user->username;
                $table->store();
            }*/
        }

        return true;
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function saveProfile()
    {
        $input  = $this->app->input;
        $method = $input->getMethod();
        $user   = KunenaFactory::getUser($input->$method->get('userid', 0, 'int'));

        if ($this->config->signature) {
            $signature = $input->$method->get('signature', '', 'raw');

            if ($this->me->checkUserAllowedLinksImages() && $signature != $user->signature) {
                $signature = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $signature);
                $signature = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $signature);

                // When the bbcode urls and images are removed just remove the others links
                $signature = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $signature);

                $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED_WITHOUT_LINKS_IMAGES'), 'success');
            }

            $user->signature = $signature;
        }

        $user->personalText = $input->$method->get('personalText', '', 'string');
        $birthdate          = $input->$method->get('birthdate', '', 'string');

        if ($birthdate) {
            if ($birthdate == '11/30/-0001') {
                $birthdate = '1901/01/01';
            }

            $date = Factory::getDate($birthdate);

            $birthdate = $date->format('Y-m-d');
        }

        if ($birthdate == null) {
            $now       = new Date();
            $birthdate = $now->format('Y-m-d');
        }

        foreach ($user->socialButtons() as $key => $social) {
            $user->$key = str_replace(' ', '', trim($input->$method->get($key, '', 'string')));
        }

        $user->birthdate = $birthdate;
        $user->location  = trim($input->$method->get('location', '', 'string'));
        $user->gender    = $input->$method->get('gender', 0, 'int');

        $user->websitename = $input->$method->get('websitename', '', 'string');
        $user->websiteurl  = $input->$method->get('websiteurl', '', 'string');

        // Save avatar from gallery
        $avatar_gallery = $input->$method->get('avatar_gallery', '', 'string');

        if (!empty($avatar_gallery)) {
            $user->avatar = $avatar_gallery;
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function saveSettings()
    {
        $this->user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

        if ($this->app->input->get('hidemail', null) === null) {
            return;
        }

        $this->user->ordering     = $this->app->input->getInt('messageordering', 0);
        $this->user->hideEmail    = $this->app->input->getInt('hidemail', 1);
        $this->user->showOnline   = $this->app->input->getInt('showonline', 1);
        $this->user->canSubscribe = $this->app->input->getInt('cansubscribe', -1);
        $this->user->userListtime = $this->app->input->getInt('userlisttime', -2);
        $this->user->socialshare  = $this->app->input->getInt('socialshare', 1);
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function ban()
    {
        $user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

        if (!$user->exists() || !Session::checkToken('post')) {
            $this->setRedirect($user->getUrl(false), Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

            return;
        }

        $ban = KunenaBan::getInstanceByUserid($user->userid, true);

        try {
            $ban->canBan();
        } catch (Exception $e) {
            $this->setRedirect($user->getUrl(false), $e->getMessage(), 'error');

            return;
        }

        $ip             = $this->app->input->getString('ip', '');
        $banlevel       = $this->app->input->getInt('banlevel', 0);
        $expiration     = $this->app->input->getString('expiration', '');
        $reason_private = $this->app->input->getString('reason_private', '');
        $reason_public  = $this->app->input->getString('reason_public', '');
        $comment        = $this->app->input->getString('comment', '');

        $banDelPosts     = $this->app->input->getString('bandelposts', '');
        $banDelPostsPerm = $this->app->input->getString('bandelpostsperm', '');
        $DelAvatar       = $this->app->input->getString('delavatar', '');
        $DelSignature    = $this->app->input->getString('delsignature', '');
        $DelProfileInfo  = $this->app->input->getString('delprofileinfo', '');

        $delban = $this->app->input->getString('delban', '');

        if (!$ban->id) {
            $ban->ban($user->userid, $ip, $banlevel, $expiration, $reason_private, $reason_public, $comment);
            $success = $ban->save();

            // Send report to stopforumspam
            $this->report($user->userid);
        } else {
            if ($delban) {
                $ban->unBan($comment);
                $success = $ban->save();
            } else {
                $ban->blocked = $banlevel;
                $ban->setExpiration($expiration, $comment);
                $ban->setReason($reason_public, $reason_private);
                $success = $ban->save();
            }
        }

        if ($banlevel) {
            if ($ban->isEnabled()) {
                $this->app->logout($user->userid);
                $message = Text::_('COM_KUNENA_USER_BLOCKED_DONE');
                $log     = KunenaLog::LOG_USER_BLOCK;
            } else {
                $message = Text::_('COM_KUNENA_USER_UNBLOCKED_DONE');
                $log     = KunenaLog::LOG_USER_UNBLOCK;
            }
        } else {
            if ($ban->isEnabled()) {
                $message = Text::_('COM_KUNENA_USER_BANNED_DONE');
                $log     = KunenaLog::LOG_USER_BAN;
            } else {
                $message = Text::_('COM_KUNENA_USER_UNBANNED_DONE');
                $log     = KunenaLog::LOG_USER_UNBAN;
            }
        }

        try {
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if ($success) {
            if ($this->config->logModeration) {
                KunenaLog::log(
                    KunenaLog::TYPE_MODERATION,
                    $log,
                    [
                        'expiration'     => $delban ? 'NOW' : $expiration,
                        'reason_private' => $reason_private,
                        'reason_public'  => $reason_public,
                        'comment'        => $comment,
                        'options'        => [
                            'resetProfile'   => (bool) $DelProfileInfo,
                            'resetSignature' => (bool) $DelSignature || $DelProfileInfo,
                            'deleteAvatar'   => (bool) $DelAvatar || $DelProfileInfo,
                            'deletePosts'    => (bool) $banDelPosts,
                        ],
                    ],
                    null,
                    null,
                    $user
                );

                KunenaUserHelper::recountBanned();
            }

            $this->app->enqueueMessage($message, 'success');
        }

        if (!empty($DelAvatar) || !empty($DelProfileInfo)) {
            $avatar_deleted = '';

            // Delete avatar from file system
            if (is_file(JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar) && !stristr($user->avatar, 'gallery/')) {
                File::delete(JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar);
                $avatar_deleted = Text::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR_FILESYSTEM');
            }

            $user->avatar = '';
            $user->save();
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR') . $avatar_deleted, 'success');
        }

        $now       = new Date();
        $birthdate = $now->format('Y-m-d');

        if (!empty($DelProfileInfo)) {
            $user->personalText = '';
            $user->birthdate    = $birthdate;
            $user->location     = '';
            $user->gender       = 0;

            foreach ($user->socialButtons() as $social) {
                $user->$social = '';
            }

            $user->websitename = '';
            $user->websiteurl  = '';
            $user->signature   = '';
            $user->save();
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_PROFILEINFO'), 'success');
        } elseif (!empty($DelSignature)) {
            $user->signature = '';
            $user->save();
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_SIGNATURE'), 'success');
        }

        if (!empty($banDelPosts)) {
            $params = ['starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid, 'mode' => 'unapproved'];

            list($total, $messages) = KunenaMessageHelper::getLatestMessages(false, 0, 0, $params);

            $parmas_recent = ['starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid];

            list($total, $messages_recent) = KunenaMessageHelper::getLatestMessages(false, 0, 0, $parmas_recent);

            $messages = array_merge($messages_recent, $messages);

            foreach ($messages as $mes) {
                $mes->publish(KunenaForum::DELETED);
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_MESSAGES'), 'success');
        }

        if (!empty($banDelPostsPerm)) {
            $params = ['starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid, 'mode' => 'unapproved'];

            list($total, $messages) = KunenaMessageHelper::getLatestMessages(false, 0, 0, $params);

            $parmas_recent = ['starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid];

            list($total, $messages_recent) = KunenaMessageHelper::getLatestMessages(false, 0, 0, $parmas_recent);

            $messages = array_merge($messages_recent, $messages);

            foreach ($messages as $mes) {
                $mes->delete();
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_PERM_BAD_MESSAGES'), 'success');
        }

        $this->setRedirect($user->getUrl(false));
    }

    // Internal functions:

    /**
     * Reports a user to stopforumspam.com
     *
     * @param   int          $userid    userid
     * @param   string|null  $evidence  evidence
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function report(int $userid = 0, string $evidence = null)
    {
        if (!$this->config->stopForumSpamKey || !$userid) {
            return false;
        }

        $spammer = Factory::getUser($userid);

        // TODO: remove this query by getting the ip of user by an another way
        $query = $this->db->getQuery(true);
        $query
            ->select($this->db->quoteName(['ip']))
            ->from($this->db->quoteName('#__kunena_messages'))
            ->where($this->db->quoteName('userid') . ' = ' . $this->db->quote($userid))
            ->group($this->db->quoteName('ip'))
            ->order('time DESC');

        $this->db->setQuery($query, 0, 1);
        $ip = $this->db->loadResult();

        if (!empty($ip)) {
            $data                     = [];
            $data['username']         = $spammer->username;
            $data['ip']               = $ip;
            $data['email']            = $spammer->email;
            $data['stopForumSpamKey'] = $this->config->stopForumSpamKey;
            $data['evidence']         = $evidence;

            try {
                $result = KunenaUserHelper::storeCheckStopforumspam($data, 'add');
            } catch (Exception $e) {
                // Is this empty by design?
                $this->app->enqueueMessage();

                return false;
            }

            if ($result != false) {
                // Report accepted. There is no need to display the reason
                $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_SUCCESS'), 'success');

                return true;
            } else {
                // Report failed or refused
                $reasons = [];
                preg_match('/<p>.*<\/p>/', $result->body, $reasons);

                // Stopforumspam returns only one reason, which is reasons[0], but we need to strip out the html tags before using it
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_STOPFORUMSPAM_REPORT_FAILED', strip_tags($reasons[0])), 'error');

                return false;
            }
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_NO_IP_GIVEN'), 'error');
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function cancel()
    {
        $user = KunenaFactory::getUser();
        $this->setRedirect($user->getUrl(false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function login()
    {
        if (!$this->app->getIdentity()->guest || !Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $input  = $this->app->input;
        $method = $input->getMethod();

        $username  = $input->$method->get('username', '', 'USERNAME');
        $password  = $input->$method->get('password', '', 'RAW');
        $remember  = $this->input->getBool('remember', false);
        $secretkey = $input->$method->get('secretkey', '', 'RAW');

        $login = KunenaLogin::getInstance();
        $error = $login->loginUser($username, $password, $remember, $secretkey);

        // Get the return url from the request and validate that it is internal.
        $return = base64_decode($input->post->get('return', '', 'BASE64'));

        if (!$error && $return && Uri::isInternal($return)) {
            // Redirect the user.
            $this->setRedirect(Route::_($return, false));

            return;
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
    public function logout()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $login = KunenaLogin::getInstance();

        if (!$this->app->getIdentity()->guest) {
            $login->logoutUser();
        }

        // Get the return url from the request and validate that it is internal.
        $return = base64_decode($this->app->input->getBase64('return'));

        if ($return && Uri::isInternal($return)) {
            // Redirect the user.
            $this->setRedirect(Route::_($return, false));

            return;
        }

        $this->setRedirectBack();
    }

    /**
     * Save online status for user
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function status()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $status     = $this->app->input->getInt('status', 0);
        $me         = KunenaUserHelper::getMyself();
        $me->status = $status;

        try {
            $me->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if ($me->save()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STATUS_SAVED'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * Set online status text for user
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function statusText()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $status_text     = $this->app->input->post->getString('status_text', '');
        $me              = KunenaUserHelper::getMyself();
        $me->status_text = $status_text;

        try {
            $me->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if ($me->save()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STATUS_SAVED'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * Upload avatar with AJAX.
     *
     * @return  void
     *
     * @throws  null
     * @since   Kunena 5.1
     */
    public function upload()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        $upload = KunenaUpload::getInstance();
        $user   = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

        // We are converting all exceptions into JSON.
        try {
            $caption = $this->input->getString('caption');
            $options = [
                'filename'   => $this->input->getString('filename'),
                'size'       => $this->input->getInt('size'),
                'mime'       => $this->input->getString('mime'),
                'hash'       => $this->input->getString('hash'),
                'chunkStart' => $this->input->getInt('chunkStart', 0),
                'chunkEnd'   => $this->input->getInt('chunkEnd', 0),
                'image_type' => 'avatar',
            ];

            // Upload!
            $this->config->avatarTypes = strtolower($this->config->avatarTypes);
            $upload->addExtensions(explode(',', $this->config->avatarTypes));
            $response = (object) $upload->ajaxUpload($options);

            if (!empty($response->completed)) {
                $this->deleteOldAvatars();

                // We have it all, lets update the avatar in user table
                $uploadFile = $upload->getProtectedFile();
                list($basename, $extension) = $upload->splitFilename();

                File::copy($uploadFile, KPATH_MEDIA . '/avatars/users/avatar' . $user->userid . '.' . $extension);

                KunenaPath::setPermissions(KPATH_MEDIA . '/avatars/users/avatar' . $user->userid . '.' . $extension);

                // Save in the table \Kunena\Forum\Libraries\User\KunenaUser
                $kuser            = $user;
                $kuser->avatar    = 'users/avatar' . $user->userid . '.' . $extension;
                $kuser->timestamp = round(microtime(true));
                $kuser->save();
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
     * Delete previoulsy uploaded avatars from filesystem
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function deleteOldAvatars()
    {
        $user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

        if (preg_match('|^users/|', $user->avatar)) {
            // Delete old uploaded avatars:
            if (is_dir(KPATH_MEDIA . '/avatars/resized')) {
                $deletelist = Folder::folders(KPATH_MEDIA . '/avatars/resized', '.', false, true);

                foreach ($deletelist as $delete) {
                    if (is_file($delete . '/' . $user->avatar)) {
                        File::delete($delete . '/' . $user->avatar);
                    }
                }
            }

            if (is_file(KPATH_MEDIA . '/avatars/' . $user->avatar)) {
                File::delete(KPATH_MEDIA . '/avatars/' . $user->avatar);
            }
        }
    }

    /**
     * Remove avatar with AJAX
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 5.1
     */
    public function removeAvatar()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $success = [];
        $kuser   = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

        if (
            KunenaUserHelper::getMyself()->userid == $kuser->userid || KunenaUserHelper::getMyself()->isAdmin()
            || KunenaUserHelper::getMyself()->isModerator()
        ) {
            $this->deleteOldAvatars();

            // Save in the table \Kunena\Forum\Libraries\User\KunenaUser
            $kuser->avatar = '';
            $success       = $kuser->save();
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
            // Do nothing
        }

        echo json_encode($success);

        jexit();
    }

    /**
     * Get avatar attached to a profile with AJAX.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 5.1
     */
    public function loadAvatar()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('request')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $userid = $this->input->getInt('userid');
        $kuser  = KunenaFactory::getUser($userid);

        $avatar       = new stdClass();
        $avatar->name = $kuser->avatar;

        if (!empty($kuser->avatar)) {
            $avatar->path = Uri::root() . 'media/kunena/avatars/' . $kuser->avatar;
        } else {
            $avatar->path = Uri::root() . 'media/kunena/avatars/' . KunenaConfig::getInstance()->defaultAvatar;
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        while (@ob_end_clean()) {
        }

        echo json_encode($avatar);

        jexit();
    }

    /**
     * Delete attachment(s) selected in user profile
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function delfile()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        if (!empty($cid)) {
            $number = 0;

            foreach ($cid as $id) {
                $attachment  = KunenaAttachmentHelper::get($id);
                $message     = $attachment->getMessage();
                $attachments = [$attachment->id, 1];
                $attach      = [];
                $removeList  = array_keys(array_diff_key($attachments, $attach));
                $removeList  = ArrayHelper::toInteger($removeList);
                $message->removeAttachments($removeList);

                $topic = $message->getTopic();

                if ($attachment->isAuthorised('delete') && $attachment->delete()) {
                    $message_text = $attachment->removeBBCodeInMessage($message->message);

                    if ($message_text !== false) {
                        $message->message = $message_text;
                    }

                    $message->save();

                    if ($topic->attachments > 0) {
                        $topic->attachments = $topic->attachments - 1;
                        $topic->save(false);
                    }

                    $number++;
                }
            }

            if ($number > 0) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ATTACHMENTS_DELETE_SUCCESSFULLY', $number), 'success');
                $this->setRedirectBack();

                return;
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_DELETE_FAILED'), 'error');
                $this->setRedirectBack();

                return;
            }
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_NO_ATTACHMENTS_SELECTED'), 'error');
        $this->setRedirectBack();
    }
}
