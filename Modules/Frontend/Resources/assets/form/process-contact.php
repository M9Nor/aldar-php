<?php

    $to = "";  // Your email here
    $from = $_REQUEST['email'];
    $name = $_REQUEST['firstname'];
    $name = $_REQUEST['lastname'];
	  $message = $_REQUEST['message'];
    $headers = "From: $from";
	  $subject = "Contact Form BuildPower Site";
   
    $fields = array();
    $fields{"firstname"} = "First name";
    $fields{"lastname"} = "Last name";
    $fields{"email"} = "Email";
    $fields{"message"} = "Message";
	

    $body = "Here is what was sent:\n\n"; foreach($fields as $a => $b){   $body .= sprintf("%20s: %s\n\n",$b,$_REQUEST[$a]); }

    $send = mail($to, $subject, $body, $headers);

?>
