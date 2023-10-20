<?php

namespace App\Listeners;

use App\Events\EmployeeCreatedEvent;
use App\Models\ManagerEmployee;

class EmployeeCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeCreatedEvent $event): void
    {
        ManagerEmployee::create([
            'employee_id' => $event->user_id,
            'manager_id' => $event->manager_id,
        ]);
    }
}
