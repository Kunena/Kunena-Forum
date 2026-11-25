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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\LanguageFactoryInterface;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Email\KunenaMailTemplate;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Throwable;

/**
 * What the e-mails of Kunena say (tool "E-mail templates"): each template which Kunena sends, in each language of the
 * site, filled with sample data, and sent to the administrator on demand. Test email checks that mail works; this
 * checks the texts (which an administrator can change in Joomla: System, Mail Templates).
 *
 * @since   Kunena 8.0
 */
class KunenaMailPreview
{
    /**
     * The templates which Kunena sends: id => language key of their name.
     *
     * @since   Kunena 8.0
     */
    public const TEMPLATES = [
        'com_kunena.subscription'  => 'COM_KUNENA_TOOLS_MAILS_SUBSCRIPTION',
        'com_kunena.moderator'     => 'COM_KUNENA_TOOLS_MAILS_MODERATOR',
        'com_kunena.mention'       => 'COM_KUNENA_TOOLS_MAILS_MENTION',
        'com_kunena.reportmessage' => 'COM_KUNENA_TOOLS_MAILS_REPORT',
    ];

    /**
     * The sample values of the tags (the others get "[name]").
     *
     * @param   string  $sitename    The name of the site.
     * @param   string  $boardtitle  The title of the forum.
     * @param   string  $base        The address of the site.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function samples(string $sitename, string $boardtitle, string $base): array
    {
        return [
            'sitename'    => $sitename,
            'boardtitle'  => $boardtitle,
            'heading'     => 'A new reply in a topic you follow',
            'subject'     => 'Re: Welcome to the forum',
            'topic'       => 'Welcome to the forum',
            'category'    => 'General discussion',
            'author'      => 'jane.doe',
            'poster'      => 'jane.doe',
            'sender'      => 'john.smith',
            'title'       => 'Re: Welcome to the forum',
            'reason'      => 'Spam',
            'report'      => 'This message advertises a shop.',
            'message'     => "Thank you for the welcome!\nI am new here and glad to join.",
            'more'        => '',
            'footer'      => 'You get this e-mail because you follow this topic.',
            'url'         => rtrim($base, '/') . '/index.php?option=com_kunena&view=topic&catid=1&id=1',
            'unsubscribe' => rtrim($base, '/') . '/index.php?option=com_kunena&view=topics&layout=user&mode=subscriptions',
        ];
    }

    /**
     * The tags which a notification fills with HTML of its own; the others are text, which Joomla escapes in the HTML.
     *
     * @since   Kunena 8.0
     */
    public const HTML_TAGS = ['message', 'more', 'footer', 'unsubscribe'];

    /**
     * The values of a mail as a notification of Kunena has them (KunenaMessage::getMailTemplateData()): the text values, and
     * the HTML ones, in which the message, the footer and the link to unsubscribe are HTML.
     *
     * @param   array   $samples      The samples (samples()), the address to unsubscribe in "unsubscribe".
     * @param   string  $description  The text before the link to unsubscribe.
     * @param   string  $label        The text of the link to unsubscribe.
     * @param   string  $quote        The title of the message (COM_KUNENA_MESSAGE).
     *
     * @return  array{html: array, plain: array, unsafe: string[]}  unsafe: the tags which Joomla escapes in the HTML.
     *
     * @since   Kunena 8.0
     */
    public static function mailValues(array $samples, string $description, string $label, string $quote): array
    {
        $esc = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $url = (string) ($samples['unsubscribe'] ?? '');

        $plain                = $samples;
        $plain['unsubscribe'] = KunenaMessage::unsubscribePlain($url, $description);

        $html                = $samples;
        $html['message']     = (string) ($samples['message'] ?? '') === '' ? ''
            : '<p style="margin:18px 0 4px;color:#888888">' . $esc($quote) . ':</p><div style="border-left:3px solid #dddddd;padding:4px 14px;margin:0 0 6px">'
              . nl2br($esc((string) $samples['message']), false) . '</div>';
        $html['more']        = (string) ($samples['more'] ?? '') === '' ? '' : '<p style="font-size:14px">' . $esc((string) $samples['more']) . '</p>';
        $html['footer']      = $esc((string) ($samples['footer'] ?? ''));
        $html['unsubscribe'] = KunenaMessage::unsubscribeHtml($url, $description, $label);

        return ['html' => $html, 'plain' => $plain, 'unsafe' => array_values(array_diff(array_keys($samples), self::HTML_TAGS))];
    }

