<?php

use Illuminate\Database\Seeder;

class PreferencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('preferences')->delete();
        
        \DB::table('preferences')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Game',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Coarse',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Sea',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            3 => 
            array (
                'id' => 4,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Kayak',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            4 => 
            array (
                'id' => 5,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Recreation',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            5 => 
            array (
                'id' => 6,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Match',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            6 => 
            array (
                'id' => 7,
                'type' => 'Modules\\Members\\Entities\\Discipline',
                'name' => 'Specimen',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            7 => 
            array (
                'id' => 8,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Cornwall',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            8 => 
            array (
                'id' => 9,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'East Anglia',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            9 => 
            array (
                'id' => 10,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Essex',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            10 => 
            array (
                'id' => 11,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Isle of Wight',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            11 => 
            array (
                'id' => 12,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Midlands',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            12 => 
            array (
                'id' => 13,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'North East',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            13 => 
            array (
                'id' => 14,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'North West',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            14 => 
            array (
                'id' => 15,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Northumberland',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            15 => 
            array (
                'id' => 16,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Severn',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            16 => 
            array (
                'id' => 17,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Southern',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            17 => 
            array (
                'id' => 18,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'South East',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            18 => 
            array (
                'id' => 19,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Sussex',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            19 => 
            array (
                'id' => 20,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Wessex',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            20 => 
            array (
                'id' => 21,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Wyvern',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            21 => 
            array (
                'id' => 22,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Yorkshire',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            22 => 
            array (
                'id' => 23,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Wales',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            23 => 
            array (
                'id' => 24,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Scotland',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            24 => 
            array (
                'id' => 25,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Northern Ireland',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            25 => 
            array (
                'id' => 26,
                'type' => 'Modules\\Members\\Entities\\Region',
                'name' => 'Republic of Ireland',
                'created_at' => '2020-01-16 09:46:32',
                'updated_at' => '2020-01-16 09:46:32',
            ),
            26 => 
            array (
                'id' => 27,
                'type' => 'Modules\\Members\\Entities\\Division',
                'name' => 'Division 1',
                'created_at' => '2020-01-16 09:51:59',
                'updated_at' => '2020-01-16 09:51:59',
            ),
            27 => 
            array (
                'id' => 28,
                'type' => 'Modules\\Members\\Entities\\Division',
                'name' => 'Division 2',
                'created_at' => '2020-01-16 09:51:59',
                'updated_at' => '2020-01-16 09:51:59',
            ),
        ));
        
        
    }
}