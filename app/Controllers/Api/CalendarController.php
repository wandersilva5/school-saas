<?php

namespace App\Controllers\Api;

use App\Models\Calendar;

class CalendarController extends ApiBaseController
{
    private $calendarModel;

    public function __construct()
    {
        $this->calendarModel = new Calendar();
        $this->handleCorsOptions();
    }

    public function getEvents()
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Optional date range parameters
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            
            // Validate date format if provided
            if ($startDate && !$this->validateDateFormat($startDate)) {
                return $this->errorResponse('Invalid start_date format. Use YYYY-MM-DD');
            }
            
            if ($endDate && !$this->validateDateFormat($endDate)) {
                return $this->errorResponse('Invalid end_date format. Use YYYY-MM-DD');
            }
            
            // Get events
            $events = $this->calendarModel->getEvents($institutionId, $startDate, $endDate);
            
            return $this->successResponse([
                'events' => $events
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving events: ' . $e->getMessage(), 500);
        }
    }

    public function getEventsByDay($date)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Validate date format
            if (!$this->validateDateFormat($date)) {
                return $this->errorResponse('Invalid date format. Use YYYY-MM-DD');
            }
            
            // Get events for the specified day
            $events = $this->calendarModel->getEventsByDay($institutionId, $date);
            
            return $this->successResponse([
                'events' => $events,
                'date' => $date
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving events: ' . $e->getMessage(), 500);
        }
    }

    public function getEvent($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get event details
            // This would typically come from a getEventById method, but we'll simulate it
            $events = $this->calendarModel->getEvents($institutionId);
            $event = null;
            
            foreach ($events as $e) {
                if ($e['id'] == $id) {
                    $event = $e;
                    break;
                }
            }
            
            if (!$event) {
                return $this->errorResponse('Event not found', 404);
            }
            
            return $this->successResponse([
                'event' => $event
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving event: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    private function validateDateFormat($date)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }
}