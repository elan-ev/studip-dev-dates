<?php

/**
 * devdates.php - Stud.IP plugin "Dev Dates"
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
class DevdatesController extends StudipController
{
    function settings_action()
    {
        global $perm;

        if (!$perm->have_perm('root')) {
            throw new AccessDeniedException();
        }

        $this->ical_url = Config::get()->DEVDATES_ICAL_URL ?? '';
    }

    public function update_config_action()
    {
        global $perm;

        if (!$perm->have_perm('root')) {
            throw new AccessDeniedException();
        }

        Config::get()->store('DEVDATES_ICAL_URL', Request::get('devdates_ical_url', ''));

        $this->redirect(URLHelper::getURL('dispatch.php/start'));
    }
}
