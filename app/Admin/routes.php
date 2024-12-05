<?php

use App\Admin\Controllers\Appointments\DeliveryTimeController;
use App\Admin\Controllers\Appointments\PickupTimeController;
use App\Admin\Controllers\InquiryPrice\InquiryPriceController;
use App\Admin\Controllers\InquiryPrice\PurchaserController;
use App\Admin\Controllers\InquiryPrice\SendEmailController;
use App\Admin\Controllers\InquiryPrice\BrokerController;
use Illuminate\Routing\Router;

use App\Admin\Controllers\Appointments\WarehouseController;
use App\Admin\Controllers\Appointments\AllTimeSlotsController;
use App\Admin\Controllers\Appointments\DateManageController;
use App\Admin\Controllers\Appointments\DeliveryController;
use App\Admin\Controllers\Appointments\PickupController;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    //$router->resource('/appointment/total', AppointmentController::class);
    $router->resource('/appointment/warehouse', WarehouseController::class);
    $router->resource('/appointment/pickups', PickupController::class);
    $router->resource('/appointment/deliveries', DeliveryController::class);
    $router->resource('/appointment/datemanage', DateManageController::class);
    $router->resource('/appointment/alltimeslots', AllTimeSlotsController::class);
    $router->resource('/appointment/pickup-times', PickupTimeController::class);
    $router->resource('/appointment/delivery-times', DeliveryTimeController::class);
    $router->resource('/inquiryprice/send-email', SendEmailController::class);
    $router->resource('/inquiryprice/inquiry-price', InquiryPriceController::class);
    $router->resource('/inquiryprice/purchaser', PurchaserController::class);
    $router->resource('/inquiryprice/broker', BrokerController::class);

});
