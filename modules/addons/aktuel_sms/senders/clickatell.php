<?php
class clickatell extends AktuelSms {

    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){
        $params = $this->getParams();

        $baseurl = "http://api.clickatell.com";

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

        $url = "$baseurl/http/auth?user=$params->user&password=$params->pass&api_id=$params->apiid&from=$params->senderid";
        $ret = file($url);
        $log[] = ("Sunucudan dönen cevap: ".$ret);

        $sess = explode(":", $ret[0]);
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=$params->senderid";

            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                $log[] = ("Mesaj gönderildi.");
            } else {
                $log[] = ("Mesaj gönderilemedi. Hata: $ret");
                $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $ret");
            }
        } else {
            $log[] = ("Mesaj gönderilemedi. Authentication Hata: $ret[0]");
            $error[] = ("Authentication failed. $ret[0] ");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $send[1],
        );
    }

    function balance(){
        return null;
    }

    function report($msgid){
        return null;
    }
}

return array(
    'value' => 'clickatell',
    'label' => 'ClickAtell',
    'fields' => array(
        'user','pass','apiid'
    )
);
