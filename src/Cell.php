<?php

    namespace MatteoOreficeIt\GameOfLife;

    use Illuminate\Contracts\Support\Arrayable;

    class Cell implements Arrayable
    {

        /**
         * @var Coordinates
         */
        protected $coordinates;

        /**
         * @var int
         */
        protected static $idSequence = 1;

        /**
         * @var int
         */
        protected $id;

        /**
         * @param Coordinates|null $coordinates
         */
        public function __construct(Coordinates $coordinates=null)
        {
            $this->coordinates = $coordinates ? clone $coordinates : new Coordinates();
            $this->id = self::$idSequence++;
        }


        /**
         * @return Coordinates
         */
        public function getCoordinates(): Coordinates
        {
            return $this->coordinates;
        }

        /**
         * @param Coordinates $coordinates
         * @return Cell
         */
        public function setCoordinates(Coordinates $coordinates): Cell
        {
            $this->coordinates = $coordinates;
            return $this;
        }


        public function toArray() : array
        {
            return [
                'x'=>$this->getCoordinates()->getX(),
                'y'=>$this->getCoordinates()->getY(),
                'id'=>$this->id
            ];
        }

        /**
         * @return int
         */
        public function getId(): int
        {
            return $this->id;
        }

        public function equalsTo(self $cell) : bool
        {
            return $cell->getCoordinates()->equalsTo($cell->getCoordinates());
        }
    }
