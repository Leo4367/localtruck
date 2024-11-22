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
        $works = Work::with(['inquiries' => function ($query) {
            $query->with('broker'); // Load brokers for each inquiry
        }])->get();


        $brokers = Broker::pluck('broker_name'); // Get all broker names

        $tableData = $works->map(function ($work) use ($brokers) {
            // Base row data
            $row = [
                'customer_name' => $work->customer_name,
                'deliver_address' => $work->address,
                'work_order' => $work->work_order,
            ];

            // Broker quotes
            $quotes = [];
            foreach ($brokers as $broker) {
                // Get the price for the current broker
                $inquiry = $work->inquiries->firstWhere('broker.broker_name', $broker);
                $quotes[$broker] = $inquiry ? $inquiry->price : null;
                $row[$broker] = $quotes[$broker];
            }

            // Calculate the spread if there are valid quotes
            $validQuotes = array_filter($quotes, fn($price) => !is_null($price));
            $row['spread'] = count($validQuotes) > 1
                ? max($validQuotes) - min($validQuotes)
                : null;

            return $row;
        });


        return Inertia::render('Broker/Price', [
            'tableData' => $tableData,
            'columns' => $brokers->map(fn($name) => [
                'label' => $name . '($)',
                'prop' => $name,
            ]),
        ]);
    }


}
