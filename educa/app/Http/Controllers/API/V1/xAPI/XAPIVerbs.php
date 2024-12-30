<?php

namespace App\Http\Controllers\API\V1\xAPI;

class XAPIVerbs
{
    public static $CREATED = [
        'id' => 'http://activitystrea.ms/schema/1.0/create',
        'display' =>  [
            'en-US' => 'created'
        ]
    ];

    public static $UPDATED = [
        'id' => 'http://activitystrea.ms/schema/1.0/updated',
        'display' =>  [
            'en-US' => 'updated'
        ]
    ];

    public static $LIKE = [
        'id' => 'http://activitystrea.ms/schema/1.0/like',
        'display' =>  [
            'en-US' => 'liked'
        ]
    ];

    public static $DISLIKE = [
        'id' => 'http://activitystrea.ms/schema/1.0/dislike',
        'display' =>  [
            'en-US' => 'disliked'
        ]
    ];

    public static $COMMENT = [
        'id' => 'http://adlnet.gov/expapi/verbs/commented',
        'display' =>  [
            'en-US' => 'commented'
        ]
    ];

    public static $ADD = [
        'id' => 'http://activitystrea.ms/schema/1.0/add',
        'display' =>  [
            'en-US' => 'added'
        ]
    ];

    public static $ACCESS = [
        'id' => 'http://activitystrea.ms/schema/1.0/access',
        'display' =>  [
            'en-US' => 'accessed'
        ]
    ];
}
