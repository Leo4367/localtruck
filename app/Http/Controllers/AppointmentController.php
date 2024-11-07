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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AppointmentController extends Controller
{
    /*protected array $pickupTime = [
        "08:00:00", "08:30:00", "09:00:00", "09:30:00", "10:00:00", "10:30:00", "11:00:00", "11:30:00", "12:00:00",
        "13:00:00", "13:30:00", "14:00:00", "14:30:00", "15:00:00", "15:30:00", "16:00:00", "16:30:00"
    ];
    protected array $deliveryTime = [
        "08:00:00", "08:30:00", "09:00:00", "09:30:00", "10:00:00", "10:30:00", "11:00:00",
        "13:00:00", "13:30:00", "14:00:00", "14:30:00", "15:00:00", "15:30:00", "16:00:00", "16:30:00"
    ];*/

    public function store(Request $request)
    {
        // Validate the input data
        $validated = $request->validate([
            'driver_name' => 'required|string|max:255',
            'pickup_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'time_slot' => 'required|string',
            'warehouse_id' => 'required|string',
            'type' => 'required|string|in:Pickup,Delivery', // 验证 type 字段
        ]);

        // 查找该 time_slot 是否已被预约
        $existingAppointment = Appointment::where('time_slot', $request->time_slot)
            ->where('type', $request->type) // 只检查同类型的预约
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        if ($existingAppointment) {
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
            'id' => $user->id,
            'phone_number' => $request->phone_number,
            'pickup_number' => $request->pickup_number,
            'time_slot' => $request->time_slot,
            'warehouse_id' => $request->warehouse_id,
            'driver_name' => $request->driver_name,
            'type' => $request->type,
        ];
        // 创建预约记录
        $appointment_id = $this->appointment_id($pa);
        // 登录用户
        Auth::login($user);
        // Save the data to the database
        if ($request->type == 'Pickup') {
            Pickup::create([
                'appointment_id' => $appointment_id,
                'driver_name' => $request->driver_name,
                'pickup_number' => $request->pickup_number,
                'phone_number' => $request->phone_number,
                'time_slot' => $request->time_slot,
                'warehouse_id' => $request->warehouse_id,
            ]);
        }
        if ($request->type == 'Delivery') {
            Delivery::create([
                'appointment_id' => $appointment_id,
                'driver_name' => $request->driver_name,
                'container_number' => $request->pickup_number,
                'phone_number' => $request->phone_number,
                'time_slot' => $request->time_slot,
                'warehouse_id' => $request->warehouse_id,
            ]);
        }

        // Return a response (or redirect)
        return Redirect::route('appointment.show');
    }

    public function show()
    {
        /// 获取当前登录用户的ID
        $user = Auth::user();

        // 获取该用户所有预约信息,status为有效预约
        $appointments = Appointment::with('warehouse')
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->get();

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

        if (empty($warehouse_id)) {
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
                $pickupTime = PickupTime::where('status',1)->pluck('time');
                foreach ($pickupTime as $timeSlot) {
                    $option_pick[] = $today->toDateString() . ' ' . $timeSlot;
                }
                $allTimeSlots = $option_pick;
            } elseif ($type === 'Delivery') {
                $option_deli = [];
                $deliveryTime = DeliveryTime::where('status',1)->pluck('time');
                foreach ($deliveryTime as $timeSlot) {
                    $option_deli[] = $today->toDateString() . ' ' . $timeSlot;
                }
                $allTimeSlots = $option_deli;
            } else {
                return ['message' => 'Invalid type.'];
            }
        }

        // 查询特定类型 (Pickup 或 Delivery) 的已预约时间段
        $bookedSlots = Appointment::where('type', $type)
            ->whereDate('time_slot', $today)
            ->where('warehouse_id', $warehouse_id)
            ->pluck('time_slot')
            ->toArray();

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

    protected function appointment_id($pa)
    {
        $appointment = Appointment::create([
            'user_id' => $pa['id'],
            'phone_number' => $pa['phone_number'],
            'pickup_number' => $pa['pickup_number'],
            'time_slot' => $pa['time_slot'],
            'driver_name' => $pa['driver_name'],
            'type' => $pa['type'],
            'warehouse_id' => $pa['warehouse_id'],
        ]);
        return $appointment->id;
    }

    //获取仓库列表信息
    protected function getWarehouses()
    {
        $warehouses = Warehouse::select('id', 'name', 'address')->where('status', 1)->get()->toArray();
        return response()->json($warehouses);
    }

}
