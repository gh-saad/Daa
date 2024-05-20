<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\FPDF;
use App\Libraries\PDF;

class ReceiptController extends Controller
{
    public function generatePdf($paymentDetails)
    {
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Load data
        $data = $pdf->LoadData($paymentDetails);

        // Create a basic table
        $pdf->BasicTable($data);

        // Save the PDF to the desired location
        $fileName = 'receipt_' . time() . '.pdf';
        $filePath = public_path('uploads/receipts/' . $fileName);
        $pdf->Output('F', $filePath);

        return $filePath;
    }
}
