import {redux_store} from './../../store'

const P_GROUPS_CREATE = 'social.group.create'
const P_GROUPS_APP_OPEN = 'social.open'
const P_LOGIN_ALLOWED = 'login.allowed'

const P_GROUPS_APP_BLOCK = 'social.block'
const P_GROUPS_APP_REPORT = 'social.report'

const P_GROUP_MEMBER_EDIT = 'group.member.edit'
const P_GROUP_ROLE_EDIT = 'group.role.edit'
const P_GROUP_EDIT  = 'group.edit'
const P_GROUP_ARCHIVE  = 'group.archive'
const P_GROUP_DELETE  = 'group.delete'
const P_GROUP_SECTION_CREATE = 'group.section.create'

const P_TASKS_APP_OPEN = 'task.open'
const P_TASKS_CREATE = 'task.create'
const P_TASKS_RECEIVE = 'task.receive'
const P_TASKS_FORM_CREATE = 'task.form.create'


const P_CALENDAR_APP_OPEN = 'calendar.open'
const P_CALENDAR_CREATE_EVENT = 'calendar.create'
const P_CALENDAR_VIEW_OUTLOOK = 'calendar.view.outlook'


const P_MESSAGES_OPEN = "messages.open"
const P_MESSAGES_CREATE = "messages.create"
const P_MESSAGES_CHAT_CREATE = "messages.chat.create"

const P_MEETING_VIEW = "meeting.view";
const P_MEETING_MODERATOR = "meeting.moderator";
const P_MEETING_EDIT = "meeting.edit";

const P_ANNOUNCEMENT_OPEN = "announcement.open"
const P_ANNOUNCEMENT_CREATE = "announcement.create"
const P_ANNOUNCEMENT_LIKE = "announcement.like"
const P_ANNOUNCEMENT_COMMENT = "announcement.comment"

const P_FILES_OPEN = "files.open"
const P_FILES_EDIT = "files.edit"
const P_FILES_UPLOAD = "files.upload"

const P_LEARNCONTENT_CREATE = 'learnContent.create'
const P_LEARNCONTENT_EDIT_ALL = 'learnContent.edit'
const P_LEARNCONTENT_CATEGORY_CREATE = "learnContent.category.create"
const P_LEARNCONTENT_TAGS_CREATE = "learnContent.tags.create";
const P_LEARNCONTENT_CATEGORY_EDIT = "learnContent.category.edit"
const P_LEARNCONTENT_LIKE = "learnContent.like"
const P_LEARNCONTENT_COMMENT = "learnContent.comment"
const P_LEARNCONTENT_BOOKMARK = "learnContent.bookmark"
const P_LEARNCONTENT_COMPETENCES = "learnContent.competences";
const P_LEARNCONTENT_PERMISSIONS = "learnContent.permissions";
const P_LEARNCONTENT_DEVELOPER = "learnContent.developer"
const P_LEARNCONTENT_DEVELOPER_XAPI = "learnContent.developer.xapi"
const P_LEARNCONTENT_SELECT_FROM_LIBRARY = "learnContent.selectFromLibrary"


const P_ACCESSCODE_VIEW = "accesscode.view"
const P_WIKI_EDIT = "wiki.edit"
const P_WIKI_OPEN = "wiki.open"

const P_SECTION_VIEW = "section.view"
const P_SECTION_EDIT = "section.edit"

const P_INTERACTIVE_COURSE_VIEW = "interactive_course.open"
const P_INTERACTIVE_COURSE_EDIT = "interactive_course.create"
const P_INTERACTIVE_COURSE_ANALYTICS = "interactive_course.analytics"

