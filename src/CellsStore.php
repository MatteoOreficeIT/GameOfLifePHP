<?php

    namespace MatteoOreficeIt\GameOfLife;

    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Support\Collection;
    use Traversable;

    /**
     * Conserva le celle in maniera unica in funzione delle coordinate, inoltre permette
     * di associare ad un cella dei dati addizionali ( info )
     *
     * Due oggetti Cell sono considerati uguali se hanno le stesse coordinate, per questo :
     *
     * - In caso di get ritorna istanza originariamente salvata
     * - In caso di set non rimpiazza istanza presente ma solo le info
     *
     *
     * La complessita' delle operazioni Set/Get/Contains è O(1)
     */
    class CellsStore implements \IteratorAggregate,ReadOnlyCellStore
    {

        /**
         * @var Collection|Cell[]
         */
        protected $data;

        /**
         * @var int
         */
        protected $width;

        /**
         * @var int
         */
        protected $heigth;

        /**
         * @var ReadOnlyCellStore
         */
        protected $cellsCache;

        /**
         * @param int $width
         * @param int $heigth
         */
        public function __construct(int $width , int $heigth,ReadOnlyCellStore $cellsCache)
        {
            $this->width = $width;
            $this->heigth = $heigth;
            $this->data = new Collection();
            $this->cellsCache = $cellsCache;
        }

        /**
         * Inserisce o sovrascrive una cella nello store in O(1)
         *
         * @param Cell $cell
         * @param $info
         * @return $this
         */
        public function set(Cell $cell , $info = null): CellsStore
        {
            $key = $this->getUniqueKey($cell);
            // aggiorna info
            if($existing=$this->data->get($key)) {
                $existing['info'] = $info;
                $this->data->put($key,$existing);
            } else {
                $this->data->put($key,['cell' => $cell, 'info' => $info]);
            }
            return $this;
        }

        public function unset(Cell $cell) : self
        {
            $this->data->forget($this->getUniqueKey($cell));
            return $this;
        }


        /**
         * @param Cell|Coordinates $cellOrCoordinates
         * @return bool
         */
        public function contains($cellOrCoordinates) : bool
        {
            return $this->data->has($this->getUniqueKey($cellOrCoordinates));
        }

        /**
         * Recupera istanza cella
         *
         * @param Cell $cell
         * @return Cell|null
         */
        public function get(Cell $cell) : ?Cell
        {
            return $this->getByCoordinates($cell->getCoordinates());
        }

        /**
         * Recupera i dati associati ad una cella se presenti altrimenti null
         *
         * @param Cell|Coordinates $cellOrCoordinates
         * @param $default
         * @return mixed|null
         */
        public function getInfo($cellOrCoordinates, $default=null)
        {
            if($existing=$this->data->get($this->getUniqueKey($cellOrCoordinates))) {
                return $existing['info'] ?? $default;
            }
            return $default;
        }



        /**
         * Recupera una cella tramite le coordinate e la ritorna
         *
         * @return ?Cell
         */
        public function getByCoordinates(Coordinates $coordinates,$default=null) : ?Cell
        {

            if( $coordinates->getX()<1 or $coordinates->getY()<1 or
                $coordinates->getX()>$this->width or $coordinates->getY()>$this->heigth) {
                throw new \OutOfRangeException(sprintf(
                    'la coordinata (%d x %d) è oltre le dimensioni dello spazio (%d x %d)',
                    $coordinates->getX(),$coordinates->getY(),$this->width,$this->heigth
                ));
            }

            if($existing=$this->data->get($this->getUniqueKey($coordinates))) {
                return $existing['cell'];
            }
            return $default;
        }

        /**
         * Trova una cella esistente oppure ne alloca una nuova
         *
         * Per la ricerca delle celle esistenti usa una cache condivisa
         *
         * @param Coordinates $coordinates
         * @return Cell
         */
        public function findOrCreateByCoordinates(Coordinates $coordinates) : Cell
        {
            if(!$existingOrNew=$this->cellsCache->getByCoordinates($coordinates)) {
                $this->set($existingOrNew = new Cell($coordinates));
            }
            return $existingOrNew;
        }

        public function getArrayDump() : array
        {
            return collect($this->data)->map(function($item){
                return $item instanceof Arrayable ? $item->toArray() : $item;
            })->toArray();
        }

        /**
         * @param Cell|Coordinates $cellOrCoordinates
         * @return string
         */
        protected function getUniqueKey($cellOrCoordinates) : string
        {
            if($cellOrCoordinates instanceof Cell) {
                $cellOrCoordinates = $cellOrCoordinates->getCoordinates();
            }
            return $this->getCoordinatesUniqueId($cellOrCoordinates);
        }

        protected function getCoordinatesUniqueId(Coordinates $cellOrCoordinates) : string
        {
            return $cellOrCoordinates->getX().':'.$cellOrCoordinates->getY();
        }

        /**
         * Torna un iteratore delle sole celle senza i dati info
         *
         * @return Traversable
         */
        public function getIterator() : Traversable
        {
            return $this->data->map(function($data){
                return $data['cell'];
            })->getIterator();
        }

        protected function filterInMaxSize(Coordinates $coordinates) : bool
        {
            return
                $coordinates->getX() > 0 && $coordinates->getX() <= $this->width &&
                $coordinates->getY() > 0 && $coordinates->getY() <= $this->heigth
                ;
        }

    }
