import React from "react";
import FliesentischZentralrat from "../../../FliesentischZentralrat";
import { Collapse, ListGroup } from "react-bootstrap";
import { GROUP_SECTION_OVERVIEW_APP, GROUP_VIEWS } from "../GroupBrowse";

export default function GroupBrowseSideSections(props) {
    if (!Array.isArray(props.group.sections))
        return (
            <ListGroup.Item className={"bg-transparent"}>
                Noch keine {props.t("sections")}
            </ListGroup.Item>
        );

    return props.group.sections.map(section => {
        if (!FliesentischZentralrat.sectionViewSection(section)) return null;

        const selected = props.section && props.section.id === section.id;

        return (
            <div key={section.id}>
                <ListGroup.Item
                    style={{
                        display: "flex",
                        flexDirection: "row",
                        columnGap: "1rem",
                        alignItems: "center",
                        cursor: "pointer"
                    }}
                    active={selected}
                    key={section.id}
                    onClick={() =>
                        props.navigate([
                            props.group.id,
                            GROUP_VIEWS.SECTIONS,
                            section.id,
                            GROUP_SECTION_OVERVIEW_APP
                        ])
                    }
                    className={"bg-transparent border-0"}
                >
                    {selected ? (
                        <i className="fas fa-chevron-circle-down"></i>
                    ) : (
                        <i className="fas fa-chevron-circle-right"></i>
                    )}
                    {selected ? <b>{section.name}</b> : section.name}
                </ListGroup.Item>
                <Collapse in={selected}>
                    <ListGroup bg={"transparent"} variant="flush">
                        {props.section
                            ? props.section.section_group_apps.map(app => {
                                  if (
                                      !app ||
                                      !app.group_app ||
                                      (app.group_app.type === "announcement" &&
                                          !FliesentischZentralrat.sectionAnnouncementView(
                                              props.section
                                          )) ||
                                      (app.group_app.type === "accessCode" &&
                                          !FliesentischZentralrat.sectionAccesscodeView(
                                              props.section
                                          )) ||
                                      (app.group_app.type === "chat" &&
                                          !FliesentischZentralrat.sectionMessagesView(
                                              props.section
                                          )) ||
                                      (app.group_app.type === "calendar" &&
                                          !FliesentischZentralrat.sectionCalendarView(
                                              props.section
                                          )) ||
                                      (app.group_app.type === "files" &&
                                          !FliesentischZentralrat.sectionFilesView(
                                              props.section
                                          )) ||
                                      (app.group_app.type === "task" &&
                                          !FliesentischZentralrat.sectionTaskView(
                                              props.section
                                          ))
                                  )
                                      return;

                                  const appIconClass = app?.group_app?.icon
                                      ? "fa " + app.group_app.icon
                                      : null;

                                  const selected =
                                      app.id === props.sectionApp?.id;

                                  return (
                                      <ListGroup.Item
                                          active={selected}
                                          key={app.id}
                                          onClick={() =>
                                              props.navigate([
                                                  props.group.id,
                                                  GROUP_VIEWS.SECTIONS,
                                                  props.section.id,
                                                  app.group_app.type
                                              ])
                                          }
                                          style={{
                                              cursor: "pointer"
                                          }}
                                          as="li"
                                          className={"bg-transparent border-0"}
                                      >
                                          <div
                                              style={{
                                                  marginLeft: "2.03rem",
                                                  display: "flex",
                                                  flexDirection: "row",
                                                  justifyContent:
                                                      "space-between",
                                                  alignItems: "center"
                                              }}
                                          >
                                              <div
                                                  style={{
                                                      display: "flex",
                                                      flexDirection: "row",
                                                      columnGap: "0.5rem",
                                                      alignItems: "center",
                                                      fontWeight: selected
                                                          ? "bold"
                                                          : "normal"
                                                  }}
                                              >
                                                  {app?.group_app?.icon.lastIndexOf("/") !== -1 ?
                                                      <img src={app?.group_app?.icon} style={{height: "20px", width: "20px"}} />
                                                      : <i
                                                      className={appIconClass}
                                                  ></i> }
                                                  {app.name}
                                              </div>
                                              <i
                                                  className="fas fa-chevron-right"
                                                  style={{ fontSize: "0.7rem" }}
                                              ></i>
                                          </div>
                                      </ListGroup.Item>
                                  );
                              })
                            : null}
                    </ListGroup>
                </Collapse>
            </div>
        );
    });
}
