<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Config;

\defined('_JEXEC') or die();

use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;

/**
 * Kunena Config class
 *
 * Propertylist generated via KunenaConfig::getIdeProperties()
 * 
 * @property string $XConsumerKey                          The X consumer key
 * @property string $XConsumerSecret                       The X consumer secret
 * @property string $accessComponent                       Direct Component Access
 * @property string $activeMenuItem                        Active menu class name
 * @property string $allowAvatarGallery                    Use Avatars Gallery
 * @property string $allowAvatarUpload                     Allow Avatar Upload
 * @property string $allowChangeSubject                    Let users edit the subject
 * @property string $allowEditPoll                         Allow Edit Poll
 * @property string $allowFavorites                        Allow Favorites
 * @property string $allowSubscriptions                    Allow Subscriptions
 * @property string $allowUserEditPoll                     Set if you want allow the user to edit the poll when someone has voted
 * @property string $articleDisplay                        Choose how to display article by default
 * @property string $askEmail                              Require E-mail
 * @property string $attachEnd                             Number of characters from end for shorten filename
 * @property string $attachStart                           Number of characters from start for shorten filename
 * @property string $attachmentLimit                       Limit Attachments
 * @property string $attachmentProtection                  Protect Attachments
 * @property string $attachmentUtf8                        Enable utf8 characters on attachments
 * @property string $autoEmbedEbay                         Auto Embed eBay Items
 * @property string $autoEmbedInstagram                    Auto Embed Instagram Items
 * @property string $autoEmbedSoundcloud                   Auto Embed Soundcloud items
 * @property string $autoEmbedYoutube                      Auto Embed YouTube Videos
 * @property string $autoLink                              Auto-Link URLs
 * @property string $avatarCrop                            Avatar Cropping
 * @property string $avatarEdit                            Edit avatar by clicking on avatar
 * @property string $avatarOnCategory                      Show Avatar on Category Index and Recent Topics
 * @property string $avatarQuality                         Avatar Quality (%)
 * @property string $avatarSize                            Max. Avatar File Size (KB)
 * @property string $avatarType                            Default avatar type
 * @property string $avatarTypes                           Allowed avatar types
 * @property string $bbcodeImgSecure                       Display Images With Non-standard File Extensions
 * @property string $blueskyappHandleOfApp                 BlueSky App ID
 * @property string $blueskyappPasswordOfApp               The BlueSky consumer secret
 * @property string $boardOffline                          Forum Offline
 * @property string $boardTitle                            Forum Title
 * @property string $boxGhostMessage                       Check Shadow Topic Box
 * @property string $cache                                 Caching
 * @property string $cacheTime                             Cache Time
 * @property string $captcha                               Display Captcha for :
 * @property string $captchaPostLimit                      CAPTCHA Challenge for Users
 * @property string $categorySubscriptions                 Category Subscriptions
 * @property string $checkMimeTypes                        Check MIME Types
 * @property string $datePickerFormat                      Date format for datepicker
 * @property string $debug                                 Enable Debug Mode
 * @property string $defaultAvatar                         Default avatar
 * @property string $defaultAvatarSmall                    Default small avatar
 * @property string $defaultSort                           Message Ordering
 * @property string $disableEmoticons                      Disable Emoticons
 * @property string $disableRe                             Disable Re: on subject
 * @property string $displayFilenameAttachment             Display attachment filename
 * @property string $ebayAffiliateId                       eBay Affiliate ID
 * @property string $ebayApiKey                            eBay AppID key
 * @property string $ebayCertId                            eBay Cert ID (Client Secret)
 * @property string $ebayLanguage                          eBay Widget Language Code
 * @property string $editMarkup                            Show Edited Mark Up
 * @property string $email                                 Forum E-mail Address
 * @property string $emailHeader                           Email Header Image
 * @property string $emailHeaderSizeX                      Header Width
 * @property string $emailHeaderSizeY                      Header Height
 * @property string $emailRecipientCount                   E-mail to Multiple Recipients
 * @property string $emailRecipientPrivacy                 E-mail Recipient Privacy
 * @property string $emailSenderName                       Sender name for mail
 * @property string $emailVisibleAddress                   Visible E-mail Recipient
 * @property string $enableForumJump                       Enable Category Jump
 * @property string $enableRss                             Enable RSS Feeds
 * @property string $enableThreadedLayouts                 Enable Threaded Layouts
 * @property string $fallbackEnglish                       Use English On Missing Strings
 * @property string $fileSize                              Maximum File Size (KB)
 * @property string $fileTypes                             Allowed File Types
 * @property string $fileUpload                            Allow File Uploads
 * @property string $floodProtection                       Flood Protection
 * @property string $googleMapApiKey                       Google maps API key to get better stats usage of google API
 * @property string $hideIp                                Hide IP Addresses From Moderators
 * @property string $highlightCode                         Enable Code Highlighting
 * @property string $historyLimit                          History Limit
 * @property string $holdGuestPosts                        Moderate Guests
 * @property string $holdNewUsersPosts                     Moderate New Users
 * @property string $homeId                                Home ID
 * @property string $imageHeight                           Maximum Image Height (px)
 * @property string $imageMimeTypes                        Legal MIME Types
 * @property string $imageQuality                          Image Quality for JPG Resizing (%)
 * @property string $imageSize                             Maximum Image File Size (KB)
 * @property string $imageTypes                            Allowed Image Types
 * @property string $imageUpload                           Allow Image Uploads
 * @property string $imageWidth                            Maximum Image Width (px)
 * @property string $indexId                               Index ID
 * @property string $ipTracking                            IP Address Tracking
 * @property array  $latestCategory                        Category List in Recent Topics
 * @property string $latestCategoryIn                      Category Filter in Recent Topics
 * @property string $lightbox                              Enable Lightbox for Images
 * @property string $listCatShowModerators                 Show Moderators in Category Index
 * @property string $logModeration                         Define if you want to log action or moderation
 * @property string $mailAdministrators                    E-mail Administrators
 * @property string $mailBodyUserBanned                    Set the body of the mail when the user is banned
 * @property string $mailBodyUserUnBanned                  Set the body of the mail when the user is unbanned
 * @property string $mailFull                              Include Post Contents
 * @property string $mailModerators                        E-mail Moderators
 * @property string $mainMenuId                            Mainmenu
 * @property string $maxLinks                              Max Links in message
 * @property string $maxPersonalText                       Max. Personal Text Length
 * @property string $maxSig                                Max. Signature Length
 * @property string $messagesPerPage                       Messages Per Page
 * @property string $messagesPerPageSearch                 Search Results
 * @property string $minimalUserPostsAddUrlImage           Set the number minimum of user posts to able to add url and image
 * @property string $miscId                                MISC ID
 * @property string $modSeeDeleted                         Show Deleted Messages
 * @property string $moderatorPermDelete                   Allow moderators to permdelete
 * @property string $moderatorsId                          Moderators ID
 * @property string $newUsersPreventPostUrlImages          Prevent that new users are able to add url and image in his messages or profile
 * @property string $offlineMessage                        Forum Offline Message
 * @property string $orderingSystem                        Reply Numbering Inside Topic
 * @property string $personal                              Personal
 * @property string $pickupCategory                        Force users to pickup a category
 * @property string $plainEmail                            Plain Text Emails
 * @property string $pollAllowVoteOne                      Single Vote Only
 * @property string $pollEnabled                           Enable Polls
 * @property string $pollNbOptions                         Maximum Number Poll Options
 * @property string $pollNbVotesByUser                     Maximum Number of Votes Per User
 * @property string $pollResultsUserslist                  Show Voters
 * @property string $pollTimeBtVotes                       Time Between Consecutive Votes
 * @property string $popPollsCount                         Number of Popular Polls
 * @property string $popSubjectCount                       Number of Popular Subjects
 * @property string $popThanksCount                        Number of Popular Thank Yous
 * @property string $popUserCount                          Number of Popular Users
 * @property string $postDateFormat                        Message Time Format
 * @property string $postDateFormatHover                   Hover Message Time Format
 * @property string $privateMessage                        Enable private message
 * @property string $profileId                             Profile ID
 * @property string $profiler                              Enable profiler
 * @property string $pubProfile                            Allow Guests to see User Profiles
 * @property string $pubWrite                              Allow Guests to Post/Write
 * @property string $quickReply                            Show Quick Reply
 * @property string $rankImages                            Show Rank
 * @property string $ratingEnabled                         Allow rating
 * @property string $readOnly                              Read Only Mode
 * @property string $regOnly                               Registered Users Only
 * @property string $reportMsg                             Message Reporting
 * @property string $rssAllowHtml                          Render HTML &amp; BBCode in RSS feeds
 * @property string $rssAuthorFormat                       Author Format
 * @property string $rssAuthorInTitle                      Render Author in Title
 * @property string $rssCache                              Caching Interval
 * @property array  $rssExcludedCategories                 Exclude Categories
 * @property string $rssFeedBurnerUrl                      The URL of the basic RSS feed into FeedBurner.
 * @property array  $rssIncludedCategories                 Include Categories
 * @property string $rssLimit                              RSS Limit
 * @property string $rssOldTitles                          Use Oldstyle Titles
 * @property string $rssSpecification                      RSS Specification
 * @property string $rssTimeLimit                          RSS History (timelimit)
 * @property string $rssType                               RSS Feed type
 * @property string $rssWordCount                          Limit Number of Characters Per Item
 * @property string $rules                                 Permissions
 * @property string $searchId                              Search ID
 * @property string $searchTime                            Search Time
 * @property string $sef                                   Search Engine Friendly URLs
 * @property string $sefRedirect                           SEF Redirect
 * @property string $sendEmails                            Send E-mails to Users
 * @property string $sendMailUserBanned                    Send a mail to the user when he is banned and unbanned
 * @property string $sessionTimeOut                        Session Lifetime
 * @property string $showAnnouncement                      Show Announcement
 * @property string $showBannedReason                      Show Banned Reason
 * @property string $showChildCatIcon                      Show Sub-Category Image
 * @property string $showEmail                             Show E-mail
 * @property string $showFileForGuest                      Show Attachments for Guests
 * @property string $showGenStats                          Show General Statistics
 * @property string $showHistory                           Show History
 * @property string $showImgFilesManageProfile             Display profile tab to manage attachments
 * @property string $showImgForGuest                       Show Images for Guests
 * @property string $showKarma                             Show Karma Indicator
 * @property string $showListTime                          Recent Topics Time Period
 * @property string $showNew                               Show New posts
 * @property string $showPopPollStats                      Show Popular Poll Statistics
 * @property string $showPopSubjectStats                   Show Popular Subject Statistics
 * @property string $showPopThankYouStats                  Show Popular Thank You Statistics
 * @property string $showPopUserStats                      Show Popular User Statistics
 * @property string $showRanking                           Ranking
 * @property string $showSessionStartTime                  Online Users Time Limit
 * @property string $showSessionType                       Online Users Contains
 * @property string $showStats                             Show Statistics
 * @property string $showThankYou                          Enable Thank You Feature
 * @property string $showUserStats                         Show User Statistics
 * @property string $showWhoIsOnline                       Show Who is Online
 * @property string $signature                             Signatures
 * @property string $smartLinking                          Smart Auto Linking
 * @property string $statsLinkAllowed                      Allow Guests to see Stats link
 * @property string $stopForumSpamKey                      API key
 * @property string $stopForumSpamNewUserCheck             Check New User
 * @property string $subscriptionsChecked                  Check Subscription Box
 * @property string $superAdminUserlist                    Hide Super Users in user list
 * @property string $teaser                                Hide Replies For Guests
 * @property string $template                              Template
 * @property string $thankYouMax                           Visible Thank You Limit
 * @property string $threadsPerPage                        Topics Per Page
 * @property string $thumbHeight                           Thumbnail Height (px)
 * @property string $thumbWidth                            Thumbnail Width (px)
 * @property string $timeToCreatePage                      Display Page Creation Time
 * @property string $topicIcons                            Selectable Topic Icons
 * @property string $topicLayout                           Default topic layout
 * @property string $topicListId                           Topiclist ID
 * @property string $topicSubscriptions                    Topic Subscriptions
 * @property string $trimLongUrls                          Trim Long URLs
 * @property string $trimLongUrlsBack                      Back Portion of Trimmed URLs
 * @property string $trimLongUrlsFront                     Front Portion of Trimmed URLs
 * @property string $urlSubjectTopic                       Prevent posting topic or reply with URL in title
 * @property string $useSystemEmails                       Treat as System Email
 * @property string $userDeleteMessage                     User Can Delete Own Post
 * @property string $userEdit                              User Edits
 * @property string $userEditTime                          User Edit Time
 * @property string $userEditTimeGrace                     User Edit Grace Time
 * @property string $userListUserType                      Show User Types
 * @property string $userReport                            User can report himself
 * @property string $userStatus                            Show User Status
 * @property string $userlistAllowed                       Allow Guests to see Userlist
 * @property string $userlistAvatar                        Display Avatar
 * @property string $userlistCountUsers                    User Count Contains
 * @property string $userlistEmail                         Show E-mail
 * @property string $userlistJoinDate                      Show Join Date
 * @property string $userlistKarma                         Show Karma
 * @property string $userlistLastVisitDate                 Show Last Visit Date
 * @property string $userlistOnline                        Online Status
 * @property string $userlistPosts                         Show Number of Posts
 * @property string $userlistRows                          Users Per Page
 * @property string $userlistUserHits                      Show Profile Hits
 * @property string $username                              Display User Name
 * @property string $utmSource                             UTM Source
 *    
 * @since 7.0.0
 */
