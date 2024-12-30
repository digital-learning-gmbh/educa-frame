<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class GroupClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $names = ["Meine Lerngruppen","Gymnasium Musterstadt", "Freizeit",""];

    public function run()
    {
        foreach (\App\CloudID::all() as $cloudID)
        {
            for ($i = 0; $i < 3; $i++)
            {
                $cluster = new \App\GroupCluster();
                $cluster->name = $this->names[array_rand($this->names)];
                $cluster->cloudid = $cloudID->id;
                $cluster->readonly = random_int(0,1);
                $cluster->save();

                $gruppen = $cloudID->gruppen()->random(3);

                $cluster->groups()->sync($gruppen);
            }
        }
    }
}
