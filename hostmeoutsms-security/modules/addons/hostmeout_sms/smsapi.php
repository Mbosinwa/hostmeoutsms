<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * HostmeoutSMS - http://www.hostmeout.com
 *
 * https://hostmeout.com/knowledgebase/10/HostMeOut-SMS-APi
 *
 * Developed And Re-modified by Mbosinwa Awunor (www.hostmeout.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
 header('Content-type: application/json');

putenv("TZ=Africa/Lagos");

//header("Content-Type: application/json");
//header("Expires: 0");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");

if (isset($_REQUEST['tag']) && $_REQUEST['tag'] != '') {
    //get tag
//	
    
    $tag = $_REQUEST['tag'];
 
    // include db handler

	require_once("licenseclass.php");
	require("../../../init.php");
	$userid = $_REQUEST['userid'];
	$usersip = (isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']);
	$usedomain = $_SERVER['SERVER_NAME'];
	
         $class = new HostmeoutSms();

  $result = mysql_query("SELECT firstname, credit FROM tblclients WHERE id=" . $userid);
  $data = mysql_fetch_array($result);
  //echo $ca->getUserID();
  //echo var_dump($data);
	        $client = $data[0];
                $credit = $data[1]; 
            $gsmnumber = $_REQUEST['phone'];
            $message   = $_REQUEST['message'];
            $api       = $_REQUEST['apikey'];
            $senderid    = $_REQUEST['sender'];
            $currency = getCurrency($userid);

            $class->setGsmnumber($gsmnumber);
            $class->setMessage($message);
            $class->setUserid($userid);
            $class->setSenderid($senderid);
			 $smscost = 1.8;

	  $grabapikey = full_query("SELECT * 
FROM  `tbladdonmodules` 
WHERE `module` = 'hostmeout_sms' AND `setting` = 'apikey'
LIMIT 0 , 30");
      $par = mysql_fetch_array($grabapikey);
      $apikey = $par['value'];



	  $grablic = full_query("SELECT * 
FROM  `tbladdonmodules` 
WHERE `module` = 'hostmeout_sms' AND `setting` = 'licensekey'
LIMIT 0 , 30");
      $params = mysql_fetch_array($grablic);

$grablocal = full_query("SELECT * 
FROM  `tbladdonmodules` 
WHERE  `module` =  'hostmeout_sms'
AND  `setting` =  'localkey'
LIMIT 0 , 30");

$var = mysql_fetch_array($grablocal);

   
	 
    $checky = new HostmeoutLicense();
	 $licensekey = $params['value'];
	 $localkey = $var['value'];
	$results = $checky->hmosms_check_license($licensekey, $localkey);
			 
	
    $response = array("tag" => $tag, "success" => 0, "error" => 0);

		if ($results['status'] == 'Active') {
			if ($results['localkey']) {
				$locakey = $results['localkey'];
                                 $api_key = md5($licensekey);
				$sql_update_apikey = 'UPDATE tbladdonmodules SET value = \'' . $api_key . '\' where setting=\'apikey\' and module=\'hostmeout_sms\'';
				$update_apikey_res = full_query($sql_update_apikey);

				$sql_update_config_details_SMSAdminid = 'UPDATE tbladdonmodules SET value = \'' . $locakey . '\' where setting=\'localkey\' and module=\'hostmeout_sms\'';
				$update_SMSAdminid_res = full_query($sql_update_config_details_SMSAdminid);
				$msg = 'License Active';
			}
		} 
else {
			if ($results['status']  == 'Invalid') {

				$msg = 'Invalid License';
			} 
else {
				if ($results['status'] == 'Expired') {
					$msg = 'Expired License!';
				} 
else {
					if ($results['status'] == 'Suspended') {
						$msg = 'Suspended License.';
					} 
else {
						
						$msg = ' License required .msg:' . $results;
					}
				}
			}
		}


if ($tag == 'bulksms') {

		if ($results['status'] != 'Active') {
			$statt = $results['status'];
			$response['alert'] = "$msg ($licensekey)";
			$response["error"] = 1;
			echo json_encode($response);
			
		}


		if ($results['status'] == 'Active') {
			$statt = $results['status'];
			$locakey = $results['localkey'];
		}
 

      if(!empty($_REQUEST['message']) && !empty($_REQUEST['phone']) && !empty($_REQUEST['apikey']) && $apikey==$api){


$lengths=strlen($message);
if($lengths<=160)
{
	$page=1;
}
elseif($lengths>160 && $lengths<=320)
{
	$page=2;
}
elseif($lengths>320 && $lengths<=480)
{
	$page="3";
}
elseif($lengths>480 && $lengths<=640)
{
	$page="4";
}
elseif($lengths>640 && $lengths<=800)
{
	$page="5";
}
elseif($lengths>800 && $lengths<=960)
{
	$page="6";
}
		if($_REQUEST['sender']>11)
		{
			$response['alert'] = "Your Sender ID Is more than 11 characters";
			$response["error"] = 1;
			echo json_encode($response);
		}

if(isset($_REQUEST['send']))
{
	
	if(empty($_REQUEST['type']))
	{
				$response['alert'] = "Please Select Reciepent Method";
				$response["error"] = 1;
                echo json_encode($response);
	}
	

	if($_REQUEST['type']=="gsm")
	{
		$numbers=$str = preg_replace('/\s+/', '', $gsmnumber);
		$exploded=explode(',', $numbers);
		$counter = count($exploded);
		//Total Cost
 $cost = $smscost * $page * $counter*1;
 $date = date('Ymd');
 
  $query = 'SELECT username
FROM  `tbladmins` 
WHERE id = 1';
$result = full_query($query);

while ($data = mysql_fetch_array($result)) {
	$admin .= $data['username'];
}
 $command = "createinvoice";
 $adminuser = $admin;
 $values["userid"] = $userid;
 $values["date"] = $date;
 $values["duedate"] = $date;
 $values["paymentmethod"] = "banktransfer";
 $values["sendinvoice"] = true;
 $values["itemdescription1"] = "$client - Bulk SMS From APi to $counter Recipient which took about $page page(s)";
 $values["itemamount1"] = $cost;
 $values["itemtaxed1"] = 0;
 
 
 if ($results['status'] == 'Active') {
 $localapi = localAPI($command,$values,$adminuser);
 $invoiceid = $localapi['invoiceid'];
            if ($localapi['result']!="success") 
          {
            logActivity("HostMeOut APi SMS: An Error Occurred, ".$localapi['message']);
           }
         else
         {       

            logActivity("HostMeOut APi SMS: #$invoiceid Invoice id Generated For $client");

/*
	for($i=0; $i<$counter; $i++)
{
	echo $sperated_number=$exploded[$i];
}
*/
            if($credit <= $cost) {
           	$response['alert'] = "Insufficent Funds , please fund your account";
			$response["error"] = 1;
			echo json_encode($response);
            }
            else
          {
            $result = $class->send();
            if($result == false){
                $smserror = $class->getErrors();
            }else{
                //add invoice payment of message
                   $command = "applycredit";
                   $adminuser = $admin;
                   $values["invoiceid"] = $invoiceid;
                   $values["amount"] = $cost;      
                   $localapi = localAPI($command,$values,$adminuser);
 
             if ($localapi["result"]=="success") {
   # Result was OK!
              logActivity("HostMeOut APi SMS: Invoice #".$localapi["invoiceid"]." Payment for Bulk SMS has been mark as Paid!");
 } else {
   # An error occured
   logActivity("The following error occured: ".$localapi["message"]);
 }
		$response['alert'] = 'SMS Sent to '.$gsmnumber.' Cost '.number_format($cost, 2);
		$response["success"] = 8080;
		echo json_encode($response);
                 }
		
          }

}//end of invoice creation and payment
}//end of licenseclass check
}
}
}
else{
	        $response['alert'] = "Empty Message and Empty phone OR Invalid API Key";
		$response["error"] = 1;
                echo json_encode($response);
}

}
elseif ($tag == 'checkbalance') {
	$userid = $_REQUEST['userid'];
	$result = full_query("SELECT firstname, credit FROM tblclients WHERE id=" . $userid);
    $data = mysql_fetch_array($result);	
	$client = $data[0];
    $credit = $data[1]; 
	 if (!empty($_REQUEST['apikey']) && $apikey == $api) {
                $response["success"] = 8080;
                $response["user"]["name"] = $client;
                $response["user"]["balance"] = number_format($credit, 2);
                echo json_encode($response);
            } else {
                // user failed to retrive
                	$response['alert'] = "Invalid API Key";
			$response["error"] = 1;
                echo json_encode($response);
            }
}

}
	
?>