<?php

namespace App\Models;

use App\Http\Controllers\GlobalController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Observers\ItemObserver;
use phpDocumentor\Reflection\Types\Boolean;

class Item extends Model
{
    protected $fillable = ['base_id', 'project_id', 'updated_user_id', 'code', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];

    function base()
    {
        return $this->belongsTo(Base::class, 'base_id');
    }

    function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    function created_user()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    function updated_user()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

//  Похожие строки в в GlobalController.php и в Item.php
    function created_date()
    {
        return $this->created_at->Format(trans('main.format_date'));
    }

    function updated_date()
    {
        return $this->updated_at->Format(trans('main.format_date'));
    }

    function created_user_date()
    {
        return $this->created_user->name() . ", " . $this->created_at->Format(trans('main.format_date')) . ", " . $this->created_user->email;
    }

    function updated_user_date()
    {
        return $this->updated_user->name() . ", " . $this->updated_at->Format(trans('main.format_date')) . ", " . $this->updated_user->email;
    }

    function created_user_date_time()
    {
        return $this->created_user->name() . ", " . $this->created_at->Format(trans('main.format_date_time')) . ", " . $this->created_user->email;
    }

    function updated_user_date_time()
    {
        return $this->updated_user->name() . ", " . $this->updated_at->Format(trans('main.format_date_time')) . ", " . $this->updated_user->email;
    }

    function child_mains()
    {
        return $this->hasMany(Main::class, 'child_item_id');
    }

    function parent_mains()
    {
        return $this->hasMany(Main::class, 'parent_item_id');
    }

    public function text()
    {
        return $this->hasOne(Text::class);
    }

    // name() используется для отображения значений полей
    // $fullname = true/false - вывод полной строки (более 255 символов)
    // Например у одного $item два описания (тип-Текст) с признаком "для вычисляемого наименования"
    // при $fullname = true результатом строка из двух строк 255 и 255 символов с каждого $item->name()
    // $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
    // $rightnull = true/false - у вещественных чисел убрать правые нули после запятой
    function name_start($fullname = false, $numcat = false, $rightnull = false)
    {
        $result = "";  // нужно, не удалять

//        //этот вариант тоже работает, но второй вариант предпочтительней
//        if ($this->base->type_is_date()) {
//            $result = date_create($this->name_lang_0)->Format(trans('main.format_date'));
//        } else {
//            $index = array_search(App::getLocale(), config('app.locales'));
//            if ($index !== false) {   // '!==' использовать, '!=' не использовать
//                $result = $this['name_lang_' . $index];
//            }
//        }
        $base = $this->base;
        if ($base) {
            if ($base->type_is_date()) {
                // "$this->name_lang_0 ??..." нужно, в случае если в $this->name_lang_0 хранится не дата
//               $result = $this->name_lang_0 ? date_create($this->name_lang_0)->Format(trans('main.format_date')):'';
                if (($timestamp = date_create($this->name_lang_0)) === false) {
                    $result = $this->name_lang_0;
                } else {
                    $result = date_create($this->name_lang_0)->Format(trans('main.format_date'));
                }
                // Не использовать
//                    $result = date_create($this->name_lang_0)->Format('Y.m.d');

            } elseif ($base->type_is_number()) {
                $result = GlobalController::restore_number_from_item($base, $this->name_lang_0, $numcat, $rightnull);

            } elseif ($base->type_is_boolean()) {
                //    Похожие строки в Base.php
                // #65794 - ранее был пустой квадратик
                $result = $this->name_lang_0 == "1" ? html_entity_decode('  &#9745;')
                    : ($this->name_lang_0 == "0" ? html_entity_decode('&#10065;') : trans('main.empty'));
                // Не использовать
//                $result = $this->name_lang_0 == "1" ? "1-".trans('main.true')
//                    : ($this->name_lang_0 == "0" ? "0-".trans('main.false') : trans('main.empty'));
                //
            } elseif ($base->type_is_text()) {
                $result = GlobalController::it_txnm_n2b($this->id);
            } else {
                $index = array_search(App::getLocale(), config('app.locales'));
                if ($index !== false) {   // '!==' использовать, '!=' не использовать
                    $result = trim($this['name_lang_' . $index]);
                    // Не удалять
//                    if ($fullname == true) {
//                        //ограниченные 255 - размером полей хранятся в $item->name_lang_0 - $item->name_lang_3
//                        $maxlen = 255;
//                        if (($base->is_calcname_lst == true) && (mb_strlen($result) >= $maxlen)) {
//                            // похожи GlobalController::itnm_left() и Item.php ("...")
//                            if (mb_substr($result, $maxlen - 3, 3) == "...") {
//                                // Полное наименование, более 255 символов
//                                //https://stackoverflow.com/questions/19693946/non-static-method-should-not-be-called-statically
//                                $result = ItemController::calc_value_func($this)['calc_full_lang_' . $index];
//                            }
//                        }
//                    }
                    if ($fullname == true & $base->is_calcname_lst == true) {
                        $result = ItemController::calc_value_func($this)['calc_full_lang_' . $index];
                    }
                }
            }
        }
//        if ($result == "") {
//            $result = $this->name_lang_0;
//        }

        return $result;
    }

