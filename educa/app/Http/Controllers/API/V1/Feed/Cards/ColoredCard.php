<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Http\Controllers\API\V1\Feed\Card;

class ColoredCard extends Card
{
    private $color = "green";
    private $emoji = "";
    private $bodySize = "MEDIUM";
    private $headingSize = "MEDIUM";
    private $headline = "";
    private $content = "";

    function __construct($headline, $content) {
        $this->type = "coloredCard";
        $this->content = $content;
        $this->headline = $headline;
        $this->payload = $this->createPayload();
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getEmoji(): string
    {
        return $this->emoji;
    }

    /**
     * @param string $emoji
     */
    public function setEmoji(string $emoji): void
    {
        $this->emoji = $emoji;
    }

    /**
     * @return string
     */
    public function getBodySize(): string
    {
        return $this->bodySize;
    }

    /**
     * @param string $bodySize
     */
    public function setBodySize(string $bodySize): void
    {
        $this->bodySize = $bodySize;
    }

    /**
     * @return string
     */
    public function getHeadingSize(): string
    {
        return $this->headingSize;
    }

    /**
     * @param string $headingSize
     */
    public function setHeadingSize(string $headingSize): void
    {
        $this->headingSize = $headingSize;
    }

    /**
     * @return string
     */
    public function getHeadline(): string
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     */
    public function setHeadline(string $headline): void
    {
        $this->headline = $headline;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }



    function createPayload() {
        return [ "headingText" => $this->headline, "bodyText" => $this->content, "color" => $this->color, "emoji" => $this->emoji, "bodySize" => $this->bodySize, "headingSize" => $this->headingSize ];
    }

}
