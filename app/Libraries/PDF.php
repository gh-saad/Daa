<?php

namespace App\Libraries;
require('fpdf.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Load data
    function LoadData($data)
    {
        return $data;
    }

    // Simple table
    function BasicTable($data)
    {
        foreach ($data as $key => $value) {
            $this->Cell(60, 10, $key, 1);
            $this->Cell(130, 10, $value, 1);
            $this->Ln();
        }
    }
}
