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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;

/**
 * Kunena Misc Controller
 *
 * @since   Kunena 2.0
 */
class MiscController extends KunenaController
{
    /**
     * @param   array  $config  config
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function template()
    {
        // The choice of a template is kept in a cookie for the visitor, so a link from another site must not set it.
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $name = $this->input->getString(
        	'name',
        	$this->input->cookie->getString('kunena_template', '')
        );

        $options = [
            'expires'  => 0,
            'path'     => Uri::root(true) . '/',
            'domain'   => '',
            'secure'   => Uri::getInstance()->isSsl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        if ($name) {
            // Only the name of an installed template (letters, digits, _ and -): the name becomes part of file paths.
            if (!KunenaTemplateHelper::isInstalled($name)) {
                $name = 'aurelia';
            }

            setcookie('kunena_template', $name, $options);
        } else {
            $options['expires'] = time() - 3600;

            setcookie('kunena_template', '', $options);
        }

        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
    }
}
