<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\Archive\Archive;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;

/**
 * Kunena Backend Templates Controller
 *
 * @since   Kunena 2.0
 */
class TemplatesController extends KunenaController
{
    /**
     * @var     null|string
     * @since   Kunena 2.0
     */
    protected $baseurl = null;

    /**
     * @var     array
     * @since   Kunena 2.0
     */
    protected $locked = ['aurelia'];

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
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
    }

    /**
     * Install the new template
     *
     * @return bool
     *
     * @since   Kunena 2.0
     * @throws \Exception
     */
    public function install(): bool
    {
        $tmpKunena = KunenaPath::tmpdir() . '/kinstall/';
        $dest      = KPATH_SITE . '/template/';
        $file      = $this->app->input->files->get('install_package', null, 'raw');
        $result    = true;

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return false;
        }

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error'])) {
            $this->app->enqueueMessage(
                Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_MISSING', $this->escape($file['name'])),
                'error'
            );
            $result = false;
        } else {
            $success = File::upload($file ['tmp_name'], $dest . $file ['name'], false, true);

            if ($success) {
                try {
                    $archive = new Archive();
                    $archive->extract($dest . $file ['name'], $tmpKunena);
                } catch (Exception $e) {
                    $this->app->enqueueMessage(
                        Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_FAILED', $this->escape($file['name'])),
                        'error'
                    );
                    $result = false;
                }

                File::delete($dest . $file ['name']);
            }

            if ($result) {
                if (is_dir($tmpKunena)) {
                    $templates = KunenaTemplateHelper::parseXmlFiles($tmpKunena);

                    if (!empty($templates)) {
                        foreach ($templates as $template) {
                            // Never overwrite locked templates
                            if (\in_array($template->directory, $this->locked)) {
                                continue;
                            }

                            // Check that the template is compatible with the actual Kunena version
                            if (!KunenaTemplateHelper::templateIsKunenaCompatible($template->targetversion)) {
                                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_COMPATIBLE_WITH_KUNENA_INSTALLED_VERSION', $template->name, $template->version), 'error');
                                $result = false;
                            } else {
                                if (is_file($dest . $template->directory . '/config/params.ini')) {
                                    if (is_file($tmpKunena . $template->sourcedir . '/config/params.ini')) {
                                        File::delete($tmpKunena . $template->sourcedir . '/config/params.ini');
                                    }

                                    File::move($dest . $template->directory . '/config/params.ini', $tmpKunena . $template->sourcedir . 'config/params.ini');
                                }

                                if (is_dir($dest . $template->directory . '/assets/images')) {
                                    if (is_dir($tmpKunena . $template->sourcedir . '/assets/images')) {
                                        Folder::delete($tmpKunena . $template->sourcedir . '/assets/images');
                                    }

                                    Folder::move($dest . $template->directory . '/assets/images', $tmpKunena . $template->sourcedir . '/assets/images');
                                }

                                if (is_file($dest . $template->directory . '/assets/scss/custom.scss')) {
                                    File::move($dest . $template->directory . '/assets/scss/custom.scss', $tmpKunena . $template->sourcedir . '/assets/scss/custom.scss');
                                }

                                if (is_file($dest . $template->directory . '/assets/css/custom.css')) {
                                    File::move($dest . $template->directory . '/assets/css/custom.css', $tmpKunena . $template->sourcedir . '/assets/css/custom.css');
                                }

                                Folder::delete($dest . $template->directory);

                                $success = Folder::move($tmpKunena . $template->sourcedir, $dest . $template->directory);

                                if ($success !== true) {
                                    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_FAILED', $template->directory), 'error');
                                    $result = false;
                                } else {
                                    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_SUCCESS', $template->directory), 'success');
                                }
                            }
                        }

                        // Delete the tmp install directory
                        if (is_dir($tmpKunena)) {
                            Folder::delete($tmpKunena);
                        }

                        // Clear all cache, just in case.
                        KunenaCacheHelper::clearAll();
                    } else {
                        $this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_MISSING_FILE'), 'error');
                        $result = false;
                    }
                } else {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'), 'error');
                }
            }
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));

        return $result;
    }

    /**
     * Method to just redirect to main manager in case of use of cancel button
     *
     * @param   null  $key  key
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 3.0.5
     */
    public function cancel($key = null)
    {
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }
}
