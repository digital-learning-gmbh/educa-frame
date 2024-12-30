import React, {useRef} from "react";
import "./bottomAIBar.css"
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import ChatDialog from "./Dialogs/ChatDialog";

function BottomAIBar() {

    const educaModalRef = useRef();

    let showInfo = () => {

        educaModalRef?.current?.open( (btn) => {}, "Über mich, educa AI", "Hallo! Ich bin educa, eine freundliche KI-Assistentin, die von der Digital Learning GmbH entwickelt wurde. Ich bin hier, um dir bei all deinen Fragen oder Anliegen zu helfen. Du kannst mich alles fragen, was du willst und ich werde mein Bestes tun, um dir weiterzuhelfen. Ich bin programmiert, um eine Vielzahl von Themen zu beherrschen, wie zum Beispiel Bildung, Technologie, Wissenschaft und mehr. Also, fühle dich frei, mich alles zu fragen, was du willst, und ich werde mein Bestes tun, um die Antworten zu finden, die du suchst.", [MODAL_BUTTONS.CLOSE]  )
    }

    let showChat = () => {

        educaModalRef?.current?.open( (btn) => {}, "Mit educa AI chatten", <ChatDialog/>, [MODAL_BUTTONS.CLOSE]  )
    }

    return <div>
        <div id="container-floating">
            <div className="nd4 nds"  onClick={() => showChat()}>
                <p className="letter"><i className="fas fa-comments"></i></p>
            </div>

            <div className="nd3 nds">
                <p className="letter"><i className="fas fa-magic"></i></p>
            </div>

            <div className="nd1 nds" onClick={() => showInfo()}>
                <p className="letter"><i className="fas fa-info"></i></p>
            </div>

            <div id="floating-button">
                <p className="letter edit" style={{color: "#000", top: "15px"}}><i className="fas fa-times"></i></p>
                <img className="plus"
                     src="/images/educa_ai_loading_indicator.gif"/>
            </div>
        </div>
        <EducaModal size={"lg"} ref={educaModalRef}/>
    </div>
}

export default BottomAIBar;
