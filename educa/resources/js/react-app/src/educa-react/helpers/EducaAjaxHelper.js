import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import EventManager, { isUserLoggedIn } from "../EducaEventManager";
import {
    COOKIE_RC_ACCESS_TOKEN,
    COOKIE_RC_UID_TOKEN,
} from "../rocket-chat-components/RocketChatHelper";
import EducaHelper from "./EducaHelper";

let BASE = "/api/v1/";

import axios from 'axios';

const API_LOCALES = "locales";
//Forms
const API_ADMINISTRATION_FORMS = "forms/{form_id}";
const API_ADMINISTRATION_FORMS_REVISION_SAVE =
    "forms/{form_id}/revision/{revision_id}";

// Tenants
const API_TENANT_CONFIG = "tenants/currentConfig";

//Auth
const API_LOGIN = "loginReact";
const API_REGISTER = "registerReact";
const API_LOGOUT = "logoutReact";
const API_SWITCH_BACK = "administration/masterdata/users/switchBackUser";
const API_QRCODE = "qrCode";
const API_ME_SESSIONS = "me/sessionToken";
const API_ME_SESSION_CLOSE = "me/sessionToken/{session_id}/close";
const API_HOME_SECTION = "feed/sections";
const API_HOME_LAST_SECTION = "feed/sections/lastseen";
const API_ME_GROUP_SETTINGS = "me/groupSettings"
const API_ME_GROUP_CLUSTERS = "me/groupClusters"
const API_ME_GROUP_CLUSTERS_FAVORITES = "me/groupClusters/favorites"
//Settings
const API_SETTINGS = "settings";
const API_SETTINGS_UPDATE = "settings/general/save";
const API_SETTINGS_SECURITY = "settings/general/security";
const API_SETTINGS_PASSWORD_UPDATE = "settings/general/updatePassword";
const API_SETTINGS_2FA_TOGGLE = "settings/general/2FAToggle";
const API_SETTINGS_2FA_QRCODE = "settings/general/2FAqrCode";
const API_SETTINGS_IMAGE_UPDATE = "settings/general/updateProfileImage";
const API_SETTINGS_APP = "settings/{appName}";
const API_SETTINGS_APP_SAVE = "settings/{appName}/save";
const API_SETTINGS_ANALYTICS_DOWNLOAD = "settings/analytics/xapi/download";

// Code
const API_CHECK_CODE_IN_APP = "code/inApp";
const API_CHECK_CODE = "code/check";
const API_CREATE_CODE = "code/createAccount";
const API_CHECK_RECOVER_OPTIONS = "login/recovery";
const API_RECOVER_SEND_EMAIL = "login/recovery/sendMail";
const API_RECOVER_EXECUTE = "login/recovery/execute";
const API_RECOVER_RESET = "login/recovery/resetPassword";

//wiki
const API_WIKI_LIST = "wiki";
const API_WIKI_CREATE = "wiki/create";
const API_WIKI_DELETE = "wiki/delete";
const API_WIKI_UPDATE = "wiki/update";
const API_WIKI_SEARCH = "wiki/search";
const API_WIKI_UPLOAD_IMAGE = "wiki/uploadImage";

//Groups
const API_GROUPS_ALL = "groups";
const API_GROUP_CREATE = "groups/create";
const API_GROUP_ADD_SECTION = "groups/{groupId}/section";
const API_GROUP_ARCHIVE = "groups/{groupId}/archive";
const API_GROUP_DELETE = "groups/{groupId}/delete";
const API_GROUP_EXTERNAL_INTEGRATION_ADD =
    "groups/{groupId}/externalIntegration/add";
const API_GROUP_EXTERNAL_INTEGRATION_REMOVE =
    "groups/{groupId}/externalIntegration/{external_integration_id}/remove";

const API_ADMINISTRATION_TIMETABLE = "administration/timetable/teaching";
const API_ADMINISTRATION_TIMETABLE_TIMEFRAME =
    "administration/timetable/timeframe";

const API_GROUP_TEMPLATE = "grouptemplates";
const API_GROUP_TEMPLATE_CREATE_GROUP =
    "grouptemplates/template/{template_id}/create";
const API_GROUP_TEMPLATE_DELETE =
    "grouptemplates/template/{template_id}/delete";
const API_GROUP_TEMPLATE_CREATE_FROM_GROUP =
    "grouptemplates/createTemplateFromGroup";

const API_GROUP_SECTION_GET_ALL_APPS = "groups/apps/all";
const API_GROUP_GROUP_SETTINGS = "groups/{groupId}/settings";
const API_GROUP_FEED = "groups/{groupId}/feed?lastTime={timestamp}";
const API_GROUP_SECTION_GET_ACCESS_CODE = "groups/{groupId}/code";
const API_GROUP_ADD_MEMBER = "groups/{groupId}/members/add";
const API_GROUP_REMOVE_MEMBER = "groups/{groupId}/members/remove";
const API_GROUP_UPDATE_MEMBER = "groups/{groupId}/members/update";
const API_GROUP_UPDATE_ROLE = "groups/{groupId}/roles/{roleId}/update";
const API_GROUP_ADD_ROLE = "groups/{groupId}/roles/add";
const API_GROUP_DELETE_ROLE = "groups/{groupId}/roles/{roleId}/delete";

//Section
const API_SECTION_CHAT = "groups/sections/{sectionId}/chat";
const API_SECTION_GET_AVAILABLE_APPS =
    "groups/sections/{sectionId}/apps/available";
const API_SECTION_ADD_APP = "groups/sections/{sectionId}/apps/add";
const API_SECTION_REMOVE_APP = "groups/sections/{sectionId}/apps/remove";
const API_SECTION_ANNOUNCEMENTS = "groups/sections/{sectionId}/announcements";
const API_GROUP_SECTION_GET_MEETING = "groups/sections/{sectionId}/meeting";
const API_GROUP_SECTION_GET_OPENCAST = "groups/sections/{sectionId}/opencast";
const API_SECTION_UPDATE = "groups/sections/{sectionId}/update";
const API_SECTION_UPDATE_IMAGE = "groups/sections/{sectionId}/updateSectionImage";
const API_SECTION_REMOVE = "groups/sections/{sectionId}/remove";
const API_SECTIONS_REORDER = "groups/{groupId}/sections/reorder";
const API_SECTION_ANNOUNCEMENT_TEMPLATES =
    "groups/sections/{sectionId}/announcementtemplates";
const API_SECTION_MEMBERS = "groups/sections/{sectionId}/members";
const API_SECTION_EVENTS = "groups/sections/{sectionId}/sectionEvents";
const API_SECTION_TASKS = "groups/sections/{sectionId}/sectionTasks";
const API_SECTION_INTERACTIVE_COURSE_INFORMATION =
    "groups/sections/{sectionId}/educaCourse";
const API_SECTION_INTERACTIVE_COURSE_LINK =
    "groups/sections/{sectionId}/educaCourse/{courseId}";

// Interactive Course

const API_INTERACTIVE_COURSE_CREATE = "educaCourse/create";
const API_INTERACTIVE_COURSE_UPLOAD = "educaCourse/upload";
const API_INTERACTIVE_COURSE_LIST = "educaCourse/list";
const API_INTERACTIVE_COURSE_GET = "educaCourse/{courseId}";
const API_INTERACTIVE_COURSE_REORDER_CHAPTERS =
    "educaCourse/{courseId}/reorder";
const API_INTERACTIVE_COURSE_ADD_CHAPTER = "educaCourse/{courseId}/chapter/add";
const API_INTERACTIVE_COURSE_IMAGE = "/api/image/interactive_course?id";
const API_INTERACTIVE_COURSE_SAVE_CHAPTER =
    "educaCourse/{courseId}/chapter/{chapterId}/save";
const API_INTERACTIVE_COURSE_DELETE_CHAPTER =
    "educaCourse/{courseId}/chapter/{chapterId}/delete";
const API_INTERACTIVE_COURSE_SAVE = "educaCourse/{courseId}";
const API_INTERACTIVE_COURSE_CHAPTER_EXPORT =
    "educaCourse/{courseId}/chapter/{chapterId}/export";
const API_INTERACTIVE_COURSE_EXPORT = "educaCourse/{courseId}/export";

const API_INTERACTIVE_COURSE_ADD_TOPIC =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/add";
const API_INTERACTIVE_COURSE_SAVE_TOPIC =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/{topicId}/save";
const API_INTERACTIVE_COURSE_DELETE_TOPIC =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/{topicId}/delete";
const API_INTERACTIVE_COURSE_ADD_VARIANT =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/{topicId}/variant";
const API_INTERACTIVE_COURSE_SAVE_VARIANT =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/{topicId}/variant/{variantId}";
const API_INTERACTIVE_COURSE_DELETE_VARIANT =
    "educaCourse/{courseId}/chapter/{chapterId}/topic/{topicId}/variant/{variantId}/delete";
const API_INTERACTIVE_COURSE_REORDER_TOPICS =
    "educaCourse/{courseId}/chapter/{chapterId}/reorder";
const API_INTERACTIVE_COURSE_ANSWERS = "educaCourse/{courseId}/chapter/answers";
const API_H5P_RESET_USER_CONTENT_DATA = "h5p/resetUserData";
const API_H5P_IMPORT_COURSE = "h5p/import";
const API_INTERACTIVE_COURSE_ANALYTICS = "educaCourse/{courseId}/analytics";
const API_INTERACTIVE_COURSE_ANALYTICS_STATEMENTS = "educaCourse/{courseId}/analytics/statements";
const API_INTERACTIVE_COURSE_ANALYTICS_XAPI = "educaCourse/{courseId}/analytics/userCentric";
const API_INTERACTIVE_COURSE_H5P_ANALYTICS =
    "educaCourse/{courseId}/analytics/h5p";
const API_INTERACTIVE_COURSE_TASK_ANALYTICS =
    "educaCourse/{courseId}/analytics/task";

const SCORM_UPLOAD_URL = "scorm/upload";
const SCORM_PLAYER_URL = "scorm/play/{content-id}";
const SCORM_DOWNLOAD_URL = "scorm/download/{content-id}";
const CMI5_PLAYER_URL = "cmi5/play/{content-id}";
const CMI5_UPLOAD_URL = "cmi5/upload";

/* LearnContent */
const API_LEARN_CONTENT_TAGS = "learnContent/tags/{learnContentId}";
const API_LEARN_CONTENT_TAGS_ALL = "learnContent/tags";

const API_LEARN_CONTENT_GET = "learnContent/content/{learnContentId}";
const API_LEARN_CONTENT_BROWSE = "learnContent/browse";
const API_LEARN_CONTENT_CREATE = "learnContent/create";
const API_LEARN_CONTENT_IMPORT = "learnContent/import";
const API_LEARN_CONTENT_DOWNLOAD =
    "learnContent/content/{learnContentId}/download";
const API_LEARN_CONTENT_XAPI_DOWNLOAD =
    "learnContent/content/{learnContentId}/xapi/download";
const API_LEARN_CONTENT_DELETE = "learnContent/content/{learnContentId}/delete";
const API_LEARN_CONTENT_UPDATE_METADATA =
    "learnContent/content/{learnContentId}/metadata";

const API_LEARN_CONTENT_CATEGORY = "learnContent/category";
const API_LEARN_CONTENT_CATEGORY_DETAILS = "learnContent/category/{categoryId}";
const API_LEARN_CONTENT_CATEGORY_UPDATE =
    "learnContent/category/{categoryId}/update";
const API_LEARN_CONTENT_CATEGORY_UPDATE_IMAGE =
    "learnContent/category/{categoryId}/image";
const API_LEARN_CONTENT_CATEGORY_DELETE =
    "learnContent/category/{categoryId}/delete";
const API_LEARN_CONTENT_CATEGORY_IMAGE = "/api/image/category?id";

const API_LEARN_CONTENT_BOOKMARK_LIST_CREATE = "learnContent/bookmark/list";
const API_LEARN_CONTENT_BOOKMARK_LIST_GET =
    "learnContent/bookmark/list/{listId}";
const API_LEARN_CONTENT_BOOKMARK_LIST_EDIT =
    "learnContent/bookmark/list/{listId}/edit";
const API_LEARN_CONTENT_BOOKMARK_LIST_DELETE =
    "learnContent/bookmark/list/{listId}/delete";
const API_LEARN_CONTENT_BOOKMARK_LIST_ADD =
    "learnContent/bookmark/list/{listId}/add/{learnContentId}";
const API_LEARN_CONTENT_BOOKMARK_LIST_REMOVE =
    "learnContent/bookmark/list/{listId}/remove/{learnContentId}";
const API_LEARN_CONTENT_BOOKMARK_LISTS_WITH_ITEM =
    "learnContent/bookmark/lists/{learnContentId}";
const API_LEARN_CONTENT_BOOKMARK_LISTS_BY_ME = "learnContent/bookmark/lists";

const API_LEARN_CONTENT_LIKES_COUNT = "learnContent/likes/{learnContentId}";
const API_LEARN_CONTENT_LIKES_ADD = "learnContent/likes/{learnContentId}/add";
const API_LEARN_CONTENT_LIKES_REMOVE =
    "learnContent/likes/{learnContentId}/remove";

const API_LEARN_CONTENT_COMPETENCES =
    "learnContent/competences/{learnContentId}";
const API_LEARN_CONTENT_COMPETENCE_ADD =
    "learnContent/competences/{learnContentId}/add/{competenceId}";
const API_LEARN_CONTENT_COMPETENCE_UPDATE_POINTS =
    "learnContent/competences/{learnContentId}/points/{competenceId}";
const API_LEARN_CONTENT_COMPETENCE_REMOVE =
    "learnContent/competences/{learnContentId}/remove/{competenceId}";

const API_LEARN_CONTENT_COMMENTS =
    "learnContent/comments/content/{learnContentId}";
const API_LEARN_CONTENT_COMMENTS_WRITE =
    "learnContent/comments/content/{learnContentId}/write";
const API_LEARN_CONTENT_COMMENTS_DELETE =
    "learnContent/comments/post/{commentId}/delete";

const API_LEARN_CONTENT_PERMISSIONS =
    "learnContent/permissions/{learn_content_id}";
