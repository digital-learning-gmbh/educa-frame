import React, {Component, useState} from "react";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import { Collapse, InputGroup } from "react-bootstrap";
import { CloudIdSelectMultiple } from "../../../shared/shared-components/EducaSelects";
import Button from "react-bootstrap/Button";
import { EducaDefaultTable } from "../../../shared/shared-components/Tables";
import { connect } from "react-redux";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import Card from "react-bootstrap/Card";
import Select from "react-select";
import AddMemberModal from "./components/AddMemberModal.js";

const MEMBER_TABLE_COLUMNS = [
    {
        Header: "Name",
        accessor: "name" // accessor is the "key" in the data
    },
    {
        Header: "E-Mail",
        accessor: "email" // accessor is the "key" in the data
    },
    {
        Header: "Rollen",
        accessor: "role",
        sortType: (rowA, rowB, id, desc) => {
            let a = rowA.original["roleStr"];
            let b = rowB.original["roleStr"];
            return a?.localeCompare(b);
        }
    },
    {
        Header: "",
        accessor: "control"
    }
];

class GroupMembersTab extends Component {
    constructor(props) {
        super(props);

        this.state = {
            addUserActive: false,
            addUserList: [],
            showMemberModal: false,
        };

        this.modalRef = React.createRef();
    }

    deleteMember(member) {
        if (this.state.noPermission) return;
        AjaxHelper.removeGroupMember(this.props.group.id, member.id)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.group) {
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Das Mitglied wurde erfolgreich aus der Gruppe entfernt."
                    );
                    this.props.setGroup(resp.payload.group);
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Das Mitglied konnte nicht aus der Gruppe entfernt werden. " +
                        err.messages
                );
            });
    }

    deleteMemberClicked(member) {
        if (this.state.noPermission) return;
        let callback = btn => {
            if (btn === MODAL_BUTTONS.YES) this.deleteMember(member);
        };
        this.modalRef.current.open(
            callback,
            "Mitglied entfernen",
            "Soll das Mitglied '" +
                member.name +
                "' wirklich aus der Gruppe " +
                this.props.group.name +
                " entfernt werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        );
    }

    addUsers(addUserList) {
        if (this.state.noPermission) return;
        if (addUserList?.length === 0)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Keine Nutzer ausgewählt."
            );

        let ids = addUserList.map(usr => usr.id);

        AjaxHelper.addGroupMember(this.props.group.id, ids)
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        addUserList.length +
                            " Nutzer wurde bzw. wurden der Gruppe hinzugefügt."
                    );
                    this.setState({ showMemberModal: false });
                    //Update in parent
                    this.props.setGroup(resp.payload.group);
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Das Mitglied konnte nicht aus der Gruppe entfernt werden. " +
                        err.messages
                );
            });
    }

    render() {
        return (
            <div>
                <Card>
                    <Card.Body>
                    <EducaDefaultTable
                        size={"lg"}
                        pageSize={20}
                        pagination={true}
                        globalFilter={true}
                        buttonExcelExport={true}
                        columnPicker={true}
                        customButtonBarComponents={[
                            FliesentischZentralrat.groupEditMember(
                                this.props.group
                            ) ? <Button variant={"primary"} onClick={() => this.setState({ showMemberModal: true})}>
                                <i className="fas fa-user-plus"></i>{" "}
                                Mitglied hinzufügen
                            </Button> : null
                        ]}
                        columns={MEMBER_TABLE_COLUMNS}
                        data={this.props.group.members.map(mem => {
                            return {
                                ...mem,
                                role: (
                                    <RoleSelectBox
                                        value={
                                            mem.role
                                        }
                                        mem={mem}
                                        setGroup={this.props.setGroup}
                                        group={this.props.group}
                                    />
                                ),
                                roleStr:
                                    mem.role?.length > 0
                                        ? mem.role[0]?.name
                                        : "",
                                control: (
                                    <div>
                                        {FliesentischZentralrat.groupEditMember(
                                            this.props.group
                                        ) &&
                                        mem.id !==
                                            this.props.store.currentCloudUser
                                                .id ? (
                                            <Button
                                                className={"btn-danger ml-1"}
                                                onClick={() => {
                                                    this.deleteMemberClicked(
                                                        mem
                                                    );
                                                }}
                                            >
                                                <i className={"fa fa-trash"} />
                                            </Button>
                                        ) : null}
                                    </div>
                                )
                            };
                        })}
                    /></Card.Body>
                </Card>
                <AddMemberModal  addUsers={(userList) => this.addUsers(userList)} show={this.state.showMemberModal} close={() => this.setState({ showMemberModal: false})} group={this.props.group} />
                <EducaModal ref={this.modalRef} />
            </div>
        );
    }

}

const mapStateToProps = state => ({ store: state });

export default connect(mapStateToProps)(GroupMembersTab);

function RoleSelectBox(props) {
    let [isDisabled, setIsDisabled] = useState(false);

    if (!props.group?.roles) return <div>Fehler</div>;
    if (!FliesentischZentralrat.groupEditMember(props.group))
        return <div>{props.value?.name}</div>;



    let editMemberClicked = (member, newRole) => {
        if (!member.id || !newRole) {
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Ein Nutzer muss mindestens eine Rolle haben."
            );
        }
        setIsDisabled(true)

        let updateMember = btn => {
            AjaxHelper.updateGroupMember(
                props.group.id,
                member.id,
                newRole.map(role => role.id)
            )
                .then(resp => {
                    if (resp.status > 0 && resp.payload?.group) {
                        props.setGroup(resp.payload.group);
                    }
                })
                .catch(err => {
                    EducaHelper.fireErrorToast(
                        "Fehler",
                        "Die Rolle konnte nicht zugewiesen werden. " +
                        err.message
                    );
                }).finally(() => {
                    setIsDisabled(false)
            });
        };
        updateMember();
        // this.modalRef.current.open(
        //     updateMember,
        //     "Rolle bearbeiten",
        //     "Soll dem Mitglied '" +
        //         member.name +
        //         "' die Rolle '" +
        //         newRole.name +
        //         "' zugewiesen werden?",
        //     [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        // );
    }

    return (
        <Select
            placeholder={"Rolle"}
            getOptionLabel={option => option.name}
            getOptionValue={option => option.id}
            menuPortalTarget={document.body}
            menuPlacement="auto"
            isMulti={true}
            isDisabled={isDisabled}
            onChange={role =>
                editMemberClicked(props.mem, role)
            }
            value={props.value}
            options={props.group.roles}
        />
    );
}
