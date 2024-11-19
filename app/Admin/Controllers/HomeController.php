<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Pickup;
use Carbon\Carbon;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        // 获取本周的开始和结束日期

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $mon = $startOfWeek->format('Y-m-d H:i:s'); // 本周一
        $sun = $endOfWeek->format('Y-m-d H:i:s'); // 本周日

        $dates = collect();

        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }
        // 查询本周的预约数据
        $pickups = Pickup::whereBetween('time_slot', [$mon, $sun])
            ->select(DB::raw('DATE(time_slot) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $deliveries = Delivery::whereBetween('time_slot', [$mon, $sun])
            ->select(DB::raw('DATE(time_slot) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        // 整理数据为前端需要的格式
        $pickups_data = [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('m-d')), // 本周所有日期
            'dataset' => $dates->map(fn($date) => $pickups->get($date, 0)) // 缺失的日期填充为0
        ];

        $deliveries_data = [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('m-d')), // 本周所有日期
            'dataset' => $dates->map(fn($date) => $deliveries->get($date, 0)) // 缺失的日期填充为0
        ];

        return $content
            ->title('Dashboard')
            ->description('Pickup & Delivery')
            ->view('dashboard', [
                'pickups' => $pickups_data,
                'deliveries' => $deliveries_data,
                ]); // 加载自定义视图
    }
}
