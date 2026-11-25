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

use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Throwable;

/**
 * One look at the forum (tool "Forum health summary"): the checks of the other tools which read only the database and the
 * files of this site, each with its number and a link to its page. The checks which ask other servers (broken links, the
 * addresses of the subscribers) are not run here: they are slow.
 *
 * @since   Kunena 8.0
 */
class KunenaHealthSummary
{
    /**
     * The levels, the worst first.
     *
     * @since   Kunena 8.0
     */
    public const LEVELS = ['error', 'problem', 'warning', 'info', 'ok'];

    /**
     * The title and the icon of each tool of the summary (as on the page of the tools).
     *
     * @since   Kunena 8.0
     */
    public const TOOLS = [
        'security'       => ['COM_KUNENA_TOOLS_SECURITY', 'shield-halved'],
        'beforeupdate'   => ['COM_KUNENA_TOOLS_UPDATE', 'rocket'],
        'diagnostics'    => ['COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS', 'stethoscope'],
        'moderation'     => ['COM_KUNENA_TOOLS_QUEUE', 'gavel'],
        'missingfiles'   => ['COM_KUNENA_TOOLS_MISSINGFILES', 'file-circle-xmark'],
        'overrides'      => ['COM_KUNENA_TOOLS_OVERRIDES', 'pen-ruler'],
        'avatarfiles'    => ['COM_KUNENA_TOOLS_AVATARS', 'circle-user'],
        'contentlimits'  => ['COM_KUNENA_TOOLS_LIMITS', 'ruler'],
        'banreview'      => ['COM_KUNENA_TOOLS_BANS', 'user-lock'],
        'warnings'       => ['COM_KUNENA_TOOLS_WARNINGS', 'triangle-exclamation'],
        'thankyourings'  => ['COM_KUNENA_TOOLS_RINGS', 'handshake'],
        'reactions'      => ['COM_KUNENA_TOOLS_REACTIONS', 'face-smile'],
        'ratingabuse'    => ['COM_KUNENA_TOOLS_RATINGS', 'star-half-stroke'],
        'unanswered'     => ['COM_KUNENA_TOOLS_UNANSWERED', 'comment-slash'],
        'categoryhealth' => ['COM_KUNENA_TOOLS_CATHEALTH', 'folder-tree'],
        'topicicons'     => ['COM_KUNENA_TOOLS_ICONS', 'icons'],
        'rankladder'     => ['COM_KUNENA_TOOLS_RANKS', 'ranking-star'],
        'announcements'  => ['COM_KUNENA_TOOLS_ANNOUNCEMENTS', 'bullhorn'],
    ];

    /**
     * The level of the checks of Security audit or Before you update: the worst status of them.
     *
     * @param   array  $checks  [name, status, ...] (KunenaUpdateCheck).
     *
     * @return  array{0: int, 1: string}  [the checks which are not fine, the level].
     *
     * @since   Kunena 8.0
     */
    public static function fromChecks(array $checks): array
    {
        $statuses = array_map(static fn ($check) => (string) $check[1], $checks);
        $bad      = \count(array_filter($statuses, static fn ($status) => \in_array($status, [KunenaUpdateCheck::PROBLEM, KunenaUpdateCheck::WARNING], true)));

        if (\in_array(KunenaUpdateCheck::PROBLEM, $statuses, true)) {
            return [$bad, 'problem'];
        }

        return [$bad, $bad ? 'warning' : 'ok'];
    }

    /**
     * An entry of the summary: the level of a number.
     *
     * @param   string  $tool   The layout of the tool.
     * @param   int     $count  What it found.
     * @param   string  $level  The level when it found something.
     *
     * @return  array{tool: string, count: int, level: string}
     *
     * @since   Kunena 8.0
     */
    public static function entry(string $tool, int $count, string $level): array
    {
        return ['tool' => $tool, 'count' => $count, 'level' => $count > 0 ? $level : 'ok'];
    }

    /**
     * The entries sorted, the worst first, and the level of the whole forum.
     *
     * @param   array  $entries  The entries (entry(), or with the level "error" when a check failed).
     *
     * @return  array{level: string, entries: array, counts: array<string, int>}
     *
     * @since   Kunena 8.0
     */
    public static function summarize(array $entries): array
    {
        $order = array_flip(self::LEVELS);
        usort($entries, static fn ($a, $b) => [$order[$a['level']] ?? 9, -$a['count']] <=> [$order[$b['level']] ?? 9, -$b['count']]);

        $counts = array_fill_keys(self::LEVELS, 0);

        foreach ($entries as $entry) {
            $counts[$entry['level']] = ($counts[$entry['level']] ?? 0) + 1;
        }

        $level = 'ok';

        foreach (['error', 'problem', 'warning'] as $worst) {
            if ($counts[$worst]) {
                $level = $worst;
                break;
            }
        }

        return ['level' => $level, 'entries' => $entries, 'counts' => $counts];
    }

