import React, {Component} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import {Card, Container} from "react-bootstrap";
import EducaHelper from "../../helpers/EducaHelper";
import QRCode from "react-qr-code";

class SectionOpenCastView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                opencastInfo: null,
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
        AjaxHelper.getOpenCastDetails(this.props.group.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.series)
                    this.setState({opencastInfo: resp.payload.series})
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
        if (this.state.opencastInfo)
            return (
                <Container className="gedf-wrapper">
                    {this.state.opencastInfo.map((video) => {
                        if (!video?.publication_status?.includes("engage-player")) {
                            return <></>
                        }
                        return <Card className="mt-4">
                            <h2 class={"m-2"}>{video?.title}</h2>
                            <iframe allowFullScreen
                                    src={"https://isba-video.educacloud.de/play/" + video?.identifier}
                                    name="Player" scrolling="no" frameBorder="0"
                                    marginHeight="0px" marginWidth="0px" width="100%" height="500"></iframe>
                        </Card>
                    })}
                </Container>
            );
    }
}

export default SectionOpenCastView;
