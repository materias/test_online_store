<?php 
require dirname(__DIR__) . '/config.php';

class PaypalCheckout{ 
    public $paypalAuthAPI   = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
	public $paypalAPI    = 'https://api-m.sandbox.paypal.com/v2/checkout';
    public $paypalClientID;
    private $paypalSecret;

    public function __construct() {
        $this->paypalClientID = $_ENV['CLIENT_ID'] ?? null;
        $this->paypalSecret = $_ENV['CLIENT_SECRET'] ?? null;
    }    
    
    public function validate($order_id) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->paypalAuthAPI); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        curl_setopt($ch, CURLOPT_USERPWD, $this->paypalClientID.":".$this->paypalSecret); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
        $auth_response = json_decode(curl_exec($ch));
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 

        if ($http_code != 200 && !empty($auth_response->error)) { 
            throw new Exception('Error '.$auth_response->error.': '.$auth_response->error_description); 
        }
        
        if (empty($auth_response)) {
            return false; 
        } else {
            if(!empty($auth_response->access_token)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->paypalAPI.'/orders/'.$order_id);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '. $auth_response->access_token)); 
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $api_data = json_decode(curl_exec($ch), true);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
                curl_close($ch);

                if ($http_code != 200 && !empty($api_data['error'])) { 
                    throw new Exception('Error '.$api_data['error'].': '.$api_data['error_description']); 
                }

                return !empty($api_data) && $http_code == 200 ? $api_data : false;
            } else {
                return false;
            }
        }
    }
}