<?php
require('../FPDF/fpdf.php'); 
require '../db_connection.php';

$sql = "SELECT Donorid, firstName, lastName, NICnumber, weight, bloodGroup, donationDuration, dateOfBirth, gender, address, personalContact, emergencyContact, email, eligibilityStatus, username, password FROM donors";
$result = $db->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Donor Details',0,1,'L');
$pdf->Ln(1); 
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
$pdf->Ln(3); 

// Data rows as cards
$pdf->SetFont('Arial','',12);
while($row = $result->fetch_assoc()) { 

    $pdf->Cell(0,9,'Donor ID: ' . $row['Donorid'],0,1);
    $pdf->Cell(0,9,'Name: ' . $row['firstName'] . ' ' . $row['lastName'],0,1);
    $pdf->Cell(0,9,'NIC Number: ' . $row['NICnumber'],0,1);
    $pdf->Cell(0,9,'Weight: ' . $row['weight'] . ' kg',0,1);
    $pdf->Cell(0,9,'Blood Group: ' . $row['bloodGroup'],0,1);
    $pdf->Cell(0,9,'Donation Duration: ' . $row['donationDuration'] . ' months',0,1);
    $pdf->Cell(0,9,'Date of Birth: ' . $row['dateOfBirth'],0,1);
    $pdf->Cell(0,9,'Gender: ' . $row['gender'],0,1);
    $pdf->Cell(0,9,'Address: ' . $row['address'],0,1);
    $pdf->Cell(0,9,'Personal Contact: ' . $row['personalContact'],0,1);
    $pdf->Cell(0,9,'Emergency Contact: ' . $row['emergencyContact'],0,1);
    $pdf->Cell(0,9,'Email: ' . $row['email'],0,1);
    $pdf->Cell(0,9,'Eligibility Status: ' . ($row['eligibilityStatus'] == 1 ? 'Eligible' : 'Not Eligible'),0,1);
    $pdf->Ln(5); 
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
    $pdf->Ln(5); 
}


$db->close();

// Output PDF
$pdf->Output('D', 'donor_details.pdf'); // 'D' to force download

?>
