<?php

/**
 * Kunena Privacy Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Privacy
 *
 * @copyright       Copyright (C) 2023 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Privacy\Kunena\Extension;

// No direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\Component\Privacy\Administrator\Export\Domain;
use Joomla\Component\Privacy\Administrator\Plugin\PrivacyPlugin;
use Joomla\Component\Privacy\Administrator\Removal\Status;
use Joomla\Component\Privacy\Administrator\Table\RequestTable;
use Joomla\Database\ParameterType;
use Kunena\Forum\Plugin\Privacy\Kunena\Helper\KunenaHelper;

/**
 * Plug-in to prevent Registering with Disposable Email Addresses
 *
 * @since  6.1.0
 */
class Kunena extends PrivacyPlugin
{
    /**
     * Application object
     *
     * @var    CMSApplicationInterface
     * @since  6.1.0
     */
    protected $app;

    /**
     * Performs validation to determine if the data associated with a remove information request can be processed
     *
     * This event will not allow a super user account to be removed
     *
     * @param   RequestTable  $request  The request record being processed
     * @param   User          $user     The user account associated with this request if available
     *
     * @return  Status
     *
     * @since   6.1.0
     */
    public function onPrivacyCanRemoveData(RequestTable $request, User $user = null)
    {
        $status = new Status();

        if (!$user) {
            return $status;
        }

        if ($user->authorise('core.admin')) {
            $status->canRemove = false;
            $status->reason    = Text::_('PLG_PRIVACY_USER_ERROR_CANNOT_REMOVE_SUPER_USER');
        }

        return $status;
    }

    /**
     * Processes an export request for Kunena user data
     *
     * This event will collect data for the following Kunena tables:
     *
     * - #__kunena_announcement
     * - #__kunena_attachments
     * - #__kunena_logs
     * - #__kunena_messages > #__kunena_messages_text
     * - #__kunena_polls_users > #__kunena_polls
     * - #__kunena_private
     * - #__kunena_rate
     * - #__kunena_thankyou
     * - #__kunena_topics
     * - #__kunena_users
     * - #__kunena_user_banned
     *
     * @param   RequestTable  $request  The request record being processed
     * @param   User          $user     The user account associated with this request if available
     *
     * @return  \Joomla\Component\Privacy\Administrator\Export\Domain[]
     *
     * @since   6.1.0
     */
    public function onPrivacyExportRequest(RequestTable $request, User $user = null)
    {
        if (!$user) {
            return [];
        }

        $domains = [];
        $domains[] = $this->createKunenaUserDomain($user->id);
        $domains[] = $this->createKunenaAnnouncementDomain($user->id);
        $domains[] = $this->createKunenaAttachmentsDomain($user->id);
        $domains[] = $this->createKunenaLogsDomain($user->id);
        $domains[] = $this->createKunenaMessagesDomain($user->id);
        $domains[] = $this->createKunenaPollsDomain($user->id);
        $domains[] = $this->createKunenaPrivateDomain($user->id);
        $domains[] = $this->createKunenaRateDomain($user->id);
        $domains[] = $this->createKunenaThankyouDomain($user->id);
        $domains[] = $this->createKunenaTopicsDomain($user->id);
        $domains[] = $this->createKunenaUserbannedDomain($user->id);

        return $domains;
    }

    /**
     * Removes the data associated with a remove information request
     *
     * This event will pseudoanonymise the user data
     *
     * @param   RequestTable  $request  The request record being processed
     * @param   User          $user     The user account associated with this request if available
     *
     * @return  void
     *
     * @since   6.1.0
     */
    public function onPrivacyRemoveData(RequestTable $request, User $user = null)
    {
        // This plugin only processes data for registered user accounts
        if (!$user) {
            return;
        }

        $anonymizedname = 'User ID ' . $user->id;

        // Anomynize User name in messages
        KunenaHelper::anomynizeUserData('#__kunena_messages', 'userid', $user->id, 'name', $anonymizedname);
        KunenaHelper::anomynizeUserData('#__kunena_messages', 'userid', $user->id, 'ip', '');

        // Anomynize User name in topics
        KunenaHelper::anomynizeUserData('#__kunena_topics', 'first_post_userid', $user->id, 'first_post_guest_name', $anonymizedname);
        KunenaHelper::anomynizeUserData('#__kunena_topics', 'last_post_userid', $user->id, 'last_post_guest_name', $anonymizedname);

        // Delete Kunena User Profile
        KunenaHelper::deleteUserData('#__kunena_users', 'userid', $user->id);

        // Delete User Category / Topic subscriptions, User Topic / Message Read status
        KunenaHelper::deleteUserData('#__kunena_user_categories', 'user_id', $user->id);
        KunenaHelper::deleteUserData('#__kunena_user_topics', 'user_id', $user->id);
        KunenaHelper::deleteUserData('#__kunena_user_read', 'user_id', $user->id);

        // Delete miscelanious
        KunenaHelper::deleteUserData('#__kunena_logs', 'user_id', $user->id);
        KunenaHelper::deleteUserData('#__kunena_logs', 'target_user', $user->id);
        KunenaHelper::deleteUserData('#__kunena_rate', 'userid', $user->id);
        KunenaHelper::deleteUserData('#__kunena_thankyou', 'userid', $user->id);
        KunenaHelper::deleteUserData('#__kunena_thankyou', 'targetuserid', $user->id);
        KunenaHelper::deleteUserData('#__kunena_users_banned', 'userid', $user->id);
    }

