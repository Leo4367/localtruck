<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Traits\Date;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->{$this->argument('name')}();
    }

    protected function time_zone(){
        $appointment = Appointment::first();
        // 在控制器或模型中，格式化时间为应用的时区
        return $appointment->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }
}
