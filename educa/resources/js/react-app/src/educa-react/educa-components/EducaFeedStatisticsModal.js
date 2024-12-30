import React, {useEffect, useState} from 'react';
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {EducaDefaultTable} from "../../shared/shared-components";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    TimeScale
} from 'chart.js';
import 'chartjs-adapter-moment';
import { Line } from "react-chartjs-2";
import {EducaLoading} from "../../shared-local/Loading";
import {Col, Row} from "react-bootstrap";
import "../../shared/shared-components/modals.css"

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    TimeScale
);

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: '',
        },
    },
    scales: {
        y: {
            title: {
                display: true,
                text: "Anzahl"
            },
            ticks: {
                stepSize: 1
            }
        },
        x: {
            title: {
                display: true,
                text: "Zeitraum"
            },
            type: 'time',
            time: {
                unit: "day"
            }
        }
    }
};

function EducaFeedStatisticsModal({feedActivity, hide}) {

    const [users, setUsers] = useState([])
    const [history, setHistory] = useState([])
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        if (feedActivity)
            loadInfo()
    }, [feedActivity])

    const loadInfo = () => {
        setIsLoading(true)
        AjaxHelper.getFeedStatistics(feedActivity.id)
            .then( resp => {
                if(resp.status > 0) {
                    setUsers(resp.payload.count_rollen)
                    setHistory(resp.payload.history)
                }
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Auswertung konnte nicht geladen werden."))
            .finally(() => {
                setIsLoading(false)
            })
    }

    const chartdata= {
        datasets: history
    };

    return (
        <Modal show={!!feedActivity} onHide={hide} size={"xl"} dialogClassName={"modal-90-vw"}
        >
            <Modal.Header>
                <Modal.Title>
                    <i className="far fa-chart-bar mr-1"></i> Auswertung
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Row>
                    <Col style={{ height: "750px"}}>
                        <Line height={350}  data={chartdata}
                              options={chartOptions}/>
                    </Col>
                    <Col>
                        {
                            isLoading ? <EducaLoading/> : <>  <h5><i className={"fas fa-eye"}/> Nutzer, die diesen Beitrag gesehen haben</h5>
                                <EducaDefaultTable
                                    columns={[
                                        {Header :"Rolle", accessor : "role"},
                                        {Header :"Anzahl", accessor : "count"},
                                    ]}
                                    data={users}
                                /></> }
                    </Col>
                </Row>


            </Modal.Body>
            <Modal.Footer>
                <Button variant={"secondary"} onClick={hide}>Schließen</Button>
            </Modal.Footer>
        </Modal>
    );
}

export default EducaFeedStatisticsModal;
