import React, {createRef, useEffect, useState} from 'react';
import {EducaLoading} from "../../../../shared-local/Loading";
import Card from "react-bootstrap/Card";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
function SystemSettingsLearnContent(props) {

    let [loading, setLoading] = useState(false);
    let [learnContents, setLearnContents] = useState([]);

    useEffect(() => {
        loadLearnContents()
    },[])


    const loadLearnContents = () =>
    {
        setLoading(true)
        // AjaxHelper.loadSystemSettingsLearnContent(tenant.id)
        //     .then( resp =>
        //     {
        //         if(resp.payload.roles) {
        //
        //         }
        //
        //         throw new Error()
        //     })
        //     .finally(() => setLoading(false))
    }


    return (
        loading ? <EducaLoading/> :
            <>
                <div className={"row mb-2"}>
                    <div className={"col"}>
                        <Card style={{backgroundColor: "white"}}>
                            <Card.Body>
                            </Card.Body>
                        </Card>
                    </div>
                    <div className={"col"}>
                        <Card style={{backgroundColor: "white"}}>
                            <Card.Body>
                            </Card.Body>
                        </Card>
                    </div>
                </div>

            <Card style={{backgroundColor: "white"}}>
                <Card.Header>
                    <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                        <h5 className="card-title">
                            <b><i className="fas fa-book"></i> Lerninhalte</b>
                        </h5>
                    </div>
                </Card.Header>
                <Card.Body>
                    <EducaDefaultTable
                        size={"lg"}
                        defaultPageSize={50}
                        pagination={true}
                        columnResizing={true}
                        globalFilter={true}
                        buttonPdfExport={true}
                        buttonExcelExport={true}
                        pageSizePicker={true}
                        filename={"lerninhalt_"}
                        columns={[
                            { Header: 'Anzeigename', accessor: 'name',  filter : true  },
                            { Header: 'Autoren', accessor: 'email',  filter : true  },
                            { Header: 'Kategorien', accessor: 'role',  filter : true  },
                            { Header: 'Tags', accessor: 'location',  filter : true  },
                            { Header: 'Erstellt von', accessor: 'telephone',  filter : true  },
                            { Header: 'Erstellt am', accessor: 'visible_for',  filter : true  },
                            { Header: 'Aktion', accessor: 'action',  filter : true , width: 50 },
                        ]}
                        data={
                            learnContents?
                                learnContents?.map( s =>
                                {
                                    return {...s,
                                        visible_for: s?.roles.map(r => r.name).join(", "),
                                        action: <>
                                            <EducaCircularButton size={"small"} onClick={() => modifyContact(s)}><i className={"fa fa-pencil-alt"}/></EducaCircularButton>
                                            <EducaCircularButton className={"ml-1"} variant={"danger"} size={"small"}
                                                                 onClick={() => onDeleteClick(s)}><i
                                                className={"fas fa-trash"}/></EducaCircularButton>
                                        </>
                                    };
                                }) : []
                        }
                    />
                </Card.Body>
            </Card></>
    )
}
export default SystemSettingsLearnContent;
