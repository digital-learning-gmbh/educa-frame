<?php

namespace App\Http\Controllers\API\V1\Administration\Curricula;

use App\Fach;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Schule;
use App\Studium;
use Illuminate\Http\Request;

class SubjectController extends AdministationApiController
{
    public function list(Request $request)
    {
        $school_id = $request->input("school_id");
        $school =  Schule::find($school_id);
        // TODO filter den standort
        return parent::createJsonResponse("subjects ",false, 200, [ "subjects" => Fach::with('studies')->get() ]);
    }

    public function add(Request $request)
    {
        if(Fach::where('lecture_number','=', $request->input("lecture_number"))->exists())
            return $this->createJsonResponse("subject already exists.", true, 403);

        $subject = new Fach;
        $subject->lecture_number = $request->input("lecture_number");
        $subject->save();
        $subject->studies()->sync( $request->input("study_ids", []) );

        return $this->details($subject->id, $request);
    }

    public function attachSubjectsToStudy($study_id, Request $request)
    {
        $study = Studium::find($study_id);
        if($study == null)
            return $this->createJsonResponse("Study not found.", true, 403);

        $study->subjects()->syncWithoutDetaching($request->input("subject_ids"));
        return $this->createJsonResponse("Subjects attached to study.", false, 200, ["study" => $study->with("subjects")]);
    }

    public function details($subject_id, Request $request)
    {
        $subject = Fach::find($subject_id);
        if($subject == null)
            return $this->createJsonResponse("subject not found.", true, 404);

        $subject->load("studies");
        return parent::createJsonResponse("subject details",false, 200, [ "subject" => $subject]);
    }

    public function update($subject_id, Request $request)
    {
        $subject = Fach::find($subject_id);
        if($subject == null)
            return $this->createJsonResponse("subject not found.", true, 404);

        $subjectObjc = $request->input("object");
        $subject->name = $subjectObjc["name"];
        $subject->lecture_number = $subjectObjc["lecture_number"];
        $subject->abk = $subjectObjc["lecture_number"];
        $subject->color = $subjectObjc["color"];
        $subject->duration = $subjectObjc["duration"];
        $subject->ects = $subjectObjc["ects"];
        $subject->duration_complete = $subjectObjc["duration_complete"];
        $subject->beschreibung = $subjectObjc["beschreibung"];
        $subject->features = $subjectObjc["features"];
        $subject->content = $subjectObjc["content"];
        $subject->studies()->sync( $subjectObjc["study_ids"] );

        $subject->save();

        $subject->load("studies");

        return $this->details($subject->id, $request);
    }

    public function archive($subject_id, Request $request)
    {
        $subject = Fach::find($subject_id);
        if($subject == null)
            return $this->createJsonResponse("subject not found.", true, 404);

        $subject->archived = true;

        $subject->save();
        return parent::createJsonResponse("subject archived",false, 200, [ "subject" => $subject]);
    }
}
