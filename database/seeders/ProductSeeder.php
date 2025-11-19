<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 16GB RAM, 512GB SSD, Intel i7 processor. Perfect for work and gaming.',
                'price' => 1299.99,
                'stock' => 25,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with 2.4GHz connectivity. Battery life up to 12 months.',
                'price' => 29.99,
                'stock' => 150,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical keyboard with Cherry MX switches. Full-size layout with number pad.',
                'price' => 89.99,
                'stock' => 45,
            ],
            [
                'name' => '4K Monitor 27"',
                'description' => 'Ultra HD 4K monitor with IPS panel. 60Hz refresh rate, HDMI and DisplayPort inputs.',
                'price' => 399.99,
                'stock' => 30,
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with HDMI, USB 3.0 ports, SD card reader, and power delivery.',
                'price' => 49.99,
                'stock' => 80,
            ],
            [
                'name' => 'Webcam HD 1080p',
                'description' => 'Full HD webcam with auto-focus and built-in microphone. Perfect for video calls and streaming.',
                'price' => 79.99,
                'stock' => 60,
            ],
            [
                'name' => 'Noise Cancelling Headphones',
                'description' => 'Premium wireless headphones with active noise cancellation. 30-hour battery life.',
                'price' => 249.99,
                'stock' => 40,
            ],
            [
                'name' => 'External SSD 1TB',
                'description' => 'Portable SSD with USB-C interface. Read speeds up to 1050MB/s, write speeds up to 1000MB/s.',
                'price' => 119.99,
                'stock' => 55,
            ],
            [
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand. Ergonomic design for better posture and cooling.',
                'price' => 39.99,
                'stock' => 100,
            ],
            [
                'name' => 'USB-C Cable',
                'description' => 'Premium USB-C to USB-C cable, 6ft length. Supports fast charging and data transfer.',
                'price' => 19.99,
                'stock' => 200,
            ],
            [
                'name' => 'Gaming Mouse Pad',
                'description' => 'Large gaming mouse pad with RGB lighting. Smooth surface for precise mouse movements.',
                'price' => 34.99,
                'stock' => 75,
            ],
            [
                'name' => 'Tablet 10"',
                'description' => '10-inch Android tablet with 64GB storage. Perfect for reading, browsing, and media consumption.',
                'price' => 199.99,
                'stock' => 0, // Out of stock
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Fitness smartwatch with heart rate monitor, GPS, and 7-day battery life. Water resistant.',
                'price' => 179.99,
                'stock' => 35,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Portable Bluetooth speaker with 360-degree sound. 12-hour battery, waterproof design.',
                'price' => 69.99,
                'stock' => 50,
            ],
            [
                'name' => 'Phone Case',
                'description' => 'Protective phone case with shock absorption. Available for iPhone and Android models.',
                'price' => 24.99,
                'stock' => 120,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
