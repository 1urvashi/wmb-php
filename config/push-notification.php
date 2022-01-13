<?php

return array(

     'appNameIOSProd'     => array(
        'environment' =>'production',
        'certificate' =>storage_path() .'/apns-cert.pem',
        'passPhrase'  =>'12345',
        'service'     =>'apns'
    ),
	 'appNameIOSDev'     => array(
        'environment' =>'development',
        'certificate' =>storage_path() .'/ck.pem',
		// 'certificate' =>storage_path() .DIRECTORY_SEPARATOR.'ck.pem',
        'passPhrase'  =>'12345',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'development',
        'apiKey'      =>'AIzaSyD1mF494nviLsjvBbZQtYByufvxsXAccLU',
        'service'     =>'gcm'
    )

);