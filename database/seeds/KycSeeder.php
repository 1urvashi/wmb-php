<?php

use Illuminate\Database\Seeder;
use App\CarMake;
use App\CarCondition;
use App\Market;
use App\Specification;
use App\Permission;

class KycSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Market::create(['title'=>'Local Market']);
        Market::create(['title'=>'Export']);

        CarCondition::create(['title'=>'Perfect']);
        CarCondition::create(['title'=>'Average']);
        CarCondition::create(['title'=>'Poor']);

        Specification::create(['title'=>'GCC specs']);
        Specification::create(['title'=>'American specs']);
        Specification::create(['title'=>'Japanese specs']);
        Specification::create(['title'=>'European specs']);

        CarMake::create(['title'=>'Japanese/Korean']);
        CarMake::create(['title'=>'American']);
        CarMake::create(['title'=>'German']);
        CarMake::create(['title'=>'Other', 'otherStatus'=>1]);

        Permission::create(['name'=>'traders_View-Deleted', 'label'=>'auction_Export-Completed-Auction']);

    }
}
