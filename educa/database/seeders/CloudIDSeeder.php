<?php

namespace Database\Seeders;
use App\MailAccount;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CloudIDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('cloud_i_d_s')->insertGetId([
            'name' => "Benjamin Ledel",
            'email' => 'benjamin.ledel@digitallearning.gmbh',
            'password' => bcrypt('educaeduca321'),
            'loginServer' => 'local',
            'loginType' => 'eloquent',
            'created_at' => Carbon::now()
        ]);

        $id = DB::table('cloud_i_d_s')->insertGetId([
            'name' => "Digital Learning Tester",
            'email' => 'support@digitallearning.gmbh',
            'password' => bcrypt('educaeduca321'),
            'loginServer' => 'local',
            'loginType' => 'eloquent',
            'created_at' => Carbon::now()
        ]);


        DB::table('cloud_i_d_s')->insert([
            'name' => "Mario",
            'email' => 'mario.kroesche@digitallearning.gmbh',
            'password' => bcrypt('educaeduca321'),
            'loginServer' => 'local',
            'loginType' => 'eloquent',
            'created_at' => Carbon::now()
        ]);

        DB::table('cloud_i_d_s')->insert([
            'name' => "Lennard",
            'email' => 'lennard.strohmeyer@digitallearning.gmbh',
            'password' => bcrypt('educaeduca321'),
            'loginServer' => 'local',
            'loginType' => 'eloquent',
            'created_at' => Carbon::now()
        ]);

        $numberOfAccounts = 10;
        for($i = 0; $i < $numberOfAccounts; $i++) {
            DB::table('cloud_i_d_s')->insert([
                'name' => "Test Nutzer " . $i,
                'email' => 'test'.$i,
                'password' => bcrypt('test'),
                'loginServer' => 'local',
                'loginType' => 'eloquent',
                'created_at' => Carbon::now()
            ]);
        }

        // all a superadmins
        $cloudids = \App\CloudID::all();
        foreach ($cloudids as $cloudID)
        {
            $cloudID->assignRole("Super-Administrator");
            $cloudID->tenants()->sync(Tenant::all());
        }

        if (defined('PHPUNIT_YOURAPPLICATION_TESTSUITE') && PHPUNIT_YOURAPPLICATION_TESTSUITE) {

        } else {
            $faker = \Faker\Factory::create();
            $faker->addProvider(new \Mmo\Faker\LoremSpaceProvider($faker));
            foreach (\App\CloudID::all() as $cloudId) {
                $img = $this->random_pic(base_path("database/seeders/images"));
                $filename = basename($img);
                copy($img,base_path("storage/app/public/images/user/".$filename));
                $cloudId->image = str_replace(".png", "", $filename);
                $cloudId->created_at = $faker->dateTimeBetween("-1 year","now");
                $cloudId->save();
            }
        }

        \Illuminate\Support\Facades\Artisan::call("cloud:idchecker");
    }

    private function random_pic($dir = 'uploads')
    {
        // print_r($dir);
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
    }
}
