<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'carot.jpg' => ['text' => 'Cà Rốt', 'bg' => 'FF8C00', 'emoji' => '🥕'],
            'khoaitay.jpg' => ['text' => 'Khoai Tây', 'bg' => 'D2B48C', 'emoji' => '🥔'],
            'chuoi.jpg' => ['text' => 'Chuối', 'bg' => 'FFD700', 'emoji' => '🍌'],
            'raumuong.jpg' => ['text' => 'Rau Muống', 'bg' => '228B22', 'emoji' => '🥬'],
        ];

        $publicPath = Storage::disk('public')->path('sanpham');

        foreach ($images as $filename => $config) {
            $svg = <<<SVG
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
  <rect width="400" height="300" fill="#{$config['bg']}"/>
  <text x="200" y="130" font-size="80" text-anchor="middle" fill="white">{$config['emoji']}</text>
  <text x="200" y="200" font-size="28" text-anchor="middle" fill="white" font-family="sans-serif" font-weight="bold">{$config['text']}</text>
</svg>
SVG;
            $filePath = $publicPath.'/'.$filename;
            file_put_contents($filePath, $svg);
            $this->command->info("✓ Created: {$filename}");
        }

        $this->command->info('Done! Product images seeded.');
    }
}
