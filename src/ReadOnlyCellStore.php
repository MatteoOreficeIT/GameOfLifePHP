<?php

    namespace MatteoOreficeIt\GameOfLife;

    interface ReadOnlyCellStore
    {
        public function getByCoordinates(Coordinates $coordinates,$default=null) : ?Cell;
    }
