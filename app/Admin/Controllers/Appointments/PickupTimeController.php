<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\PickupTime;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Validation\ValidationException;

class PickupTimeController extends AdminController
{

    protected $title = 'Pickup Time';

    protected function grid()
    {
        $grid = new Grid(new PickupTime());
        $grid->column('id', __('Id'));
        $grid->column('time', __('Time'));
        $grid->column('status', __('Status'))->bool();
        $grid->column('created_at', __('Created at'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
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

    protected function detail($id)
    {
        $show = new Show(PickupTime::findOrFail($id));

        $show->field('di', __('ID'));
        $show->field('time', __('Time'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new PickupTime());

        $form->time('time', 'Pickup Time')->required();
        $form->switch('status', 'Status')->default(true);

        $form->saving(function (Form $form) {
            if(PickupTime::where('time', $form->time)->exists()){
                throw ValidationException::withMessages([
                    'time' => $form->time . ' already exists',
                ]);
            }
        });

        return $form;
    }
}
