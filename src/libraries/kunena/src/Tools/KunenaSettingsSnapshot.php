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

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use ReflectionMethod;
use Throwable;

/**
 * The settings of the forum which differ from the defaults (tool "Settings snapshot"): seen at once, saved to a file and put
 * on another site (from a test site to the live one) after a preview of what changes. The keys and passwords of services
 * and the e-mail address of the forum stay on their site: they are never in the file and never taken from one.
 *
 * @since   Kunena 8.0
 */
class KunenaSettingsSnapshot
{
    /**
     * The settings which stay on their site: their name matches this pattern, or they are in SITE.
     *
     * @since   Kunena 8.0
     */
    public const SECRET = '/key|secret|password|token/i';

    /**
     * The settings of the site itself.
     *
     * @since   Kunena 8.0
     */
    public const SITE = ['email'];

    /**
     * The largest file which is read (bytes).
     *
     * @since   Kunena 8.0
     */
    public const MAX_FILE = 512 * 1024;

    /**
     * Does a setting stay on its site?
     *
     * @param   string  $name  The setting.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isPrivate(string $name): bool
    {
        return \in_array($name, self::SITE, true) || preg_match(self::SECRET, $name) === 1;
    }

    /**
     * A value as it is compared: the settings keep "1", 1 and true for the same.
     *
     * @param   mixed  $value  The value.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function normal(mixed $value): string
    {
        if (\is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (\is_array($value) || \is_object($value)) {
            return (string) json_encode($value);
        }

        return trim((string) $value);
    }

    /**
     * The settings which differ from their default, the private ones left out.
     *
     * @param   array  $current   name => value.
     * @param   array  $defaults  name => default.
     *
     * @return  array<string, array{value: mixed, default: string}>
     *
     * @since   Kunena 8.0
     */
    public static function changed(array $current, array $defaults): array
    {
        $changed = [];

        foreach ($defaults as $name => $default) {
            $name = (string) $name;

            if (!\array_key_exists($name, $current) || self::isPrivate($name)) {
                continue;
            }

            if (self::normal($current[$name]) !== self::normal($default)) {
                $changed[$name] = ['value' => $current[$name], 'default' => (string) $default];
            }
        }

        ksort($changed);

        return $changed;
    }

    /**
     * The file of a snapshot.
     *
     * @param   array   $settings  name => value.
     * @param   string  $version   The version of Kunena.
     * @param   string  $date      The date.
     *
     * @return  string  JSON.
     *
     * @since   Kunena 8.0
     */
    public static function export(array $settings, string $version, string $date): string
    {
        $settings = array_filter($settings, static fn ($name) => !self::isPrivate((string) $name), ARRAY_FILTER_USE_KEY);
        ksort($settings);

        return (string) json_encode(['kunena' => $version, 'date' => $date, 'settings' => $settings], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * The settings of a file of a snapshot.
     *
     * @param   string  $json  The file.
     *
     * @return  array|null  name => value, null when it is no snapshot.
     *
     * @since   Kunena 8.0
     */
    public static function read(string $json): ?array
    {
        if (\strlen($json) > self::MAX_FILE) {
            return null;
        }

        $data = json_decode($json, true);

        if (!\is_array($data) || !isset($data['kunena']) || !\is_array($data['settings'] ?? null)) {
            return null;
        }

        // Only plain values: a setting is never a list or an object in a snapshot.
        return array_filter($data['settings'], static fn ($value) => \is_scalar($value) || $value === null);
    }

    /**
     * What a snapshot changes: the settings of this site which it gives another value, and the names it gives which this
     * site does not know or keeps private.
     *
     * @param   array  $import   name => value of the file.
     * @param   array  $current  name => value of this site.
     *
     * @return  array{changes: array<string, array{from: mixed, to: mixed}>, ignored: string[]}
     *
     * @since   Kunena 8.0
     */
    public static function preview(array $import, array $current): array
    {
        $changes = [];
        $ignored = [];

        foreach ($import as $name => $value) {
            $name = (string) $name;

            if (!\array_key_exists($name, $current) || self::isPrivate($name)) {
                $ignored[] = $name;

                continue;
            }

            if (self::normal($current[$name]) !== self::normal($value)) {
                $changes[$name] = ['from' => $current[$name], 'to' => $value];
            }
        }

        ksort($changes);
        sort($ignored);

        return ['changes' => $changes, 'ignored' => $ignored];
    }

    /**
     * The defaults of the settings of this site (config.xml of Kunena).
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function defaultsOnSite(): array
    {
        try {
            // The reader of the configuration itself: one list of the defaults.
            $method = new ReflectionMethod(KunenaConfig::class, 'getXmlDefaults');

            return (array) $method->invoke(null, JPATH_ADMINISTRATOR . '/components/com_kunena/config.xml');
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * The settings of this site.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function currentOnSite(): array
    {
        return KunenaConfig::getInstance()->toArray();
    }

    /**
     * The file of the settings of this site which differ from the defaults.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function exportOnSite(): string
    {
        $changed = self::changed(self::currentOnSite(), self::defaultsOnSite());

        return self::export(array_map(static fn ($c) => $c['value'], $changed), (string) KunenaForum::version(), gmdate('Y-m-d H:i:s'));
    }

    /**
     * Gives this site the settings of a snapshot (only the ones of the preview: known, not private, different).
     *
     * @param   array  $import  name => value.
     *
     * @return  integer  The settings changed.
     *
     * @since   Kunena 8.0
     */
    public static function importOnSite(array $import): int
    {
        $config  = KunenaConfig::getInstance();
        $changes = self::preview($import, $config->toArray())['changes'];

        foreach ($changes as $name => $change) {
            $config->$name = $change['to'];
        }

        if ($changes) {
            $config->save();
        }

        return \count($changes);
    }
}
