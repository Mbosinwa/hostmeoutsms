<?php
class RouteSms extends HostmeoutSms {

    function __construct($message,$gsmnumber,$senderid){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
        $this->senderid = $this->utilsender($senderid);
    }

    function send(){
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }

        $params = $this->getParams();

                    if (empty($params->senderid)){
		      $senderid   =  $this->getSenderid();
			
			}
			else{
				$senderid = $params->senderid;
			}
/*
RouteSms SmsPlus - Bulk Http API Specification 
 the Bulk HTTP API for the RouteSms SMPP System for HostmeoutSms

http://smsplus.routesms.com:8080/bulksms/bulksms?username=lmysmsl2&type=0&dlr=1&password=@lr6fse6d&destination=@@receipient&source=@@sender&message=@@message&


*/ 
        $result = @file_get_contents('smsplus.routesms.com:8080/bulksms/bulksms?username='.$params->user.'&type=0&dlr=1&password='.$params->pass.'&destination='.urlencode($this->gsmnumber).'&source='.urlencode($senderid).'&message='.urlencode($this->message).'&');
        $result = explode("|",$result);

        if($result[0] == "1701") {
            $log[] = ("Message sent.");
        } elseif($result[0] == "1707") {
            $log[] = ("Invalid Source (Sender).");
            $error[] = ("Invalid Source (Sender).");
        }
		elseif($result[0] == "1705") {
            $log[] = ("Invalid Message.");
            $error[] = ("Invalid Message.");
        }
		elseif($result[0] == "1706") {
            $log[] = ("Invalid Destination.");
            $error[] = ("Invalid Destination.");
        } elseif($result[0] == "1703") {
            $log[] = ("Invalid username/password combination.");
            $error[] = ("Invalid username/password combination.");
        } elseif($result[0] == "1710") {
            $log[] = ("Internal Error.");
            $error[] = ("Internal Error.");
        } elseif($result[0] == "1715") {
            $log[] = ("Gateway unavailable.");
            $error[] = ("Gateway unavailable.");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'number' => $result[1],
            'msgid' => $result[2],
        );
    }

    function balance(){
        $params = $this->getParams();
        if($params->user && $params->pass) {
			//http://smsplus.routesms.com:8080/bulksms/bulksms?username=mysmsl2&type=0&dlr=1&password=lr6fse6&balance=true&
            $result = @file_get_contents('http://smsplus.routesms.com:8080/bulksms/bulksms?username='.$params->user.'&password='.$params->pass.'&balance=true&');

            if ($result) {
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function report($msgid){
        return "success";
    }

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
        function utilsender($senderid){
        return $senderid;
    }
}

return array(
    'value' => 'RouteSms',
    'label' => 'RouteSms',
    'fields' => array(
        'user','pass'
    )
);