<?php

return [
    'url' => '',       // The API URL of the Opencast instance. (required)
    'username' => '',                          // The API username. (required)
    'password' => '',                       // The API password. (required)
    'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
    'connect_timeout' => 0,                         // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
    'version' => null,                              // The API Version. (Default null). (optional)
    'handler' => null                               // The callable Handler or HandlerStack. (Default null). (optional)
];
