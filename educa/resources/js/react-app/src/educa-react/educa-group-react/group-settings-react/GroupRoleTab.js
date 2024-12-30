import React, { useEffect, useRef, useState } from "react";
import {
    GROUP_SETTINGS_PERMISSIONS,
    SECTION_SETTINGS_PERMISSIONS
} from "../../FliesentischZentralrat";
import { Accordion, FormControl } from "react-bootstrap";
import Button from "react-bootstrap/Button";
import EducaHelper from "../../helpers/EducaHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SafeDeleteModal from "../../../shared/shared-components/SafeDeleteModal";
import Select, { components } from "react-select";
import { CollapsePanelComponent } from "./EducaGroupSettingsView";
import Card from "react-bootstrap/Card";

export default function GroupRoleTab(props) {
    let [preparedRoles, setPreparedRoles] = useState([]);

    let safeDeleteRef = useRef();
    useEffect(() => {
        prepare(props.roles);
    }, [props.roles]);

    let prepare = roles => {
        if (!Array.isArray(roles)) return setPreparedRoles([]);

        let prepared = [];
        roles.forEach(roleRaw => {
            let obj = {
                name: roleRaw.role.name,
                id: roleRaw.role.id,
                groupPermissions: roleRaw.groupPermissions,
                sectionPermissions: []
            };
            roleRaw.sections.forEach(s => {
                obj.sectionPermissions.push({
                    name: s.section.name,
                    id: s.section.id,
                    permissions: s.sectionPermissions
                });
            });
            prepared.push(obj);
        });
        setPreparedRoles(prepared);
    };

    let updateRole = (roleId, newRole) => {
        let roleIndex = preparedRoles.findIndex(r => r.id === roleId);
        if (roleIndex == undefined || roleIndex == null || roleIndex < 0)
            return;

        let arr = JSON.parse(JSON.stringify(preparedRoles)); //deep copy
        arr[roleIndex] = newRole;

        setPreparedRoles(arr);
    };

    let updateSectionObject = (roleId, sectionId, newSectionObject) => {
        let roleIndex = preparedRoles.findIndex(r => r.id === roleId);
        if (roleIndex == undefined || roleIndex == null || roleIndex < 0)
            return;

        let arr = JSON.parse(JSON.stringify(preparedRoles)); //deep copy

        let newRole = arr[roleIndex];

        let sectionIndex = newRole.sectionPermissions.findIndex(
            sp => sp.id === sectionId
        );
        newRole.sectionPermissions[sectionIndex] = newSectionObject;
        arr[roleIndex] = newRole;
        setPreparedRoles(arr);
    };

    let getSectionsContent = (sectionPermissionObj, roleId, disabled) => {
        return (
            <div
                key={sectionPermissionObj.id}
                style={{ display: "flex", flexDirection: "row", flex: 1 }}
                className={"mb-2"}
            >
                <div style={{ width: "250px" }}>
                    {sectionPermissionObj.name}
                </div>
                <div style={{ width: "400px" }}>
                    <RoleSelect
                        disabled={disabled}
                        options={SECTION_SETTINGS_PERMISSIONS}
                        value={sectionPermissionObj.permissions}
                        onChange={newValues => {
                            updateSectionObject(
                                roleId,
                                sectionPermissionObj.id,
                                {
                                    ...sectionPermissionObj,
                                    permissions: newValues
                                }
                            );
                        }}
                    />
                </div>
            </div>
        );
    };

    let getGroupPermissionsContent = () => {
        return (
            <div>
                <Accordion defaultActiveKey={preparedRoles.filter(r => r.name != "Besitzer")[0].id}>
                    {preparedRoles.map(r => {
                        return (
                            <div key={r.id}>
                                <Accordion.Toggle
                                    as={CollapsePanelComponent}
                                    variant="link"
                                    eventKey={r.id}
                                >
                                    {r.name}
                                </Accordion.Toggle>
                                <Accordion.Collapse eventKey={r.id}>
                                    <Card className={"m-1"}>
                                        <Card.Body>
                                            <div
                                                className={"mb-2"}
                                                style={{
                                                    display: "flex",
                                                    flexDirection: "row",
                                                    flex: 1
                                                }}
                                            >
                                                <div style={{ width: "250px" }}>
                                                    Name
                                                </div>
                                                <div>
                                                    <FormControl
                                                        value={r.name}
                                                        disabled={r.name == "Besitzer"}
                                                        onChange={evt =>
                                                            updateRole(r.id, {
                                                                ...r,
                                                                name:
                                                                    evt.target
                                                                        .value
                                                            })
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                style={{
                                                    display: "flex",
                                                    flexDirection: "row",
                                                    flex: 1
                                                }}
                                                className={"mb-2"}
                                            >
                                                <div style={{ width: "250px" }}>
                                                    Gruppenrechte
                                                </div>
                                                <div style={{ width: "400px" }}>
                                                    <RoleSelect
                                                        options={
                                                            GROUP_SETTINGS_PERMISSIONS
                                                        }
                                                        value={
                                                            r.groupPermissions
                                                        }
                                                        disabled={r.name == "Besitzer"}
                                                        onChange={grpPermsNew => {
                                                            updateRole(r.id, {
                                                                ...r,
                                                                groupPermissions: grpPermsNew
                                                            });
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                            {r?.sectionPermissions?.map(sp => {
                                                return getSectionsContent(
                                                    sp,
                                                    r.id,
                                                    r.name == "Besitzer"
                                                );
                                            })}
                                            {
                                                r.name != "Besitzer" ?
                                            <div
                                                style={{
                                                    display: "flex",
                                                    flexDirection: "row",
                                                    justifyContent: "flex-end"
                                                }}
                                            >
                                                <Button
                                                    className={"mr-1"}
                                                    variant={"primary"}
                                                    onClick={() => saveRoles(r)}
                                                >
                                                    Rolle speichern
                                                </Button>
                                                <Button
                                                    variant={"danger"}
                                                    onClick={() =>
                                                        deleteRoleModalOpen(r)
                                                    }
                                                >
                                                    Rolle löschen
                                                </Button>
                                            </div> : <div>
                                                        <Button
                                                            className={"mr-1"}
                                                            variant={"primary"}
                                                            onClick={() => resetRoles(r)}
                                                        >
                                                            Rolle zurücksetzen
                                                        </Button>

                                                    </div> }
                                        </Card.Body>
                                    </Card>
                                </Accordion.Collapse>
                            </div>
                        );
                    })}
                </Accordion>
            </div>
        );
    };

    let resetRoles = role => {
        if (!role)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Interner Fehler. Bitte aktualisiere die Seite."
            );

        if (!role.name)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Bitte gebe einen gültigen Rollennamen an."
            );

        console.log(role)
        console.log(props.group)

        saveRoles({
            ...role,
            groupPermissions: GROUP_SETTINGS_PERMISSIONS.map((o) => o.value),
            sectionPermissions: props.group.sections.map((section) => {
                return {
                    ...section,
                    permissions: SECTION_SETTINGS_PERMISSIONS.map((o) => o.value)
                }
            })
        })
    }

    let saveRoles = role => {
        if (!role)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Interner Fehler. Bitte aktualisiere die Seite."
            );

        if (!role.name)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Bitte gebe einen gültigen Rollennamen an."
            );

        let object = {};
        object["name"] = role.name;
        object["group"] = role.groupPermissions;
        object["sections"] = [];
        role.sectionPermissions.forEach(sp => {
            object["sections"].push({
                section_id: sp.id,
                permissions: sp.permissions
            });
        });
        AjaxHelper.updateGroupRole(props.groupId, role.id, object)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.group) {
                    props.setGroup(resp.payload.group);
                    return EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Die Rolle wurden erfolgreich updated."
                    );
                }

                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Rolle '" +
                        object.name +
                        "' konnte nicht updated werden." +
                        err.message
                );
            });
    };

    let createNewRole = () => {
        AjaxHelper.addGroupRole(props.groupId)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.group) {
                    props.setGroup(resp.payload.group);
                    return EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Eine neue Rolle wurde erstellt."
                    );
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Eine neue Rolle konnte nicht erstellt werden. " +
                        err.message
                );
            });
    };
    let deleteRole = id => {
        AjaxHelper.deleteGroupRole(props.groupId, id)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.group) {
                    props.setGroup(resp.payload.group);
                    return EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Die Rolle wurde erfolgreich gelöscht."
                    );
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die rolle konnte nicht gelöscht werden. " + err.message
                );
            });
    };

    let deleteRoleModalOpen = role => {
        if (!role)
            return EducaHelper.fireErrorToast(
                "Fehler",
                "Interner Fehler. Bitte aktualisiere die Seite."
            );

        const keyword = "LÖSCHEN";
        safeDeleteRef.current?.open(
            flag => {
                if (flag) deleteRole(role.id);
            },
            "Rolle Löschen",
            "Wenn du die Rolle '" +
                role.name +
                "' wirklich löschen möchtest gebe '" +
                keyword +
                "' in das Feld ein",
            keyword
        );
    };

    if (!preparedRoles || preparedRoles.length === 0) return null;

    return (
        <div
            style={{ width: "750px", marginRight: "auto", marginLeft: "auto" }}
        >
            <div className={"mb-2 mt-2 float-right"}>
                <Button
                    variant={"primary"}
                    onClick={() => {
                        createNewRole();
                    }}
                    size={"medium"}
                >
                    Rolle hinzufügen
                </Button>
            </div>
            <div className={"clearfix"}></div>
            {getGroupPermissionsContent()}
            <SafeDeleteModal ref={safeDeleteRef} />
        </div>
    );
}

