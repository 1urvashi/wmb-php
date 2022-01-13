<?php

return [
  'gcm' => [
      'priority' => 'high',
      'dry_run' => false,
      'apiKey' => 'AIzaSyD1mF494nviLsjvBbZQtYByufvxsXAccLU',
  ],
  'fcm' => [
        'priority' => 'high',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
  ],
  'apn' => [
      'certificate' => storage_path() .'/apns-cert.pem',
      'passPhrase' => '12345', //Optional
      'passFile' => storage_path() .'/apns-cert.pem', //Optional
      'dry_run' => false
  ]
];