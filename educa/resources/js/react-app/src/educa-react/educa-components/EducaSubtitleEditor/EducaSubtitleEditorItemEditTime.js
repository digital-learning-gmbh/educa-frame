import React, { useState } from "react";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import Button from "react-bootstrap/Button";
import {
    PLAYER_TIME_CORRECTION,
    secondsToSubtitleTime,
    subtitleTimeToSeconds,
    VTT_TIME_FORMAT_REGEX,
} from "./EducaSubtitleEditorHelper";
import { FormControl } from "react-bootstrap";
import ReactTooltip from "react-tooltip";

export default function EducaSubtitleEditorItemEditTime({
    video,
    part,
    subtitle,
    setSubtitle,
    index,
}) {
    const [edit, setEdit] = useState("");

    return (
        <div
            style={{
                display: "flex",
                columnGap: "0.4rem",
                alignItems: "center",
            }}
        >
            <div style={{ display: edit === "start" ? undefined : "none" }}>
                <TimeStartEdit
                    video={video}
                    part={part}
                    setEdit={setEdit}
                    subtitle={subtitle}
                    setSubtitle={setSubtitle}
                    index={index}
                />
            </div>
            <div style={{ display: edit === "start" ? "none" : undefined }}>
                <TimeStartDisplay video={video} part={part} setEdit={setEdit} />
            </div>
            <div style={{ display: edit === "end" ? undefined : "none" }}>
                <TimeEndEdit
                    video={video}
                    part={part}
                    setEdit={setEdit}
                    subtitle={subtitle}
                    setSubtitle={setSubtitle}
                    index={index}
                />
            </div>
            <div style={{ display: edit === "end" ? "none" : undefined }}>
                <TimeEndDisplay video={video} part={part} setEdit={setEdit} />
            </div>
        </div>
    );
}

function TimeStartDisplay({ video, part, setEdit }) {
    return (
        <ButtonGroup>
            <Button
                variant={
                    part.start === video?.progress?.playedWholeSeconds
                        ? "success"
                        : "primary"
                }
                data-for="start-seek"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                onClick={() => {
                    video?.player?.current?.seekTo(
                        part.start / video?.duration + PLAYER_TIME_CORRECTION
                    );
                }}
            >
                {secondsToSubtitleTime(part.start)}
            </Button>
            <Button
                data-for="start-edit"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                onClick={() => setEdit("start")}
            >
                <i className="fas fa-edit" />
            </Button>
        </ButtonGroup>
    );
}

function TimeEndDisplay({ video, part, setEdit }) {
    return (
        <ButtonGroup>
            <Button
                variant={
                    part.end === video?.progress?.playedWholeSeconds
                        ? "success"
                        : "primary"
                }
                data-for="end-seek"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                onClick={() =>
                    video?.player?.current?.seekTo(
                        part.end / video?.duration + PLAYER_TIME_CORRECTION
                    )
                }
            >
                {secondsToSubtitleTime(part.end)}
            </Button>
            <Button
                data-for="end-edit"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                onClick={() => setEdit("end")}
            >
                <i className="fas fa-edit" />
            </Button>
        </ButtonGroup>
    );
}

function TimeStartEdit({ video, part, setEdit, subtitle, setSubtitle, index }) {
    const [time, setTime] = useState(
        secondsToSubtitleTime(part?.start).toString()
    );

    const parsedTime = subtitleTimeToSeconds(time);
    const valid =
        time.match(VTT_TIME_FORMAT_REGEX) &&
        parsedTime < part.end &&
        ((index === 0 && parsedTime >= 0) ||
            parsedTime > subtitle[index - 1]?.start);

    const onOrPreceding =
        video?.progress?.playedWholeSeconds < part.end &&
        ((index === 0 && video?.progress?.playedWholeSeconds >= 0) ||
            video?.progress?.playedWholeSeconds > subtitle[index - 1]?.start);

    const before = index === 0 ? 0 : subtitle[index - 1]?.end;

    return (
        <ButtonGroup>
            <Button
                data-for="start-current"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                disabled={!onOrPreceding}
                variant="warning"
                onClick={() =>
                    setTime(
                        secondsToSubtitleTime(
                            video?.progress?.playedWholeSeconds
                        )
                    )
                }
            >
                <i className="fas fa-bullseye" />
            </Button>
            <FormControl
                style={{
                    minWidth: "7.8rem",
                    borderRadius: 0,
                }}
                isValid={valid}
                value={time}
                onChange={(event) => setTime(event.target.value)}
            />
            <Button
                data-for="start-save"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                style={{ marginLeft: "-2.5rem", zIndex: 999 }}
                variant="success"
                disabled={!valid}
                onClick={() => {
                    if (parsedTime < before)
                        setSubtitle([
                            ...subtitle.slice(0, Math.max(index - 1, 0)),
                            {
                                ...subtitle[Math.max(index - 1, 0)],
                                end: parsedTime,
                            },
                            { ...part, start: parsedTime },
                            ...subtitle.slice(
                                Math.min(index + 1, subtitle.length),
                                subtitle.length
                            ),
                        ]);
                    else
                        setSubtitle([
                            ...subtitle.slice(0, Math.max(index, 0)),
                            { ...part, start: parsedTime },
                            ...subtitle.slice(
                                Math.min(index + 1, subtitle.length),
                                subtitle.length
                            ),
                        ]);
                    setEdit("");
                }}
            >
                <i className="fas fa-check" />
            </Button>
            <Button
                data-for="start-cancel"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                variant="danger"
                onClick={() => setEdit("")}
            >
                <i className="fas fa-times" />
            </Button>
        </ButtonGroup>
    );
}