    /**
     * Adds the Kunena Privacy Information to Joomla Privacy plugin.
     *
     * @return  array
     *
     * @since   6.1.0
     */
    public function onPrivacyCollectAdminCapabilities(): array
    {
        return [
            'Kunena Privacy' => [
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_EMAIL'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_IP_ADDRESS'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_USERPROFILE'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_POSTS'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_RATINGS'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_STATISTICS'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_COOKIES'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_LOGS'),
                Text::_('PLG_PRIVACY_KUNENA_CAPABILITY_SOCIAL'),
            ],
        ];
    }

    /**
     * Create the domain for the user data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaUserDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user', 'kunena_user_data');
        $redacted = [];
        $excluded = [];
        $item     = KunenaHelper::getUserItems('#__kunena_users', 'userid', $userid, true);
        $data     = KunenaHelper::processUserData($item, $excluded, $redacted);

        $domain->addItem($this->createItemFromArray($data, $userid));

        return $domain;
    }

    /**
     * Create the domain for the user announcement data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaAnnouncementDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_announcement', 'kunena_user_announcement_data');
        $redacted = [];
        $excluded = [];
        $items    = KunenaHelper::getUserItems('#__kunena_announcement', 'created_by', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user attachments data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaAttachmentsDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_attachments', 'kunena_user_attachments_data');
        $redacted = [];
        $excluded = [];
        $items    = KunenaHelper::getUserItems('#__kunena_attachments', 'userid', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user logs data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaLogsDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_logs', 'kunena_user_logs_data');
        $redacted = [];
        $excluded = [];
        $items    = KunenaHelper::getUserItems('#__kunena_logs', 'user_id', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user messages data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaMessagesDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_messages', 'kunena_user_messages_data');
        $db       = $this->db;
        $redacted = [];
        $excluded = [];

        $query = $db->getQuery(true)
            ->select(['m.*', 't.message'])
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->join('LEFT', $db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('t.mesid'))
            ->where($db->quoteName('m.userid') . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        $items = $db->setQuery($query)->loadAssocList();

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user polls data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaPollsDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_polls', 'kunena_user_polls_data');
        $db       = $this->db;
        $redacted = [];
        $excluded = [];

        $query = $db->getQuery(true)
            ->select(['u.*', 'p.title'])
            ->from($db->quoteName('#__kunena_polls_users', 'u'))
            ->join('LEFT', $db->quoteName('#__kunena_polls', 'p') . ' ON ' . $db->quoteName('u.pollid') . ' = ' . $db->quoteName('p.id'))
            ->where($db->quoteName('u.userid') . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        $items = $db->setQuery($query)->loadAssocList();

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user private data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaPrivateDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_private', 'kunena_user_private_data');
        $redacted = [];
        $excluded = ['params'];
        $items    = KunenaHelper::getUserItems('#__kunena_private', 'author_id', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user rate data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaRateDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_rate', 'kunena_user_rate_data');
        $redacted = [];
        $excluded = [];
        $items    = KunenaHelper::getUserItems('#__kunena_rate', 'userid', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user thankyou data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaThankyouDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_thankyou', 'kunena_user_thankyou_data');
        $redacted = [];
        $excluded = ['targetuserid'];
        $items    = KunenaHelper::getUserItems('#__kunena_thankyou', 'userid', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user topics data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaTopicsDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_topics', 'kunena_user_topics_data');
        $redacted = [];
        $excluded = ['first_post_message', 'last_post_message', 'last_post_userid', 'last_post_guest_name', 'params'];
        $items    = KunenaHelper::getUserItems('#__kunena_topics', 'first_post_userid', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }

    /**
     * Create the domain for the user banned data
     *
     * @param   int  $userid  The User ID
     *
     * @return  Domain
     *
     * @since   6.1.0
     */
    private function createKunenaUserbannedDomain(int $userid)
    {
        $domain   = $this->createDomain('kunena_user_banned', 'kunena_user_banned_data');
        $redacted = [];
        $excluded = ['created_by', 'reason_private', 'modified_by', 'modified_time', 'params'];
        $items    = KunenaHelper::getUserItems('#__kunena_users_banned', 'userid', $userid);

        foreach ($items as $item) {
            $data = KunenaHelper::processUserData($item, $excluded, $redacted);

            $domain->addItem($this->createItemFromArray($data, $data['id']));
        }

        return $domain;
    }
}
