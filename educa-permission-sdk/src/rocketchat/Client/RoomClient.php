<?php

namespace StuPla\CloudSDK\rocketchat\Client;

use Exception;
use Httpful\Exception\ConnectionErrorException;
use StuPla\CloudSDK\rocketchat\Exceptions\ChannelActionException;
use StuPla\CloudSDK\rocketchat\Models\Integration;
use StuPla\CloudSDK\rocketchat\Exceptions\ChatActionException;
use StuPla\CloudSDK\rocketchat\Exceptions\IntegrationActionException;

class RoomClient extends Client
{
    const API_PATH_ROOM_INFO       = 'rooms.info';
    const API_PATH_ROOM_GET       = 'rooms.get';


    public function info($roomId)
    {
        $response = $this->request()->get($this->apiUrl(self::API_PATH_ROOM_INFO,['roomId' => $roomId]))
            ->send();

        return $this->handleResponse($response, new ChannelActionException(),"room");
    }


    public function get()
    {
        $response = $this->request()->get($this->apiUrl(self::API_PATH_ROOM_GET))
            ->send();

        return $this->handleResponse($response, new ChannelActionException(),"update");
    }
}