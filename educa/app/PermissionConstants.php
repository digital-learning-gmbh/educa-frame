<?php

namespace App;

class PermissionConstants
{

    //SCOPES

    public const SCOPE_GROUP                                      = "group";
    public const SCOPE_SECTION                                    = "section";

    //GLOBALS
    //verwaltung
    public const ADMINISTRATION_DOZENT_STUPLA_SCHOOL              = "dozent.stundenplan.schule";
    public const ADMINISTRATION_STUPLA_EDIT_AFTER_TIME            = "verwaltung.stundenplan.editAfterTime";
    public const ADMINISTRATION_TEACHING_PLAN_EDIT                = "verwaltung.lehrplan.edit";
    public const ADMINISTRATION_CONTACTS_EDIT                     = "verwaltung.kontakte.edit";
    public const ADMINISTRATION_CONTACTS_ACCESS_EDIT              = "verwaltung.kontakte.zugang.edit";
    public const ADMINISTRATION_CONTACTS_PRACTICAL_CAPACITY_EDIT  = "verwaltung.kontakte.praxiskapazitaet.edit";
    public const ADMINISTRATION_CONTACTS_RELATION_EDIT            = "verwaltung.kontakte.beziehung.edit";
    public const ADMINISTRATION_EMPLOYEES_ALL_EDIT                = "verwaltung.mitarbeiter.edit_all";

    // verwaltung iba

    public const ADMINISTRATION_VIEW_ALL_STUPLA_SCHOOL            = "verwaltung.schule.all";
    public const ADMINISTRATION_VIEW_STUDENTS                     = "verwaltung.students.view";
    public const ADMINISTRATION_VIEW_DOZENTEN                     = "verwaltung.dozenten.view";
    public const ADMINISTRATION_VIEW_CONTACTS                     = "verwaltung.contacts.view";
    public const ADMINISTRATION_VIEW_EMPLOYEE                     = "verwaltung.employees.view";
    public const ADMINISTRATION_VIEW_ALUMNI                       = "verwaltung.alumni.view";

    public const ADMINISTRATION_EDIT_STUDENT_STAPEL_IMMA          = "verwaltung.student.immaStapel";
    public const ADMINISTRATION_EDIT_STUDENT_CREATE               = "verwaltung.student.create";
    public const ADMINISTRATION_EDIT_STUDENT_EDIT                 = "verwaltung.student.edit";
    public const ADMINISTRATION_EDIT_STUDENT_STUDY_PROGESS_MANUAL = "verwaltung.student.manualProgessEntry";
    public const ADMINISTRATION_EDIT_DOZENT_CREATE                = "verwaltung.dozent.create";
    public const ADMINISTRATION_EDIT_DOZENT_EDIT                  = "verwaltung.dozent.edit";
    public const ADMINISTRATION_EDIT_CONTACT_CREATE               = "verwaltung.contacts.create";


    public const ADMINISTRATION_DELETE_DOZENT                     = "verwaltung.dozent.delete";
    public const ADMINISTRATION_DELETE_STUDENT                    = "verwaltung.student.delete";
    public const ADMINISTRATION_DELETE_CONTACT                    = "verwaltung.contacts.delete";

    public const ADMINISTRATION_CURRICULA_VIEW                   = "verwaltung.curricula.view";
    public const ADMINISTRATION_CURRICULA_EDIT                   = "verwaltung.curricula.edit";
    public const ADMINISTRATION_KOHORTEN_EDIT                    = "verwaltung.kohorten.edit";
    public const ADMINISTRATION_SUBJECT_EDIT                     = "verwaltung.subject.edit";
    public const ADMINISTRATION_MODULE_EDIT                      = "verwaltung.module.edit";
    public const ADMINISTRATION_COURSE_EDIT                      = "verwaltung.course.edit";
    public const ADMINISTRATION_COURSE_BLOCK_EDIT                = "verwaltung.course.block.edit";

    public const ADMINISTRATION_STUDENT_DOCUMENTS                = "verwaltung.student.documents.view";
    public const ADMINISTRATION_STUDENT_DOCUMENTS_EDIT           = "verwaltung.student.documents.edit";

    public const ADMINISTRATION_SCHOOLS_VIEW                     = "verwaltung.schools.view";
    public const ADMINISTRATION_SCHOOLS_EDIT                     = "verwaltung.schools.edit";
    public const ADMINISTRATION_SCHOOLS_SEMESTER_CREATE          = "verwaltung.schools.semester.edit";

    public const ADMINISTRATION_EXAM_VIEW_RESULT                 = "verwaltung.exam.result.view";
    public const ADMINISTRATION_EXAM_EDIT                        = "verwaltung.exam.edit";
    public const ADMINISTRATION_EXAM_DELETE                      = "verwaltung.exam.delete";
    public const ADMINISTRATION_EXAM_CORRECT                     = "verwaltung.exam.correct";
    public const ADMINISTRATION_EXAM_LOGFILES                    = "verwaltung.exam.logfiles";
    public const ADMINISTRATION_EXAM_PUBLIC                      = "verwaltung.exam.public";
    public const ADMINISTRATION_EXAM_PUBLIC_EDIT                 = "verwaltung.exam.public.edit";

