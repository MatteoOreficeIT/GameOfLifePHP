<?php

    namespace MatteoOreficeIt\GameOfLife;

    class Universe
    {

        /**
         * Contiene le celle vive ad ogni iterazione e soltanto quelle
         *
         * @var CellsStore
         */
        protected $aliveSpace;

        /**
         * Conta per ogni cella che ha dei vicini quanti essi siano
         *
         * @var NeightboursCounterStore
         */
        protected $neightboursCountSpace;


        /**
         * @var int
         */
        protected $width;

        /**
         * @var int
         */
        protected $heigth;


        /**
         * Dimensioni max universo
         *
         * @param int $width
         * @param int $heigth
         */
        protected function __construct(int $width,int $heigth)
        {
            $this->width = $width;
            $this->heigth = $heigth;
            $storeCache = new CellStoreUnionCache();
            $this->neightboursCountSpace = new NeightboursCounterStore($this->width,$this->heigth,$storeCache);
            $this->aliveSpace = new CellsStore($this->width,$this->heigth,$storeCache);
            // collega i due store alla cache
            $storeCache->push($this->neightboursCountSpace)->push($this->aliveSpace);
        }

        public static function creation($dimensions,...$lifeSeedsCoordinates) : Universe
        {
            $universe = new self(...$dimensions);
            collect($lifeSeedsCoordinates)->each(function(Coordinates $coordinate)use($universe){
                $universe->giveLife(new Cell($coordinate));
            });
            return $universe;
        }


        /**
         * Per dare vita ad una cella bisogna :
         *
         * - inserirla nel campo delle vite
         * - incrementare il contatore dei suoi adiacenti
         *
         * @param Cell $luckyCell
         * @return void
         */
        public function giveLife(Cell $luckyCell)
        {
            $this->aliveSpace->set($luckyCell);
            $this->neightboursCountSpace->increment($luckyCell);
        }

        /**
         * Per togliere vita ad una cella bisogna :
         *
         * - rimuoverla dal campo delle vite
         * - decrementare il contatore dei suoi adiacenti
         *
         * @param Cell $lucklessCell
         * @return void
         */
        public function takeLife(Cell $lucklessCell)
        {
            $this->aliveSpace->unset($lucklessCell);
            $this->neightboursCountSpace->decrement($lucklessCell);
        }


        public function tick()
        {
            /**
             * Prossime celle che devono morire
             */
            $haveToDie = collect();
            /**
             * Prossime celle che devono vivere
             */
            $haveToLive = collect();

            /**
             * CONDIZIONI per MORIRE:
             * - Qualsiasi cella viva con meno di due celle vive adiacenti muore, come per effetto d'isolamento
             * - Qualsiasi cella viva con più di tre celle vive adiacenti muore, come per effetto di sovrappopolazione
             *
             * CONDIZIONI per VIVERE:
             * - Qualsiasi cella morta con esattamente tre celle vive adiacenti diventa una cella viva, come per effetto di riproduzione
             *
             * CONDIZIONI per RIMANERE:
             * - Qualsiasi cella viva con due o tre celle vive adiacenti sopravvive alla generazione successiva
             */

            // per ogni cella viva
            collect($this->aliveSpace)->each(function(Cell $aliveCell)use($haveToDie,$haveToLive){
                // se meno di 2 o piu di 3 adiacenti vive muore
                if($this->neightboursCountSpace->count($aliveCell) < 2 || $this->neightboursCountSpace->count($aliveCell) > 3) {
                    $haveToDie->push($aliveCell);
                }
            });

            // per ogni cellache ha due o tre celle vive adiacenti ...
            $this->neightboursCountSpace->getCountBetweenList(3,3)->each(function(Cell $cell)use ($haveToLive){
                // ... e se la cella non è nel campo dei vivi ... la porto in vita !!!
                if(!$this->aliveSpace->contains($cell)) {
                    $haveToLive->push($cell);
                }
            });

            $haveToDie->each([$this,'takeLife']);
            $haveToLive->each([$this,'giveLife']);
        }

        /**
         * @return CellsStore
         */
        public function getAliveSpace(): CellsStore
        {
            return $this->aliveSpace;
        }

        /**
         * @return NeightboursCounterStore
         */
        public function getNeightboursCountSpace(): NeightboursCounterStore
        {
            return $this->neightboursCountSpace;
        }

        /**
         * @return int
         */
        public function getWidth(): int
        {
            return $this->width;
        }

        /**
         * @return int
         */
        public function getHeigth(): int
        {
            return $this->heigth;
        }



    }
