<?php


namespace App\Admin\Controllers\InquiryPrice;


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
        $grid->column('customer_name', __('Customer Name'));
        $grid->column('address', __('Address'));
        $grid->column('work_order', __('Work Order'));

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
