<?php

    error_reporting(E_ALL);
    ini_set('display_errors' , true);
    require __DIR__ . '/vendor/autoload.php';

    use function Termwind\render;
    use function Termwind\terminal;

    render(<<<"HTML"
    <div>
        $th
        <table>
                <tr>
                    <th>X</th>
                    <th>&nbsp;</th>
                    <th>X</th>
                    <th>X</th>
                </tr>
                <tr>
                    <th>X</th>
                    <th>&nbsp;</th>
                    <th>X</th>
                    <th>X</th>
                </tr>
        </table>
    </div>
HTML
    );

    function rstring($len) {
        $res = '';
        for($i=$len;$i>0;$i--) {
            $res .= round(random_int(0,1)) == 1 ? '●' : '&nbsp;';
        }
        return $res.'|';
    }

    for($i=50;$i>0;$i--) {
        for($h=terminal()->height()-1;$h>0;$h--) {
            $string = rstring(terminal()->width()-8);
            render(sprintf("<div>%03s: %s</div>",$h,$string));
        }
        sleep(1);
        terminal()->clear();
    }




