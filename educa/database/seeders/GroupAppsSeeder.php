<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class GroupAppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // WICHTIG: Diese Zeile wird zwingend benötigt, sonst failt viel
        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 1,
            'name' => "Ankündigungen",
            'type' => 'announcement',
            'icon' => 'fa fa-bullhorn'
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 2,
            'name' => "Chat",
            'type' => 'chat',
            'icon' => 'fas fa-comment-dots',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 3,
            'name' => "Kalender",
            'type' => 'calendar',
            'icon' => 'fa fa-calendar-alt',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 4,
            'name' => "Aufgaben",
            'type' => 'task',
            'icon' => 'fa fa-tasks',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 5,
            'name' => "Dateien",
            'type' => 'files',
            'icon' => 'fa fa-folder-open',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 7,
            'name' => "Zugangscode",
            'type' => 'accessCode',
            'icon' => 'fas fa-unlock-alt',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 8,
            'name' => "Interaktiver Kurs",
            'type' => 'h5pCourse',
            'icon' => 'fas fa-object-group',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 9,
            'name' => "Wiki-Seite",
            'type' => 'wikiPage',
            'icon' => 'fas fa-atlas',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 10,
            'name' => "Moodle",
            'type' => 'moodle',
            'icon' => '/images/moodle.png',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 11,
            'name' => "NextCloud",
            'type' => 'nextcloud',
            'icon' => '/images/nextcloud.svg',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 12,
            'name' => "Videokonferenz",
            'type' => 'meeting',
            'icon' => 'fas fa-video',
        ]);

        \Illuminate\Support\Facades\DB::table('group_apps')->insert([
            'id' => 13,
            'name' => "OpenCast",
            'type' => 'opencast',
            'icon' => '/images/opencast.png',
        ]);

    }
}
