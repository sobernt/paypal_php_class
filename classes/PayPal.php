<?php
namespace classes;
/* 
 * Class to work with paypal.
 */

class PayPal{

    protected static $settings = ['USER' => '',//paste USER here
    'PWD' => 'VCBPLHEW9XCWY7DM',//paste PWD here
    'SIGNATURE' => '',//paste SIGNATURE here
     'VERSION' => '115.0'
     ];
    protected static $params = [
        'RETURNURL'=> 'http://paytest.loc/paymentsuccess.php',
        'CANCELURL' => 'http://paytest.loc/paymenterror.php',
        
    ];
    
    const APIurl = 'https://api-3t.sandbox.paypal.com/nvp';
    
    public static $errors = [];
    public static $error_id='';
    
     /*
     * $currencyCode - e.g 'USD'
      * $amount - summ without shipping cost e.g 20.00
      * $user_id - id of user e.g 123
      * $order_id - id order e.g 1231
      * $deleveryCost - shipping cost e.g 10.00
     * return request data from PP API
     */
    public static function getRequestCode($currencyCode,$amount,$user_id,$order_id,$deleveryCost = 0){
        $data=[];
        $data['METHOD'] = 'SetExpressCheckout';
        $data['PAYMENTREQUEST_0_AMT'] = $amount;
        $data['PAYMENTREQUEST_0_ITEMAMT'] = $amount+$deleveryCost;
        $data['SOLUTIONTYPE'] = 'Sole';
        $data['BRANDNAME'] = 'Leader-IT';//MY COMPANY NAME
        
        $data['PAYMENTREQUEST_0_CUSTOM']=json_encode(array("order"=> $order_id,"user_id"=> $user_id));
        $data['PAYMENTREQUEST_0_SELLERID'] = $order_id;
        
        $data['PAYMENTREQUEST_0_CURRENCYCODE'] = $currencyCode;
        $data['PAYMENTREQUEST_0_SHIPPINGAMT'] = $deleveryCost;
        
        $data+=self::$params;
        $result = self::PostQuery($data);
        if(is_array($result) && ($result['ACK'] == 'Success' || $result['ACK'] == 'SuccessWithWarning')) { 
          return $result;
        }else {
            self::$errors[] = $result['L_LONGMESSAGE0'];
            self::log(implode("\n", self::$errors),"query_error");
            return false;
       }
    }
    
     /*
      * $token - payment token from 
     * return request data from PP API with payment info
     */
    public static function transactionInfo($token){
        $data=[];
        $data['METHOD'] = 'GetExpressCheckoutDetails';
        $data['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
        $data['TOKEN'] = $token;
        
        $result = self::PostQuery($data);
         if(is_array($result) && ($result['ACK'] == 'Success' || $result['ACK'] == 'SuccessWithWarning')) { 
          return $result;
        }else {
            self::$errors[] = $result['L_LONGMESSAGE0'];
            self::log(implode("\n", self::$errors),"query_error");
            return false;
       }
       
    }
    
    
    /*
     * $data - array of transaction info 
     * array has keys ['TOKEN','PAYERID','PAYMENTREQUEST_0_AMT','PAYMENTREQUEST_0_CURRENCYCODE']
     * return request data from PP API with payment info
     */
    
    public static function transactionComplite($data){
        $data['METHOD'] = 'DoExpressCheckoutPayment';
        $result = self::PostQuery($data);
        if(is_array($result) && ($result['ACK'] == 'Success' || $result['ACK'] == 'SuccessWithWarning')) { 
          return $result;
        }else {
            self::$errors[] = $result['L_LONGMESSAGE0'];
            self::log(implode("\n", self::$errors),"query_error");
            return false;
       }
    }
    
    
    /*
     * select $keys from $array
     */
    public static function selectKeys($array,$keys) {
        return array_intersect_key($array, array_flip($keys));
    }
    /*
     * set $data array to $url via post data
     * return aray data
     */
    private static function PostQuery($data =[]){
         
         $data +=  self::$settings;
         $st = curl_init(); 
         curl_setopt($st,CURLOPT_URL,self::APIurl); 
         curl_setopt($st, CURLOPT_SSL_VERIFYPEER , TRUE );
         curl_setopt($st,CURLOPT_VERBOSE,TRUE); 
         curl_setopt($st, CURLOPT_SSLVERSION,CURL_SSLVERSION_TLSv1); 
         curl_setopt($st,CURLOPT_RETURNTRANSFER,TRUE); 
         //curl_setopt($st, CURLOPT_HEADER, true);
         //curl_setopt($st, CURLOPT_HTTPHEADER , array("Authorization: Bearer ".$settings['PWD']));
         curl_setopt( $st, CURLOPT_POST, TRUE );
         curl_setopt( $st, CURLOPT_POSTFIELDS, http_build_query($data ));
         
        $ouput = curl_exec($st);
        if (curl_errno($st)) {
         self::$errors[] = curl_error($st);
         self::log(implode("\n", self::$errors),"curl_error");
         return false;
      }
        self::log($ouput);
        curl_close($st);
        parse_str($ouput,$result);
        return $result;
    }
    
    /*
     * log $data string to logs/paypal_$prefix_time()_[1111-9999].log
     */
    private function log($data,$prefix = ''){
        self::$error_id=time()."_".rand ( 1111 , 9999 );
        @file_put_contents("logs/paypal_".$prefix."_".self::$error_id.".log", $data);
    }
    
    /*
     * log $datakeys from $data[]  to result/$identifer.log
     */
    public static function saveResult($identifer,$data,$datakeys){
        $extarr = self::selectKeys($data,$datakeys);
        @file_put_contents("result/$identifer.log", json_encode($extarr));
        
    }
}
?>
