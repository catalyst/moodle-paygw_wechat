# WeChat  #

This plugin was developed thanks to funding from Xi’an Jiaotong-Liverpool University (https://www.xjtlu.edu.cn)

The plugin allows a site to connect to Wechat China using the PHP SDK available here:  
https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=11_1
  
### Note: You must download the SDK and add it to the code prior to installing this plugin. ###

## Pre-install ##
The WeChat SDK is not distributed by WeChat with a valid open source license - unfortunately this means that we cannot 
pre-package the SDK within the Moodle plugin as this violates the GPL license so you must download it manually prior to installing this plugin.

The PHP SDK is available here:
https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=11_1
This plugin has been tested with wechat php_sdk_v3.0.10

Copy the 5 files from the "lib" folder in the zip supplied above into the .extlib/wechatsdk folder in your plugin.
you should have the following files in the following location:
* /payment/gateway/wechat/.extlib/wechatsdk/WxPay.Api.php
* /payment/gateway/wechat/.extlib/wechatsdk/WxPay.Config.Interface.php
* /payment/gateway/wechat/.extlib/wechatsdk/WxPay.Data.php
* /payment/gateway/wechat/.extlib/wechatsdk/WxPay.Exception.php
* /payment/gateway/wechat/.extlib/wechatsdk/WxPay.Notify.php

Then install the plugin into your site like normal.

## Configure Moodle
* Go to site administration / Plugins / Manage payment gateways and enable the WeChat payment gateway.
* Go to site administration / Payments / Payment accounts
* Click the button 'Create payment account' then enter an account name for identifying it when setting up enrolment on payment, then save changes.
* On the Payment accounts page, click the payment gateway link to configure WeChat.
* In the configuration page, enter your appid/merchant id/key and secret from the application you have created in your WeChat account.
      
## Add Enrolment on payment.
* Go to Go to Site administration > Plugins > Enrolments > Manage enrol plugins and click the eye icon opposite Enrolment on payment.
* Click the settings link, configure as required then click the 'Save changes' button.
* Go to the course you wish to enable payment for, and add the 'Enrolment on payment' enrolment method to the course.
* Select a payment account, amend the enrolment fee as necessary then click the button 'Add method'.

see also:  
[moodledocs: Payment Gateways](https://docs.moodle.org/en/Payment_gateways)  
[moodledocs: Enrolment on Payment](https://docs.moodle.org/en/Enrolment_on_payment)

## License ##

Copyright 2021 Catalyst IT

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
