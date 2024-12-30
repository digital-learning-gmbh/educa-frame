<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    use HasFactory;

    public function learnContents()
    {
        return $this->belongsToMany(LearnContent::class);
    }

    public function cluster()
    {
        return $this->belongsTo(CompetenceCluster::class, "competence_cluster_id");
    }
}
