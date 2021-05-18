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
 * PayPal repository module to encapsulate all of the AJAX requests that can be sent for wechat.
 *
 * @module     paygw_wechat/repository
 * @package    paygw_wechat
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Return the wechat form
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description The description of the payment.
 * @returns {Promise<{clientid: string, brandname: string, cost: number, currency: string}>}
 */
export const getForm = (component, paymentArea, itemId, description) => {
    const request = {
        methodname: 'paygw_wechat_get_code',
        args: {
            component,
            paymentarea: paymentArea,
            itemid: itemId,
            description: description
        },
    };
    return Ajax.call([request])[0];
};

/**
 * Checks to see if the order is processed.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description The description of the payment.
 * @returns {Promise<{status: boolean}>}
 */
export const getState = (component, paymentArea, itemId, description) => {
    const request = {
        methodname: 'paygw_wechat_get_status',
        args: {
            component,
            paymentarea: paymentArea,
            itemid: itemId,
            description: description
        },
    };
    // TODO - loop on ajax call until we get a succesful state and then redirect to course.
    return Ajax.call([request])[0];
};

/**
 * Return the payment url.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @returns {string} payment url.
 */
export const createRedirectUrl = async(component, paymentArea, itemId) => {
    const url = M.cfg.wwwroot + '/payment/gateway/wechat/redirect.php?' +
        'component=' + component +
        '&paymentarea=' + paymentArea +
        '&itemid=' + itemId;
    return url;
};