    /**
     * Replaces the tags {NAME} of a text with the values; an unknown tag shows its name.
     *
     * @param   string  $text    The text.
     * @param   array   $values  name => value.
     * @param   array   $tags    The tags of the template.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function fill(string $text, array $values, array $tags): string
    {
        foreach (array_unique(array_merge(array_keys($values), $tags)) as $tag) {
            $text = str_ireplace('{' . strtoupper((string) $tag) . '}', (string) ($values[strtolower((string) $tag)] ?? '[' . $tag . ']'), $text);
        }

        return $text;
    }

    /**
     * The languages of the site: tag => name.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function languagesOnSite(): array
    {
        $languages = [];

        foreach (LanguageHelper::getKnownLanguages(JPATH_SITE) as $tag => $language) {
            $languages[(string) $tag] = (string) ($language['name'] ?? $tag);
        }

        ksort($languages);

        return $languages;
    }

    /**
     * A template of this site in a language, filled with the samples.
     *
     * @param   string  $id        The template (a key of TEMPLATES).
     * @param   string  $language  The language tag.
     *
     * @return  array{subject: string, body: string, html: string, tags: string[], customized: bool}|null  Null when unknown.
     *
     * @since   Kunena 8.0
     */
    public static function renderOnSite(string $id, string $language): ?array
    {
        if (!isset(self::TEMPLATES[$id])) {
            return null;
        }

        try {
            $template = MailTemplate::getTemplate($id, $language);
        } catch (Throwable $e) {
            $template = null;
        }

        if (!$template) {
            return null;
        }

        $lang   = self::language($language);
        $params = json_decode((string) ($template->params ?? ''), true);
        $tags   = \is_array($params) && \is_array($params['tags'] ?? null) ? array_map('strval', $params['tags']) : [];
        $values = self::valuesOnSite($lang);

        // In the HTML, the text values escaped as Joomla does when it sends the mail.
        $html = $values['html'];

        foreach ($values['unsafe'] as $key) {
            $html[$key] = htmlspecialchars((string) $html[$key], ENT_QUOTES, 'UTF-8');
        }

        return [
            'subject' => self::fill($lang->_((string) $template->subject), $values['plain'], $tags),
            'body'    => self::fill($lang->_((string) $template->body), $values['plain'], $tags),
            'html'    => self::fill($lang->_((string) ($template->htmlbody ?? '')), $html, $tags),
            'tags'    => $tags,
            // A template changed in Joomla (System, Mail Templates) has its own row for the language.
            'customized' => (string) ($template->language ?? '') !== '',
        ];
    }

    /**
     * The tags {NAME} of a text which the template does not know: they would be sent as they are.
     *
     * @param   string    $text  The text.
     * @param   string[]  $tags  The tags of the template.
     *
     * @return  string[]  The unknown tags, upper case, each once.
     *
     * @since   Kunena 8.0
     */
    public static function unknownTags(string $text, array $tags): array
    {
        $known = array_map('strtoupper', $tags);

        preg_match_all('/\{([A-Z0-9_]+)\}/i', $text, $matches);

        return array_values(array_unique(array_filter(
            array_map('strtoupper', $matches[1]),
            static fn (string $tag) => !\in_array($tag, $known, true)
        )));
    }

