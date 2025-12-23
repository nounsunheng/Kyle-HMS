<?php

namespace App\Controllers\Doctor;

use App\Core\Controller;
use App\Services\ScheduleService;
use App\Services\ValidationService;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

class ScheduleController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private ValidationService $validator
    ) {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['d']))->handle();
    }

    public function index(): void
    {
        $schedules = $this->scheduleService->getDoctorSchedules(userId());

        $this->view('doctor.schedule', [
            'schedules' => $schedules
        ]);
    }

    public function create(): void
    {
        (new CsrfMiddleware())->handle();

        $data = request()->only([
            'title',
            'scheduledate',
            'scheduletime',
            'duration',
            'nop'
        ]);

        $errors = $this->validator->validateScheduleCreation($data);

        if ($errors) {
            back()->withErrors($errors)->withInput();
        }

        $result = $this->scheduleService->createSchedule([
            'docid' => userId(),
            ...$data
        ]);

        flash(
            $result['message'] ?? 'Schedule created successfully',
            $result['success'] ? 'success' : 'error'
        );

        redirect('/doctor/schedule.php');
    }

    public function delete(): void
    {
        (new CsrfMiddleware())->handle();

        $result = $this->scheduleService
            ->deleteSchedule(request('schedule_id'));

        flash(
            $result['success']
                ? 'Schedule deleted successfully'
                : $result['message'],
            $result['success'] ? 'success' : 'error'
        );

        redirect('/doctor/schedule.php');
    }
}
