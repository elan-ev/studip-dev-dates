<?php

/**
 * settings.php - Stud.IP plugin "Dev Dates"
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
?>
<article class="studip" id="studip-dev-dates">
    <form class="default" method="post"
        action="<?= PluginEngine::getUrl('studipdevdates', [], 'devdates/update_config') ?>"
    >
        <fieldset>
            <label class="required" for="devdates-ical-url">
                iCal-URL aus der die Milestones der Entwicklung generiert werden:

            </label>
            <input type="text"
                name="devdates_ical_url"
                id="devdates-ical-url"
                required value="<?= htmlReady($ical_url) ?>"
            >
        </fieldset>
        <footer data-dialog-button>
            <?= Studip\Button::createAccept(_('Speichern')) ?>
            <?= Studip\Button::createCancel(_('Abbrechen'), URLHelper::getURL('dispatch.php/start')) ?>
        </footer>
    </form>
</article>