<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GruppenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('de_DE');
        $faker->addProvider(new \Mmo\Faker\LoremSpaceProvider($faker));
        $this->createSampleGroupWithoutTemplate();
        for($i = 0; $i < 10; $i++) {
            $name = "";
            $type = random_int(0,4);
            if($type == 0)
            {
                $name ="Gruppe ".$i;
            } else if ($type == 1) {
                $name = "Klasse ".$i.$faker->randomLetter;
            } else if($type == 2)
            {
                $name = "Lerngruppe";
            } else if($type == 3)
            {
                $name = "Schule ".$faker->city;
            } else if($type == 4)
            {
                $name = "Kurs ".$faker->jobTitle;
            }
            $this->createSampleGroupWithoutTemplate($name);
        }
        $this->createSampleGroupWithoutTemplate("Archivierte Gruppe", true);
        if (defined('PHPUNIT_YOURAPPLICATION_TESTSUITE') && PHPUNIT_YOURAPPLICATION_TESTSUITE) {

        } else {
            foreach (\App\Group::all() as $cloudId) {
                $img = $this->random_pic(base_path("database/seeders/groupImages"));
                $filename = basename($img);
                copy($img,base_path("storage/app/public/images/groups/".str_replace(".jpg",".png",$filename)));
                $cloudId->image = str_replace(".png", "", str_replace(".jpg",".png",$filename));
                $cloudId->save();
            }
        }

    }


    private function createSampleGroupWithoutTemplate($name = "Demo Gruppe", $archived = false)
    {
        // erstelle eine Testgruppe
        $group = new \App\Group;
        $group->name = $name;
        $group->tenant_id = Tenant::inRandomOrder()->first()->id;
        $group->save();
        $group->createRolesTemplate();

        $cloudIds = \App\CloudID::inRandomOrder()->take(random_int(10, 100))->get();
        //Alle Nutzer zur Gruppe hinzufügen (?)
        foreach ($cloudIds as $cloudID)
        {
            $isAdmin = random_int(0,1) == 1;

            if (defined('PHPUNIT_YOURAPPLICATION_TESTSUITE') && PHPUNIT_YOURAPPLICATION_TESTSUITE) {
                if ($cloudID->id < 20) // damit testen wir :-)
                {
                    $isAdmin = true;
                }
            }
            DB::table("cloudid_group")->insert(Array("cloudid" => $cloudID->id, "group_id" => $group->id));
            if($isAdmin)
            {
                $cloudID->assignRole($group->getAdminRole());
            } else {
                $cloudID->assignRole($group->getMemberRole());
            }
        }

        $sectionMathe = $group->addSection("Allgemein");
        $sectionDeutsch = $group->addSection("Workshop 1");
        $group->addSection("Workshop 2");
        $group->setArchived($archived);
        $sectionMathe->addSectionGroupApp('meeting');
        $sectionMathe->addSectionGroupApp('h5pCourse');
        $sectionMathe->addSectionGroupApp('wikiPage');
        $sectionMathe->addSectionGroupApp('announcement');
        $sectionMathe->addSectionGroupApp('task');
        $sectionMathe->addSectionGroupApp('calendar');
        $sectionMathe->addSectionGroupApp('files');
        $sectionMathe->addSectionGroupApp('accessCode');

        $sectionDeutsch->addSectionGroupApp('announcement');
        $sectionDeutsch->addSectionGroupApp('task');
        $sectionDeutsch->addSectionGroupApp('calendar');
    }

    private function random_pic($dir = 'uploads')
    {
        // print_r($dir);
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
    }
}
