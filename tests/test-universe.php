<?php


    ini_set('display_errors' , true);
    require __DIR__ . '/vendor/autoload.php';

    use function Termwind\render;
    use function Termwind\terminal;
    use MatteoOreficeIt\GameOfLife\Universe;
    use MatteoOreficeIt\GameOfLife\Coordinates;
    use Illuminate\Support\Collection;
    use MatteoOreficeIt\GameOfLife\Cell;



    function universeGetRow(Universe $u,$y) : Collection {

        $rowCoords = new Coordinates(1,$y);
        $row = collect();
        for($x=1;$x<=$u->getWidth();$x++) {
            $rowCoords->setX($x);
            $cell = $u->getAliveSpace()->getByCoordinates($rowCoords);
            $row->push($cell);
        }
        return $row;
    }

    function universeGetRowN(Universe $u,$y) : Collection {

        $rowCoords = new Coordinates(1,$y);
        $row = collect();
        for($x=1;$x<=$u->getWidth();$x++) {
            $rowCoords->setX($x);
            $row->push($u->getNeightboursCountSpace()->getInfo($rowCoords,0));
        }
        return $row;
    }

    function rowToString(Collection $row) {
        return $row->reduce(function($result,?Cell $aliveOrDead){
            $result .= $aliveOrDead ? '●' : '&nbsp;';
            return $result;
        },'');
    }

    function rowCountToString(Collection $row) {
        return $row->reduce(function($result,int $count){
            $result .= $count;
            return $result;
        },'');
    }

    function dumpUniverse(Universe $u) {

        printf("\nUNIVERSE\n\n");
        for($y=1;$y<=$u->getHeigth();$y++) {
            $rowString = rowToString(universeGetRow($u,$y));
            render(sprintf("<div>%02d: %s |</div>",$y,$rowString));
        }
    }

    function dumpNeightbours(Universe $u) {

        printf("\nNEIGHTBOURS\n\n");
        for($y=1;$y<=$u->getHeigth();$y++) {
            $rowString = rowCountToString(universeGetRowN($u,$y));
            render(sprintf("<div>%02d: %s |</div>",$y,$rowString));
        }
    }

    $u = Universe::creation(
        [10,10],
        Coordinates::new(4,5),
        Coordinates::new(4,4),
        Coordinates::new(5,4)
    );


    dumpUniverse($u);
    dumpNeightbours($u);
    printf("after one tick...\n");
    $u->tick();
    dumpUniverse($u);
    dumpNeightbours($u);





