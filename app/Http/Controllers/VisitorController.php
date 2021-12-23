<?php

namespace App\Http\Controllers;

use App\Models\Base;
use App\Models\Item;
use App\Models\Project;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    // Подсчет количества посетителей на сайте онлайн
    static function visitors_count()
    {
        // https://blog.myrusakov.ru/whoonline.html
        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');
        // 300 - 5 минут в секундах
        // 60 секунд * 5 минут
        $online_time = 300;
        $ip = $_SERVER["REMOTE_ADDR"];
        $ip = ip2long($ip);
        $date = time();
        $delete_date = $date - $online_time;
        $visitor = Visitor::where('ip', $ip)->first();
        if ($visitor) {
            $visitor->date = $date;
            $visitor->save();
        } else {
            $visit_new = new Visitor();
            $visit_new->ip = $ip;
            $visit_new->date = $date;
            $visit_new->save();
        }
        Visitor::where('date', '<', $delete_date)->delete();
        $result = Visitor::count();
        return $result;
    }

}
