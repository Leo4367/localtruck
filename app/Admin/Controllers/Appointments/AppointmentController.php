<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\Appointment;
use App\Models\Pickup;
use App\Models\Delivery;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;


class AppointmentController extends AdminController
{
    protected $title = "All Appointments";
    protected array $states = [
        'on' => ['value' => 1, 'text' => 'on', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => 'off', 'color' => 'danger'],
    ];

    //菜单主页list
    protected function grid()
    {
        $grid = new Grid(new Appointment());

        $grid->column('id', __('ID'))->sortable()->hide();
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
        $show = new Show(Appointment::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('time_slot', __('Time Slot'));
        $show->field('completion_time', __('Completion Time'));
        $show->field('driver_name', __('Driver Name'));
        $show->field('phone_number', __('Phone Number'));
        $show->field('pickup_number', __('Appt Number'));
        $show->field('type', __('Type'));
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
        $form = new Form(new Appointment);

        $form->display('id', __('ID'));
        $form->display('driver_name', __('Driver Name'));
        $form->display('phone_number', __('Phone Number'));
        $form->display('pickup_number', __('Appt Number'));
        $form->display('type', __('Type'))->with(function ($value) {
            $labels = [
                'Pickup' => 'info',
                'Delivery' => 'warning',
                'Other' => 'default',
            ];

            $label = $labels[$value] ?? 'default';
            return "<span class='label label-{$label}'>{$value}</span>";
        });
        $form->select('warehouse_id', __('Warehouse'))->options(Warehouse::where('status',1)->pluck('name', 'id')->toArray());
        $form->radioCard('status', __('Status'))->options([
            0 => 'No-Show',
            1 => 'Scheduled',
            2 => 'Completed',
        ]);
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        // 同步更新 pickups 和 deliveries 表的 status 字段
        $form->saved(function (Form $form) {
            // 获取当前保存的 Appointment 记录
            $appointment = $form->model();

            DB::transaction(function () use ($appointment) {
                // 如果状态是 Completed，更新 completion_time
                $completion_time = $appointment->status == 2 ? now() : null;

                // 更新主表（appointments）的 completion_time
                $appointment->completion_time = $completion_time;
                $appointment->save();  // 保存修改的 completion_time

                // 同步更新 Pickup 或 Delivery 表
                $updateData = [
                    'status' => $appointment->status,
                    'completion_time' => $completion_time,
                    'warehouse_id' => $appointment->warehouse_id,
                ];

                if ($appointment->type == 'Pickup') {
                    $appointment->pickup()->update($updateData);
                } elseif ($appointment->type == 'Delivery') {
                    $appointment->delivery()->update($updateData);
                }
            });
        });

        return $form;
    }

}
