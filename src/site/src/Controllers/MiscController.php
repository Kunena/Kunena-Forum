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
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;

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
     */
    public function template()
    {
        // The choice of a template is kept in a cookie of the visitor, so a link or a form of another site must not set it.
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));

            return;
        }

        // Only for the server: no script of the page needs the cookie.
        $options = [
            'path'     => Uri::root(true) . '/',
            'secure'   => Uri::getInstance()->isSsl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ];

        $name = $this->input->getString(
            'name',
            $this->input->cookie->getString('kunena_template', '')
        );

        if ($name) {
            $name = KunenaPath::clean($name);

            // The name of a folder of the templates, never a path.
            // An unknown name gives the template of the configuration, as KunenaTemplate::getInstance() does (Agora by default).
            if (preg_match('/^[\w\-]+$/', $name) !== 1 || !is_readable(KPATH_SITE . "/template/{$name}/config/template.xml")) {
                $name = (string) KunenaFactory::getConfig()->template;
            }

            // The name of an existing folder of the templates ([\w-] only), checked above (psalm.xml, TaintedCookie).
            setcookie('kunena_template', $name, ['expires' => 0] + $options);
        } else {
            setcookie('kunena_template', '', ['expires' => time() - 3600] + $options);
        }

        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
    }
}
