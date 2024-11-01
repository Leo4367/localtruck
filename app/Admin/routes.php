<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers\Appointments\AppointmentController;
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
    $router->resource('/appointment/total', AppointmentController::class);
    $router->resource('/appointment/warehouse', WarehouseController::class);
    $router->resource('/appointment/pickups', PickupController::class);
    $router->resource('/appointment/deliveries', DeliveryController::class);
    $router->resource('/appointment/datemanage', DateManageController::class);
    $router->resource('/appointment/alltimeslots', AllTimeSlotsController::class);

});