class KunenaConfig
{
    /**
     * @var string
     */
    private const COMPONENT = 'com_kunena';

    /**
     * @var KunenaConfig|null
     */
    private static ?KunenaConfig $instance = \null;

    /**
     * @var array
     */
    private array $config = [];

    /**
     * Private constructor to prevent direct instantiation of this utility class.
     * This class is intended to be used statically.
     * 
     * @since  7.0.0
     */
    private function __construct()
    {
        // Prevent direct instantiation
    }

    /**
     * Public clone method to prevent cloning of this utility class.
     * Throw an exception to make it explicit that cloning is not allowed.
     * 
     * @since  7.0.0
     */
    public function __clone()
    {
        throw new \BadMethodCallException('Cloning of ' . __CLASS__ . ' is not allowed.');
    }

    /**
     * Public wakeup method to prevent unserialization of this utility class.
     * Throw an exception to make it explicit that unserialization is not allowed.
     * 
     * @since  7.0.0
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException('Unserialization of ' . __CLASS__ . ' is not allowed.');
    }

    /**
     * Magic method to get protected properties by key.
     * This is called when reading data from inaccessible properties.
     *
     * @param   string $key The key requested (e.g., 'name' for $class->name)
     * @return  mixed|null The value from the $param array or null if the key doesn't exist
     * @since   7.0.0
     */
    public function __get(string $key): mixed
    {
        // Check if the key exists in the protected array
        if (\array_key_exists($key, $this->config)) {

            // Ensure minum value
            if (\in_array($key, ['messagesPerPage', 'messagesPerPageSearch', 'threadsPerPage'])) {
                $this->config[$key]       = max($this->config[$key], 1);
            }

            if ($key === 'email') {
                $email = $this->email;

                return !empty($email) ? $email : Factory::getApplication()->get('mailfrom', '');
            }

            // Return the value
            return $this->config[$key];
        }

        throw new \Exception('Trying to get non-existing property: ' . $key, 500);
    }

