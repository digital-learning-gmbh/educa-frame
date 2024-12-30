<?php

namespace StuPla\CloudSDK\formular\controller;

use DigitalesBlatt;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use StuPla\CloudSDK\formular\models\Formular;
use StuPla\CloudSDK\formular\models\FormularRevision;

/**
 * Class to manage a single sheet
 * Class EditController
 * @package AppStuplaPatched\Http\Controllers\Apps\DigitalesBlatt
 */
class EditController extends Controller
{

    /**
     * Delete a sheet and all the executions of it
     */
    public function delete(Request $request, $id)
    {
        $blatt = DigitalesBlatt::findOrFail($id);
        if($blatt->user_id != Auth::user()->id)
            abort(404);
        $blatt->delete();
        return redirect("/formulare")->with('status', 'Arbeitsblatt wurde in den Papierkorb gelegt.');
    }

    public function restore(Request $request, $id)
    {
        $blatt = Formular::withTrashed()->findOrFail($id);
        if($blatt->user_id != Auth::user()->id)
            abort(404);
        $blatt->restore();
        return redirect("/formulare")->with('status', 'Arbeitsblatt wurde wiederhergestellt.');
    }
    /**
     * Saves a sheet, called after the edit form is submitted
     */
    public static function save(Request $request, $id)
    {
        $blatt = Formular::findOrFail($id);

        $lastRevision = $blatt->lastRevision;
        if($lastRevision->data != $request->input('data'))
        {
            $lastRevisionNew = new FormularRevision();
            $lastRevisionNew->data = $request->input('data');
            $lastRevisionNew->formular_id = $blatt->id;
            $lastRevisionNew->number = $lastRevision->number + 1;
            $lastRevisionNew->user_id = Auth::user()->id;
            $lastRevisionNew->save();
        }

        $blatt->save();
    }

    /**
     * Presents the edit view of a sheet
     */
    public function edit($id)
    {
        $blatt = DigitalesBlatt::findOrFail($id);
        if($blatt->user_id != Auth::user()->id)
            abort(404);
        $lastRevision = $blatt->lastRevision;

        return parent::displayUserView('app.apps.digitalesblatt.blatt.edit',["lastRevision" => $lastRevision, "blatt" => $blatt]);
    }


    /**
     * Saves the video file
     */
    public function upload(Request $request)
    {
        if(!$request->hasFile('file'))
        {
            return response()->json([
                'status' => 0,
                'message' => 'No file'
            ]);
        }
        $file = $request->file('file');
        $path = $file->storeAs('formulare', $request->input('id'));

        return response()->json([
            'status' => 1
        ]);
    }

    public function getFile(Request $request)
    {
        $fileName = $request->input('video', $request->input("image"));
        $fileName = str_replace("-preview", "", $fileName);
        $pfad = 'formulare/' . $fileName;
        try {
            return response()->file(Storage::getDriver()->getAdapter()->applyPathPrefix($pfad));
        } catch (\Exception $exception)
        {
            return "File not found!";
        }
    }

}
