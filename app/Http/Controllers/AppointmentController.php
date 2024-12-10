<?php

namespace App\Http\Controllers;

use App\Models\DateManage;
use App\Models\Delivery;
use App\Models\DeliveryTime;
use App\Models\PickupTime;
use App\Models\User;
use App\Models\Pickup;
use App\Models\Appointment;
use App\Models\Warehouse;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentNotification;

// 假设你创建了一个发送短信的通知类
use Illuminate\Support\Str;
use Inertia\Inertia;


class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input data
        $validated = $request->validate([
            'driver_name' => 'required|string|max:255',
            'appt_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'po_number' => 'required|string',
            //'vehicle_type' => 'required|string',
            'time_slot' => 'required|string',
            'warehouse_id' => 'required|string',
            'dock_number' => 'required',
            'type' => 'required|string|in:Pickup,Delivery', // 验证 type 字段
        ]);
        $type = $request->type;
        $model = ($type === 'Pickup') ? new Pickup() : new Delivery();
        // 查找该 time_slot 是否已被预约
        $existingAppointment = $model::where('time_slot', $request->time_slot)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('dock_number', $request->dock_number)
            ->count();

        if ($existingAppointment >= 2) {
            return back()->withErrors(['time_slot' => 'This time slot has been booked. Please select another time.']);
        }

        // 查找该手机号是否已经注册
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            // 用户不存在，进行自动注册
            //$password = Str::random(8); // 生成随机8位密码
            $password = '12345678'; // 生成随机8位密码
            $user = User::create([
                'phone_number' => $request->phone_number,
                'name' => $request->driver_name,
                'password' => Hash::make($password),
            ]);

            // 发送短信通知用户注册成功和密码
            //Notification::send($user, new AppointmentNotification($password));
        }
        $pa = [
            'user_id' => $user->id,
            'phone_number' => $request->phone_number,
            'appt_number' => $request->appt_number,
            'po_number' => $request->po_number,
            //'vehicle_type' => $request->vehicle_type,
            'time_slot' => $request->time_slot,
            'warehouse_id' => $request->warehouse_id,
            'driver_name' => $request->driver_name,
            'dock_number' => $request->dock_number,
        ];
        // 登录用户
        Auth::login($user);
        // Save the data to the database
        if ($type == 'Pickup') {
            $isNotification = Pickup::create($pa);
            if ($isNotification) {
                $etruck = User::find(3);
                $isNotification->type = 'Pickup';
                //Notification::send($etruck, new AppointmentNotification($isNotification));
            }
        }
        if ($type == 'Delivery') {
            $isNotification = Delivery::create($pa);
            if ($isNotification) {
                $etruck = User::find(3);
                $isNotification->type = 'Delivery';
                //Notification::send($etruck, new AppointmentNotification($isNotification));
            }
        }

        // Return a response (or redirect)
        return Redirect::route('appointment.show');
    }

    public function show()
    {
        /// 获取当前登录用户的ID
        $user = Auth::user();
        // 获取该用户所有预约信息,status为有效预约
        $pickups = Pickup::with('warehouse')
            ->where('user_id', $user->id)
            //->where('status', '=', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($pickup) {
                $pickup->type = "Pickup";
                return $pickup;
            })
            ->toArray();
        $deliveries = Delivery::with('warehouse')
            ->where('user_id', $user->id)
            //->where('status', '=', 1)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($delivery) {
                $delivery->type = 'Delivery';
                return $delivery;
            })
            ->toArray();

        $appointments = array_merge($pickups, $deliveries);

        // 返回到前端的Vue页面，并将预约信息传递过去
        return Inertia::render('Appoint/Appointment', [
            'appointments' => $appointments
        ]);
    }

    //根据日期获取时段选项值
    public function getBookedSlots(Request $request)
    {
        $type = $request->input('type');
        $warehouse_id = $request->input('warehouse_id');
        $today = Carbon::parse($request->slot);
        $dock_number = $request->input('dock_number');

        if (empty($warehouse_id) or empty($dock_number)) {
            return response()->json(['message' => 'Please select the warehouse first']);
        }

        // 如果是周末
        if ($today->isWeekend()) {
            // 查询数据库，获取该日期的自定义时间段
            $customTimeSlots = DB::table('all_time_slots')
                ->whereDate('date_slot', $request->slot)
                ->where('type', $type)
                ->where('warehouse_id', $warehouse_id)
                ->where('status', '=', 1)
                ->pluck('time_slot');

            // 如果数据库中有时间段，返回这些时间段
            if ($customTimeSlots->isNotEmpty()) {
                $allTimeSlots = $customTimeSlots->toArray();
            } else {
                // 如果没有时间段，返回空数组（或者根据需求返回禁用提示）
                $allTimeSlots = [];
            }
        } else {
            // 如果不是周末，根据类型返回对应时间段
            if ($type === 'Pickup') {
                $option_pick = [];
                $pickupTime = PickupTime::where('status', 1)->pluck('time');
                foreach ($pickupTime as $timeSlot) {
                    $option_pick[] = $today->toDateString() . ' ' . $timeSlot;
                }
                $allTimeSlots = $option_pick;
            } elseif ($type === 'Delivery') {
                $option_deli = [];
                $deliveryTime = DeliveryTime::where('status', 1)->pluck('time');
                foreach ($deliveryTime as $timeSlot) {
                    $option_deli[] = $today->toDateString() . ' ' . $timeSlot;
                }
                $allTimeSlots = $option_deli;

            } else {
                return ['message' => 'Invalid type.'];
            }
        }

        $model = ($type === 'Pickup') ? new Pickup() : new Delivery();
        $bookedSlots = $model::whereDate('time_slot', $today)  // 只查找今天的预约
        ->where('warehouse_id', $warehouse_id) // 特定仓库
        ->where('dock_number', $dock_number) // 特定Dock
        ->select('time_slot', DB::raw('COUNT(*) as count'))  // 统计每个时间段的预约数量
        ->groupBy('time_slot')  // 按时间段分组
        ->having('count', '=', 2)  // 只选择已预约 2 次的时间段
        ->pluck('time_slot')  // 获取时间段列表
        ->toArray();

        /*$bookedSlots = Appointment::where('type', $type)
            ->whereDate('time_slot', $today)  // 只查找今天的预约
            ->where('warehouse_id', $warehouse_id) // 特定仓库
            ->select('time_slot', DB::raw('COUNT(*) as count'))  // 统计每个时间段的预约数量
            ->groupBy('time_slot')  // 按时间段分组
            ->having('count', '=', 2)  // 只选择已预约 2 次的时间段
            ->pluck('time_slot')  // 获取时间段列表
            ->toArray();*/

        return response()->json(['booked' => $bookedSlots, 'allTimeSlots' => $allTimeSlots]);
    }

    public function forbiddenDates(Request $request)
    {
        $warehouse_id = $request->input('warehouse_id');
        $type = $request->input('type');
        // 假设你有一个禁用日期的数组（可以从数据库获取）
        $forbiddenDates = DateManage::where('status', 1)
            ->where('type', $type)
            ->where('warehouse_id', $warehouse_id)
            ->pluck('forbidden_date')
            ->toArray();
        // 返回 JSON 响应
        return response()->json($forbiddenDates);
    }

    //获取仓库列表信息
    protected function getWarehouses()
    {
        $warehouses = Warehouse::select('id', 'name', 'address')->where('status', 1)->get()->toArray();

        return response()->json($warehouses);
    }

}
