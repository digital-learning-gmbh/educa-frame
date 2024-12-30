import React, {useState} from 'react';


export default function MutedParagraph(props) {

    return  <p className={"text-muted"} style={{fontSize:props.fontSize ?? "1.0rem"}}>{props.children}</p>;
}
