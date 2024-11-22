<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use function Termwind\render;

class BrokerController extends Controller
{
    public function sendEmail()
    {
        $admin = new User();
        $admin->email = 'admin@gmail.com';
        $customers = explode("\n", request('customer_name'));
        $address = explode("\n", request('deliver_address'));
        $work_order = explode("\n", request('work_order'));
        //$customer_email = explode("\n", request('customer_email'));

        // 确保两个数组的长度一致，使用 count 找到最大长度
        $maxCount = max(count($customers), count($address),count($work_order));

        // 使用 array_pad 补全数组，避免索引溢出
        $customers = array_pad($customers, $maxCount, '');
        $address = array_pad($address, $maxCount, '');
        $work_order = array_pad($work_order, $maxCount, '');
        //$customer_email = array_pad($customer_email, $maxCount, '');

        // 整理数据为表格所需格式
        $tableData = [];
        foreach ($customers as $key => $customer) {
            //$work_order = rand(10000, 99999);
            $cornerstone = rand(1, 1000);
            $tlx = rand(1, 1999);
            $tql = rand(1, 1999);
            $spread = rand(1, 100);
            $tableData[] = [
                'name' => $customer,         // 客户姓名
                'address' => $address[$key], // 对应地址
                'work_order' => $work_order[$key],
                //'customer_email' => $customer_email,
                'a' => '',
                'b' => '',
                'c' => '',
                'spread' => '',
            ];
        }
        return Inertia::render("Broker/Price", ['tableData' => $tableData,]);
    }

    public function getPrice(Request $request)
    {
        // 整理数据为表格所需格式
        $tableData = [];
        for ($i = 1; $i < 6; $i++) {
            $customer = Str::random(6);
            $address = Str::random(6);
            $work_order = rand(10000, 99999);
            $cornerstone = rand(1, 1000);
            $tlx = rand(1, 1999);
            $tql = rand(1, 1999);
            $spread = rand(1, 100);
            $tableData[] = [
                'name' => $customer,         // 客户姓名
                'address' => $address, // 对应地址
                'work_order' => $work_order,
                'cornerstone' => $cornerstone,
                'tlx' => $tlx,
                'tql' => $tql,
                'spread' => $spread,
            ];
        }
        return Inertia::render("Broker/Price", ['tableData' => $tableData,]);
    }
}
