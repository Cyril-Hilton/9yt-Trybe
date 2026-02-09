<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use App\Models\GalleryImage;
use App\Models\MagazineImage;
use App\Models\ShopProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductionContentSeeder extends Seeder
{
    /**
     * Seed minimal production content to prevent Soft 404 errors.
     * 
     * This seeder creates basic content for pages that Google is flagging as Soft 404s.
     * Run this on production ONLY if you want to populate empty pages with minimal content.
     */
    public function run(): void
    {
        $this->command->info('Seeding minimal production content...');

        // Seed Team Members
        $this->seedTeamMembers();

        // Seed Gallery Images
        $this->seedGalleryImages();

        // Seed Shop Products
        $this->seedShopProducts();

        $this->command->info('Production content seeded successfully!');
    }

    private function seedTeamMembers(): void
    {
        $this->command->info('Seeding team members...');

        $teamMembers = [
            [
                'full_name' => '9yt !Trybe Team',
                'title' => 'Community Manager',
                'role' => 'Staff',
                'job_description' => 'Managing the !Trybe community and ensuring amazing event experiences for all members.',
                'email' => 'team@9yttrybe.com',
                'contact_number' => '+233 XX XXX XXXX',
                'status' => 'approved',
            ],
            [
                'full_name' => 'Events Coordinator',
                'title' => 'Event Planning Specialist',
                'role' => 'Staff',
                'job_description' => 'Coordinating and planning exciting events across Ghana to bring the community together.',
                'email' => 'events@9yttrybe.com',
                'contact_number' => '+233 XX XXX XXXX',
                'status' => 'approved',
            ],
        ];

        foreach ($teamMembers as $member) {
            TeamMember::firstOrCreate(
                ['email' => $member['email']],
                $member
            );
        }

        $this->command->info('Team members seeded.');
    }

    private function seedGalleryImages(): void
    {
        $this->command->info('Seeding gallery images...');

        // Note: You'll need to add actual image URLs or use placeholders
        $galleryImages = [
            [
                'title' => '9yt !Trybe Events',
                'image_url' => asset('ui/logo/9yt-trybe-logo-light.png'), // Placeholder
                'category' => 'new',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Community Gathering',
                'image_url' => asset('ui/logo/9yt-trybe-logo-dark.png'), // Placeholder
                'category' => 'new',
                'is_active' => true,
                'order' => 2,
            ],
        ];

        foreach ($galleryImages as $image) {
            GalleryImage::firstOrCreate(
                ['title' => $image['title'], 'category' => $image['category']],
                $image
            );
        }

        // Magazine images
        $magazineImages = [
            [
                'title' => '!Trybe Magazine Cover',
                'image_url' => asset('ui/logo/9yt-trybe-logo-light.png'), // Placeholder
                'is_active' => true,
                'order' => 1,
            ],
        ];

        foreach ($magazineImages as $image) {
            MagazineImage::firstOrCreate(
                ['title' => $image['title']],
                $image
            );
        }

        $this->command->info('Gallery images seeded.');
    }

    private function seedShopProducts(): void
    {
        $this->command->info('Seeding shop products...');

        $products = [
            [
                'name' => '9yt !Trybe T-Shirt',
                'slug' => Str::slug('9yt !Trybe T-Shirt'),
                'description' => 'Official 9yt !Trybe branded t-shirt. Represent the vibe!',
                'price' => 50.00,
                'stock' => 100,
                'status' => 'approved',
                'is_active' => true,
                'image_url' => asset('ui/logo/9yt-trybe-logo-light.png'), // Placeholder
            ],
            [
                'name' => '!Trybe Cap',
                'slug' => Str::slug('!Trybe Cap'),
                'description' => 'Stylish cap with the !Trybe logo. Perfect for any event.',
                'price' => 30.00,
                'stock' => 50,
                'status' => 'approved',
                'is_active' => true,
                'image_url' => asset('ui/logo/9yt-trybe-logo-dark.png'), // Placeholder
            ],
        ];

        foreach ($products as $product) {
            ShopProduct::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }

        $this->command->info('Shop products seeded.');
    }
}
