<?php
require_once 'vendor/autoload.php';

{
   
   $transport = (new Swift_SmtpTransport('smtp.gmail.com, 465, 'ssl'))
       -> setUsername('way2society.com')
       -> setPassword('Way2S123')
   ;


$mailer = new Swift_Mailer($transport);

$body = 'Hello, <p> Email sent through;" Swift Mailer </p>;

$message = (new Swift_Message('Email Through Swift Mailer'))
	-> setFrom(['no-reply@way2society.com' => 'way2society.com'])
        -> setTo(['Madhura madhurprsh@gmail.com'])
	-> setBody($body)
	-> setContentType('text/html')
;


   $mailer->send($message);


echo 'Email has been sent.' ;
catch(Exception $e) {
echo $e-> getMessage(); 
