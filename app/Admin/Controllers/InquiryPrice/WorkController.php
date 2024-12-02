<?php


namespace App\Admin\Controllers\InquiryPrice;


use App\Models\InquiryPrice;
use App\Models\SendEmail;
use App\Models\Work;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkController extends AdminController
{
    protected $title = "Work";

    protected function grid()
    {
        $grid = new Grid(new Work());

        $grid->column('id', __('ID'))->sortable();
        /*$grid->column('customer_name', __('Customer Name'))->expand(function ($model) {

            $comments = InquiryPrice::with('broker')->where('work_id', $model->id)->orderBy('price')->get()->map(function ($comment) {
                $comment->company_name = $comment->broker->company_name;
                $comment->broker_name = $comment->broker->broker_name;
                return $comment->only(['company_name', 'broker_name', 'price', 'created_at']);
            });

            return new Table(['Company', 'Broker', 'Price', 'Created At'], $comments->toArray());
        });*/

        $grid->column('customer_name', __('Customer Name'))->expand(function ($model) {
            // 获取报价数据并按价格升序排序
            $comments = InquiryPrice::with('broker')
                ->where('work_id', $model->id)
                ->whereNotNull('price') // 排除没有报价的数据
                ->orderBy('price', 'asc')
                ->get();

            // 获取最低报价的 `price`
            $minPrice = $comments->isEmpty() ? null : $comments->first()->price;

            // 构建表格行
            $headers = ['Company', 'Broker', 'Price', 'Created At'];
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
            </tbody>
        </table>";
            $table .= '<style>
.highlight-row {
    background-color: #ffefc3; /* 浅黄色背景 */
    font-weight: bold;
}

</style>';

            return $table;
        })->color('purple');
        $grid->column('address', __('Address'));
        //$grid->column('work_order', __('Work Order'));
        $grid->column('work_order')->view('emails.broker_inquiry');

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Work::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('customer_name', __('Customer Name'));
        $show->field('address', __('Address'));
        $show->field('work_order', __('Work Order'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Work());

        $form->display('id', __('ID'));
        $form->text('customer_name', __('Customer Name'));
        $form->text('address', __('Address'));
        $form->text('work_order', __('Work Order'));

        return $form;
    }
}
