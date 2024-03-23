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
    // Экранное вычисление
    // 1) Если результат расчета - строка с несколькими языковыми значениями, то берется значения по текущему языку
    // 2) Нет обработки команды "I"
    // Это 1)-2) есть в неэкранном вычислении steps_calc_code()
    static function steps_javascript_code(Link $link, $block)
    {
        $result = "";
        // "$link->parent_is_nc_screencalc == true" используется
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
                            $result = $result . "\ny = x;\n x = '" . $step->first . "';";
                            break;
                        // x - значение параметра
                        case "Z":
                            $ln_first = Link::find($step->first);
                            if ($ln_first) {
                                // Не удалять комментарий (для информации):
                                // См. условие '@if($link->parent_is_parent_related == true & $link->parent_base->type_is_list())'
                                // в ext_edit.php и StepController::steps_javascript_code()
                                // "innerHTML" - id параметра
                                // "value" - введенное числовое значение (число, сумма)
                                $result = $result . "\ny = x;";
                                if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                    $i = 0;
                                    foreach (config('app.locales') as $lang_key => $lang_value) {
                                        // Начиная со второго(индекс==1) элемента массива языков сохранять
                                        if ($i > 0) {
                                            $result = $result . "\n y_" . $lang_key . " = x_" . $lang_key . ";";
                                        }
                                        $i = $i + 1;
                                    }
                                }

                                if ($ln_first->parent_base->type_is_number()) {
                                    $nc_p = "";
                                    $nc_s = "";
                                    if ($ln_first->parent_is_parent_related == true) {
                                        $nc_s = "innerHTML";
                                        if ($step->second == "I") {
                                            $nc_p = "nc_param_id_4_";
                                        } else {
                                            $nc_p = "nc_parameter_4_";
                                        }
                                    } else {
                                        $nc_p = "nc_parameter_4_";
                                        $nc_s = "value";
                                    }
                                    $result = $result . "\n x = " . $nc_p . $step->first . "." . $nc_s . ";alert(".$ln_first->id. " ". $nc_p . $step->first .")";

                                    if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            // Начиная со второго(индекс==1) элемента массива языков сохранять
                                            if ($i > 0) {
                                                $result = $result . "\n x" . "_" . $lang_key . " = x;";
                                            }
                                            $i = $i + 1;
                                        }
                                    }

                                } elseif ($ln_first->parent_base->type_is_string()) {
                                    $nc_p = "";
                                    $nc_s = "";
                                    if ($ln_first->parent_is_parent_related == true) {
                                        $nc_s = "innerHTML";
                                        if ($step->second == "I") {
                                            $nc_p = "nc_param_id_4_";
                                        } else {
                                            $nc_p = "nc_parameter_4_";
                                        }
                                    } else {
                                        $nc_p = "nc_parameter_4_";
                                        $nc_s = "value";
                                    }
                                    $result = $result . "\n x = " . $nc_p . $step->first . "." . $nc_s . ";";
                                    if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            // Начиная со второго(индекс==1) элемента массива языков сохранять
                                            if ($i > 0) {
                                                if ($ln_first->parent_base->is_one_value_lst_str_txt == false) {
                                                    $result = $result . "\n x" . "_" . $lang_key . " = " . $nc_p . $step->first
                                                        . "_" . $lang_key . "." . $nc_s . ";";
                                                } else {
                                                    $result = $result . "\n x" . "_" . $lang_key . " = x;";
                                                }
                                            }
                                            $i = $i + 1;
                                        }
                                    }

                                } elseif ($ln_first->parent_base->type_is_boolean()) {
                                    if ($ln_first->parent_is_parent_related == true) {
                                        if ($step->second == "I") {
                                            $result = $result . "\n x = nc_param_id_4_" . $step->first
                                                . ".innerHTML;";
                                        } else {
                                            if ($step->second == "0") {
                                                $result = $result . "\n if(nc_parameter_4_" . $step->first . ".innerHTML == '"
                                                    . GlobalController::is_boolean_true()
                                                    . "') {x = 1;} else {x = 0;}";
                                            } else {
                                                $result = $result . "\n x = nc_parameter_4_" . $step->first
                                                    . ".innerHTML;";
                                            }
                                        }
                                    } else {
                                        if ($step->second == "0") {
                                            $result = $result . "\n if(nc_parameter_4_" . $step->first . ".checked) {x = 1;} else {x = 0;}";
                                        } else {
                                            $result = $result . "\n if(nc_parameter_4_" . $step->first . ".checked) {x = '"
                                                . GlobalController::is_boolean_true()
                                                . "';} else {x = '" . GlobalController::is_boolean_false() . "';}";
                                        }
                                    }

                                    if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            // Начиная со второго(индекс==1) элемента массива языков сохранять
                                            if ($i > 0) {
                                                $result = $result . "\n x" . "_" . $lang_key . " = x;";
                                            }
                                            $i = $i + 1;
                                        }
                                    }

                                } elseif ($ln_first->parent_base->type_is_list()) {
                                    if ($ln_first->parent_is_parent_related == true) {
                                        if ($step->second == "I") {
                                            // Используется "x = nc_param_id_4_"
                                            $result = $result . "\n x = nc_param_id_4_" . $step->first
                                                . ".innerHTML;";
                                        } else {
                                            // Используется "x = nc_parameter_4_"
                                            $result = $result . "\n x = nc_parameter_4_" . $step->first
                                                . ".innerHTML;";
                                        }
                                    } else {
                                        if ($step->second == "I") {
                                            // Используется "x = nc_parameter_4_"
                                            $result = $result . "\n x = nc_parameter_4_" . $step->first
                                                . ".value;";
//                                            // Используется "x = nc_param_id_4_"
//                                            $result = $result . "\n x = nc_param_id_4_" . $step->first
//                                                . ".innerHTML;";
                                        } else {
                                            // Используется "x = nc_parameter_4_"
                                            $result = $result . "\n  x = nc_parameter_4_" . $step->first
                                                . ".options[nc_parameter_4_" . $step->first
                                                . ".selectedIndex"
                                                . "].text;";
                                        }
                                    }
                                    if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            // Начиная со второго(индекс==1) элемента массива языков сохранять
                                            if ($i > 0) {
                                                $result = $result . "\n x" . "_" . $lang_key . " = x;";
                                            }
                                            $i = $i + 1;
                                        }
                                    }
                                }
                            }
                            break;
                        case
                        "M":
                            // Математические операции над x и y
                            // Нужно
                            $result = $result . "\n x = Number(x);\n y = Number(y);";
                            switch ($step->first) {
                                case "+":
                                    $result = $result . "\n x = y + x;\n y = 0;";
                                    break;
                                case "-":
                                    $result = $result . "\n x = y - x;\n y = 0;";
                                    break;
                                case "*":
                                    $result = $result . "\n x = y * x;\n y = 0;";
                                    break;
                                case "/":
                                    $result = $result . "\n if (x == 0) {
                                        x = 0;\n y = 0; error_message = error_div0;
                                    }else
                                    {x = y / x;\n y = 0;}";
                                    break;
                            }
                            break;
                        case "S":
                            // Строковые операции над x и y
                            switch ($step->first) {
                                case ".":
                                    $result = $result . "\n x = y + '" . trim($step->second) . "' + x; y = '';";

                                    if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false) {
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            // Начиная со второго(индекс==1) элемента массива языков сохранять
                                            if ($i > 0) {
                                                $result = $result . "\n x_" . $lang_key . " = y_" . $lang_key . " + '" . trim($step->second) . "' + x_" . $lang_key . "; y_" . $lang_key . " = '';";
                                            }
                                            $i = $i + 1;
                                        }
                                    }


                                    break;
                            }
                            break;
                        case "R":
                            // Округление числа по правилам математики
                            // Нужно
                            $result = $result . "\n x = Number(x);";
                            $round_type = "0";
                            // Округление числа в меньшую сторону
                            if ($step->second == "-1") {
                                $round_type = "-1";
                                // Округление числа в большую сторону
                            } elseif ($step->second == "1") {
                                $round_type = "1";
                            }
                            $result = $result . "\n x = my_rnd(x, " . $step->first . ", " . $round_type . ");";
                            break;
                        case "U":
                            // Сдвиг по стеку
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