function TimeEndEdit({ video, part, setEdit, subtitle, setSubtitle, index }) {
    const [time, setTime] = useState(
        secondsToSubtitleTime(part?.end).toString()
    );

    const parsedTime = subtitleTimeToSeconds(time);
    const valid =
        time.match(VTT_TIME_FORMAT_REGEX) &&
        parsedTime > part.start &&
        ((index === subtitle.length - 1 && parsedTime <= video?.duration) ||
            parsedTime < subtitle[index + 1]?.end);

    const onOrProceeding =
        video?.progress?.playedWholeSeconds > part.start &&
        ((index === subtitle.length - 1 &&
            video?.progress?.playedWholeSeconds <= video?.duration) ||
            video?.progress?.playedWholeSeconds < subtitle[index + 1]?.end);

    const after =
        index === subtitle.length - 1
            ? Math.floor(video?.duration)
            : subtitle[index + 1]?.start;

    return (
        <ButtonGroup>
            <Button
                data-for="end-current"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                disabled={!onOrProceeding}
                variant="warning"
                onClick={() =>
                    setTime(
                        secondsToSubtitleTime(
                            video?.progress?.playedWholeSeconds
                        )
                    )
                }
            >
                <i className="fas fa-bullseye" />
            </Button>
            <FormControl
                style={{
                    minWidth: "7.8rem",
                    borderRadius: 0,
                }}
                isValid={valid}
                value={time}
                onChange={(event) => setTime(event.target.value)}
            />
            <Button
                data-for="end-save"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                style={{ marginLeft: "-2.5rem", zIndex: 999 }}
                variant="success"
                disabled={!valid}
                onClick={() => {
                    if (parsedTime > after)
                        setSubtitle([
                            ...subtitle.slice(0, Math.max(index, 0)),
                            { ...part, end: parsedTime },
                            {
                                ...subtitle[
                                    Math.min(index + 1, subtitle.length)
                                ],
                                start: parsedTime,
                            },
                            ...subtitle.slice(
                                Math.min(index + 2, subtitle.length),
                                subtitle.length
                            ),
                        ]);
                    else
                        setSubtitle([
                            ...subtitle.slice(0, Math.max(index, 0)),
                            { ...part, end: parsedTime },
                            ...subtitle.slice(
                                Math.min(index + 1, subtitle.length),
                                subtitle.length
                            ),
                        ]);
                    setEdit("");
                }}
            >
                <i className="fas fa-check" />
            </Button>
            <Button
                data-for="end-cancel"
                aria-haspopup="true"
                aria-expanded="false"
                data-tip="tooltip"
                variant="danger"
                onClick={() => setEdit("")}
            >
                <i className="fas fa-times" />
            </Button>
        </ButtonGroup>
    );
}

export function TimeTooltips() {
    return (
        <>
            <ReactTooltip id="start-save" place="bottom">
                Startzeit speichern
            </ReactTooltip>
            <ReactTooltip id="start-cancel" place="bottom">
                Bearbeiten abbrechen
            </ReactTooltip>
            <ReactTooltip id="start-edit" place="bottom">
                Startzeit bearbeiten
            </ReactTooltip>
            <ReactTooltip id="start-seek" place="bottom">
                Zu Startzeit springen
            </ReactTooltip>
            <ReactTooltip id="start-current" place="bottom">
                Aktuelle Zeit als Startzeit setzen
            </ReactTooltip>
            <ReactTooltip id="end-save" place="top">
                Endzeit speichern
            </ReactTooltip>
            <ReactTooltip id="end-cancel" place="top">
                Bearbeiten abbrechen
            </ReactTooltip>
            <ReactTooltip id="end-edit" place="top">
                Endzeit bearbeiten
            </ReactTooltip>
            <ReactTooltip id="end-seek" place="top">
                Zu Endzeit springen
            </ReactTooltip>
            <ReactTooltip id="end-current" place="top">
                Aktuelle Zeit als Endzeit setzen
            </ReactTooltip>
        </>
    );
}
