import React, {Component} from "react";
import Button from "react-bootstrap/Button";
import ImageCropper from "./ImageCropper";
import EducaModal from "./EducaModal";

export default class AttachedFileComponent extends Component {

    constructor(props) {
        super(props);
        this.educaModalRef = React.createRef()
        this.openFileEditModal = this.openFileEditModal.bind(this)
    }

    openFileEditModal(file)
    {
        this.educaModalRef.current.open( () => {}, "Bild ändern",
            <ImageCropper
                hideFilePicker={true}
                initFile={file}
                imageReadyCallback={ (img) => {
                    this.educaModalRef?.current?.close();
                    let f = new File([img], file.name);
                    this.props.fileChangedCallback(f)}}
            />,[])

    }
    render() {
        return (
            <div className={"p-1"} style={this.props.zebra ? {
                display: "flex",
                flexDirection: "row",
                backgroundColor: "lightgray"
            } : {display: "flex", flexDirection: "row"}}>
                <div style={{overflow: "hidden", textOverflow: "ellipsis"}}> {this.props.file.name}</div>
                <div style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "flex-end"}}>
                    <Button
                        className={"mr-1"}
                        variant={"outline-secondary"}
                        onClick={() => this.openFileEditModal(this.props.file)}
                    >
                        <i className={"fas fa-pencil-alt"}/> </Button>
                    <Button
                        onClick={() => this.props.fileRemoveCallback()}
                    >
                        <i className={"fa fa-times"}/> </Button>
                </div>
                <EducaModal size={"lg"} closeButton={true} ref={this.educaModalRef}/>
            </div>
        );
    }

}
