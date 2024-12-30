import TextareaAutosize from "react-textarea-autosize";
import EducaHelper, {LIMITS} from "../helpers/EducaHelper";
import React, {useRef, useState} from "react";
import EducaModal, {MODAL_BUTTONS} from "../../shared/shared-components/EducaModal";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import { EducaLoading } from "../../shared-local/Loading";
import { Spinner } from "react-bootstrap";
import {EducaCKEditorDefaultConfig} from "../../shared/shared-helpers/SharedHelper";
import {CKEditor} from "@ckeditor/ckeditor5-react";
import {useSelector} from "react-redux";


export default function EducaAICKEditor(props)
{
    let educaModalRef = useRef();
    let [value, setValue] = useState(props.data??"");
    let [isAILoading, setIsAILoading] = useState(false)

    let tenant = useSelector(s => s.tenant);

    let askAI = (answer) => {
        setIsAILoading(true)
        AjaxHelper.aiCompleteTextbox(answer)
            .then(resp => {
                if (resp.payload && resp.payload?.answer) {
                    setIsAILoading(false)
                    setValue((value !== "" ? value + " " : "") + resp.payload?.answer)
                } else
                    throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    let showAIModal = () => {
        let answer = ""
        educaModalRef?.current?.open((btn) => btn === MODAL_BUTTONS.OK? askAI(answer) : null
            , "educa AI",
            <ChatGPTQuestion onChange={(val) => answer = val} />, [
                MODAL_BUTTONS.OK,MODAL_BUTTONS.CLOSE
            ]);
    }

    return <>
        <CKEditor
            className={props.className}
            editor={props.editor}
            config={props.config}
            data={value}
            onReady={props.onReady}
            onChange={(event, editor) => { props.onChange(event,editor) }}
        />
        { tenant.openai_key ?
            <div className={"float-right mt-1"}>
                <button className={"btn btn-secondary"} onClick={() => { showAIModal() }}>{ isAILoading ? <><Spinner style={{height: "20px", width: "20px"}} className={"ml-2 align-self-center"} animation={"grow"}/> Anfrage läuft... </>: <><i className="fas fa-robot"></i> educa AI fragen</>}</button>
                { !isAILoading ? <>
                    {props.children}
                </> : null }
            </div> : null }
        <div className={"clearfix"}></div>

        <EducaModal
            ref={educaModalRef}
            size={"lg"}
            closeButton={true}
        />
    </>

}

function ChatGPTQuestion(props) {

    return <>
        <TextareaAutosize
            placeholder={"Frag mich etwas: z.B. Schreib mir einen Text über Datenschutz, erstell mir eine Ankündigung für das Sommerfest"}
            className="form-control editor"
            maxRows={props.maxRows ?? 6}
            minRows={props.minRows ?? 3}
            onChange={(evt) => props.onChange(evt.target.value)}
        />
    </>
}
