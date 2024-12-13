<?php

namespace App\Admin\Controllers\InquiryPrice;

use App\Models\SendEmail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SendEmailController extends AdminController
{
    protected $title = 'Send Email';

    protected function grid(){

        $grid = new Grid(new SendEmail());

        $grid->column('purchaser.customer_name', __('Customer Name'));
        $grid->column('purchaser.address', __('Address'));
        $grid->column('purchaser.work_order', __('Work Order'));
        $grid->column('broker.company_name', __('Company Name'));
        $grid->column('broker.broker_name', __('Broker Name'));
        $grid->column('broker.email', __('Broker Email'));
        $grid->column('status', __('Is Send'))->bool();
        $grid->column('created_at', __('Created At'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        });
        $grid->column('updated_at', __('Updated At'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        })->hide();

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->batchActions(function ($batch) {
            $batch->disableDelete(); // 禁用批量删除按钮
        });

        return $grid;


    }

}
