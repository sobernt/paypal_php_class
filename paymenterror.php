<?php

require 'classes\PayPal.php'; 
use classes\PayPal as PayPal;
$PayPal=new PayPal;

if( isset($_GET['token']) && !empty($_GET['token']) )
{
$response = $PayPal::transactionInfo($_GET['token']);
    
    if( is_array($response)) { 
     $payer = json_decode($response['CUSTOM']);
     print("<br>");
     print($response['PAYMENTREQUESTINFO_0_SHORTMESSAGE']);print("<br>");
     print($response['PAYMENTREQUESTINFO_0_LONGMESSAGE']);print("<br>");
     print "payment for user id:".$payer->user_id." & order #".$payer->order;
   }
}


?>