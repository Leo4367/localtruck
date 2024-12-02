<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Broker;
use App\Models\InquiryPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BrokerInquiryMail;
use Inertia\Inertia;
use function Laravel\Prompts\table;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'deliver_address' => 'required|string',
            'work_order' => 'required|string',
        ]);

        // 将每个 textarea 的内容按行分割成数组
        $customerNames = array_map('trim', explode("\n", $request->input('customer_name')));
        $deliver_address = array_map('trim', explode("\n", $request->input('deliver_address')));
        $workOrders = array_map('trim', explode("\n", $request->input('work_order')));

        // 检查三个数组的行数是否一致
        if (count($customerNames) !== count($deliver_address) || count($deliver_address) !== count($workOrders)) {
            return back()->withErrors('输入的字段数量不一致，请确保每一行匹配！');
        }

        $brokers = Broker::all();
        $createdWorks = [];

        // 遍历每一行，创建 Work 记录
        foreach ($customerNames as $index => $customerName) {
            $customerAddress = $deliver_address[$index];
            $workOrder = $workOrders[$index];

            // 创建 Work 记录
            $createdWork = Work::create([
                'customer_name' => $customerName,
                'address' => $customerAddress,
                'work_order' => $workOrder,
            ]);
            $createdWorks[] = $createdWork;

            // 发送邮件给所有报价公司
            foreach ($brokers as $broker) {
                /*Mail::to($broker->email)->send(
                    new BrokerInquiryMail($customerName, $customerAddress, $workOrder)
                );*/

                // 创建 InquiryPrice 记录
                InquiryPrice::create([
                    'work_id' => $createdWork->id,
                    'broker_id' => $broker->id,
                    'price' => null,
                ]);
            }
        }
        return redirect('/broker-sendemail');
    }

    public function index()
    {
        // 获取所有工作记录及其关联的报价
        $works = Work::with(['inquiries' => function ($query) {
            $query->with('broker'); // 加载关联的 broker 信息
        }])->get();

        // 获取所有 broker 信息
        $brokers = Broker::all();

        // 构建 columns 嵌套数据结构
        $columns = $brokers
            ->groupBy('company_name') // 按公司分组
            ->map(function ($group, $company) {
                return [
                    'label' => $company,
                    'children' => $group->map(function ($broker) {
                        return [
                            'label' => $broker->broker_name,
                            'prop' => $broker->broker_name, // 动态列名，后端要与前端绑定
                        ];
                    }),
                ];
            });

        // 构建 tableData 表格数据
        $tableData = $works->map(function ($work) use ($brokers) {
            // 初始化基础数据
            $row = [
                'customer_name' => $work->customer_name,
                'deliver_address' => $work->address,
                'work_order' => $work->work_order,
            ];

            // 初始化报价数据
            $quotes = []; // 存储每个 broker 的报价
            foreach ($brokers as $broker) {
                $inquiry = $work->inquiries->firstWhere('broker.id', $broker->id);
                $price = $inquiry ? $inquiry->price : null;
                $row[$broker->broker_name] = $price; // 填充 broker 的报价

                if ($price !== null) {
                    $quotes[] = $price; // 收集有效报价用于计算差价
                }
            }

            // 计算差价 (spread) —— 每一行所有报价的最大差价
            $row['spread'] = count($quotes) > 1 ? max($quotes) - min($quotes) : null;

            return $row;
        });

        return Inertia::render('Broker/Price', [
            'tableData' => $tableData,
            'columns' => $columns->values()->toArray(), // 嵌套结构的 columns
        ]);
    }



}