const API_LEARN_CONTENT_PERMISSIONS_ADD_TENANT =
    "learnContent/permissions/{learn_content_id}/addTenant";
const API_LEARN_CONTENT_PERMISSIONS_UPDATE_TENANT =
    "learnContent/permissions/{learn_content_id}/updateTenant";
const API_LEARN_CONTENT_PERMISSIONS_DELETE_TENANT =
    "learnContent/permissions/{learn_content_id}/deleteTenant";
const API_LEARN_CONTENT_PERMISSIONS_ADD_USER =
    "learnContent/permissions/{learn_content_id}/addUser";
const API_LEARN_CONTENT_PERMISSIONS_SAVE_USER =
    "learnContent/permissions/{learn_content_id}/updateUser";
const API_LEARN_CONTENT_PERMISSIONS_DELETE_USER =
    "learnContent/permissions/{learn_content_id}/deleteUser";

const API_LEARN_CONTENT_TRANSLATE =
    "learnContent/translate/{learnContentId}/translate";
const API_LEARN_CONTENT_GET_TRANSLATE =
    "learnContent/translate/{learnContentId}";

const API_INTERACTIVE_COURSE_BADGE_IMAGE = "/api/image/badge?id";
const API_INTERACTIVE_COURSE_BADGE_LIST =
    "interactiveCourses/{interactive_course_id}/interactiveCourseBadges/list";
const API_INTERACTIVE_COURSE_BADGE_CREATE =
    "interactiveCourses/{interactive_course_id}/interactiveCourseBadges/create";
const API_INTERACTIVE_COURSE_BADGE_UPDATE =
    "interactiveCourses/{interactive_course_id}/interactiveCourseBadges/{interactive_course_badge_id}/update";
const API_INTERACTIVE_COURSE_BADGE_DELETE =
    "interactiveCourses/{interactive_course_id}/interactiveCourseBadges/{interactive_course_badge_id}/delete";

const API_INTERACTIVE_COURSE_LEVEL_LIST = "interactiveCourses/{interactive_course_id}/interactiveCourseLevels/list";
const API_INTERACTIVE_COURSE_LEVEL_CREATE = "interactiveCourses/{interactive_course_id}/interactiveCourseLevels/create";
const API_INTERACTIVE_COURSE_LEVEL_UPDATE = "interactiveCourses/{interactive_course_id}/interactiveCourseLevels/{level_id}/update";
const API_INTERACTIVE_COURSE_LEVEL_DELETE = "interactiveCourses/{interactive_course_id}/interactiveCourseLevels/{level_id}/delete";

const API_INTERACTIVE_COURSE_ATTENDEE = "interactiveCourses/{interactive_course_id}/executions";
const API_INTERACTIVE_COURSE_EXECUTION_CREATE =
    "interactiveCourses/{interactive_course_id}/executions/create";

const API_INTERACTIVE_COURSE_EXECUTION_PROGRESS_CREATE =
    "interactiveCourses/{interactive_course_id}/executions/{interactive_course_execution_id}/progress/create";
const API_INTERACTIVE_COURSE_EXECUTION_PROGRESS_UPDATE =
    "interactiveCourses/{interactive_course_id}/executions/{interactive_course_execution_id}/progress/update";

//Masterdata
const API_MASTERDATA_ALL_CLOUD_USERS = "masterdata/allcloudusers";
const API_MASTERDATA_ROOMS = "masterdata/rooms";
const API_SEARCH_GET_CURRENT_USER = "me";

//Feed
const API_MAIN_FEED = "feed";
const API_FEED_TASKS = "feed/tasks";
const API_FEED_EVENTS = "feed/events";
const API_FEED_INTERACTIVE_COURSE = "feed/interactiveCourse";
const API_FEED_BADGE = "feed/badge";
const API_FEED_STATISTICS = "feed/statistics";

//Announcements
const API_ANNOUNCEMENT_BY_ID = "announcements/{announcementId}/";
const API_SECTION_ANNOUNCEMENTS_PREVIEW = "announcements/{sectionId}/announcementsPreview";
const API_ANNOUNCEMENT_TOGGLE_LIKE = "announcements/{beitragId}/like";
const API_ANNOUNCEMENT_CREATE_COMMENT = "announcements/{beitragId}/comment";
const API_ANNOUNCEMENT_HIDE_COMMENT =
    "announcements/{beitragId}/comment/{commentId}/toggleHidden";
const API_ANNOUNCEMENT_EDIT_COMMENT =
    "announcements/{beitragId}/comment/{commentId}/edit";
const API_ANNOUNCEMENT_HISTORY_COMMENT =
    "announcements/{beitragId}/comment/{commentId}/history";
const API_ANNOUNCEMENT_UPDATE = "announcements/{beitragId}/update";
const API_ANNOUNCEMENT_DELETE = "announcements/{beitragId}/delete";
const API_ANNOUNCEMENT_TEMPLATE_DELETE =
    "announcementtemplates/{templateId}/delete";
const API_ANNOUNCEMENT_TEMPLATE_UPDATE =
    "announcementtemplates/{templateId}/update";

//Messages
const API_IM_REPORT = "messages/report";
const API_IM_LIST_CLOUDID_MAPPING = "messages/imlist";
const API_IM_CREATE_CHAT = "messages/createimchat";
const API_IM_CREATE_GROUP_CHAT = "messages/creategroupchat";

//Calendar / Events

const API_EVENTS = "events";
const API_EVENTS_OUTLOOK_TOKEN = "events/outlook/shareToken"
const API_EVENTS_UPDATE = "events/{eventId}";
const API_EVENTS_CREATE = "events/create";
const API_EVENTS_DETAILS = "events/{eventId}/details";
const API_EVENTS_DELETE = "events/{eventId}/delete";
const API_EVENTS_INVITES = "events/invites";
const API_EVENTS_UPDATE_STATUS = "events/{eventId}/status";
const API_EVENTS_SINGLE_CANCEL = "events/{eventId}/single/cancel";
const API_EVENTS_SINGLE_MOVE = "events/{eventId}/single/move";
const API_EVENTS_SINGLE_DELETE =
    "events/{eventId}/single/{singleAppointmentId}/delete";
const API_EVENTS_SINGLE_UPDATE =
    "events/{eventId}/single/{singleAppointmentId}/update";

const API_EVENTS_TIMETABLE = "events/timetable";
const API_EVENTS_CHECK_USERS = "events/checkUsers";

//Classbook

const API_CLASSBOOK_TEACHING_INFO = "classbook/teaching/info";
const API_CLASSBOOK_TEACHING_MEMBERS = "classbook/teaching/members";
const API_CLASSBOOK_TEACHING_SAVE = "classbook/teaching/save";
const API_WIDGET_ABSENTEESIM = "classbook/absenteeism";
const API_WIDGET_MARKS = "classbook/marks";
const API_WIDGET_EXAM_DATES = "classbook/examDates";

//Task templates
const API_TASK_TEMPLATES = "tasktemplates";
const API_TASK_TEMPLATES_CREATE = "tasktemplates/create";
const API_TASK_TEMPLATES_UPDATE = "tasktemplates/{taskTemplateId}";
const API_TASK_TEMPLATES_DETAILS = "tasktemplates/{taskTemplateId}/details";
const API_TASK_TEMPLATES_DELETE = "tasktemplates/{taskTemplateId}/delete";
const API_TASK_TEMPLATES_CREATE_TASK = "tasktemplates/{taskTemplateId}/create";
const API_TASK_TEMPLATES_CREATE_TASK_INTERACTIVE_COURSE =
    "tasktemplates/{taskTemplateId}/interactiveCourseCreate";
const API_TASK_TEMPLATES_GET_TASK_INTERACTIVE_COURSE =
    "tasktemplates/{taskTemplateId}/interactiveCourse";
const API_TASK_TEMPLATES_UPDATE_FORM_TEMPLATE =
    "tasktemplates/{taskTemplateId}/formTemplate";

//Tasks
const API_TASKS = "tasks";
const API_TASKS_CREATE = "tasks/create";
const API_TASKS_UPDATE = "tasks/{taskId}";
const API_TASKS_UPDATE_FORM_TEMPLATE = "tasks/{taskId}/formTemplate";
const API_TASKS_CLOSE = "tasks/{taskId}/close";
const API_TASKS_DETAILS = "tasks/{taskId}/details";
const API_TASKS_DELETE = "tasks/{taskId}/delete";
const API_TASKS_CONTENT_UPDATE = "tasks/{taskId}/content";
const API_TASKS_FINISH_UPDATE = "tasks/{taskId}/finishTaskUpdate";
const API_TASKS_ARCHIVED = "tasks/archived";

//Tasks -> Submissions
const API_TASKS_SUBMISSIONS = "tasks/{taskId}/submissions";
const API_TASKS_SUBMISSION_CREATE = "tasks/submissions/create";
const API_TASKS_SUBMISSION_DETAILS =
    "tasks/{taskId}/submissions/{submissionId}";
const API_TASKS_SUBMISSION_UPDATE = "tasks/{taskId}/submissions/{submissionId}";
const API_TASKS_SUBMISSION_COMPLETEALL = "tasks/{taskId}/completeSubmissions";
const API_TASKS_SUBMISSION_RESET =
    "tasks/{taskId}/submissions/{submissionId}/reset";
const API_TASKS_AI_RATE_SUBMISSION = "tasks/{taskId}/aiSubmissions";

// Documents

const API_DOCUMENTS = "documents";
const API_DOCUMENTS_FOLDERS = "documents/folders";
const API_DOCUMENTS_MOVE = "documents/move";
const API_DOCUMENTS_MOVE_OR_COPY = "documents/moveOrCopy";
const API_DOCUMENTS_DELETE = "documents/delete";
const API_DOCUMENTS_ZIP = "documents/zip";
const API_DOCUMENTS_RENAME = "documents/rename";
const API_DOCUMENTS_LIST = "documents/list";
const API_DOCUMENTS_QUERY = "documents/ask";
const API_DOCUMENTS_DOWNLOAD = "documents/{documentId}/download";
const API_DOCUMENTS_SUBTITLES = "documents/{documentId}/subtitles";
const API_DOCUMENTS_SUBTITLE_DOWNLOAD =
    "documents/{documentId}/subtitles/{subtitleId}";
const API_DOCUMENTS_SUBTITLE_ADD = "documents/{documentId}/subtitles";
const API_DOCUMENTS_SUBTITLE_EDIT =
    "documents/{documentId}/subtitles/{subtitleId}";
const WEB_DOCUMENTS_OPEN = "/dokument/office/?file={documentId}";
const API_DOCUMENT = "documents/{documentId}";
const API_DOCUMENT_DETAILS = "documents/{documentId}/details";
const API_DOCUMENTS_UPDATE = "documents/{document_id}/update"

// MEETING
const API_MEETINGS_JOIN = "meetings/{modelType}/{id}/join";
const API_MEETINGS_INFO = "meetings/{modelType}/{id}/info";
const API_MEETINGS_LIVE_INFO = "meetings/{modelType}/{id}/live";
//Misc

const API_CLOUD_ID_AVATAR = "/api/image/cloud?cloud_id";
const API_GROUP_ID_AVATAR = "/api/image/group?id";
const API_SECTION_ID_AVATAR = "/api/image/section?id";
const API_SEND_SUPPORT = "support";
const API_SUPPORT_LIST = "support/list";
const API_SUPPORT_TICKET_GET = "support/ticket/{ticket_id}";
const API_SUPPORT_TICKET_CLOSE = "support/ticket/{ticket_id}/close";
const API_SUPPORT_TICKET_FILE = "support/ticket/{ticket_id}/file";

const API_BLOCK_USER_SUPPORT = "support/block";
const API_REPORT_SUPPORT = "support/report";

const API_CLOUD_USER_LIST = "cloud/user/list";
const API_CLOUD_USER_DETAILS = "cloud/user/{cloud_id}";
const API_CLOUD_USER_ROLES_EDIT = "cloud/user/{cloud_id}/roles";
const API_CLOUD_USERS_IMPORT = "cloud/user/import";
const API_CLOUD_USERS_CREATE = "cloud/user/create";
const API_CLOUD_USERS_UPDATE = "cloud/user/{cloud_id}/update";
const API_CLOUD_USERS_DELETE = "cloud/user/{cloud_id}/delete";

const API_CLOUD_TENANTS = "cloud/tenants";
const API_CLOUD_TENANTS_CREATE = "cloud/tenants/create";
const API_CLOUD_TENANTS_GET = "cloud/tenants/{tenant_id}/get";
const API_CLOUD_TENANTS_UPDATE = "cloud/tenants/{tenant_id}/update";
const API_CLOUD_TENANTS_DELETE = "cloud/tenants/{tenant_id}/delete";

const API_CLOUD_ANALYTICS_REPORTS = "cloud/analytics/reports";

const API_CLOUD_PERMISSIONS_ROLES = "cloud/permissions/roles";
const API_CLOUD_PERMISSIONS_ROLES_CREATE = "cloud/permissions/roles/create";
const API_CLOUD_PERMISSIONS_ROLES_EDIT =
    "cloud/permissions/roles/{role_id}/edit";
const API_CLOUD_PERMISSIONS_ROLES_DELETE =
    "cloud/permissions/roles/{role_id}/delete";

const API_CLOUD_PERMISSIONS = "cloud/permissions/";
const API_CLOUD_PERMISSIONS_FLIP = "cloud/permissions/flip";

const API_CLOUD_GROUP_LIST = "cloud/groups";
const API_CLOUD_GROUP_DELETE = "cloud/groups/{group_id}/delete";
const API_CLOUD_GROUP_UNARCHIVE = "cloud/groups/{group_id}/unarchive";

const API_CLOUD_MAINTENANCE = "cloud/maintenance";

const API_CLOUD_GENERAL_CHART_INFO = "cloud/general";
const API_CLOUD_GENERAL_CHART_NEW_USERS = "cloud/general/widgets/new_users";
const API_CLOUD_GENERAL_CHART_ACTIVE_USERS =
    "cloud/general/widgets/active_users";
const API_CLOUD_GENERAL_CHART_ACTIVITY = "cloud/general/widgets/activity";
const API_CLOUD_GENERAL_CHART_OBJECTS = "cloud/general/widgets/objects";
const API_CLOUD_GENERAL_CHART_LEARN_CONTENT_OBJECTS =
    "cloud/general/widgets/learnContentObjects";
