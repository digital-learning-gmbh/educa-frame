export const VTT_HEADER = "WEBVTT\n\n";
export const VTT_DIVIDER = "\n\n";
export const VTT_PART_TIME_DIVIDER = " --> ";
export const VTT_PART_CONTENT_DIVIDER = "\n";
export const VTT_TIME_FORMAT_REGEX = /^\d{2}:\d{2}:\d{2}$/;
export const VTT_TIME_ADDITION = ".000";
export const PLAYER_TIME_CORRECTION = 0.0001;
export const SUBTITLE_EDITOR_BETWEEN_OFFSET = 0.5;

export function vttValid(vtt) {
    return vtt?.startsWith(VTT_HEADER);
}

export function vttToObject(vtt) {
    return vtt
        .slice(VTT_HEADER.length)
        .split(VTT_DIVIDER)
        .map((subtitle) => {
            const subtitleParts = subtitle.split(VTT_PART_CONTENT_DIVIDER);
            const subtitleTimeParts = subtitleParts[0].split(
                VTT_PART_TIME_DIVIDER
            );

            return {
                start: subtitleTimeToSeconds(
                    subtitleTimeParts[0]?.split(".")[0]
                ),
                end: subtitleTimeToSeconds(subtitleTimeParts[1]?.split(".")[0]),
                content: subtitleParts[1],
            };
        });
}

export function objectToVTT(object) {
    const vttParts = object.map(
        (subtitle) =>
            secondsToSubtitleTime(subtitle.start) +
            VTT_TIME_ADDITION +
            VTT_PART_TIME_DIVIDER +
            secondsToSubtitleTime(subtitle.end) +
            VTT_TIME_ADDITION +
            VTT_PART_CONTENT_DIVIDER +
            subtitle.content
    );
    return VTT_HEADER + vttParts.join(VTT_DIVIDER);
}

export function subtitleTimeToSeconds(time) {
    const timeParts = time.split(":");

    const hours = parseInt(timeParts[0], 10);
    const minutes = parseInt(timeParts[1], 10);
    const seconds = parseInt(timeParts[2], 10);

    return (hours * 60 + minutes) * 60 + seconds;
}

export function secondsToSubtitleTime(seconds) {
    const hours = Math.floor(seconds / (60 * 60));
    seconds %= 60 * 60;
    const minutes = Math.floor(seconds / 60);
    seconds %= 60;

    const formattedHours = String(hours).padStart(2, "0");
    const formattedMinutes = String(minutes).padStart(2, "0");
    const formattedSeconds = String(seconds).padStart(2, "0");

    return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
}