const P_CLOUD_MANAGE_OPEN = "cloud.manage.open"
const P_CLOUD_MANAGE_STATS = "cloud.manage.stats";
const P_CLOUD_MANAGE_RIGHTS = "cloud.manage.rights"
const P_CLOUD_MANAGE_USERS = "cloud.manage.user"
const P_CLOUD_MANAGE_CLOUD = "cloud.manage.cloud"
const P_CLOUD_MANAGE_GROUPS = "cloud.manage.groups"
const P_CLOUD_MANAGE_ANALYTICS = "cloud.manage.analytics"
const P_CLOUD_MANAGE_TENANTS = "cloud.manage.tenants"
const P_CLOUD_MANAGE_MAINTENANCE = "cloud.manage.maintenance"

const P_OPENAST_OPEN = "opencast.open";
const P_OPENAST_EDIT = "opencast.edit";

const P_EDU_OPEN = "edu.open"

const P_STORE_COIN_SHOW = "store.coin.show";
const P_STORE_COIN_COLLECT = "store.coin.collect";

const P_CONTACTS_OPEN = "contacts.open";

const P_FEED_STATISTICS = "feed.statistics";

export const GROUP_SETTINGS_PERMISSIONS =
    [
        { label : "Gruppe bearbeiten", value : P_GROUP_EDIT, iconClass : "fas fa-users" },
        { label : "Gruppenmitglieder bearbeiten", value : P_GROUP_MEMBER_EDIT, iconClass : "fas fa-users"  },
        { label : "Gruppenrollen bearbeiten", value : P_GROUP_ROLE_EDIT, iconClass : "fas fa-users"  },
        { label : "Gruppe archivieren", value : P_GROUP_ARCHIVE, iconClass : "fas fa-users"  },
        { label : "Gruppe löschen", value : P_GROUP_DELETE, iconClass : "fas fa-users"  },
        { label : "Gruppenbereiche erstellen", value : P_GROUP_SECTION_CREATE, iconClass : "fas fa-users"  },
    ]

export const SECTION_SETTINGS_PERMISSIONS =
    [
        { label : "Bereich öffnen", value : P_SECTION_VIEW, iconClass : "fas fa-users"  },
        { label : "Bereich bearbeiten", value : P_SECTION_EDIT, iconClass : "fas fa-users"  },
        { label : "Ankündigungen öffnen", value : P_ANNOUNCEMENT_OPEN, iconClass : "fas fa-bullhorn" },
        { label : "Ankündigungen erstellen", value : P_ANNOUNCEMENT_CREATE, iconClass : "fas fa-bullhorn" },
        { label : "Ankündigungen liken", value : P_ANNOUNCEMENT_LIKE, iconClass : "fas fa-bullhorn" },
        { label : "Ankündigungen kommentieren", value : P_ANNOUNCEMENT_COMMENT, iconClass : "fas fa-bullhorn" },
        { label : "Nachrichten öffnen", value : P_MESSAGES_OPEN, iconClass : "fas fa-envelope" },
        { label : "Nachrichten erstellen", value : P_MESSAGES_CREATE, iconClass : "fas fa-envelope"  },
        { label : "Kalender öffnen", value : P_CALENDAR_APP_OPEN, iconClass : "fas fa-calendar-alt" },
        { label : "Kalendertermin erstellen", value :  P_CALENDAR_CREATE_EVENT, iconClass : "fas fa-calendar-alt"},
        { label : "Aufgaben öffnen", value : P_TASKS_APP_OPEN, iconClass : "fas fa-calendar-alt" },
        { label : "Aufgaben erstellen", value :  P_TASKS_CREATE, iconClass : "fas fa-calendar-alt"},
        { label : "Aufgaben erhalten", value :  P_TASKS_RECEIVE, iconClass : "fas fa-calendar-alt"},
        { label : "Dateien-App öffnen", value : P_FILES_OPEN, iconClass : "fas fa-file" },
        { label : "Dateien bearbeiten", value : P_FILES_EDIT, iconClass : "fas fa-file" },
        { label : "Dateien hochladen", value : P_FILES_UPLOAD, iconClass : "fas fa-file" },
        { label : "Zugriffscode öffnen", value : P_ACCESSCODE_VIEW, iconClass : "fas fa-qrcode" },
        { label : "Wiki öffnen", value : P_WIKI_OPEN, iconClass : "fas fa-atlas" },
        { label : "Wiki bearbeiten", value : P_WIKI_EDIT, iconClass : "fas fa-atlas" },
        { label : "Interaktiven Kurs öffnen", value : P_INTERACTIVE_COURSE_VIEW, iconClass : "fas fa-object-group" },
        { label : "Interaktiven Kurs bearbeiten", value : P_INTERACTIVE_COURSE_EDIT, iconClass : "fas fa-object-group" },
        { label : "Interaktiven Kurs Analyse", value : P_INTERACTIVE_COURSE_ANALYTICS, iconClass : "fas fa-object-group" },
        { label : "Meeting öffnen", value : P_MEETING_VIEW, iconClass : "fas fa-video" },
        { label : "Meeting Moderator-Rechte", value : P_MEETING_MODERATOR, iconClass : "fas fa-video" },
        { label : "Meeting bearbeiten", value : P_MEETING_EDIT, iconClass : "fas fa-video" },
    ]