// Неэкранное вычисление
    static function steps_calc_code(Item $item, Link $link, $block)
    {
        $res_array = array();
        // массив "glo_menu_main" показывает, что четыре поля наименований хранятся в bases и items
        // ['1', '2', '3', '4'] - тут разницы нет, какие значения хранятся; главное, чтобы что-то хранилось
        $main_array = ['1', '2', '3', '4'];
        // Сохранить текущий язык
        $locale = App::getLocale();
        // "$link->parent_is_nc_screencalc == false" используется
        if ($link->parent_is_numcalc == true & $link->parent_is_nc_screencalc == false) {
            $steps = Step::where('link_id', $link->id)
                ->where('block', $block)
                ->orderBy('row')
                ->get();
            if (count($steps) > 0) {
                foreach ($main_array as $lang_key => $lang_value) {
                    $x = "";
                    $y = "";
                    $z = "";
                    if ($lang_key < count(config('app.locales'))) {
                        $lc = config('app.locales')[$lang_key];
                        App::setLocale($lc);
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
                                    //$item_link = GlobalController::get_parent_item_from_main($item->id, $link_first->id);
                                    $item_link = GlobalController::view_info($item->id, $link_first->id);
                                    $y = $x;
                                    if ($link_first) {
                                        if ($item_link) {
//                                $result = $item->name_lang_0 == "1" ? html_entity_decode('  &#9745;')
//                                    : ($item->name_lang_0 == "0" ? html_entity_decode('&#10065;') : trans('main.empty'));
//                            } elseif ($link->parent_base->type_is_boolean()) {
//                                $result = $result . "\n if(nc_parameter_4_" . $step->first . ".checked) {x = 1;}
//                                else {x = 0;}";
////                                $result = $result . "\n x = 0;";
                                            if ($link_first->parent_base->type_is_boolean()) {
                                                if ($step->second == "0") {
                                                    $x = $item_link->name_lang_0 == "1" ? 1 : 0;
                                                } else {
                                                    $x = ($step->second == "I" ? $item_link->id : $item_link->name());
                                                }
                                            } elseif ($link_first->parent_base->type_is_number()) {
                                                $x = $step->second == "I" ? $item_link->id : $item_link->name();
                                            } elseif ($link_first->parent_base->type_is_string()) {
                                                $x = ($step->second == "I" ? $item_link->id : $item_link->name());
                                            } elseif ($link_first->parent_base->type_is_list()) {
                                                $x = ($step->second == "I" ? $item_link->id : $item_link->name());
                                            } else {
                                                $x = "";
//                                    // Для списков результатом является $item_link->id
//                                    if ($link_first->parent_base->type_is_list()) {
//                                        $x = $item_link->id;
//                                    } else {
//                                        $x = $item_link->name();
//                                    }
//                                    $x = ($step->second == "I" ? $item_link->name() : $item_link->id);
                                            }
                                        } else {
                                            $x = "";
                                        }
                                    } else {
                                        $x = "";
                                    }
                                    break;
                                case
                                "M":
                                    // Математические операции над x и y
                                    // Нужно
                                    $x = floatval($x);
                                    $y = floatval($y);
                                    switch ($step->first) {
                                        case "+":
                                            $x = $y + $x;
                                            $y = 0;
                                            break;
                                        case "-":
                                            $x = $y - $x;
                                            $y = 0;
                                            break;
                                        case "*":
                                            $x = $y * $x;
                                            $y = 0;
                                            break;
                                        case "/":
                                            if ($x == 0) {
                                                $x = 0;
                                            } else {
                                                $x = $y / $x;
                                            }
                                            $y = 0;
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
                                    // Нужно
                                    $x = floatval($x);
                                    $round_type = "0";
                                    // Округление числа в меньшую сторону
                                    if ($step->second == "-1") {
                                        $round_type = "-1";
                                        // Округление числа в большую сторону
                                    } elseif ($step->second == "1") {
                                        $round_type = "1";
                                    }
                                    $x = self::lc_rnd($x, $step->first, $round_type);
                                    break;
                                case "I":
                                    if ($step->first == 0 | $step->first == "") {
                                        $x = 0;
                                    } else {
                                        $x = floatval($x);
                                        $sf = floatval($step->first);
                                        // Для числового поля - число-нижняя граница
                                        $x = intval($x / $sf) * $sf;
                                        // Для строкового поля - интервал дат.
                                        if ($link->parent_base->type_is_string()) {
                                            // $step->first - база (например, 5)
                                            // Число дробное или нет
                                            $a0 = strpos($step->first, ".");
                                            // Не найдено "."
                                            if ($a0 == false) {
                                                $a1 = 0;
                                            } else {
                                                // Сначала вычисляется дробное число,
                                                // Затем считается количество цифр в дробной части
                                                // "-1" нужно
                                                $a1 = strlen(substr($step->first, $a0)) - 1;
                                            }
                                            $a2 = pow(10, ($a1 + 1));
                                            // Например 5 - 9.9
                                            $x = $x . " - " . (($x + $sf) - 1 / $a2);
                                        }
                                    }
                                    break;
                                case "U":
                                    // Сдвиг по стеку
                                    $z = $y;
                                    $y = $x;
                                    $x = $z;
                                    $z = "";
                                    break;
                            }
                        }
//        if ($link->parent_base->type_is_boolean()) {
//            if ($x != 1) {
//                $x = 0;
//            }
//        }
                    }
                    $res_array[$lang_key] = $x;
                }
            }
        }
        // Восстановить текущий язык
        App::setLocale($locale);
        return $res_array;
    }

    function lc_rnd($a, $b, $c)
    {
        $r = 0;
        $p = pow(10, $b);
        switch ($c) {
            case 0:
                $r = round($a * $p) / $p;
                break;
            case -1:
                $r = floor($a * $p) / $p;
                break;
            case 1:
                $r = ceil($a * $p) / $p;
                break;
        }
        return $r;
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
