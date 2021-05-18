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
 * Test wechat settings.
 *
 * @package     paygw_wechat
 * @category    admin
 * @copyright   2021 Catalyst IT
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Test wechat settings.
use core_payment\helper;

require_once(__DIR__ . '/../../../config.php');

require_login();
require_admin();

$gateway = $DB->get_record('payment_gateways', ['gateway' => 'wechat'], '*', MUST_EXIST);
$config = @json_decode($gateway->config, true);
$config = (object)$config;

$neworder = new \stdClass();
$neworder->id = 'TEST1234123';
$neworder->component = 'enrol_fee';
$neworder->paymentarea = 'fee';
$neworder->itemid = '58';
$neworder->userid = '2';
$neworder->accountid = '1';
$neworder->status = 0;
$neworder->timecreated = time();
$neworder->modified = $neworder->timecreated;

$payment = \paygw_wechat\wechat_helper::get_payment_script($config, $neworder, 'TEST', "40");
echo $payment;