    public const ADMINISTRATION_EXAM_CERTIFICATE_PRINT           = "verwaltung.exam.certificate.print";

    public const ADMINISTRATION_TIMETABLE_VIEW                   = "verwaltung.timetable.view";
    public const ADMINISTRATION_TIMETABLE_EDIT                   = "verwaltung.timetable.edit";

    public const ADMINISTRATION_ANALYTICS_VIEW                   = "verwaltung.analytics.view";
    public const ADMINISTRATION_ANALYTICS_EDIT                   = "verwaltung.analytics.edit";

    public const ADMINISTRATION_COURSE_BOOK_VIEW                 = "verwaltung.course_book.view";
    public const ADMINISTRATION_COURSE_BOOK_ATTENDANCES          = "verwaltung.course_book.attendances";
    public const EDUCA_CHAT_REPORT                                = "chat.report";
    public const EDUCA_CHAT_BLOCK                                 = "chat.block";


    //educa
    public const EDUCA_CLASSBOOK_OPEN                             = "klassenbuch.open";
    public const EDUCA_LOGIN                                      = "login.allowed";
    public const EDUCA_LOGIN_APP                                      = "login.app.allowed";

    public const EDUCA_FEED_SHOW_STATISTICS = "feed.statistics";

    public const EDUCA_HOME_OPEN                             = "home.open";
    public const EDUCA_SOCIAL_OPEN                                = "social.open";
    public const EDUCA_SOCIAL_BLOCK                               = "social.block";
    public const EDUCA_SOCIAL_REPORT                              = "social.report";
    public const EDUCA_SOCIAL_GROUP_CREATE                        = "social.group.create";
    public const EDUCA_TASK_OPEN                                  = "task.open";
    public const EDUCA_TASK_CREATE                                = "task.create";
    public const EDUCA_TASK_FORM                                  = "task.form.create";
    public const EDUCA_EDU_OPEN                                   = "edu.open";
    public const EDUCA_EDU_SHARE_FILES                            = "edu.share.files";
    public const EDUCA_EDU_MOODLE                                 = "edu.mooodle";
    public const EDUCA_EDU_ULMER                                  = "edu.ulmer";
    public const EDUCA_DEVICES_OPEN                               = "devices.open";
    public const EDUCA_DEVICES_MANAGE                             = "devices.manage";
    public const EDUCA_CALENDAR_OPEN                              = "calendar.open";
    public const EDUCA_CALENDAR_CREATE                            = "calendar.create";
    public const EDUCA_CALENDAR_EDIT_ALL                          = "calendar.edit.all";
    public const EDUCA_CALENDAR_CAN_DISCARD_INVITES               = "calendar.can.discard.invites";
    public const EDUCA_CALENDAR_VIEW_OUTLOOK                      = "calendar.view.outlook";
    public const EDUCA_MESSAGES_OPEN                              = "messages.open";
    public const EDUCA_MESSAGES_CREATE                            = "messages.create";
    public const EDUCA_MESSAGES_CHAT_CREATE                       = "messages.chat.create";
    public const EDUCA_EXPLORER_OPEN                              = "explorer.open";

    public const EDUCA_LEARN_CONTENT_CREATE                       = "learnContent.create";
    public const EDUCA_LEARN_CONTENT_EDIT_ALL                     = "learnContent.edit";
    public const EDUCA_LEARN_CONTENT_SELECT_FROM_LIBRARY           = "learnContent.selectFromLibrary";
    public const EDUCA_LEARN_CONTENT_TAGS_CREATE                = "learnContent.tags.create";

    public const EDUCA_LEARN_CONTENT_CATEGORY_CREATE              = "learnContent.category.create";
    public const EDUCA_LEARN_CONTENT_CATEGORY_EDIT                = "learnContent.category.edit";
    public const EDUCA_LEARN_CONTENT_COMMENT                       = "learnContent.comment";
    public const EDUCA_LEARN_CONTENT_LIKE                          = "learnContent.like";

    public const EDUCA_LEARN_CONTENT_CROSS_LINK               = "learnContent.course.crosslink";
    public const EDUCA_LEARN_CONTENT_BOOKMARK                      = "learnContent.bookmark";
    public const EDUCA_LEARN_CONTENT_DEVELOPER                     = "learnContent.developer";
    public const EDUCA_LEARN_CONTENT_XAPI_DEVELOPER                 = "learnContent.developer.xapi";

    public const EDUCA_LEARN_CONTENT_PERMISSIONS                      = "learnContent.permissions";

