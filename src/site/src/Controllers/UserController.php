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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Event\KunenaAfterProfileUpdateEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Karma\KunenaKarmaHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Notification\KunenaPopupNotification;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Response\KunenaResponseJson;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Forum\Libraries\User\KunenaUserSocials;
use Kunena\Forum\Plugin\Kunena\Kunena\Helper\KunenaProfileKunena;
use RuntimeException;
use stdClass;

/**
 * Kunena User Controller
 *
 * @property KunenaUser|null user user
 *
 * @since   Kunena 2.0
 */
class UserController extends KunenaController
{
    public $user;

    /**
     * @param   bool  $cachable   catchable
     * @param   bool  $urlparams  urlparams
     *
     * @return BaseController
     *
     * @since   Kunena 6.0
     * @throws Exception
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

        if ($redirect && $this->app->getInput()->getCmd('format', 'html') == 'html') {
            $profileIntegration = KunenaFactory::getProfile();
            $layout             = $this->app->getInput()->getCmd('layout', 'default');

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

        $layout = $this->app->getInput()->getCmd('layout', 'default');

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
     * @since   Kunena 6.0
     */
    public function search()
    {
        $uri = new Uri('index.php?option=com_kunena&view=user&layout=list');

        $search     = $this->app->getInput()->getString('search', '');
        $limitstart = $this->app->getInput()->getInt('limitstart', 0);

        if ($search) {
            $uri->setVar('search', $search);
        }

        if ($limitstart) {
            $uri->setVar('limitstart', $limitstart);
        }

        $this->setRedirect(KunenaRoute::_($uri, false));
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function change()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $layout = $this->app->getInput()->getString('topicLayout', 'default');
        $this->me->setTopicLayout($layout);
        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  Exception
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

        $userid = $this->app->getInput()->getInt('userid', 0);

        $target               = KunenaFactory::getUser($userid);
        $karma                = KunenaKarmaHelper::get(0);
        $karma->userid        = $this->me->userid;
        $karma->target_userid = $userid;

        // A banned user gives no karma (like he may not post).
        if (!$this->config->showKarma || !$this->me->exists() || $this->me->isBanned() || !$target->exists() || $karmaDelta == 0) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_USER_ERROR_KARMA'), 'error');
            $this->setRedirectBack();

            return;
        }

        $now = Factory::getDate()->toUnix();

        // Check the time of the last karma given by this user to the target
        $lastKarmaTime = $karma->getLastTimeKarma();

        if (!$this->me->isModerator() && $lastKarmaTime && $now - $lastKarmaTime < $karma_delay) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_WAIT'), 'warning');
            $this->setRedirectBack();

            return;
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

        $target->karma += $karmaDelta;

