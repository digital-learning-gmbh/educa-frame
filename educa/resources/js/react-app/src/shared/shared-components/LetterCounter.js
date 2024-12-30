import React from 'react';

function LetterCounter(props) {

    if(props.hide || !props.maxLetters)
        return <></>

    var span = document.createElement('span');
    span.innerHTML = props.string;
    let shorText= span.textContent || span.innerText;

    return (
        <div style={{display :"flex", fontSize : "80%"}} className={"text-muted"}>
            {shorText?.replace(/<[^>]*>?/gm, '').length}/{props.maxLetters}
        </div>
    );
}

export default LetterCounter;
