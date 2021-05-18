<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Redirects after succesful payment.
 *
 * This script waits for Payment notification from Alipay,
 * then double checks that data by sending it back to Alipay.
 * If Alipay verifies this then it flags the payment as paid.
 *
 * @package    paygw_alipay
 * @copyright 2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require_once(__DIR__ . '/../../../config.php');

// Get Moodle order id from Alipay response.
$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_ALPHANUMEXT);

require_login(null, false);

$successurl = new moodle_url('/');

if (method_exists('\core_payment\helper', 'get_success_url')) {
    // This is a 3.11 or higher site, we can get the url from the api.
    $successurl = helper::get_success_url($component, $paymentarea, $itemid);
} else if ($component == 'enrol_fee' && $paymentarea == 'fee') {
    require_once($CFG->dirroot.'/course/lib.php');
    // Moodle 3.10 site - try to work out the correct course to redirect this person to on payment.
    $courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);
    if (!empty($courseid)) {
        $successurl = course_get_url($courseid);
    }
}

redirect($successurl);
