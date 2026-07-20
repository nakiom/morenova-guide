<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Center;
use Illuminate\Database\Seeder;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        $centers = [
            [
                'name' => 'CCU',
                'location' => 'Bahia Blanca',
                'address' => 'Dorrego 30',
                'phone' => null,
                'email' => 'alejandrodorazio@gmail.com',
                'contact_name' => 'Alejandro Dorazio',
                'instagram_url' => null,
                'youtube_url' => null,
                'facebook_url' => null,
                'x_url' => null,
                'notes' => 'Morenova 6233',
                'is_active' => true,
            ],
            [
                'name' => 'CUIN',
                'location' => 'Neuquén',
                'address' => 'Roca 135',
                'phone' => '+54 9299 5155 690',
                'email' => 'felixramirezibarra@yahoo.es',
                'contact_name' => 'Dr Felix Ramirez',
                'instagram_url' => 'https://www.instagram.com/cuincentrourologico/',
                'youtube_url' => 'https://www.youtube.com/@infoCuin',
                'facebook_url' => 'https://www.facebook.com/CUIN-Centro-Urol%C3%B3gico-Integral-104019258979624',
                'x_url' => null,
                'notes' => "Morenova DT v2 - con WaterTank\nTEM: ED - FEM",
                'is_active' => true,
            ],
            [
                'name' => 'Urolit - OULTON',
                'location' => 'Cordoba',
                'address' => 'Av Velez Sarsfield 562',
                'phone' => '+54 9351 6799 589',
                'email' => 'mdmaglione@gmail.com',
                'contact_name' => 'Mario Maglione',
                'instagram_url' => 'https://www.instagram.com/urologia.urolit/',
                'youtube_url' => null,
                'facebook_url' => null,
                'x_url' => null,
                'notes' => 'Morevnova estudios clinicos',
                'is_active' => true,
            ],
        ];

        foreach ($centers as $data) {
            Center::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
