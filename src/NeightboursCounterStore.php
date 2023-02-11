<?php

    namespace MatteoOreficeIt\GameOfLife;

    use Illuminate\Support\Collection;

    class NeightboursCounterStore extends CellsStore
    {

        /**
         * Incrementa di number il contatore dei vicini di cell
         *
         * Non memorizza la presenza di cell ma aggiorna i contatori dei suoi vicini
         *
         * @param Cell $cell
         * @param int $number
         * @return $this
         */
        public function increment(Cell $cell,int $number=1) : self
        {
            $this->neightbours($cell)->each(function(Cell $neightbour)use($number){
                $counter = $this->getInfo($neightbour,0);
                $this->set($neightbour,$counter+$number);
            });
            return $this;
        }

        /**
         * Decrementa di number il contatore dei vicini di cell, se raggiunge zero dealloca memoria
         *
         * Non memorizza la presenza di cell ma aggiorna i contatori dei suoi vicini
         *
         * @param Cell $cell
         * @param int $number
         * @return $this
         */
        public function decrement(Cell $cell,int $number=1) : self
        {
            $this->neightbours($cell)->each(function(Cell $cell)use($number){
                $counter = $this->getInfo($cell,0) - $number;
                if($counter>0) {
                    $this->set($cell,$counter);
                } else {
                    $this->unset($cell);
                }
            });
            return $this;
        }

        /**
         * Recupera elenco delle celle vicine evitando di allocare nuovi oggetti ma riutilizzando
         * quelli gia presenti
         *
         * @param Cell $cell
         * @return Collection|Cell[]
         */
        public function neightbours(Cell $cell) : Collection
        {
            $cellCoords = $cell->getCoordinates();
            return collect([
                $cellCoords->getRelative(-1,1),
                $cellCoords->getRelative(0,1),
                $cellCoords->getRelative(1,1),
                $cellCoords->getRelative(-1,0),
                $cellCoords->getRelative(1,0),
                $cellCoords->getRelative(-1,-1),
                $cellCoords->getRelative(0,-1),
                $cellCoords->getRelative(1,-1),
            ])
                // vengono rimosse le coordinate out of space
                ->filter(function(Coordinates $coordinates){
                    return $this->filterInMaxSize($coordinates);
                })
                // crea o recupera il rispettivo contatore
                ->map(function(Coordinates $coords){
                    return $this->findOrCreateByCoordinates($coords);
                })
            ;
        }

        /**
         * Conta quanti vicini ha cell
         *
         * @param Cell $cell
         * @return int
         */
        public function count(Cell $cell) : int
        {
            return $this->getInfo($cell,0);
        }

        /**
         * Conta quanti vicini ha cell
         *
         * @param Cell $cell
         * @return bool
         */
        public function hasNeightbours(Cell $cell) : bool
        {
            return $this->getInfo($cell,0) > 0;
        }

        /**
         * Crea un iteratore che filtra solo gli elementi con un count compreso tra min e max
         *
         * @param $min
         * @param $max
         * @return \Iterator
         */
        public function getCountBetweenIterator($min,$max) : \Iterator
        {
            // attenzione ad usare interatore pubblico che ritorna solo cell....
            return new class($this->getIterator(),$this,$min,$max) extends \FilterIterator {
                /**
                 * @var NeightboursCounterStore
                 */
                protected $store;
                protected $min;
                protected $max;
                public function __construct($iterator,NeightboursCounterStore $store,$min,$max)
                {
                    $this->store = $store;
                    $this->min = $min;
                    $this->max = $max;
                    parent::__construct($iterator);
                }
                public function accept()
                {
                    $cell = parent::current();
                    return $this->store->count($cell) >= $this->min &&
                        $this->store->count(parent::current()) <= $this->max;
                }

            };
        }

        /**
         * Ottiene una lista filtrata per count between min / max
         *
         * @param $min
         * @param $max
         * @return Collection
         */
        public function getCountBetweenList($min,$max) : Collection
        {
            return collect($this->getCountBetweenIterator($min,$max));
        }
    }
