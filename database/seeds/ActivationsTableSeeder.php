<?php

use Illuminate\Database\Seeder;

class ActivationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('activations')->delete();
        
        \DB::table('activations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'code' => '29rcmAGrU0caLO0JDpEAx2kX4NLsjWDf',
                'completed' => 1,
                'completed_at' => '2019-12-09 11:45:35',
                'created_at' => '2019-12-09 11:45:00',
                'updated_at' => '2019-12-09 11:45:00',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'code' => 'Sl5zUDktzQpBzt7BZxR8m6gimmehX7FG',
                'completed' => 1,
                'completed_at' => '2019-12-09 11:45:35',
                'created_at' => '2019-12-09 11:45:17',
                'updated_at' => '2019-12-09 11:45:17',
            ),
        ));
        
        
    }
}