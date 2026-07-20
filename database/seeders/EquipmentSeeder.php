<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipment = [
            [
                'serial_number' => '6233',
                'model' => 'Morenova DT v1',
                'center' => 'CCU',
                'user_email' => 'maurommeo@gmail.com',
                'location' => 'Dorrego 30',
                'notes' => 'Transductores: ED y FEM',
                'is_active' => true,
            ],
            [
                'serial_number' => '6258',
                'model' => 'Morenova DT v2 w/WaterTank',
                'center' => 'CUIN',
                'user_email' => 'felixramirezibarra@yahoo.es',
                'location' => 'Roca 135 , Neuquen Piso 1',
                'notes' => null,
                'is_active' => true,
            ],
            [
                'serial_number' => '6246',
                'model' => 'Morenova DT v1',
                'center' => 'Urolit - OULTON',
                'user_email' => 'yani@urolit.com.ar',
                'location' => 'Av. Vélez Sarsfield 562, Córdoba',
                'notes' => 'Transductor ED',
                'is_active' => true,
            ],
        ];

        foreach ($equipment as $data) {
            $centerName = $data['center'];
            $userEmail = $data['user_email'];
            unset($data['center'], $data['user_email']);

            $data['center_id'] = Center::where('name', $centerName)->value('id');
            $data['user_id'] = User::where('email', $userEmail)->value('id');

            Equipment::updateOrCreate(
                ['serial_number' => $data['serial_number']],
                $data
            );
        }
    }
}
