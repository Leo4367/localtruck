<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\InquiryPrice;
use App\Models\Purchaser;
use App\Models\SendEmail;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use function Laravel\Prompts\table;
use App\Jobs\SendEmail as SendEmailJob;

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
        $new_purchaser_ids = [];

        // 遍历每一行，创建 Work 记录
        foreach ($customerNames as $index => $customerName) {
            $customerAddress = $deliver_address[$index];
            $workOrder = $workOrders[$index];

            // 创建 Work 记录
            $createdWork = Purchaser::create([
                'customer_name' => $customerName,
                'address' => $customerAddress,
                'work_order' => $workOrder,
            ]);
            $createdWorks[] = $createdWork;

            $new_purchaser_ids[] = $createdWork->id;
            // 发送邮件给所有报价公司
            foreach ($brokers as $broker) {
                // 创建 SendEmail 记录
                SendEmail::create([
                    'purchaser_id' => $createdWork->id,
                    'broker_id' => $broker->id,
                ]);

                // 创建 InquiryPrice 记录
                InquiryPrice::create([
                    'purchaser_id' => $createdWork->id,
                    'broker_id' => $broker->id,
                    'price' => null,
                ]);
            }
        }

        foreach ($brokers as $send_broker) {
            $email = $send_broker->email;
            $purchasers = Purchaser::all()->whereIn('id', $new_purchaser_ids)->toArray();
            $emailData = [
                'broker_name' => $send_broker->broker_name,
                'purchasers' => $purchasers,
            ];
            if ($send_broker->status) {
                SendEmailJob::dispatch($email, $emailData)->onQueue('emails');//将发送邮件的任务放到队列名为 emails 中
                //更新为已发送邮件状态
                SendEmail::whereIn('purchaser_id', $new_purchaser_ids)
                    ->where('broker_id', $send_broker->id)
                    ->update(['status' => true]);
            }
        }
        return redirect('/price');
    }

    public function index()
    {
        // 获取所有工作记录及其关联的报价
        $works = Purchaser::with(['inquiries' => function ($query) {
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
            $cornerstonePrice = null;

            foreach ($brokers as $broker) {
                $inquiry = $work->inquiries->firstWhere('broker.id', $broker->id);
                $price = $inquiry ? $inquiry->price : null;
                $row[$broker->broker_name] = $price; // 填充 broker 的报价

                // 获取 Cornerstone 公司的报价
                if ($broker->company_name === 'Cornerstone Systems Inc' && $price !== null) {
                    $cornerstonePrice = $price;
                }

                if ($price !== null) {
                    $quotes[] = $price; // 收集有效报价用于计算差价
                }
                // 将 broker_id 和 inquiry_id 加入到行数据中
                $row[$broker->broker_name . '_id'] = $inquiry ? $inquiry->id : null;
            }
            // 计算差价 (spread)
            if ($cornerstonePrice !== null && count($quotes) > 1) {
                $minPrice = min($quotes);
                // 如果最低报价是 Cornerstone 的报价，则差价为0
                $row['spread'] = ($minPrice === $cornerstonePrice) ? 0 : round($minPrice - $cornerstonePrice, 2);
            } else {
                $row['spread'] = null; // 如果没有有效报价或者 Cornerstone 没有报价
            }
            return $row;
        });


        return Inertia::render('Broker/Price', [
            'tableData' => $tableData,
            'columns' => $columns->values()->toArray(), // 嵌套结构的 columns
        ]);
    }

    public function tempindex()
    {
        // 获取所有工作记录及其关联的报价
        $works = Purchaser::with(['inquiries' => function ($query) {
            $query->with('broker'); // 加载关联的 broker 信息
        }])->get();

        // 获取所有 broker 信息
        $brokers = Broker::all();

        // 构建 columns 嵌套数据结构
        $columns = $brokers
            ->groupBy('company_name') // 按公司分组
            ->map(function ($group, $company) use ($works) {
                return [
                    'label' => $company,
                    'children' => $group->map(function ($broker) use ($works) {
                        // 获取当前 broker 的所有报价记录（inquiry_price）
                        $inquiry = $works->flatMap(function ($work) use ($broker) {
                            return $work->inquiries->filter(function ($inquiry) use ($broker) {
                                return $inquiry->broker_id === $broker->id;
                            });
                        })->first(); // 获取第一个匹配的报价记录

                        return [
                            'label' => $broker->broker_name,
                            'prop' => $broker->broker_name, // 动态列名，后端要与前端绑定
                            'inquiry_price_id' => $inquiry ? $inquiry->id : null, // 将 inquiry_price_id 添加到子节点
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
                'purchaser_id' => $work->id,
            ];

            // 初始化报价数据
            $quotes = []; // 存储每个 broker 的报价
            $cornerstonePrice = null;

            foreach ($brokers as $broker) {
                $inquiry = $work->inquiries->firstWhere('broker.id', $broker->id);
                $price = $inquiry ? $inquiry->price : null;
                $row[$broker->broker_name] = $price; // 填充 broker 的报价

                // 获取 Cornerstone 公司的报价
                if ($broker->company_name === 'Cornerstone Systems Inc' && $price !== null) {
                    $cornerstonePrice = $price;
                }

                if ($price !== null) {
                    $quotes[] = $price; // 收集有效报价用于计算差价
                }

                // 将 broker_id 和 inquiry_id 加入到行数据中
                $row[$broker->broker_name . '_id'] = $inquiry ? $inquiry->id : null;
            }
            // 计算差价 (spread) —— 每一行所有报价的最大差价
            if ($cornerstonePrice !== null && count($quotes) > 1) {
                $minPrice = min($quotes);
                // 如果最低报价是 Cornerstone 的报价，则差价为0
                $row['spread'] = ($minPrice === $cornerstonePrice) ? 0 : round($minPrice - $cornerstonePrice, 2);
            } else {
                $row['spread'] = null; // 如果没有有效报价或者 Cornerstone 没有报价
            }

            return $row;
        });

        // 返回前端的数据，包括基础数据和报价
        return Inertia::render('Broker/TempPrice', [
            'tableData' => $tableData,
            'columns' => $columns->values()->toArray(), // 嵌套结构的 columns
        ]);
    }

    public function updatePrice(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'price' => 'nullable|numeric|min:0',
        ]);

        // 查找对应的报价记录
        $inquiry = InquiryPrice::where('id', $validated['id'])->first();

        if (!$inquiry) {
            return response()->json(['success' => false, 'message' => '报价记录未找到'], 404);
        }

        // 更新价格
        $inquiry->price = $validated['price'];
        $inquiry->save();
        return response()->json(['success' => true, 'message' => '报价更新成功']);
    }

    public function gettabledata()
    {
        // 获取所有工作记录及其关联的报价
        $works = Purchaser::with(['inquiries' => function ($query) {
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
            $cornerstonePrice = null;

            foreach ($brokers as $broker) {
                $inquiry = $work->inquiries->firstWhere('broker.id', $broker->id);
                $price = $inquiry ? $inquiry->price : null;
                $row[$broker->broker_name] = $price; // 填充 broker 的报价

                // 获取 Cornerstone 公司的报价
                if ($broker->company_name === 'Cornerstone Systems Inc' && $price !== null) {
                    $cornerstonePrice = $price;
                }
                if ($price !== null) {
                    $quotes[] = $price; // 收集有效报价用于计算差价
                }
                // 将 broker_id 和 inquiry_id 加入到行数据中
                $row[$broker->broker_name . '_id'] = $inquiry ? $inquiry->id : null;
            }
            // 计算差价 (spread)
            if ($cornerstonePrice !== null && count($quotes) > 1) {
                $minPrice = min($quotes);
                // 如果最低报价是 Cornerstone 的报价，则差价为0
                $row['spread'] = ($minPrice === $cornerstonePrice) ? 0 : round($minPrice - $cornerstonePrice, 2);
            } else {
                $row['spread'] = null; // 如果没有有效报价或者 Cornerstone 没有报价
            }
            return $row;
        });
        return [
            'tableData' => $tableData,
            'columns' => $columns->values()->toArray(), // 嵌套结构的 columns
        ];
    }

}
