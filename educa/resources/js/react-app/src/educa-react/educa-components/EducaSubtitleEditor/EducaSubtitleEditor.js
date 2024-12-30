import { Alert, Modal } from "react-bootstrap";
import React, { useRef, useState } from "react";
import {
    EDUCA_LANGUAGES_ACTIVATED,
    EducaLanguageSelect,
} from "../EducaLanguageSelect";
import {
    objectToVTT,
    secondsToSubtitleTime,
    vttToObject,
    vttValid,
} from "./EducaSubtitleEditorHelper";
import ReactPlayer from "react-player";
import EducaSubtitleEditorTab from "./EducaSubtitleEditorTab";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import { redux_store } from "../../../../store";

export default function EducaSubtitleEditor({
    documentId,
    video,
    subtitles,
    setSubtitles,
}) {
    const player = useRef();

    const [language, setLanguage] = useState(subtitles[0]?.language);
    const subtitle = subtitles.find((item) => item.language === language);

    const [playing, setPlaying] = useState(false);
    const [progress, setProgress] = useState({});
    const [duration, setDuration] = useState(0);

    return (
        <div
            style={{
                display: "flex",
                flexDirection: "column",
                rowGap: "2%",
                height: "80vh",
                maxHeight: "80vh",
            }}
        >
            <div
                style={{
                    width: "100%",
                    minWidth: "100%",
                    maxWidth: "100%",
                    height: "43%",
                    minHeight: "43%",
                    maxHeight: "43%",
                }}
            >
                <ReactPlayer
                    ref={player}
                    playsinline
                    style={{ minWidth: "100%", minHeight: "100%" }}
                    controls
                    playing={playing}
                    url={video}
                    progressInterval={50}
                    onStart={() => setPlaying(true)}
                    onPlay={() => setPlaying(true)}
                    onPause={() => setPlaying(false)}
                    onEnded={() => setPlaying(false)}
                    onDuration={setDuration}
                    onProgress={(progress) =>
                        setProgress({
                            ...progress,
                            playedWholeSeconds: Math.floor(
                                progress.playedSeconds
                            ),
                        })
                    }
                />
            </div>
            {subtitles.length > 0 ? (
                <>
                    <div
                        style={{
                            width: "100%",
                            minWidth: "100%",
                            maxWidth: "100%",
                            height: "3%",
                            maxHeight: "3%",
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                columnGap: "1rem",
                                alignItems: "center",
                            }}
                        >
                            <Button
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    columnGap: "0.5rem",
                                }}
                                disabled
                            >
                                <i className="fas fa-clock" />
                                {secondsToSubtitleTime(
                                    progress?.playedWholeSeconds
                                )}
                            </Button>
                            <div style={{ minWidth: "13rem", width: "13rem" }}>
                                <EducaLanguageSelect
                                    show={subtitles.map(
                                        (item) => item.language
                                    )}
                                    value={language}
                                    onChange={(event) =>
                                        setLanguage(event.code)
                                    }
                                />
                            </div>
                        </div>
                        <div
                            style={{
                                display: "flex",
                                columnGap: "1rem",
                                alignItems: "center",
                            }}
                        >
                            <AddLanguage
                                documentId={documentId}
                                setLanguage={setLanguage}
                                subtitles={subtitles}
                                setSubtitles={setSubtitles}
                            />
                            <Button
                                variant="success"
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    columnGap: "0.5rem",
                                }}
                                onClick={
                                    subtitle.id
                                        ? () =>
                                              AjaxHelper.editSubtitle(
                                                  documentId,
                                                  subtitle.id,
                                                  subtitle.subtitle
                                              )
                                                  .then((response) => {
                                                      if (response.status > 0)
                                                          EducaHelper.fireSuccessToast(
                                                              "Erfolg",
                                                              "Der Untertitel wurde erfolgreich gespeichert."
                                                          );
                                                      else throw new Error("");
                                                  })
                                                  .catch((_) =>
                                                      EducaHelper.fireErrorToast(
                                                          "Fehler",
                                                          "Der Untertitel konnte nicht gespeichert werden."
                                                      )
                                                  )
                                        : () =>
                                              AjaxHelper.addSubtitle(
                                                  documentId,
                                                  language,
                                                  subtitle.subtitle
                                              )
                                                  .then((response) => {
                                                      if (
                                                          response.status > 0 &&
                                                          response.payload
                                                              ?.subtitle
                                                      ) {
                                                          EducaHelper.fireSuccessToast(
                                                              "Erfolg",
                                                              "Der Untertitel wurde erfolgreich erstellt."
                                                          );

                                                          const index =
                                                              subtitles.findIndex(
                                                                  (item) =>
                                                                      item.language ===
                                                                      language
                                                              );
                                                          setSubtitles([
                                                              ...subtitles.slice(
                                                                  0,
                                                                  Math.max(
                                                                      index - 1,
                                                                      0
                                                                  )
                                                              ),
                                                              {
                                                                  ...response
                                                                      .payload
                                                                      .subtitle,
                                                              },
                                                              ...subtitles.slice(
                                                                  Math.min(
                                                                      index + 1,
                                                                      subtitles.length
                                                                  )
                                                              ),
                                                          ]);
                                                      } else
                                                          throw new Error("");
                                                  })
                                                  .catch((_) =>
                                                      EducaHelper.fireErrorToast(
                                                          "Fehler",
                                                          "Der Untertitel konnte nicht erstellt werden."
                                                      )
                                                  )
                                }
                            >
                                <i className="fas fa-check" />
                                Sprache{" "}
                                {subtitle?.id ? "speichern" : "erstellen"}
                            </Button>
                        </div>
                    </div>
                    <div
                        style={{
                            width: "100%",
                            minWidth: "100%",
                            maxWidth: "100%",
                            height: "50%",
                            maxHeight: "50%",
                            overflowY: "auto",
                        }}
                    >
                        {vttValid(subtitle?.subtitle) ? (
                            <EducaSubtitleEditorTab
                                key={language}
                                video={{
                                    player,
                                    playing,
                                    progress,
                                    duration,
                                }}
                                subtitle={vttToObject(subtitle.subtitle)}
                                setSubtitle={(newSubtitle) => {
                                    const index = subtitles.findIndex(
                                        (item) => item.language === language
                                    );
                                    setSubtitles([
                                        ...subtitles.slice(
                                            0,
                                            Math.max(index - 1, 0)
                                        ),
                                        {
                                            ...subtitle,
                                            subtitle: objectToVTT(newSubtitle),
                                        },
                                        ...subtitles.slice(
                                            Math.min(
                                                index + 1,
                                                subtitles.length
                                            )
                                        ),
                                    ]);
                                }}
                            />
                        ) : (
                            <Alert variant="danger">
                                Ungültige Formatierung der Untertitel
                            </Alert>
                        )}
                    </div>
                </>
            ) : (
                <div
                    style={{
                        width: "100%",
                        display: "flex",
                        justifyContent: "center",
                    }}
                >
                    <AddLanguage
                        documentId={documentId}
                        setLanguage={setLanguage}
                        subtitles={subtitles}
                        setSubtitles={setSubtitles}
                    />
                </div>
            )}
        </div>
    );
}