class EducaPermissionsManagerClass {

    _globals() {
        let g = redux_store.getState()?.currentCloudUser?.permissions_global
        return g ? g : []
    }

    _globalsContain(permission) {
        let globals = this._globals()
        if (Array.isArray(globals) && globals.find(p => p === permission))
            return true
        return false
    }

    _sectionLookup(id, permission) {
        let groups = redux_store.getState()?.currentCloudUser?.groups
        let subgroup = null
        for (let i = 0; i < groups?.length; i++) {
            subgroup = groups[i].sections?.find(s => s.id === id)
            if (subgroup)
                break
        }
        if (subgroup) {
            return !!subgroup?.permissions?.find(p => p === permission)
        }
        return false
    }

    _groupLookup(id, permission) {
        let groups = redux_store.getState()?.currentCloudUser?.groups
        let group = groups?.find(g => g.id === id)

        if (group) {
            return false
            return !!group?.permissions?.find(p => p === permission)
        }
        return false
    }

    /**
     * GROUPS
     */
    globalGroupView() {
        return this._globalsContain(P_GROUPS_APP_OPEN)
    }

    globalGroupCreate() {
        return this._globalsContain(P_GROUPS_CREATE)
    }

    globalCanReport() {
        return this._globalsContain(P_GROUPS_APP_BLOCK)
    }

    globalCanEdu() {
        return this._globalsContain(P_EDU_OPEN)
    }

    globalCanBlock() {
        return this._globalsContain(P_GROUPS_APP_REPORT)
    }

    globalLoginAllowed() {
        return this._globalsContain(P_LOGIN_ALLOWED)
    }

    /**
     * TASKS
     */
    globalTaskView() {
        return this._globalsContain(P_TASKS_APP_OPEN)
    }

    globalTaskCreate() {
        return this._globalsContain(P_TASKS_CREATE)
    }

    globalLearnContentCreate() {
        return this._globalsContain(P_LEARNCONTENT_CREATE)
    }

    globalLearnContentEdit() {
        return this._globalsContain(P_LEARNCONTENT_EDIT_ALL)
    }

    globalLearnContentTagsCreate() {
        return this._globalsContain(P_LEARNCONTENT_TAGS_CREATE)
    }

    globalLearnContentCategoryCreate() {
        return this._globalsContain(P_LEARNCONTENT_CATEGORY_CREATE)
    }

    globalLearnContentCategoryEdit() {
        return this._globalsContain(P_LEARNCONTENT_CATEGORY_EDIT)
    }

    globalLearnContentComment() {
        return this._globalsContain(P_LEARNCONTENT_COMMENT)
    }

    globalLearnContentLike() {
        return this._globalsContain(P_LEARNCONTENT_LIKE)
    }

