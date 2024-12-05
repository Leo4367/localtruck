<?php

namespace App\Admin\Controllers\InquiryPrice;

use App\Models\InquiryPrice;
use App\Models\Purchaser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PurchaserController extends AdminController
{
    protected $title = 'Purchaser';

    protected function grid()
    {
        $grid = new Grid(new Purchaser());

        $grid->column('id', __('ID'))->sortable();

        $grid->column('customer_name', __('Customer Name'))->expand(function ($model) {
            // 获取报价数据并按价格升序排序
            $comments = InquiryPrice::with('broker')
                ->where('purchaser_id', $model->id)
                ->whereNotNull('price') // 排除没有报价的数据
                ->orderBy('price', 'desc')
                ->get();
            /**
             * 计算差价 $differ 逻辑
             */
            $quotes = []; // 存储每个 broker 的报价
            $cornerstonePrice = null;
            foreach ($comments as $comment) {
                // 获取 Cornerstone 公司的报价,先决条件：这家公司在数据库只能有一条报价
                if ($comment->broker->company_name === 'Cornerstone Systems Inc' && $comment->price !== null) {
                    $cornerstonePrice = $comment->price;
                }

                if ($comment->price !== null) {
                    $quotes[] = $comment->price; // 收集有效报价用于计算差价
                }
            }
            if ($cornerstonePrice !== null && count($quotes) > 1) {
                $minPrice = min($quotes);
                // 如果最低报价是 Cornerstone 的报价，则差价为0
                $differ = ($minPrice === $cornerstonePrice) ? 0 : $minPrice - $cornerstonePrice;
            } else {
                $differ = null; // 如果没有有效报价或者 Cornerstone 没有报价
            }


            // 获取最低报价的 `price`
            $minPrice = $comments->isEmpty() ? null : $comments->last()->price;
            // 构建表格行
            $headers = ['Company', 'Broker', 'Price ($)', 'Created At'];
            $rows = $comments->map(function ($comment) use ($minPrice) {
                // 判断是否为最低价行
                $isMinPriceRow = $comment->price === $minPrice;
                $rowClass = $isMinPriceRow ? 'class="highlight-row"' : '';

                return "<tr {$rowClass}>
                <td>{$comment->broker->company_name}</td>
                <td>{$comment->broker->broker_name}</td>
                <td>{$comment->price}</td>
                <td>{$comment->created_at->format('Y-m-d H:i:s')}</td>
            </tr>";
            })->implode('');

            // 拼接 HTML 表格
            $table = "<table class='table table-bordered'>
            <thead>
                <tr><th>" . implode('</th><th>', $headers) . "</th></tr>
            </thead>
            <tbody>
                {$rows}
                <tr>
                <td colspan='2'>" . '差价（$）' . "</td>
                <td>{$differ}</td></tr>
            </tbody>
        </table>";
            $table .= '<style>
.highlight-row {
    background-color: #56b7c9; /* 浅黄色背景 */
    font-weight: bold;
}

</style>';

            return $table;
        })->color('purple');
        $grid->column('address', __('Address'));
        $grid->column('work_order', __('Work Order'));
        //$grid->column('work_order')->view('emails.broker_inquiry');

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->batchActions(function ($batch) {
            $batch->disableDelete(); // 禁用批量删除按钮
        });

        return $grid;
    }

    protected function detail($id)
    {

        $show = new Show(Purchaser::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('customer_name', __('Customer Name'));
        $show->field('address', __('Address'));
        $show->field('work_order', __('Work Order'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Purchaser());

        $form->display('id', __('ID'));
        $form->text('customer_name', __('Customer Name'));
        $form->text('address', __('Address'));
        $form->text('work_order', __('Work Order'));

        return $form;
    }
}
