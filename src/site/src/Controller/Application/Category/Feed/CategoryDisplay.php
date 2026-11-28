<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Category\Feed;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Document\Feed\FeedImage;
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\CategoryModel;

/**
 * CategoryDisplay
 *
 * @since   Kunena 6.0
 */
class CategoryDisplay extends KunenaControllerDisplay
{
    /**
     * @var     mixed
     * @since   Kunena 6.0
     */
    public mixed $category = null;

    /**
     * @var     mixed
     * @since   Kunena 6.0
     */
    public mixed $topics = null;

    /**
     * Return true if layout exists.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function exists()
    {
        return KunenaFactory::getTemplate()->isHmvc();
    }

    /**
     * Prepare the content of the feed.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function before()
    {
        $this->config = KunenaFactory::getConfig();
        $this->checkForumIsOpen();
        $catid          = $this->input->getInt('catid', 0);
        $model          = new CategoryModel();
        $this->document = $this->app->getDocument();

        if (!$this->config->enableRss) {
            throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 404);
        }

        KunenaParser::$relative = false;

        $this->category = KunenaCategoryHelper::get($catid);

        try {
            $this->category->tryAuthorise('read');
        } catch (Exception $e) {
            throw new KunenaException($e->getMessage(), 404);
        }

        $this->topics = $model->getTopics();

        // Load all the authors with one query instead of one by one.
        KunenaUserHelper::loadUsers(array_map(static fn ($topic) => \is_object($topic) ? $topic->last_post_userid : 0, (array) $this->topics));

        foreach ($this->topics as $topic) {
            if ($this->config->rssType == 'topic') {
                $description = Text::sprintf('COM_KUNENA_RSS_TOPICS_CONTAINS_MESSAGES', $topic->posts) . ' - ' . Text::sprintf('COM_KUNENA_RSS_LAST_AUTHOR', KunenaFactory::getUser($topic->last_post_userid)->getName($topic->last_post_guest_name));
            } elseif (!KunenaUserHelper::getMyself()->userid && $topic->getCategory()->hidesRepliesForGuests() && $topic->last_post_id != $topic->first_post_id) {
                // The replies are hidden from the guests (like the feed of the topics).
                $description = Text::_('COM_KUNENA_TEASER_TEXT');
            } else {
                $description = $topic->last_post_message;
            }

            $date        = new Date($topic->last_post_time);
            $userid      = $topic->last_post_userid;

            $username    = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name, false);

            $title    = $topic->subject;
            $category = $topic->getCategory();
            $url      = $topic->getUrl($category, true, 'last');

            $this->createItem($title, $url, $description, $this->category->name, $date, $userid, $username);
        }

        // The title, the description, the image and the link of the feed: prepareDocument() runs for the controller of a page only,
        // which the feed is not, so the feed had the title of the menu item ("Home"), no description and the host as its link. The
        // feed is not rendered through a view, which would load the language file of the title.
        KunenaFactory::loadLanguage('com_kunena.views');
        $this->prepareDocument();
    }

    /**
     * Prepare document.
     *
     * @return void
     *
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $title = Text::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->category->name;
        $this->setTitle($title);

        $metaDesc = KunenaParser::parseText($this->category->name . ' - ' . $this->config->boardTitle);
        $current  = trim((string) $this->document->getDescription());
        $this->setDescription($current !== '' ? $current . '. ' . $metaDesc : $metaDesc);

        // The page of the category (the renderer of Joomla puts the host in front; without a link the feed had the host alone).
        $this->document->link = $this->category->getUrl(null, false);

        // Create image for feed
        $image                 = new FeedImage();
        $image->title          = $this->document->getTitle();
        // Addresses with the host: the renderer writes them as they are, a feed reader has no page to read a path against. The image
        // of the feed is the one of Kunena (the image of the templates, icons/rss.png, is not there).
        $image->url            = Uri::root() . 'media/kunena/core/images/icons/rss.png';
        $image->link           = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->category->getUrl(null, false);
        $image->description    = $this->document->getDescription();
        $this->document->image = $image;
    }

    /**
     * Create the item for the feed
     *
     * @param   string   $title        title
     * @param   string   $url          url
     * @param   string   $description  description
     * @param   string   $category     category
     * @param   integer  $date         date
     * @param   integer  $userid       userid
     * @param   string   $username     username
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function createItem($title, $url, $description, $category, $date, $userid, $username)
    {
        // $username is text (the feed escapes the title and the author; the HTML name showed "Tom &amp; Jerry").
        if ($this->config->rssAuthorInTitle) {
            // We want author in item titles
            $title .= ' - ' . Text::_('COM_KUNENA_RSS_BY') . ': ' . $username;
        }

        if ((int) $this->config->rssWordCount === -1) {
            $description = '';
        } else {
            // The text of [private] is shown to nobody: without HTML the feed gives the stored text.
            $description = KunenaParser::removeBlocks((string) $description, ['confidential', 'hide', 'private']);
            $description = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/s', '', $description);
            $description = preg_replace('/\[code\](.*?)\[\/code]/s', '', $description);

            if ((bool) $this->config->rssAllowHtml) {
                $description = KunenaParser::parseBBCode($description, null, (int) $this->config->rssWordCount);

                // Joomla puts the base URL (with the folder of the site) in front of the root-relative URLs of the feed
                if (\is_string($description) && $description !== '') {
                    $description = KunenaParser::absoluteUrls($description, Uri::getInstance()->toString(['scheme', 'host', 'port']));
                }
            } else {
                $description = KunenaParser::parseText($description, (int) $this->config->rssWordCount);
            }
        }

        // Assign values to feed item
        $item              = new FeedItem();
        $item->title       = $title;
        $item->link        = $url;
        $item->description = $description;
        $item->date        = $date->toSql();
        $item->author      = $username;

        // The address only when the reader may see it (as on the profile: the choice of the member, the option "Show e-mail
        // addresses"): the feed gave the address of every author to every reader.
        if ($this->config->rssAuthorFormat != 'name' && $userid && KunenaUserHelper::getMyself()->canDisplayEmail(KunenaUserHelper::get($userid))) {
            $item->authorEmail = (string) KunenaUserHelper::get((int) $userid)->email;
        }

        $item->category = $category;

        // Finally, add item to feed
        $this->document->addItem($item);
    }
}
