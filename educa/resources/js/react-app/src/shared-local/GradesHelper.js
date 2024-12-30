export const PASSED_OPTIONS =
    {
        PASSED: {value: 1, label: "Bestanden"},
        NOT_PASSED: {value: 5, label: "Nicht Bestanden"}
    }
export const ATTENDANCE_OPTIONS =
    {
        PASSED: {value: 1, label: "Teilgenommen"},
        NOT_PASSED: {value: 5, label: "Nicht teilgenommen"}
    }


export const PART_EXAM_RATING = {
    ATTENDANCE: "took_part",
    PASSING: "passed",
    GRADING: "graded",
    POINTS: "points",
    THESIS: "thesis",
}

class GradesHelperClass
{

    isGrade(rating)
    {
        return rating == PART_EXAM_RATING.GRADING || rating === PART_EXAM_RATING.THESIS
    }

    isPoints(rating)
    {
        return rating == PART_EXAM_RATING.POINTS
    }

    isAttendance(rating)
    {
        return rating == PART_EXAM_RATING.ATTENDANCE
    }

    isPassing(rating)
    {
        return rating == PART_EXAM_RATING.PASSING
    }


}

const GradesHelper = new GradesHelperClass()
export default GradesHelper