    globalLearnContentBookmark() {
        return this._globalsContain(P_LEARNCONTENT_BOOKMARK)
    }

    globalLearnContentDeveloper() {
        return this._globalsContain(P_LEARNCONTENT_DEVELOPER)
    }

    globalLearnContentPermissions() {
        return this._globalsContain(P_LEARNCONTENT_PERMISSIONS)
    }

    globalLearnContentCompetences() {
        return this._globalsContain(P_LEARNCONTENT_COMPETENCES)
    }

    globalLearnContentDeveloperxAPI() {
        return this._globalsContain(P_LEARNCONTENT_DEVELOPER_XAPI)
    }

    globalLearnContentSelectFromLibrary() {
        return this._globalsContain(P_LEARNCONTENT_SELECT_FROM_LIBRARY)
    }

    globalTaskFormCreate() {
        return true;
    }

    /**
     * CALENDAR
     */
    globalCalendarView() {
        return this._globalsContain(P_CALENDAR_APP_OPEN)
    }

    globalCalendarEventCreate() {
        return this._globalsContain(P_CALENDAR_CREATE_EVENT)
    }

    globalCalendarEventOutlook() {
        return this._globalsContain(P_CALENDAR_VIEW_OUTLOOK)
    }

    /**
     * MESSAGES
     */
    globalMessagesView() {
        return this._globalsContain(P_MESSAGES_OPEN)
    }

    globalMessagesCreate() {
        return this._globalsContain(P_MESSAGES_CREATE)
    }

    globalMessagesChatCreate() {
        return this._globalsContain(P_MESSAGES_CHAT_CREATE)
    }

    globalWikiOpen() {
        return this._globalsContain(P_WIKI_OPEN)
    }

    globalWikiEdit() {
        return this._globalsContain(P_WIKI_EDIT)
    }

    /***** GROUPS *****/

