<?php

namespace App\Console;

use App\Console\Commands\A5\A5ExcelSubjectMigrator;
use App\Console\Commands\A5\A5NotenMigrator;
use App\Console\Commands\Delete\DeleteAll;
use App\Console\Commands\Delete\DeleteBeitrage;
use App\Console\Commands\Delete\DeleteDozent;
use App\Console\Commands\Delete\DeleteKohorte;
use App\Console\Commands\Delete\DeleteKontakte;
use App\Console\Commands\Delete\DeleteLehrplans;
use App\Console\Commands\Delete\DeleteRooms;
use App\Console\Commands\Delete\DeleteSchoolClass;
use App\Console\Commands\Delete\DeleteStudents;
use App\Console\Commands\Delete\DeleteSubjects;
use App\Console\Commands\A5\A5PersonMigrator;
use App\Console\Commands\A5\A5StudyProgressMigrator;
use App\Console\Commands\A5\A5SubjectMigrator;
use App\Console\Commands\ActiveMeetingWatch;
use App\Console\Commands\CloudIDChecker;
use App\Console\Commands\Delete\DeleteStudyProgress;
use App\Console\Commands\EducaStuPlaSync;
use App\Console\Commands\GenerateTranslation;
use App\Console\Commands\IBA\EducaIBAGroupCreator;
use App\Console\Commands\IBA\StuPlaNotenIBACalculator;
use App\Console\Commands\MeltDozentenDuplicates;
use App\Console\Commands\A5\A5Migrator;
use App\Console\Commands\H5P\CompressFiles;
use App\Console\Commands\MigrateMultipleCourse;
use App\Console\Commands\NotifiyAppointment;
use App\Console\Commands\NotifiyTask;
use App\Console\Commands\IBAMaker;
use App\Console\Commands\Repair\RepairFSPlannungsruppe;
use App\Console\Commands\Repair\RepairGroupPermission;
use App\Console\Commands\RIOS\RIOSEventImporter;
use App\Console\Commands\RIOS\RIOSGroupImporter;
use App\Console\Commands\SchulePlus\SchulePlusMigrator;
use App\Console\Commands\StuPlaAufgabenWatch;
use App\Console\Commands\StuPlaImportTask;
use App\Console\Commands\xAPI\SyncStatements;
use App\Console\Commands\Tools\CopySchoolYears;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
