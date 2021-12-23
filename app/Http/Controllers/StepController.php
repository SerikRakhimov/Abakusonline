<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Base;
use App\Models\Item;
use App\Models\Link;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    static function steps_javascript_code(Link $link)
    {
        $value = 0;
        $result = "";
        if ($link->parent_is_numcalc == true && $link->parent_is_nc_screencalc == true) {
            $steps = Step::where('link_id', $link->id)->orderBy('row')->get();
            if ($steps != null) {
                foreach ($steps as $step) {
                    switch ($step->command) {
                        // x = число-константа
                        case "N":
                            $result = $result . "\ny = x;\n x =" . $step->first . ";";
                            break;
                        // x - значение параметра
                        case "Z":
//                            $result = $result . "\n alert(Number(nc_parameter_4_315.innerHTML));x = Number(nc_parameter_4_" . $step->first
//                            . "." . $step->second == "V" ? "innerHTML" : "value" . ");";
                            $result = $result . "\ny = x; \n x = Number(nc_parameter_4_" . $step->first
                                . "." . ($step->second == "V" ? "innerHTML" : "value") . ");";
                            break;
                        case "M":
                            // Математические операции над x и y
                            switch ($step->first) {
                                case "+":
                                    $result = $result . "\n x = x + y; y = 0;";
                                    break;
                                case "-":
                                    $result = $result . "\n x = y - x; y = 0;";
                                    break;
                                case "*":
                                    $result = $result . "\n x = x * y; y = 0;";
                                    break;
                                case "/":
                                    $result = $result . "\n if (x == 0) {
                                        x = 0;  y = 0; error_message = error_div0;
                                    }else
                                    {x = y / x; y = 0;}";
                                    break;

                            }
                            break;
                        // Округление числа
                        case "R":
                            // Округление числа по правилам математики
                            $round_type = "0";
                            // Округление числа в меньшую сторону
                            if ($step->second == "-1") {
                                $round_type = "-1";
                                // Округление числа в большую сторону
                            } elseif ($step->second == "1") {
                                $round_type = "1";
                            }
                            $result = $result . "\n x = round(x, " . $step->first . ", " . $round_type . ");";
                            break;
                        // Сдвиг по стеку
                        case "U":
                            $result = $result . "\nz = y;\ny = x;\nx = z;\nz = 0;";
                            break;
                    }
                }
            } else {
                $result = $result + 'error_message = error_nodata;';
            }
        }
        return $result;
    }

    function run_steps(Link $link)
    {
        $value = 0;
        $error_message = "";
        $steps = Step::where('link_id', $link->id)->orderBy('row')->get();
        if ($steps != null) {
            $value = 0;
        } else {
            $error_message = "steps is null";
        }
        return ['value' => $value, 'error_message' => $error_message];
    }
}
