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

namespace Kunena\Forum\Site\Controller\Application\Attachment\Initial;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Class ComponentKunenaControllerApplicationAttachmentDefaultDisplay
 *
 * Only implemented on raw format as it's faster to run.
 *
 * @since   Kunena 4.0
 */
class AttachmentDisplay extends KunenaControllerDisplay
{
    /**
     * @var KunenaUser|null
     * @since Kunena 4.0
     */
    private ?KunenaUser $me = null;

    /**
     * Return true if layout exists.
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function exists()
    {
        return true;
    }

    /**
     * Display attachment.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  KunenaExceptionAuthorise
     * @throws  RuntimeException
     */
    public function execute()
    {
        try {
            $this->display();
        } catch (Exception $e) {
            // In case of an error we want to set HTTP error code.
            // We want to wrap the exception to be able to display correct HTTP status code.
            $error = new KunenaExceptionAuthorise($e->getMessage(), $e->getCode(), $e);
            header('HTTP/1.1 ' . $error->getResponseStatus(), true);

            echo $error->getResponseStatus();
        }

        flush();
        $this->app->close();
    }

    /**
     * Display attachment.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function display()
    {
        KunenaFactory::loadLanguage('com_kunena');

        $format   = $this->input->getWord('format', 'html');
        $id       = $this->input->getInt('id', 0);
        $thumb    = $this->input->getBool('thumb', false);
        $download = $this->input->getBool('download', false);

        // Run before executing action.
        $this->before();

        if ($format != 'raw' || !$id) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
        }

        if ($this->config->boardOffline && !$this->me->isAdmin()) {
            // Forum is offline.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_FORUM_IS_OFFLINE'), 503);
        }

        if ($this->config->regOnly && !$this->me->exists()) {
            // Forum is for registered users only.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LOGIN_NOTIFICATION'), 403);
        }

        $attachment = KunenaAttachmentHelper::get($id);
        $attachment->tryAuthorise();

        $path = $attachment->getPath($thumb);

        if ($thumb && !$path) {
            $path = $attachment->getPath(false);
        }

        if (!$path) {
            // File doesn't exist.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
        }

        if (headers_sent()) {
            throw new KunenaExceptionAuthorise('HTTP headers were already sent. Sending attachment failed.', 500);
        }

        // Close all output buffers, just in case.
        while (@ob_end_clean()) {
        }

        // Handle 304 Not Modified
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);

            if ($etag == $attachment->hash) {
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);

                // Give fast response.
                flush();
                $this->app->close();
            }
        }

        // Set file headers.
        header('ETag: ' . $attachment->hash);
        header('Pragma: public');
        header('X-Content-Type-Options: nosniff');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');

        // If guests can access an inline picture or PDF, we allow it to be cached for an hour.
        $guestMayRead = !$download && ($attachment->isImage() || $attachment->isPdf())
            && $attachment->isAuthorised('read', new KunenaUser());

        foreach (self::fileHeaders($attachment, $download, $guestMayRead) as $line) {
            header($line);
        }

        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . fileSize($path));
        flush();

        // Output the file contents.
        @readfile($path);
        flush();

        $this->app->close();
    }

    /**
     * Prepare attachment display.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function before()
    {
        // Load language files.
        KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

        $this->me       = KunenaUserHelper::getMyself();
        $this->config   = KunenaConfig::getInstance();
        $this->document = $this->app->getDocument();
    }

    /**
     * The headers which depend on the kind of answer: an inline picture, an inline PDF or a download.
     *
     * @param   KunenaAttachment  $attachment    The attachment.
     * @param   bool              $download      True when the file is asked as a download.
     * @param   bool              $guestMayRead  True when a guest may read the file: an inline file is then cached for an hour.
     *
     * @return  string[]  The header lines.
     *
     * @since   Kunena 8.0
     */
    protected static function fileHeaders(KunenaAttachment $attachment, bool $download, bool $guestMayRead): array
    {
        // A file opened by itself runs no script and reaches no other address (a picture with a script, an SVG): it is shown,
        // and a picture in a page is not changed by it.
        $sandbox = "Content-Security-Policy: default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox";
        $name    = self::dispositionName($attachment->getFilename(false));

        $pdf = !$download && $attachment->isPdf();

        if (!$pdf && ($download || !$attachment->isImage())) {
            // Otherwise force file download.
            return [
                $sandbox,
                'Expires: 0',
                'Cache-Control: must-revalidate, post-check=0, pre-check=0',
                'Content-Description: File Transfer',
                'Content-Type: application/force-download',
                'Content-Type: application/octet-stream',
                'Content-Type: application/download',
                'Content-Disposition: attachment; ' . $name,
            ];
        }

        if ($guestMayRead) {
            $maxage  = 60 * 60;
            $headers = [
                'Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxage) . ' GMT',
                'Cache-Control: public, max-age=' . $maxage,
            ];
        }
        // No guest access -- force re-validate.
        else {
            $headers = [
                'Expires: 0',
                'Cache-Control: must-revalidate, post-check=0, pre-check=0',
            ];
        }

        if ($pdf) {
            // The viewer of PDF files of Chromium does not open in a sandbox, and it is an object which default-src 'none'
            // blocks. The fixed type and nosniff let only the viewer of the browser show the file; no other site frames it.
            $headers[] = "Content-Security-Policy: frame-ancestors 'self'";
            $headers[] = 'Content-Type: application/pdf';
        } else {
            $headers[] = $sandbox;
            $headers[] = 'Content-type: ' . $attachment->filetype;
        }

        $headers[] = 'Content-Disposition: inline; ' . $name;

        return $headers;
    }

    /**
     * The name of the file in the header Content-Disposition. The name of the upload can have any character: a quote in it
     * ended the name, and the rest became a second name (filename*), which the browsers take before the first one.
     *
     * @param   string  $name  The name of the file.
     *
     * @return  string  filename="..." in ASCII and filename*=UTF-8''... with the whole name.
     *
     * @since   Kunena 8.0
     */
    protected static function dispositionName(string $name): string
    {
        $ascii = preg_replace('/[^\x20-\x7e]|["\\\\;]/', '_', $name);

        return 'filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($name);
    }
}