    /**
     * Magic method to set inaccessible properties.
     * This is called when writing data to inaccessible properties.
     * It allows setting a configuration value only if the key already exists
     * in the loaded configuration array.
     *
     * @param   string $key The key requested (e.g., 'name' for $class->name)
     * @param   mixed $value The value to set
     * @return  void
     * @since   7.0.0
     */
    public function __set(string $key, mixed $value): void
    {
        // Only set the value if the key is already present in the config array
        if (\array_key_exists($key, $this->config)) {
            $this->config[$key] = $value;

            return;
        }

        throw new \Exception('Trying to set non-existing property: ' . $key, 500);
    }

    /**
     * Get an instance of KunenaConfig
     * 
     * @return  ?KunenaConfig
     * @since   7.0.0
     */
    public static function getInstance(): ?KunenaConfig
    {
        if (\null === self::$instance) {
            $instance = new KunenaConfig();
            $instance->config = self::getCachedConfig();
            self::$instance = $instance;
        }

        return self::$instance;
    }


    /**
     * Function to build the configuration values with default values from config.xml
     * 
     * @return  array
     * @since   7.0.0
     */
    private static function buildConfig(): array
    {
        $config   = [];
        $params   = ComponentHelper::getParams(self::COMPONENT);
        $defaults = self::getXmlDefaults(JPATH_ADMINISTRATOR . '/components/' . self::COMPONENT . '/config.xml');

        foreach ($defaults as $name => $default) {
            $config[$name] = $params->get($name, $default);
        }

        return $config;
    }

