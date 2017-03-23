<?php
require 'classes\PayPal.php'; 
use classes\PayPal as PayPal;


$PayPal=new PayPal;

$result = PayPal::getRequestCode('RUB',$_POST['summ'],$_POST['user_id'],$_POST['order_id']);
if(!$PayPal::$errors){
        header( 'Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='. urlencode($result['TOKEN']));
} else{
    print('error:!<br>error_log_id='.$PayPal::$error_id);
}
?>