    // "\~" - символ перевода каретки (используется также в Item.php: функции name() nmbr())
    // "\~" - символ перевода каретки (используется также в ItemController.php: calc_value_func(); GlobalController: itnm_left)
    // "\t" - символ широкого пробела(исп. для отступа в абзаце)
    //  (используется также в Item.php: name(),nmbr(), GlobalController:itnm_left()
    // $fullname = true/false - вывод полной строки (более 255 символов)
    // $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
    // $rightnull = true/false - у вещественных чисел убрать правые нули после запятой
    function name($fullname = false, $numcat = false, $rightnull = false, $emoji_enable = false)
    {
        $result = self::name_start($fullname, $numcat, $rightnull);
        $result = str_replace('\~', '', $result);
        // Не нужна эта строка
        // $result = str_replace('\t', '', $result);
        // Похожая строка в Item.php::name() и Text::name()
        // Вторым параметром передается $this->base
        if ($emoji_enable == true) {
            // Передается значение $numcat - выводить пробел между числом и символом валюты (в основном для этого используется)
            $result = GlobalController::name_and_emoji($result, $this->base, $numcat);
        }
        return $result;
    }

    function cdnm($fullname = false, $numcat = false, $rightnull = false)
    {
        $result = self::name($fullname, $numcat, $rightnull);
        if ($this->base->is_code_needed == true) {
            $result = $this->code . ', ' . $result;
        }
        return $result;
    }

    // "\~" - символ перевода каретки (используется также в Item.php: name() nmbr())
    // "\~" - символ перевода каретки (используется также в ItemController.php: calc_value_func(), GlobalController: itnm_left)
    // "\t" - символ широкого пробела(исп. для отступа в абзаце) (используется также в Item.php: функции name() nmbr())
    // $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
    // fullname = true
    function nmbr($fullname = false, $numcat = false, $rightnull = false, $emoji_enable = false)
    {
        //$result = self::name_start(true, false);
        $result = self::name_start($fullname, $numcat, $rightnull);
        $result = str_replace('\~', '<br>', $result);
        // Не нужна эта строка
        // $result = str_replace('\t', '&emsp;&emsp;', $result);
        if ($emoji_enable == true) {
            $result = GlobalController::name_and_first_emoji($result, $this->base, $numcat);
        }
        return $result;
    }

