<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Throwable;

/**
 * Why do the notifications not arrive? (tool "Test email"): the settings of Joomla and of Kunena which the e-mails depend
 * on, the queue, and a test e-mail sent with the sender of the forum, with the error of the mailer when it fails.
 *
 * @since   Kunena 8.0
 */
class KunenaMailTest
{
    /**
     * The problems of the settings: language keys of warnings.
     *
     * @param   array  $settings  The settings (settings()).
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function problems(array $settings): array
    {
        $problems = [];

        if (!$settings['mailonline']) {
            $problems[] = 'COM_KUNENA_TOOLS_MAILTEST_PROBLEM_OFFLINE';
        }

        if (!$settings['sendEmails']) {
            $problems[] = 'COM_KUNENA_TOOLS_MAILTEST_PROBLEM_KUNENA_OFF';
        }

        if ($settings['sender'] === '' || !MailHelper::isEmailAddress($settings['sender'])) {
            $problems[] = 'COM_KUNENA_TOOLS_MAILTEST_PROBLEM_SENDER';
        }

        if ($settings['mailer'] === 'smtp' && $settings['smtphost'] === '') {
            $problems[] = 'COM_KUNENA_TOOLS_MAILTEST_PROBLEM_SMTP';
        }

        if ($settings['queue'] > 0 && !$settings['queueTask']) {
            $problems[] = 'COM_KUNENA_TOOLS_MAILTEST_PROBLEM_QUEUE';
        }

        return $problems;
    }

    /**
     * The settings of this site.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function settings(): array
    {
        $app    = Factory::getApplication();
        $config = KunenaConfig::getInstance();
        $queue  = 0;

        try {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName('#__kunena_notifications_mailsqueue'))
                ->where($db->quoteName('send') . ' = 0');
            $queue = (int) $db->setQuery($query)->loadResult();
        } catch (Throwable $e) {
            // Without the queue, nothing waits.
        }

        $tasks = KunenaScheduledTasks::listOnSite();

        return [
            'mailonline' => (bool) $app->get('mailonline', 1),
            'mailer'     => (string) $app->get('mailer', 'mail'),
            'smtphost'   => (string) $app->get('smtphost', ''),
            'from'       => (string) $app->get('mailfrom', ''),
            'fromName'   => (string) $app->get('fromname', ''),
            'sender'     => (string) $config->email,
            'sendEmails' => (bool) $config->sendEmails,
            'templates'  => (bool) $config->useMailTemplates,
            'queue'      => $queue,
            'queueTask'  => \in_array($tasks['add.mailsqueue']['state'] ?? 'missing', ['ok', 'never', 'late'], true),
        ];
    }

    /**
     * Sends a test e-mail with the sender of the forum.
     *
     * @param   string  $to  The address.
     *
     * @return  array{ok: bool, error: string}
     *
     * @since   Kunena 8.0
     */
    public static function send(string $to): array
    {
        if (!MailHelper::isEmailAddress($to)) {
            return ['ok' => false, 'error' => Text::_('COM_KUNENA_TOOLS_MAILTEST_ERROR_ADDRESS')];
        }

        $config = KunenaConfig::getInstance();

        try {
            $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            $mail->setSender([$config->email, (string) $config->boardTitle]);
            $mail->addRecipient($to);
            $mail->setSubject(Text::sprintf('COM_KUNENA_TOOLS_MAILTEST_SUBJECT', (string) $config->boardTitle));
            $mail->setBody(Text::sprintf('COM_KUNENA_TOOLS_MAILTEST_BODY', (string) $config->boardTitle, Factory::getDate()->toSql()));
            $mail->isHtml(false);

            $ok = (bool) $mail->send();
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }

        return ['ok' => $ok, 'error' => $ok ? '' : Text::_('COM_KUNENA_TOOLS_MAILTEST_ERROR_UNKNOWN')];
    }
}
