<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserSocials;

/**
 * Socials Model for Kunena: the providers (social websites) of the profile are kept in the parameters of the component.
 *
 * @since   Kunena 8.0
 */
class SocialsModel extends AdminModel
{
    /**
     * @param   array    $data      data
     * @param   boolean  $loadData  load data
     *
     * @return  Form|false
     *
     * @since   Kunena 8.0
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_kunena.socials', 'socials', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * The data of the form: the providers of the admin, or the default providers to start from.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function loadFormData()
    {
        // The names of the default providers are strings of the site.
        KunenaFactory::loadLanguage('com_kunena.templates', 'site');

        $rows = KunenaUserSocials::getCustomProviderRows();

        // A name which is a language key (the default providers) is shown as the text of the key.
        foreach ($rows as $index => $row) {
            $rows[$index]['title'] = Text::_((string) ($row['title'] ?? ''));
        }

        if (!$rows) {
            foreach (KunenaUserSocials::getProviders() as $key => $provider) {
                $rows[] = [
                    'key'   => $key,
                    'title' => Text::_($provider->title),
                    'url'   => $provider->url,
                    'fa'    => $provider->fa,
                    'nourl' => (int) $provider->nourl,
                ];
            }
        }

        return ['socials' => $rows];
    }

    /**
     * Save the providers in the parameters of the component.
     *
     * @param   array  $data  The data of the form.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public function save($data)
    {
        // A title which is the text of a default title is saved as its language key again: it stays translated.
        KunenaFactory::loadLanguage('com_kunena.templates', 'site');
        $titles = [];

        foreach (json_decode(KunenaUserSocials::getDefaultSocialsJson(), true) as $key => $default) {
            $titles[$key] = ['text' => Text::_($default['title']), 'key' => $default['title']];
        }

        $rows = [];
        $used = [];

        foreach ((array) ($data['socials'] ?? []) as $row) {
            $row   = (array) $row;
            $key   = strtolower(trim((string) ($row['key'] ?? '')));
            $title = trim(strip_tags((string) ($row['title'] ?? '')));

            if (!preg_match('/^[a-z0-9_]{1,30}$/', $key) || $key === 'userid') {
                $this->setError(Text::sprintf('COM_KUNENA_A_SOCIAL_KEY_INVALID', $key));

                return false;
            }

            if (isset($used[$key])) {
                $this->setError(Text::sprintf('COM_KUNENA_A_SOCIAL_KEY_TWICE', $key));

                return false;
            }

            $used[$key] = true;
            $nourl      = !empty($row['nourl']) ? 1 : 0;
            $url        = trim((string) ($row['url'] ?? ''));

            // The address of the profile is a link: http, https or the program of a messenger (never javascript:), with the place
            // for the value of the user.
            if (!$nourl && (!preg_match('#^(https?://|skype:|tg:|mailto:|tel:)#i', $url) || strpos($url, '##VALUE##') === false)) {
                $this->setError(Text::sprintf('COM_KUNENA_A_SOCIAL_URL_INVALID', $key));

                return false;
            }

            if (isset($titles[$key]) && $title === $titles[$key]['text']) {
                $title = $titles[$key]['key'];
            }

            $rows['socials' . \count($rows)] = [
                'key'   => $key,
                'title' => $title !== '' ? $title : $key,
                'url'   => $nourl ? '##VALUE##' : $url,
                'fa'    => preg_replace('/[^a-z0-9 -]/i', '', (string) ($row['fa'] ?? '')),
                'nourl' => $nourl,
            ];
        }

        $params = ComponentHelper::getParams('com_kunena');
        $params->set('socials', $rows);
        $json = $params->toString();

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = :params')
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->bind(':params', $json);

        try {
            $db->setQuery($query)->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // The users have the values of the old list in their profile cache.
        KunenaCacheHelper::clear();

        return true;
    }
}