    // Массив names() в разбивке по языкам
    // names() используется для расчета вычисляемого наименования
    function names()
    {
        $res_array = array();
        // массив "glo_menu_main" показывает, что четыре поля наименований хранятся в bases и items
        // ['1', '2', '3', '4'] - тут разницы нет, какие значения хранятся; главное, чтобы что-то хранилось
        $main_array = ['1', '2', '3', '4'];
        // Сохранить текущий язык
        $locale = App::getLocale();
//        foreach (session('glo_menu_main') as $lang_key => $lang_value) {
        foreach ($main_array as $lang_key => $lang_value) {
            $name = "";  // нужно, не удалять
            if ($lang_key < count(config('app.locales'))) {
                $lc = config('app.locales')[$lang_key];
                App::setLocale($lc);
                $base = $this->base;
                if ($base) {
                    // Эта строка нужна, не удалять
                    // Для полей типа текст - наименование берется из $item->name_lang_x, а не с $text->name_lang_x
                    $name = $this['name_lang_' . $lang_key];
                    if ($base->type_is_date()) {
                        $name = date_create($name)->Format(trans('main.format_date'));
                        // Нужно для правильной сортировки по полю $item->name_lang_x
                        //$name = date_create($name)->Format('Y.m.d');

                    } elseif ($base->type_is_number()) {
                        $name = GlobalController::restore_number_from_item($base, $name);

                    } elseif ($base->type_is_boolean()) {
                        //    Похожие строки в Base.php
//                    $name = $name == "1" ? html_entity_decode('	&#9745;')
//                        : ($name == "0" ? html_entity_decode('&#10065;') : trans('main.empty'));
                        // Нужно для правильной сортировки по полю $item->name_lang_x
                        $name = $name == "1" ? trans('main.yes')
                            : ($name == "0" ? trans('main.no') : trans('main.empty'));
                        //

                    } elseif ($base->type_is_text()) {
                        // Полное текстовое наименование(более 255 символов), с разметкой
                        $name = GlobalController::it_txnm_n2b($this);
                    }
                }
            }
            $res_array[$lang_key] = $name;
        }
        // Восстановить текущий язык
        App::setLocale($locale);
        return $res_array;
    }

    function info()
    {
        return $this->name();
    }

    function code_add_zeros()
    {
        if ($this->base->is_code_zeros == true) {
            // Дополнить код слева нулями
            $this->code = str_pad($this->code, $this->base->significance_code, '0', STR_PAD_LEFT);
        }
    }

    function info_full()
    {
        return trans('main.item') . " (" . $this->id . ") _ " . $this->base->name() . " _ " . $this->name();
    }

