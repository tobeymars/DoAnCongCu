<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/VenuesModel.php';
require_once __DIR__ . '/../Model/EventModel.php';
//Nguyễn Trương Tuấn Kiệt- 2180605351
class HomeController
{
    private $Venuemodel;
    private $Eventmodel;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->Venuemodel = new Venue($this->conn);
    }
    public function getAllVenues()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $venues = $this->Venuemodel->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($venues);
        }
    }
    public function getAllEvents()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $events = $this->Eventmodel->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($events);
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'getAllVenues') {
    $controller->getAllVenues();
}
else if ($_GET['action'] === 'getActiveEvents') {
    $controller->getAllEvents();
}