import React, {useState} from 'react';
import {Card} from "react-bootstrap";


export default function CardSectionImageLeft({isLoading, title, image, subtitle, headline, headlineImage, onClick}) {

    return  <div onClick={onClick} className="card m-1"  style={{borderRadius: "1rem", cursor: "pointer"}}>
        <div className="row no-gutters">
            <div className="col-auto">
                { isLoading ?   <div className="box shine" style={{
                    height: "150px",
                    width: "150px",
                    borderTopLeftRadius: "calc(1rem - 1px)", borderBottomLeftRadius: "calc(1rem - 1px)"
                }}/> :
                <img loading={"lazy"} src={image} className="img-fluid" alt="" style={{
                    objectFit: "cover",
                    width: "150px",
                    height: "150px",
                    borderTopLeftRadius: "calc(1rem - 1px)", borderBottomLeftRadius: "calc(1rem - 1px)"
                }}/> }
            </div>
            <div className="col">
                <div className="card-block px-2" style={{top: "50%",
                    position: "relative",
                    transform: "translateY(-50%)"}}>
                        { isLoading ? <>
                            <div className="linesHolder">
                    <div className="line shine" style={{width: "200px"}}/>
                    <div className="line shine"  style={{width: "200px"}}/>
                                <div className="line shine"  style={{width: "200px"}}/>
            </div>
                        </> : <>
                            {headline ? <p className="card-text"><img loading="lazy" src={headlineImage} width="23" className="mr-1" style={{ borderRadius: "50%"}}/> {headline}</p> : null }
                            <h4 className="card-title" style={{fontWeight: 600}}>{title}</h4>
                            <p className="card-text">{subtitle}</p></> }
                </div>
            </div>
        </div>
    </div>;
}
