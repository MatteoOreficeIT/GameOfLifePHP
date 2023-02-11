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

        render(sprintf("<div>┌%s┐</div>",str_repeat('─',$u->getWidth())));
        for($y=1;$y<=$u->getHeigth();$y++) {
            $rowString = rowToString(universeGetRow($u,$y));
            render(sprintf("<div>│%s│</div>",$rowString));
        }
        render(sprintf("<div>└%s┘</div>",str_repeat('─',$u->getWidth())));
        printf("\nGameOfLife by Matteo Orefice: initial configuration Die Hard!");
    }

    function dumpNeightbours(Universe $u) {

        printf("\nNEIGHTBOURS\n\n");
        for($y=1;$y<=$u->getHeigth();$y++) {
            $rowString = rowCountToString(universeGetRowN($u,$y));
            render(sprintf("<div>|%s|</div>",$y,$rowString));
        }
    }

    $u = Universe::creation(
        [terminal()->width()-10,terminal()->height()-4],
        ...Coordinates::diehard(terminal()->width()/2,terminal()->height()/2)
    );

    dumpUniverse($u);
    $delayUSec = $argv[2] * 1000;

    usleep($delayUSec);
    for($i=1;$i<=$argv[1];$i++) {
        terminal()->clear();
        $u->tick();
        dumpUniverse($u);
        usleep($delayUSec);
    }






