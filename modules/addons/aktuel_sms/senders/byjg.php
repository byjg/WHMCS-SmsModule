<?php

class byjg extends AktuelSms {
    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
    }

    function send(){
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }
        $params = $this->getParams();

		$number = preg_replace('/[^0-9]/', '', $this->gsmnumber);
		$number = preg_replace('/^55/', '', $number);
		$ddd = substr($number, 0, 2);
		$celular = substr($number, 2);

        $url = "http://www.byjg.com.br/ws/sms?httpmethod=enviarsms&usuario=$params->user&senha=$params->pass&ddd=$ddd&celular=$celular&mensagem=".urlencode($this->message)."";
        $log[] = "Request url: ".$url;
        $result = file_get_contents($url);

        $return = $result;
        $log[] = "Retorno: ".$result;

        $result = explode("|", $result);
        if ($result[0] == "ERR") {
            $log[] = "Mensagem não enviada";
            $error[] = "Mensagem não enviada: $return";
        }elseif($result[0] == "OK"){
			$dados = explode(",", $result[1]);
			if ($dados[0] == "0")
			{
	            $log[] = "Mensagem enviada";
			}
			else
			{
	            $log[] = "Mensagem não enviada";
				$error[] = "Mensagem não enviada: $return";				
			}
		}

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => '1111111',
        );
    }

    function balance(){
        $params = $this->getParams();
        $url = "http://www.byjg.com.br/ws/sms?httpmethod=creditos&usuario=$params->user&senha=$params->pass";
        $log[] = "Request url: ".$url;
        $result = file_get_contents($url);
		return $result;
    }

    function report($msgid){
        return null;
    }

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'byjg',
    'label' => 'ByJG Web Service (Brasil)',
    'fields' => array(
        'user','pass'
    )
);