const customStylesSelect = {
    menu: provided => ({ ...provided, zIndex: 9999 })
};

const MultiValueLabelComponent = props => {
    return (
        <div style={{ display: "flex", flexDirection: "row" }}>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    justifyContent: "center"
                }}
                className={"m-1"}
            >
                <i className={props.data?.iconClass}></i>
            </div>
            <components.MultiValueLabel {...props} />
        </div>
    );
};
const OptionComponent = props => {
    let newProps = { ...props };
    newProps.children = (
        <div style={{ display: "flex", flexDirection: "row" }}>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    justifyContent: "center"
                }}
                className={"m-1"}
            >
                <i className={props.data?.iconClass} />
            </div>
            {props.data.label}{" "}
        </div>
    );
    return (
        <div>
            <components.Option {...newProps} />
        </div>
    );
};

function RoleSelect(props) {
    let value = props.value?.map(p => {
        return props.options.find(grpPrm => grpPrm.value === p);
    });

    return (
        <Select
            styles={customStylesSelect}
            components={{
                MultiValueLabel: MultiValueLabelComponent,
                Option: OptionComponent
            }}
            noOptionsMessage={() => "Keine Rechte"}
            closeMenuOnSelect={false}
            placeholder={"Rechte auswählen..."}
            isMulti={true}
            value={value}
            isDisabled={props.disabled}
            onChange={selectedValues => {
                if (!selectedValues) return props.onChange([]);
                props.onChange(selectedValues.map(v => v.value));
            }}
            options={props.options}
        />
    );
}
