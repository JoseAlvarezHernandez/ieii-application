<?php 


class My_Utils_fpdf_mypdf extends My_Utils_fpdf_fpdf{
	private $titulo;
	private $logo;
	private $logoEmpresa = "logos/icon.png";
	public function __construct($titulo = "Resultados de Busqueda"){
		$this->titulo = $titulo;
	}
	public function titulo($titulo,$logo = ""){
		if($logo == ""){
			$this->logo = "logo1.png";
		}else{
			$this->logo = "logos/".$logo;
		}
		if($titulo == ""){
			$this->titulo="Resultados de Busqueda";
		}else{
			$this->titulo = $titulo;
		}				
	}
	// Page header
	function Header()
	{
	    // Logo de empresa contratista
	    $this->Image($this->logo,10,6,50);
	    //Logo 
	    $this->Image($this->logoEmpresa,230,8,70);
	    // Arial bold 15
	    $this->SetFont('Arial','B',17);
	    // Move to the right
	    $this->Cell(120);
	    // Title
	    $this->Cell(45,10,$this->titulo,0,0,'C');
	    setlocale(LC_TIME,"es_ES");
	    $date = new DateTime();	    
	    $this->Ln();
	    $this->Cell(450,40,"Fecha y hora :".$date->format('Y-m-d H:i:s'),200,100,'C');	    
	    // Line break	    
	    $this->Ln(5);
	}
	
	// Page footer
	function Footer()
	{
	    // Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Page number
	    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
	// Load data
	function LoadData($file)
	{
		// Read file lines
		$lines = file($file);
		$data = array();
		foreach($lines as $line)
			$data[] = explode(';',trim($line));
		return $data;
	}
	// Colored table
	function FancyTable($header, $data ,$w = array(20, 100, 40, 45, 45, 30)){
		// Colors, line width and bold font
		$this->SetFillColor(0,97,137);
		$this->SetTextColor(255);
		$this->SetDrawColor(0,153,255);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		// Header
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = false;
		foreach($data as $row){
			for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],6,utf8_decode($row[$i]),'LR',0,'L',$fill);
			$this->Ln();
			$fill = !$fill;
		}
		// Closing line
		$this->Cell(array_sum($w),0,'','T');
		}
}
?>