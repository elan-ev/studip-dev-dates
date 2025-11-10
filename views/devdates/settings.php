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

    <fieldset class="contentbox">
        <legend>
            <?= _('Hinweise zur Formatierung der Termine') ?>
        </legend>
        <section>
            <p>
                <?= _('Die Titel der Termine im iCal-Format sollten mit der Versionsnummer beginnen (z.B. "6.2"). '
                    . 'Optional können Sie Zeitraum-Indikatoren verwenden:') ?>
            </p>
            <ul>
                <li>
                    <strong><?= _('Einzeltermin:') ?></strong>
                    <?= _('6.2 Feature Freeze') ?>
                    <br>
                    <em><?= _('Wird als einzelnes Datum angezeigt') ?></em>
                </li>
                <li>
                    <strong><?= _('Ab einem Datum:') ?></strong>
                    <?= _('6.2 > Testphase') ?>
                    <br>
                    <em><?= _('Wird als "ab [Datum]" angezeigt') ?></em>
                </li>
                <li>
                    <strong><?= _('Bis zu einem Datum:') ?></strong>
                    <?= _('6.2 < Release-Vorbereitung') ?>
                    <br>
                    <em><?= _('Wird als "bis [Datum]" angezeigt') ?></em>
                </li>
                <li>
                    <strong><?= _('Zeitraum:') ?></strong>
                    <?= _('6.2 - Beta-Phase') ?>
                    <br>
                    <em><?= _('Wird als Datumsbereich "[Startdatum] - [Enddatum]" angezeigt (benötigt DTSTART und DTEND im iCal)') ?></em>
                </li>
            </ul>
            <p>
                <?= _('Die Versionsnummer wird automatisch erkannt und die Termine werden entsprechend gruppiert. '
                    . 'Versionen, bei denen alle Termine in der Vergangenheit liegen, werden automatisch ausgeblendet.') ?>
            </p>
        </section>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern')) ?>
        <?= Studip\Button::createCancel(_('Abbrechen'), URLHelper::getURL('dispatch.php/start')) ?>
    </footer>
</form>
