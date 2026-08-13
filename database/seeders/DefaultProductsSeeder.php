<?php

namespace Database\Seeders;

use App\Models\DefaultGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DefaultProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raw = Storage::disk('local')->json('base.json');
        if (!isset($raw)) {
            throw new \Exception("Data file is not present");
        }

        $data = collect($raw);
        foreach($data['groups'] as $group){
            $gr = DefaultGroup::create([
                'name' => $group['gr_name'],
            ]);
            $gr->default_products()->createMany($group['prods']);
        }
        echo "done\n";
    }
}
