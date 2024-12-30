import React, {Component} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import {Card, Container} from "react-bootstrap";
import EducaHelper from "../../helpers/EducaHelper";
import QRCode from "react-qr-code";
import {useEducaLocalizedStrings, withEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

class SectionAccessCodeView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                currentCode: null,
                isReady: false
            }

    }

    componentDidMount() {
        this._isMounted = true
        this.init()
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.section.id !== prevProps.section.id || this.props.group.id !== prevProps.group.id)
            this.init()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    init() {
        this.setState({isReady: false})
        AjaxHelper.getGroupAccessCode(this.props.group.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.code)
                    this.setState({currentCode: resp.payload.code})
                else
                    throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "QR Code konnte nicht übertragen werden.")
            })
            .finally(() => {
                this.setState({isReady: true})
            })
    }

    render() {
        if (!this.state.isReady)
            return <EducaLoading/>
        if (this.state.currentCode.code)
            return (
                <Container className="gedf-wrapper">
                    <Card className="mt-2">
                        <Card.Body>
                            <div className="offset-4 col-4 text-center">
                                <QRCode
                                    className={"m-2"}
                                    renderAs={"svg"}
                                    bgColor={"#FFFF"}
                                    size={256}
                                    value={"educa://join/" + this.state.currentCode.code}></QRCode>
                                <h4>{this.props.translate("access_code","Zugangscode:")}</h4>
                                <h2>{this.state.currentCode.code}</h2>
                                <p>{this.props.translate("access_code.info","Gebe diesen Code ein, um dich bei educa zu registrieren. Falls du bereits einen Account hast, kannst du über diesen Code weitere Personen in die Gruppe einladen.")}</p>
                            </div>
                        </Card.Body>
                    </Card>
                </Container>
            );
    }
}

export default withEducaLocalizedStrings(SectionAccessCodeView);
