<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenceCluster extends Model
{
    use HasFactory;

    public function competences()
    {
        return $this->hasMany(Competence::class);
    }

    public function delete()
    {
        foreach ($this->competences as $competence)
        {
            $competence->delete();
        }
        return parent::delete();
    }
}
