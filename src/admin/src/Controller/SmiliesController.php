<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\KunenaUploadHelper;
use RuntimeException;

/**
 * Kunena Smileys Controller
 *
 * @since   Kunena 2.0
 */
class SmiliesController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 6.0
     */
    protected mixed $baseurl = null;

    /**
     * Construct
     *
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=smilies';
    }

    /**
     * Add
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function add(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $this->setRedirect(Route::_('index.php?option=com_kunena&view=smiley&layout=add', false));
    }

    /**
     * Edit
     *
     * @param   null  $key     key
     * @param   null  $urlVar  url var
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function edit($key = null, $urlVar = null): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $id = array_shift($cid);

        if (!$id) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_SMILEYS_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $this->setRedirect(Route::_("index.php?option=com_kunena&view=smiley&layout=edit&id={$id}", false));
    }

    /**
     * Save
     *
     * @param   null  $key     key
     * @param   null  $urlVar  url var
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function save($key = null, $urlVar = null): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $smileyCode        = $this->input->getString('smileyCode');
        $smileyLocation    = basename((string) $this->input->getString('smiley_url'));
        $smileyEmoticonBar = $this->input->getInt('smileyEmoticonBar', 0);
        $smileyId          = $this->input->getInt('smileyId', 0);

        if (empty($smileyCode)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_SMILEY_SMILEY_CODE_CANNOT_BE_EMPTY'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if (!$smileyId) {
            try {
                // A new smiley comes last: MySQL does not allow a subquery on the same table in the insert
                $query = $db->createQuery()
                    ->select('MAX(' . $db->quoteName('ordering') . ')')
                    ->from($db->quoteName('#__kunena_smileys'));

                $ordering = (int) $db->setQuery($query)->loadResult() + 1;

                $query = $db->createQuery()
                    ->insert("{$db->quoteName('#__kunena_smileys')}")
                    ->set("code={$db->quote($smileyCode)}")
                    ->set("location={$db->quote($smileyLocation)}")
                    ->set("emoticonbar={$db->quote($smileyEmoticonBar)}")
                    ->set("ordering={$ordering}");

                $db->setQuery($query);
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }
        } else {
            $query = $db->createQuery()
                ->update("{$db->quoteName('#__kunena_smileys')}")
                ->set("code={$db->quote($smileyCode)}")
                ->set("location={$db->quote($smileyLocation)}")
                ->set("emoticonbar={$db->quote($smileyEmoticonBar)}")
                ->where("id={$db->quote($smileyId)}");

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_SMILEY_SAVED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Smiley upload
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function smileyUpload(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $file = $this->input->files->get('Filedata');

        // A smiley is a picture: the folder of the emoticons is served by the site, and an svg or an html file with a script in
        // it would run in the browser of everyone who opens it.
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

        if (
            !\in_array($extension, ['gif', 'jpg', 'jpeg', 'png', 'webp'], true)
            || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || @getimagesize($file['tmp_name']) === false
        ) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_UNABLE'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        // TODO : change this part to use other method than \Kunena\Forum\Libraries\Upload\KunenaUploadHelper::upload()
        $upload = KunenaUploadHelper::upload($file, JPATH_ROOT . '/' . KunenaFactory::getTemplate()->getSmileyPath(), 'html');

        if ($upload) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_EMOTICONS_UPLOAD_SUCCESS'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_UNABLE'), 'error');
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Remove
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function remove(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $cids = implode(',', $cid);

        if ($cids) {
            $query = $db->createQuery()
                ->delete()->from("{$db->quoteName('#__kunena_smileys')}")
                ->where("id IN ($cids)");

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_SMILEY_DELETED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Save the order of the smilies which the list sends over AJAX after a drag
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function saveorderajax(): void
    {
        if (!Session::checkToken('post') || !$this->app->getIdentity()->authorise('core.manage', 'com_kunena')) {
            $this->app->close();
        }

        // Get the arrays from the Request
        $pks   = ArrayHelper::toInteger($this->input->post->get('cid', [], 'array'));
        $order = ArrayHelper::toInteger($this->input->post->get('order', [], 'array'));

        $posted = [];

        foreach ($pks as $i => $pk) {
            if ($pk > 0 && isset($order[$i])) {
                $posted[$pk] = $order[$i];
            }
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        try {
            $query = $db->createQuery()
                ->select([$db->quoteName('id'), $db->quoteName('ordering')])
                ->from($db->quoteName('#__kunena_smileys'));

            $current = array_map('intval', $db->setQuery($query)->loadAssocList('id', 'ordering'));

            foreach (static::orderPositions($current, $posted) as $id => $position) {
                if ($current[$id] === $position) {
                    continue;
                }

                $query = $db->createQuery()
                    ->update($db->quoteName('#__kunena_smileys'))
                    ->set($db->quoteName('ordering') . ' = :ordering')
                    ->where($db->quoteName('id') . ' = :id')
                    ->bind(':ordering', $position, ParameterType::INTEGER)
                    ->bind(':id', $id, ParameterType::INTEGER);

                $db->setQuery($query)->execute();
            }
        } catch (RuntimeException $e) {
            $this->app->close();
        }

        echo '1';

        // Close the application
        $this->app->close();
    }

    /**
     * The positions of all the smilies after a drag: the posted orderings replace the stored ones of the smilies which exist,
     * then every smiley is numbered from 1 by (ordering, id), so that the gaps of a deletion and the duplicates go away.
     *
     * @param   array  $current  The stored orderings, [id => ordering].
     * @param   array  $posted   The posted orderings, [id => ordering].
     *
     * @return  array  [id => position]
     *
     * @since   Kunena 8.0
     */
    protected static function orderPositions(array $current, array $posted): array
    {
        $orderings = $current;

        foreach ($posted as $id => $ordering) {
            if (\array_key_exists($id, $orderings)) {
                $orderings[$id] = (int) $ordering;
            }
        }

        if (!$orderings) {
            return [];
        }

        $ids = array_keys($orderings);

        usort($ids, static fn ($a, $b) => [$orderings[$a], $a] <=> [$orderings[$b], $b]);

        return array_combine($ids, range(1, \count($ids)));
    }

    /**
     * Method to just redirect to main manager in case of use of cancel button
     *
     * @param   null  $key  key
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    public function cancel($key = null): void
    {
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }
}
