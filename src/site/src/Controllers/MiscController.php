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
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

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

    /**
     * A tweet of a message, read from X after the page (tweet-load.js): the HTML of the tweet as JSON ({"html": "..."}). Only for the
     * key which the page gave with the id (KunenaBBCodeLibrary::tweetKey()), so that nobody makes the forum ask X for other tweets.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function tweet(): void
    {
        $id  = $this->input->getString('id', '');
        $key = $this->input->getString('key', '');

        // KunenaBBCodeLibrary is in the file of KunenaBBCode.
        class_exists(KunenaBBCode::class);

        if (!ctype_digit($id) || \strlen($id) > 18 || !hash_equals(KunenaBBCodeLibrary::tweetKey($id), $key)) {
            throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 403);
        }

        if (KunenaFactory::getTemplate()->isHmvc() && !KunenaFactory::getTemplate()->params->get('X_Social')) {
            throw new RuntimeException(Text::_('COM_KUNENA_NO_ACCESS'), 404);
        }

        $html = (new KunenaBBCodeLibrary())->renderTweet((int) $id);

        header('Content-type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        while (@ob_end_clean()) {
        }

        echo json_encode(['html' => \is_string($html) ? $html : '']);

        jexit();
    }
}
