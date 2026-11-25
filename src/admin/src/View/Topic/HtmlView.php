<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Administrator\View\Topics\HtmlView as TopicsView;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer;
use Kunena\Forum\Libraries\Tools\KunenaTopicEditor;
use Kunena\Forum\Libraries\Tools\KunenaTopicManager;
use Throwable;

/**
 * One topic edited: its subject, category, lock and stickiness, and the subject and text of each message.
 *
 * @since   Kunena 8.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The texts of the buttons and dialogs of the editor (as the editor widget of the templates).
     *
     * @since   Kunena 8.0
     */
    public const EDITOR_TEXTS = [
        'COM_KUNENA_EDITOR_LINK_TARGET', 'COM_KUNENA_EDITOR_LINK_TARGET_SAME', 'COM_KUNENA_EDITOR_LINK_TARGET_NEW',
        'COM_KUNENA_EDITOR_CONFIDENTIAL', 'COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES', 'COM_KUNENA_EDITOR_EBAY', 'COM_KUNENA_EDITOR_EMOTICONS',
        'COM_KUNENA_EDITOR_HIDE', 'COM_KUNENA_EDITOR_INSTAGRAM', 'COM_KUNENA_EDITOR_MAP', 'COM_KUNENA_EDITOR_MAP_PLACE', 'COM_KUNENA_EDITOR_MAP_TYPE',
        'COM_KUNENA_EDITOR_MAP_ZOOM', 'COM_KUNENA_EDITOR_PASTE_IMAGES_NOT_ALLOWED', 'COM_KUNENA_EDITOR_POLL', 'COM_KUNENA_EDITOR_SIZE_1',
        'COM_KUNENA_EDITOR_SIZE_2', 'COM_KUNENA_EDITOR_SIZE_3', 'COM_KUNENA_EDITOR_SIZE_4', 'COM_KUNENA_EDITOR_SIZE_5', 'COM_KUNENA_EDITOR_SIZE_6',
        'COM_KUNENA_EDITOR_SOUNDCLOUD', 'COM_KUNENA_EDITOR_SPOILER', 'COM_KUNENA_EDITOR_TWEET', 'COM_KUNENA_EDITOR_VIDEO',
        'COM_KUNENA_EDITOR_VIDEO_HEIGHT', 'COM_KUNENA_EDITOR_VIDEO_ID', 'COM_KUNENA_EDITOR_VIDEO_OR_PROVIDER', 'COM_KUNENA_EDITOR_VIDEO_PROVIDER',
        'COM_KUNENA_EDITOR_VIDEO_SIZE', 'COM_KUNENA_EDITOR_VIDEO_URL', 'COM_KUNENA_EDITOR_VIDEO_WIDTH',
    ];

    /**
     * The topic and a page of its messages (KunenaTopicEditor::loadOnSite()).
     *
     * @since   Kunena 8.0
     */
    public ?array $data = null;

    /**
     * The categories, in the order of the tree.
     *
     * @since   Kunena 8.0
     */
    public array $categories = [];

    /**
     * The filters of the list to go back to.
     *
     * @since   Kunena 8.0
     */
    public array $back = [];

    /**
     * May the user save (core.admin, as the list)?
     *
     * @since   Kunena 8.0
     */
    public bool $canAdmin = false;

    /**
     * @param   string|null  $tpl  The template.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $input = Factory::getApplication()->getInput();

        $this->back       = KunenaTopicManager::active(KunenaTopicManager::filters((array) $input->get('filter', [], 'array')));
        $this->data       = KunenaTopicEditor::loadOnSite($input->getInt('id', 0), $input->getInt('page', 1));
        $this->categories = KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite());
        $this->canAdmin   = $this->getCurrentUser()->authorise('core.admin', 'com_kunena');

        if ($this->data === null) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_KUNENA_TOPIC_EDIT_NOT_FOUND'), 'warning');
            Factory::getApplication()->redirect(Route::_('index.php?option=com_kunena&view=topics', false));

            return;
        }

        // No other page of the administration while the form is open (the menu on the left).
        $input->set('hidemainmenu', true);

        $this->getDocument()->setTitle(Text::_('COM_KUNENA_TOPIC_EDIT') . ': ' . $this->data['topic']['subject']);
        $this->addToolbar();
        TopicsView::useScript($this);

        if ($this->canAdmin) {
            $this->useEditor();
        }

        parent::display($tpl);
    }

    /**
     * The editor of the site (CKEditor 5 with the BBCode of Kunena) on the text of each message: the same buttons, emoticons
     * and language as in the forum. The textarea keeps the BBCode which is saved; a text which is not changed in the editor
     * is saved as it was.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function useEditor(): void
    {
        $app      = Factory::getApplication();
        $document = $this->getDocument();
        $wa       = $document->getWebAssetManager();
        $base     = Uri::root() . 'media/kunena/core/js/ckeditor5/';

        // The texts of the buttons and dialogs are in the language files of the site.
        foreach (['com_kunena', 'com_kunena.templates'] as $file) {
            foreach ([JPATH_SITE . '/components/com_kunena', JPATH_SITE] as $folder) {
                $app->getLanguage()->load($file, $folder);
            }
        }

        // The language of the editor: the one of the administration, if the editor has it ("de", "pt-br").
        $tag      = $app->getLanguage()->getTag();
        $language = 'en';

        foreach ([strtolower($tag), strtolower(substr($tag, 0, 2))] as $candidate) {
            if ($candidate !== 'en' && is_file(JPATH_ROOT . '/media/kunena/core/js/ckeditor5/translations/' . $candidate . '.js')) {
                $language = $candidate;
                $wa->registerAndUseScript('kunena.editor.language', $base . 'translations/' . $candidate . '.js', ['version' => 'auto']);
                break;
            }
        }

        // The buttons which the template of the forum shows.
        try {
            $buttons = (string) KunenaTemplate::getInstance()->params->get('editorButtons', '');
        } catch (Throwable $e) {
            $buttons = '';
        }

        $wa->registerAndUseStyle('kunena.editor', $base . 'ckeditor5.css', ['version' => 'auto']);
        $wa->registerAndUseScript('kunena.editor', $base . 'kunena-editor.js', ['version' => 'auto'], ['defer' => true]);
        $wa->registerAndUseScript(
            'kunena_admin_topic_edit',
            Uri::base() . 'components/com_kunena/media/js/kunena-topic-edit.js',
            ['version' => 'auto'],
            ['defer' => true],
            ['kunena.editor']
        );

        $document->addScriptOptions('com_kunena.ckeditor_config', 'ckeditor5');
        $document->addScriptOptions('com_kunena.ckeditor_language', $language);
        $document->addScriptOptions('com_kunena.ckeditor_buttons_configuration', $buttons);
        $document->addScriptOptions('com_kunena.ckeditor_smiley_path', Uri::root(true) . '/media/kunena/emoticons/');
        $document->addScriptOptions('com_kunena.ckeditor_emoticons', json_encode(self::emoticons()));

        foreach (self::EDITOR_TEXTS as $key) {
            Text::script($key);
        }
    }

    /**
     * The emoticons of the bar of the editor: code => file. KunenaParser::getEmoticons() reads the size of each image from a
     * path of the site, which the administration (in its own folder) does not find; the editor needs only the files.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    private static function emoticons(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['code', 'location']))
            ->from($db->quoteName('#__kunena_smileys'))
            ->where($db->quoteName('emoticonbar') . ' = 1')
            ->order([$db->quoteName('ordering') . ' ASC', $db->quoteName('id') . ' ASC']);

        try {
            return array_map('strval', (array) $db->setQuery($query)->loadAssocList('code', 'location'));
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TOPIC_EDIT'), 'pencil-alt');

        if ($this->canAdmin) {
            ToolbarHelper::apply('topic.apply');
            ToolbarHelper::save('topic.save');
        }

        ToolbarHelper::cancel('topic.cancel', 'JTOOLBAR_CLOSE');
        ToolbarHelper::help('COM_KUNENA', false, 'https://docs.kunena.org/en/');
    }
}