const API_CLOUD_GENERAL_CHART_FEEDS = "cloud/general/widgets/feeds";
const API_CLOUD_GENERAL_CHART_SPACES = "cloud/general/widgets/spaces";

/* Competences & CompetenceClusters */
const API_CLOUD_COMPETENCE_CLUSTERS = "cloud/competenceCluster";
const API_CLOUD_COMPETENCE_CLUSTER_CREATE = "cloud/competenceCluster/create";
const API_CLOUD_COMPETENCE_CLUSTER_UPDATE =
    "cloud/competenceCluster/{clusterId}/update";
const API_CLOUD_COMPETENCE_CLUSTER_DELETE =
    "cloud/competenceCluster/{clusterId}/delete";
const API_CLOUD_COMPETENCE_CLUSTER_COMPETENCES =
    "cloud/competenceCluster/{clusterId}/competences";
const API_CLOUD_COMPETENCE = "cloud/competence/{competenceId}";
const API_CLOUD_COMPETENCE_ALL = "cloud/competence/all";
const API_CLOUD_COMPETENCE_CREATE =
    "cloud/competenceCluster/{clusterId}/competence";
const API_CLOUD_COMPETENCE_UPDATE = "cloud/competence/{competenceId}/update";
const API_CLOUD_COMPETENCE_DELETE = "cloud/competence/{competenceId}/delete";

const API_CLOUD_WORKFLOWS = "cloud/workflow";
const API_CLOUD_WORKFLOWS_ADD = "cloud/workflow/add";
const API_CLOUD_WORKFLOWS_AVAILABLE_NODES = "cloud/workflow/getAvailableNodes";
const API_CLOUD_WORKFLOW_DETAILS = "cloud/workflow/{workflow_id}/details";
const API_CLOUD_WORKFLOW_START = "cloud/workflow/{workflow_id}/start";
const API_CLOUD_WORKFLOW_UPDATE = "cloud/workflow/{workflow_id}/update";

const API_CLOUD_INSTANCE_DETAILS =
    "cloud/workflow/instances/{instance_id}/details";
const API_CLOUD_INSTANCE_PAUSE = "cloud/workflow/instances/{instance_id}/pause";
const API_CLOUD_INSTANCE_UNPAUSE =
    "cloud/workflow/instances/{instance_id}/unpause";
// Search
const API_SEARCH_ALL = "search";

const API_PRIVACY = "privacy";
const API_PRIVACY_ACCEPT = "privacy/accept";

// Learn Content
const API_LEARN_CONTENT_DASHBOARD = "learnContent/dashboard";
const API_LEARN_PROVIDER_CONFIGURATION =
    "learnContent/provider/{name}/configuration";
const API_LEARN_PROVIDER_OVERVIEW = "learnContent/provider/{name}/overview";
const API_UPLOAD_SHARE_FILES = "learnContent/provider/shareFiles/upload";
const API_LEARN_PROVIDER_YOUTUBE_VIDEO_INFO =
    "learnContent/provider/youtube/{name}";

//Reports
const API_TEMPLATE_GENERATE = "formtemplates/{id}/generate";
const API_TEMPLATE_OPEN = "formtemplates/open";

const API_SEND_MASS_CORRESPONDENCE = "correspondence/multiCreate";

const API_EXTERNAL_INTEGRATION_TEMPLATES = "externalIntegration/templates";

// Explore API
const API_EXPLORE_TENANTS = "explore/tenants";
const API_EXPLORE_TENANTS_PUBLIC_GROUPS = "explore/tenants/{tenant_id}/groups";

// AI API
const API_AI_COMPLETE_TEXTBOX = "ai/completeTextbox";

// Adressbook
const API_ADDRESS_BOOK_GET = "addressbook";
const API_ADDRESS_BOOK_LIST = "addressbook/list";
const API_ADDRESS_BOOK_ADD = "addressbook/add";
const API_ADDRESS_BOOK_UPDATE = "addressbook/update";
const API_ADDRESS_BOOK_DELETE = "addressbook/delete";
const API_ADDRESS_BOOK_MAIL = "addressbook/mail";

const API_FRAME_CONIGURATION = "frame/grid-configuration";
const API_RIOS_CALL = "frame/rios";

////// Status codes 16 Bit

export const STATUS_CODE_ERROR_TOKEN_INVALID = 0xffff;


import Base64 from 'crypto-js/enc-base64';
import AES from 'crypto-js/aes';
import CryptoJS, { mode } from "crypto-js";
import Pkcs7 from 'crypto-js/pad-pkcs7'

class EducaAjaxHelper {
    _getAuthHeaderObj() {
        return { Authorization: "Bearer " + SharedHelper.getJwt() };
    }

    _enableEncryption(resp)
    {
        return resp.encrypt;
    }
    _decryptResponse(encryptStr)
    {
        encryptStr = Base64.parse(encryptStr);
        let encryptData = encryptStr.toString(CryptoJS.enc.Utf8);
        encryptData = JSON.parse(encryptData);
        let iv = Base64.parse(encryptData.iv);
        var decrypted = AES.decrypt(encryptData.value,  CryptoJS.enc.Utf8.parse("67ef74t5YPdbf8au"), {
            iv : iv,
            mode: mode.CBC,
            padding: Pkcs7
        });
        return decrypted.toString(CryptoJS.enc.Utf8);
    }

