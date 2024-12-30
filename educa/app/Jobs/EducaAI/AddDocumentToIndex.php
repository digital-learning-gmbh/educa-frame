<?php

namespace App\Jobs\EducaAI;

use App\Models\DokumentParts;
use DigitalLearningGmbh\EducaAiConnector\Document\DocumentExtractor;
use DigitalLearningGmbh\EducaAiConnector\EducaAIAPIFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AddDocumentToIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model_with_vector;
    private $dokument;

    /**
     * Create a new job instance.
     */
    public function __construct($model_with_vector, $dokument)
    {
        $this->model_with_vector = $model_with_vector;
        $this->dokument = $dokument;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $aiFactory = new EducaAIAPIFactory(config("educa-ai.backend"));
        $vectorAPI = $aiFactory->getVectorAPI();

        $index = $this->model_with_vector->vector_index;
        if ($index == null)
        {
            $this->model_with_vector->vector_index = $this->model_with_vector->generateIndexName();
            $this->model_with_vector->save();
        }
        if($this->dokument->metadata == null ||
            !array_key_exists("extracted_content",$this->dokument->metadata))
        {
            DocumentExtractor::extractDocumentContent($this->dokument);
        }
        if(!array_key_exists("extracted_content",$this->dokument->metadata))
            return;

        $extracted_content = $this->dokument->metadata["extracted_content"];

        //print_r($extracted_content);
        if(array_key_exists("pages",$extracted_content)) {
        foreach ($extracted_content["pages"] as $page) {
          //  print_r("add to index " . $index . PHP_EOL);
            $document_id = $vectorAPI->addDocument($page["extracted_text"], $index, false);#

          //  print_r($document_id);
            $documentPart = new DokumentParts();
            $documentPart->dokument_id = $this->dokument->id;
            $documentPart->vectorId = $document_id["document_id"];
            $documentPart->content = $page["extracted_text"];
            $documentPart->save();

        }
    }
    }
}
