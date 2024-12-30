import React, {Component} from "react";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import SendMailsModal from "../../shared-local/SendMailsModal";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper";


export default class EducaSendMailsModal extends Component
{

    constructor(props) {
        super(props);
        this.send = this.send.bind(this)
    }


    send(modelType,modelIds,subject,content, additionalReceivers)
    {
        EducaAjaxHelper.sendMassCorrespondence(modelType,modelIds,subject,content, additionalReceivers)
            .then( resp =>
            {
                if(resp.status > 0 )
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Die Mails wurden erfolgreich gesendet.")
                    this.props.forwardRef?.current?.close()
                    return
                }
                throw new Error("")
            })
            .catch( err =>
            {
                return SharedHelper.fireErrorToast("Fehler", "Die Mails konnten nicht gesendet werden. "+err.message)
            })
    }

    render() {
        return <SendMailsModal sendCallback={this.send} ref={this.props.forwardRef}/>
    }

}
