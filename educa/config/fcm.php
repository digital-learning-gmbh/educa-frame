<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAYFZK4RM:APA91bGmhKXuK5E8z684apvER6SRhcGjCqD9tzmyBFZxrC8uHC6YItTBHD4Zc93bZvu1F4dheSrCvw-erttWNO3kzqkUkyhzqnFgZjl8Rx5Xe3KdJ3K2aosjFwJZ_nR-wsMAVeMKU5qE'),
        'sender_id' => env('FCM_SENDER_ID', '413764608275'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
