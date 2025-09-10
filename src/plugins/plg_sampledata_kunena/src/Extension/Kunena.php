<?php

/**
 * @package        Joomla.Plugin
 * @subpackage     Sampledata.Kunena
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 */

namespace Kunena\Forum\Plugin\Sampledata\Kunena\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\Component\Menus\Administrator\Model\ItemModel;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaSampleData;

/**
 * Sampledata - Kunena Plugin
 *
 * @since  4.0.0
 */
final class Kunena extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * @var     string language path
     *
     * @since   4.0.0
     */
    protected $path = null;

    /**
     * @var   integer Admin Id, author of all generated content.
     *
     * @since   4.0.0
     */
    protected $adminId;

    /**
     * Holds the menuitem model
     *
     * @var     ItemModel
     *
     * @since   4.0.0
     */
    private $menuItemModel;


    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   6.5
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onSampledataGetOverview'    => 'onSampledataGetOverview',
            'onAjaxSampledataApplyStep1' => 'onAjaxSampledataApplyStep1',
        ];
    }

    /**
     * Get an overview of the proposed sampleData.
     *
     * @return  \stdClass|void  True on success.
     *
     * @since   4.0.0
     */
    public function onSampledataGetOverview()
    {
        $app = $this->getApplication();

        if (!$app->getIdentity()->authorise('core.manage', 'com_kunena')) {
            return;
        }

        if (KunenaForum::versionSampleData()) {
            return;
        }

        $data              = new \stdClass();
        $data->name        = $this->_name;
        $data->title       = Text::_('PLG_SAMPLEDATA_KUNENA_OVERVIEW_TITLE');
        $data->description = Text::_('PLG_SAMPLEDATA_KUNENA_OVERVIEW_DESC');
        $data->icon        = 'comments';
        $data->steps       = 1;

        return $data;
    }

    /**
     * First step to enable the Language filter plugin.
     *
     * @return  array|void  Will be converted into the JSON response to the module.
     *
     * @throws Exception
     * @since   4.0.0
     */
    public function onAjaxSampledataApplyStep1()
    {
        if (!Session::checkToken('get') || $app->getInput()->get('type') != $this->_name) {
            return;
        }

        if (!$this->enablePlugin('plg_system_kunena')) {
            $response            = [];
            $response['success'] = false;

            $response['message'] = Text::_('PLG_SYSTEM_KUNENA_NOT_ENABLED');

            return $response;
        }

        KunenaSampleData::installSampleData();

        $response            = [];
        $response['success'] = true;
        $response['message'] = Text::_('PLG_SAMPLEDATA_KUNENA_STEP1_SUCCESS');

        return $response;
    }

    /**
     * Enable a Joomla plugin.
     *
     * @param   string  $pluginName  The name of plugin.
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    private function enablePlugin(string $pluginName): bool
    {
        // Create a new db object.
        /** @var DatabaseDriver $db */
        $db    = $this->getDatabase();
        $query = $db->createQuery();

        $query
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('name') . ' = :pluginname')
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->bind(':pluginname', $pluginName);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            return false;
        }

        return true;
    }
}
