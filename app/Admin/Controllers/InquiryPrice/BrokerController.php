<?php


namespace App\Admin\Controllers\InquiryPrice;


use App\Models\Broker;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrokerController extends AdminController
{
    protected $title = "Broker";

    protected function grid()
    {
        $grid = new Grid(new Broker());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('company_name', __('Company Name'));
        $grid->column('broker_name', __('Broker Name'));
        $grid->column('email', __('Email'));
        $grid->column('status', __('Send/Not'))->bool();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Broker::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('company_name', __('Company Name'));
        $show->field('broker_name', __('Broker Name'));
        $show->field('email', __('Email'));
        $show->field('status', __('Send/Not'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Broker());

        $form->display('id', __('ID'));
        $form->text('company_name', __('Company Name'));
        $form->text('broker_name', __('Broker Name'));
        $form->email('email', __('Email'));
        $form->radioCard('status', __('Send/Not'))->options([
            false => "Not Send",
            true => 'Send',
        ]);

        return $form;
    }
}