    _groupBaseCheck(group, id = null, permissions)
    {
        if (id)
            return this._groupLookup(id, permissions)
        return !!group?.permissions?.find(p => p === permissions)
    }
    groupEditGroup(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_EDIT)
    }

    groupEditMember(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_MEMBER_EDIT)
    }

    groupEditRoles(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_ROLE_EDIT)
    }

    groupArchiveGroup(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_ARCHIVE)
    }

    groupDeleteGroup(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_DELETE)
    }

    groupCreateSection(group, id = null)
    {
        return this._groupBaseCheck(group, id,P_GROUP_SECTION_CREATE)
    }

    globalStoreCoinShow() {
        return this._globalsContain(P_STORE_COIN_SHOW)
    }

    /**** SECTIONS ******/

    _sectionBaseCheck(section, id = null, permission)
    {
        if (id)
            return this._sectionLookup(id, permission)
        return !!section?.permissions?.find(p => p === permission)
    }

    sectionViewSection(section, id = null) {
        return this._sectionBaseCheck(section,id,P_SECTION_VIEW )
    }

    sectionEditSection(section, id = null) {
        return this._sectionBaseCheck(section,id, P_SECTION_EDIT)
    }
    //Messages
    sectionMessagesView(section, id = null) {
        return this._sectionBaseCheck(section,id, P_MESSAGES_OPEN)
    }

    sectionMessagesCreate(section, id = null) {
        return this._sectionBaseCheck(section,id, P_MESSAGES_CREATE)
    }

    //Calendar
    sectionCalendarView(section, id = null) {
        return this._sectionBaseCheck(section,id, P_CALENDAR_APP_OPEN)
    }

    sectionCalendarEdit(section, id = null) {
        return this._sectionBaseCheck(section,id, P_CALENDAR_CREATE_EVENT)
    }

    sectionAnnouncementView(section, id = null) {
        return this._sectionBaseCheck(section,id, P_ANNOUNCEMENT_OPEN)
    }

    sectionAnnouncementCreate(section, id = null) {
        return this._sectionBaseCheck(section,id, P_ANNOUNCEMENT_CREATE)
    }

    sectionAnnouncementLike(section, id = null) {
        return this._sectionBaseCheck(section,id,P_ANNOUNCEMENT_LIKE )
    }

    sectionAnnouncementComment(section, id = null) {
        return this._sectionBaseCheck(section,id,P_ANNOUNCEMENT_COMMENT )
    }

    sectionFilesView(section, id = null) {
        return this._sectionBaseCheck(section,id,P_FILES_OPEN )
    }

    sectionMeetingView(section, id = null) {
        return this._sectionBaseCheck(section,id,P_MEETING_VIEW )
    }

    sectionMeetingEdit(section, id = null) {
        return this._sectionBaseCheck(section,id,P_MEETING_EDIT )
    }

    sectionFilesEdit(section, id = null) {
        return this._sectionBaseCheck(section,id, P_FILES_EDIT)
    }

    sectionFilesUpload(section, id = null) {
        return this._sectionBaseCheck(section,id, P_FILES_UPLOAD)
    }

    sectionTaskView(section, id = null) {
        return this._sectionBaseCheck(section,id,P_TASKS_APP_OPEN )
    }

    sectionTaskCreate(section, id = null) {
        return this._sectionBaseCheck(section,id, P_TASKS_CREATE)
    }

    sectionAccesscodeView(section, id = null) {
        return this._sectionBaseCheck(section,id, P_ACCESSCODE_VIEW)
    }

    sectionWikiEdit(section, id = null) {
        return this._sectionBaseCheck(section,id, P_WIKI_EDIT)
    }

    sectionWikiOpen(section, id = null) {
        return this._sectionBaseCheck(section,id, P_WIKI_OPEN)
    }
    sectionInteractiveCourseView(section, id = null) {
        return this._sectionBaseCheck(section,id,P_INTERACTIVE_COURSE_VIEW )
    }
    sectionInteractiveCourseEdit(section, id = null) {
        return this._sectionBaseCheck(section,id,P_INTERACTIVE_COURSE_EDIT )
    }

    sectionInteractiveCourseAnalytics(section, id = null) {
        return this._sectionBaseCheck(section,id,P_INTERACTIVE_COURSE_ANALYTICS )
    }

    sectionOpencastOpen(section, id = null) {
        return this._sectionBaseCheck(section,id, P_OPENAST_OPEN)
    }

    sectionOpencastEdit(section, id = null) {
        return this._sectionBaseCheck(section,id, P_OPENAST_EDIT)
    }
    /**
     *
     */

    systemSettingsOpen(){
        return this._globalsContain(P_CLOUD_MANAGE_OPEN)
    }

    systemSettingsManageStats(){
        return this._globalsContain(P_CLOUD_MANAGE_STATS)
    }

    systemSettingsManageUsers(){
        return this._globalsContain(P_CLOUD_MANAGE_USERS)
    }

    systemSettingsManagePermissions(){
        return this._globalsContain(P_CLOUD_MANAGE_RIGHTS)
    }

    systemSettingsManageAnalytics(){
        return this._globalsContain(P_CLOUD_MANAGE_ANALYTICS)
    }

    systemSettingsManageGroups(){
        return this._globalsContain(P_CLOUD_MANAGE_GROUPS)
    }

    systemSettingsManageTenants(){
        return this._globalsContain(P_CLOUD_MANAGE_TENANTS)
    }

    systemSettingsMaintenance(){
        return this._globalsContain(P_CLOUD_MANAGE_MAINTENANCE)
    }

    globalAdressbookOpen(){
        return this._globalsContain(P_CONTACTS_OPEN)
    }

    globalFeedStatistics() {
        return this._globalsContain(P_FEED_STATISTICS) || true;
    }
}


const FliesentischZentralrat = new EducaPermissionsManagerClass()
export default FliesentischZentralrat
