<?php

use Illuminate\Database\Seeder;
use App\Emirate;

class EmiratesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Emirate::create(['name'=>'Abu Dhabi']);
         Emirate::create(['name'=>'Ajman']);
         Emirate::create(['name'=>'Sharjah']);
         Emirate::create(['name'=>'Dubai']);
         Emirate::create(['name'=>'Fujairah']);
         Emirate::create(['name'=>'Ras Al Khaimah']);
         Emirate::create(['name'=>'Umm Al Quwain']);
    }
}
