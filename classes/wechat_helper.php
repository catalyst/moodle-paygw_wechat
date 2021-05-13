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
 * Contains helper class to work with wechat.
 *
 * @package    paygw_wechat
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_wechat;
use core_payment\helper;
use moodle_url;
use html_writer;
use paygw_wechat\nativepay;

defined('MOODLE_INTERNAL') || die();

/**
 * Class wechat_helper
 * @package paygw_wechat
 * @copyright 2021 Catalyst IT
 */
class wechat_helper {
    /**
     * @var integer Payment is pending
     */
    public const ORDER_STATUS_PENDING = 0;
    /**
     * @var integer Payment was received.
     */
    public const ORDER_STATUS_PAID = 1;

    /**
     * Get an unprocessed order record - if one already exists - return it.
     *
     * @param string $component
     * @param string $paymentarea
     * @param integer $itemid
     * @return false|\stdClass
     */
    public static function get_unprocessed_order($component, $paymentarea, $itemid) {
        global $USER, $DB;

        $existingorder = $DB->get_record('paygw_wechat', ['component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'userid' => $USER->id,
            'status' => self::ORDER_STATUS_PENDING]);
        if ($existingorder) {
            return $existingorder;
        }
        return false;
    }

    /**
     * Create a new order.
     *
     * @param string $component
     * @param string $paymentarea
     * @param integer $itemid
     * @param string $accountid
     * @return \stdClass
     */
    public static function create_order($component, $paymentarea, $itemid, $accountid) {
        global $USER, $DB;

        // Create a new order record.
        $neworder = new \stdClass();
        $neworder->component = $component;
        $neworder->paymentarea = $paymentarea;
        $neworder->itemid = $itemid;
        $neworder->userid = $USER->id;
        $neworder->accountid = $accountid;
        $neworder->status = self::ORDER_STATUS_PENDING;
        $neworder->timecreated = time();
        $neworder->modified = $neworder->timecreated;

        $id = $DB->insert_record('paygw_wechat', $neworder);
        $neworder->id = $id;

        return $neworder;
    }

    /**
     * Get payment script to trigger QR Code display.
     *
     * @param \stdClass $config
     * @param \stdClass $order
     * @param string $description
     * @param int $cost
     * @return string
     */
    public static function get_payment_script($config, $order, $description, $cost) {
        global $CFG;
        require_once($CFG->dirroot."/payment/gateway/wechat/.extlib/wechatsdk/WxPay.Api.php");
        require_once($CFG->dirroot."/payment/gateway/wechat/.extlib/WxPay.Config.php");

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($description);
        $input->SetOut_trade_no($order->id.date("YmdHis"));
        $input->SetTotal_fee($cost);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 1200));
        $input->SetNotify_url($CFG->wwwroot.'/payment/gateway/wechat/process.php');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($order->id);

        try {
            $wxconfig = new \WxPayConfig($config);
            $result = \WxPayApi::unifiedOrder($wxconfig, $input);

            $qrcode = new \core_qrcode($result["code_url"]);
            $imagedata = 'data:image/png;base64,' . base64_encode($qrcode->getBarcodePngData(6, 6));
            return html_writer::img($imagedata, '');
        } catch (\Exception $e) {
            // TODO EXCEPTION;
            return get_string('errorgeneratingcode', 'paygw_wechat');
        }

        return get_string('errorgeneratingcode', 'paygw_wechat');
    }

    /**
     * Check wechat to see if this order has been paid.
     *
     * @param $config
     * @param \stdClass $order
     * @throws \Exception
     * @return boolean
     */
    public static function check_payment($config, $order) {
        // TODO: check if payment processed already.
        return false;

        try {
            $result = Factory::payment()->common()->query($order->id);
            $responsechecker = new ResponseChecker();
            if ($responsechecker->success($result)) {
                if (!empty($result->tradeStatus) &&
                    ($result->tradeStatus === 'TRADE_SUCCESS' || $result->tradeStatus === 'TRADE_FINISHED')) {
                    return true;
                } else {
                    debugging("Call success, but invalid tradeStatus");
                }
            }
        } catch (Exception $e) {
            debugging("Call failed, " . $e->getMessage());
        }
        return false;
    }

    /**
     * Process payment and deliver the order.
     * @param \stdClass $order
     * @return array
     * @throws \coding_exception
     */
    public static function process_payment ($order) {
        global $DB;
        $payable = helper::get_payable($order->component, $order->paymentarea, $order->itemid);
        $cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), helper::get_gateway_surcharge('wechat'));
        $message = '';
        try {
            $paymentid = helper::save_payment($payable->get_account_id(), $order->component, $order->paymentarea,
                $order->itemid, (int) $order->userid, $cost, $payable->get_currency(), 'paypal');

            // Store wechat extra information.
            $order->paymentid = $paymentid;
            $order->timemodified = time();
            $order->status = self::ORDER_STATUS_PAID;

            $DB->update_record('paygw_wechat', $order);

            helper::deliver_order($order->component, $order->paymentarea, $order->itemid, $paymentid, (int) $order->userid);
            $success = true;
        } catch (\Exception $e) {
            debugging('Exception while trying to process payment: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $message = get_string('internalerror', 'paygw_wechat');
            $success = false;
        }

        return [
            'success' => $success,
            'message' => $message,
        ];
    }
}