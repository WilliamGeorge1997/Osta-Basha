<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Currency;

class CommonDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['title' => 'دولار أمريكي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'يورو', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'جنيه إسترليني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ين ياباني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دولار أسترالي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دولار كندي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'فرنك سويسري', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'يوان صيني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'كرونة سويدية', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دولار نيوزيلندي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'بيزو مكسيكي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دولار سنغافوري', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دولار هونج كونج', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'كرونة نرويجية', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'وون كوري', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ليرة تركية', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'روبل روسي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'روبية هندية', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ريال برازيلي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'راند جنوب أفريقي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ريال سعودي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'درهم إماراتي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'جنيه مصري', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دينار كويتي', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دينار بحريني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ريال قطري', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ريال عماني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دينار أردني', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'ليرة لبنانية', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'دينار عراقي', 'created_at' => now(), 'updated_at' => now()],
        ];

        Currency::insert($currencies);
    }
}
