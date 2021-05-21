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
 * Gateway cleanup, check if remaining orders are paid, and if not, delete them to clean up.
 *
 * @package    paygw_wechat
 * @author     Dan Marsden https://danmarsden.com
 * @copyright  2021 Catalyst IT https://www.catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_wechat\task;

defined('MOODLE_INTERNAL') || die();

use paygw_wechat\wechat_helper;
use core_payment\helper;

/**
 * get_scores class, used to get scores for submitted files.
 *
 * @package    paygw_wechat
 * @author     Dan Marsden https://danmarsden.com
 * @copyright  2021 Catalyst IT https://www.catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends \core\task\scheduled_task {
    /**
     * Returns the name of this task.
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('cleanup', 'paygw_wechat');
    }

    /**
     * Executes task.
     */
    public function execute() {
        global $DB;

        // Get old expired orders.
        $orders = $DB->get_recordset_select('paygw_wechat', 'status = ? AND timemodified < ?',
            [wechat_helper::ORDER_STATUS_PENDING, (time() - (HOURSECS))]);
        foreach ($orders as $order) {
            try {
                $config = (object)helper::get_gateway_configuration($order->component, $order->paymentarea,
                                                                    $order->itemid, 'wechat');
            } catch (\dml_exception $e) {
                // This payment method doesn't exist - delete the order - happens when enrol fee is removed from a course.
                $DB->delete_records('paygw_wechat', ['id' => $order->id]);
                continue;
            }

            // Sanity check if order was actually processed.
            if (wechat_helper::check_payment($config, $order)) {
                // Flag this as processed and enrol the user.
                wechat_helper::process_payment($order);
            } else {
                // This in an old unprocessed order - delete it.
                $DB->delete_records('paygw_wechat', ['id' => $order->id]);
            }
        }
        $orders->close();
    }
}