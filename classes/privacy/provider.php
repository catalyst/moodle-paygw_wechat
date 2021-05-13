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
 * Privacy Subsystem implementation for paygw_wechat.
 *
 * @package    paygw_wechat
 * @category   privacy
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_wechat\privacy;

use core_payment\privacy\paygw_provider;
use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy Subsystem implementation for paygw_wechat.
 *
 * We implement a partial privacy api here - the tables we use are linked to the payment api privacy provider
 * so the base payment privacy class does the bulk of the effort.
 * But - we have a new userid field in here so we need to flag that as well and implement userlist_provider class
 * so that privacy unit tests don't fail.
 *
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider,
    paygw_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'paygw_wechat',
            [
                'userid'       => 'privacy:metadata:paygw_wechat:userid',
                'status'       => 'privacy:metadata:paygw_wechat:status',
                'timecreated'  => 'privacy:metadata:paygw_wechat:timecreated',
                'timemodified' => 'privacy:metadata:paygw_wechat:timemodified',
            ],
            'privacy:metadata:paygw_wechat'
        );

        return $items;
    }
    /**
     * Export all user data for the specified payment record, and the given context.
     *
     * @param \context $context Context
     * @param array $subcontext The location within the current context that the payment data belongs
     * @param \stdClass $payment The payment record
     */
    public static function export_payment_data(\context $context, array $subcontext, \stdClass $payment) {
        global $DB;

        $subcontext[] = get_string('gatewayname', 'paygw_wechat');
        $record = $DB->get_record('paygw_wechat', ['paymentid' => $payment->id]);

        $data = (object) [
            'orderid' => $record->pp_orderid,
        ];
        writer::with_context($context)->export_data(
            $subcontext,
            $data
        );
    }

    /**
     * Delete all user data related to the given payments.
     *
     * @param string $paymentsql SQL query that selects payment.id field for the payments
     * @param array $paymentparams Array of parameters for $paymentsql
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams) {
        global $DB;

        $DB->delete_records_select('paygw_wechat', "paymentid IN ({$paymentsql})", $paymentparams);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   int $userid The user to search.
     * @return  contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        // Core payment api will already sorted this - see summary in class description above.
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        // Core payment api will already sorted this - see summary in class description above.
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Core payment api will already sorted this - see summary in class description above.
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // Core payment api will already sorted this - see summary in class description above.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // Core payment api will already have triggered this.
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist   $userlist   The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        // Core payment api will already have triggered this.
    }
}
