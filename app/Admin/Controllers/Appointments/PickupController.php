<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\Pickup;
use App\Models\VehicleType;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class PickupController extends AdminController
{
    protected $title = "Pickup";

    protected array $states = [
        'on' => ['value' => 1, 'text' => 'on', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => 'off', 'color' => 'danger'],
    ];

    protected function grid()
    {
        $grid = new Grid(new Pickup());

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('time_slot', __('Time Slot'))->sortable();
        $grid->column('completion_time', __('Completion Time'))->color('green')->sortable();
        $grid->column('driver_name', __('Company Name'))->sortable();
        $grid->column('phone_number', __('Phone Number'));
        $grid->column('po_number', __('PO#'))->editable();
        $grid->column('vehicle_type_id', __('Type'))->editable('select', VehicleType::where('status', 1)->pluck('name', 'id')->toArray());
        $grid->column('appt_number', __('Pickup Number'));
        $grid->column('dock_number', __('Dock#'))->sortable();
        $grid->column('warehouse_id', __('Warehouse'))->editable('select', Warehouse::where('status', 1)->pluck('name', 'id')->toArray())->sortable();
        $grid->column('status',__('Status'))->editable('select',[
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
                $filter->date('time_slot', 'Time Slot');
                $filter->like('phone_number', 'Phone Number');
                $filter->like('appt_number', 'Pickup Number');
                $filter->equal('warehouse_id', 'Warehouse')->select(Warehouse::where('status', 1)->pluck('name', 'id'));
                $filter->equal('vehicle_type_id', 'Type')->select(VehicleType::where('status', 1)->pluck('name', 'id'));
                // 设置created_at字段的范围查询
                $filter->between('created_at', 'Created Time')->datetime();
            });
            // 范围过滤器，调用模型的`onlyTrashed`方法，查询出被软删除的数据。
            //$filter->scope('trashed', '回收站')->onlyTrashed();
        });
        $grid->disableCreateButton();


        // 判断当前用户是否为 "View Only Role" 角色
        if (Admin::user()->isRole('view-only')) {
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->batchActions(function ($batch) {
                $batch->disableDelete(); // 禁用批量删除按钮
            });
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
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
        $show = new Show(Pickup::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('time_slot', __('Time Slot'));
        $show->field('completion_time', __('Completion Time'));
        $show->field('driver_name', __('Company Name'));
        $show->field('phone_number', __('Phone Number'));
        $show->field('po_number', __('PO#'));
        $show->field('vehicle_type_id', __('Type'));
        $show->field('appt_number', __('Pick Number'));
        $show->field('dock_number', __('Dock#'));
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
        $form = new Form(new Pickup);

        $form->display('id', __('ID'));
        $form->display('time_slot', __('Time Slot'));
        $form->display('completion_time', __('Completion Time'));
        $form->display('driver_name', __('Company Name'));
        $form->display('phone_number', __('Phone Number'));
        $form->text('po_number', __('PO#'));
        $form->select('vehicle_type_id', __('Type'))->options(VehicleType::where('status',1)->pluck('name', 'id')->toArray());
        $form->display('appt_number', __('Pick Number'));
        $form->display('dock_number', __('Dock#'));
        $form->select('warehouse_id', __('Warehouse'))->options(Warehouse::where('status',1)->pluck('name', 'id')->toArray());
        $form->radioCard('status', __('Status'))->options([
            0 => 'No-Show',
            1 => 'Scheduled',
            2 => 'Completed',
        ]);
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        // 同步更新completion_time 字段
        $form->saved(function (Form $form) {
            $pickup = $form->model();

            DB::transaction(function () use ($pickup) {
                // 检查状态是否为“Completed”
                $pickup->completion_time = $pickup->status == 2 ? now() : null;
                $pickup->save();
            });
        });

        return $form;
    }
}
