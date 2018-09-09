<?php
/* WHMCS SMS Addon with Licencing
 * HostMeOut LLC - http://www.hostmeout.com
 *
 * https://hostmeout.com/knowledgebase/10/HostMeOut-SMS-APi
 *
 * Developed by Mbosinwa Awunor (www.hostmeout.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

function hostmeout_sms_config() {
	$licensekey = grabaddonsconfig("licensekey");
	$localkey = grabaddonsconfig("localkey");
	include ROOTDIR . "/modules/addons/hostmeout_sms/licenseclass.php";
	$checky = new HostmeoutLicense();
	$licensedata = $checky->hmosms_check_license($licensekey, $localkey);

	$licensestatus = $licensedata['status'];

	$isenabled = ( $licensestatus == "Active" ? $licensestatus : 0);


	$configarray = array( "FriendlyName" => array( "Type" => "System", "Value" => "HostMeOut SMS One-Time Password Security" ), "Description" => array( "Type" => "System", "Value" => "HostMeOut OTP SMS Security requires that a user enter's a $r 6 digit code which will be sent as SMS to the user mobile Device to complete login. Admin can also disable the SMS Based OTP to save your sms unit cost and use the In built alternative One Time Base Security, This works with mobile apps such as OATH Token, Google Authenticator and FREE OTP.<br /><br />For more information about HostMeOut SMS Time Based Tokens OR One Time Base Authenticator, please <a href=\"https://hostmeout.com/knowledgebase/10/HostMeOut-SMS-APi\" target=\"_blank\">click here</a>." . ($isenabled ? "" : "<br /><br /><strong>HostMeOut SMS Gives You a Free 3 days Test Drive for (unlimited users)</strong>") ), "enablesms" => array( "FriendlyName" => "Use SMS Based OTP", "Type" => "yesno", "Size" => "10", "Description" => "Check to enable ability to send sms as verification to login leave uncheck to use inbuilt default Time-based One-Time Password" ), "Licensed" => array( "Type" => "System", "Value" => ($isenabled ? true : false) ), "SubscribeLink" => array( "Type" => "System", "Value" => "https://hostmeout.com/sms_license" ) );
	return $configarray;
}


function hostmeout_sms_activate($params) {
	global $whmcs;
    $enablesms = $params['settings']['enablesms'];
	
	    	if($enablesms)
	{
	$is_smsenabled = 'yes';
	}
	else
	{
	$is_smsenabled = 'no';
	}
	
		if ($whmcs->get_req_var( "startsms" )) {
		if (!isset( $_SESSION['qrdetails'] )) {
			exit();
		}

		include ROOTDIR . "/modules/security/hostmeout_sms/qrcodegenerator.php";
		QRcode::png( $_SESSION['qrdetails'], false, 6, 6 );
		exit();
	}
	else{
	if ($whmcs->get_req_var( "showqrimage" )) {
		if (!isset( $_SESSION['totpqrurl'] )) {
			exit();
		}

		include ROOTDIR . "/modules/security/hostmeout_sms/qrcodegenerator.php";
		QRcode::png( $_SESSION['totpqrurl'], false, 6, 6 );
		exit();
	}
	}
	if($is_smsenabled=="yes")
	{
		//sms startsms
		
	$username = $params['user_info']['username'];
	$userid = $params['user_info']['id'];
	$tokendata = (isset( $params['user_settings']['tokendata'] ) ? $params['user_settings']['tokendata'] : "");
	
	if ($whmcs->get_req_var( "step" ) == "verify") {
		
		if($whmcs->get_req_var( "action" ) == "sendsms")
		{
		$smsotp = gen_code(6);
	    $table = "mod_hostmeoutsms_otp";
        $values = array(
            "name" => $username,
            "userid" => $userid,
            "code" => $smsotp
        );
        if (insert_query($table, $values))
		{
			
		$sms = new HostmeoutSms();
		$settings = $sms->getSettings();
		
		$userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `b`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `c`.`value` = 'on' 
        AND `a`.`id` = '".$userid."' order by `a`.`firstname`";
        $result = mysql_query($userSql);
        while ($data = mysql_fetch_array($result))
		{
		$gsmnumber = $data['gsmnumber'];
		$you = $data['firstname'];
		}
                $Hour = date('G');

             if ( $Hour >= 5 && $Hour <= 11 ) {
            $greet = "Good Morning";
             } else if ( $Hour >= 12 && $Hour <= 18 ) {
             $greet =  "Good Afternoon";
          } else if ( $Hour >= 19 || $Hours <= 4 ) {
             $greet =  "Good Evening";
         }
		$message = "$greet $you, Your Security Code is $smsotp, Use this code above to Authenticate your Login!";
		$senderid = "HMOSMS";
		$cost = grabaddonsconfig("smscost");
            $sms->setSenderid($senderid);	
	    $sms->setGsmnumber($gsmnumber);
            $sms->setMessage($message);
            $sms->setUserid($userid);
			$sms->setSmscost($cost);
			$result = $sms->send();

            if($result == false){
                $smserror = "An Error Occur on SMS";
            }
		}
		}

		$verifyfail = false;

		if ($whmcs->get_req_var( "verifykey" )) {
		
			$ans = authenticate_code( $userid, $whmcs->get_req_var( "verifykey" ) );

			if ($ans == $whmcs->get_req_var( "verifykey" )) {
				//send sms
				$output = array();
				$output['completed'] = true;
				$output['msg'] = "Key Verified Successfully!";
				$output['settings'] = array( "tokendata" => $tokendata );
                                $sql = "DELETE FROM mod_hostmeoutsms_otp WHERE userid = '$userid'";
                                full_query($sql);
				return $output;
			}

			$verifyfail = true;
		}

		$output = "<h2>Verification Step</h2><p>Enter the security code sent to your mobile phone as authenticator and we'll make sure it's configured correctly before enabling it.</p>";

		if ($verifyfail) {
			$output .= "<div class=\"errorbox\"><strong>It seem's there's a problem...</strong><br />The security code you entered did not match what was expected. Please try again. ( \"" . $smserror . "\" )</div>";
		}

		$output .= "<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"hostmeout_sms\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<p align=\"center\"><input type=\"text\" name=\"verifykey\" size=\"10\" maxlength=\"6\" style=\"font-size:18px;\" /></p>
<p align=\"center\"><input type=\"button\" value=\"Confirm &raquo;\" class=\"btn btn-primary large\" onclick=\"dialogSubmit()\" /></p>
</form>";
	}
	else {
		$fullname = $params['user_info']['firstname']." ".$params['user_info']['lastname'];
		$msg = "Your Security Code will be Sent to you Shortly, once you click continue!";
		$qrdetails = $whmcs->get_config( "CompanyName" ) . " : " . $fullname . " : " . $username . " : " . $msg;
		$_SESSION['qrdetails'] = $qrdetails;
		$output = "<h2>HostMeOut SMS Security One-Time Password</h2>
<p>This authentication option get's it's second factor using an SMS One-Time password based algorithm, Meaning you dont need to mermorize the Security code, since it One-Time Password.  We generate a specially crafted 6 digit security code and send to Your mobile phone as SMS for you to Authenticate the code before login.  This Add Extra Layer of Security to your Login.</p>
<p>To configure your SMS Authenticator:</p>
<ul>
<li>Begin by Notifying your User to Enable the Tick Want SMS Field</li>
<li>User Should Enter Valid Phone Number in their GSM Number Field</li>
<li>Note: This are the Custom Field you created during installation of this Addons.</li>
</ul>

<div align=\"center\">" . (function_exists( "imagecreate" ) ? "<img src=\"" . $_SERVER['PHP_SELF'] . "?2fasetup=1&module=hostmeout_sms&startsms=1\" />" : "<em>GD is missing from the PHP build on your server so unable to generate image</em>") . "</div>

<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"hostmeout_sms\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<input type=\"hidden\" name=\"action\" value=\"sendsms\" />
<p align=\"center\"><input type=\"button\" value=\"Continue &raquo;\" onclick=\"dialogSubmit()\" class=\"btn btn-primary\" /></p>
</form>

";
	}

	return $output;
		
		//sms otp
		
	}
    else{
	$username = $params['user_info']['username'];
	$tokendata = (isset( $params['user_settings']['tokendata'] ) ? $params['user_settings']['tokendata'] : "");
	hostmeout_sms_loadgaclass();
	$gaotp = new MyOauth();
	$username = $whmcs->sanitize( "a-z", $whmcs->get_config( "CompanyName" ) ) . ":" . $username;

	if ($whmcs->get_req_var( "step" ) == "verify") {
		$verifyfail = false;

		if ($whmcs->get_req_var( "verifykey" )) {
			$ans = $gaotp->authenticateUser( $username, $whmcs->get_req_var( "verifykey" ) );

			if ($ans) {
				$output = array();
				$output['completed'] = true;
				$output['msg'] = "Key Verified Successfully!";
				$output['settings'] = array( "tokendata" => $tokendata );
				return $output;
			}

			$verifyfail = true;
		}

		$output = "<h2>Verification Step</h2><p>Enter the security code generated by your mobile authenticator app and we'll make sure it's configured correctly before enabling it.</p>";

		if ($verifyfail) {
			$output .= "<div class=\"errorbox\"><strong>It seem's there's a problem...</strong><br />The code you entered did not match what was expected. Please try again.</div>";
		}

		$output .= "<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"hostmeout_sms\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<p align=\"center\"><input type=\"text\" name=\"verifykey\" size=\"10\" maxlength=\"6\" style=\"font-size:18px;\" /></p>
<p align=\"center\"><input type=\"button\" value=\"Confirm &raquo;\" class=\"btn btn-primary large\" onclick=\"dialogSubmit()\" /></p>
</form>";
	}
	else {
		$key = $gaotp->setUser( $username, "TOTP" );
		$url = $gaotp->createUrl( $username );
		$_SESSION['totpqrurl'] = $url;
		$output = "<h2>Time-based One-Time Password</h2>
<p>This authentication option get's it's second factor using a time based algorithm.  Your mobile phone can be used to generate the codes.  If you don't already have an app that can do this, we recommend Google Authenticator which is available for iOS, Android and Windows mobile devices.</p>
<p>To configure your authenticator app:</p>
<ul>
<li>Begin by selecting to add a new time based token</li>
<li>Then use your app to scan the barcode below, or alternatively enter this secret key manually: \"" . $gaotp->getKey( $username ) . "\"</li>
</ul>

<div align=\"center\">" . (function_exists( "imagecreate" ) ? "<img src=\"" . $_SERVER['PHP_SELF'] . "?2fasetup=1&module=hostmeout_sms&showqrimage=1\" />" : "<em>GD is missing from the PHP build on your server so unable to generate image</em>") . "</div>

<form onsubmit=\"dialogSubmit();return false\">
<input type=\"hidden\" name=\"2fasetup\" value=\"1\" />
<input type=\"hidden\" name=\"module\" value=\"hostmeout_sms\" />
<input type=\"hidden\" name=\"step\" value=\"verify\" />
<p align=\"center\"><input type=\"button\" value=\"Continue &raquo;\" onclick=\"dialogSubmit()\" class=\"btn btn-primary\" /></p>
</form>

";
	}

	return $output;
}
}


function hostmeout_sms_challenge($params) {
	    $enablesms = $params['settings']['enablesms'];
	$username = $params['user_info']['username'];
	$userid = $params['user_info']['id'];
	
	    	if($enablesms)
	{
	$is_smsenabled = 'yes';
	}
	else
	{
	$is_smsenabled = 'no';
	}
	 
	 	if($is_smsenabled=="yes")
	{
		//sms startsms
			$smsotp = gen_code(6);
	    $table = "mod_hostmeoutsms_otp";
        $values = array(
            "name" => $username,
            "userid" => $userid,
            "code" => $smsotp
        );
        if (insert_query($table, $values))
		{
			
		$sms = new HostmeoutSms();
		$settings = $sms->getSettings();
		
		$userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `b`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `c`.`value` = 'on' 
        AND `a`.`id` = '".$userid."' order by `a`.`firstname`";
        $result = mysql_query($userSql);
        while ($data = mysql_fetch_array($result))
		{
		$gsmnumber = $data['gsmnumber'];
		$you = $data['firstname'];
		}
                $Hour = date('G');

             if ( $Hour >= 5 && $Hour <= 11 ) {
            $greet = "Good Morning";
             } else if ( $Hour >= 12 && $Hour <= 18 ) {
             $greet =  "Good Afternoon";
          } else if ( $Hour >= 19 || $Hours <= 4 ) {
             $greet =  "Good Evening";
         }
		$message = "$greet $you, Your Security Code is $smsotp, Use this code above to Authenticate your Login!";
		$senderid = "HMOSMS";
		$cost = grabaddonsconfig("smscost");
         $sms->setSenderid($senderid);	
	    $sms->setGsmnumber($gsmnumber);
            $sms->setMessage($message);
            $sms->setUserid($userid);
            $sms->setSmscost($cost);
			$result = $sms->send();

            if($result == false){
                $smserror = "An Error Occur on SMS";
            }
		}
		
		//sms startsms
		
	}
	$output = "<form method=\"post\" action=\"dologin.php\">
    <div align=\"center\">
        <input type=\"text\" name=\"key\" size=\"10\" style=\"font-size:20px;\" maxlength=\"6\" /> <input type=\"submit\" value=\"Login &raquo;\" class=\"btn\" />
    </div>
</form>";
//$pp = var_dump($result);
// $output.= "$pp";
	return $output;
}


function hostmeout_sms_get_used_otps() {
	global $whmcs;

	$usedotps = $whmcs->get_config( "HMOUsedOTPs" );
	$usedotps = ($usedotps ? unserialize( $usedotps ) : array());

	if (!is_array( $usedotps )) {
		$usedotps = array();
	}

	return $usedotps;
}


function hostmeout_sms_verify($params) {
	global $whmcs;
    $enablesms = $params['settings']['enablesms'];
	
	    	if($enablesms)
	{
	$is_smsenabled = 'yes';
	}
	else
	{
	$is_smsenabled = 'no';
	}
	 
	 	if($is_smsenabled=="yes")
	{
		//sms startsms
		
	$username = $params['user_info']['username'];
	$userid = $params['user_info']['id'];	
	$tokendata = $params['user_settings']['tokendata'];
	$key = $params['post_vars']['key'];
	
	$user = "WHMCS:" . $username;
	$usedotps = hostmeout_sms_get_used_otps();
	
	$hash = md5( $user . $key );

	if (array_key_exists( $hash, $usedotps )) {
		return false;
	}

	$ans = false;
	$ans = authenticate_code( $userid, $key );

	if ($ans == $key) {
		$usedotps[$hash] = time();
		$expiretime = time() - 5 * 60;
		foreach ($usedotps as $k => $time) {

			if ($time < $expiretime) {
				unset( $usedotps[$k] );
			$sql = "DELETE FROM mod_hostmeoutsms_otp WHERE userid = '$userid'";
            full_query($sql);
				continue;
			}

			break;
		}

		$whmcs->set_config( "HMOUsedOTPs", serialize( $usedotps ) );
	}

	return $ans;

		
		
		//sms endsms
		
	}
	else{
	$username = $params['admin_info']['username'];
	$tokendata = $params['admin_settings']['tokendata'];
	$key = $params['post_vars']['key'];
	hostmeout_sms_loadgaclass();
	$gaotp = new MyOauth();
	$gaotp->setTokenData( $tokendata );
	$username = "WHMCS:" . $username;
	$usedotps = hostmeout_sms_get_used_otps();
	
	$hash = md5( $username . $key );

	if (array_key_exists( $hash, $usedotps )) {
		return false;
	}

	$ans = false;
	$ans = $gaotp->authenticateUser( $username, $key );

	if ($ans) {
		$usedotps[$hash] = time();
		$expiretime = time() - 5 * 60;
		foreach ($usedotps as $k => $time) {

			if ($time < $expiretime) {
				unset( $usedotps[$k] );
				continue;
			}

			break;
		}

		$whmcs->set_config( "HMOUsedOTPs", serialize( $usedotps ) );
	}

	return $ans;
	}
}

function gen_code($length=10)
{
    $final_rand='';
    for($i=0;$i< $length;$i++)
    {
        $final_rand .= rand(0,9);
 
    }
 
    return $final_rand;
}

function authenticate_code($userid, $code)
	{
	        $where = array(
			"userid" => array("sqltype" => "LIKE", "value" => $userid),
			"code" => array("sqltype" => "LIKE", "value" => $code)
			);
            $result = select_query("mod_hostmeoutsms_otp", "*", $where);
			while ($data = mysql_fetch_array($result)) {
				if ($data['code'] == $code)
				{
					//send sms
					return $data['code'];
				}
				else{
					return false;
				}
			}
	
	}
	
	function grabaddonsconfig($config)
	{
		$grabdata = full_query("SELECT * 
		FROM  `tbladdonmodules` 
		WHERE `module` = 'hostmeout_sms' AND `setting` = '".$config."'
		LIMIT 0 , 30");
      
	  $par = mysql_fetch_array($grabdata);
      $data = $par['value'];
	  return $data;	
	}
	

function hostmeout_sms_loadgaclass() {
	if (!class_exists( "GoogleAuthenticator" )) {
		include ROOTDIR . "/modules/security/hostmeout_sms/ga.php";
		class MyOauth extends GoogleAuthenticator {
			protected $tokendata = "";

			function setTokenData($token) {
				$this->tokendata = $token;
			}


			function getData($username) {
				global $twofa;

				$tokendata = ($this->tokendata ? $this->tokendata : $twofa->getUserSetting( "tokendata" ));
				return $tokendata;
			}


			function putData($username, $data) {
				global $twofa;

				$twofa->saveUserSettings( array( "tokendata" => $data ) );
				return true;
			}


			function getUsers() {
				return false;
			}


		}


	}

}


?>