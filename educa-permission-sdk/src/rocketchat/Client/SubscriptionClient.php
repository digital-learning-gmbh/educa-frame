<?php

namespace StuPla\CloudSDK\rocketchat\Client;

use Exception;
use Httpful\Exception\ConnectionErrorException;
use StuPla\CloudSDK\rocketchat\Exceptions\ImActionException;
use StuPla\CloudSDK\rocketchat\Models\Room;

class SubscriptionClient extends Client
{
    const API_PATH_SUBSCRIPTIONS_GET          = 'subscriptions.get';

    public function get()
    {
        $response = $this->request()->get($this->apiUrl(self::API_PATH_SUBSCRIPTIONS_GET))->send();

        return $this->handleResponse($response, new ImActionException(), ['update']);
    }

}