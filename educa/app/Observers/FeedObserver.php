<?php


namespace App\Observers;


use App\FeedActivity;
use App\Section;

class FeedObserver
{
    public static $shouldFakeData = false;

    public static  function addSectionActivity($belong, $creator, $creator_model, $type, $merge_id, $payload, $created_at = null)
    {
        $section = Section::find($belong);
        static::addFeedActivity($section->group->id,"group",$creator,$creator_model, $type, $merge_id, $payload, $created_at);
    }

    public static  function addGroupActivity($belong, $creator, $creator_model, $type, $merge_id, $payload, $created_at = null)
    {
        static::addFeedActivity($belong,"group",$creator,$creator_model, $type, $merge_id, $payload, $created_at);
    }

    public static function addUserAcitivty($belong, $creator, $creator_model, $type, $merge_id, $payload, $created_at = null)
    {
        static ::addFeedActivity($belong,"user",$creator,$creator_model, $type, $merge_id, $payload, $created_at);
    }

    public static function addFeedActivity($belongTo, $belongType, $creator, $creator_model, $type, $merge_id, $payload, $created_at = null)
    {
        if($creator != null)
        {
            $creator = $creator->id;
        } else {
            $creator_model = "none";
        }

        $feedActivity = new FeedActivity();
        $feedActivity->belong_id = $belongTo;
        $feedActivity->belong_type = $belongType;
        $feedActivity->creator = $creator;
        $feedActivity->creator_model = $creator_model;
        $feedActivity->type = $type;
        $feedActivity->merge_id = $merge_id;
        $feedActivity->payload = utf8_decode(json_encode($payload));

        $feedActivity->save();

        if(static::$shouldFakeData)
        {
            $faker = \Faker\Factory::create('de_DE');
            $feedActivity->created_at = $faker->dateTimeBetween('-1 year', 'now');
            $feedActivity->save();
        }

        if($created_at != null)
        {
            $feedActivity->created_at = $created_at;
            $feedActivity->save();
        }
    }


}
