<?php

use Illuminate\Database\Seeder;
use App\InspectorSource;

class InspectorSourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InspectorSource::create(['title'=>'Wecashanycar (Internal)', 'status' => 1]);
        InspectorSource::create(['title'=>'Corporate', 'status' => 1]);
    }
}
