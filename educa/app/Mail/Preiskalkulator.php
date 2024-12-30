<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Preiskalkulator extends Mailable
{
    use Queueable, SerializesModels;

    private $firstKalkulator;
    private $secondKalkulator;
    private $thirdKalkulator;
    private $fourthKalkulator;
    private $fiveKalkulator;
    private $calculatedPrice;
    private $data;
    private $request;
    private $validatedData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request, $calculatedPrice, $firstKalkulator, $secondKalkulator, $thirdKalkulator, $fourthKalkulator, $fiveKalkulator, $validatedData)
    {
        $this->request = $request;
        $this->calculatedPrice = $calculatedPrice;
        $this->firstKalkulator = $firstKalkulator;
        $this->secondKalkulator = $secondKalkulator;
        $this->thirdKalkulator = $thirdKalkulator;
        $this->fourthKalkulator = $fourthKalkulator;
        $this->fiveKalkulator = $fiveKalkulator;
        $this->validatedData = $validatedData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.preiskalkulator',["validatedData" => $this->validatedData, "data" => $this->request, "calculatedPrice" => $this->calculatedPrice, "firstKalkulator" => $this->firstKalkulator, "secondKalkulator" => $this->secondKalkulator, "thirdKalkulator" => $this->thirdKalkulator, "fourthKalkulator" => $this->fourthKalkulator, "fiveKalkulator" => $this->fiveKalkulator]);
    }
}
