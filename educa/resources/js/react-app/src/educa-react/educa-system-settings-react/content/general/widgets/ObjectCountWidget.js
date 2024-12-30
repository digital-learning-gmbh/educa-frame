import React from "react";
import {useEffect, useRef, useState} from "react";
import {Card, Form, Spinner} from "react-bootstrap";
import AjaxHelper from "../../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../../shared/shared-helpers/SharedHelper";
import {DisplayPair, NumberInput} from "../../../../../shared/shared-components/Inputs";

import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    TimeScale
} from 'chart.js';
import 'chartjs-adapter-moment';
import {Bar, Line} from 'react-chartjs-2';
import DatePicker from "react-datepicker";
import moment from "moment/moment";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    TimeScale
);

export function ObjectCountWidget(props) {
    const [data, setData] = useState([])
    const [startDay, setStartDay] = useState(moment()
        .subtract(4, "weeks").toDate())
    const [endDay, setEndDay] = useState(moment().toDate())

    const [isLoading, setIsLoading]  = useState(false)
    let calendarRef = useRef();

    useEffect(() =>{
        loadData()
    },[])

    useEffect(() =>{
        loadData()
    },[startDay,endDay])

    const loadData = () => {
        if(!moment(endDay).isValid() || !moment(startDay).isValid())
            return;

        setIsLoading(true)
        AjaxHelper.getSystemSettingsGeneralWidgetInfoObjects(moment(startDay).unix(),moment(endDay).unix())
            .then(resp => {
                setData(resp.payload.cloudIds)
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Auswertung konnten nicht geladen werden."))
            .finally(() => setIsLoading(false))
    }

    const chartData = {
        datasets : data
    }

    const options = {
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
            }
        }
    };

    let RangeDateInput = props => {
        return (
            <Form.Control
                onChange={() => {}}
                value={
                    moment(startDay).format("DD.MM.YYYY") +
                    " - " +
                    moment(endDay).format("DD.MM.YYYY")
                }
                onClick={props.onClick}
            />
        );
    };


    return <Card style={{height: "100%"}}>
        <Card.Header style={{backgroundColor: "#fff"}}>
            <h5 className="card-title"><b><i className="fas fa-chart-line"></i> Erstellte Daten</b>
                {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}
                <div style={{float:"right"}}>
                <DisplayPair title={"Anzahl Tage"} hasError={false}>
                    <DatePicker
                        ref={calendarRef}
                        shouldCloseOnSelect={false}
                        customInput={<RangeDateInput />}
                        selected={startDay}
                        onChange={dates => {
                            console.log(dates)
                            setStartDay(dates[0]);
                            setEndDay(dates[1]);
                        }}
                        startDate={startDay}
                        endDate={endDay}
                        selectsRange
                        locale="de-DE"
                    />
                </DisplayPair>
                </div>
            </h5>
        </Card.Header>
        <Card.Body>
            <div className={"d-flex flex-column"}>
                <div>
                    <Line data={chartData} height={350} options={options}/>
                </div>
            </div>
        </Card.Body>
    </Card>
}

export default ObjectCountWidget
