<?php

namespace App\Jobs\EducaAI;

use App\Models\User;
use DigitalLearningGmbh\EducaAiConnector\EducaAIAPIFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveDocumentFromIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $dokument;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $dokument)
    {
        $this->user = $user;
        $this->dokument = $dokument;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user = User::find($this->user->id);
        $aiFactory = new EducaAIAPIFactory(config("educa-ai.backend"));
        $vectorAPI = $aiFactory->getVectorAPI();

        $index = $this->user->index_name;
        if($index == null)
            return;

        foreach ($this->dokument->documentParts as $part) {
            $vectorAPI->removeDocument($index,$part->vectorId);
        }
    }
}
