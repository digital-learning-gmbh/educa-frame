<?php


namespace App\Http\Controllers\API\V1\Feed;


class CardFactory
{
    private $cardList = [];

    public function build()
    {
        foreach ($this->cardList as $card)
        {
            $card->generatePayload();
        }
        return $this->cardList;
    }

    public function addCard(Card $card)
    {
        $this->cardList[] = $card;
    }
}
