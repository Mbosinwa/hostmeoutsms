<?php
/* WHMCS SMS Addon with Licencing
 * HostMeOut LLC - http://www.hostmeout.com
 *
 * https://hostmeout.com/knowledgebase/10/HostMeOut-SMS-APi
 *
 * Developed at Mbosinwa Awunor (www.mbosinwa.me)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function hook_hostmeout_sms_clientHeadBlock($vars) {
    //Here is my custom code below
    $headoutput = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">';
    return $headoutput;
    
}

add_hook("ClientAreaHeadOutput",1,"hook_hostmeout_sms_clientHeadBlock");

#adding Menu Item to primaryNavbar

use WHMCS\View\Menu\Item as MenuItem;
add_hook('ClientAreaPrimaryNavbar', 1, function (MenuItem $primaryNavbar)
{
    $primaryNavbar->addChild('Send SMS')
        ->setUri('index.php?m=hostmeout_sms')
        ->setOrder(70);
});

require_once("smsclass.php");
$class = new HostmeoutSms();
$hooks = $class->getHooks();

foreach($hooks as $hook){
    add_hook($hook['hook'], 1, $hook['function'], "");
}