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

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

/**
 * Component Controller
 *
 * @since   Kunena 6.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     *
     * @since  Kunena 6.0
     */
    protected $default_view = 'cpanel';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see
     *                               {@link \JFilterInput::clean()}.
     *
     * @return  BaseController
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function display($cachable = false, $urlparams = []): BaseController
    {
        $document = $this->app->getDocument();
        $view     = Factory::getApplication()->getInput()->getCmd('view', '');

        // The views of Kunena in the menu on the left get an icon: the manifest of a component can not give icons to the
        // items below the main one.
        $document->getWebAssetManager()->addInlineScript(
            <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
    var icons = {
        cpanel: 'gauge-high', attachments: 'paperclip', badges: 'award', categories: 'sitemap', config: 'gear',
        smilies: 'face-smile', stats: 'chart-column', logs: 'clipboard-list', mailsqueues: 'envelope', plugins: 'plug',
        ranks: 'ranking-star', socials: 'share-nodes', templates: 'palette', tools: 'wrench', trashs: 'trash', users: 'users'
    };

    document.querySelectorAll('#sidebarmenu a.no-dropdown').forEach(function (link) {
        var href = link.getAttribute('href') || '';
        var found = href.match(/[?&]view=(\w+)/);
        var key = href.indexOf('component=com_kunena') > -1 ? 'config' : (href.indexOf('option=com_kunena') > -1 && found ? found[1] : '');

        if (!icons[key] || link.querySelector('.kunena-menu-icon')) {
            return;
        }

        var icon = document.createElement('span');
        icon.className = 'kunena-menu-icon fa-solid fa-fw fa-' + icons[key];
        icon.setAttribute('aria-hidden', 'true');
        icon.style.marginRight = '0.5rem';
        link.insertBefore(icon, link.firstChild);
    });
});
JS
        );

        if ($view !== 'ranks') {
            $wa = $document->getWebAssetManager();
            $wa->registerStyle('kunena_theme', Uri::base() . '/components/com_kunena/media/css/theme.min.css', [], [], []);
            $wa->useStyle('kunena_theme');

            // The theme sets a dark text color, which cannot be read in the dark administrator template: follow the template.
            $wa->addInlineStyle('main #kunena { color: inherit; }', [], [], ['kunena_theme']);
            // Lists (multiple or size > 1) have no arrow image, so the template paints them in its arrow color: use the input background.
            $wa->addInlineStyle('main #kunena .form-select[multiple], main #kunena .form-select[size]:not([size="1"]) { background: var(--form-control-bg, #fff); }', [], [], ['kunena_theme']);
        }

        return parent::display($cachable, $urlparams);
    }
}
