import {
    secondsToSubtitleTime,
    SUBTITLE_EDITOR_BETWEEN_OFFSET,
} from "./EducaSubtitleEditorHelper";
import { ListGroupItem } from "react-bootstrap";
import Button from "react-bootstrap/Button";
import React from "react";

export default function EducaSubtitleEditorItemAdd({
    video,
    currentRef,
    subtitle,
    setSubtitle,
    index,
}) {
    const active =
        (index === -SUBTITLE_EDITOR_BETWEEN_OFFSET ||
            video?.progress?.playedWholeSeconds >=
                subtitle[index - SUBTITLE_EDITOR_BETWEEN_OFFSET]?.end) &&
        (index === subtitle.length - SUBTITLE_EDITOR_BETWEEN_OFFSET ||
            video?.progress?.playedWholeSeconds <=
                subtitle[index + SUBTITLE_EDITOR_BETWEEN_OFFSET]?.start);

    const before =
        index === -SUBTITLE_EDITOR_BETWEEN_OFFSET
            ? 0
            : subtitle[Math.max(index - SUBTITLE_EDITOR_BETWEEN_OFFSET, 0)]
                  ?.end;
    const after =
        index === subtitle.length - SUBTITLE_EDITOR_BETWEEN_OFFSET
            ? Math.floor(video?.duration)
            : subtitle[
                  Math.min(
                      index + SUBTITLE_EDITOR_BETWEEN_OFFSET,
                      subtitle.length
                  )
              ]?.start;

    return (
        <ListGroupItem
            ref={active ? currentRef : undefined}
            variant={active ? "dark" : ""}
            style={{ display: "flex", justifyContent: "space-between" }}
        >
            <div style={{ display: "flex", columnGap: "2.9rem" }}>
                <Button
                    disabled
                    variant={
                        video?.progress?.playedWholeSeconds === before
                            ? "success"
                            : "primary"
                    }
                >
                    {secondsToSubtitleTime(before)}
                </Button>
                <Button
                    disabled
                    variant={
                        video?.progress?.playedWholeSeconds === after
                            ? "success"
                            : "primary"
                    }
                >
                    {secondsToSubtitleTime(after)}
                </Button>
            </div>
            <Button
                variant="success"
                style={{
                    display: "flex",
                    alignItems: "center",
                    columnGap: "0.5rem",
                }}
                onClick={() =>
                    setSubtitle([
                        ...subtitle.slice(
                            0,
                            Math.max(index + SUBTITLE_EDITOR_BETWEEN_OFFSET, 0)
                        ),
                        {
                            start: before,
                            end: after,
                            content: " ",
                        },
                        ...subtitle.slice(
                            Math.min(
                                index + SUBTITLE_EDITOR_BETWEEN_OFFSET,
                                subtitle.length
                            )
                        ),
                    ])
                }
            >
                <i className="fas fa-plus" />
                Abschnitt hinzufügen
            </Button>
        </ListGroupItem>
    );
}
