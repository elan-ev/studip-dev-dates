<?php

/**
 * 001_add_dev_dates_config.php - Stud.IP plugin "Dev Dates"
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

class AddDevDatesConfig extends Migration
{
     /**
     * {@inheritdoc}
     */
    public function description()
    {
        return 'Add config option to set ical URL for Stud.IP development schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        Config::get()->create('DEVDATES_ICAL_URL', [
            'description' => 'URL to the ical file for Stud.IP development schedule',
            'section'     => 'DevDatesPlugin'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        Config::get()->delete('DEVDATES_ICAL_URL');
    }
}