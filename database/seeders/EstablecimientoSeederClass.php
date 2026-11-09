<?php

namespace Database\Seeders;

use App\Models\Comuna;
use App\Models\Establecimiento;
use Illuminate\Database\Seeder;

class EstablecimientoSeederClass extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $establecimientos = [
            ['nombre' => 'Hospital Dr. Cesar Gavagno Burotto (Talca)', 'codigo' => '0701', 'direccion' => '1 Norte', 'comuna' => '07101'],
            ['nombre' => 'Hospital San Juan de Dios (Curicó)', 'codigo' => '07301', 'direccion' => 'Archipielago Juan Fernandez 1890', 'comuna' => '07301'],
            ['nombre' => 'Hospital Chileno Japones de Hualañe', 'codigo' => '07302', 'direccion' => 'Av Libertad 402', 'comuna' => '07302'],
            ['nombre' => 'Direccion Servicio de Salud Maule', 'codigo' => '01000', 'direccion' => 'Av. España 2000', 'comuna' => '07101'],
            // Agrega más establecimientos según sea necesario
        ];

        foreach ($establecimientos as $establecimiento) {
            $comunaId = Comuna::query()
                ->where('codigo', $establecimiento['comuna'])
                ->value('id');

            Establecimiento::updateOrCreate(
                ['codigo' => $establecimiento['codigo']],
                [
                    'nombre' => $establecimiento['nombre'],
                    'direccion' => $establecimiento['direccion'],
                    'comuna_id' => $comunaId,
                ],
            );
        }
    }
}
