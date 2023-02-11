<?php

use Illuminate\Database\Seeder;

class OauthClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_clients')->delete();
        
        \DB::table('oauth_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'name' => 'Wordpress',
                'secret' => 'MVz0inos7bFVJjB7flAA0VLVQ1LdxzHyPiR0bdXp',
                'redirect' => '',
                'personal_access_client' => 0,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2019-12-04 15:54:22',
                'updated_at' => '2019-12-04 15:54:22',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => NULL,
                'name' => 'Member Login',
                'secret' => 'PVA9g5E1aEr1gbtdmntbphhOtQTIT67Xzxe7RcKs',
                'redirect' => '',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2019-12-13 13:07:20',
                'updated_at' => '2019-12-13 13:07:20',
            ),
        ));
        
        
    }
}