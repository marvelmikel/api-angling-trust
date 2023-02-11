<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'email' => 'webadmin@barques.co.uk',
                'password' => '$2y$10$ifxYWeahtqg0T2YE.dvL3uvVPbZjIxFZiNj13o2p7QO2HMrisqEUO',
                'permissions' => NULL,
                'last_login' => NULL,
                'first_name' => NULL,
                'last_name' => NULL,
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'created_at' => '2019-12-09 10:10:13',
                'updated_at' => '2019-12-09 10:10:13',
            ),
            1 => 
            array (
                'id' => 2,
                'email' => 'elliot@barques.co.uk',
                'password' => '$2y$10$ifxYWeahtqg0T2YE.dvL3uvVPbZjIxFZiNj13o2p7QO2HMrisqEUO',
                'permissions' => NULL,
                'last_login' => '2020-01-02 09:27:33',
                'first_name' => 'Elliot',
                'last_name' => 'Pettingale',
                'stripe_id' => 'cus_GN0czn7YyzGQKl',
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'created_at' => '2019-12-09 10:10:13',
                'updated_at' => '2020-01-02 09:27:33',
            ),
        ));
        
        
    }
}