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
    static function steps_javascript_code(Link $link, $block)
    {
        $result = "";
        if ($link->parent_is_numcalc == true && $link->parent_is_nc_screencalc == true) {
            $steps = Step::where('link_id', $link->id)
                ->where('block', $block)
                ->orderBy('row')
                ->get();
            if ($steps != null) {
                foreach ($steps as $step) {
                    switch ($step->command) {
                        // Вызов функции button_nc_click_5_XXX();
                        // Отключено
                        case "L":
                            $ln_first = Link::find($step->first);
                            if ($ln_first) {
                                if ($ln_first->parent_is_nc_viewonly == true) {
                                    // Префикс "5_" д.б. одинаков в StepController::steps_javascript_code() и в item\ext_edit.php
                                    //$result = $result . "\nbutton_nc_click_5_" . $step->first . '();';
                                    $result = $result . "\nbutton_nc_click_5_" . $step->first . "();";
                                }
                            }
                            break;
                        // x = число-константа
                        case "N":
                            $result = $result . "\ny = x;\n x =" . $step->first . ";";
                            break;
                        // x - значение параметра
                        case "Z":
                            // Не удалять комментарий (для информации):
                            // См. условие '@if($link->parent_is_parent_related == true & $link->parent_base->type_is_list())'
                            // в ext_edit.php и StepController::steps_javascript_code()
                            // "innerHTML" - id параметра
                            // "value" - введенное числовое значение (число, сумма)
                            $result = $result . "\ny = x;";
                            if ($link->parent_base->type_is_number()) {
//                            $result = $result . "\n alert(Number(nc_parameter_4_315.innerHTML));x = Number(nc_parameter_4_" . $step->first
//                            . "." . $step->second == "V" ? "innerHTML" : "value" . ");";
                                $result = $result . "\n x = Number(nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ");";
                            } elseif ($link->parent_base->type_is_string()) {
                                $result = $result . "\n x = nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ";";
                            } elseif ($link->parent_base->type_is_boolean()) {
                                $result = $result . "\n if(nc_parameter_4_" . $step->first . ".checked) {x = 1;}
                                else {x = 0;}";
//                                $result = $result . "\n x = 0;";
                            } elseif ($link->parent_base->type_is_list()) {
                                $result = $result . "\n x = Number(nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ");";
                            }
                            break;
                        case "M":
                            // Математические операции над x и y
                            switch ($step->first) {
                                case "+":
                                    $result = $result . "\n x = y + x; y = 0;";
                                    break;
                                case "-":
                                    $result = $result . "\n x = y - x; y = 0;";
                                    break;
                                case "*":
                                    $result = $result . "\n x = y * x; y = 0;";
                                    break;
                                case "/":
                                    $result = $result . "\n if (x == 0) {
                                        x = 0;  y = 0; error_message = error_div0;
                                    }else
                                    {x = y / x; y = 0;}";
                                    break;
                            }
                            break;
                        case "S":
                            // Строковые операции над x и y
                            switch ($step->first) {
                                case ".":
                                    $result = $result . "\n x = y + '" . trim($step->second) . "' + x; y = '';";
                                    break;
                            }
                            break;
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

    static function steps_calc_code(Item $item, Link $link, $block)
    {
        $x = "155";
        $y = "";
        $z = "";
        if ($link->parent_is_numcalc == true && $link->parent_is_nc_screencalc == false) {
            $steps = Step::where('link_id', $link->id)
                ->where('block', $block)
                ->orderBy('row')
                ->get();
            //if ($steps != null) {
            foreach ($steps as $step) {
                switch ($step->command) {
                    // x = число-константа
                    case "N":
                        $y = $x;
                        $x = $step->first;
                        break;
                    // x - значение параметра
                    case "Z":
                        $link_first = Link::find(intval($step->first));
                        $item_link = GlobalController::get_parent_item_from_main($item->id, $link_first->id);
                        $y = $x;
                        if ($link_first) {
                            if ($item_link) {
                                // Для списков результатом является $item_link->id
                                if ($link_first->parent_base->type_is_list()) {
                                    $x = $item_link->id;
                                } else {
                                    $x = $item_link->name();
                                }
                            } else {
                                $x = "";
                            }
                        } else {
                            $x = "";
                        }
                        if (1 == 2) {
                            // Не удалять комментарий (для информации):
                            // См. условие '@if($link->parent_is_parent_related == true & $link->parent_base->type_is_list())'
                            // в ext_edit.php и StepController::steps_javascript_code()
                            // "innerHTML" - id параметра
                            // "value" - введенное числовое значение (число, сумма)
                            $result = $result . "\ny = x;";
                            if ($link->parent_base->type_is_number()) {
//                            $result = $result . "\n alert(Number(nc_parameter_4_315.innerHTML));x = Number(nc_parameter_4_" . $step->first
//                            . "." . $step->second == "V" ? "innerHTML" : "value" . ");";
                                $result = $result . "\n x = Number(nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ");";
                            } elseif ($link->parent_base->type_is_string()) {
                                $result = $result . "\n x = nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ";";
                            } elseif ($link->parent_base->type_is_boolean()) {
                                $result = $result . "\n if(nc_parameter_4_" . $step->first . ".checked) {x = 1;}
                                else {x = 0;}";
//                                $result = $result . "\n x = 0;";
                            } elseif ($link->parent_base->type_is_list()) {
                                $result = $result . "\n x = Number(nc_parameter_4_" . $step->first
                                    . "." . ($step->second == "I" ? "innerHTML" : "value") . ");";
                            }
                        }
                        break;
                    case "M":
                        // Математические операции над x и y
                        switch ($step->first) {
                            case "+":
                                $result = $result . "\n x = y + x; y = 0;";
                                break;
                            case "-":

                                $result = $result . "\n x = y - x; y = 0;";
                                break;
                            case "*":
                                $x = $y * $x;
                                $y = 0;
                                break;
                            case "/":
                                $result = $result . "\n if (x == 0) {
                                        x = 0;  y = 0; error_message = error_div0;
                                    }else
                                    {x = y / x; y = 0;}";
                                break;
                        }
                        break;
                    case "S":
                        // Строковые операции над x и y
                        switch ($step->first) {
                            case ".":
                                $x = $y . trim($step->second) . $x;
                                $y = "";
                                break;
                        }
                        break;
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
                        $z = $y;
                        $y = $x;
                        $x = $z;
                        $z = "";
                        break;
                }
            }
//            } else {
//                $result = $result + 'error_message = error_nodata;';
//            }
        }
        return $x;
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
