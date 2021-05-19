<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'paygw_wechat', language 'en'
 *
 * @package    paygw_wechat
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['appid'] = 'App ID';
$string['appid_help'] = 'The app ID that WeChat generated for your application.';
$string['merchantid'] = 'Merchant ID';
$string['merchantid_help'] = 'The Merchant ID that WeChat generated for your account.';
$string['gatewaydescription'] = 'Pay using WeChat.';
$string['gatewayname'] = 'WeChat';
$string['internalerror'] = 'An internal error has occurred. Please contact us.';
$string['pluginname'] = 'WeChat';
$string['pluginname_desc'] = 'The wechat plugin allows you to receive payments via WeChat.';
$string['privacy:metadata:paygw_wechat'] = 'Stores information about WeChat payments';
$string['privacy:metadata:paygw_wechat:timecreated'] = 'The time when the order was initiated.';
$string['privacy:metadata:paygw_wechat:timemodified'] = 'The time when the order record was last updated.';
$string['privacy:metadata:paygw_wechat:userid'] = 'The user who made the order.';
$string['privacy:metadata:paygw_wechat:status'] = 'The status of the order.';
$string['secret'] = 'Application secret';
$string['secret_help'] = 'The application secret that wechat generated.';
$string['key'] = 'Key';
$string['key_help'] = 'The key that wechat generated for your application.';
$string['errorgeneratingcode'] = 'An error occurred when attempting to initiate the payment.';
$string['sdknotinstalled'] = 'You cannot enable WeChat as the WeChat SDK is missing, please see the instructions in the plugin readme.';
$string['paymentsuccessful'] = 'Your payment has been successfully processed';