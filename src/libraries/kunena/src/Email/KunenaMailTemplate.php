<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Email
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Log\KunenaLog;

/**
 * The e-mails of the forum which are made from the mail templates of Joomla (Users, Mail Templates): the subject and the body
 * are edited there, the forum gives the values of the tags (like {TOPIC} and {URL}).
 *
 * @since   Kunena 7.2
 */
abstract class KunenaMailTemplate
{
    /**
     * @var     boolean[]
     * @since   Kunena 7.2
     */
    protected static array $found = [];

    /**
     * Is the mail made from the mail template of Joomla: the setting of the forum is on, and the template is in the database
     * (when it is not, the layouts of the template of the forum are used)?
     *
     * @param   string  $templateId  The id of the mail template, like com_kunena.mention.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isUsed(string $templateId): bool
    {
        if (!KunenaConfig::getInstance()->useMailTemplates) {
            return false;
        }

        if (!isset(self::$found[$templateId])) {
            try {
                $db    = Factory::getContainer()->get(DatabaseInterface::class);
                $query = $db->createQuery()
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__mail_templates'))
                    ->where($db->quoteName('template_id') . ' = ' . $db->quote($templateId));
                $db->setQuery($query);

                self::$found[$templateId] = (int) $db->loadResult() > 0;
            } catch (Exception $e) {
                // Joomla without the table of the mail templates: the layouts are used.
                self::$found[$templateId] = false;
            }
        }

        return self::$found[$templateId];
    }

    /**
     * Send a mail which is made from a mail template of Joomla to one address.
     *
     * @param   string  $templateId  The id of the mail template.
     * @param   string  $language    The language of the receiver (the language of the site when it is empty).
     * @param   Mail    $mail        The mail: the sender, the headers and the reply address are set already.
     * @param   array   $html        The values of the tags for the html body and the subject.
     * @param   array   $plain       The values of the tags for the plain text body (the values of $html when it is empty).
     * @param   array   $unsafe      The tags with text of a user: they are escaped in the html body.
     * @param   string  $to          The address.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function send(string $templateId, string $language, Mail $mail, array $html, array $plain, array $unsafe, string $to): bool
    {
        if (isset(KunenaEmail::$mailer_error_status)) {
            // Mailer is broken, so prevent any sending.
            Log::add(KunenaEmail::$mailer_error_status->getMessage(), Log::ERROR, 'kunena');

            return false;
        }

        if ($language === '') {
            $language = Factory::getApplication()->getLanguage()->getTag();
        }

        $success = false;

        try {
            $mail->clearAllRecipients();

            $template = new MailTemplate(
                $templateId,
                $language,
                $mail,
                Factory::getContainer()->get(LanguageFactoryInterface::class),
                Factory::getContainer()->get(DatabaseInterface::class)
            );
            $template->addTemplateData($html);
            $template->addTemplateData($plain ?: $html, true);
            $template->addUnsafeTags($unsafe);
            $template->addRecipient($to);

            $success = (bool) $template->send();
        } catch (Exception $e) {
            Log::add($e->getMessage(), Log::ERROR, 'kunena');
        }

        KunenaLog::log(
            KunenaLog::TYPE_ACTION,
            $success ? KunenaLog::LOG_EMAIL_NOTIFICATION_SENT : KunenaLog::LOG_EMAIL_NOTIFICATION_NOTSENT,
            $success ? ['mail_subject' => $mail->Subject, 'emailsent' => 1] : ['mail_subject' => $mail->Subject, 'totalEmailsAttempted' => 1]
        );

        return $success;
    }
}
