import React, {useCallback, useEffect, useRef, useState} from "react";
import Cropper from 'react-easy-crop'
import {Alert, Button} from "react-bootstrap";
import RangeSlider from 'react-bootstrap-range-slider';
import {getDisplayPair} from "./Inputs";
import 'react-bootstrap-range-slider/dist/react-bootstrap-range-slider.css';
import SharedHelper from "../shared-helpers/SharedHelper";

const ImageCropper = (props) =>
{
    const [baseImg, setBaseImg] = useState(null);
    const [crop, setCrop] = useState({ x: 0, y: 0 })
    const [zoom, setZoom] = useState(1)
    const [croppedAreaPixels, setCroppedAreaPixels] = useState(1)
    const [rotation, setRotation] = useState(0)

    const uid = SharedHelper.createUUID()
    const input = useRef()

    useEffect(() =>
    {
        setBaseImg(props.initImage)
    },[props.initImage])

    useEffect(() =>
    {
        const reader = new FileReader();
        reader.addEventListener('load', () => setBaseImg(reader.result));
        if(props.initFile)
         reader.readAsDataURL(props.initFile);
    },[props.initFile])

    const onSelectFile = (e) => {
        if (e.target.files && e.target.files.length > 0) {
            const reader = new FileReader();
            reader.addEventListener('load', () => setBaseImg(reader.result));
            reader.readAsDataURL(e.target.files[0]);
        }
    }

    const onCropComplete = useCallback((croppedArea, croppedAreaPixels) => {
        setCroppedAreaPixels(croppedAreaPixels)
    }, [])

    const save = () =>
    {
        if(!croppedAreaPixels)
           return props.imageReadyCallback( baseImg )

        getCroppedImg(
            baseImg,
            croppedAreaPixels,
            rotation
        ).then( resp =>
        {
            props.imageReadyCallback( resp.file)
        })
       .catch( err =>
         {
             setBaseImg(null)
             SharedHelper.fireErrorToast("Fehler", "Das Bild konnte nicht gespeichert werden. "+err.message)
         })
    }

    return <div>
        <Alert variant="info">
            Daran solltest du bei der Wahl eines Profilbildes immer denken:
            <ol>
                <li>Es ist keine Pflicht ein Profilbild einzustellen</li>
                <li>Dein Profilbild wird immer zusammen mit deinem Namen angezeigt</li>
                <li>Nacktfotos und Bilder die Rassismus, Sexismus und/oder Gewalt zeigen sind verboten</li>
                <li>Am besten wählst du ein Bild aus, auf dem dich andere erkennen können. Alternativ kannst du auch ein Bild einstellen das deine Hobbys zeigt</li>
            </ol>
            </Alert>
        {props.hideFilePicker? null : <input
            accept={"image/*"}
            type="file"
            ref={input}
            id={"input_" + uid}
            onChange={onSelectFile}
            style={{width: "0px", display: "none"}}/>}
        {props.hideFilePicker? null : <Button
            title={"Datei auswählen"}
            variant="outline-dark"
            className="m-1"
            onClick={() => {
               input.current?.click()
            }}
            type="button">Datei auswählen...
        </Button>}
        {baseImg?<div style={{display :"flex", flexDirection : "column"}}>
            <div style={{position: "relative", minHeight :"400px"}}>
                <Cropper
                  crop={crop}
                  rotation={rotation}
                  zoom={zoom}
                  image={baseImg}
                  aspect={props.aspect? props.aspect : 1}
                  cropShape={props.round? "round" : "rect"}
                  onCropChange={setCrop}
                  onCropComplete={onCropComplete}
                  onZoomChange={setZoom}
                  onRotationChange={setRotation}
            />
            </div>
            {getDisplayPair("Zoom", <RangeSlider
                value={zoom}
                variant={"primary"}
                min={1}
                max={3}
                step={0.1}
                onChange={(evt) => setZoom(evt.target.value)}
            />)}
            {getDisplayPair("Rotation",<RangeSlider
                value={rotation}
                variant={"primary"}
                min={0}
                max={360}
                step={1}
                onChange={(evt) => setRotation(evt.target.value)}
            />)}
            <div style={{display : "flex", flex : 1, justifyContent : "flex-end"}}><Button
            children={"Speichern"}
            onClick={() => save() }
            /></div>

        </div> : "Bitte wählen Sie ein Bild aus."}
    </div>

}

const createImage = url =>
    new Promise((resolve, reject) => {
        const image = new Image()
        image.addEventListener('load', () => resolve(image))
        image.addEventListener('error', error => reject(error))
        image.setAttribute('crossOrigin', 'anonymous') // needed to avoid cross-origin issues on CodeSandbox
        image.src = url
    })

function getRadianAngle(degreeValue) {
    return (degreeValue * Math.PI) / 180
}

/**
 * This function was adapted from the one in the ReadMe of https://github.com/DominicTobias/react-image-crop
 * @param {File} image - Image File url
 * @param {Object} pixelCrop - pixelCrop Object provided by react-easy-crop
 * @param {number} rotation - optional rotation parameter
 */
async function getCroppedImg(imageSrc, pixelCrop, rotation = 0) {
    const image = await createImage(imageSrc)
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')

    const maxSize = Math.max(image.width, image.height)
    const safeArea = 2 * ((maxSize / 2) * Math.sqrt(2))

    // set each dimensions to double largest dimension to allow for a safe area for the
    // image to rotate in without being clipped by canvas context
    canvas.width = safeArea
    canvas.height = safeArea

    // translate canvas context to a central location on image to allow rotating around the center.
    ctx.translate(safeArea / 2, safeArea / 2)
    ctx.rotate(getRadianAngle(rotation))
    ctx.translate(-safeArea / 2, -safeArea / 2)

    // draw rotated image and store data.
    ctx.drawImage(
        image,
        safeArea / 2 - image.width * 0.5,
        safeArea / 2 - image.height * 0.5
    )
    const data = ctx.getImageData(0, 0, safeArea, safeArea)

    // set canvas width to final desired crop size - this will clear existing context
    canvas.width = pixelCrop.width
    canvas.height = pixelCrop.height

    // paste generated rotate image with correct offsets for x,y crop values.
    ctx.putImageData(
        data,
        Math.round(0 - safeArea / 2 + image.width * 0.5 - pixelCrop.x),
        Math.round(0 - safeArea / 2 + image.height * 0.5 - pixelCrop.y)
    )

    // As Base64 string
    // return canvas.toDataURL('image/jpeg');

    // As a blob
    return new Promise(resolve => {
        canvas.toBlob(file => {
            resolve( { url : URL.createObjectURL(file), file : file})
        }, 'image/jpeg')
    })
}


export default ImageCropper