function AddLanguage({ documentId, setLanguage, subtitles, setSubtitles }) {
    const store = redux_store.getState();
    const EDUCA_LANGUAGES = EDUCA_LANGUAGES_ACTIVATED(store);

    const [show, setShow] = useState(false);
    const [add, setAdd] = useState();

    return (
        <>
            <Button
                style={{
                    display: "flex",
                    alignItems: "center",
                    columnGap: "0.5rem",
                }}
                disabled={EDUCA_LANGUAGES.length === subtitles.length}
                onClick={() => setShow(true)}
            >
                <i className="fas fa-plus" />
                Sprache hinzufügen
            </Button>
            <Modal show={show} onHide={() => setShow(false)}>
                <Modal.Header closeButton>
                    <Modal.Title>Untertitelsprache hinzufügen</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <EducaLanguageSelect
                        hide={subtitles.map((item) => item.language)}
                        value={add}
                        onChange={(event) => setAdd(event.code)}
                    />
                </Modal.Body>
                <Modal.Footer>
                    <Button
                        style={{
                            display: "flex",
                            alignItems: "center",
                            columnGap: "0.5rem",
                        }}
                        disabled={!add}
                        onClick={() => {
                            setSubtitles([
                                ...subtitles,
                                {
                                    dokument_id: documentId,
                                    language: add,
                                    subtitle: objectToVTT([
                                        {
                                            start: 0,
                                            end: 1,
                                            content:
                                                "Untertitel Eintrag von Sekunde 0 bis Sekunde 1",
                                        },
                                    ]),
                                },
                            ]);

                            setShow(false);

                            setLanguage(add);
                        }}
                    >
                        Sprache hinzufügen
                    </Button>
                </Modal.Footer>
            </Modal>
        </>
    );
}
