<?php

namespace App\Jobs;

use Gumlet\ImageResize;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompressImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;
    private $resolution;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $maxResolution = 710)
    {
        $this->file = $file;
        $this->resolution = $maxResolution;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Gumlet\ImageResizeException
     */
    public function handle()
    {
        $image = new ImageResize($this->file);
        $image->resizeToLongSide($this->resolution);
        $image->save($this->file);
    }
}
