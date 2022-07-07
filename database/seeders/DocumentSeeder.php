<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Document::truncate();
        // $csvCorpus = fopen(base_path("database/data/corpus2.csv"), "r");  
        // $firstline = true;
        // while (($data = fgetcsv($csvCorpus, 2000, ",")) !== FALSE) {
        //     if (!$firstline) {
        //         Document::create([
        //             "id" => $data['0'],
        //             "ori_doc" => $data['1'],
        //         ]);    
        //     }
        //     $firstline = false;
        // }   
        // fclose($csvCorpus);

        // Document::create([
        //     'id' => 1,
        //     'ori_doc' => 'test',
        //     'pp_doc' => 'test',
        // ]);
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