    _get(endpoint, params = {}) {
        if (!SharedHelper.getJwt()) return Promise.resolve();

        return fetch(BASE + endpoint + SharedHelper.concatParams(params), {
            method: "GET",
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
                ...this._getAuthHeaderObj(),
            },
        })
            .then((resp) => {
                if (resp.status == 499) {
                    if (isUserLoggedIn())
                        EducaHelper.fireInfoToast(
                            "Automatischer Logout",
                            "Bitte logge Dich erneut ein. "
                        );
                    this.logout();
                }

                return resp;
            })
            .then((resp) => resp.json())
            .then(resp => {
                if(this._enableEncryption(resp))
                    resp.payload = JSON.parse(this._decryptResponse(resp.payload))
                return resp
            });
    }

    _post(endpoint, payload = {}) {
        if (!SharedHelper.getJwt()) return Promise.resolve();

        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: JSON.stringify(payload),
        })
            .then((resp) => {
                if (resp.status == 499) {
                    if (isUserLoggedIn())
                        EducaHelper.fireInfoToast(
                            "Automatischer Logout",
                            "Bitte logge Dich erneut ein. "
                        );
                    this.logout();
                }

                return resp;
            })
            .then((resp) => resp.json())
            .then(resp => {
                if(this._enableEncryption(resp))
                    resp.payload = JSON.parse(this._decryptResponse(resp.payload))
                return resp
            });
    }

    getLocales() {
        return fetch(BASE + API_LOCALES, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "GET",
        }).then((resp) => resp.json())  .then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    getTenantConfig() {
        return fetch(BASE + API_TENANT_CONFIG, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "GET",
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    getQRCode() {
        return BASE + API_QRCODE + "?token=" + SharedHelper.getJwt();
    }

    /**
     *  Forms
     */
    getForm(formId, modelId, modelType) {
        return this._get(
            API_ADMINISTRATION_FORMS.replace("{form_id}", formId),
            { model_type: modelType, model_id: modelId }
        );
    }

    saveFormForRevision(formId, revisionId, modelId, modelType, form_data) {
        return this._post(
            API_ADMINISTRATION_FORMS_REVISION_SAVE.replace(
                "{form_id}",
                formId
            ).replace("{revision_id}", revisionId),
            { model_type: modelType, model_id: modelId, form_data: form_data }
        );
    }

    /**
     * Wiki
     */

    getWikiPages(modelType, modelId) {
        let params = { modelType };
        if (modelId) params["modelId"] = modelId;
        return this._get(API_WIKI_LIST, params);
    }

    createWikiPage(modelType, modelId, pageName) {
        return this._post(API_WIKI_CREATE, {
            modelType,
            modelId,
            name: pageName,
        });
    }

    deleteWikiPage(modelType, modelId, pageId) {
        return this._post(API_WIKI_DELETE, {
            modelType,
            modelId,
            page_id: pageId,
        });
    }

    updateWikiPage(modelType, modelId, pageId, page) {
        return this._post(API_WIKI_UPDATE, {
            modelType,
            modelId,
            page_id: pageId,
            page: page,
        });
    }

    searchWikiPage(modelType, modelId, search) {
        return this._post(API_WIKI_SEARCH, {
            modelType,
            modelId,
            q: search,
        });
    }

    uploadWikiPageImage(file) {
        let formData = new FormData();
        formData.append("image", file);

        return fetch(BASE + API_WIKI_UPLOAD_IMAGE, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    /**
     * Groups
     */

    getGroups() {
        return this._get(API_GROUPS_ALL);
    }

    createGroup(name, color, member_ids) {
        let payload = {};
        if (name) payload["name"] = name;
        if (color) payload["color"] = color;
        if (member_ids) payload["member_ids"] = member_ids;
        return this._post(API_GROUP_CREATE, payload);
    }

    getAvailableSectionApps(sectionId) {
        let endpoint = API_SECTION_GET_AVAILABLE_APPS.replace(
            "{sectionId}",
            sectionId
        );
        return this._get(endpoint);
    }

    getAllGroupSectionApps() {
        let endpoint = API_GROUP_SECTION_GET_ALL_APPS;
        return this._get(endpoint);
    }

    getGroupSettings(groupId) {
        let endpoint = API_GROUP_GROUP_SETTINGS.replace("{groupId}", groupId);
        return this._get(endpoint);
    }

    setGroupSettings(groupId, content) {
        let endpoint = API_GROUP_GROUP_SETTINGS.replace("{groupId}", groupId);
        return this._post(endpoint, content);
    }

    setGroupImage(groupId, image) {
        let endpoint = API_GROUP_GROUP_SETTINGS.replace("{groupId}", groupId);
        let formData = new FormData();
        formData.append("image", image);
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    setSectionImage(sectionId, image) {
        let endpoint = API_SECTION_UPDATE_IMAGE.replace("{sectionId}", sectionId);
        let formData = new FormData();
        formData.append("image", image);
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    addSectionToGroup(groupId, sectionName) {
        let endpoint = API_GROUP_ADD_SECTION.replace("{groupId}", groupId);
        return this._post(endpoint, { name: sectionName });
    }

    groupArchive(groupId) {
        let endpoint = API_GROUP_ARCHIVE.replace("{groupId}", groupId);
        return this._post(endpoint);
    }

    removeGroup(groupId) {
        let endpoint = API_GROUP_DELETE.replace("{groupId}", groupId);
        return this._post(endpoint);
    }

    createGroupExternalIntegration(groupId, obj) {
        let endpoint = API_GROUP_EXTERNAL_INTEGRATION_ADD.replace(
            "{groupId}",
            groupId
        );
        return this._post(endpoint, { external_integration: obj });
    }

    deleteGroupExternalIntegration(groupId, externalIntegrationId) {
        let endpoint = API_GROUP_EXTERNAL_INTEGRATION_REMOVE.replace(
            "{groupId}",
            groupId
        ).replace("{external_integration_id}", externalIntegrationId);
        return this._post(endpoint);
    }

    getGroupSectionAnnouncements(sectionId) {
        let endpoint = API_SECTION_ANNOUNCEMENTS.replace(
            "{sectionId}",
            sectionId
        );
        return this._get(endpoint);
    }

    addGroupSectionAnnouncement(
        sectionId,
        text,
        media,
        shouldPush,
        comments_active,
        planned_for
    ) {
        let endpoint = API_SECTION_ANNOUNCEMENTS.replace(
            "{sectionId}",
            sectionId
        );
        let formData = new FormData();
        formData.append("text", text);
        formData.append("shouldPush", shouldPush);
        formData.append("comments_active", comments_active);
        formData.append("planned_for", planned_for);
        if (media) {
            media.forEach(function (image) {
                formData.append("media[]", image);
            });
        }
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    addGroupSectionAnnouncementTemplate(sectionId, title, text, media) {
        let endpoint = API_SECTION_ANNOUNCEMENT_TEMPLATES.replace(
            "{sectionId}",
            sectionId
        );
        let formData = new FormData();
        formData.append("title", title);
        formData.append("text", text);
        if (media) {
            media.forEach(function (image) {
                formData.append("media[]", image);
            });
        }
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    deleteAnnouncementTemplate(templateId) {
        let endpoint = API_ANNOUNCEMENT_TEMPLATE_DELETE.replace(
            "{templateId}",
            templateId
        );
        return this._post(endpoint);
    }

    updateAnnouncementTemplate(
        announcementTemplateId,
        newFiles,
        fileIdsToDelete,
        newTitle,
        newContent
    ) {
        let endpoint = API_ANNOUNCEMENT_TEMPLATE_UPDATE.replace(
            "{templateId}",
            announcementTemplateId
        );

        let formData = new FormData();
        if (fileIdsToDelete)
            formData.append(
                "file_ids_to_delete",
                JSON.stringify(fileIdsToDelete)
            );
        if (newTitle) formData.append("title", newTitle);
        if (newContent) formData.append("content", newContent);

        if (newFiles) {
            newFiles.forEach(function (image) {
                formData.append("new_files[]", image);
            });
        }

        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    getGroupAccessCode(groupId) {
        let endpoint = API_GROUP_SECTION_GET_ACCESS_CODE.replace(
            "{groupId}",
            groupId
        );
        return this._get(endpoint);
    }

    getSectionMeetingDetails(sectionId) {
        let endpoint = API_GROUP_SECTION_GET_MEETING.replace(
            "{sectionId}",
            sectionId
        );
        return this._get(endpoint);
    }

    updateSectionMeetingDetails(sectionId, name, welcomeText)
    {
        let endpoint = API_GROUP_SECTION_GET_MEETING.replace(
            "{sectionId}",
            sectionId
        );
        return this._post(endpoint,{
            name,
            welcomeText
        });
    }

    getOpenCastDetails(sectionId) {
        let endpoint = API_GROUP_SECTION_GET_OPENCAST.replace(
            "{sectionId}",
            sectionId
        );
        return this._get(endpoint);
    }

    listEducaCourses() {
        return this._get(API_INTERACTIVE_COURSE_LIST);
    }

    getHomeViewSections(group_id = null) {
        return this._get(API_HOME_SECTION, {group_id: group_id});
    }

    getHomeViewLastSections() {
        return this._get(API_HOME_LAST_SECTION);
    }

    getSectionInteractiveCourse(sectionId) {
        let endpoint = API_SECTION_INTERACTIVE_COURSE_INFORMATION.replace(
            "{sectionId}",
            sectionId
        );
        return this._get(endpoint);
    }

    interactiveCourseCreate(sectionId, name) {
        return this._post(API_INTERACTIVE_COURSE_CREATE, {
            sectionId,
            name,
        });
    }

    interactiveCourseUpload(sectionId, file) {
        return this._post(API_INTERACTIVE_COURSE_UPLOAD, {
            sectionId,
            file,
        });
    }



    getInteractiveCourseImageUrl(id, size = "35", image = "") {
        return (
            API_INTERACTIVE_COURSE_IMAGE +
            "=" +
            id +
            "&size=" +
            size +
            "&name=" +
            image
        );
    }

    interactiveCourseAddChapter(courseId, name, language) {
        let endpoint = API_INTERACTIVE_COURSE_ADD_CHAPTER.replace(
            "{courseId}",
            courseId
        );
        return this._post(endpoint, { name, language });
    }

    interactiveCourseReorderChapters(courseId, updates) {
        let endpoint = API_INTERACTIVE_COURSE_REORDER_CHAPTERS.replace(
            "{courseId}",
            courseId
        );
        return this._post(endpoint, { new_order_mapping: updates });
    }

    interactiveCourseReorderTopics(courseId, chapterId, updates) {
        let endpoint = API_INTERACTIVE_COURSE_REORDER_TOPICS.replace(
            "{courseId}",
            courseId
        ).replace("{chapterId}", chapterId);
        return this._post(endpoint, { new_order_mapping: updates });
    }

    interactiveCourseDeleteChapter(courseId, chapterId) {
        let endpoint = API_INTERACTIVE_COURSE_DELETE_CHAPTER.replace(
            "{courseId}",
            courseId
        ).replace("{chapterId}", chapterId);
        return this._post(endpoint);
    }

    interactiveCourseChapterExport(courseId, chapterId) {
        return (
            BASE +
            API_INTERACTIVE_COURSE_CHAPTER_EXPORT.replace(
                "{courseId}",
                courseId
            ).replace("{chapterId}", chapterId) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    interactiveCourseExport(courseId) {
        return (
            BASE +
            API_INTERACTIVE_COURSE_EXPORT.replace("{courseId}", courseId) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    interactiveCourseAddTopic(courseId, chapterId, name, language, topicType) {
        let endpoint = API_INTERACTIVE_COURSE_ADD_TOPIC.replace(
            "{courseId}",
            courseId
        ).replace("{chapterId}", chapterId);
        return this._post(endpoint, { name, language, topicType });
    }

    h5pImportCourse(file, progress) {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };

        let endpoint = API_H5P_IMPORT_COURSE;

        let formData = new FormData();
        formData.append("import_file", file);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    interactiveCourseSaveTopic(courseId, chapterId, topicId, visibility,levels) {
        let endpoint = API_INTERACTIVE_COURSE_SAVE_TOPIC.replace(
            "{courseId}",
            courseId
        )
            .replace("{chapterId}", chapterId)
            .replace("{topicId}", topicId);
        return this._post(endpoint, {
            public: visibility,
            levels: levels
        });
    }

    interactiveCourseDeleteTopic(courseId, chapterId, topicId) {
        let endpoint = API_INTERACTIVE_COURSE_DELETE_TOPIC.replace(
            "{courseId}",
            courseId
        )
            .replace("{chapterId}", chapterId)
            .replace("{topicId}", topicId);
        return this._post(endpoint);
    }

    interactiveCourseAddVariant(
        courseId,
        chapterId,
        topicId,
        name,
        language,
        learn_content_id
    ) {
        let endpoint = API_INTERACTIVE_COURSE_ADD_VARIANT.replace(
            "{courseId}",
            courseId
        )
            .replace("{chapterId}", chapterId)
            .replace("{topicId}", topicId);
        return this._post(endpoint, {
            name,
            language,
            learn_content_id,
        });
    }

    interactiveCourseSaveVariant(
        courseId,
        chapterId,
        topicId,
        variantId,
        name,
        description,
        verb,
        finish_mode,
        required_score,
        learn_content_id
    ) {
        let endpoint = API_INTERACTIVE_COURSE_SAVE_VARIANT.replace(
            "{courseId}",
            courseId
        )
            .replace("{chapterId}", chapterId)
            .replace("{topicId}", topicId)
            .replace("{variantId}", variantId);
        return this._post(endpoint, {
            name,
            description,
            learn_content_id,
            verb,
            finish_mode,
            required_score,
        });
    }

    interactiveCourseDeleteVariant(courseId, chapterId, topicId, variantId) {
        let endpoint = API_INTERACTIVE_COURSE_DELETE_VARIANT.replace(
            "{courseId}",
            courseId
        )
            .replace("{chapterId}", chapterId)
            .replace("{topicId}", topicId)
            .replace("{variantId}", variantId);
        return this._post(endpoint);
    }

    interactiveCourseLoadAnswers(courseId) {
        let endpoint = API_INTERACTIVE_COURSE_ANSWERS.replace(
            "{courseId}",
            courseId
        );
        return this._get(endpoint);
    }

    resetH5pContent(contentId) {
        return this._post(API_H5P_RESET_USER_CONTENT_DATA, {
            contentId: contentId,
        });
    }

    analyticsOverview(courseId, sectionId) {
        let endpoint = API_INTERACTIVE_COURSE_ANALYTICS.replace(
            "{courseId}",
            courseId
        );
        return this._get(endpoint, {sectionId: sectionId});
    }

    analyticsStatements(courseId, sectionId) {
        let endpoint = API_INTERACTIVE_COURSE_ANALYTICS_STATEMENTS.replace(
            "{courseId}",
            courseId
        );
        return this._get(endpoint,{sectionId: sectionId});
    }

    analyticsStatementsxAPI(courseId, sectionId) {
        let endpoint = API_INTERACTIVE_COURSE_ANALYTICS_XAPI.replace(
            "{courseId}",
            courseId
        );
        return this._get(endpoint,{sectionId: sectionId});
    }
    analyticsH5PTopic(courseId, topicId, variantId) {
        let endpoint = API_INTERACTIVE_COURSE_H5P_ANALYTICS.replace(
            "{courseId}",
            courseId
        );
        return this._get(endpoint, {
            topicId: topicId,
            variantId: variantId,
        });
    }

    analyticsTaskTopic(courseId, topicId, variantId) {
        return this._get(
            API_INTERACTIVE_COURSE_TASK_ANALYTICS.replace(
                "{courseId}",
                courseId
            ),
            {
                topicId: topicId,
                variantId: variantId,
            }
        );
    }

    getLearnContent(learnContentId) {
        let endpoint = API_LEARN_CONTENT_GET.replace(
            "{learnContentId}",
            learnContentId
        );
        return this._get(endpoint);
    }

    browseLearnContent(
        q,
        cloud_ids,
        content_types,
        tags,
        languages,
        categories,
        bookmarked,
        sort_by,
        direction,
        limit,
        offset,
        trimmed = false
    ) {
        return this._get(API_LEARN_CONTENT_BROWSE, {
            q,
            cloud_ids,
            content_types,
            tags,
            languages,
            categories,
            bookmarked,
            sort_by,
            direction,
            limit,
            offset,
            trimmed,
        });
    }

    createLearnContent(contentId, foreignId, foreignType, metadata = null) {
        return this._post(API_LEARN_CONTENT_CREATE, {
            contentId,
            foreignId,
            foreignType,
            metadata,
        });
    }

    importLearnContent(file, setProgress) {
        const config = {
            onUploadProgress: (progress) =>
                setProgress(
                    Math.round((progress.loaded * 100) / progress.total)
                ),
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };

        let endpoint = API_LEARN_CONTENT_IMPORT;

        let formData = new FormData();
        formData.append("import", file);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    downloadLearnContent(learnContentId) {
        return (
            BASE +
            API_LEARN_CONTENT_DOWNLOAD.replace(
                "{learnContentId}",
                learnContentId
            ) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    downloadLearnContentxAPI(learnContentId) {
        return (
            BASE +
            API_LEARN_CONTENT_XAPI_DOWNLOAD.replace(
                "{learnContentId}",
                learnContentId
            ) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    deleteLearnContent(contentId) {
        return this._post(
            API_LEARN_CONTENT_DELETE.replace("{learnContentId}", contentId),
            {
                contentId,
            }
        );
    }

    learnContentUpdateMetadata(learnContentId, metadata, categoriesId, tagIds) {
        let endpoint = API_LEARN_CONTENT_UPDATE_METADATA.replace(
            "{learnContentId}",
            learnContentId
        );
        return this._post(endpoint, {
            metadata,
            categories: categoriesId,
            tags: tagIds,
        });
    }

    updateLearnContentCategoryImage(categoryId, image = undefined) {
        const formData = new FormData();
        if (image) formData.append("image", image);

        return fetch(
            BASE +
                API_LEARN_CONTENT_CATEGORY_UPDATE_IMAGE.replace(
                    "{categoryId}",
                    categoryId
                ),
            {
                headers: {
                    Accept: "*/*",
                    ...this._getAuthHeaderObj(),
                },
                method: "POST",
                body: formData,
            }
        ).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    updateLearnContentCategory(
        categoryId,
        name,
        color,
        description,
        isVisible
    ) {
        return this._post(
            API_LEARN_CONTENT_CATEGORY_UPDATE.replaceAll(
                "{categoryId}",
                categoryId
            ),
            {
                name,
                color,
                description,
                isVisible,
            }
        );
    }

    getLearnContentCategories() {
        return this._get(API_LEARN_CONTENT_CATEGORY);
    }

    getLearnContentCategoryDetails(categoryId) {
        return this._get(
            API_LEARN_CONTENT_CATEGORY_DETAILS.replaceAll(
                "{categoryId}",
                categoryId
            )
        );
    }

    uploadScrom(file, incremental = false, progress = null) {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };
        let endpoint = SCORM_UPLOAD_URL;

        let formData = new FormData();
        formData.append("zip", file);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    downloadScorm(contentId) {
        return BASE + SCORM_DOWNLOAD_URL.replace("{content-id}", contentId);
    }

    getScromPlayerUrl(contentId) {
        return (
            BASE +
            SCORM_PLAYER_URL.replace("{content-id}", contentId) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    uploadCmi5(file, incremental = false, progress = null) {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };
        let endpoint = CMI5_UPLOAD_URL;

        let formData = new FormData();
        formData.append("file", file);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    getCmi5PlayerUrl(contentId) {
        return (
            BASE +
            CMI5_PLAYER_URL.replace("{content-id}", contentId) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    learnContentTags(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_TAGS.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    allLearnContentTags() {
        return this._get(API_LEARN_CONTENT_TAGS_ALL);
    }

    createLearnContentCategory(
        name,
        color,
        description,
        image = undefined,
        parent = undefined,
        isVisible = true
    ) {
        const formData = new FormData();
        formData.append("name", name);
        formData.append("color", color);
        formData.append("description", description);
        formData.append("isVisible", isVisible);
        if (image) formData.append("image", image);
        if (parent) formData.append("parent", parent);

        return fetch(BASE + API_LEARN_CONTENT_CATEGORY, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    deleteLearnContentCategory(categoryId) {
        return this._post(
            API_LEARN_CONTENT_CATEGORY_DELETE.replaceAll(
                "{categoryId}",
                categoryId
            )
        );
    }

    createLearnContentBookmarkList(name) {
        return this._post(API_LEARN_CONTENT_BOOKMARK_LIST_CREATE, { name });
    }

    learnContentBookmarkList(listId) {
        return this._get(
            API_LEARN_CONTENT_BOOKMARK_LIST_GET.replaceAll("{listId}", listId)
        );
    }

    editLearnContentBookmarkList(listId, bookmarks) {
        return this._post(
            API_LEARN_CONTENT_BOOKMARK_LIST_EDIT.replaceAll("{listId}", listId),
            { bookmarks }
        );
    }

    deleteLearnContentBookmarkList(listId) {
        return this._post(
            API_LEARN_CONTENT_BOOKMARK_LIST_DELETE.replaceAll(
                "{listId}",
                listId
            )
        );
    }

    addToLearnContentBookmarkList(listId, learnContentId) {
        return this._post(
            API_LEARN_CONTENT_BOOKMARK_LIST_ADD.replaceAll(
                "{listId}",
                listId
            ).replaceAll("{learnContentId}", learnContentId)
        );
    }

    removeFromLearnContentBookmarkList(listId, learnContentId) {
        return this._post(
            API_LEARN_CONTENT_BOOKMARK_LIST_REMOVE.replaceAll(
                "{listId}",
                listId
            ).replaceAll("{learnContentId}", learnContentId)
        );
    }

    inLearnContentBookmarkLists(listId, learnContentId) {
        return this._get(
            API_LEARN_CONTENT_BOOKMARK_LISTS_WITH_ITEM.replaceAll(
                "{listId}",
                listId
            ).replaceAll("{learnContentId}", learnContentId)
        );
    }

    myLearnContentBookmarkLists() {
        return this._get(API_LEARN_CONTENT_BOOKMARK_LISTS_BY_ME);
    }

    learnContentLikes(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_LIKES_COUNT.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    addLikeToLearnContent(learnContentId) {
        return this._post(
            API_LEARN_CONTENT_LIKES_ADD.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    removeLikeFromLearnContent(learnContentId) {
        return this._post(
            API_LEARN_CONTENT_LIKES_REMOVE.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    learnContentComments(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_COMMENTS.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    writeLearnContentComment(learnContentId, content) {
        return this._post(
            API_LEARN_CONTENT_COMMENTS_WRITE.replaceAll(
                "{learnContentId}",
                learnContentId
            ),
            { content }
        );
    }

    deleteLearnContentComment(commentId) {
        return this._post(
            API_LEARN_CONTENT_COMMENTS_DELETE.replaceAll(
                "{commentId}",
                commentId
            )
        );
    }

    loadLearnContentTenantDetails(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_PERMISSIONS.replaceAll(
                "{learn_content_id}",
                learnContentId
            )
        );
    }

    addLearnContentTenant(learnContentId, tenant_id) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_ADD_TENANT.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            { tenant_id }
        );
    }

    updateLearnContentTenant(
        learnContentId,
        tenant_id,
        group_ids,
        showInLearnBib
    ) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_UPDATE_TENANT.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            {
                tenant_id,
                group_ids,
                showInLearnBib,
            }
        );
    }

    deleteLearnContentTenant(learnContentId, tenant_id) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_DELETE_TENANT.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            { tenant_id }
        );
    }

    addLearnContentUser(learnContentId, cloudid) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_ADD_USER.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            { cloudid }
        );
    }

    updateLearnContentUser(learnContentId, cloudid, permission) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_SAVE_USER.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            { cloudid, permission }
        );
    }

    deleteLearnContentUser(learnContentId, cloudid) {
        return this._post(
            API_LEARN_CONTENT_PERMISSIONS_DELETE_USER.replaceAll(
                "{learn_content_id}",
                learnContentId
            ),
            { cloudid }
        );
    }

    getTranslateLearnContent(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_GET_TRANSLATE.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    translateLearnContent(learnContentId, language, topicId = null) {
        return this._post(
            API_LEARN_CONTENT_TRANSLATE.replaceAll(
                "{learnContentId}",
                learnContentId
            ),
            { language, topicId }
        );
    }

    learnContentCompetences(learnContentId) {
        return this._get(
            API_LEARN_CONTENT_COMPETENCES.replaceAll(
                "{learnContentId}",
                learnContentId
            )
        );
    }

    addLearnContentCompetence(learnContentId, competenceId) {
        return this._post(
            API_LEARN_CONTENT_COMPETENCE_ADD.replaceAll(
                "{learnContentId}",
                learnContentId
            ).replaceAll("{competenceId}", competenceId)
        );
    }

    updateLearnContentCompetencePoints(learnContentId, competenceId, points) {
        return this._post(
            API_LEARN_CONTENT_COMPETENCE_UPDATE_POINTS.replaceAll(
                "{learnContentId}",
                learnContentId
            ).replaceAll("{competenceId}", competenceId),
            { points }
        );
    }

    removeLearnContentCompetence(learnContentId, competenceId) {
        return this._post(
            API_LEARN_CONTENT_COMPETENCE_REMOVE.replaceAll(
                "{learnContentId}",
                learnContentId
            ).replaceAll("{competenceId}", competenceId)
        );
    }

    addSectionGroupApp(sectionId, type) {
        let endpoint = API_SECTION_ADD_APP.replace("{sectionId}", sectionId);
        return this._post(endpoint, { type: type });
    }

    removeSectionGroupApp(sectionId, section_app_id) {
        let endpoint = API_SECTION_REMOVE_APP.replace("{sectionId}", sectionId);
        return this._post(endpoint, { section_app_id: section_app_id });
    }

    getSectionChat(sectionId) {
        let endpoint = API_SECTION_CHAT.replace("{sectionId}", sectionId);
        return this._get(endpoint);
    }

    getSectionMembers(sectionId) {
        let endpoint = API_SECTION_MEMBERS.replace("{sectionId}", sectionId);
        return this._get(endpoint);
    }

    getSectionEvents(sectionId) {
        return this._get(API_SECTION_EVENTS.replace("{sectionId}", sectionId))
    }

    getSectionTasks(sectionId) {
        return this._get(API_SECTION_TASKS.replace("{sectionId}", sectionId))
    }

    updateSection(sectionId, name, description) {
        let endpoint = API_SECTION_UPDATE.replace("{sectionId}", sectionId);
        return this._post(endpoint, { name: name, description: description });
    }

    removeSection(sectionId) {
        let endpoint = API_SECTION_REMOVE.replace("{sectionId}", sectionId);
        return this._post(endpoint);
    }

    reorderSections(groupId, updates) {
        let endpoint = API_SECTIONS_REORDER.replace("{groupId}", groupId);
        return this._post(endpoint, { new_order_mapping: updates });
    }

    removeGroupMember(groupId, memberId) {
        let endpoint = API_GROUP_REMOVE_MEMBER.replace("{groupId}", groupId);
        return this._post(endpoint, { member_id: memberId });
    }

    updateGroupMember(groupId, memberId, newRoleId) {
        let endpoint = API_GROUP_UPDATE_MEMBER.replace("{groupId}", groupId);
        return this._post(endpoint, { member_id: memberId, role: newRoleId });
    }

    addGroupMember(groupId, memberIdsArray) {
        let endpoint = API_GROUP_ADD_MEMBER.replace("{groupId}", groupId);
        return this._post(endpoint, { member_ids: memberIdsArray });
    }

    updateGroupRole(groupId, roleId, object) {
        let endpoint = API_GROUP_UPDATE_ROLE.replace(
            "{groupId}",
            groupId
        ).replace("{roleId}", roleId);
        return this._post(endpoint, { object: object });
    }

    addGroupRole(groupId) {
        let endpoint = API_GROUP_ADD_ROLE.replace("{groupId}", groupId);
        return this._post(endpoint);
    }

    deleteGroupRole(groupId, roleId) {
        let endpoint = API_GROUP_DELETE_ROLE.replace(
            "{groupId}",
            groupId
        ).replace("{roleId}", roleId);
        return this._post(endpoint);
    }

    /**
     * Auth
     */

    login(email, pass, twoFaDigits, browser, device, os) {
        return fetch(BASE + API_LOGIN, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({
                email: email,
                password: pass,
                "2fa": twoFaDigits,
                browser: browser,
                device: device,
                os: os,
            }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    register(name, email, pass) {
        return fetch(BASE + API_REGISTER, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({ name: name, email: email, password: pass }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    logoutServer() {
        return this._post(API_LOGOUT);
    }

    switchBackUser() {
        return this._post(API_SWITCH_BACK);
    }

    logout(localLogoutOnly) {
        //@TODO server sided logout
        SharedHelper.eraseCookie(COOKIE_RC_ACCESS_TOKEN);
        SharedHelper.eraseCookie(COOKIE_RC_UID_TOKEN);
        EventManager.fireLogoutEvent();
        if (!localLogoutOnly) {
            this.logoutServer().then((resp) => {});
        }
        SharedHelper.resetJwt();
    }

    userAliasLogout() {
        this.switchBackUser().then((resp) => {
            SharedHelper.eraseCookie(COOKIE_RC_ACCESS_TOKEN);
            SharedHelper.eraseCookie(COOKIE_RC_UID_TOKEN);
            SharedHelper.setCookie(
                COOKIE_RC_ACCESS_TOKEN,
                localStorage.getItem("educa_rc_token_user_alias")
            );
            SharedHelper.setCookie(
                COOKIE_RC_UID_TOKEN,
                localStorage.getItem("educa_rc_uid_user_alias")
            );
            SharedHelper.resetUserAliasJwt();
            if (resp.payload?.jwt) SharedHelper.setJwt(resp.payload?.jwt);
            window.location.href = "/cloud/logoutSecond";
            //window.location.reload()
        });
    }

    /**
     * Users
     */

    getAllCloudUsers() {
        return this._get(API_MASTERDATA_ALL_CLOUD_USERS);
    }

    getRooms() {
        return this._get(API_MASTERDATA_ROOMS);
    }

    getCurrentUser() {
        return this._get(API_SEARCH_GET_CURRENT_USER);
    }

    //Feeds

    getFeedStatistics(feed_activity_id) {
        return this._get(API_FEED_STATISTICS, { feed_activity_id });
    }

    getMainFeed(lastTimestamp, filter = "all") {
        return this._get(API_MAIN_FEED,{filter : filter,timestamp: lastTimestamp});
    }

    getMainFeedEvents() {
        return this._get(API_FEED_EVENTS);
    }

    getMainFeedTasks() {
        return this._get(API_FEED_TASKS);
    }

    getMainFeedInteractiveCourse() {
        return this._get(API_FEED_INTERACTIVE_COURSE);
    }

    getGroupFeed(lastTimestamp, groupId) {
        let endpoint = API_GROUP_FEED.replace(
            "{timestamp}",
            lastTimestamp
        ).replace("{groupId}", groupId);
        return this._get(endpoint);
    }

    /**
     * Announcements
     */

    getfirstAnnouncementsOfSection(sectionId) {
        return this._get(API_SECTION_ANNOUNCEMENTS_PREVIEW.replace("{sectionId}", sectionId))
    }

    likeAnnouncement(anouncementId) {
        let endpoint = API_ANNOUNCEMENT_TOGGLE_LIKE.replace(
            "{beitragId}",
            anouncementId
        );
        return this._post(endpoint);
    }

    createNewCommentInAnnouncement(anouncementId, comment) {
        let endpoint = API_ANNOUNCEMENT_CREATE_COMMENT.replace(
            "{beitragId}",
            anouncementId
        );
        return this._post(endpoint, { comment: comment });
    }

    toggleCommentInAnnouncement(anouncementId, commentId) {
        let endpoint = API_ANNOUNCEMENT_HIDE_COMMENT.replace(
            "{beitragId}",
            anouncementId
        ).replace("{commentId}", commentId);
        return this._post(endpoint);
    }

    updateCommentInAnnouncement(anouncementId, commentId, commmentText) {
        let endpoint = API_ANNOUNCEMENT_EDIT_COMMENT.replace(
            "{beitragId}",
            anouncementId
        ).replace("{commentId}", commentId);
        return this._post(endpoint, { comment: commmentText });
    }

    commentHistoryAnnouncement(anouncementId, commentId) {
        let endpoint = API_ANNOUNCEMENT_HISTORY_COMMENT.replace(
            "{beitragId}",
            anouncementId
        ).replace("{commentId}", commentId);
        return this._get(endpoint);
    }

    updateAnnouncement(
        announcementId,
        newFiles,
        fileIdsToDelete,
        newConent,
        comments_active,
        comments_hide
    ) {
        let endpoint = API_ANNOUNCEMENT_UPDATE.replace(
            "{beitragId}",
            announcementId
        );

        let formData = new FormData();
        if (fileIdsToDelete)
            formData.append(
                "file_ids_to_delete",
                JSON.stringify(fileIdsToDelete)
            );
        if (newConent) formData.append("content", newConent);

        formData.append("comments_active", comments_active);
        formData.append("comments_hide", comments_hide);

        if (newFiles) {
            newFiles.forEach(function (image) {
                formData.append("new_files[]", image);
            });
        }

        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    deleteAnnouncement(anouncementId) {
        let endpoint = API_ANNOUNCEMENT_DELETE.replace(
            "{beitragId}",
            anouncementId
        );
        return this._post(endpoint);
    }

    getAnnouncementById(announcementId) {
        let endpoint = API_ANNOUNCEMENT_BY_ID.replace(
            "{announcementId}",
            announcementId
        );
        return this._get(endpoint);
    }

    /**
     * Messages
     */

    getImListCloudIdMapping() {
        return this._get(API_IM_LIST_CLOUDID_MAPPING);
    }

    reportMessages(obj) {
        return this._post(API_IM_REPORT, { object: obj });
    }

    /**
     *
     * @param cloudIds Array
     */
    createImChat(cloudIds) {
        return this._post(API_IM_CREATE_CHAT, { cloudIds: cloudIds });
    }

    createGroupChat(cloudIds, name) {
        return this._post(API_IM_CREATE_GROUP_CHAT, {
            cloudIds: cloudIds,
            name: name,
        });
    }

    /**
     * Events / Calendar
     */

    getEvents(
        start,
        end,
        groupIdArr,
        direct,
        sectionIdArray,
        viewForCloudId,
        showRemovedEvents,
        eventTypeFilter
    ) {
        let params = {
            start: start,
            end: end,
        };
        if (groupIdArr) params["groups"] = groupIdArr;
        if (direct) params["direct"] = direct;
        if (sectionIdArray) params["sections"] = sectionIdArray;
        if (viewForCloudId) params["viewForCloudId"] = viewForCloudId;
        if (showRemovedEvents) params["showRemovedEvents"] = showRemovedEvents;
        if (eventTypeFilter) params["eventTypeFilter"] = eventTypeFilter;
        return this._post(API_EVENTS, params);
    }

    getEventDetails(eventId, occurrenceDate = null) {
        let endpoint = API_EVENTS_DETAILS.replace("{eventId}", eventId);
        return this._get(endpoint, { occurrenceDate: occurrenceDate });
    }

    updateEvent(event) {
        let endpoint = API_EVENTS_UPDATE.replace("{eventId}", event.id);
        return this._post(endpoint, {
            ...event,
        });
    }

    moveEvent(event) {
        let endpoint = API_EVENTS_UPDATE.replace("{eventId}", event.id);
        return this._post(endpoint, {
            action: "move",
            ...event,
        });
    }

    createEvent(event) {
        return this._post(API_EVENTS_CREATE, {
            ...event,
        });
    }

    deleteEvent(eventId) {
        let endpoint = API_EVENTS_DELETE.replace("{eventId}", eventId);
        return this._post(endpoint);
    }

    deleteTerminEvent(eventId, occurrenceDate) {
        let endpoint = API_EVENTS_SINGLE_CANCEL.replace("{eventId}", eventId);
        return this._post(endpoint, { occurrenceDate: occurrenceDate });
    }

    deleteRemovedTerminEvent(eventId, singleAppointId, occurrenceDate) {
        let endpoint = API_EVENTS_SINGLE_DELETE.replace(
            "{eventId}",
            eventId
        ).replace("{singleAppointmentId}", singleAppointId);
        return this._post(endpoint, { occurrenceDate: occurrenceDate });
    }

    createSingleTerminEvent(eventId, occurrenceDate) {
        let endpoint = API_EVENTS_SINGLE_MOVE.replace("{eventId}", eventId);
        return this._post(endpoint, { occurrenceDate: occurrenceDate });
    }

    updateSingleTerminEvent(eventId, singleAppointId, occurrenceDate, data) {
        let endpoint = API_EVENTS_SINGLE_UPDATE.replace(
            "{eventId}",
            eventId
        ).replace("{singleAppointmentId}", singleAppointId);
        return this._post(endpoint, {
            singleAppointment: data,
            occurrenceDate: occurrenceDate,
        });
    }

    getEventInvites() {
        return this._get(API_EVENTS_INVITES);
    }

    updateEventInviteStatus(eventId, status) {
        let endpoint = API_EVENTS_UPDATE_STATUS.replace("{eventId}", eventId);
        return this._post(endpoint, { status: status });
    }


    checkUserCalendars(start, end, cloud_ids, exclude) {
        let payload = { start: start, end: end, cloud_ids: cloud_ids };
        if (exclude) payload["exclude"] = exclude;
        return this._post(API_EVENTS_CHECK_USERS, payload);
    }

    //Stupla
    getTimetableEvents(entityType, entityIds, breaks, start, end) {
        return this._get(API_EVENTS_TIMETABLE, {
            ids: entityIds?.join(","),
            type: entityType,
            start: start,
            end: end,
            breaks: breaks,
        });
    }

    generateOutlookShareToken(obj)
    {
        return this._post(API_EVENTS_OUTLOOK_TOKEN, obj);
    }

    /**
     * Classbook
     */

    getClassBookInfo(uniqueEventId, lessonType, lessonId) {
        return this._get(API_CLASSBOOK_TEACHING_INFO, {
            unique_id: uniqueEventId,
            type: lessonType,
            id: lessonId,
        });
    }

    getClassBookMembers(
        schoolYearId = undefined,
        uniqueEventId,
        lessonOrLessonPlanType,
        lessonOrLessonPlanId,
        eventStartUnix,
        eventEndUnix
    ) {
        return this._get(API_CLASSBOOK_TEACHING_MEMBERS, {
            unique_id: uniqueEventId,
            id: lessonOrLessonPlanId,
            type: lessonOrLessonPlanType,
            start: eventStartUnix,
            end: eventEndUnix,
        });
    }

    saveClassBookEntry(
        uniqueEventId,
        lessonOrLessonPlanId,
        lessonOrLessonPlanType,
        formRevisionId,
        formData,
        presenceList,
        eventStartUnix,
        eventEndUnix
    ) {
        return this._post(API_CLASSBOOK_TEACHING_SAVE, {
            unique_id: uniqueEventId,
            id: lessonOrLessonPlanId,
            type: lessonOrLessonPlanType,
            presence: presenceList,
            form_data: formData,
            form_revision_id: formRevisionId,
            start: eventStartUnix,
            end: eventEndUnix,
        });
    }

    /**
     * TaskTemplates
     */
    getTaskTemplates() {
        let payload = {};
        return this._post(API_TASK_TEMPLATES, payload);
    }

    getTaskTemplateDetails(taskTemplateId) {
        let endpoint = API_TASK_TEMPLATES_DETAILS.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        return this._get(endpoint);
    }

    createTaskTemplate(
        title,
        description,
        privatenote,
        handIn,
        type,
        isLearnContent = false,
        defaultEndOffset = -1,
        autostart = false,
        maxPoints = 100
    ) {
        let payload = {};
        if (title) payload["title"] = title;
        if (description) payload["description"] = description;
        if (privatenote) payload["privatenote"] = privatenote;
        if (handIn) payload["handIn"] = handIn;
        if (type) payload["type"] = type;
        if (isLearnContent) payload["isLearnContent"] = isLearnContent;
        if (defaultEndOffset) payload["defaultEndOffset"] = defaultEndOffset;
        if (autostart) payload["autostart"] = autostart;
        if (maxPoints) payload["maxPoints"] = maxPoints;

        return this._post(API_TASK_TEMPLATES_CREATE, payload);
    }

    updateTaskTemplate(
        taskTemplateId,
        title,
        description,
        privatenote,
        handIn,
        type,
        isLearnContent = false,
        defaultEndOffset = -1,
        autostart = false,
        maxPoints = 100
    ) {
        let endpoint = API_TASK_TEMPLATES_UPDATE.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        let payload = {};
        if (title) payload["title"] = title;
        if (description) payload["description"] = description;
        if (privatenote) payload["privatenote"] = privatenote;
        if (handIn) payload["handIn"] = handIn;
        if (type) payload["type"] = type;
        if (isLearnContent) payload["isLearnContent"] = isLearnContent;
        if (defaultEndOffset) payload["defaultEndOffset"] = defaultEndOffset;
        if (autostart) payload["autostart"] = autostart;
        if (maxPoints) payload["maxPoints"] = maxPoints;

        return this._post(endpoint, payload);
    }

    updateTaskTemplateFormTemplate(taskTemplateId, formTemplate) {
        let endpoint = API_TASK_TEMPLATES_UPDATE_FORM_TEMPLATE.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        return this._post(endpoint, { form_template: formTemplate });
    }

    finishTaskUpdate(taskId) {
        let endpoint = API_TASKS_FINISH_UPDATE.replace("{taskId}", taskId);
        return this._post(endpoint);
    }

    deleteTaskTemplate(taskTemplateId) {
        let endpoint = API_TASK_TEMPLATES_DELETE.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        return this._post(endpoint);
    }

    createTaskFromTemplate(taskTemplateId) {
        let endpoint = API_TASK_TEMPLATES_CREATE_TASK.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        return this._post(endpoint);
    }

    createInteractiveCourseTask(
        taskTemplateId,
        learnContentId,
        interactiveCourseId,
        sectionId
    ) {
        let endpoint =
            API_TASK_TEMPLATES_CREATE_TASK_INTERACTIVE_COURSE.replace(
                "{taskTemplateId}",
                taskTemplateId
            );
        return this._post(endpoint, {
            learnContentId: learnContentId,
            interactiveCourseId: interactiveCourseId,
            sectionId: sectionId
        });
    }

    getInteractiveCourseTask(
        taskTemplateId,
        learnContentId,
        interactiveCourseId
    ) {
        let endpoint = API_TASK_TEMPLATES_GET_TASK_INTERACTIVE_COURSE.replace(
            "{taskTemplateId}",
            taskTemplateId
        );
        return this._get(endpoint, {
            learnContentId: learnContentId,
            interactiveCourseId: interactiveCourseId,
        });
    }

    /**
     * Tasks
     */

    getTasks(
        start,
        end,
        groupIdArr,
        direct,
        sectionIdArray,
        viewForCloudId,
        isMyTaskCheckboxChecked
    ) {
        let payload = {};
        if (start) payload["start"] = start;
        if (end) payload["end"] = end;
        if (groupIdArr) payload["groups"] = groupIdArr;
        if (direct) payload["direct"] = direct;
        if (sectionIdArray) payload["sections"] = sectionIdArray;
        if (viewForCloudId) payload["viewForCloudId"] = viewForCloudId;
        if (isMyTaskCheckboxChecked)
            payload["myTask"] = isMyTaskCheckboxChecked;

        return this._post(API_TASKS, payload);
    }

    getArchivedTasks(groupIdArr) {
        let payload = {};
        if (groupIdArr) payload["groups"] = groupIdArr;

        return this._post(API_TASKS_ARCHIVED, payload);
    }

    getTaskDetails(taskId) {
        let endpoint = API_TASKS_DETAILS.replace("{taskId}", taskId);
        return this._get(endpoint);
    }

    createTask(
        title,
        start,
        end,
        description,
        privatenote,
        attendeesIds,
        sectionIds,
        handIn,
        remember_minutes,
        type,
        formTemplate
    ) {
        let payload = {};
        if (title) payload["title"] = title;
        if (description) payload["description"] = description;
        if (privatenote) payload["privatenote"] = privatenote;
        if (start) payload["start"] = start;
        if (end) payload["end"] = end;
        if (attendeesIds) payload["attendees"] = attendeesIds;
        if (sectionIds) payload["sections"] = sectionIds;
        if (handIn) payload["handIn"] = handIn;
        if (remember_minutes) payload["remember_minutes"] = remember_minutes;
        if (type) payload["type"] = type;
        if (formTemplate) payload["form_template"] = formTemplate;

        return this._post(API_TASKS_CREATE, payload);
    }

    updateTask(
        taskId,
        title,
        start,
        end,
        description,
        privatenote,
        attendeesIds,
        sectionIds,
        handIn,
        remember_minutes,
        type,
        formTemplate
    ) {
        let endpoint = API_TASKS_UPDATE.replace("{taskId}", taskId);
        let payload = {};
        if (title) payload["title"] = title;
        if (description) payload["description"] = description;
        if (privatenote) payload["privatenote"] = privatenote;
        if (start) payload["start"] = start;
        if (end) payload["end"] = end;
        if (attendeesIds) payload["attendees"] = attendeesIds;
        if (sectionIds) payload["sections"] = sectionIds;
        if (handIn) payload["handIn"] = handIn;
        if (remember_minutes) payload["remember_minutes"] = remember_minutes;
        if (type) payload["type"] = type;
        if (formTemplate) payload["form_template"] = formTemplate;

        return this._post(endpoint, payload);
    }

    updateTaskFormTemplate(taskId, formTemplate) {
        let endpoint = API_TASKS_UPDATE_FORM_TEMPLATE.replace(
            "{taskId}",
            taskId
        );
        return this._post(endpoint, { form_template: formTemplate });
    }

    deleteTask(taskId) {
        let endpoint = API_TASKS_DELETE.replace("{taskId}", taskId);
        return this._post(endpoint);
    }

    uploadContentToTask(taskId, file) {
        let endpoint = API_TASKS_CONTENT_UPDATE.replace("{taskId}", taskId);

        let formData = new FormData();
        formData.append("import_file", file);

        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData ? formData : null,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    closeTask(taskId) {
        let endpoint = API_TASKS_CLOSE.replace("{taskId}", taskId);
        return this._post(endpoint);
    }

    resetSubmission(taskId, submissionId) {
        return this._post(
            API_TASKS_SUBMISSION_RESET.replace("{taskId}", taskId).replace(
                "{submissionId}",
                submissionId
            )
        );
    }

    updateSubmission(taskId, id, description, points, rating, stage) {
        let endpoint = API_TASKS_SUBMISSION_UPDATE.replace(
            "{taskId}",
            taskId
        ).replace("{submissionId}", id);
        let payload = {};
        if (description) payload["description"] = description;
        if (points) payload["points"] = points;
        if (rating) payload["rating"] = rating;
        if (stage) payload["stage"] = stage;

        return this._post(endpoint, payload);
    }

    completeAllSubmissions(taskId) {
        let endpoint = API_TASKS_SUBMISSION_COMPLETEALL.replace(
            "{taskId}",
            taskId
        );
        let payload = {};

        return this._post(endpoint, payload);
    }

    taskAIRateSubmission(taskId, correctAnswer) {
        let endpoint = API_TASKS_AI_RATE_SUBMISSION.replace("{taskId}", taskId);
        let payload = {
            correctAnswer: correctAnswer,
        };

        return this._post(endpoint, payload);
    }

    /**
     * Dokumente
     */

    /**
     * Used for XMLHttpRequest in order to use file upload progress...
     * Does the same as 'createDocument'
     * @returns {string}
     */
    getDocumentUploadUrl() {
        return BASE + API_DOCUMENTS;
    }

    getDocumentList(modelId, modelType, withPath = true) {
        let payload = { model_id: modelId, model_type: modelType };
        if (withPath) payload["withPath"] = true;
        return this._get(API_DOCUMENTS_LIST, payload);
    }

    createDocument(modelId, modelType, parent_id, file, incremental = false) {
        let formData = new FormData();
        formData.append("document", file);
        formData.append("parent_id", parent_id);
        formData.append("model_id", modelId);
        formData.append("model_type", modelType);
        formData.append("incremental", incremental.toString());

        return fetch(BASE + API_DOCUMENTS, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    updateAxiosDocument(
        document_id,
        file,
        progress = null
    )  {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };
        let endpoint = API_DOCUMENTS_UPDATE.replace("{document_id}",document_id);

        let formData = new FormData();
        formData.append("document", file);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    createAxiosDocument(
        modelId,
        modelType,
        parent_id,
        file,
        incremental = false,
        overrideNames = false,
        path = null,
        progress = null,
    ) {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };
        let endpoint = API_DOCUMENTS;

        let formData = new FormData();
        formData.append("document", file);
        if(parent_id)
            formData.append("parent_id", parent_id);
        formData.append("model_id", modelId);
        formData.append("model_type", modelType);
        formData.append("override",overrideNames);
        formData.append("path",path);
        formData.append("incremental", incremental.toString());

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    createDocumentFolder(modelId, modelType, parent_id, foldername) {
        let payload = {};
        payload["folder"] = foldername;
        payload["parent_id"] = parent_id;
        payload["model_id"] = modelId;
        payload["model_type"] = modelType;

        return this._post(API_DOCUMENTS_FOLDERS, payload);
    }

    downloadDocumentUrl(documentId, access_hash) {
        return (
            BASE + API_DOCUMENTS_DOWNLOAD.replace("{documentId}", documentId) +  "?access_hash=" + access_hash
        );
    }

    downloadSubtitleUrl(documentId, subtitleId) {
        return (
            BASE +
            API_DOCUMENTS_SUBTITLE_DOWNLOAD.replace(
                "{documentId}",
                documentId
            ).replace("{subtitleId}", subtitleId) +
            "?token=" +
            SharedHelper.getJwt()
        );
    }


    openDocumentUrl(documentId, access_hash) {
        return WEB_DOCUMENTS_OPEN.replace("{documentId}", documentId) +  "&access_hash=" + access_hash;
    }

    moveDocument(documentId, newParentId) {
        let payload = { document_id: documentId };
        if (newParentId !== null && newParentId !== undefined)
            payload["parent_id"] = newParentId;
        return this._post(API_DOCUMENTS_MOVE, payload);
    }

    moveOrCopyDocument(documentId, newParentId, newModelType, newModelId, mode) {
        let payload = { document_id: documentId, newModelType: newModelType, newModelId: newModelId, mode : mode };
        if (newParentId !== null && newParentId !== undefined)
            payload["parent_id"] = newParentId;
        return this._post(API_DOCUMENTS_MOVE_OR_COPY, payload);
    }

    renameDocument(documentId, newName) {
        return this._post(API_DOCUMENTS_RENAME, {
            document_id: documentId,
            name: newName,
        });
    }

    getDocumentsSubtitles(documentId) {
        return this._get(
            API_DOCUMENTS_SUBTITLES.replace("{documentId}", documentId)
        );
    }

    addSubtitle(documentId, language, content) {
        return this._post(
            API_DOCUMENTS_SUBTITLE_ADD.replace("{documentId}", documentId),
            { language, content }
        );
    }

    editSubtitle(documentId, subtitleId, content) {
        return this._post(
            API_DOCUMENTS_SUBTITLE_EDIT.replace(
                "{documentId}",
                documentId
            ).replace("{subtitleId}", subtitleId),
            { content }
        );
    }

    deleteDocument(documentId) {
        return this._post(API_DOCUMENTS_DELETE, { document_id: documentId });
    }

    getDocument(documentId) {
        return this._get(API_DOCUMENT.replaceAll("{documentId}", documentId));
    }

    detailsDocument(documentId) {
        return this._get(API_DOCUMENT_DETAILS.replaceAll("{documentId}", documentId));
    }

    queryDocument(modelId, modelType, query) {
        return this._post(API_DOCUMENTS_QUERY, { query: query, model_id: modelId, model_type: modelType });
    }

    downloadViaZIPDocument(documents_ids)
    {
        if( !SharedHelper.getJwt() )
            return Promise.resolve()

        return fetch(BASE + API_DOCUMENTS_ZIP,
            {
                headers:
                    {
                        'Accept': '*/*',
                        'Content-Type': 'application/json',
                        ...this._getAuthHeaderObj()
                    },
                method: "POST",
                body: JSON.stringify({documents_ids : documents_ids})
            })
            .then(resp => {
                if (resp.status == 499) {
                    //     if (isUserLoggedIn())
                    //     SharedHelper.fireInfoToast("Automatischer Logout", "Bitte logge Dich erneut ein. ")
                    this.logout()
                }

                return resp
            })
    }

    /**
     * Code
     */
    checkCode(codeText) {
        return fetch(BASE + API_CHECK_CODE, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({ code: codeText }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    checkCodeInApp(code)
    {
        return this._post(API_CHECK_CODE_IN_APP, {code});
    }

    createAccountWithCode(codeText, emailText, nameText, passwordText) {
        return fetch(BASE + API_CREATE_CODE, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({
                code: codeText,
                email: emailText,
                name: nameText,
                password: passwordText,
            }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    checkAccountRecoverOptions(email) {
        return fetch(BASE + API_CHECK_RECOVER_OPTIONS, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({ email: email }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    sendRecoverMail(option, email) {
        return fetch(BASE + API_RECOVER_SEND_EMAIL, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({ option: option, email: email }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    executeRecover(option, email, code, firstAnswer, secondAnswer) {
        return fetch(BASE + API_RECOVER_EXECUTE, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({
                option: option,
                email: email,
                code: code,
                firstAnswer: firstAnswer,
                secondAnswer: secondAnswer,
            }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    resetPassword(email, password, token) {
        return fetch(BASE + API_RECOVER_RESET, {
            headers: {
                Accept: "*/*",
                "Content-Type": "application/json",
                Authorization: "Bearer " + token,
            },
            method: "POST",
            body: JSON.stringify({ password: password, email: email }),
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    /**
     *  Settings
     */

    loadSettings() {
        return this._get(API_SETTINGS);
    }

    updateGeneralSetting(name, language) {
        return this._post(API_SETTINGS_UPDATE, {
            name,
            language,
        });
    }

    analyticsDownloadxAPI() {
        return (
            BASE +
            API_SETTINGS_ANALYTICS_DOWNLOAD +
            "?token=" +
            SharedHelper.getJwt()
        );
    }

    getSecuritySettings() {
        return this._get(API_SETTINGS_SECURITY);
    }

    getSessions() {
        return this._get(API_ME_SESSIONS);
    }

    getGroupNotificationSettings()
    {
        return this._get(API_ME_GROUP_SETTINGS);
    }

    setSectionNotificationSetting(groupId, sectionId, flag)
    {
        return this._post(API_ME_GROUP_SETTINGS, {groupId, sectionId, flag});
    }

    setGroupClusterCollapsedFlag(groupClusterId, flag)
    {
        return this._post(API_ME_GROUP_CLUSTERS, {groupClusterId, flag});
    }

    flipGroupFavorite(groupId)
    {
        return this._post(API_ME_GROUP_CLUSTERS_FAVORITES, {groupId});
    }

    closeSession(session_id) {
        return this._post(
            API_ME_SESSION_CLOSE.replace("{session_id}", session_id)
        );
    }

    updatePasssword(password) {
        return this._post(API_SETTINGS_PASSWORD_UPDATE, {
            password,
        });
    }

    toggle2FA() {
        return this._post(API_SETTINGS_2FA_TOGGLE);
    }

    get2FACode() {
        return (
            BASE +
            API_SETTINGS_2FA_QRCODE +
            "?token=" +
            SharedHelper.getJwt() +
            "&random=" +
            Math.random().toString(36).slice(2)
        );
    }

    updateSecuritySettings(securitySettings) {
        return this._post(API_SETTINGS_SECURITY, securitySettings);
    }

    updateGeneralSettingsProfileImage(image) {
        let endpoint = API_SETTINGS_IMAGE_UPDATE;
        let formData = new FormData();
        formData.append("image", image);
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    getAppSettings(appName) {
        return this._get(API_SETTINGS_APP.replace("{appName}", appName));
    }

    setAppSettings(appName, payload) {
        return this._post(
            API_SETTINGS_APP_SAVE.replace("{appName}", appName),
            payload
        );
    }

    /**
     * Meetings
     */

    joinMeeting(modelType, id) {
        let endpoint = API_MEETINGS_JOIN.replace(
            "{modelType}",
            modelType
        ).replace("{id}", id);
        return this._get(endpoint);
    }

    infoMeeting(modelType, id) {
        let endpoint = API_MEETINGS_INFO.replace(
            "{modelType}",
            modelType
        ).replace("{id}", id);
        return this._get(endpoint);
    }

    liveInfoMeeting(modelType, id) {
        let endpoint = API_MEETINGS_LIVE_INFO.replace(
            "{modelType}",
            modelType
        ).replace("{id}", id);
        return this._get(endpoint);
    }

    deleteMeeting(modelType, id) {
        throw new Error("Not implemented");
    }

    /**
     * SEARCH
     */

    searchAnything(str, categories) {
        let params = { q: str };
        if (categories) params["categories"] = categories;
        return this._post(API_SEARCH_ALL, params);
    }

    /**
     * Misc
     */
    loadPrivacy() {
        return this._get(API_PRIVACY);
    }

    acceptPrivacy() {
        return this._post(API_PRIVACY_ACCEPT);
    }

    getCloudUserAvatarUrl(cloudId, size = "35", imageId = "") {
        return (
            API_CLOUD_ID_AVATAR +
            "=" +
            cloudId +
            "&size=" +
            size +
            "&name=" +
            imageId
        );
    }

    getGroupAvatarUrl(id, size = "35", imageId = "") {
        return (
            API_GROUP_ID_AVATAR +
            "=" +
            id +
            "&size=" +
            size +
            "&name=" +
            imageId
        );
    }

    getSectionAvatarUrl(id, size = "35", imageId = "") {
        return (
            API_SECTION_ID_AVATAR +
            "=" +
            id +
            "&size=" +
            size +
            "&name=" +
            imageId
        );
    }

    sendSupport(image, text, additionalFiles)
    {
        let formData = new FormData();
        formData.append("image", image)
        formData.append("text", text)

        if (additionalFiles) {
            additionalFiles.forEach(function (image) {
                formData.append('additionalFiles[]', image);
            });
        }

        return fetch(BASE + API_SEND_SUPPORT,
            {
                headers:
                    {
                        'Accept': '*/*',
                        ...this._getAuthHeaderObj()
                    },
                method: "POST",
                body: formData,
            })
            .then(resp => resp.json()).then(resp => {
                if(this._enableEncryption(resp))
                    resp.payload = JSON.parse(this._decryptResponse(resp.payload))
                return resp
            });
    }

    getSupportTickets() {
        return this._get(API_SUPPORT_LIST);
    }

    getSupportTicket(ticket_id) {
        return this._get(
            API_SUPPORT_TICKET_GET.replace("{ticket_id}", ticket_id)
        );
    }

    closeSupportTicket(ticket_id) {
        return this._post(
            API_SUPPORT_TICKET_CLOSE.replace("{ticket_id}", ticket_id)
        );
    }

    addSupportTicketAnswer(ticket_id, msg) {
        return this._post(
            API_SUPPORT_TICKET_GET.replace("{ticket_id}", ticket_id),
            { msg: msg }
        );
    }

    blockUser(user) {
        return this._post(API_BLOCK_USER_SUPPORT, { cloudId: user });
    }

    reportContent(content_type, content) {
        return this._post(API_REPORT_SUPPORT, {
            content_type: content_type,
            content: content,
        });
    }

    uploadFileSupportTicketAnswer(ticket_id, msg, file) {
        let formData = new FormData();
        formData.append("msg", msg);
        formData.append("attachment", file);
        return fetch(
            BASE + API_SUPPORT_TICKET_FILE.replace("{ticket_id}", ticket_id),
            {
                headers: {
                    Accept: "*/*",
                    ...this._getAuthHeaderObj(),
                },
                method: "POST",
                body: formData,
            }
        ).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    loadSystemSettingsUserList() {
        return this._get(API_CLOUD_USER_LIST);
    }

    loadSystemSettingsUserDetails(id) {
        return this._get(API_CLOUD_USER_DETAILS.replace("{cloud_id}", id));
    }

    updateSystemSettingsUserRoles(user_id, tenant_id, role_ids) {
        return this._post(
            API_CLOUD_USER_ROLES_EDIT.replace("{cloud_id}", user_id),
            {
                user_id,
                tenant_id,
                role_ids,
            }
        );
    }

    updateSystemSettingsUser(cloudId, usrObj) {
        return this._post(
            API_CLOUD_USERS_UPDATE.replace("{cloud_id}", cloudId),
            usrObj
        );
    }

    createSystemSettingsUser(usrObj) {
        return this._post(API_CLOUD_USERS_CREATE, usrObj);
    }

    deleteSystemSettingsUser(cloudId) {
        return this._post(
            API_CLOUD_USERS_DELETE.replace("{cloud_id}", cloudId)
        );
    }

    loadSystemSettingsTenants() {
        return this._get(API_CLOUD_TENANTS);
    }

    loadSystemSettingsRoles(tenant_id) {
        return this._get(API_CLOUD_PERMISSIONS_ROLES, { tenant_id });
    }

    createSystemSettingsRole = (tenant_id, name) => {
        return this._post(API_CLOUD_PERMISSIONS_ROLES_CREATE, {
            name: name,
            tenant_id,
        });
    };

    editSystemSettingsRole = (tenant_id, roleId, name) => {
        return this._post(
            API_CLOUD_PERMISSIONS_ROLES_EDIT.replace("{role_id}", roleId),
            { name: name, tenant_id }
        );
    };

    deleteSystemSettingsRole = (tenant_id, roleId) => {
        return this._post(
            API_CLOUD_PERMISSIONS_ROLES_DELETE.replace("{role_id}", roleId),
            { tenant_id }
        );
    };

    loadSystemSettingsPermissions = (tenantId) => {
        return this._get(API_CLOUD_PERMISSIONS, { tenant_id: tenantId });
    };

    flipSystemSettingsPermissionOfRole(role_id, permission_id, tenant_id) {
        return this._post(API_CLOUD_PERMISSIONS_FLIP, {
            role_id,
            permission_id,
            tenant_id,
        });
    }

    getSystemSettingsGroups() {
        return this._get(API_CLOUD_GROUP_LIST);
    }

    deleteSystemSettingsGroup(groupId) {
        return this._post(
            API_CLOUD_GROUP_DELETE.replace("{group_id}", groupId)
        );
    }

    archiveSystemSettingsGroups(groupId) {
        return this._post(
            API_CLOUD_GROUP_UNARCHIVE.replace("{group_id}", groupId)
        );
    }

    getSystemSettingsGeneralChartInfo() {
        return this._get(API_CLOUD_GENERAL_CHART_INFO);
    }

    getSystemSettingsGeneralWidgetInfoNewUsers(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_NEW_USERS, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetInfoActiveUsers(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_ACTIVE_USERS, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetActivity(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_ACTIVITY, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetInfoObjects(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_OBJECTS, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetLearnContentObjects(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_LEARN_CONTENT_OBJECTS, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetInfoFeed(start, end) {
        return this._get(API_CLOUD_GENERAL_CHART_FEEDS, {
            start: start,
            end: end,
        });
    }

    getSystemSettingsGeneralWidgetInfoSpaces() {
        return this._get(API_CLOUD_GENERAL_CHART_SPACES, {
        });
    }

    getSystemSettingsTenant(tenant_id) {
        const payload = new FormData();

        return fetch(
            BASE + API_CLOUD_TENANTS_GET.replace("{tenant_id}", tenant_id),
            {
                headers: {
                    Accept: "*/*",
                    ...this._getAuthHeaderObj(),
                },
                method: "GET",
            }
        ).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    createSystemSettingsTenant(tenant) {
        const payload = new FormData();
        Object.keys(tenant).forEach((key) => {
            payload.append(
                key,
                typeof tenant[key] == "boolean"
                    ? tenant[key]
                        ? 1
                        : 0
                    : tenant[key] ?? ""
            );
        });
        return fetch(BASE + API_CLOUD_TENANTS_CREATE, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: payload,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    updateSystemSettingsTenant(tenant) {
        const payload = new FormData();
        Object.keys(tenant).forEach((key) => {
            payload.append(
                key,
                typeof tenant[key] == "boolean"
                    ? tenant[key]
                        ? 1
                        : 0
                    : tenant[key] ?? ""
            );
        });
        return fetch(
            BASE + API_CLOUD_TENANTS_UPDATE.replace("{tenant_id}", tenant.id),
            {
                headers: {
                    Accept: "*/*",
                    ...this._getAuthHeaderObj(),
                },
                method: "POST",
                body: payload,
            }
        ).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    deleteSystemSettingsTenant(tenantId) {
        return this._post(
            API_CLOUD_TENANTS_DELETE.replace("{tenant_id}", tenantId)
        );
    }

    getSystemSettingsAnalyticsReports() {
        return this._get(API_CLOUD_ANALYTICS_REPORTS);
    }

    updateSystemSettingsAnalyticsReports(obj) {
        return this._post(API_CLOUD_ANALYTICS_REPORTS, { report_roles: obj });
    }

    /* Workflows */

    getSystemSettingsWorkflows(tenantId) {
        return this._get(API_CLOUD_WORKFLOWS, { tenantId: tenantId });
    }

    getSystemSettingsAvailableNodes(tenantId) {
        return this._get(API_CLOUD_WORKFLOWS_AVAILABLE_NODES, {
            tenantId: tenantId,
        });
    }

    addSystemSettingsWorkflow() {
        return this._post(API_CLOUD_WORKFLOWS_ADD);
    }

    getSystemSettingsWorkflowDetails(workflowId) {
        return this._get(
            API_CLOUD_WORKFLOW_DETAILS.replace("{workflow_id}", workflowId)
        );
    }

    updateSystemSettingsWorkflow(workflowId, workflow) {
        return this._post(
            API_CLOUD_WORKFLOW_UPDATE.replace("{workflow_id}", workflowId),
            { workflow }
        );
    }

    startSystemSettingsWorkflow(workflowId) {
        return this._post(
            API_CLOUD_WORKFLOW_START.replace("{workflow_id}", workflowId)
        );
    }

    loadSystemSettingsWorkflowInstanceDetails(instanceId) {
        return this._get(
            API_CLOUD_INSTANCE_DETAILS.replace("{instance_id}", instanceId)
        );
    }

    pauseSystemSettingsWorkflowInstanceDetails(instanceId) {
        return this._post(
            API_CLOUD_INSTANCE_PAUSE.replace("{instance_id}", instanceId)
        );
    }

    unpauseSystemSettingsWorkflowInstanceDetails(instanceId) {
        return this._post(
            API_CLOUD_INSTANCE_UNPAUSE.replace("{instance_id}", instanceId)
        );
    }

    getSystemSettingsMaintenance() {
        return this._get(API_CLOUD_MAINTENANCE);
    }

    /* Competences & CompetenceClusters */

    competenceClusters() {
        return this._get(API_CLOUD_COMPETENCE_CLUSTERS);
    }

    createCompetenceCluster(name, description) {
        return this._post(API_CLOUD_COMPETENCE_CLUSTER_CREATE, {
            name,
            description,
        });
    }

    updateCompetenceCluster(clusterId, name, description) {
        return this._post(
            API_CLOUD_COMPETENCE_CLUSTER_UPDATE.replaceAll(
                "{clusterId}",
                clusterId
            ),
            { name, description }
        );
    }

    deleteCompetenceCluster(clusterId) {
        return this._post(
            API_CLOUD_COMPETENCE_CLUSTER_DELETE.replaceAll(
                "{clusterId}",
                clusterId
            )
        );
    }

    competenceClusterCompetences(clusterId) {
        return this._get(
            API_CLOUD_COMPETENCE_CLUSTER_COMPETENCES.replaceAll(
                "{clusterId}",
                clusterId
            )
        );
    }

    competence(competenceId) {
        return this._get(
            API_CLOUD_COMPETENCE.replaceAll("{competenceId}", competenceId)
        );
    }

    allCompetences() {
        return this._get(API_CLOUD_COMPETENCE_ALL);
    }

    createCompetence(clusterId, name, description, color) {
        return this._post(
            API_CLOUD_COMPETENCE_CREATE.replaceAll("{clusterId}", clusterId),
            { name, description, color }
        );
    }

    updateCompetence(competenceId, name, description, color) {
        return this._post(
            API_CLOUD_COMPETENCE_UPDATE.replaceAll(
                "{competenceId}",
                competenceId
            ),
            { name, description, color }
        );
    }

    deleteCompetence(competenceId) {
        return this._post(
            API_CLOUD_COMPETENCE_DELETE.replaceAll(
                "{competenceId}",
                competenceId
            )
        );
    }

    /**
     * Learn Content Provider
     */
    getLernContentDashboard() {
        return this._get(API_LEARN_CONTENT_DASHBOARD);
    }

    getLernContentProvider(name) {
        return this._get(
            API_LEARN_PROVIDER_CONFIGURATION.replace("{name}", name)
        );
    }

    getLernContentProviderOverviewPage(name) {
        return this._get(API_LEARN_PROVIDER_OVERVIEW.replace("{name}", name));
    }

    getYoutubeVideo(name) {
        return this._get(
            API_LEARN_PROVIDER_YOUTUBE_VIDEO_INFO.replace("{name}", name)
        );
    }

    uploadShareFileLearnContent(file) {
        let formData = new FormData();
        formData.append("file", file);

        return fetch(BASE + API_UPLOAD_SHARE_FILES, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    /**
     * Group Templates
     */
    getGroupTemplates() {
        return this._get(API_GROUP_TEMPLATE);
    }

    deleteGroupTemplates(template_id) {
        return this._post(
            API_GROUP_TEMPLATE_DELETE.replace("{template_id}", template_id)
        );
    }

    createGroupFromGroupTemplates(template_id, name = null, color = null) {
        return this._post(
            API_GROUP_TEMPLATE_CREATE_GROUP.replace(
                "{template_id}",
                template_id
            ),
            {
                name: name,
                color: color,
            }
        );
    }

    createTemplateFromGroup(group_id, name) {
        return this._post(API_GROUP_TEMPLATE_CREATE_FROM_GROUP, {
            group_id: group_id,
            name: name,
        });
    }

    /**
     * Classbook
     */
    getAbsenteeism(studentId, start, end) {
        return this._get(API_WIDGET_ABSENTEESIM, {
            student_id: studentId,
            start: start,
            end: end,
        });
    }

    getMarks(studentId) {
        return this._get(API_WIDGET_MARKS, { student_id: studentId });
    }

    getExamDates(studentId) {
        return this._get(API_WIDGET_EXAM_DATES, { student_id: studentId });
    }

    getTimetableTimeFrame(entityType, entityId) {
        return this._get(API_ADMINISTRATION_TIMETABLE_TIMEFRAME, {
            id: entityId,
            type: entityType,
        });
    }

    getTimetable(entityType, entityId, breaks, start, end) {
        return this._get(API_ADMINISTRATION_TIMETABLE, {
            ids: entityId,
            type: entityType,
            start: start,
            end: end,
            breaks: breaks,
            external_booking: true,
        });
    }

    generateReportTemplate(template_id, model_id, model_type) {
        return this._post(API_TEMPLATE_GENERATE.replace("{id}", template_id), {
            model_id: model_id,
            model_type: model_type,
        });
    }

    getReportTemplateUrl(token) {
        return BASE + API_TEMPLATE_OPEN + "?token=" + token;
    }

    sendMassCorrespondence(
        modelType,
        modelIds,
        subject,
        content,
        additionalReceivers
    ) {
        return this._post(API_SEND_MASS_CORRESPONDENCE, {
            model_type: modelType,
            model_ids: modelIds,
            subject: subject,
            content: content,
            additional_receivers: additionalReceivers,
        });
    }

    getExternalIntegrationTemplates() {
        return this._get(API_EXTERNAL_INTEGRATION_TEMPLATES);
    }

    getPublicTenants() {
        return this._get(API_EXPLORE_TENANTS);
    }

    getStudentAvatarUrl(cloudId, size = "100") {
        return API_STUDENT_ID_AVATAR + "=" + cloudId + "&size=" + size;
    }

    getPublicGroupsForTenant(tenant_id) {
        return this._get(
            API_EXPLORE_TENANTS_PUBLIC_GROUPS.replace("{tenant_id}", tenant_id)
        );
    }

    excelImportUsers(file) {
        let formData = new FormData();
        formData.append("file", file);
        return fetch(BASE + API_CLOUD_USERS_IMPORT, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    listInteractiveCourseBadge(interactiveCourseId) {
        const endpoint = API_INTERACTIVE_COURSE_BADGE_LIST.replace(
            "{interactive_course_id}",
            interactiveCourseId
        );
        return this._get(endpoint);
    }

    createInteractiveCourseBadge(
        interactiveCourseId,
        name,
        image,
        deleteImage
    ) {
        const endpoint = API_INTERACTIVE_COURSE_BADGE_CREATE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        );
        let formData = new FormData();
        formData.append("image", image);
        formData.append("name", name);
        formData.append("deleteImage", deleteImage ? 1 : 0);
        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    updateInteractiveCourseBadge(
        interactiveCourseId,
        id,
        name,
        image,
        deleteImage
    ) {
        const endpoint = API_INTERACTIVE_COURSE_BADGE_UPDATE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        ).replace("{interactive_course_badge_id}", id);

        let formData = new FormData();
        formData.append("image", image);
        formData.append("name", name);
        formData.append("deleteImage", deleteImage ? 1 : 0);

        return fetch(BASE + endpoint, {
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
            method: "POST",
            body: formData,
        }).then((resp) => resp.json()).then(resp => {
            if(this._enableEncryption(resp))
                resp.payload = JSON.parse(this._decryptResponse(resp.payload))
            return resp
        });
    }

    listInteractiveCourseLevel(interactiveCourseId) {
        const endpoint = API_INTERACTIVE_COURSE_LEVEL_LIST.replace(
            "{interactive_course_id}",
            interactiveCourseId
        );
        return this._get(endpoint);
    }

    addInteractiveCourseLevel(interactiveCourseId,name) {
        const endpoint = API_INTERACTIVE_COURSE_LEVEL_CREATE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        );
        return this._post(endpoint,{name});
    }

    updateInteractiveCourseLevel(interactiveCourseId,level_id,names) {
        const endpoint = API_INTERACTIVE_COURSE_LEVEL_UPDATE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        ).replace(
            "{level_id}",
            level_id
        );
        return this._post(endpoint,{names});
    }

    deleteInteractiveCourseLevel(interactiveCourseId, level_id) {
        const endpoint = API_INTERACTIVE_COURSE_LEVEL_DELETE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        ).replace(
            "{level_id}",
            level_id
        );
        return this._post(endpoint);
    }

    listInteractiveCourseAttendees(interactiveCourseId, sectionId) {
        const endpoint = API_INTERACTIVE_COURSE_ATTENDEE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        );
        return this._get(endpoint, {sectionId: sectionId});
    }

    deleteInteractiveCourseBadge(interactiveCourseId, id) {
        const endpoint = API_INTERACTIVE_COURSE_BADGE_DELETE.replace(
            "{interactive_course_id}",
            interactiveCourseId
        ).replace("{interactive_course_badge_id}", id);
        return this._post(endpoint);
    }

    createInteractiveCourseExecution(interactiveCourseId) {
        return this._post(
            API_INTERACTIVE_COURSE_EXECUTION_CREATE.replace(
                "{interactive_course_id}",
                interactiveCourseId
            )
        );
    }

    createInteractiveCourseExecutionProgress(
        interactiveCourseId,
        courseExecutionId,
        learnContentId,
        topicId,
        success,
        progress
    ) {
        return this._post(
            API_INTERACTIVE_COURSE_EXECUTION_PROGRESS_CREATE.replace(
                "{interactive_course_execution_id}",
                courseExecutionId
            ).replace("{interactive_course_id}", interactiveCourseId),
            {
                learn_content_id: learnContentId,
                topic_id: topicId,
                success,
                progress,
            }
        );
    }

    updateInteractiveCourseExecutionProgress(
        interactiveCourseId,
        courseExecutionId,
        success,
        progress
    ) {
        return this._post(
            API_INTERACTIVE_COURSE_EXECUTION_PROGRESS_UPDATE.replace(
                "{interactive_course_execution_id}",
                courseExecutionId
            ).replace("{interactive_course_id}", interactiveCourseId),
            { success, progress }
        );
    }

    getLearnBibCategoryImageUrl(id, size = "35", image = "") {
        return (
            API_LEARN_CONTENT_CATEGORY_IMAGE +
            "=" +
            id +
            "&size=" +
            size +
            "&name=" +
            image
        );
    }

    getInteractiveCourseBadgeImageUrl(id, size = "35", image = "") {
        return (
            API_INTERACTIVE_COURSE_BADGE_IMAGE +
            "=" +
            id +
            "&size=" +
            size +
            "&name=" +
            image
        );
    }

    getTenantLogoUrl(filename) {
        return "/storage/images/tenants/" + filename;
    }

    getTenantCoverUrl(filename) {
        return "/storage/images/tenants/" + filename;
    }

    aiCompleteTextbox(text) {
        return this._get(API_AI_COMPLETE_TEXTBOX, { q: text });
    }

    // Adressbook
    getAddressbook() {
        return this._get(API_ADDRESS_BOOK_GET);
    }

    loadAllAddressbookEntries() {
        return this._get(API_ADDRESS_BOOK_LIST);
    }

    addContact(contact) {
        return this._post(API_ADDRESS_BOOK_ADD, { contact: contact });
    }

    updateContact(contact) {
        return this._post(API_ADDRESS_BOOK_UPDATE, { contact: contact });
    }

    deleteContact(contact) {
        return this._post(API_ADDRESS_BOOK_DELETE, { contact: contact });
    }

    sendMail(
        email,
        name,
        isMailAnonymized,
        subject,
        message,
        file1,
        file2,
        file3,
        progress
    ) {
        const config = {
            onUploadProgress: progress,
            headers: {
                Accept: "*/*",
                ...this._getAuthHeaderObj(),
            },
        };
        let endpoint = API_ADDRESS_BOOK_MAIL;

        let formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("isMailAnonymized", isMailAnonymized);
        formData.append("subject", subject);
        formData.append("message", message);
        formData.append("file1", file1);
        formData.append("file2", file2);
        formData.append("file3", file3);

        return axios
            .post(BASE + endpoint, formData ? formData : null, config)
            .then((resp) => resp.data);
    }

    loadFrameConfiguration()
    {
        return this._get(API_FRAME_CONIGURATION);
    }

    callRIOSCommand(selfService, content)
    {
        return this._post(API_RIOS_CALL, {selfService, content});
    }
}

let AjaxHelper = new EducaAjaxHelper();

export default AjaxHelper;