    /**
     * Does Joomla send the HTML body of the templates (System, Mail Templates, Options: mail format HTML or both)?
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function htmlOnSite(): bool
    {
        return ComponentHelper::getParams('com_mails')->get('mail_style', 'plaintext') !== 'plaintext';
    }

    /**
     * A template of this site to edit in a language: its own texts when it was changed for the language, else the texts of
     * Kunena in the language (not filled in).
     *
     * @param   string  $id        The template.
     * @param   string  $language  The language tag.
     *
     * @return  array{subject: string, body: string, htmlbody: string, tags: string[], customized: bool}|null  Null when unknown.
     *
     * @since   Kunena 8.0
     */
    public static function editableOnSite(string $id, string $language): ?array
    {
        if (!isset(self::TEMPLATES[$id])) {
            return null;
        }

        $rows   = self::rows($id, $language);
        $master = $rows[''] ?? null;

        if ($master === null) {
            return null;
        }

        $params = json_decode((string) ($master['params'] ?? ''), true);
        $tags   = \is_array($params) && \is_array($params['tags'] ?? null) ? array_map('strval', $params['tags']) : [];

        if (isset($rows[$language])) {
            $row = $rows[$language];

            return ['subject' => (string) $row['subject'], 'body' => (string) $row['body'], 'htmlbody' => (string) $row['htmlbody'], 'tags' => $tags, 'customized' => true];
        }

        $lang = self::language($language);

        return [
            'subject'    => $lang->_((string) $master['subject']),
            'body'       => $lang->_((string) $master['body']),
            'htmlbody'   => (string) $master['htmlbody'] !== '' ? $lang->_((string) $master['htmlbody']) : '',
            'tags'       => $tags,
            'customized' => false,
        ];
    }

    /**
     * Saves the texts of a template for a language of this site, as Joomla does (System, Mail Templates): a row of the template
     * for the language, which Joomla sends instead of the texts of Kunena.
     *
     * @param   string  $id        The template.
     * @param   string  $language  The language tag.
     * @param   array   $texts     subject, body, htmlbody.
     *
     * @return  string  Empty when saved, else the language key of the error.
     *
     * @since   Kunena 8.0
     */
    public static function saveOnSite(string $id, string $language, array $texts): string
    {
        $subject  = trim((string) preg_replace('/[\r\n]+/', ' ', (string) ($texts['subject'] ?? '')));
        $body     = str_replace(["\r\n", "\r"], "\n", (string) ($texts['body'] ?? ''));
        $htmlbody = (string) ($texts['htmlbody'] ?? '');

        if (!isset(self::TEMPLATES[$id]) || !isset(self::languagesOnSite()[$language])) {
            return 'COM_KUNENA_TOOLS_MAILS_ERROR_TEMPLATE';
        }

        if ($subject === '' || trim($body) === '') {
            return 'COM_KUNENA_TOOLS_MAILS_ERROR_EMPTY';
        }

        $rows   = self::rows($id, $language);
        $master = $rows[''] ?? null;

        if ($master === null) {
            return 'COM_KUNENA_TOOLS_MAILS_ERROR_TEMPLATE';
        }

        // Without the HTML of Joomla, the HTML body is kept as it was.
        if (!self::htmlOnSite()) {
            $htmlbody = (string) ($rows[$language]['htmlbody'] ?? '');
        }

        $db  = Factory::getContainer()->get('DatabaseDriver');
        $row = (object) [
            'template_id' => $id,
            'extension'   => (string) $master['extension'],
            'language'    => $language,
            'subject'     => $subject,
            'body'        => $body,
            'htmlbody'    => $htmlbody,
            'attachments' => (string) ($rows[$language]['attachments'] ?? ''),
            'params'      => (string) ($rows[$language]['params'] ?? $master['params']),
        ];

        try {
            if (isset($rows[$language])) {
                $db->updateObject('#__mail_templates', $row, ['template_id', 'language']);
            } else {
                $db->insertObject('#__mail_templates', $row);
            }
        } catch (Throwable $e) {
            return 'COM_KUNENA_TOOLS_MAILS_ERROR_SAVE';
        }

        return '';
    }

