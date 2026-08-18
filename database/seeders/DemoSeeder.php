<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\Promo;
use App\Models\TicketEvent;
use App\Models\JastipTrip;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Goguma Sago Dessert (300ml)',
                'description' => 'Dessert sago rasa Goguma (ubi ungu) yang sehat, nikmat, dan menyegarkan. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 30000,
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Mango Sago Dessert (300ml)',
                'description' => 'Dessert sago rasa Mangga segar yang sehat, nikmat, dan menyegarkan. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 30000,
                'is_available' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Avocado Sago Dessert (300ml)',
                'description' => 'Dessert sago rasa Alpukat yang creamy, sehat, nikmat, dan menyegarkan. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 30000,
                'is_available' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Strawberry Sago Dessert (300ml)',
                'description' => 'Dessert sago rasa Strawberry yang asam manis segar. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 30000,
                'is_available' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Spicy Dakbal (Ceker Pedas Tanpa Tulang)',
                'description' => 'Spicy and tasty! Ceker pedas tanpa tulang ala Dapur Mamaya. Order our Spicy Dakbal now!',
                'price' => 25000,
                'is_available' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Asinan Kiamboy (500ml)',
                'description' => 'Asinan Kiamboy segar isi buah-buahan premium. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 45000,
                'is_available' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Asinan Kuah Lemon (500ml)',
                'description' => 'Asinan buah segar dengan kuah lemon asli yang nikmat dan menyegarkan. Freshly made, tanpa cuka, dan tanpa pengawet.',
                'price' => 45000,
                'is_available' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Creamy Mango Sago (400ml)',
                'description' => 'Varian Creamy Mango Sago dengan ukuran lebih besar (400ml). Sehat, nikmat, menyegarkan, tanpa cuka, dan tanpa pengawet.',
                'price' => 30000,
                'is_available' => true,
                'sort_order' => 8,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $productData) {
            $createdProducts[] = Product::create($productData);
        }

        // Create a demo batch
        $batch = Batch::create([
            'title' => 'Batch #1 - Siap: 10 Juni 2026',
            'description' => 'Batch pertama pre-order bulan Juni. Pemesanan dibuka sampai 8 Juni.',
            'open_date' => now()->subDay(),
            'close_date' => now()->addDays(7),
            'ready_date' => now()->addDays(9),
            'delivery_date' => now()->addDays(10),
            'is_active' => true,
        ]);

        // Attach all products to the batch
        $productIds = collect($createdProducts)->pluck('id')->toArray();
        $batch->products()->sync($productIds);

        // Create a demo promo code
        Promo::create([
            'code' => 'MAMAYA10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 15000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        Promo::create([
            'code' => 'HEMAT5K',
            'type' => 'fixed',
            'value' => 5000,
            'min_order' => 50000,
            'is_active' => true,
        ]);

        // Create demo campaigns
        Campaign::create([
            'title' => 'Promo Diskon 10%',
            'content' => '🎉 Gunakan kode MAMAYA10 untuk diskon 10% (maks. Rp15.000) di Batch #1!',
            'bg_color' => '#e97a1e',
            'text_color' => '#ffffff',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Campaign::create([
            'title' => 'Free Shipping Promo',
            'content' => '🚚 Gratis ongkir untuk pemesanan pickup! Ambil langsung di dapur kami.',
            'bg_color' => '#15803c',
            'text_color' => '#ffffff',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Create Demo Ticket Event
        $event = TicketEvent::create([
            'name' => 'K-Pop SuperFest 2026 Jakarta',
            'slug' => 'kpop-superfest-2026',
            'description' => "Konser K-Pop terbesar tahun ini! Menampilkan grup dan artis K-Pop papan atas.\n\nSyarat & Ketentuan:\n1. 1 KTP maksimal 4 tiket.\n2. Tiket tidak dapat dipindahtangankan.\n3. E-Ticket akan ditukar di venue H-1.",
            'venue' => 'Gelora Bung Karno Stadium',
            'war_start_time' => now()->subHours(2), // Active right now
            'war_end_time' => now()->addDays(2),
            'event_date' => now()->addMonths(2),
            'is_active' => true,
        ]);

        $event->categories()->createMany([
            ['name' => 'CAT 1 (Standing)', 'price' => 3500000, 'quota' => 500, 'available_quota' => 500],
            ['name' => 'CAT 2 (Seating)', 'price' => 2800000, 'quota' => 1000, 'available_quota' => 1000],
            ['name' => 'FESTIVAL A', 'price' => 1500000, 'quota' => 2000, 'available_quota' => 2000],
        ]);

        // Create Demo Jastip Trip
        $trip = JastipTrip::create([
            'destination' => 'Jepang (Tokyo & Osaka)',
            'slug' => 'jepang-tokyo-osaka-agustus-2026',
            'departure_date' => now()->addDays(15),
            'return_date' => now()->addDays(22),
            'po_close_date' => now()->addDays(10),
            'baggage_quota_kg' => 30,
            'description' => "Open Jastip Jepang! Bisa titip skincare, snack, merchandise anime, sampai barang elektronik (kecuali handphone). Fee jastip mulai dari Rp50.000 per barang tergantung ukuran dan berat.",
            'is_active' => true,
        ]);

        $trip->catalogs()->createMany([
            ['name' => 'Tokyo Banana Original (8 pcs)', 'estimated_price' => 185000],
            ['name' => 'Hada Labo Gokujyun Premium Lotion', 'estimated_price' => 145000],
            ['name' => 'Royce Chocolate Nama (Mild Cacao)', 'estimated_price' => 170000],
        ]);
    }
}
