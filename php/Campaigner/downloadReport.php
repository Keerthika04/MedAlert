<?php
require('../FPDF/fpdf.php');
require '../db_connection.php';

if (isset($_GET['eventId'])) {
    $eventId = $_GET['eventId'];

    $eventQuery = "
    SELECT e.eventid, 
           e.name, 
           e.date, 
           h.hospitalName, 
           h.email AS hospitalEmail, 
           h.contact AS hospitalContact
    FROM events e
    JOIN hospitals h ON e.hospitalId = h.hospitalId
    WHERE e.eventid = ?
";

    $stmtEvent = $db->prepare($eventQuery);
    $stmtEvent->bind_param('s', $eventId);
    $stmtEvent->execute();
    $eventResult = $stmtEvent->get_result();
    $event = $eventResult->fetch_assoc();
    $stmtEvent->close();

    $donationQuery = "
    SELECT d.bloodGroup, 
           COUNT(d.donorId) AS bloodGroupCount
    FROM blooddonationhistory bh
    JOIN donors d ON bh.donorId = d.Donorid
    WHERE bh.eventid = ?
    GROUP BY d.bloodGroup
";

    $stmtDonation = $db->prepare($donationQuery);
    $stmtDonation->bind_param('s', $eventId);
    $stmtDonation->execute();
    $donationResult = $stmtDonation->get_result();
    $donations = $donationResult->fetch_all(MYSQLI_ASSOC);
    $stmtDonation->close();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Event details
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(1);
    $pdf->Cell(0, 10, 'Event Report', 0, 1, 'L');
    $pdf->Ln(1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, ' Event Name: ' . htmlspecialchars($event['name']), 0, 1);
    $pdf->Cell(0, 10, ' Date: ' . htmlspecialchars($event['date']), 0, 1);
    $pdf->Cell(0, 10, ' Hospital: ' . htmlspecialchars($event['hospitalName']), 0, 1);
    $pdf->Cell(0, 10, ' Contact: ' . htmlspecialchars($event['hospitalContact']), 0, 1);
    $pdf->Cell(0, 10, ' Email: ' . htmlspecialchars($event['hospitalEmail']), 0, 1);
    $pdf->Ln(1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);

    // Blood donation details
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(1);
    $pdf->Cell(0, 10, 'Blood Donation Summary', 0, 1);
    $pdf->Ln(1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);

    $pdf->SetFont('Arial', '', 12);
    if (empty($donations)) {
        $pdf->Cell(0, 10, 'No donation history available.', 0, 1);
    } else {
        foreach ($donations as $donation) {
            $pdf->Cell(0, 10, htmlspecialchars($donation['bloodGroup']) . ': ' . htmlspecialchars($donation['bloodGroupCount']) . ' donors', 0, 1);
        }
    }

    $pdf->Output('D', 'Event_Report_' . $eventId . '.pdf');
} else {
    echo "Event ID is not provided!";
}