    /**
     * The texts of Kunena again for a template in a language: its own row for the language is deleted.
     *
     * @param   string  $id        The template.
     * @param   string  $language  The language tag.
     *
     * @return  boolean  A row was deleted.
     *
     * @since   Kunena 8.0
     */
    public static function resetOnSite(string $id, string $language): bool
    {
        if (!isset(self::TEMPLATES[$id]) || $language === '') {
            return false;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->delete($db->quoteName('#__mail_templates'))
            ->where($db->quoteName('template_id') . ' = :id')
            ->where($db->quoteName('language') . ' = :language')
            ->bind(':id', $id)
            ->bind(':language', $language);
        $db->setQuery($query)->execute();

        return $db->getAffectedRows() > 0;
    }

    /**
     * The values of the samples of this site, as a notification has them (mailValues()), in a language: the link to
     * unsubscribe is a real one, for the current user and the first topic, which opens the page that asks to confirm.
     *
     * @param   Language  $lang  The language of the mail.
     *
     * @return  array{html: array, plain: array, unsafe: string[]}
     *
     * @since   Kunena 8.0
     */
    private static function valuesOnSite(Language $lang): array
    {
        $app     = Factory::getApplication();
        $samples = self::samples((string) $app->get('sitename'), (string) KunenaConfig::getInstance()->boardTitle, Uri::root());

        $samples['unsubscribe'] = KunenaEmailUnsubscribe::getUrl(KunenaEmailUnsubscribe::TOPIC, 1, (int) $app->getIdentity()?->id);

        return self::mailValues($samples, $lang->_('COM_KUNENA_EMAIL_UNSUBSCRIBE_DESC'), $lang->_('COM_KUNENA_EMAIL_UNSUBSCRIBE'), $lang->_('COM_KUNENA_MESSAGE'));
    }

    /**
     * The rows of a template: the one of Kunena (language '') and the one of the language, by language.
     *
     * @param   string  $id        The template.
     * @param   string  $language  The language tag.
     *
     * @return  array<string, array>
     *
     * @since   Kunena 8.0
     */
    private static function rows(string $id, string $language): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $empty = '';
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__mail_templates'))
            ->where($db->quoteName('template_id') . ' = :id')
            ->whereIn($db->quoteName('language'), [$empty, $language], ParameterType::STRING)
            ->bind(':id', $id);

        return (array) $db->setQuery($query)->loadAssocList('language');
    }

    /**
     * The language with the texts of the mails of Kunena: English first, the language over it (a text which is not
     * translated yet shows in English, not as its key). The texts are in the language file of the site of Kunena, in the
     * language folder of the site or in the folder of the component.
     *
     * @param   string  $language  The language tag.
     *
     * @return  Language
     *
     * @since   Kunena 8.0
     */
    private static function language(string $language): Language
    {
        $lang = Factory::getContainer()->get(LanguageFactoryInterface::class)->createLanguage($language);

        foreach (array_unique(['en-GB', $language]) as $tag) {
            foreach ([JPATH_SITE . '/components/com_kunena', JPATH_SITE] as $base) {
                $lang->load('com_kunena', $base, $tag, true, false);
            }
        }

        return $lang;
    }

    /**
     * Sends a template with the samples to an address, as Kunena sends it.
     *
     * @param   string  $id        The template.
     * @param   string  $language  The language tag.
     * @param   string  $to        The address.
     *
     * @return  array{ok: bool, error: string}
     *
     * @since   Kunena 8.0
     */
    public static function sendOnSite(string $id, string $language, string $to): array
    {
        if (!isset(self::TEMPLATES[$id]) || !MailHelper::isEmailAddress($to)) {
            return ['ok' => false, 'error' => 'COM_KUNENA_TOOLS_MAILTEST_ERROR_ADDRESS'];
        }

        $config = KunenaConfig::getInstance();
        $values = self::valuesOnSite(self::language($language));

        try {
            $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            $mail->setSender([$config->email, (string) $config->boardTitle]);
            $ok = KunenaMailTemplate::send($id, $language, $mail, $values['html'], $values['plain'], $values['unsafe'], $to);
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }

        return ['ok' => $ok, 'error' => $ok ? '' : 'COM_KUNENA_TOOLS_MAILTEST_ERROR_UNKNOWN'];
    }
}
