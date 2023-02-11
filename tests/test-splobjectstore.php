<?php

    error_reporting(E_ALL);
    ini_set('display_errors',true);
    require __DIR__ . '/vendor/autoload.php';

    use Illuminate\Contracts\Support\Arrayable;
    use MatteoOreficeIt\GameOfLife\Cell;

    function dump(...$vars)
    {
        foreach ($vars as $v) {
            \Symfony\Component\VarDumper\VarDumper::dump($v);
        }
    }

    $store = new class extends \SplObjectStorage {
        /**
         * genera una stringa unica per ogni cella
         */
        public function getHash( $object) : string
        {
            return $object->getCoordinates()->getX().':'.$object->getCoordinates()->getY();
        }

        public function toArray() : array
        {
            return collect($this)->map(function($item){
                return [
                    'item' => $item instanceof Arrayable ? $item->toArray() : $item,
                    'info' => $this->offsetGet($item)
                ];
            })->toArray();
        }
    };

    $cells = [['c'=>[3,5],'i'=>10],['c'=>[2,6],'i'=>20]];
    $firsts =[];


    echo "Primo inserimento\n";
    foreach ($cells as $cell) {
        $newItem = new Cell($cell['c'][0],$cell['c'][1]);
        $store->attach($newItem,$cell['i']);
    }
    dump($store->toArray());

    echo "Secondo inserimento\n";
    foreach ($cells as $cell) {
        $newItem = new Cell($cell['c'][0],$cell['c'][1]);
        // inserisco usando una nuova istanza Cell, con stesse coordinate ma diverse info
        $store->attach($newItem,$cell['i']+1);
    }

    dump($store->toArray());

