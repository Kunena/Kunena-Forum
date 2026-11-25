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
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use ReflectionClassConstant;
use Throwable;

/**
 * The settings of the forum and of the site which make it easy to abuse (tool "Security audit"): file types which run as a
 * script, upload folders without their protection, guests who post without a captcha, e-mail addresses on the profiles,
 * accounts without an e-mail check, the debug mode, http. Each check says what is wrong and where to change it.
 *
 * @since   Kunena 8.0
 */
class KunenaSecurityAudit
{
    /**
     * The upload folders of Kunena, which have an .htaccess against scripts.
     *
     * @since   Kunena 8.0
     */
    public const FOLDERS = ['media/kunena/attachments', 'media/kunena/avatars/users', 'media/kunena/inbox'];

    /**
     * The checks of the settings.
     *
     * @param   array  $config  The settings of Kunena: fileTypes, imageTypes, avatarTypes, pubWrite, captcha, imageUpload,
     *                          fileUpload, showEmail, checkMimeTypes.
     * @param   array  $env     The site: forbidden (extensions which Kunena refuses), protected (folder => has .htaccess),
     *                          apache (bool), debug (bool), errorReporting, registration (bool), activation (int),
     *                          https (bool), forceSsl (int).
     *
     * @return  array<int, array{0: string, 1: string, 2: string, 3: string[], 4: string}>  [key, status, value, details, where: kunena, com_users, com_config].
     *
     * @since   Kunena 8.0
     */
    public static function checks(array $config, array $env): array
    {
        $checks = [];

        // File types which run as a script or as a page: Kunena refuses them at the upload, but they should not be listed.
        $listed    = [];
        $forbidden = array_map('strtolower', (array) ($env['forbidden'] ?? []));

        foreach (['fileTypes', 'imageTypes', 'avatarTypes'] as $setting) {
            foreach (explode(',', strtolower((string) ($config[$setting] ?? ''))) as $type) {
                $type = trim($type, " .\t");

                if ($type !== '' && \in_array($type, $forbidden, true)) {
                    $listed[$type] = $type;
                }
            }
        }

        $checks[] = ['types', $listed ? KunenaUpdateCheck::WARNING : KunenaUpdateCheck::OK, implode(', ', $listed), array_values($listed), 'kunena'];

        // The .htaccess of the upload folders: on Apache it keeps a script which got there from running.
        $missing = array_keys(array_filter((array) ($env['protected'] ?? []), static fn ($has) => !$has));

        if (!$missing) {
            $checks[] = ['folders', KunenaUpdateCheck::OK, '', [], ''];
        } else {
            $checks[] = ['folders', !empty($env['apache']) ? KunenaUpdateCheck::PROBLEM : KunenaUpdateCheck::INFO, implode(', ', $missing), $missing, ''];
        }

        // Guests who post: without a captcha for them, a spam robot posts too.
        $guests = !empty($config['pubWrite']);

        if (!$guests) {
            $checks[] = ['guests', KunenaUpdateCheck::OK, '', [], 'kunena'];
        } else {
            $checks[] = ['guests', (int) ($config['captcha'] ?? 0) === 1 ? KunenaUpdateCheck::INFO : KunenaUpdateCheck::PROBLEM, '', [], 'kunena'];
        }

        // Uploads for everybody: with guest posting, anybody stores files on the site.
        $everybody = array_values(array_filter(['imageUpload', 'fileUpload'], static fn ($s) => ($config[$s] ?? '') === 'everybody'));
        $checks[]  = ['uploads', $everybody && $guests ? KunenaUpdateCheck::WARNING : KunenaUpdateCheck::OK, implode(', ', $everybody), $everybody, 'kunena'];

        // The type of a file is read from its content too: without it a script can be uploaded with the name of a picture.
        $checks[] = ['mime', !empty($config['checkMimeTypes']) ? KunenaUpdateCheck::OK : KunenaUpdateCheck::WARNING, '', [], 'kunena'];

        // E-mail addresses on the profiles: robots collect them.
        $checks[] = ['email', !empty($config['showEmail']) ? KunenaUpdateCheck::WARNING : KunenaUpdateCheck::OK, '', [], 'kunena'];

        // Accounts without a check of the e-mail address: a robot registers as many accounts as it wants.
        if (empty($env['registration'])) {
            $checks[] = ['registration', KunenaUpdateCheck::OK, '', [], 'com_users'];
        } else {
            $checks[] = ['registration', (int) ($env['activation'] ?? 0) === 0 ? KunenaUpdateCheck::WARNING : KunenaUpdateCheck::OK, '', [], 'com_users'];
        }

        // The debug mode and the full error messages show paths and queries to every visitor.
        $debug    = !empty($env['debug']) || \in_array((string) ($env['errorReporting'] ?? ''), ['maximum', 'development'], true);
        $checks[] = ['debug', $debug ? KunenaUpdateCheck::WARNING : KunenaUpdateCheck::OK, '', [], 'com_config'];

        // Passwords and session cookies over http can be read on the way.
        $https    = !empty($env['https']) || (int) ($env['forceSsl'] ?? 0) === 2;
        $checks[] = ['https', $https ? KunenaUpdateCheck::OK : KunenaUpdateCheck::WARNING, '', [], 'com_config'];

        return $checks;
    }

    /**
     * The checks of this site.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function checksOnSite(): array
    {
        $app    = Factory::getApplication();
        $config = KunenaConfig::getInstance();
        $users  = ComponentHelper::getParams('com_users');

        try {
            $forbidden = (array) (new ReflectionClassConstant(KunenaUpload::class, 'FORBIDDEN'))->getValue();
        } catch (Throwable $e) {
            $forbidden = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'shtml', 'exe', 'js', 'html', 'htm', 'svg', 'htaccess'];
        }

        $protected = [];

        foreach (self::FOLDERS as $folder) {
            // A folder which does not exist yet is created with its .htaccess.
            $protected[$folder] = !is_dir(JPATH_ROOT . '/' . $folder) || is_file(JPATH_ROOT . '/' . $folder . '/.htaccess');
        }

        $settings = [];

        foreach (['fileTypes', 'imageTypes', 'avatarTypes', 'pubWrite', 'captcha', 'imageUpload', 'fileUpload', 'showEmail', 'checkMimeTypes'] as $key) {
            $settings[$key] = $config->$key ?? null;
        }

        return self::checks($settings, [
            'forbidden' => $forbidden,
            'protected' => $protected,
            'apache'    => stripos((string) ($_SERVER['SERVER_SOFTWARE'] ?? ''), 'apache') !== false
                || stripos((string) ($_SERVER['SERVER_SOFTWARE'] ?? ''), 'litespeed') !== false,
            'debug'          => (bool) $app->get('debug'),
            'errorReporting' => (string) $app->get('error_reporting'),
            'registration'   => (bool) $users->get('allowUserRegistration'),
            'activation'     => (int) $users->get('useractivation'),
            'https'          => Uri::getInstance()->isSsl(),
            'forceSsl'       => (int) $app->get('force_ssl'),
        ]);
    }
}
