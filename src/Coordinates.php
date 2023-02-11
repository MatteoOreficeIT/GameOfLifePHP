<?php

    namespace MatteoOreficeIt\GameOfLife;

    class Coordinates
    {

        /**
         * @var int
         */
        protected $x;

        /**
         * @var int
         */
        protected $y;

        /**
         * @param int $x
         * @param int $y
         */
        public function __construct(int $x = 1 , int $y = 1)
        {
            $this->x = $x;
            $this->y = $y;
        }

        public static function new(int $x = 1 , int $y = 1) : self
        {
            return new self($x,$y);
        }

        public static function diehard($offsetX=0,$offsetY=0) : array
        {
            return collect([
                [7,3],
                [1,2],[2,2],
                [2,1],[6,1],[7,1],[8,1]
            ])->map(function(array $coord)use($offsetX,$offsetY){
                return new Coordinates($offsetX+$coord[0],$offsetY+$coord[1]);
            })->toArray();
        }

        /**
         * @return int
         */
        public function getX(): int
        {
            return $this->x;
        }

        /**
         * @param int $x
         * @return Coordinates
         */
        public function setX(int $x): Coordinates
        {
            $this->x = $x;
            return $this;
        }

        /**
         * @return int
         */
        public function getY(): int
        {
            return $this->y;
        }

        /**
         * @param int $y
         * @return Coordinates
         */
        public function setY(int $y): Coordinates
        {
            $this->y = $y;
            return $this;
        }

        public function equalsTo(self $coordinates) : bool
        {
            return $this->x == $coordinates->x && $this->y == $coordinates->y;
        }

        public function getRelative($x=0,$y=0) : Coordinates
        {
            return new Coordinates($this->x+$x,$this->y+$y);
        }

        public function __toString()
        {
            return sprintf("(coords=%s:%s)",$this->x,$this->y);
        }


    }
