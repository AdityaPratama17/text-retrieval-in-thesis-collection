<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Index;
use App\Models\Term;
use Illuminate\Http\Request;
use Mockery\Generator\StringManipulation\Pass\Pass;
use voku\helper\StopWords;
use Illuminate\Support\Str;
use Sastrawi\Stemmer\StemmerFactory;
// require_once __DIR__ . '/vendor/autoload.php';

class PreprocessingController extends Controller
{
    public function index()
    {
        Term::truncate();
        Index::truncate();
        Document::truncate();

        // import data
        $iter_id = 1;
        $file = fopen(base_path("database/data/docs.txt"), "r");
        while(!feof($file)) {
            $str = explode("|",fgets($file));
            Document::create([
                "id" => $iter_id++,
                "judul" => $str[0],
                "doc" => $str[1],
            ]);
        }
        fclose($file);

        $docs = Document::all();
        $docs_after = [];
        $docs_excerpt = [];
        $terms = [];
        $tf = [];
        foreach ($docs as $key => $doc) {
            $termInDoc = [];
            // preprocessing
            $text = self::preprocessing($doc);
            $docs_after[$doc->id] = $text;
            $docs_excerpt[$doc->id] = Str::words($doc->doc, $limit = 20, $end = ' ...');
            foreach (explode(" ", $text) as $key => $term) {
                // find unique term in all Docs
                if (!in_array($term, $terms, true)) {
                    array_push($terms, $term);
                    Term::create(['term' => $term]);
                }
                // find term frequences (TF)
                $tf[$term][$doc->id] = count(array_keys(explode(" ", $text), $term));
                // find unique term in this Doc
                if (!in_array($term, $termInDoc)) {
                    array_push($termInDoc, $term);
                    Index::create([
                        'id_doc' => $doc->id,
                        'term' => $term,
                        'tf' => $tf[$term][$doc->id],
                    ]);
                }
            }
        }

        // document frequency
        $df = self::df($terms);

        return view('documents.index', [
            'title' => 'Documents',
            'active' => 'documents',
            'docs' => $docs,
            'docs_after' => $docs_after,
            'docs_excerpt' => $docs_excerpt,
            'tf' => $tf,
            'df' => $df,
        ]);
    }

    public static function preprocessing($doc)
    {
        // judul + abstrak
        $text = $doc->judul." ".$doc->doc;

        // case folding
        $text = preg_replace('/[^a-z0-9]+/i', ' ', strtolower($text));

        // tokenisasi
        $text = array_filter(explode(" ", $text), 'strlen');

        // stopword
        // https://github.com/voku/stop-words/blob/master/src/voku/helper/StopWords.php
        // install => composer require voku/stop-words
        $stopWords = new StopWords();
        $stopWords = $stopWords->getStopWordsFromLanguage('id');
        $text_new = [];
        foreach ($text as $value) {
            if (!in_array($value, $stopWords)) {
                array_push($text_new, $value);
            }
        }

        // stemming
        // https://github.com/sastrawi/sastrawi#cara-install
        // install => composer require sastrawi/sastrawi:^1 (kalau gabisa pake => composer require sastrawi/sastrawi , lalu => composer dump-autoload)
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer = $stemmerFactory->createStemmer();
        $text = $stemmer->stem(implode(' ', $text_new));

        return $text;
    }

    public static function df($terms)
    {
        $df = [];
        foreach ($terms as $term) {
            $df[$term] = Index::where('term', $term)->count();
        }
        return $df;
    }
}
