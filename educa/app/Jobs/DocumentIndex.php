<?php

namespace App\Jobs;

use App\Console\Commands\DocumentContentIndexer;
use App\Dokument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DocumentIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $dokument;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dokument $dokument)
    {
        $this->dokument = $dokument;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DocumentContentIndexer::handleSingleDocument($this->dokument);
    }
}
