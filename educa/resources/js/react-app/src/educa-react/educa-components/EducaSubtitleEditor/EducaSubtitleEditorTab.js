import React, { useEffect, useRef, useState } from "react";
import { ListGroup } from "react-bootstrap";
import EducaSubtitleEditorItemAdd from "./EducaSubtitleEditorItemAdd";
import { SUBTITLE_EDITOR_BETWEEN_OFFSET } from "./EducaSubtitleEditorHelper";
import EducaSubtitleEditorItemEdit, {
    RemoveTooltips,
} from "./EducaSubtitleEditorItemEdit";
import { TimeTooltips } from "./EducaSubtitleEditorItemEditTime";

export default function EducaSubtitleEditorTab(props) {
    const [subtitle, setSubtitle] = useState(props.subtitle);

    const currentRef = useRef();

    useEffect(() => {
        currentRef?.current?.scrollIntoView({
            behavior: "smooth",
            block: "center",
            inline: "start",
        });
    }, [currentRef?.current]);

    function saveSubtitle(subtitle) {
        setSubtitle(subtitle);
        props.setSubtitle(subtitle);
    }

    return (
        <>
            <ListGroup key={subtitle.length}>
                {subtitle.map((part, index) => (
                    <React.Fragment key={index}>
                        {(index !== 0 &&
                            part.start !== subtitle[index - 1].end) ||
                        (index === 0 && part.start > 0) ? (
                            <EducaSubtitleEditorItemAdd
                                video={props.video}
                                currentRef={currentRef}
                                subtitle={subtitle}
                                setSubtitle={saveSubtitle}
                                index={index - SUBTITLE_EDITOR_BETWEEN_OFFSET}
                            />
                        ) : null}
                        <EducaSubtitleEditorItemEdit
                            video={props.video}
                            currentRef={currentRef}
                            subtitle={subtitle}
                            setSubtitle={saveSubtitle}
                            index={index}
                            part={part}
                        />
                        {index === subtitle.length - 1 &&
                        Math.floor(part.end) !==
                            Math.floor(props.video?.duration) ? (
                            <EducaSubtitleEditorItemAdd
                                video={props.video}
                                currentRef={currentRef}
                                subtitle={subtitle}
                                setSubtitle={saveSubtitle}
                                index={index + SUBTITLE_EDITOR_BETWEEN_OFFSET}
                            />
                        ) : null}
                    </React.Fragment>
                ))}
            </ListGroup>
            <RemoveTooltips />
            <TimeTooltips />
        </>
    );
}
