import React from 'react'
import "./schimmer.css"
import {Card} from "react-bootstrap";

export function EducaShimmer(props) {

    return (
        <Card  className="mt-2 loading">
            <Card.Body  style={{display: "block"}}>
                {box(props.box)}
                {lines(props.lines)}
            </Card.Body>
        </Card>
    )

    function lines(display)
    {
        if(display == "none")
            return <div></div>
        return (
            <div className="linesHolder">
                <div className="line shine"/>
                <div className="line shine"/>
                <div className="line shine"/>
            </div>
        );
    }

    function box(display)
    {
        if(display == "none")
            return <div></div>
        return  <div className="box shine"/>
    }
}