    /**
     * The checks: layout => fn (int $now): array{0: int, 1: string} ([what it found, the level when it found something]).
     *
     * @return  array<string, callable>
     *
     * @since   Kunena 8.0
     */
    public static function checks(): array
    {
        return [
            'security'      => static fn () => self::fromChecks(KunenaSecurityAudit::checksOnSite()),
            'beforeupdate'  => static fn () => self::fromChecks(KunenaUpdateCheck::checksOnSite()),
            'diagnostics'   => static fn () => [array_sum(array_map([KunenaDiagnostics::class, 'count'], KunenaDiagnostics::getList())), 'warning'],
            'moderation'    => static fn () => [(int) KunenaModerationQueue::unapprovedOnSite()['total'], 'info'],
            'missingfiles'  => static fn () => [\count(KunenaMissingAttachments::findOnSite()['items']), 'warning'],
            'overrides'     => static fn () => [array_sum(array_map(static fn ($file) => \count($file['problems']), KunenaLanguageOverrides::checkOnSite())), 'warning'],
            'avatarfiles'   => static fn () => [\count(KunenaAvatarFiles::checkOnSite()['items']), 'info'],
            'contentlimits' => static function () {
                $limits = KunenaContentLimits::findOnSite();

                return [$limits['attachmentsTotal'] + $limits['signaturesTotal'], 'info'];
            },
            'banreview'     => static fn (int $now) => [\count(KunenaBanReview::reviewOnSite($now)['items']), 'warning'],
            'warnings'      => static fn (int $now) => [\count(array_filter(KunenaWarningsOverview::reportOnSite(KunenaWarningsOverview::PERIODS[0], $now)['users'], static fn ($user) => $user['reaches'])), 'warning'],
            'thankyourings' => static fn () => [\count(KunenaThankyouRings::findOnSite()['pairs']), 'warning'],
            'reactions'     => static fn () => [\count(KunenaReactionsOverview::reportOnSite()['suspects']), 'warning'],
            'ratingabuse'   => static function () {
                $found = KunenaRatingAbuse::findOnSite();

                return [\count($found['sameScore']) + \count($found['targeted']), 'warning'];
            },
            'unanswered'     => static fn (int $now) => [(int) KunenaUnansweredTopics::reportOnSite(KunenaUnansweredTopics::AGES[0], $now)['total'], 'info'],
            'categoryhealth' => static fn (int $now) => [\count(KunenaCategoryHealth::checkOnSite(KunenaCategoryHealth::PERIODS[1], $now)['items']), 'info'],
            'topicicons'     => static function () {
                $icons = KunenaTopicIcons::checkOnSite();

                return [array_sum(array_map(static fn ($set) => array_sum($set['unknown']), $icons['sets'])) + \count($icons['missing']), 'info'];
            },
            'rankladder' => static function () {
                $ranks = KunenaRankLadder::checkOnSite();

                return [\count($ranks['same']) + (int) $ranks['noStart'] + \count($ranks['images']) + \count($ranks['unknown']), 'info'];
            },
            'announcements' => static fn (int $now) => [\count(array_filter(KunenaAnnouncementsReview::reviewOnSite($now)['items'], static fn ($item) => \in_array($item['state'], ['expired', 'old'], true))), 'warning'],
        ];
    }

    /**
     * Runs the checks of this site; a check which fails is shown as such and does not stop the others.
     *
     * @param   int  $now  The current time.
     *
     * @return  array{level: string, entries: array, counts: array<string, int>, seconds: float}
     *
     * @since   Kunena 8.0
     */
    public static function runOnSite(int $now): array
    {
        $start   = microtime(true);
        $entries = [];

        foreach (self::checks() as $tool => $check) {
            try {
                [$count, $level] = $check($now);
                $entries[]       = self::entry($tool, (int) $count, $level);
            } catch (Throwable $e) {
                $entries[] = ['tool' => $tool, 'count' => 0, 'level' => 'error', 'error' => $e->getMessage()];
            }
        }

        return self::summarize($entries) + ['seconds' => round(microtime(true) - $start, 2)];
    }
}
