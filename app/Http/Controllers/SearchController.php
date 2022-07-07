<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use voku\helper\StopWords;
use App\Models\Index;
use App\Models\Term;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('POST')) {
            $time_start = microtime(true); 

            // Get Docs
            $docs = Document::all();
            // Preprocessing query
            $query = self::preprocessing($request->search);
            // Get Index
            $index = self::tf($query);
            // Get Document frequency
            $df = self::df($query);
            // Inverse document frequency
            $idf = self::idf($df);
            // Weight
            $weight = self::weight($index, $idf);
            // index term
            $indexTerm = self::indexTerm($weight);
            // vektor dokumen
            $vectorDoc = self::vektorDokumen($weight, $indexTerm, $docs);
            // similarity
            $rank = self::similarity($docs, $weight, $vectorDoc);
            // Cosine Similarity
            // $rank = self::cosineSim($docs, $weight);

            // jumlah data yang retrieve
            $jum_relevan = 0;
            foreach ($rank as $key => $value) {
                if($value['cosine'] >= 0.9){
                    $jum_relevan++;
                }
            }

            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);

            return view('search.result', [
                'title' => 'Home',
                'active' => 'home',
                'docs' => $docs,
                'index' => $index,
                'df' => $df,
                'idf' => $idf,
                'weight' => $weight,
                'vectorDoc' => $vectorDoc,
                'rank' => $rank,
                'old' => $request->search,
                'jum_relevan' => $jum_relevan,
                'jum_doc' => count($docs),
                'execution_time' => $execution_time,
            ]);
        }

        return view('search.index', [
            'title' => 'Home',
            "active" => "home",
        ]);
    }

    public function detail(Document $doc)
    {
        return view('search.detail', [               
            "title" => "Document",
            "active" => "home",
            "doc" => $doc,
        ]);
    }

    public static function preprocessing($doc)
    {
        // -- case folding
        $text = preg_replace('/[^a-z0-9]+/i', ' ', strtolower($doc));

        // tokenisasi
        $text = array_filter(explode(" ", $text), 'strlen');

        // -- stopword
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

        // -- stemming
        // https://github.com/sastrawi/sastrawi#cara-install
        // install => composer require sastrawi/sastrawi:^1 (kalau gabisa pake => composer require sastrawi/sastrawi , lalu => composer dump-autoload)
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer = $stemmerFactory->createStemmer();
        $text = $stemmer->stem(implode(' ', $text_new));

        return explode(" ", $text);
    }

    public static function tf($query)
    {
        $index = [];
        foreach ($query as $value) {
            $tempIndex = Index::where('term', $value)->get();
            if (count($tempIndex) == 0) {
                $index[$value]['query'] = 1;
            } else {
                foreach ($tempIndex as $item) {
                    $index[$item->term][$item->id_doc] = $item->tf;
                }
                $index[$value]['query'] = 1;
            }
        }
        return $index;
    }

    public static function df($query)
    {
        $df = [];
        foreach ($query as $term) {
            $df[$term] = Index::where('term', $term)->count();
        }
        return $df;
    }

    public static function idf($df)
    {
        $idf = [];
        $D = Document::get()->count();
        foreach ($df as $key => $value) {
            if ($value == 0) {
                $idf[$key] = 0;
            } else {
                $idf[$key] = log10($D / $value);
            }
        }
        return $idf;
    }

    public static function weight($index, $idf)
    {
        $docs = Document::get();
        $weight = [];
        foreach ($index as $key => $value) {
            foreach ($docs as $doc) {
                if (isset($value[$doc->id])) {
                    $weight[$key][$doc->id] = $value[$doc->id] * $idf[$key];
                } else {
                    $weight[$key][$doc->id] = 0;
                }
            }
            if (isset($value['query'])) {
                $weight[$key]['query'] = $value['query'] * $idf[$key];
            } else {
                $weight[$key]['query'] = 0;
            }
        }
        return $weight;
    }

    public static function indexTerm($weight)
    {
        $docs = Document::get();
        $indexTerm = [];
        foreach ($weight as $key => $value) {
            $penyebut = 0;
            foreach ($docs as $doc) {
                $penyebut += pow($value[$doc->id], 2);
            }
            $penyebut = sqrt($penyebut);

            foreach ($docs as $doc) {
                if ($penyebut == 0) {
                    $indexTerm[$key][$doc->id] = 0;
                } else {
                    $indexTerm[$key][$doc->id] = $value[$doc->id]/$penyebut;
                }
            }
        }

        return $indexTerm;
    }

    public static function vektorDokumen($weight, $indexTerm, $docs)
    {
        $vectorDoc = [];
        foreach ($docs as $doc1) {
            foreach ($weight as $key => $value) {
                foreach ($docs as $doc2) {
                    if(isset($vectorDoc[$doc1->id][$doc2->id])){
                        $vectorDoc[$doc1->id][$doc2->id] += $value[$doc1->id] * $indexTerm[$key][$doc2->id];
                    } else {
                        $vectorDoc[$doc1->id][$doc2->id] = $value[$doc1->id] * $indexTerm[$key][$doc2->id];
                    }
                }
            }

            foreach ($weight as $key => $value) {
                if (isset($vectorDoc['query'][$doc1->id])) {
                    $vectorDoc['query'][$doc1->id] += $value['query'] * $indexTerm[$key][$doc1->id];
                } else {
                    $vectorDoc['query'][$doc1->id] = $value['query'] * $indexTerm[$key][$doc1->id];
                }
            }
        }

        return $vectorDoc;
    }

    public static function similarity($docs, $weight, $vectorDoc)
    {
        $rank = [];
        foreach ($docs as $doc1) {
            $pembilang = 0;
            $penyebut_D = 0;
            $penyebut_Q = 0;
            foreach ($docs as $doc2) {
                $pembilang += $vectorDoc[$doc1->id][$doc2->id] * $vectorDoc['query'][$doc2->id];
                $penyebut_D += pow($vectorDoc[$doc1->id][$doc2->id], 2);
                $penyebut_Q += pow($vectorDoc['query'][$doc2->id], 2);
            }
            $penyebut = sqrt($penyebut_D) * sqrt($penyebut_Q);
            if ($penyebut == 0) {
                $cosine = 0;
            } else {
                $cosine = $pembilang / (sqrt($penyebut_D) * sqrt($penyebut_Q));
            }
            $rank = self::rangking($doc1, $cosine, $rank);
        }

        return $rank;
    }

    public static function cosineSim($docs, $weight)
    {
        $rank = [];
        foreach ($docs as $doc) {
            $pembilang = 0;
            $penyebut_D = 0;
            $penyebut_Q = 0;
            foreach ($weight as $key => $value) {
                $pembilang += $value[$doc->id] * $value['query'];
                $penyebut_D += pow($value[$doc->id], 2);
                $penyebut_Q += pow($value['query'], 2);
            }
            $penyebut = sqrt($penyebut_D) * sqrt($penyebut_Q);
            if ($penyebut == 0) {
                $cosine = 0;
            } else {
                $cosine = $pembilang / (sqrt($penyebut_D) * sqrt($penyebut_Q));
            }
            $rank = self::rangking($doc, $cosine, $rank);
        }
        return $rank;
    }

    public static function rangking($doc, $cosine, $rank)
    {
        $new_doc = [
            'id' => $doc->id,
            'judul' => $doc->judul,
            'doc' => Str::words($doc->doc, $limit = 50, $end = '...'),
            'cosine' => $cosine,
        ];

        if (count($rank) == 0) {
            array_push($rank, $new_doc);
        } else {
            $flag = false;
            foreach ($rank as $key => $item) {
                if ($new_doc['cosine'] > $item['cosine']) {
                    array_splice($rank, $key, 0, array($new_doc)); 
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                array_push($rank, $new_doc);
            }
        }

        return $rank;
    }
}
