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

/**
 * The progress of the recount of the statistics (Tools, Recount). The recount runs in steps: the topics and the topics of the
 * users go through the ids of the topics in batches, the categories, the users and the polls in one go each. The progress
 * is counted from the step and the place in it, only over the steps which were chosen: it never goes back or past the end.
 *
 * @since   Kunena 8.0
 */
class KunenaRecountProgress
{
    /**
     * The steps, in their order: the name of the choice of each.
     *
     * @since   Kunena 8.0
     */
    public const STEPS = ['topics', 'usertopics', 'categories', 'users', 'polls'];

    /**
     * The steps which go through the topics in batches.
     *
     * @since   Kunena 8.0
     */
    public const BATCHED = ['topics', 'usertopics'];

    /**
     * The topics of a batch.
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 5000;

    /**
     * The weight of a step which runs in one go, in topics (a tenth of the topics, at least one).
     *
     * @param   int  $maxId  The highest id of a topic.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function oneGo(int $maxId): int
    {
        return max(1, intdiv(max(0, $maxId), 10));
    }

    /**
     * The weight of a step.
     *
     * @param   string  $step   A name of STEPS.
     * @param   int     $maxId  The highest id of a topic.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function weight(string $step, int $maxId): int
    {
        return \in_array($step, self::BATCHED, true) ? max(1, $maxId) : self::oneGo($maxId);
    }

    /**
     * The progress in percent.
     *
     * @param   array<string, bool>  $chosen  name of a step => chosen.
     * @param   int                  $step    The index of the current step in STEPS (past the last one: done).
     * @param   int                  $start   The first topic of the next batch of the current step (0: not started).
     * @param   int                  $maxId   The highest id of a topic.
     *
     * @return  float  From 0 to 100.
     *
     * @since   Kunena 8.0
     */
    public static function percent(array $chosen, int $step, int $start, int $maxId): float
    {
        $total = 0;
        $done  = 0;

        foreach (self::STEPS as $index => $name) {
            if (empty($chosen[$name])) {
                continue;
            }

            $weight = self::weight($name, $maxId);
            $total += $weight;

            if ($index < $step) {
                $done += $weight;
            } elseif ($index === $step && \in_array($name, self::BATCHED, true)) {
                // The topics before the start of the next batch are done.
                $done += min($weight, max(0, $start - 1));
            }
        }

        return $total ? round(100 * $done / $total, 1) : 100.0;
    }

    /**
     * The last topic of the batch which starts at a topic: the next batch starts right after it (BETWEEN includes both ends).
     *
     * @param   int  $start  The first topic of the batch.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function batchEnd(int $start): int
    {
        return max(1, $start) + self::BATCH - 1;
    }
}