    // Для типов полей Изображение, Документ
    // '$moderation = true' -  возвращать имя файла, независимо прошло/не прошло модерацию
    function filename($moderation = false)
    {
        // Для типов полей Изображение, Документ
        $result = $this->name_lang_0;
        if ($this->base->type_is_image() == true) {
            // Показывать для пользователя, создавшего фото
            // Для другого пользователя - проверка на модерацию
            $check = false;
            if (Auth::check()) {
                $check = $this->created_user_id != Auth::user()->id;
            } else {
                $check = true;
            }
            if ($check) {
                if ($moderation == false) {
                    if ($this->base->is_to_moderate_image == true) {
                        // На модерации
                        if ($this->name_lang_1 == "3") {
                            $result = "on_moderation.png";
                        } // Не прошло модерацию
                        elseif ($this->name_lang_1 == "2") {
                            $result = "did_not_pass_the_moderation.png";
                        }
                    }
                }
            }
        }
        return $result;
    }

// Для типов полей Изображение
    function title_img()
    {
        $result = trans('main.сlick_to_view');
        if ($this->base->type_is_image() == true) {
            if ($this->base->is_to_moderate_image == true) {
                // На модерации
                if ($this->name_lang_1 == "3") {
                    $result = trans('main.on_moderation');
                } // Не прошло модерацию
                elseif ($this->name_lang_1 == "2") {
                    $result = trans('main.did_not_pass_the_moderation');
                    // Показывать для пользователя, создавшего фото
                    if ($this->created_user_id == Auth::user()->id) {
                        if ($this->name_lang_2 != "") {
                            $result = $result . ": " . $this->name_lang_2;
                        }
                    }
                }
            }
        }
        return $result;
    }

// Возвращает true, если статус =  "не прошло модерацию"  и есть комментарий
//    function is_no_moderation_info()
//    {
//        $result = false;
//        if ($this->base->type_is_image() == true) {
//            // Показывать для пользователя, создавшего фото
//            if ($this->created_user_id == Auth::user()->id) {
//                if ($this->base->is_to_moderate_image == true) {
//                    // Не прошло модерацию
//                    if ($this->name_lang_1 == "2") {
//                        $result = trans('main.did_not_pass_the_moderation');
//                        if ($this->name_lang_2 != "") {
//                            $result = true;
//                        }
//                    }
//                }
//            }
//        }
//        return $result;
//    }

// Возвращает true, если статус =  "на модерации и не прошло модерацию"
// для пользователя, создавшего фото
    function is_moderation_info()
    {
        $result = false;
        if ($this->base->type_is_image() == true) {
            // Показывать для пользователя, создавшего фото
            $check = false;
            if (Auth::check()) {
                $check = $this->created_user_id == Auth::user()->id;
            } else {
                $check = false;
            }
            if ($check) {
                if ($this->base->is_to_moderate_image == true) {
                    // На модерации
                    if ($this->name_lang_1 == "3") {
                        $result = true;
                    }
                    // Не прошло модерацию
                    if ($this->name_lang_1 == "2") {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }

// Для типов полей Изображение
    function status_img()
    {
        $result = "";
        if ($this->base->type_is_image() == true) {
            if ($this->base->is_to_moderate_image == true) {
                // На модерации
                if ($this->name_lang_1 == "3") {
                    $result = trans('main.on_moderation');
                } // Не прошло модерацию
                elseif ($this->name_lang_1 == "2") {
                    $result = trans('main.did_not_pass_the_moderation');
                    if ($this->name_lang_2 != "") {
                        $result = $result . ": " . $this->name_lang_2;
                    }
                    // Прошло модерацию
                } elseif ($this->name_lang_1 == "1") {
                    $result = trans('main.moderated');
                    // Без модерации
                } elseif ($this->name_lang_1 == "0") {
                    $result = trans('main.without_moderation');
                }
            }
        }
        return $result;
    }

    static function get_img_statuses()
    {
        return array(
            "0" => trans('main.without_moderation'),
            "1" => trans('main.moderated'),
            "2" => trans('main.did_not_pass_the_moderation'),
            "3" => trans('main.on_moderation')
        );
    }

// Для типов полей Изображение, Документ
    function img_doc_exist()
    {
        return $this->name_lang_0 != "";
    }

// Для типа полей Число или Логический
// Результат, число дробное, целое число
    function numval()
    {
        $value = 0;
        $int_vl = 0;
        $result = false;
        if ($this->base->type_is_number() || $this->base->type_is_boolean()) {
            //if ($this->base->type_is_number()) {
            $result = true;
            if ($this->name_lang_0 == "") {
                $value = 0;
            } else {
                //$value = strval($this->name_lang_0);
                $value = floatval(strval($this->name_lang_0));
            }
            $int_vl = intval($value);
        }
        return ['result' => $result, 'value' => $value, 'int_vl' => $int_vl];
    }

// Для типа полей Число
    function boolval()
    {
        $value = null;
        $result = false;
        if ($this->base->type_is_boolean()) {
            $result = true;
            if ($this->name_lang_0 == "1") {
                $value = true;
            } elseif ($this->name_lang_0 == "0") {
                $value = false;
            } else {
                $value = null;
            }
        }
        return ['result' => $result, 'value' => $value];
    }

    // Для типа полей Дата
    // Преобразует '2022-06-15' в 'HJHH-JD-IE' для сортировки по убыванию
    function dt_desc()
    {
        //$result = $this->name();
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
        }
        $result = $this[$name];
        $search = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $replace = array('J', 'I', 'H', 'G', 'F', 'E', 'D', 'C', 'B', 'A');
        if ($this->base->type_is_date()) {
            $result = str_replace($search, $replace, $result);
        }
        return $result;
    }

    function is_history()
    {
        return $this->is_history == true;
    }

    function set_history(bool $save_record = false)
    {
        $this->is_history = true;
        if ($save_record == true) {
            $this->save();
        }
    }

    function clear_history(bool $save_record = false)
    {
        $this->is_history = false;
        if ($save_record == true) {
            $this->save();
        }
    }

    function button_title()
    {
        $result = "";
        if ($this->is_history()) {
            $result = trans('main.from_history');
        } else {
            $result = trans('main.to_history');
        }
        return $result;
    }

    function change_history(bool $save_record = false)
    {
        if ($this->is_history()) {
            $this->clear_history($save_record);
        } else {
            $this->set_history($save_record);
        }
    }

}
