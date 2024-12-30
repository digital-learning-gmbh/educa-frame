import React, {useState} from 'react';
import {Card} from "react-bootstrap";


export default function CardSectionImageTop({isLoading, title, image, subtitle, headline, headlineImage, onClick}) {

    return <div onClick={onClick} className="card m-1" style={{borderRadius: "1rem", cursor: "pointer"}}>
        { isLoading ?   <div className="box shine card-img-top" style={{
                    height: "150px",
                    width: "100%",
                    borderTopLeftRadius: "calc(1rem - 1px)", borderTopRightRadius: "calc(1rem - 1px)"
                }}/> :
        <img loading={"lazy"} className="card-img-top" src={image} alt="Card image" style={{
            borderTopLeftRadius: "calc(1rem - 1px)", borderTopRightRadius: "calc(1rem - 1px)",
                objectFit: "cover",
                width: "100%",
                height: "200px"
        }}></img> }
            <div className="card-body">
            { isLoading ? <>
                            <div className="linesHolder">
                <div className="line shine" style={{width: "200px"}}/>
                <div className="line shine"  style={{width: "200px"}}/>
            </div>
                        </> : <>
                       {headline ? <p className="card-text"><img loading="lazy" src={headlineImage} width="23" className="mr-1" style={{ borderRadius: "50%"}}/> {headline}</p> : null }
                <h4 className="card-title" style={{fontWeight: 600}}>{title}</h4>
                <p className="card-text">{subtitle}</p></> }
            </div>
    </div>;
}
