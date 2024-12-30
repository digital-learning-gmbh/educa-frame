<?php

namespace StuPla\CloudSDK\formular\controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
/**
 * This class is called, when a user wants to fill out the sheet
 * Class ViewerController
 * @package AppStuplaPatched\Http\Controllers\Apps\DigitalesBlatt
 */
class ViewerController extends Controller
{

    public static function generateDisplayFromRevision($id, $lastRevision, $formular_id, $values = [], $readonly = false)
    {
        $items= json_decode($lastRevision->data);
        $data =array();
        if (empty($items)) {//If Form ist empty
            return $data;
        }
        //open form
        array_push($data, Form::open(['id' => $formular_id, 'method' => 'POST', 'enctype' => 'multipart/form-data','files'=>true]));

        foreach ($items as $item){
            array_push($data,'<div class="form-group">');
            $options = array();//input option
            if($readonly)
            {
                $options = ['readonly'=> true];//input option
            }
            //header
            //if(($item->type === 'text')||($item->type === 'textarea')||($item->type === 'number')||($item->type === 'text')){
            if(($item->type !== 'header')&&($item->type !== 'paragraph'))
            {
                $loptions = array();//label options
                // add to label options and options required and help text
                if (property_exists($item, 'required')) {
                    $loptions['class'] = 'required';
                    $options['required'] = 'required';
                }
                if (property_exists($item, 'description')) {
                    $loptions['data-toggle'] = 'tooltip';
                    $loptions['data-placement'] = 'top';
                    $loptions['title'] = $item->description;
                    //add label
                    array_push($data, HTML::decode(Form::label($item->name, $item->label . '<span class="tooltip-element"></span>', $loptions)));
                } else {
                    //add label without help text
                    array_push($data, HTML::decode(Form::label($item->name, $item->label, $loptions)));
                }
            }
            if(($item->type === 'header'))
            {
                array_push($data,HTML::decode(Form::label($item->type,"<$item->subtype>".$item->label."</$item->subtype>")));
            }
            //paragraph
            elseif(($item->type === 'paragraph'))
            {
                //array_push($data,HTML::decode(Form::label($item->type,"<$item->subtype>".$item->label."</$item->subtype>")));
                array_push($data,"<$item->subtype>".$item->label."</$item->subtype>");
            }
            //text and textarea
            elseif(($item->type === 'text')||($item->type === 'textarea'))
            {
                $options['class'] = $item->className;//textbox option

                if(property_exists($item,'placeholder')){
                    $options['placeholder'] = $item->placeholder;
                }
                if(property_exists($item,'maxlength')){
                    $options['maxlength'] = $item->maxlength;
                }
                // $options = array('class' => $item->className, 'placeholder' => $item->placeholder,'maxlength' => $item->maxlength);//textbox option


                if($item->type === 'text') {
                    if ($item->subtype === 'email') {
                        array_push($data, Form::email($item->name, ViewerController::getAnswer($item->name, $values), $options));
                    } elseif ($item->subtype === 'password') {//
                        $options['value']=null;
                        array_push($data, Form::password($item->name, $options));
                    } elseif ($item->subtype === 'tel') {
                        //
                    } elseif ($item->subtype === 'color') {
                        //
                    } else {//text
                        array_push($data, Form::text($item->name, ViewerController::getAnswer($item->name, $values), $options));
                    }
                }
                elseif($item->type === 'textarea') {
                    //add num of rows for textarea to options
                    if(property_exists($item,'rows'))
                        $options['rows'] = $item->rows;
                    else
                        $options['rows'] = '3';//default value
                    if($item->subtype ==='tinymce')
                        $options['id']='ckeditor';
                    array_push($data, Form::textarea($item->name, ViewerController::getAnswer($item->name, $values), $options));
                }

            }
            //number input
            elseif(($item->type === 'number'))
            {

                $options['class'] = $item->className;//textbox option
                if(property_exists($item,'placeholder')){
                    $options['placeholder'] = $item->placeholder;
                }
                if(property_exists($item,'min')){
                    $options['min'] = $item->min;
                }
                if(property_exists($item,'max')){
                    $options['max'] = $item->max;
                }
                if(property_exists($item,'step')){
                    $options['step'] = $item->step;
                }
                $options['style']= 'width:300px';

                array_push($data, Form::number($item->name, ViewerController::getAnswer($item->name, $values), $options));
            }
            //checkbox
            elseif(($item->type === 'checkbox-group'))
            {
                array_push($data,'<br>');

                //add checkboxes
                foreach ($item->values as $v){
                    array_push($data, Form::checkbox($item->name.'/'.$v->value, $v->value, ViewerController::getAnswer($item->name, $values, $v->value), $options));//['class' => $item->className]
                    array_push($data,Form::label($item->name, $v->label));
                    if(!property_exists($item,'inline'))
                        array_push($data, '<br>');
                    elseif (property_exists($item,'inline')&& $item->inline !== true)
                        array_push($data, '<br>');

                    if(property_exists($item,'toggle')) {
                        //todo
                    }
                }
                if(property_exists($item,'other')) {
                    if($item->other ===true){
                        array_push($data, Form::checkbox($item->name.'/'.'other', 'other', ViewerController::getAnswer($item->name, $values, "other"), $options));//,['class' => $item->className]
                        array_push($data,Form::label($item->name, 'Sonstige'));
                        array_push($data, Form::text($item->name.'/'.'othertext', ViewerController::getAnswer($item->name, $values, "othertext"),['style'=>'width:300px']));//'class' => 'form-control',
                    }
                }
            }
            //radiogroup
            elseif(($item->type === 'radio-group'))
            {
                array_push($data,'<br>');
                //add radio button
                foreach ($item->values as $v){
                    array_push($data, Form::radio($item->name, $v->value, ViewerController::getAnswer($item->name, $values) == $v->value,$options));
                    array_push($data,HTML::decode(Form::label($item->name, $v->label)));
                    if(!property_exists($item,'inline'))
                        array_push($data, '<br>');
                    elseif (property_exists($item,'inline')&& $item->inline !== true)
                        array_push($data, '<br>');

                    if(property_exists($item,'toggle')) {
                        //todo
                    }
                }
                if(property_exists($item,'other')) {
                    if($item->other ===true){
                        array_push($data, Form::radio($item->name, 'other', ViewerController::getAnswer($item->name, $values) == "other",$options));
                        array_push($data,Form::label($item->name, 'Sonstige'));
                        array_push($data, Form::text($item->name.'/'.'other', ViewerController::getAnswer($item->name, $values, 'other'),['style'=>'width:300px']));//'class' => 'form-control',
                    }
                }
            }
            //selectgroup
            elseif(($item->type === 'select'))
            {
                array_push($data,'<br>');
                //create selection array
                $selection =array();
                foreach ($item->values as $v){
                    $selection[$v->value]= $v->label;
                }
                $options['class'] = $item->className. " select2";
                if(property_exists($item,'placeholder')){
                    $options['placeholder'] = $item->placeholder;
                }
                if(property_exists($item,'multiple')){
                    $options['multiple'] = 'true';
                }
                if($values != [] && $readonly)
                {
                    $options['disabled']='true';
                }
                //add select menu
                array_push($data, Form::select($item->name, $selection, ViewerController::getAnswer($item->name, $values), $options));
            }
            //date input
            elseif(($item->type === 'date'))
            {
                $options['class'] = $item->className;//textbox option
                if(property_exists($item,'placeholder')){
                    $options['placeholder'] = $item->placeholder;
                }
                $options['style']='width:300px';
                array_push($data, Form::date($item->name,ViewerController::getAnswer($item->name, $values), $options));
            }
            //image
            elseif(($item->type === 'image'))
            {
                array_push($data,'<div id='."$item->name".'>');
                if(property_exists($item,'name'))
                    array_push($data, '<img class="img-responsive" src="/formulare/'.$id.'/getFile?video='.$item->name.'"/>');

                array_push($data,'</div>');
            }
            //video
            elseif(($item->type === 'video'))
            {
                array_push($data,'<div id='."$item->name".'><video controls="" width="100%">');
                if(property_exists($item,'name'))
                    array_push($data, '<source src="/formulare/'.$id.'/getFile?video='.$item->name.'" type="video/mp4">');
                else
                    array_push($data, '<source src="dsfsdf" type="video/mp4">');
                array_push($data,'</video></div>');
            }
            elseif(($item->type === 'starRating'))
            {
                $tmp = ViewerController::getAnswer($item->name, $values);
                $tmpvalue =  ($tmp == null) ? 0 :$tmp;
                array_push($data,HTML::decode('<div class="starRating" id='."$item->name".' name='."$item->name".' data-rateyo-rating='."$tmpvalue".' >bla</div>'));

            }
            elseif(($item->type === 'signature')) {
                $tmp = ViewerController::getAnswer($item->name, $values);
                if (is_array($tmp))
                {       $tmpvalue = ($tmp == null) ? "" : implode(",", $tmp);;
            } else { $tmpvalue = ($tmp == null) ? "" :substr($tmp,strpos($tmp,",")); }
                array_push($data,HTML::decode('<div class="signature" id='."$item->name".' name='."$item->name".' data='."$tmpvalue".'</div>'));

            }
            array_push($data,'</div>');
        }
        //add hidden input for revision id
        array_push($data,Form::hidden('_revision',$lastRevision->id));
        array_push($data,Form::hidden('_time','-1',['id'=>'_time']));
        // array_push($data,Form::submit('Abschicken', ['class'=>'btn btn-primary','id'=>'submit']));//submit button
        array_push($data,Form::close());
        return $data;
    }

    private static function getAnswer($fieldName, $values, $secondLevel = "")
    {
        $multipleAnswers = [];
        if($secondLevel != "")
            $fieldName .= "/".$secondLevel;
        foreach ($values as $value) {
                if ($value != null) {
                    if (property_exists($value, "name")) {
                        if ($value->name == $fieldName) {
                            $multipleAnswers[] = $value->value;
                        }
                    }
                }
        }
        if(count($multipleAnswers) == 0)
            return "";
        if(count($multipleAnswers) == 1)
            return $multipleAnswers[0];
        return $multipleAnswers;
    }

}
