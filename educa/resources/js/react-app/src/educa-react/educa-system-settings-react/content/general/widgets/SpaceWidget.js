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
    TimeScale, ArcElement
} from 'chart.js';
import 'chartjs-adapter-moment';
import {Bar, Doughnut, Line} from 'react-chartjs-2';
import DatePicker from "react-datepicker";
import moment from "moment/moment";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    TimeScale,
    ArcElement
);

export function SpaceWidget(props) {
    const [data, setData] = useState(null)
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
        AjaxHelper.getSystemSettingsGeneralWidgetInfoSpaces()
            .then(resp => {
                setData(resp.payload.spaces)
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Auswertung konnten nicht geladen werden."))
            .finally(() => setIsLoading(false))
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
    }


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
            <h5 className="card-title"><b><i class="far fa-save"></i> Speicherplatz</b>
                {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}
            </h5>
        </Card.Header>
        <Card.Body>
            <div className={"d-flex flex-column"}>
                <div>
                    {data  ? <Doughnut data={data} height={350} options={options}/> : null }
                </div>
            </div>
        </Card.Body>
    </Card>
}

export default SpaceWidget
