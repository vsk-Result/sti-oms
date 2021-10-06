<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Влад Самсонов',
            'email' => 'result007@yandex.ru',
            'email_verified_at' => now(),
            'password' => Hash::make('!Res268793')
        ]);
    }
}
