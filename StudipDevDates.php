<?php

/**
 * StudipDevDates.php - Stud.IP plugin "Dev Dates"
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
class StudipDevDates extends StudIPPlugin implements PortalPlugin
{
    private $assetsUrl;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPluginName()
    {
        return 'Zeitplan für die Stud.IP-Entwicklung';
    }

    private function getDates($ical_url)
    {
        if (empty($ical_url)) {
            return [];
        }

        try {
            // Fetch the iCal data
            $ical_data = @file_get_contents($ical_url);
            if ($ical_data === false) {
                return [];
            }

            // Parse iCal events
            $events = $this->parseICalData($ical_data);

            // Group events by version number
            $grouped_events = [];
            foreach ($events as $event) {
                // Extract version number from title (e.g., "5.4 Feature Freeze" -> "5.4")
                if (preg_match('/^(\d+\.\d+)/', $event['title'], $matches)) {
                    $version = $matches[1];

                    if (!isset($grouped_events[$version])) {
                        $grouped_events[$version] = [];
                    }

                    $grouped_events[$version][] = $event;
                }
            }

            // Filter out versions where all dates are in the past
            $current_time = time();
            $filtered_events = [];

            foreach ($grouped_events as $version => $version_events) {
                $has_future_date = false;

                foreach ($version_events as $event) {
                    // Check if the event's end date is in the future or today
                    if ($event['end_timestamp'] >= $current_time) {
                        $has_future_date = true;
                        break;
                    }
                }

                if ($has_future_date) {
                    // Sort events within version by date
                    usort($version_events, function($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });

                    $filtered_events[$version] = $version_events;
                }
            }

            // Sort versions in descending order
            ksort($filtered_events, SORT_NATURAL);

            return $filtered_events;

        } catch (Exception $e) {
            return [];
        }
    }

    private function parseICalData($ical_data)
    {
        $events = [];
        $lines = explode("\n", str_replace("\r\n", "\n", $ical_data));

        // Unfold lines (handle line continuation with leading space/tab)
        $unfolded_lines = [];
        $current_line = '';

        foreach ($lines as $line) {
            // Check if line is a continuation (starts with space or tab)
            if (!empty($line) && ($line[0] === ' ' || $line[0] === "\t")) {
                // Remove the leading space/tab and append to current line
                $current_line .= substr($line, 1);
            } else {
                // Save previous line if it exists
                if ($current_line !== '') {
                    $unfolded_lines[] = $current_line;
                }
                $current_line = $line;
            }
        }
        // Don't forget the last line
        if ($current_line !== '') {
            $unfolded_lines[] = $current_line;
        }

        $current_event = null;

        foreach ($unfolded_lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $current_event = [
                    'title'         => '',
                    'date'          => '',
                    'end_date'      => '',
                    'timestamp'     => 0,
                    'end_timestamp' => 0,
                    'range_type'    => null  // 'from', 'until', 'range'
                ];
            } elseif ($line === 'END:VEVENT' && $current_event !== null) {
                if (!empty($current_event['title']) && !empty($current_event['date'])) {
                    // If no end date is set, use start date as end date
                    if ($current_event['end_timestamp'] === 0) {
                        // For "from" type events, set end timestamp far in the future
                        if ($current_event['range_type'] === 'from') {
                            // Set to 10 years in the future so it stays active until a new event comes
                            $current_event['end_timestamp'] = strtotime('+10 years', $current_event['timestamp']);
                        } else {
                            $current_event['end_timestamp'] = $current_event['timestamp'];
                        }
                    }
                    $events[] = $current_event;
                }
                $current_event = null;
            } elseif ($current_event !== null) {
                if (strpos($line, 'SUMMARY:') === 0) {
                    $title = substr($line, 8);
                    // Unescape iCal special characters
                    $title = str_replace(['\\,', '\\;', '\\n', '\\N', '\\\\'], [',', ';', "\n", "\n", '\\'], $title);

                    // Check for range indicators and extract them
                    if (preg_match('/^(\d+\.\d+)\s*>\s*(.+)$/', $title, $matches)) {
                        // "ab" case (from date onwards)
                        $current_event['range_type'] = 'from';
                        $current_event['title'] = $matches[1] . ' ' . trim($matches[2]);
                    } elseif (preg_match('/^(\d+\.\d+)\s*<\s*(.+)$/', $title, $matches)) {
                        // "bis" case (until date)
                        $current_event['range_type'] = 'until';
                        $current_event['title'] = $matches[1] . ' ' . trim($matches[2]);
                    } elseif (preg_match('/^(\d+\.\d+)\s*-\s*(.+)$/', $title, $matches)) {
                        // "von bis" case (date range)
                        $current_event['range_type'] = 'range';
                        $current_event['title'] = $matches[1] . ' ' . trim($matches[2]);
                    } else {
                        $current_event['title'] = $title;
                    }
                } elseif (strpos($line, 'DTSTART') === 0) {
                    // Handle both DTSTART:20240101 and DTSTART;VALUE=DATE:20240101
                    if (preg_match('/DTSTART[^:]*:(\d{8}(?:T\d{6}Z?)?)/', $line, $matches)) {
                        $date_string = $matches[1];

                        // Parse date (format: YYYYMMDD or YYYYMMDDTHHMMSSZ)
                        if (strlen($date_string) >= 8) {
                            $year  = substr($date_string, 0, 4);
                            $month = substr($date_string, 4, 2);
                            $day   = substr($date_string, 6, 2);

                            $current_event['timestamp'] = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
                            $current_event['date']      = date('d.m.Y', $current_event['timestamp']);
                        }
                    }
                } elseif (strpos($line, 'DTEND') === 0) {
                    // Handle both DTEND:20240101 and DTEND;VALUE=DATE:20240101
                    if (preg_match('/DTEND[^:]*:(\d{8}(?:T\d{6}Z?)?)/', $line, $matches)) {
                        $date_string = $matches[1];

                        // Parse date (format: YYYYMMDD or YYYYMMDDTHHMMSSZ)
                        if (strlen($date_string) >= 8) {
                            $year  = substr($date_string, 0, 4);
                            $month = substr($date_string, 4, 2);
                            $day   = substr($date_string, 6, 2);

                            $current_event['end_timestamp'] = mktime(23, 59, 59, (int)$month, (int)$day, (int)$year);
                            $current_event['end_date']      = date('d.m.Y', $current_event['end_timestamp']);
                        }
                    }
                }
            }
        }

        return $events;
    }

    /**
     * Return the template for the widget.
     *
     * @return Flexi\PhpTemplate The template containing the widget contents
     */
    public function getPortalTemplate()
    {
        global $perm;

        $template_factory = new \Flexi\Factory(__DIR__ . '/templates');
        $template = $template_factory->open('widget.php');

        if ($perm->have_perm('root')) {
            $navigation = new Navigation('', PluginEngine::getURL($this, [], 'devdates/settings'));
            $navigation->setImage(Icon::create('edit'));
            $navigation->setLinkAttributes([
                'title'       => _('Konfigurieren'),
                'data-dialog' => 'size=auto',
            ]);

            $template->icons = [$navigation];
        }

        $template->dates = $this->getDates(Config::get()->DEVDATES_ICAL_URL);

        return $template;
    }
}
