<?php

/**
 * widget.php - Stud.IP plugin "Dev Dates"
 *
 * This file is part of the Stud.IP plugin "Dev Dates"
 *
 * @package    StudipDevDates
 * @author     Till Glöggler <gloeggler@elan-ev.de>
 * @copyright  2025 ELAN e.V.
 * @license    https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * @var array $dates Grouped events by version
 * @var array $icons Navigation icons
 */

$current_time = time();
?>

<article class="studip" id="studip-dev-dates">
    <?php if (empty($dates)): ?>
        <section>
            <p><?= _('Keine Termine verfügbar.'); ?></p>
        </section>
    <?php else: ?>
        <?php foreach ($dates as $version => $events): ?>
            <?php
                // Find the next upcoming event and current active event for this version
                $next_event_index = null;
                $next_event_timestamp = PHP_INT_MAX;
                $active_event_indices = [];

                foreach ($events as $index => $event) {
                    // Check if event is currently active
                    if ($event['timestamp'] <= $current_time && $event['end_timestamp'] >= $current_time) {
                        $active_event_indices[] = $index;
                    }
                    // Check if event is upcoming (starts in the future) and is the earliest
                    if ($event['timestamp'] > $current_time && $event['timestamp'] < $next_event_timestamp) {
                        $next_event_timestamp = $event['timestamp'];
                        $next_event_index = $index;
                    }
                }

                // If no active events, mark the next upcoming event as active (current goal)
                if (empty($active_event_indices) && $next_event_index !== null) {
                    $active_event_indices[] = $next_event_index;
                    $next_event_index = null; // Clear next event since it's now active
                }
            ?>
            <section>
                <h2><?= _('Version') ?> <?= htmlReady($version) ?></h2>
                <table class="default">
                    <colgroup>
                        <col style="width: 30%">
                        <col style="width: 70%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?= _('Datum') ?></th>
                            <th><?= _('Ereignis') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $index => $event): ?>
                            <?php
                                $is_next = ($index === $next_event_index);
                                $is_active = in_array($index, $active_event_indices);
                                // Event is past if its end date has passed
                                $is_past = ($event['end_timestamp'] < $current_time);
                                $row_class = '';

                                if ($is_active) {
                                    $row_class = 'active-event';
                                } elseif ($is_next) {
                                    $row_class = 'next-event';
                                } elseif ($is_past) {
                                    $row_class = 'past-event';
                                }

                                // Determine date display based on range type
                                $date_display = '';
                                if ($event['range_type'] === 'from') {
                                    // "ab" case
                                    $date_display = _('ab') . ' ' . htmlReady($event['date']);
                                } elseif ($event['range_type'] === 'until') {
                                    // "bis" case
                                    $date_display = _('bis') . ' ' . htmlReady($event['end_date'] ?: $event['date']);
                                } elseif ($event['range_type'] === 'range') {
                                    // "von bis" case
                                    if (!empty($event['end_date']) && $event['end_date'] !== $event['date']) {
                                        $date_display = htmlReady($event['date']) . ' - ' . htmlReady($event['end_date']);
                                    } else {
                                        $date_display = htmlReady($event['date']);
                                    }
                                } else {
                                    // Default case (no range indicator)
                                    if (!empty($event['end_date']) && $event['end_date'] !== $event['date']) {
                                        $date_display = htmlReady($event['date']) . ' - ' . htmlReady($event['end_date']);
                                    } else {
                                        $date_display = htmlReady($event['date']);
                                    }
                                }
                            ?>
                            <tr class="<?= $row_class ?>">
                                <td>
                                    <?php if ($is_active): ?>
                                        <strong><?= $date_display ?></strong>
                                        <?= Icon::create('span-full', Icon::ROLE_STATUS_GREEN)->asImg(['title' => _('Aktuell aktiv')]) ?>
                                    <?php elseif ($is_next): ?>
                                        <strong><?= $date_display ?></strong>
                                        <?= Icon::create('date', Icon::ROLE_INFO)->asImg(['title' => _('Nächster Termin')]) ?>
                                    <?php else: ?>
                                        <?= $date_display ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($is_next || $is_active): ?>
                                        <strong><?= htmlReady($event['title']) ?></strong>
                                    <?php else: ?>
                                        <?= htmlReady($event['title']) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</article>

<style>
    #studip-dev-dates tr.next-event {
        background-color: #fffacd;
    }

    #studip-dev-dates tr.active-event {
        background-color: #d4edda;
    }

    #studip-dev-dates tr.past-event {
        opacity: 0.6;
        color: #666;
    }

    #studip-dev-dates tr.next-event:hover td {
        background-color: #fff9b3;
    }

    #studip-dev-dates tr.active-event:hover td {
        background-color: #c3e6cb;
    }
</style>