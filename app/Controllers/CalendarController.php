<?php

namespace App\Controllers;

use App\Models\Calendar;

class CalendarController extends BaseController
{
    private $calendar;

    public function __construct()
    {
        $this->calendar = new Calendar();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

        $institutionId = $_SESSION['user']['institution_id'];
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $today = date('Y-m-d');

        $date = \DateTime::createFromFormat('Y-m-d', "$year-$month-01");
        $events = $this->calendar->getEvents($institutionId);
        $todayEvents = $this->calendar->getEventsByDay($institutionId, $today);

        $this->render('calendar/index', [
            'events' => $events,
            'todayEvents' => $todayEvents,
            'currentDate' => $date,
            'today' => $today,
            'pageTitle' => "Calendário Escolar",
            'currentPage' => 'calendar',
        ]);
    }

    public function getDayEvents($date)
    {
        try {
            if (
                !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
            ) {
                throw new \Exception('Requisição inválida');
            }

            header('Content-Type: application/json; charset=utf-8');

            if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                throw new \Exception('Data inválida');
            }

            $institutionId = $_SESSION['user']['institution_id'];
            $events = $this->calendar->getEventsByDay($institutionId, $date);

            echo json_encode([
                'success' => true,
                'events' => $events ?? [],
                'date' => $date
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function store()
    {
        try {
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'date' => $_POST['date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            $this->calendar->create($data);
            $this->redirect('/calendar?success=1');
        } catch (\Exception $e) {
            $this->redirect('/calendar?error=' . urlencode($e->getMessage()));
        }
    }

    public function update($id)
    {
        try {
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'date' => $_POST['date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            $this->calendar->update($id, $data);
            $this->redirect('/calendar?success=1');
        } catch (\Exception $e) {
            $this->redirect('/calendar?error=' . urlencode($e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $this->calendar->delete($id, $_SESSION['user']['institution_id']);
            $this->redirect('/calendar?success=1');
        } catch (\Exception $e) {
            $this->redirect('/calendar?error=' . urlencode($e->getMessage()));
        }
    }
}
