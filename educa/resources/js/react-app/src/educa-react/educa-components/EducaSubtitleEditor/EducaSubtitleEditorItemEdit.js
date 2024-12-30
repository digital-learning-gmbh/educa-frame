import React, { useState } from "react";
import { InputGroup, ListGroupItem } from "react-bootstrap";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import Button from "react-bootstrap/Button";
import ReactTooltip from "react-tooltip";
import EducaSubtitleEditorItemEditTime from "./EducaSubtitleEditorItemEditTime";
import { EducaTextArea } from "../../../shared/shared-components/EducaTextArea";

export default function EducaSubtitleEditorItemEdit({
    video,
    currentRef,
    subtitle,
    setSubtitle,
    part,
    index,
}) {
    const active =
        video?.progress?.playedWholeSeconds >= part.start &&
        video?.progress?.playedWholeSeconds <= part.end;

    return (
        <ListGroupItem
            style={{
                display: "flex",
                columnGap: "0.8rem",
                alignItems: "center",
            }}
            ref={active ? currentRef : undefined}
            variant={active ? "dark" : ""}
        >
            <EducaSubtitleEditorItemEditTime
                video={video}
                part={part}
                subtitle={subtitle}
                setSubtitle={setSubtitle}
                index={index}
            />
            <Content
                part={part}
                subtitle={subtitle}
                setSubtitle={setSubtitle}
                index={index}
            />
            <Remove
                subtitle={subtitle}
                setSubtitle={setSubtitle}
                index={index}
            />
        </ListGroupItem>
    );
}

function Content({ index, part, subtitle, setSubtitle }) {
    const [content, setContent] = useState(part.content);

    return (
        <InputGroup
            style={{
                flexGrow: 9999,
            }}
        >
            <EducaTextArea
                style={{
                    flexGrow: 9999,
                }}
                value={content}
                onChange={(event) => setContent(event.target.value)}
            />
            {content !== part.content ? (
                <Button
                    data-for="save-changes"
                    aria-haspopup="true"
                    aria-expanded="false"
                    data-tip="tooltip"
                    variant="success"
                    style={{
                        borderTopLeftRadius: 0,
                        borderBottomLeftRadius: 0,
                    }}
                    onClick={() =>
                        setSubtitle([
                            ...subtitle.slice(0, index),
                            { ...part, content },
                            ...subtitle.slice(
                                Math.min(index + 1, subtitle.length)
                            ),
                        ])
                    }
                >
                    <i className="fas fa-check" />
                </Button>
            ) : null}
        </InputGroup>
    );
}

function Remove({ index, subtitle, setSubtitle }) {
    const [remove, setRemove] = useState(false);

    return (
        <>
            {!remove ? (
                <Button
                    style={{ aspectRatio: 1 }}
                    disabled={subtitle.length <= 1}
                    onClick={() => setRemove(true)}
                    variant="danger"
                >
                    <i className="fas fa-trash" />
                </Button>
            ) : null}
            <ButtonGroup style={{ display: !remove ? "none" : undefined }}>
                <Button
                    data-for="remove-confirm"
                    aria-haspopup="true"
                    aria-expanded="false"
                    data-tip="tooltip"
                    variant="danger"
                    style={{ aspectRatio: 1 }}
                    disabled={subtitle.length <= 1}
                    onClick={() =>
                        setSubtitle([
                            ...subtitle.slice(0, Math.max(index, 0)),
                            ...subtitle.slice(
                                Math.min(index + 1, subtitle.length)
                            ),
                        ])
                    }
                >
                    <i className="fas fa-check" />
                </Button>
                <Button
                    data-for="remove-cancel"
                    aria-haspopup="true"
                    aria-expanded="false"
                    data-tip="tooltip"
                    onClick={() => setRemove(false)}
                    variant="danger"
                    style={{ aspectRatio: 1 }}
                >
                    <i className="fas fa-times" />
                </Button>
            </ButtonGroup>
        </>
    );
}

export function RemoveTooltips() {
    return (
        <>
            <ReactTooltip id="remove-confirm" place="bottom">
                Löschen bestätigen
            </ReactTooltip>
            <ReactTooltip id="remove-cancel" place="bottom">
                Löschen abbrechen
            </ReactTooltip>
        </>
    );
}