    public const EDUCA_LEARN_CONTENT_COMPETENCES                      = "learnContent.competences";
    //educa gruppen
    public const EDUCA_GROUP_EDIT                                 = "group.edit";
    public const EDUCA_GROUP_MEMBER_EDIT                          = "group.member.edit";
    public const EDUCA_GROUP_MEMBER_INVITE                        = "group.member.invite";
    public const EDUCA_GROUP_ROLE_EDIT                            = "group.role.edit";
    public const EDUCA_GROUP_ARCHIVE                              = "group.archive";
    public const EDUCA_GROUP_DELETE                               = "group.delete";
    public const EDUCA_GROUP_SECTION_CREATE                       = "group.section.create";
    public const EDUCA_GROUP_MEETING_CREATE                       = "group.meeting.create";
    //educa sections
    public const EDUCA_SECTION_VIEW                               = "section.view";
    public const EDUCA_SECTION_EDIT                               = "section.edit";
    public const EDUCA_SECTION_ANNOUNCEMENT_OPEN                  = "announcement.open";
    public const EDUCA_SECTION_ANNOUNCEMENT_CREATE                = "announcement.create";
    public const EDUCA_SECTION_ANNOUNCEMENT_LIKE                  = "announcement.like";
    public const EDUCA_SECTION_ANNOUNCEMENT_COMMENT               = "announcement.comment";
    public const EDUCA_SECTION_TASK_CREATE                        = "task.create";
    public const EDUCA_SECTION_TASK_OPEN                          = "task.open";
    public const EDUCA_SECTION_TASK_RECEIVE                       = "task.receive";
    public const EDUCA_SECTION_CALENDAR_OPEN                      = "calendar.open";
    public const EDUCA_SECTION_CALENDAR_CREATE                    = "calendar.create";
    public const EDUCA_SECTION_INTERACTIVE_COURSE_OPEN            = "interactive_course.open";
    public const EDUCA_SECTION_INTERACTIVE_COURSE_CREATE          = "interactive_course.create";
    public const EDUCA_SECTION_INTERACTIVE_COURSE_ANALYTICS       = "interactive_course.analytics";
    public const EDUCA_SECTION_FILES_OPEN                         = "files.open";
    public const EDUCA_SECTION_FILES_DOWNLOAD                     = "files.download";
    public const EDUCA_SECTION_FILES_EDIT                         = "files.edit";
    public const EDUCA_SECTION_FILES_DELETE                       = "files.delete";
    public const EDUCA_SECTION_FILES_UPLOAD                       = "files.upload";
    public const EDUCA_SECTION_FILES_CREATE_FOLDER                = "files.create.folder";
    public const EDUCA_SECTION_FILES_PREVIEW                      = "files.preview";
    public const EDUCA_SECTION_ACCESSCODE_VIEW                    = "accesscode.view";

    //Wiki
    public const EDUCA_WIKI_OPEN                                  = "wiki.open";
    public const EDUCA_WIKI_EDIT                                  = "wiki.edit";

    // Documents
    public const EDUCA_DOCUMENTS_OPEN                             = "documents.open";
    public const EDUCA_DOCUMENTS_EDIT                             = "documents.edit";


    // Contacts

    public const EDUCA_CONTACTS_OPEN                               = "contacts.open";

    // Div store stuff
    public const EDUCA_STORE_COIN_SHOW                             = "store.coin.show";
    public const EDUCA_STORE_COIN_COLLECT                          = "store.coin.collect";


    //systemsteuerung
    public const SYSTEM_SETTINGS_CLOUD_OPEN                       = "cloud.manage.open";

    public const SYSTEM_SETTINGS_CLOUD_STATS                      = "cloud.manage.stats";
    public const SYSTEM_SETTINGS_CLOUD_RIGHTS                     = "cloud.manage.rights";
    public const SYSTEM_SETTINGS_CLOUD_USER                       = "cloud.manage.user";
    public const SYSTEM_SETTINGS_CLOUD_CLOUD                      = "cloud.manage.cloud";
    public const SYSTEM_SETTINGS_CLOUD_GROUPS                     = "cloud.manage.groups";
    public const SYSTEM_SETTINGS_CLOUD_ANALYTICS                  = "cloud.manage.analytics";

    public const SYSTEM_SETTINGS_MAINTENANCE                      = "cloud.manage.maintenance";
    public const SYSTEM_SETTINGS_CLOUD_TENANTS                    = "cloud.manage.tenants";
    public const IS_MULTI_TENANT_USER                             = "cloud.isMultiTenantUser";

    // misc
    public const MISC_ANALYTICS_OPEN                              = "analytics.open";
    public const MISC_SETTINGS_USERNAME_EDIT                      = "settings.username.edit";
    public const MISC_SETTINGS_IMAGE_EDIT                         = "settings.image.edit";


    public const EDUCA_MEETING_VIEW                                  = "meeting.view";
    public const EDUCA_MEETING_EDIT                                  = "meeting.edit";
    public const EDUCA_MEETING_MODERATOR                                  = "meeting.moderator";


}
