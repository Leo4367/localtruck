<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\Appointment;
use App\Models\Delivery;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class DeliveryController extends AdminController
{
    protected $title = "Delivery Appointments";

    protected array $states = [
        'on' => ['value' => 1, 'text' => 'on', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => 'off', 'color' => 'danger'],
    ];

    protected function grid()
    {
        $grid = new Grid(new Delivery());

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('time_slot', __('Time Slot'))->sortable();
        $grid->column('completion_time', __('Completion Time'))->color('green')->sortable();
        $grid->column('driver_name', __('Driver Name'));
        $grid->column('phone_number', __('Phone Number'));
        $grid->column('container_number', __('Container Number'));
        $grid->column('warehouse_id', __('Warehouse'))->editable('select', Warehouse::where('status', 1)->pluck('name', 'id')->toArray());
        $grid->column('status')->select([
            0 => 'No-Show',
            1 => 'Scheduled',
            2 => 'Completed',
        ]);
        $grid->column('created_at', __('Created At'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        });
        $grid->column('updated_at', __('Updated At'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        });

        $grid->model()->orderBy('time_slot', 'desc');
        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(2 / 3, function ($filter) {
                $filter->like('phone_number', 'Phone Number');
                $filter->like('container_number', 'Container Number');
                $filter->equal('warehouse_id', 'Warehouse')->select(Warehouse::where('status', 1)->pluck('name', 'id'));
                // 设置created_at字段的范围查询
                $filter->between('created_at', 'Created Time')->datetime();
            });
            // 范围过滤器，调用模型的`onlyTrashed`方法，查询出被软删除的数据。
            $filter->scope('trashed', '回收站')->onlyTrashed();

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
        }

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
        $show = new Show(Delivery::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('time_slot', __('Time Slot'));
        $show->field('completion', __('Completion Time'));
        $show->field('driver_name', __('Driver Name'));
        $show->field('phone_number', __('Phone Number'));
        $show->field('container_number', __('Container Number'));
        $show->field('warehouse.name', __('Warehouse'));
        $show->field('status', __('Status'))->as(function ($status) {
            return [
                0 => 'No-Show',
                1 => 'Scheduled',
                2 => 'Completed',
            ][strval($status)];
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
        $form = new Form(new Delivery);

        $form->display('id', __('ID'));
        $form->display('driver_name', __('Driver Name'));
        $form->display('phone_number', __('Phone Number'));
        $form->display('container_number', __('Container Number'));
        $form->select('warehouse_id', __('Warehouse'))->options(Warehouse::where('status', 1)->pluck('name', 'id')->toArray());
        $form->radioCard('status', __('Status'))->options([
            0 => 'No-Show',
            1 => 'Scheduled',
            2 => 'Completed',
        ]);
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        //同步更新 appointments 表的 status 字段
        $form->saved(function (Form $form) {
            $delivery = $form->model();

            DB::transaction(function () use ($delivery) {
                // 检查状态是否为“Completed”
                $delivery->completion_time = $delivery->status == 2 ? now() : null;
                $delivery->save();
                $updateData = [
                    'status' => $delivery->status,
                    'completion_time' => $delivery->completion_time,
                    'warehouse_id' => $delivery->warehouse_id,
                ];
                $delivery->appointment()->update($updateData);
            });
        });

        return $form;
    }

}

