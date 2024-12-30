import React, {Component} from 'react';
import {connect} from "react-redux";
import {Carousel, Col, Container,Row} from "react-bootstrap";
import Card from "react-bootstrap/Card";
import "./style.css"
import EducaLandingNavbar from "./EducaLandingNavbar";

class EducaLandingViewReact extends Component {

    render() {
        return <div>
            <EducaLandingNavbar/>

            <Container>

                <div className="row mb-2 mt-2">
                    <Card>
                <Carousel>
                    <Carousel.Item>
                        <img
                            className="d-block w-100"
                            src="/images/nlq/hungry-classmates.jpg"
                            alt="First slide"
                        />
                        <Carousel.Caption className={"text-dark"} style={{ backgroundColor: "rgba(255,255,255,0.8)"}}>
                            <h3>First slide label</h3>
                            <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                        </Carousel.Caption>
                    </Carousel.Item>
                    <Carousel.Item>
                        <img
                            className="d-block w-100"
                            src="/images/nlq/digital.jpg"
                            alt="Second slide"
                        />

                        <Carousel.Caption className={"text-dark"} style={{ backgroundColor: "rgba(255,255,255,0.8)"}}>
                            <h3>First slide label</h3>
                            <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                        </Carousel.Caption>
                    </Carousel.Item>
                </Carousel></Card>
                </div>


                <h2 className={"mb-2"}>Veranstaltungs-Highlights</h2>

                <div className="row mb-2">
                    <div className="col-md-6">
                        <div className="card flex-md-row mb-4 box-shadow h-md-250">
                            <div className="card-body d-flex flex-column align-items-start">
                                <strong className="d-inline-block mb-2 text-secondary">Digitalfortbildungen</strong>
                                <h3 className="mb-0">
                                    <a className="text-dark" href="#">Das iPad als »Schweizer Taschenmesser«</a>
                                </h3>
                                <div className="mb-1 text-muted">04.04.</div>
                                <p className="card-text mb-auto">Grundfunktionen des iPads jetzt auch mit neuen Funktionen kennenlernen und im Unterricht sinnvoll einsetzen.</p>
                                <a href="#">Anmelden</a>
                            </div>
                            <img className="card-img-right flex-auto d-none d-md-block"
                                 data-src="holder.js/200x250?theme=thumb" alt="Thumbnail [200x250]"
                                 style={{width: "200px", height: "250px"}}
                                 src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20250%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17f2b765712%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17f2b765712%22%3E%3Crect%20width%3D%22200%22%20height%3D%22250%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2256.1953125%22%20y%3D%22131%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                 data-holder-rendered="true"/>
                        </div>
                    </div>
                    <div className="col-md-6">
                        <div className="card flex-md-row mb-4 box-shadow h-md-250">
                            <div className="card-body d-flex flex-column align-items-start">
                                <strong className="d-inline-block mb-2 text-success">Ganztag</strong>
                                <h3 className="mb-0">
                                    <a className="text-dark" href="#">Regional, saisonal, bio, Schüler*innen erleben Ernährungsvielfalt in ihrer Umgebung</a>
                                </h3>
                                <div className="mb-1 text-muted">02.03.</div>
                                <p className="card-text mb-auto">Einordnung des Curriculums Mobilität in eine Bildung für nachhaltige Entwicklung. Aufzeigen der Umsetzung verschiedener Bausteine des Curriculums in Form des Projektes „Klassen-Regio-Challenge“</p>
                                <a href="#">Anmelden</a>
                            </div>
                            <img className="card-img-right flex-auto d-none d-md-block"
                                 data-src="holder.js/200x250?theme=thumb" alt="Thumbnail [200x250]"
                                 src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20250%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17f2b765714%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17f2b765714%22%3E%3Crect%20width%3D%22200%22%20height%3D%22250%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2256.1953125%22%20y%3D%22131%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                 data-holder-rendered="true" style={{width: "200px", height: "250px"}}/>
                        </div>
                    </div>
                </div>
                <h2 className={"mt-2"}>Bildungspolitische Schwerpunkte</h2>

                <Row>
                    <Col xs={4}>
                        <div className="card text-white bg-secondary mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Digitalfortbildungen</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                    <Col xs={4}>
                        <div className="card text-white bg-success mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Ganztag</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                    <Col xs={4}>
                        <div className="card text-white bg-primary mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Kerncurricula</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                </Row>

                <Row>
                    <Col xs={4}>
                        <div className="card text-white bg-info mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Inklusion</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                    <Col xs={4}>
                        <div className="card text-white bg-success mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Medienbildung</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                    <Col xs={4}>
                        <div className="card text-dark bg-warning mb-3">
                            <div className="card-body">
                                <h5 className="card-title">Arbeitsschutz und Gesundheitsmanagement</h5>
                                <p className="card-text">Beispieltext</p>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>

                <div className="container-fluid mt-5 bg-dark text-light pt-5">
                    <div className="mx-5">
                        <div className="row mb-4 ">
                            <div className="col-md-4 col-sm-11 col-xs-11">
                                <div className="footer-text pull-right">
                                    <div className="d-flex">
                                    </div>
                                    <p className="card-text">Finden Sie zielgerichtet Veranstaltungen und melden Sie sich zu öffentlich ausgeschriebenen Fort- und Weiterbildungen an.</p>
                                </div>
                            </div>
                        </div>
                        <div className="divider mb-4"></div>
                        <div className="row">
                            <div className="col-md-6 col-sm-6 col-xs-6">
                                <div className="pull-right mr-4 d-flex policy">
                                    <div>Impressum</div>
                                    <div>Datenschutz</div>
                                    <div>Kontakt</div>
                                </div>
                            </div>
                            <div className="col-md-6 col-sm-6 col-xs-6">
                                <div className="pull-left">
                                    <p><i className="fa fa-copyright"></i> 2022 Niedersächsische Landesinstitut für schulische Qualitätsentwicklung (NLQ)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    }
}

const mapStateToProps = state => ({store: state})

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions

    }
}

export default connect(mapStateToProps, mapDispatchToProps)(EducaLandingViewReact);
