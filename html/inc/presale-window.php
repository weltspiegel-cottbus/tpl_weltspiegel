<?php

/**
 * Presale ("Vorverkauf") cutoff date.
 *
 * Plain PHP include (not a Joomla layout — it returns a value, not markup),
 * shared by com_weltspiegel/movies/default.php and mod_current_events/default.php
 * so both use the exact same "what's current vs. presale" boundary.
 *
 * German cinema convention this encodes:
 *  - The cinema week runs Thursday–Wednesday.
 *  - Program disposition (booking) happens on Mondays, for the week that
 *    starts that same Thursday (3 days later) — i.e. one cinema week ahead
 *    of the week currently running.
 *
 * Working through what that means for "how far is the program already known":
 *  - On Monday/Tuesday, the running week still ends *this* Wednesday, but
 *    today's disposition already locked in the week after that too — so the
 *    known horizon reaches the Wednesday AFTER NEXT.
 *  - From Wednesday (the last day of the running week) through Sunday (once
 *    the new week has started), that previously-disposed week IS the next
 *    week — so the known horizon is just the NEXT Wednesday.
 *  - Both phrasings resolve to the *same calendar date* across the whole
 *    Monday–Sunday span; it only moves forward again on the following Monday.
 *
 * A show is "current" (not presale) if its date falls on or before this
 * cutoff — compare by calendar day, not by hour, since the cutoff is
 * inclusive of its own day.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

/**
 * @param   DateTime  $now  Reference date/time ("today").
 *
 * @return  DateTime  The cutoff date (midnight), inclusive.
 */
function weltspiegel_presale_cutoff_date(DateTime $now): DateTime
{
    $weekday = (int) $now->format('N'); // 1 = Monday ... 7 = Sunday

    // Days until the closest Wednesday that is strictly in the future. If
    // today already is Wednesday, that Wednesday is 7 days away, not 0.
    $daysToNextWednesday = (3 - $weekday + 7) % 7;
    if ($daysToNextWednesday === 0) {
        $daysToNextWednesday = 7;
    }

    $nextWednesday = (clone $now)->setTime(0, 0)->modify("+{$daysToNextWednesday} days");

    // Monday (1) / Tuesday (2): the week after next is already disposed today.
    if ($weekday === 1 || $weekday === 2) {
        return $nextWednesday->modify('+7 days');
    }

    return $nextWednesday;
}
