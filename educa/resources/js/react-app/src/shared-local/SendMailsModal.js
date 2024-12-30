import React from "react";
import Modal from "react-bootstrap/Modal";
import {getDisplayPair} from "../shared/shared-components/Inputs";
import {Alert, FormControl} from "react-bootstrap";
import {CKEditor} from "@ckeditor/ckeditor5-react";
import SharedHelper, {EducaCKEditorDefaultConfig, MODELS} from "../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import Select from 'react-select/creatable';


export default class SendMailsModal extends React.Component {

    constructor(props) {
        super(props);

        this.state = this.getDefaultState()
    }

    getDefaultState()
    {
        return{
            open: false,
            modelType : null,
            modelIds : null,
            subject : "",
            content : "",
            additionalReceivers: []
        }
    }

    open(modelType, modelIds) {
        this.setState(
            {
                open: true,
                modelType : modelType,
                modelIds : modelIds,
                subject : "",
                content : "",
                additionalReceivers: []
            })
    }

    close()
    {
        this.setState({open : false })
    }

    validate()
    {
        this.setState({errorContent : !this.state.content, errorSubject : !this.state.subject})
        return this.state.content && this.state.subject
    }

    send()
    {
        if(!this.validate())
            return SharedHelper.fireWarningToast("Achtung", "Bitte geben Sie einen Betreff sowie einen Inhalt an.")

        if(!this.state.modelType || !this.state.modelIds?.length > 0 )
            return SharedHelper.fireErrorToast("Fehler", "Ein unbekannter Fehler ist aufgetreten.")
        if(!this.state.subject || !this.state.content )
            return SharedHelper.fireWarningToast("Achtung", "Bitte Betreff und Inhalt angeben.")
        this.props.sendCallback(this.state.modelType, this.state.modelIds, this.state.subject, this.state.content, this.state.additionalReceivers?.map(r => r.value))
    }

    checkMails(mails)
    {
        for(let i = 0; i < mails?.length; i++)
            if(!String(mails[i]?.value).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/))
            {
                SharedHelper.fireWarningToast("Achtung", "Bitte geben Sie ein valides Email Format an.")
                return this.state.additionalReceivers
            }
        return mails

    }

    getModelTypeName(plural)
    {
        switch(this.state.modelType)
        {
            case MODELS.COURSE: return plural? "Planungsgruppen" : "Planungsgruppe"
            case MODELS.CONTACT: return  plural? "Kontakte" : "Kontakt"
            case MODELS.EMPLOYEE: return  plural? "Mitarbeitende" : "Mitarbeiter*in"
            case MODELS.STUDENT: return  plural? "Studierende" : "Studierenden"
            case MODELS.TEACHER: return  plural? "Dozierende" : "Dozent*in"
        }
    }

    render() {

        return  <Modal
            size={"lg"}
            show={this.state.open}
            onHide={() => this.setState(this.getDefaultState())}
        >
            <Modal.Header closeButton><h5>Rundmail erstellen</h5></Modal.Header>
            <Modal.Body>
                    <b>
                        Diese Rundmail wird an {this.state.modelIds?.length} {this.getModelTypeName(this.state.modelIds?.length > 1)} verschickt.
                    </b>
                {getDisplayPair("Weitere Empfänger",
                    <Select
                        isMulti={true}
                        contentEditable={true}
                        formatCreateLabel={ (str) => str + " hinzufügen"}
                        placeholder={"Email Adressen eingeben"}
                        noOptionsMessage = { () => "Email Adressen ..."}
                        value={this.state.additionalReceivers}
                        onChange={(arr) => this.setState({additionalReceivers : arr? this.checkMails(arr) : []})}
                    /> )}
                <Alert className={"m-1"} variant={"info"}>Weitere externe Empfänger bitte per Klick oder per Enter-Taste hinzufügen.</Alert>

                {getDisplayPair("Betreff",
                    <FormControl
                        isInvalid={this.state.errorSubject}
                        value={this.state.subject}
                        onChange={(evt) => this.setState({subject : evt.target.value})}
                    /> )}

                {getDisplayPair("Inhalt",
                    <div className="card-text">
                        <CKEditor
                            config={EducaCKEditorDefaultConfig}
                            editor={window.ClassicEditor}
                            data={this.state.content}
                            onChange={(event, editor) => {
                                const data = editor.getData();
                                this.setState({content: data})
                            }}
                        />
                    </div>
                ,this.state.errorContent)}
            </Modal.Body>
            <Modal.Footer>
                <Button
                    variant={"primary"}
                    onClick={() => this.send()}
                >Absenden</Button>

            </Modal.Footer>
        </Modal>
    }
}
