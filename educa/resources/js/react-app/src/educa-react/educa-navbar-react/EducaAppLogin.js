import React, {useEffect, useState} from 'react';
import AjaxHelper from '../helpers/EducaAjaxHelper';

export function EducaAppLogin(props) {

    return (<>
        {
                <div className='text-center'>
                   <p>Sie können auf die Lernplattform auch per App zugreifen. Laden Sie dafür die App 'educa' aus dem AppStore oder PlayStore herunter und scannen diesen Code.</p>
                   <img src={AjaxHelper.getQRCode()} className="img-fluid" />
                </div>
        }</>
    )
}
