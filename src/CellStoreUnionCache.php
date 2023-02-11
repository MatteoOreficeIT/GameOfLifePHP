<?php

    namespace MatteoOreficeIt\GameOfLife;

    /**
     * Una cache per trovare istanze di celle condivise in vari stores
     */
    class CellStoreUnionCache implements ReadOnlyCellStore
    {

        /**
         * @var array|ReadOnlyCellStore[]
         */
        protected $cellStores = [];

        /**
         * Recupera la prima Cell presente in uno degli stores ...
         *
         * @param Coordinates $coordinates
         * @param $default
         * @return Cell|null
         */
        public function getByCoordinates(Coordinates $coordinates , $default = null): ?Cell
        {
            /**
             * @var ?Cell $existing
             */
            $existing = null;
            collect($this->cellStores)->each(function(ReadOnlyCellStore $cellStore)use($coordinates,$default,&$existing){
                if($cell=$cellStore->getByCoordinates($coordinates,$default)) {
                    $existing = $cell;
                    return false;
                }
            });
            return $existing;
        }

        public function push(ReadOnlyCellStore $store) : self
        {
            $this->cellStores []= $store;
            return $this;
        }

    }
