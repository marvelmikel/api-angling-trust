<?php

use Illuminate\Database\Seeder;

class OauthPersonalAccessClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_personal_access_clients')->delete();
        
        \DB::table('oauth_personal_access_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'client_id' => 2,
                'created_at' => '2019-12-13 13:07:20',
                'updated_at' => '2019-12-13 13:07:20',
            ),
        ));
        
        
    }
}