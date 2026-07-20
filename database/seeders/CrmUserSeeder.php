<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Center;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrmUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@morenova.com',
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
                'center' => null,
                'phone' => null,
                'location' => null,
                'job_title' => null,
                'photo_url' => null,
            ],
            [
                'email' => 'maurommeo@gmail.com',
                'name' => 'Dr Mauro Meo',
                'password' => 'ccu123',
                'is_admin' => false,
                'center' => 'CCU',
                'phone' => null,
                'location' => null,
                'job_title' => null,
                'photo_url' => 'https://media.licdn.com/dms/image/v2/D4D03AQGTcrlf3rAcQQ/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1725713169623?e=2147483647&v=beta&t=oM6zFsXl7iwFBgLPpOCK6TMLhnhIsJIBIC4plVvDl5c',
            ],
            [
                'email' => 'fuhr.estefania@gmail.com',
                'name' => 'Dra Estefania Fuhr',
                'password' => 'password',
                'is_admin' => false,
                'center' => 'CCU',
                'phone' => '+29 1414 9626',
                'location' => 'Bahia Blanca',
                'job_title' => 'Ginecologa',
                'photo_url' => 'https://media-eze1-1.cdn.whatsapp.net/v/t61.24694-24/637166605_1712396056545527_2272432289930000185_n.jpg?ccb=11-4&oh=01_Q5Aa5AEdg-DUpWAXaHjn2ukTVIxHAOLtkq9-ZlJRusHjv5-N2Q&oe=6A663850&_nc_sid=5e03e0&_nc_cat=102',
            ],
            [
                'email' => 'felixramirezibarra@yahoo.es',
                'name' => 'Dr Felix Ramirez',
                'password' => 'password',
                'is_admin' => false,
                'center' => 'CUIN',
                'phone' => '+54 9299 5155 690',
                'location' => 'Neuquén',
                'job_title' => 'Urologo',
                'photo_url' => 'https://static.wixstatic.com/media/f000ed_0b14c636df234e0db238d9b598513d19~mv2.jpg/v1/fit/w_265,h_815,q_90,enc_avif,quality_auto/f000ed_0b14c636df234e0db238d9b598513d19~mv2.jpg',
            ],
            [
                'email' => 'alejandraballerio@yahoo.com.ar',
                'name' => 'Dra Alejandra Balleiro',
                'password' => 'password',
                'is_admin' => false,
                'center' => 'CUIN',
                'phone' => '+54 9299 4125 634',
                'location' => 'Neuquen',
                'job_title' => 'Doctora',
                'photo_url' => 'https://www.solidodigital.com.ar/clientes/cuin/images/int-ballerio.jpg',
            ],
            [
                'email' => 'yani@urolit.com.ar',
                'name' => 'Yanina',
                'password' => 'password',
                'is_admin' => false,
                'center' => 'Urolit - OULTON',
                'phone' => '+54 9351 3377 400',
                'location' => 'Cordoba',
                'job_title' => 'Tecnica',
                'photo_url' => 'https://media-eze1-1.cdn.whatsapp.net/v/t61.24694-24/504889934_1609288723822918_2620945140554809786_n.jpg?ccb=11-4&oh=01_Q5Aa5AFpJGo8VrAB6LWzEE-AAPJEMX_q0VwpmJ0q_jmnLQQQiw&oe=6A65E6AC&_nc_sid=5e03e0&_nc_cat=107',
            ],
        ];

        foreach ($users as $data) {
            $plainPassword = $data['password'];
            unset($data['password']);

            $centerName = $data['center'];
            unset($data['center']);

            $data['center_id'] = $centerName
                ? Center::where('name', $centerName)->value('id')
                : null;

            $data['password'] = Hash::make($plainPassword);
            $data['is_active'] = true;
            $data['email_verified_at'] = now();

            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
