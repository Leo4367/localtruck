<?php

namespace App\Admin\Controllers\InquiryPrice;


use App\Models\SendEmail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\fromJSON;


class SendEmailController extends AdminController
{
    protected $title = "Send Email";


    //菜单主页list
    protected function grid()
    {
        $grid = new Grid(new SendEmail());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('user.username', __('Operator'));
        /*$grid->column('data', __('Table'))->table(
            [
                'company_name' => 'company_name',
                'customer_name' => 'customer_name',
                'email' => 'email',
            ]
        );*/
        $grid->column('message', __('Message'));
        /*$grid->column('company_name', __('Company Name'));
        $grid->column('customer_name', __('Customer Name'));
        $grid->column('address', __('Address'));
        $grid->column('email', __('Email'));
        $grid->column('work_order', __('Work Order'));
        $grid->column('cornerstone', __('Cornerstone'));
        $grid->column('tlx', __('TLX'));
        $grid->column('tql', __('TQL'));
        $grid->column('spread', __('Spread'));*/
        $grid->column('is_send', __('Is Send'))->display(function () {
            return $this->is_send == 1 ? "Yes" : "No";
        })->label([
            0 => 'danger',
            1 => 'success',
        ]);

        $grid->column('created_at', __('Created At'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        });
        $grid->column('updated_at', __('Updated At'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        })->hide();
        /*$grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('time_slot', __('Time Slot'))->sortable();
        $grid->column('completion_time', __('Completion Time'))->color('green')->sortable();
        $grid->column('driver_name', __('Driver Name'));
        $grid->column('phone_number', __('Phone Number'));
        $grid->column('pickup_number', __('Appt Number'));
        $grid->column('warehouse_id', __('Warehouse'))->editable('select', Warehouse::where('status', 1)->pluck('name', 'id')->toArray());

        $grid->column('type')->label([
            'Pickup' => 'info',
            'Delivery' => 'warning',
        ]);
        $grid->column('status')->select([
            0 => 'No-Show',
            1 => 'Scheduled',
            2 => 'Completed',
        ]);

        $grid->column('created_at', __('Created At'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        })->hide();
        $grid->column('updated_at', __('Updated At'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        })->hide();

        $grid->model()->orderBy('time_slot', 'desc');

        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('phone_number', 'Phone Number');
                $filter->like('pickup_number', 'Appt Number');
                $filter->equal('type', "Type")->select(['Pickup' => 'Pickup', 'Delivery' => 'Delivery']);
                $filter->equal('warehouse_id', "Warehouse")->select(Warehouse::where('status', 1)->pluck('name', 'id'));
                // 设置created_at字段的范围查询
                $filter->between('created_at', 'Created Time')->datetime();
            });
            // 范围过滤器，调用模型的`onlyTrashed`方法，查询出被软删除的数据。
            $filter->scope('trashed', '回收站')->onlyTrashed();
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('warehouse_id', 'Warehouse', Warehouse::where('status', 1)->pluck('name', 'id'));
            $selector->selectOne('type', 'Type', ['Pickup' => 'Pickup', 'Delivery' => 'Delivery']);
        });
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
        });
        $grid->disableCreateButton();
        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
        });

        // 判断当前用户是否为 "View Only Role" 角色
        if (Admin::user()->isRole('view-only')) {
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->batchActions(function ($batch) {
                $batch->disableDelete(); // 禁用批量删除按钮
            });
        }*/

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SendEmail::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user.username', __('Operator'));
        $show->field('company_name', __('Company Name'));
        $show->field('customer_name', __('Customer Name'));
        $show->field('address', __('Address'));
        $show->field('email', __('Email'));
        $show->field('work_order', __('Work Order'));
        $show->field('cornerstone', __('Cornerstone'));
        $show->field('tlx', __('TLX'));
        $show->field('tql', __('TQL'));
        $show->field('spread', __('Spread'));
        $show->field('is_send', __('Is Send'))->as(function () {
            return [
                false => 'No',
                true => 'Yes',
            ];
        });
        $show->field('created_at', __('Created At'));
        $show->field('updated_at', __('Updated At'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SendEmail);

        $form->display('id', __('ID'));
        $form->display('user_id', __('User ID'));
        //$form->textarea('company_name', __('Company Name'));
        $form->table('data', function ($table) {
            $table->text('company_name');
            $table->text('customer_name');
            $table->text('address');
            $table->text('email');
        });
        $form->textarea('message', __('Message'));
        /*$form->textarea('customer_name', __('Customer Name'));
        $form->textarea('address', __('Address'));
        $form->textarea('email', __('Email'));
        $form->textarea('work_order', __('Work Order'));
        $form->textarea('cornerstone', __('Cornerstone'));
        $form->textarea('tlx', __('TLX'));
        $form->textarea('tql', __('TQL'));
        $form->textarea('spread', __('Spread'));*/
        $form->radioCard('is_send', __('Is Send'))->options([
            false => 'No',
            true => 'Yes',
        ]);
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        $form->saving(function (Form $form) {
            $sendemail = $form->model();
            $sendemail->user_id = Auth::id();
        });

        return $form;
    }

}
