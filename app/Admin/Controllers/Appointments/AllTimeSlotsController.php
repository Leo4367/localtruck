<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\AllTimeSlots;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Validation\ValidationException;

class AllTimeSlotsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Add Weekend Time Slots';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AllTimeSlots());
        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('date_slot', __('Weekend Date'));
        $grid->column('time_slot', __('Add Time'))->display(function () {
            return date('H:i', strtotime($this->time_slot));
        })->label('default');
        $grid->column('warehouse.name', __('Warehouse'))->label('danger');
        $grid->column('type', __('Type'))->label(['Pickup' => 'info', 'Delivery' => 'warning']);
        $grid->column('status', __('Status'))
            ->using(['0' => 'off', '1' => 'on'])
            ->label(['0' => 'danger', '1' => 'success'])
            ->dot(['0' => 'danger', '1' => 'success']);
        $grid->column('created_at', __('Created at'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        });
        $grid->column('updated_at', __('Updated at'))->display(function (){
            return $this->updated_at->format('Y-m-d H:i:s');
        })->hide();

        $grid->model()->orderBy('date_slot', 'desc');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->between('forbidden_date', 'Forbidden Date')->datetime();
            $filter->column(2 / 3, function ($filter) {
                $filter->equal('warehouse_id', 'Warehouse')->select(Warehouse::where('status', 1)->pluck('name', 'id'));
            });

        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
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
        $show = new Show(AllTimeSlots::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('date_slot', __('Weekend Date'));
        $show->field('time_slot', __('Add Time'));
        $show->field('warehouse.name', __('Warehouse'));
        $show->field('type', __('Type'));
        $show->field('status', __('Status'))
            ->using(['0' => 'off', '1' => 'on']);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $minutes = range(0, 59, 30);
        $form = new Form(new AllTimeSlots);

        $form->display('id', __('ID'));
        $form->date('date_slot', __('Weekend'))->options([
            'daysOfWeekDisabled' => [1, 2, 3, 4, 5],
            'format' => 'yyyy-mm-dd',
        ])->width('auto')->rules('required');
        //$form->date('time_slot', __('Add Time'))->format('HH:mm');
        //$form->time('time_slot', __('Add Time'))->format('HH:mm');
        $form->time('time_slot', __('Add Time'))->format('HH:mm')->width('auto')->rules('required');
        $form->column(2 / 5, function () use ($form) {
            $form->select('warehouse_id', __('Warehouse'))->options(Warehouse::where('status', 1)->pluck('name', 'id'))->rules('required');
            $form->select('type', __('Type'))->options(['Pickup' => 'Pickup', 'Delivery' => 'Delivery',])->rules('required');
        });
        $form->switch('status', __('Status'))->default(true);
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        // 在保存前进行拼接处理
        $form->saving(function ($form) {
            if ($form->date_slot && $form->time_slot) {
                // 将日期和时间拼接成完整的 datetime 字符串
                $form->time_slot = $form->date_slot . ' ' . $form->time_slot . ':00';

                // 检查数据库中是否已经存在相同的 forbidden_date 和 warehouse 记录
                $exists = AllTimeSlots::where('time_slot', $form->time_slot)
                    ->where('warehouse_id', $form->warehouse_id)
                    ->where('type', $form->type)
                    ->exists();

                if (!$form->model()->exists && $exists) {
                    throw ValidationException::withMessages([
                        'date_slot' => $form->time_slot . '-' . $form->warehouse . '-' . $form->type . ' already exists',
                    ]);
                }
            }
        });
        return $form;
    }
}