        // Save the user with the plus or minus karma given, in #__kunena_user remove karma_time
        try {
            $target->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        $karma->userid        = KunenaUserHelper::getMyself()->userid;
        $karma->target_userid = $userid;

        try {
            $karma->save();
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
        $userid = $this->app->getInput()->getInt('userid');

        // A list in the request (userid[]=1) is no user.
        if (\is_array($userid)) {
            $userid = 0;
        }

        if (!Session::checkToken('post')) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ERROR_TOKEN'), 403);
        }

        // Check permission
        $moderator = KunenaUserHelper::getMyself()->isModerator();
        $my        = $this->app->getIdentity();

        if (!$moderator) {
            if ($userid != $my->id) {
                throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_PROFILE_NOT_MODERATOR_RIGHT'), 403);
            }
        }

        // Make sure that the user exists.
        if (!$this->me->exists()) {
            throw new KunenaExceptionAuthorise(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
        }

        if (!$userid) {
            $this->user = $this->app->getIdentity();
        } else {
            $this->user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userid);
        }

        // The same rule as the form: a moderator does not change the profile of an administrator.
        KunenaUserHelper::get((int) $this->user->id)->tryAuthorise('edit');

        // The account of Joomla (the name, the e-mail address, the password) is changed by the user himself or an administrator;
        // a moderator changes the profile of the forum only.
        $success = ($this->user->id == $my->id || $this->me->isAdmin()) ? $this->saveUser() : true;

        if (!$success) {
            $errors++;
            $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_ACCOUNT_NOT_SAVED'), 'error');
        } elseif ($this->app->getInput()->post->get('nickname', null, 'raw') !== null && $this->user->id) {
            // The nickname is a custom field of the Joomla users: it is saved with the account.
            KunenaUserName::setDisplayName((int) $this->user->id, (string) $this->app->getInput()->post->get('nickname', '', 'raw'));
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

        $dispatcher = Factory::getContainer()->get(DispatcherInterface::class);
        $dispatcher->dispatch('OnKunenaAfterProfileUpdate', new KunenaAfterProfileUpdateEvent('OnKunenaAfterProfileUpdate', [$this->user, $success]));

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
        $post           = $this->app->getInput()->post->getArray();
        $post_password  = $this->app->getInput()->post->get('password', '', 'raw');
        $post_password2 = $this->app->getInput()->post->get('password2', '', 'raw');

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
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_PROFILE_PASSWORD_NOT_MINIMUM', $params->get('minimum_length')), 'error');

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

                empty($minimumLengthp) ?: $minimumLength       = (int) $minimumLengthp;
                empty($minimumIntegersp) ?: $minimumIntegers   = (int) $minimumIntegersp;
                empty($minimumSymbolsp) ?: $minimumSymbols     = (int) $minimumSymbolsp;
                empty($minimumUppercasep) ?: $minimumUppercase = (int) $minimumUppercasep;
            }

            // If the field is empty and not required, the field is valid.
            $valueLength = \strlen($value);

            // Load language file of com_users component
            $this->app->getLanguage()->load('com_users');

            // Set a variable to check if any errors are made in password
            $validPassword = true;

            // We set a maximum length to prevent abuse since it is unfiltered.
            if ($valueLength > 4096) {
                $this->app->enqueueMessage(Text::_('COM_USERS_MSG_PASSWORD_TOO_LONG'), 'error');

                $validPassword = false;
            }

            // We don't allow white space inside passwords
            $valueTrim = trim($value);

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
        try {
            $user->bind($post);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        try {
            $user->save(true);
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        // Reload the user.
        if (KunenaUserHelper::getMyself()->userid == $this->user->id) {
            $this->user->load($this->user->id);
            $session = $this->app->getSession();
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
        $input  = $this->app->getInput();
        $method = $input->getMethod();
        $user   = KunenaFactory::getUser($input->$method->get('userid', 0, 'int'));

        if ($this->config->signature) {
            $signature = $input->$method->get('signature', '', 'raw');

            if (!$this->me->checkUserAllowedLinksImages() && $signature != $user->signature) {
                $signature = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $signature);
                $signature = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $signature);

                // When the bbcode urls and images are removed just remove the others links
                $signature = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $signature);

                $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED_WITHOUT_LINKS_IMAGES'), 'success');
            }

            // The maximum length of the option (the form only stops typing, a request can send more).
            $maxSig = (int) $this->config->maxSig;

            if ($maxSig > 0 && mb_strlen($signature) > $maxSig) {
                $signature = mb_substr($signature, 0, $maxSig);
            }

            $user->signature = $signature;
        }

        // Save values entered by user for the social
        $socials = KunenaUserSocials::getInstance($user->userid);

        foreach ($socials as $key => $social) {
            if ($input->$method->get('social' . $key, '', 'string') !== null) {
                $socials->$key->value = $input->$method->get('social' . $key, '', 'string');
            }
        }

        $socials->save();

        $user->personalText = $input->$method->get('personalText', '', 'string');

        // The maximum length of the template (the form only stops typing, a request can send more).
        $maxPersonalText = (int) KunenaFactory::getTemplate()->params->get('maxPersonalText', 50);

        if ($maxPersonalText > 0 && mb_strlen((string) $user->personalText) > $maxPersonalText) {
            $user->personalText = mb_substr((string) $user->personalText, 0, $maxPersonalText);
        }
        $birthdate          = $input->$method->get('birthdate', '', 'string');

        if ($birthdate) {
            if ($birthdate == '11/30/-0001') {
                $birthdate = '1901/01/01';
            }

            // The field writes the date in the format of the date picker (dd/mm/yyyy was an error of the date parser and the
            // profile was not saved): a text which is no date is no birthdate.
            $birthdate = KunenaDate::fromPicker($birthdate) ?? '';
        }

        if (!$birthdate) {
            // No birthdate given: store the value which means "not set" (see the database default and the profile display).
            $birthdate = '1000-01-01';
        }

        $user->birthdate = $birthdate;
        $user->location  = trim($input->$method->get('location', '', 'string'));
        $user->gender    = $input->$method->get('gender', 0, 'int');

        $user->websitename = $input->$method->get('websitename', '', 'string');
        $websiteurl        = trim((string) $input->$method->get('websiteurl', '', 'string'));

        // The address has 50 characters in the database: a longer one is not saved (a part of it would be a wrong link).
        if (mb_strlen($websiteurl) > 50) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_PROFILE_WEBSITE_TOO_LONG', 50), 'warning');
        } else {
            $user->websiteurl = $websiteurl;
        }

