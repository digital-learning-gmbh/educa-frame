import React, { useState } from 'react'
import ReactDOM from 'react-dom'
import PropTypes from 'prop-types'
import { Modal } from 'react-bootstrap'
import ImageCropper from './ImageCropper'
import AdministrationAjaxHelper from '../../administration-react/AdministrationAjaxHelper'
import SharedHelper from '../shared-helpers/SharedHelper'

const MountedModal = (props) => {
    const { userId, hide } = props

    const [image, setImage] = useState(null)

    const onImageReady = (image) => {
        // TODO implement
        setImage(image)
        console.log(image)
        AdministrationAjaxHelper.updateStudentImage(userId, image)
            .then((resp) => {
                if (resp.status > 0 && resp.payload.student) {
                    SharedHelper.fireSuccessToast(
                        'Bild hochgeladen',
                        'Das Bild wurde erfolgreich hochgeladen.'
                    )
                    return
                }

                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast(
                    'Fehler',
                    'Das Bild konnte nicht hochgeladen werden. ' + err.message
                )
            })
    }

    /*
    return ReactDOM.createPortal(
        <Modal show={true} size={'lg'} onHide={hide}>
            <Modal.Header closeButton>
                <Modal.Title>Bild hochladen und bearbeiten</Modal.Title>
            </Modal.Header>
            <Modal.Body>
            </Modal.Body>
            <Modal.Footer></Modal.Footer>
        </Modal>,
        document.body
    )
    */

    return (
        <div>
            <ImageCropper initImage={image} imageReadyCallback={onImageReady} />
        </div>
    )
}
MountedModal.propTypes = {
    isOpen: PropTypes.bool.isRequired,
    initialImageUrl: PropTypes.string,
    userId: PropTypes.number.isRequired,
    hide: PropTypes.func.isRequired
}

const ImageUploadModal = (props) => {
    const { isOpen } = props

    return isOpen ? <MountedModal {...props} /> : null
}
ImageUploadModal.propTypes = {
    isOpen: PropTypes.bool.isRequired,
    initialImageUrl: PropTypes.string,
    userId: PropTypes.number.isRequired,
    hide: PropTypes.func.isRequired
}

export default ImageUploadModal
