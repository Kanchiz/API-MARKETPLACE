<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrónica', 'description' => 'Dispositivos, gadgets y accesorios tecnológicos'],
            ['name' => 'Libros y Material de Estudio', 'description' => 'Libros de texto, cuadernos y guías académicas'],
            ['name' => 'Ropa y Accesorios', 'description' => 'Prendas, calzado y accesorios personales'],
            ['name' => 'Hogar y Muebles', 'description' => 'Artículos para el hogar, escritorios y lámparas'],
            ['name' => 'Deportes', 'description' => 'Equipamiento y ropa deportiva'],
            ['name' => 'Otros', 'description' => 'Artículos varios del marketplace'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
