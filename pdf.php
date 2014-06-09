<?php

//
// http://fpdf.org/
// http://fpdf.org/en/tutorial/index.php
// http://fpdf.org/en/tutorial/tuto3.htm
// http://answers.oreilly.com/topic/1414-how-to-generate-a-pdf-with-php/
//

require('lib/fpdf/fpdf.php');

class PDF extends FPDF
{
function Header()
{
    global $title;

    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Calculate width of title and position
    $w = $this->GetStringWidth($title)+6;
    $this->SetX((210-$w)/2);
    // Colors of frame, background and text
    $this->SetDrawColor(0,80,180);
    $this->SetFillColor(230,230,0);
    $this->SetTextColor(220,50,50);
    // Thickness of frame (1 mm)
    $this->SetLineWidth(1);
    // Title
    $this->Cell($w,9,$title,1,1,'C',true);
    // Line break
    $this->Ln(10);
}

function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Text color in gray
    $this->SetTextColor(128);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}

function ChapterTitle($num, $label)
{
    // Arial 12
    $this->SetFont('Arial','',12);
    // Background color
    $this->SetFillColor(200,220,255);
    // Title
    $this->Cell(0,6,"Chapter $num : $label",0,1,'L',true);
    // Line break
    $this->Ln(4);
}

function ChapterBody($file)
{
    // Read text file
    $txt = file_get_contents($file);
    // Times 12
    $this->SetFont('Times','',12);
    // Output justified text
    $this->MultiCell(0,5,$txt);
    // Line break
    $this->Ln();
    // Mention in italics
    //$this->SetFont('','I');
    //$this->Cell(0,5,'(end of excerpt)');
}

function PrintChapter($num, $title, $file)
{
    $this->AddPage();
    $this->ChapterTitle($num,$title);
    $this->ChapterBody($file);
}
}

//writeDemo();
//writePDF1();
writePDF2();


function writeDemo() {
    global $title;

    $pdf = new PDF();
    $title = '20000 Leagues Under the Seas';
    $pdf->SetTitle($title);
    $pdf->SetAuthor('Jules Verne');
    $pdf->PrintChapter(1,'A RUNAWAY REEF','posts/note_php.txt');
    //$pdf->PrintChapter(2,'THE PROS AND CONS','20k_c2.txt');
    $pdf->Output();
}


function writePDF2() {
    global $title;

    $f = $_REQUEST['f'];
    if ($f == "") {
        print "(no file specified)";
        return;
    }

    $path = "posts/$f";

    $pdf = new PDF();
    $title = strtoupper($f); 
    //$title = '20000 Leagues Under the Seas';
    $pdf->SetTitle($title);
    $pdf->SetAuthor($_SESSION['username']);
    $pdf->PrintChapter(1, $title, $path);
    $pdf->Output();
}


function writePDF1($s) {
    $f = $_REQUEST['f'];
    if ($f == "") {
        $s = "(no file specified)";
    } else {
        $s = file_get_contents("posts/$f");
    }

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',11);

    $pdf->MultiCell(0,5,$s); // http://fpdf.org/en/doc/multicell.htm

    //$pdf->Cell(40,10,'Hello World!');
    $pdf->Output();
}


?>