        // Save avatar from gallery
        $avatar_gallery = $input->$method->get('avatar_gallery', '', 'string');

        // Only an existing picture of the avatar gallery may be used: no other folders, no parent folder segments.
        if (
            !empty($avatar_gallery)
            && preg_match('#^gallery(?:/(?!\.)[\w .\-]+)?/(?!\.)[\w .\-]+\.(?:png|jpe?g|gif|webp)$#i', $avatar_gallery)
            && is_file(KPATH_MEDIA . '/avatars/' . $avatar_gallery)
        ) {
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
        $this->user = KunenaFactory::getUser($this->app->getInput()->getInt('userid', 0));

        if ($this->app->getInput()->get('hidemail', null) === null) {
            return;
        }

        $this->user->ordering            = $this->app->getInput()->getInt('messageordering', 0);
        $this->user->hideEmail           = $this->app->getInput()->getInt('hidemail', 1);
        $this->user->showOnline          = $this->app->getInput()->getInt('showonline', 1);
        $this->user->canSubscribe        = $this->app->getInput()->getInt('cansubscribe', -1);
        $this->user->userListtime        = $this->app->getInput()->getInt('userlisttime', -2);
        $this->user->displayAgeBirthdate = $this->app->getInput()->getInt('displayAgeBirthdate', 1);

        // The e-mail when the user is mentioned in a post: 1 (the default) is stored as no value.
        if ($this->app->getInput()->get('mentionemail', null) !== null) {
            KunenaUserHelper::setJoomlaParam((int) $this->user->userid, 'kunena_mention_email', $this->app->getInput()->getInt('mentionemail', 1) ? null : 0);
        }

        // The popups for mentions and replies: 1 (the default) is stored as no value.
        if ($this->app->getInput()->get('popupnotifications', null) !== null) {
            KunenaUserHelper::setJoomlaParam((int) $this->user->userid, 'kunena_popup_notifications', $this->app->getInput()->getInt('popupnotifications', 1) ? null : 0);
        }

        // The name which others see (when the users may choose it): -1 is the choice of the forum.
        if (KunenaUserName::userCanChoose() && $this->app->getInput()->get('namechoice', null) !== null) {
            $choice = $this->app->getInput()->getInt('namechoice', -1);

            KunenaUserName::setChoice((int) $this->user->userid, \in_array($choice, [0, 1, 2], true) ? $choice : null);
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function ban()
    {
        $user = KunenaFactory::getUser($this->app->getInput()->getInt('userid', 0));

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

        $ip             = $this->app->getInput()->getString('ip', '');
        $banlevel       = $this->app->getInput()->getInt('banlevel', 0);
        $expiration     = $this->app->getInput()->getString('expiration', '');

        // The ban box of a message: a number of days (0 is permanent) instead of a date.
        $banDays = $this->app->getInput()->getInt('ban_days', -1);

        if ($expiration === '' && $banDays > 0) {
            $expiration = Factory::getDate('+' . min($banDays, 36500) . ' days')->toSql();
        }

        // Only administrators block the user from the whole site with the ban box of a message.
        if ($this->app->getInput()->getInt('quick', 0) && $banlevel && !$this->me->isAdmin()) {
            $banlevel = 0;
        }
        $reason_private = $this->app->getInput()->getString('reason_private', '');
        $reason_public  = $this->app->getInput()->getString('reason_public', '');
        $comment        = $this->app->getInput()->getString('comment', '');

        $banDelPosts     = $this->app->getInput()->getString('bandelposts', '');
        $banDelPostsPerm = $this->app->getInput()->getString('bandelpostsperm', '');
        $DelAvatar       = $this->app->getInput()->getString('delavatar', '');
        $DelSignature    = $this->app->getInput()->getString('delsignature', '');
        $DelProfileInfo  = $this->app->getInput()->getString('delprofileinfo', '');

        $delban = $this->app->getInput()->getString('delban', '');

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
            }

            KunenaUserHelper::recountBanned();

            if ($this->config->sendMailUserBanned) {
                $this->sendMailWhenUserBannedAndUnbanned($user, $ban);
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

        if (!empty($DelProfileInfo)) {
            $user->personalText = '';
            // '1000-01-01' is the value for "no birthdate"
            $user->birthdate    = '1000-01-01';
            $user->location     = '';
            $user->gender       = 0;

            $user->websitename = '';
            $user->websiteurl  = '';
            $user->signature   = '';

            // Empty socials value in the user profile if they are filled
            $socials = KunenaUserSocials::getInstance($user->userid);

            foreach ($socials as $key => $social) {
                if (isset($social->value)) {
                    $socials->$key->value = '';
                }
            }

            $socials->save();

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

        // The ban box of a message goes back to the page (of the same site) where it was opened.
        $return = $this->app->getInput()->getString('return', '');

        $this->setRedirect($return !== '' && Uri::isInternal($return) ? $return : $user->getUrl(false));
    }

    // Internal functions:

    /**
     * Send a mail to the user when he is banned or unbanned
     *
     * @param   KunenaUser   $user
     * @param   KunenaBan    $ban
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 7.0.0
     */
    protected function sendMailWhenUserBannedAndUnbanned(KunenaUser $user, $ban)
    {
        if ($ban->isEnabled()) {
            $mailTitle =  Text::_('COM_KUNENA_USER_BANNED_MAIL_TITLE');
            $mailBody  = $this->config->mailBodyUserBanned;
        } else {
            $mailTitle = Text::_('COM_KUNENA_USER_UNBANNED_MAIL_TITLE');
            $mailBody  = $this->config->mailBodyUserUnBanned;
        }

        // Create email to notify the user which has been unbanned.
        $mailnamesender  = !empty($this->config->emailSenderName) ? MailHelper::cleanAddress($this->config->emailSenderName) : MailHelper::cleanAddress($this->config->boardTitle);
        $mail            = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
        $mail->setSubject($mailTitle);
        $mail->setSender([$this->config->email, $mailnamesender]);
        $mail->setBody($mailBody);
        KunenaEmail::send($mail, [$user->email]);
    }

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
    protected function report(int $userid = 0, ?string $evidence = null)
    {
        if (!$this->config->stopForumSpamKey || !$userid) {
            return false;
        }

        $spammer = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userid);

        // TODO: remove this query by getting the ip of user by an another way
        $query = $this->db->createQuery();
        $query
            ->select($this->db->quoteName(['ip']))
            ->from($this->db->quoteName('#__kunena_messages'))
            ->where($this->db->quoteName('userid') . ' = ' . $this->db->quote($userid))
            ->group($this->db->quoteName('ip'))
            ->order('time DESC');

        $query->setLimit(1);
        $this->db->setQuery($query);
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
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return false;
            }

            if ($result && $result->getStatusCode() == 200) {
                // Report accepted. There is no need to display the reason
                $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_SUCCESS'), 'success');

                return true;
            }
            // Report failed or refused
            $reasons = [];
            preg_match('/<p>.*<\/p>/', $result ? (string) $result->getBody() : '', $reasons);

            // Stopforumspam returns only one reason, which is reasons[0], but we need to strip out the html tags before using it
            $reason = isset($reasons[0]) ? strip_tags($reasons[0]) : ($result ? 'HTTP ' . $result->getStatusCode() : 'no answer');
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_STOPFORUMSPAM_REPORT_FAILED', $reason), 'error');

            return false;

        }
        $this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_NO_IP_GIVEN'), 'error');

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
     * @since   Kunena 6.0
     */
    public function login()
    {
        if (!$this->app->getIdentity()->guest || !Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $input  = $this->app->getInput();
        $method = $input->getMethod();

        $username  = $input->$method->get('username', '', 'USERNAME');
        $password  = $input->$method->get('password', '', 'RAW');
        $remember  = $this->input->getBool('rememberme', false);
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
        if (!empty($this->app->getInput()->getBase64('return'))) {
            $return = base64_decode($this->app->getInput()->getBase64('return'));

            if ($return && Uri::isInternal($return)) {
                // Redirect the user.
                $this->setRedirect(Route::_($return, false));

                return;
            }
        }

        $this->setRedirectBack();
    }

    /**
     * Save online status for user
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function status()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $status = $this->app->getInput()->getInt('status', 0);

        // Only the known states are valid: 0 online, 1 away, 2 busy, 3 invisible.
        if ($status < 0 || $status > 3) {
            $status = 0;
        }

        $me         = KunenaUserHelper::getMyself();
        $me->status = $status;

        $saved = false;

        try {
            $saved = $me->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if ($saved) {
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
     * @since   Kunena 6.0
     */
    public function statusText()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        // The column is varchar(191): cut longer texts instead of failing the save.
        $status_text     = mb_substr($this->app->getInput()->post->getString('status_text', ''), 0, 191);
        $me              = KunenaUserHelper::getMyself();
        $me->status_text = $status_text;

        $saved = false;

        try {
            $saved = $me->save();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        if ($saved) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_STATUS_SAVED'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * Upload avatar with AJAX.
     *
     * @return  void
     *
     * @since   Kunena 5.1
     */
    public function upload()
    {
        // Only support JSON requests.
        if ($this->input->getWord('format', 'html') != 'json') {
            throw new RuntimeException(Text::_('Bad Request'), 400);
        }

        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $upload       = KunenaUpload::getInstance();
        $joomlaUserId = Factory::getApplication()->getIdentity()->id;
        $user         = KunenaFactory::getUser($joomlaUserId);

        // The rule of the profile form: a guest has no avatar, and a banned user may not change his profile.
        if (!$user->exists() || !$user->isAuthorised('edit')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

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
                $this->deleteOldAvatars($user);

                // We have it all, lets update the avatar in user table
                $uploadFile                 = $upload->getProtectedFile();
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

        echo new KunenaResponseJson($response);

        jexit();
    }

    /**
     * Delete previoulsy uploaded avatars from filesystem
     *
     * @param   KunenaUser   $user   KunenaUser object
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function deleteOldAvatars(KunenaUser $user)
    {
        if (!$user->exists()) {
            throw new RuntimeException('Delete avatar cannot be done, user not exist');
        }

        if (!empty($user->avatar)) {
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

        $success      = [];
        $joomlaUserId = Factory::getApplication()->getIdentity()->id;
        $kuser        = KunenaFactory::getUser($joomlaUserId);

        if (
            KunenaUserHelper::getMyself()->userid == $kuser->userid || KunenaUserHelper::getMyself()->isAdmin()
            || KunenaUserHelper::getMyself()->isModerator()
        ) {
            $this->deleteOldAvatars($kuser);

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
            $avatar->path = Uri::root() . 'media/kunena/avatars/' . $this->config->defaultAvatar;
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
                $attachment = KunenaAttachmentHelper::get($id);
                $message    = $attachment->getMessage();

                if (!$attachment->isAuthorised('delete')) {
                    continue;
                }

                // A file which belongs to no message: only the file goes.
                if (!$message->exists()) {
                    if ($attachment->delete()) {
                        $number++;
                    }

                    continue;
                }

                // The chosen file: the message deletes it and counts it off the topic on save. (The list was [0, 1], the keys
                // of an array: the attachment 1 of the message went too.)
                $message->removeAttachments([$attachment->id]);

                $message_text = $attachment->removeBBCodeInMessage($message->message);

                if (!empty($message_text) && $attachment->inline) {
                    $message->message = $message_text;
                }

                if ($message->save()) {
                    $number++;
                }
            }

            if ($number > 0) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ATTACHMENTS_DELETE_SUCCESSFULLY', $number), 'success');
                $this->setRedirectBack();

                return;
            }
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_DELETE_FAILED'), 'error');
            $this->setRedirectBack();

            return;

        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_NO_ATTACHMENTS_SELECTED'), 'error');
        $this->setRedirectBack();
    }

    /**
     * The users to mention in a new topic: the users whose name starts or contains the typed text (q), who may read the category.
     *
     * @param   KunenaUser  $me         The user who writes the topic.
     * @param   int[]       $ignoredBy  The users who ignore him.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    protected function searchUsersToMention($me, array $ignoredBy): array
    {
        $text = trim($this->input->getString('q', ''));
        $list = [];

        if ($text === '') {
            return $list;
        }

        $category = KunenaCategoryHelper::get($this->input->getInt('catid', 0));

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('a.id'))
            ->from($db->quoteName('#__users', 'a'))
            ->where($db->quoteName('a.block') . ' = 0')
            ->where($db->quoteName('a.id') . ' <> ' . (int) $me->userid)
            ->where(KunenaUserName::getSearchCondition($text, 'a'))
            ->order($db->quoteName('a.username') . ' ASC')
            ->setLimit(30);
        $ids = array_map('intval', $db->setQuery($query)->loadColumn());

        foreach (KunenaUserHelper::loadUsers($ids) as $found) {
            if (\count($list) >= 8) {
                break;
            }

            if (\in_array((int) $found->userid, $ignoredBy, true) || ($category->exists() && !$category->isAuthorised('read', $found))) {
                continue;
            }

            $obj           = new stdClass();
            $obj->id       = $found->userid;
            $obj->userid   = $found->userid;
            $obj->avatar   = $found->getAvatarURL();
            $obj->username = $this->escape($found->getName('', false));
            $list[]        = $obj;
        }

        return $list;
    }

    /**
     * Get the list of users to mentions in the topic by the given topicid
     *
     * @return  void
     *
     * @since   Kunena 6.3
     */
    public function getusersmentions()
    {
        if (!Session::checkToken('get')) {
            throw new RuntimeException(Text::_('Forbidden'), 403);
        }

        $id = $this->input->getInt('topicid', 0);

        // Check if the user requesting it has made a post in the topic (a new topic has no users yet: the users are searched)
        $user = KunenaFactory::getUser();
        // The users of a topic are for the users who may read it (not only for those who wrote in it): a reply mentions them.
        $topic = KunenaTopicHelper::get($id);

        if (!$user->exists() || ($id > 0 && (!$topic->exists() || !$topic->isAuthorised('read', $user)))) {
            throw new RuntimeException(Text::_('Not allowed'), 403);
        }

        $userListMentions = [];

        // Users who ignore the user can not be mentioned by him.
        $ignoredBy = KunenaUserIgnore::getIgnoredBy((int) $user->userid);

        if ($id > 0) {
            $topicId = [0 => $id];

            $userIdsList = KunenaTopicUserHelper::getUserIds($topicId);

            $usersList = KunenaUserHelper::loadUsers($userIdsList);

            foreach ($usersList as $key => $user) {
                if (\in_array((int) $user->userid, $ignoredBy, true)) {
                    continue;
                }

                $obj         = new stdClass();
                $obj->id     = $key;
                $obj->userid = $user->userid;
                $obj->avatar = $user->getAvatarURL();
                // The name which is shown of the user (username, real name or display name).
                $obj->username = $this->escape($user->getName('', false));

                $userListMentions[] = $obj;
            }
        } else {
            $userListMentions = $this->searchUsersToMention($user, $ignoredBy);
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $response = json_encode($userListMentions);

        if (ob_get_length()) {
            ob_end_clean();
        }

        echo json_encode($response);

        jexit();
    }

    /**
     * The popups for the user: the messages after the last one the browser has seen (see assets/js/notifications.js).
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public function notifications()
    {
        $answer = ['enabled' => false, 'cursor' => 0, 'items' => [], 'more' => 0];

        if ($this->me->exists() && KunenaPopupNotification::isEnabledFor((int) $this->me->userid)) {
            $answer = ['enabled' => true] + KunenaPopupNotification::get((int) $this->me->userid, $this->input->getInt('after', 0));
        }

        header('Content-type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        if (ob_get_length()) {
            ob_end_clean();
        }

        echo json_encode($answer);

        jexit();
    }

    /**
     * Get the list of users by autocomplete in the search form
     *
     * @return  void
     *
     * @since   Kunena 6.3
     */
    public function getusersmentionssearch()
    {
        $user = trim($this->input->getString('usersearch', ''));
        $row  = [];

        // Guests may search only when they are allowed to see the user list, and an empty search must not list every user.
        if (($this->me->exists() || $this->config->userlistAllowed) && $user !== '') {
            $db     = Factory::getContainer()->get('DatabaseDriver');
            $query  = $db->createQuery();
            $query->select($db->quoteName(['id', 'username']))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('block') . ' = 0')
                ->where($db->quoteName('username') . ' LIKE ' . $db->quote('%' . $db->escape($user, true) . '%'))
                ->order($db->quoteName('username') . ' ASC')
                ->setLimit(10);
            $db->setQuery($query);

            // The name which the forum shows (not the real name of Joomla when the forum shows usernames).
            foreach ((array) $db->loadObjectList() as $found) {
                $row[] = (object) [
                    'name'     => KunenaUserHelper::get((int) $found->id)->getName('', false),
                    'username' => $found->username,
                ];
            }
        }

        header('Content-type: application/json');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        //$response = [];

        if (ob_get_length()) {
            ob_end_clean();
        }

        echo json_encode($row);

        jexit();
    }

    /**
     * Put a user on the ignore list of the logged in user.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function ignore()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $me     = KunenaUserHelper::getMyself();
        $target = KunenaUserHelper::get($this->app->getInput()->getInt('userid', 0));

        try {
            if (!KunenaUserIgnore::canIgnore($me, $target)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_IGNORE_NOT_ALLOWED'), 'error');
            } elseif (KunenaUserIgnore::ignore($me, $target)) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_IGNORE_DONE', $target->getName()), 'success');
            }
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * Take a user off the ignore list of the logged in user.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function unignore()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $me     = KunenaUserHelper::getMyself();
        $target = KunenaUserHelper::get($this->app->getInput()->getInt('userid', 0));

        try {
            if ($me->exists() && KunenaUserIgnore::unignore($me->userid, $target->userid)) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_IGNORE_REMOVED', $target->getName()), 'success');
            }
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * Follow a user: the logged in user gets an e-mail when that user starts a new topic.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function follow()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $me     = KunenaUserHelper::getMyself();
        $target = KunenaUserHelper::get($this->app->getInput()->getInt('userid', 0));

        try {
            if (!KunenaUserFollow::canFollow($me, $target)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_FOLLOW_NOT_ALLOWED'), 'error');
            } elseif (KunenaUserFollow::follow($me, $target)) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_FOLLOW_DONE', $target->getName()), 'success');
            }
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }

    /**
     * Stop following a user.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function unfollow()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $me     = KunenaUserHelper::getMyself();
        $target = KunenaUserHelper::get($this->app->getInput()->getInt('userid', 0));

        try {
            if ($me->exists() && KunenaUserFollow::unfollow($me->userid, $target->userid)) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_FOLLOW_REMOVED', $target->getName()), 'success');
            }
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirectBack();
    }
}
