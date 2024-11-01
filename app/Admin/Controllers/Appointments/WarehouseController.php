<?php

namespace App\Admin\Controllers\Appointments;

use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WarehouseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Warehouse';

    protected function grid(){
        $grid = new Grid(new Warehouse());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('address', __('Address'));
        $grid->column('description', __('Description'))->hide();
        $grid->column('phone', __('Phone'))->hide();
        $grid->column('status', __('Active or not'))->bool();
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->filter(function($filter){
            $filter->column(1/2,function ($filter){
                $filter->like('name', 'Name');
                $filter->equal('status', 'Status')->select(['1' => 'Active', '0' => 'Inactive']);
            });
        });

        return $grid;
    }
    protected function detail($id){
        $show = new Show(Warehouse::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('address', __('Address'));
        $show->field('description', __('Description'));
        $show->field('phone', __('Phone'));
        $show->field('status', __('Active or not'))->as(function ($status) {
            return $status ? 'on' : 'off';
        });
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    protected function form(){
        $form = new Form(new Warehouse());

        $form->display('id', __('ID'));
        $form->text('name', __('Name'))->rules('required');
        $form->text('address', __('Address'))->rules('required');
        $form->textarea('description', __('Description'));
        $form->text('phone', __('Phone'));
        $form->switch('status', __('Active or not'))->default(1);

        return $form;
    }

}
