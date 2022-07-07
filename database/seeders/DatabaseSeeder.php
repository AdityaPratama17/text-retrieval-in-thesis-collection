<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Document::truncate();
        $csvCorpus = fopen(base_path("database/data/corpus3.csv"), "r");  
        $firstline = true;
        while (($data = fgetcsv($csvCorpus, 2000, ";")) !== FALSE) {
            if (!$firstline) {
                Document::create([
                    "id" => $data['0'],
                    "doc" => $data['1'],
                ]);    
            }
            $firstline = false;
        }   
        fclose($csvCorpus);
    }
}
