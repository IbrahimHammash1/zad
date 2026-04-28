<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Basket;
use App\Models\Customer;
use App\Models\DeliveryAgent;
use App\Models\Material;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    protected function copyLogo(string $directory): string
    {
        $source = public_path('logo.png');
        $filename = Str::random(20) . '.png';
        $relativePath = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $relativePath);
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($source, $fullPath);

        return $relativePath;
    }

    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@zad.com'],
            [
                'name' => 'ZAD Admin',
                'password' => Hash::make('admin12345'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        $customerOne = $this->createCustomer(
            name: 'Customer One',
            email: 'customer1@zad.com',
            phone: '0999000001',
            locale: 'en',
        );

        $customerTwo = $this->createCustomer(
            name: 'Customer Two',
            email: 'customer2@zad.com',
            phone: '0999000002',
            locale: 'ar',
        );

        $materials = collect([
            ['name' => 'Rice', 'slug' => 'rice', 'unit' => 'kg'],
            ['name' => 'Sugar', 'slug' => 'sugar', 'unit' => 'kg'],
            ['name' => 'Sunflower Oil', 'slug' => 'sunflower-oil', 'unit' => 'liter'],
            ['name' => 'Pasta', 'slug' => 'pasta', 'unit' => 'pack'],
            ['name' => 'Lentils', 'slug' => 'lentils', 'unit' => 'kg'],
        ])->mapWithKeys(fn (array $item): array => [
            $item['slug'] => Material::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'unit' => $item['unit'],
                    'image' => $this->copyLogo('materials'),
                    'is_active' => true,
                ],
            ),
        ]);

        $mainStore = Store::query()->updateOrCreate(
            ['name' => 'Damascus Main Store'],
            [
                'phone' => '0111111111',
                'address' => 'Damascus - Mazzeh',
                'image' => $this->copyLogo('stores'),
                'is_active' => true,
            ],
        );

        $midanStore = Store::query()->updateOrCreate(
            ['name' => 'Midan Store'],
            [
                'phone' => '0112222222',
                'address' => 'Damascus - Midan',
                'image' => $this->copyLogo('stores'),
                'is_active' => true,
            ],
        );

        $familyBasket = Basket::query()->updateOrCreate(
            ['slug' => 'family-basket'],
            [
                'name' => 'Family Basket',
                'description' => 'Balanced monthly essentials for a small family.',
                'fixed_price' => 25.50,
                'image' => $this->copyLogo('baskets'),
                'is_active' => true,
            ],
        );

        $economyBasket = Basket::query()->updateOrCreate(
            ['slug' => 'economy-basket'],
            [
                'name' => 'Economy Basket',
                'description' => 'Budget friendly essentials package.',
                'fixed_price' => 17.00,
                'image' => $this->copyLogo('baskets'),
                'is_active' => true,
            ],
        );

        $familyBasket->stores()->syncWithoutDetaching([$mainStore->id, $midanStore->id]);
        $economyBasket->stores()->syncWithoutDetaching([$mainStore->id]);

        $familyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['rice']->id],
            ['quantity' => 3, 'sort_order' => 1],
        );
        $familyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['sugar']->id],
            ['quantity' => 2, 'sort_order' => 2],
        );
        $familyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['sunflower-oil']->id],
            ['quantity' => 2, 'sort_order' => 3],
        );
        $familyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['pasta']->id],
            ['quantity' => 4, 'sort_order' => 4],
        );

        $economyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['rice']->id],
            ['quantity' => 2, 'sort_order' => 1],
        );
        $economyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['lentils']->id],
            ['quantity' => 2, 'sort_order' => 2],
        );
        $economyBasket->basketItems()->updateOrCreate(
            ['material_id' => $materials['pasta']->id],
            ['quantity' => 2, 'sort_order' => 3],
        );

        $activeAgent = DeliveryAgent::query()->updateOrCreate(
            ['name' => 'Ahmad Delivery'],
            [
                'phone' => '0933111111',
                'notes' => 'Primary Damascus delivery agent.',
                'is_active' => true,
            ],
        );

        DeliveryAgent::query()->updateOrCreate(
            ['name' => 'Backup Agent'],
            [
                'phone' => '0933222222',
                'notes' => 'Standby support agent.',
                'is_active' => false,
            ],
        );

        $this->command?->info('Seeded test data: admin, customers, catalog, stores, and delivery agents.');
        $this->command?->info('Admin login: admin@zad.com / admin12345');
        $this->command?->info('Customer login: customer1@zad.com / customer12345');
        $this->command?->info('Customer login: customer2@zad.com / customer12345');
        $this->command?->info(sprintf('Seed created/updated by: %s', $admin->email));
    }

    protected function createCustomer(string $name, string $email, string $phone, string $locale): Customer
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('customer12345'),
                'role' => UserRole::Customer,
                'is_active' => true,
            ],
        );

        return Customer::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $name,
                'phone' => $phone,
                'country' => 'Syria',
                'preferred_locale' => $locale,
                'is_active' => true,
            ],
        );
    }
}
