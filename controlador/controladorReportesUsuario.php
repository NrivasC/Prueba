<?php
	require("./FPDF/fpdf.php");
	require_once('../modelo/claseUsuario.php');

	require_once('../modelo/claseImgEncabezado.php');

	/*para fecha en español*/
	setlocale(LC_ALL,"spanish"); 
	
	//aqui guardamos la fecha, el mes y el año del sistema
	$fechaS = strftime('%d/%m/%G');

	//INICIO DEL PDFs

	




	//INICIO DEL PDFs


	class PDF extends FPDF {
        function Header(){

			//buscamos el id del ultimo encabezado
			$imgEncabezado = new imgEncabezado(null,null,null);

			$resultado = $imgEncabezado->consultarImagen();
	
			$ultimoId = pg_num_rows($resultado); //guardamos el id del encabezado

			//buscamos la ruta
				$imgEncabezado = new imgEncabezado($ultimoId,null,null);
				$rutaImg = $imgEncabezado->consultarId();
				$row = pg_fetch_array($rutaImg);
				//echo "ruta de la imagen: " . $row[2];
            
    
            $this->Cell(12);
            
            //put logos
            $this->Image("$row[2]",5,5,195);
            
            $this->Cell(100,10,'',0,1);
            
            //dummy cell to give line spacing
            //$this->Cell(0,5,'',0,1);
            //is equivalent to:
            $this->Ln(5);
            
		}


		function Footer(){


			$persona = $_GET['persona'];
			$persona = strtolower($persona);
			$persona = ucfirst($persona);
			
	

			$this->setY(-40);
			$this->SetFont('Arial','B',12);
			$this->Cell(80);
			$this->Cell(80,10,"Elaborado por");
			$this->Ln(5);
			$this->Cell(80);
			$this->Cell(80,10,"$persona");
			
			
		}
    
	}
	

	$pdf = new PDF('P','mm','A4'); //use new class
	$pdf->AddPage();
	$pdf->Ln(5); //salto de linea

	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(5);
	$pdf->Cell(5,10,"$fechaS"); // fecha automatica del dia que se esta generando el sistema
	$pdf->Ln(15);


	$pdf->Ln(25); //salto de linea
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(80);
	$pdf->Cell(80,10, utf8_decode("Reporte"));
	$pdf->Ln(8); //salto de linea

	$pdf->Cell(60);
	$pdf->Cell(60,10, utf8_decode("Información de Usuario"));
	$pdf->Ln(20); //salto de linea

	

	// Colores, ancho de línea y fuente en negrita

	$pdf->SetFillColor(1,55,102);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(128,0,0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('','');
    $pdf->Cell(20);
    $pdf->Cell(50,10,'NOMBRE',0,0,'C',1);
	$pdf->Cell(40,10, utf8_decode('CÉDULA'),0,0,'C',1);
	$pdf->Cell(50,10,'ROL',0,0,'C',1);
    $pdf->Ln(10); //salto de linea



	// Restauración de colores y fuentes
	$pdf->SetFillColor(224,235,255);
	$pdf->SetFont('Arial','',10);
	$pdf->SetTextColor(0);
	$pdf->SetFont('');
	
	$codigo = $_GET['codigo'];
	$opt = $_GET['opt'];

	if($opt == 1){

		$usuario= new usuario(null,null,$codigo,null,null); // instanciamos
		$result=$usuario->reporteEspecifico();// hacemos el llamado al método
		
		$pdf->Cell(20);
		$pdf->Cell(50,10,$result[0],0,0,'C',1);
    	$pdf->Cell(40,10,$result[2],0,0,'C',1);
    	$pdf->Cell(50,10,$result[5],0,0,'C',1);

	}else{

		$usuario= new usuario(null,null,null,null,null);// instanciamos
		$result=$usuario->reporteGeneral();// hacemos el llamado al método

		if(pg_num_rows($result)>0){
        	while($row=pg_fetch_array($result)){
        		$pdf->Cell(20);
				$pdf->Cell(50,10,$row[0],0,0,'C',1);
    			$pdf->Cell(40,10,$row[2],0,0,'C',1);
    			$pdf->Cell(50,10,$row[5],0,0,'C',1);
    			$pdf->Ln(11);	
        	}
		}

	}

	$pdf->Output(); // cerramos la clase pdf

?>