    /**
     * Function to get the cached (or create when not existing) configuration settings
     * 
     * @return  array
     * @since   7.0.0
     */
    private static function getCachedConfig(): array
    {
        /** @var CallbackController $cache */
        $cache  = self::getConfigCacheController();
        $config = $cache->get(
            fn() => self::buildConfig(),
            [],
            'config'
        );

        return $config;
    }

    /**
     * Function to store the configuration settings in to Joomla configured cache
     * 
     * @param   array  $config  The config object to store.
     *
     * @return  void
     * @since   7.0.0
     */
    private static function storeCachedConfig(array $config): void
    {
        /** @var CallbackController $cache */
        $cache = self::getConfigCacheController();
        $cache->store($config, 'config');
    }

    /**
     * Function to get the Configuration cache Controller
     * 
     * @return  CacheController
     * @since   7.0.0
     */
    private static function getConfigCacheController(): CacheController
    {
        $options = [
            'defaultgroup' => self::COMPONENT . '_configuration',
            'caching'      => \true,                                // Force caching
            'lifetime'     => 5259600                               // Config cache never expires
        ];

        /** @var CallbackController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('callback', $options);

        return $cache;
    }

    /**
     * get the XML file
     *
     * @param   string  $path  Path to the XML file
     *
     * @return  SimpleXMLElement|false
     * @since   7.0.0
     */
    private static function getXml(string $path): \SimpleXMLElement|false
    {
        if (!\is_file($path)) {
            return \false;
        }

        $xml = new \SimpleXMLElement(\file_get_contents($path));

        return $xml;
    }

    /**
     * Extract default values from config.xml using Joomla Form API
     *
     * @param   string  $path  Path to the XML file
     *
     * @return  array<string, mixed>
     * @since   7.0.0
     */
    private static function getXmlDefaults(string $path): array
    {
        $xml = self::getXml($path);

        if (!$xml) {
            return [];
        }

        $defaults = [];

        // Assuming typical Joomla form XML structure: <config><fields name="params"><fieldset><field ...>
        // Adjust XPath if your config.xml structure is different.
        foreach ($xml->xpath('//field') as $field) {
            $attributes = $field->attributes();
            $name       = (string) ($attributes['name'] ?? '');
            $type       = (string) ($attributes['type'] ?? '');
            $config     = (string) ($attributes['config'] ?? '');

            if (
                !empty($name)
                && !\str_starts_with($name, '@')
                && $type !== 'spacer'
                && $config !== 'false'
            ) {
                $defaults[$name] = (string) ($attributes['default'] ?? '');
            }
        }

        return $defaults;
    }

    /**
     * Print all properties on screen to create a docblock property list we can use in this class
     *
     * @param   string  $path  Path to the XML file
     *
     * @return  never
     * @since   7.0.0
     */
    public static function getIdeProperties(): never
    {
        $xml = self::getXml(JPATH_ADMINISTRATOR . '/components/' . self::COMPONENT . '/config.xml');

        if (!$xml) {
            die('- nothing to list -');
        }

        $properties = [];
        $params     = ComponentHelper::getParams(self::COMPONENT);

        // Assuming typical Joomla form XML structure: <config><fields name="params"><fieldset><field ...>
        // Adjust XPath if your config.xml structure is different.
        foreach ($xml->xpath('//field') as $field) {
            $attributes = $field->attributes();
            $name       = (string) ($attributes['name'] ?? '');
            $type       = (string) ($attributes['type'] ?? '');
            $config     = (string) ($attributes['config'] ?? '');

            if (
                !empty($name)
                && !\str_starts_with($name, '@')
                && $type !== 'spacer'
                && $config !== 'false'
            ) {
                $type              = \is_array($params->get($name, '')) ? 'array ' : 'string';
                $label             = Text::_((string) ($attributes['label'] ?? ''));
                $properties[$name] = ['label' => $label, 'type' => $type];
            }
        }

        $maxLength = 0;
        foreach ($properties as $property => $value) {
            $length = \strlen($property . $value['type']);
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        // Ensure the array is sorted by key
        \ksort($properties);

        // Step 2: Iterate and pad the property key to align the label
        foreach ($properties as $property => $value) {
            // Pad the property key with spaces on the right side
            $paddedProperty = \str_pad($property, $maxLength);
            $label          = $value['label'];
            $type           = $value['type'];
            // Output the string, aligning the label
            echo " * @property $type \$$paddedProperty    $label\r\n";
        }

        die;
    }

    /**
     * Function to save a key value in the component configuration
     *
     * @return  boolean
     * @since   7.0.0
     */
    public function save(): bool
    {
        $params  = ComponentHelper::getParams(self::COMPONENT);
        $changed = \false;

        foreach ($params as $key => $value) {
            if ($this->config[$key] === $value) {
                continue;
            }

            $params->set($key, $this->config[$key]);
            $changed = \true;
        }

        if ($changed) {
            /** @var DatabaseDriver $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $query = $db->createQuery()
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('params') . ' = ' . $db->quote(\json_encode($params)))
                ->where($db->quoteName('name') . ' = ' . $db->quote(self::COMPONENT));

            $db->setQuery($query);

            $result = $db->execute();

            // Clean out the Cache and start with new value(s)
            $this->reset();

            return $result;
        }

        return \true;
    }

    /**
     * Function to clear the local cached $config variable
     * 
     * @return  void
     * @since   7.0.0
     */
    public function reset(): void
    {
        $cache = self::getConfigCacheController();
        $cache->clean();

        if (self::$instance !== \null) {
            self::$instance = \null;
        }
    }
}
