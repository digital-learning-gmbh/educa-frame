import React, {useRef, useState} from 'react';
import {connect, useSelector} from "react-redux";
import {Container, Nav, Navbar, NavDropdown, Spinner} from "react-bootstrap";
import moment from "moment";
import AsyncSelect from "react-select/async";
import AnnouncementModalViewer from "../educa-components/ModalViewers/AnnouncementModalViewer";
import {BASE_ROUTES} from "../App";
import {withRouter} from "react-router";
import Button from "react-bootstrap/Button";
import {EducaCardLinkButton} from "../../shared/shared-components/Buttons";
import Dropdown from "react-bootstrap/Dropdown";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import {EDB_ROUTES} from "../EducaEDBWrapper";

function EducaLandingNavbar(props) {

    const me = useSelector( s => s.currentCloudUser)

    let changeRoute = (path, search) => {
        props.history.push({
            pathname: path,
            search: search,
        })
    }

    const logout = () =>
    {
        AjaxHelper.logout()
    }

    const ddToggle = React.forwardRef(({children, onClick}, ref) => (
     <Navbar.Text onClick={ onClick}>
            Angemeldet als <EducaCardLinkButton>{me.name} <i className={"fas fa-chevron-down"}/> </EducaCardLinkButton>
        </Navbar.Text>
    ));

    return (
            <Navbar bg="light" expand="lg">
                <Container>
                    <Navbar.Brand onClick={ () => changeRoute(EDB_ROUTES.LANDING)}><img
                        alt=""
                        src="/images/nlq/nlq-logo.png"
                        height="30"
                        style={{cursor :"pointer"}}
                        className="d-inline-block align-top"
                    /></Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav" />
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="mr-auto">
                            <NavDropdown title="Veranstaltung anbieten" id="basic-nav-dropdown">
                                <NavDropdown.Item style={{cursor :"pointer"}} onClick={() => changeRoute(EDB_ROUTES.PROPOSAL)}>Veranstaltungsvorschlag</NavDropdown.Item>
                                <NavDropdown.Item style={{cursor :"pointer"}} onClick={() => changeRoute(EDB_ROUTES.LICENSE)}>Anbieterzulassung</NavDropdown.Item>
                            </NavDropdown>
                            <Nav.Link  style={{cursor :"pointer"}} onClick={() => changeRoute(EDB_ROUTES.HELP)}>Hilfe</Nav.Link>
                        </Nav>
                    </Navbar.Collapse>
                    <Navbar.Collapse className="justify-content-center">
                        <div style={{width : "100%"}}>
                            <LandingSearchBox
                                placeholder={"Suche"}
                            />
                        </div>

                    </Navbar.Collapse>
                    <Navbar.Collapse className="justify-content-end">
                        {me?.id > 0 ?<Dropdown>
                                <Dropdown.Toggle
                                    disabled={!me || !me._id}
                                    as={ddToggle}
                                />
                                <Dropdown.Menu>
                                    <Dropdown.Item
                                        onClick={() => logout()}
                                    ><i className="fas fa-sign-out-alt"></i> Ausloggen</Dropdown.Item>
                                </Dropdown.Menu>
                            </Dropdown>
                            :
                            <Button onClick={() => changeRoute(BASE_ROUTES.LOGIN)}>
                                Zum Login <i className={"fas fa-sign-in-alt"}/>
                            </Button>
                        }
                    </Navbar.Collapse>
                </Container>
            </Navbar>
    );
}

export default withRouter(connect()(EducaLandingNavbar));



const LandingSearchBox = withRouter((props) => {

    let [searchString, setSearchString] = useState("")

    let announcementModalRef = useRef()

    let store = useSelector(state => state);

    let searchFunc = (str) => {
        if (str?.length >= 3) {
            return searchAjax(str)
        }
        return Promise.resolve([])
    }


    let changeRoute = (path, search) => {
        props.history.push({
            pathname: path,
            search: search,
        })
    }

    let searchAjax = (str) => {

        return Promise.resolve( prepareOptions(["Testveranstaltung 1", "Testveranstaltung 2"]))
        /*return AjaxHelper.searchAnything(str, props.categories ? props.categories : null)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.search) {
                    return prepareOptions(resp.payload.search)
                }
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Suchen fehlgeschlagen. " + err.message)
            })
            .finally((opts) => {
                return opts
            })*/
    }

    let prepareOptions = (search) => {

        let opts = search.map( s => (getSearchEntry(s)))
        return [{label : "Veranstaltungen", options : opts}]
    }


    let getSearchEntry = (s) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-2"}>
                <i className="fas fa-calendar" style={{"fontSize": "24px", color: "rgb(229, 0, 70)"}}></i>
            </div>
            <div style={{display: "flex", flexDirection: "column"}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{s} </div>
                <div style={{display: "flex", flexDirection: "column"}}>
                    <div>{moment().format("DD.MM.YYYY")}</div>
                    <div>{moment().format("HH:mm")} - {moment().add(2, "hours").format("HH:mm")}</div>
                </div>

            </div>
        </div>
        return {
            category: "search",
            label: comp,
            value: event.id,
            action: () => {
                changeRoute(EDB_ROUTES.SEARCH)
            }
        }
    }



    return (
        <div style={{...props.style}}>
            <AsyncSelect
                onChange={(obj) => obj.action()}
                value={null}
                loadOptions={(inputValue) => {
                    setSearchString(inputValue);
                    return searchFunc(inputValue)
                }}
                placeholder={"Suchen..."}
                loadingMessage={() => <Spinner animation={"grow"} size={"lg"}/>}
                noOptionsMessage={() => searchString?.length < 3 ?
                    <div>
                        Bitte mindestens 3 Zeichen eintippen.
                        Oder direkt zur <EducaCardLinkButton onClick={() => changeRoute(EDB_ROUTES.SEARCH)}>Detailsuche <i className={"fas fa-arrow-right"}/> </EducaCardLinkButton>
                    </div>

                    : "Keine Treffer"}
            >
            </AsyncSelect>
            <AnnouncementModalViewer ref={announcementModalRef}/>
        </div>
    );
})
