<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
                'name'                  => 'Admin User',
                'email'                 => 'admin@xyz.com',
                'password'              => '$2y$10$bz0S4wvT8a70ENfivCTeY.tTPQgmPFyrSP/Uhz60Es3LUtZk.pqa.',//'12345678',
                'role_id'               => User::ROLE_ADMIN,
                'email_verified_at'     => Carbon::now()
            ]);
    }
}
