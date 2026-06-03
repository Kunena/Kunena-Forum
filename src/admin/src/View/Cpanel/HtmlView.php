<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Cpanel;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Log\KunenaLogFinder;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * About view for Kunena cpanel
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @param   null  $tpl  tmpl
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function display($tpl = null)
    {
        $this->addToolbar();

        $lang = Factory::getApplication()->getLanguage();
        $lang->load('mod_sampledata', JPATH_ADMINISTRATOR);

        if (!KunenaForum::versionSampleData()) {
            Factory::getApplication()->getDocument()->getWebAssetManager()
                ->registerAndUseScript('mod_sampledata', 'mod_sampledata/sampledata-process.js', [], ['defer' => true], ['core']);

            Text::script('MOD_SAMPLEDATA_COMPLETED');
            Text::script('MOD_SAMPLEDATA_CONFIRM_START');
            Text::script('MOD_SAMPLEDATA_INVALID_RESPONSE');
            Text::script('MOD_SAMPLEDATA_ITEM_ALREADY_PROCESSED');

            Factory::getApplication()->getDocument()->addScriptOptions(
                'sample-data',
                [
                    'icon' => Uri::root(true) . '/media/system/images/ajax-loader.gif',
                ]
            );
        }

        $this->KunenaMenusExists = KunenaMenuHelper::KunenaMenusExists();

        $logFinder = new KunenaLogFinder();
        $this->numberOfLogs = $logFinder->count();

        $count = KunenaStatistics::getInstance()->loadCategoryCount();
        $this->categoriesCount = $count['sections'] . ' / ' . $count['categories'];

        $lastid = KunenaUserHelper::getLastId();
        $user                     = KunenaUser::getInstance($lastid)->registerDate;
        $this->lastUserRegisteredDate = KunenaDate::getInstance($user)->toKunena('ago');

        // Get the number of messages in trashbin
        $messageFinder = new KunenaMessageFinder;
        $messageFinder->filterByHold([2, 3]);
        $messagesTrashedCount = $messageFinder->count();

        // Get the number of topics in trashbin
        $topicFinder = new KunenaTopicFinder;
        $topicFinder->filterByHold([2, 3]);
        $topicTrashedCount = $topicFinder->count();

        $this->messagesTopicsInTrashBin = $messagesTrashedCount + $topicTrashedCount;

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::spacer();
        ToolbarHelper::divider();
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_DASHBOARD'), 'dashboard');

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolBarHelper::preferences('com_kunena');
        }

        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * Method to get the Kunena language pack installed
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function getLanguagePack()
    {
        $lang = false;
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('package'))
            ->andwhere($db->quoteName('name') . ' = ' . $db->quote('Kunena Language Pack'));
        $db->setQuery($query);
        $list = (array) $db->loadObjectList();

        if ($list) {
            $lang = true;
        }

        return $lang;
    }
}
