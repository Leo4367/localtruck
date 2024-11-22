<?php

namespace App\Admin\Controllers\InquiryPrice;


use App\Models\InquiryPrice;
use App\Models\SendEmail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InquiryPriceController extends AdminController
{

    protected $title = "Inquiry Price";

    protected function grid()
    {
        $grid = new Grid(new InquiryPrice());

        $grid->column('id', __('Id'))->hide();
        $grid->column('work.customer_name', __('Customer Name'));
        $grid->column('work.address', __('Address'));
        $grid->column('work.work_order', __('Work Order'));
        $grid->column('broker.company_name', __('Company Name'));
        $grid->column('broker.broker_name', __('Broker Name'));
        $grid->column('broker.email', __('Broker Email'));
        $grid->column('price', __('Price($)'))->decimal();
        $grid->column('created_at', __('Created at'));

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(InquiryPrice::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('work.customer_name', __('Customer Name'));
        $show->field('work.address', __('Address'));
        $show->field('work.work_order', __('Work Order'));
        $show->field('broker.company_name', __('Company Name'));
        $show->field('broker.broker_name', __('Broker Name'));
        $show->field('broker.email', __('Broker Email'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));

        return $show;

    }

    protected function form()
    {
        $form = new Form(new InquiryPrice());

        $form->display('id', __('ID'));
        $form->text('work.id', __('Customer ID'));
        $form->text('broker.id', __('Broker ID'));
        $form->decimal('price', __('Price'));

        return $form;
    }
}
