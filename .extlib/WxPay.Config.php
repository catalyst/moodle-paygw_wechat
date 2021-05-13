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
 * Contains class for wechat config.
 *
 * @package    paygw_wechat
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once "wechatsdk/WxPay.Config.Interface.php";

/**
 * Class WxPayConfig.
 * @copyright 2005-2021 Tenpay https://pay.weixin.qq.com/
 */
class WxPayConfig extends WxPayConfigInterface {

    /**
     * @var object - contains Moodle payment gateway config (clientid/appid etc)
     */
    protected $moodleconfig;

    public function __construct($config) {
        $this->moodleconfig = new stdClass();
        $this->moodleconfig->appid = $config->appid;
        $this->moodleconfig->merchantid = $config->merchantid;
        $this->moodleconfig->key = $config->key;
        $this->moodleconfig->secret = $config->secret;
    }

	public function GetAppId() {
        return $this->moodleconfig->appid;
	}
	public function GetMerchantId()	{
        return $this->moodleconfig->merchantid;
	}
	
	public function GetNotifyUrl() {
        global $CFG;
		return $CFG->wwwroot.'/payment/gateway/wechat/notify.php';
	}
	public function GetSignType() {
		return "HMAC-SHA256";
	}

	public function GetProxy(&$proxyHost, &$proxyPort) {
	    global $CFG;
	    if (!empty($CFG->proxyhost)) {
            $proxyHost = $CFG->proxyhost;
            $proxyPort = $CFG->proxyport;
        }
	}

	public function GetReportLevenl() {
		return 1;
	}

	public function GetKey() {
        return $this->moodleconfig->key;
	}
	public function GetAppSecret() {
        return $this->moodleconfig->secret;
	}

	public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)	{
	    // Not Required for the payment options we use.
		return;
	}
}
