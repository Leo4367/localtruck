<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\DateManage;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use http\Exception\InvalidArgumentException;
use Illuminate\Validation;
use Illuminate\Validation\ValidationException;

class DateManageController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Forbidden Day Manage';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DateManage());
        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('forbidden_date', __('Forbidden Date'))->label('danger');
        $grid->column('warehouse.name', __('Warehouse'))->label('danger');
        $grid->column('type', __('Type'))->label([
            'Pickup' => 'info',
            'Delivery' => 'warning',
        ]);
        $grid->column('status', __('Status'))
            ->using(['0' => 'off', '1' => 'on'])
            ->label(['0' => 'danger', '1' => 'success'])
            ->dot(['0' => 'danger', '1' => 'success']);
        $grid->column('created_at', __('Created at'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                $filter->between('forbidden_date', 'Forbidden Date')->datetime();
                $filter->equal('warehouse_id',__('Warehouse'))->select(Warehouse::where('status',1)->pluck('name','id'));
            });
        });

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
        $show = new Show(DateManage::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('forbidden_date', __('Forbidden Date'));
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
        $form = new Form(new DateManage);

        $form->column(2 / 5, function () use ($form) {
            $form->display('id', __('ID'));
            $form->date('forbidden_date', __('Forbidden'))->width('auto')->rules('required');
            // 显示仓库名称，保存仓库 ID
            $form->select('warehouse_id', __('Warehouse'))->rules('required')
                ->options(Warehouse::where('status', 1)->pluck('name', 'id')); // 使用 Warehouse 模型获取 id 和 name

            $form->select('type', __('Type'))->options([
                'Pickup' => 'Pickup',
                'Delivery' => 'Delivery',
            ]);
            $form->switch('status', __('Status'))->default(true);
            $form->display('created_at', __('Created At'));
            $form->display('updated_at', __('Updated At'));
        });

        // 添加保存前的验证逻辑
        $form->saving(function (Form $form) {
            // 检查数据库中是否已经存在相同的 forbidden_date 和 warehouse 记录
            $exists = DateManage::where('forbidden_date', $form->forbidden_date)
                ->where('warehouse_id', $form->warehouse_id)
                ->where('type', $form->type)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'forbidden_date' => $form->forbidden_date . ' already exists',
                ]);
            }
        });

        return $form;
    }
}
