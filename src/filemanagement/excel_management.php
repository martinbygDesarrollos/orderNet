<?php
include_once "../src/config.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

// require_once URL_UTILS . 'PHPExcel/Classes/PHPExcel.php';
// define("BPS", 2); // Fijar el numero accediendo a la BD [es el ID del debito BPS] (Esto es una facilidad simplemente)
// define("MASTER", 3); // Fijar el numero accediendo a la BD [es el ID del debito BPS] (Esto es una facilidad simplemente)
// define("BPS_PATH", '/bps/'); 

class excel_management{

	public function exportOrder($provider, $articles){
		date_default_timezone_set('America/Montevideo');
		if(!is_dir(URL_EXPORTS)){
			mkdir(URL_EXPORTS, 0777, true);
		}
		$fileName = trim($provider['nombre']) . "_" . date('Ymdhis');
		// echo " AAAAAAAAAAAAAAAAAAAAAAAa AAAAAAAAAAAAAAAAAAAAAAAa AAAAAAAAAAAAAAAAAAAAAAAa AAAAAAAAAAAAAAAAAAAAAAAa AAAAAAAAAAAAAAAAAAAAAAAa" . $fileName;
		// var_dump($provider);
		// var_dump($articles);
		// exit;
		
		$response = new \stdClass();
		$spreadsheet = new Spreadsheet();
		$excelSheet = $spreadsheet->getActiveSheet();
		$excelSheet->setTitle($provider['nombre']);
		
		$excelSheet->setCellValue('A1', "Articulo")->getStyle('A1')->getFont()->setBold(true);
		$excelSheet->setCellValue('B1', "Codigo")->getStyle('B1')->getFont()->setBold(true);
		$excelSheet->setCellValue('C1', "Cantidad")->getStyle('C1')->getFont()->setBold(true);
		
		$indexRow = 2;
		foreach ($articles as $article) {
			$excelSheet->setCellValue(('A'.$indexRow), $article['nombre']);
			$excelSheet->setCellValue(('B'.$indexRow), $article['codigo']);
			$excelSheet->setCellValue(('C'.$indexRow), $article['cantidad']);
			$indexRow++;
		}
		
		$excelSheet->getColumnDimension('A')->setAutoSize(true);
		$excelSheet->getColumnDimension('B')->setAutoSize(true);
		$excelSheet->getColumnDimension('C')->setAutoSize(true);
		$excelSheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$excelSheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$writer = new Xlsx($spreadsheet);
		$writer->save(URL_EXPORTS . "/" . $fileName . ".xlsx");
		
		$response->result = 2;
		$response->name = $fileName;
		$response->finalName = trim($provider['nombre']) . ".xlsx";

		$response->format = 'xlsx';
		$response->path = URL_EXPORTS . "/" . trim($fileName) . ".xlsx";
		return $response;
	}
	
	// public function exportExcelCobrosMASTER($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	// - tarjeta (tarjeta)
	// 	// - nombre (nombre) NULL
	// 	// - importe (importe)
	// 	// - socio (ci) UNIQUE
	// 	// - sucursal (departamento)
	// 	if($idDebito == MASTER){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/master"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/master", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE");
	// 	$excelSheet->setCellValue('B2', "CI");
	// 	$excelSheet->setCellValue('C2', "CONTRATO");
	// 	$excelSheet->setCellValue('D2', "IMPORTE");
	// 	$excelSheet->setCellValue('E2', "TARJETA");
	// 	$excelSheet->setCellValue('F2', "SUCURSAL");

	// 	$excelSheet->getStyle('E')->getNumberFormat()->setFormatCode('@');
		
	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombre']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ci']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['departamento']);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('G1', 'Monto total');
	// 	$excelSheet->setCellValue('H1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:F1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	$excelSheet->getStyle('A2:F2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . MASTER_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . MASTER_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . MASTER_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosCABAL($nombreArchivo, $clients, $empresa, $idDebito, $fechaActual){ 
	// 	if($idDebito == CABAL){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/cabal"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/cabal", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
    //     $empresaController = new ctr_empresa();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "Cuenta ó Tarjeta")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('B1', "Doc Cuenta")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('C1', "Doc Cliente")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('D1', "Socio")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('E1', "Importe")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('F1', "Cuota")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('G1', "Nro. Cupon")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('H1', "Aplica Devolucion IVA (1 = Si / 0 = No)")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('I1', "Número Factura")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('J1', "Importe Tasa Iva Básico")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('K1', "Importe Tasa Iva Mínimo")->getStyle('A1')->getFont();
	// 	$excelSheet->setCellValue('L1', "Importe Exento")->getStyle('A1')->getFont();
		
	// 	$excelSheet->getStyle('A')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('B')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('C')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('D')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('F')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('G')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('H')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('I')->getNumberFormat()->setFormatCode('@');
	// 	// $excelSheet->getStyle('E')->getNumberFormat()->setFormatCode('@');
	// 	$excelSheet->getStyle('E')->getNumberFormat()->setFormatCode('0.00');
	// 	$excelSheet->getStyle('J')->getNumberFormat()->setFormatCode('0.00');
		
	// 	// $excelSheet->getStyle('J')->getNumberFormat()->setFormatCode('');
	// 	$excelSheet->getStyle('K')->getNumberFormat()->setFormatCode('');
	// 	$excelSheet->getStyle('L')->getNumberFormat()->setFormatCode('');
		
	// 	$indexRow = 2;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		// $excelSheet->getStyle('A'.$indexRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ci']);
	// 		if($empresa->id == 2){ // GRANTAR 
	// 			$excelSheet->setCellValue(('C'.$indexRow), $client['numContrato']);
	// 		} else {
	// 			$excelSheet->setCellValue(('C'.$indexRow), $client['ciSocio']);
	// 		}
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('F'.$indexRow), $fechaActual);
	// 		$excelSheet->setCellValue(('G'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('H'.$indexRow), "1");
	// 		$responseQueryFactura = $empresaController->getFacturaId($empresa->id);
	// 		$excelSheet->setCellValue(('I'.$indexRow), $responseQueryFactura->factura);
	// 		// $IVA = intval($client['importe']) / (1.22);
	// 		// $IVA = round($IVA, 2);
	// 		// $IVA = sprintf('%.2f', $IVA);
	// 		// $IVA .= " "; 
	// 		$IVA = "=+E$indexRow/1.22"; 
	// 		// $IVA = number_format($IVA, 2, '.', '');
	// 		$excelSheet->setCellValue(('J'.$indexRow), $IVA);
	// 		$indexRow++;
	// 	}
	// 	// $excelSheet->setCellValue(('E'.$indexRow),  number_format($monto_total, 2, '.', ''));
	// 	// $excelSheet->setCellValue(('E'.$indexRow),  $monto_total);
		
	// 	// $excelSheet->setCellValue('K1', 'Monto total');
	// 	// $excelSheet->setCellValue('L1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('K')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('L')->setAutoSize(true);

	// 	// $excelSheet->mergeCells('A1:C1');
	// 	// $excelSheet->mergeCells('D1:F1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	// $excelSheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('A1:L1')->getFont()->setBold(true);
		
	// 	// $writer = new Xlsx($spreadsheet);
	// 	// $writer = new Xls($spreadsheet);
	// 	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	// 	$writer->save("exports/" . $empresa->id . CABAL_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . CABAL_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xls';
	// 	$response->path = URL_EXPORTS . $empresa->id . CABAL_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosCLUBDELESTE($nombreArchivo, $clients, $empresa, $idDebito){ 
		
	// 	if($idDebito == CLUBDELESTE){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/club"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/club", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE");
	// 	$excelSheet->setCellValue('B2', "CI");
	// 	$excelSheet->setCellValue('C2', "CONTRATO");
	// 	$excelSheet->setCellValue('D1', "CUENTA ASOCIADA")->getStyle('D1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('D2', "IMPORTE");
	// 	$excelSheet->setCellValue('E2', "TARJETA");
	// 	$excelSheet->setCellValue('F2', "NOMBRE");
	// 	$excelSheet->setCellValue('G2', "CI");
	// 	$excelSheet->setCellValue('H2', "SUCURSAL");

	// 	$excelSheet->getStyle('E')->getNumberFormat()->setFormatCode('@');
		
	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		if(isset($client['promo'])) {// descuento 
    //             $client['importe'] = number_format((floatval($client['importe']) - floatval($client['importe']) * (intval($client['promo']) / 100)), 2, '.', '');
    //         }

	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['nombre']);
	// 		$excelSheet->setCellValue(('G'.$indexRow), $client['ci']);
	// 		$excelSheet->setCellValue(('H'.$indexRow), $client['departamento']);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('I1', 'Monto total');
	// 	$excelSheet->setCellValue('J1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:C1');
	// 	$excelSheet->mergeCells('D1:G1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	$excelSheet->getStyle('A2:H2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . CLUBDELESTE_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . CLUBDELESTE_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . CLUBDELESTE_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosVISA($nombreArchivo, $clients, $empresa, $fechaLote, $fechaTransaccion, $fechaPeriodo, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();

	// 	if($idDebito == VISA){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/visa"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/visa", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE");
	// 	$excelSheet->setCellValue('B2', "CI");
	// 	$excelSheet->setCellValue('C2', "IMPORTE");
	// 	$excelSheet->setCellValue('D2', "TARJETA");
	// 	$excelSheet->setCellValue('E2', "NUMERO");
	// 	$excelSheet->setCellValue('F2', "VENCIMIENTO TARJETA");
	// 	$excelSheet->setCellValue('G2', "SUCURSAL");
		
	// 	$excelSheet->getStyle('D')->getNumberFormat()->setFormatCode('@');

	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['numContrato']);
	// 		// $excelSheet->setCellValue(('F'.$indexRow), $handleDateTimeClass->setFormatMMAAFromBD($client['vencimientoTarjeta']));
	// 		$excelSheet->setCellValue(('F'.$indexRow), $handleDateTimeClass->setFormatBarDateAAAAMM($client['vencimientoTarjeta']));
	// 		$excelSheet->setCellValue(('G'.$indexRow), $client['departamento']);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('H1', 'Monto total');
	// 	$excelSheet->setCellValue('I1', $monto_total);

	// 	$excelSheet->setCellValue('J1', 'Fecha de lote');
	// 	$excelSheet->setCellValue('K1', $fechaLote);
		
	// 	$excelSheet->setCellValue('L1', 'Fecha de transaccion');
	// 	$excelSheet->setCellValue('M1', $fechaTransaccion);

	// 	$excelSheet->setCellValue('N1', 'Fecha periodo');
	// 	$excelSheet->setCellValue('O1', $fechaPeriodo);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('K')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('L')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('M')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('N')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('O')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:G1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A2:G2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . VISA_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . VISA_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . VISA_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosCREDITEL($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();

	// 	if($idDebito == CREDITEL){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/creditel"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/creditel", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE");
	// 	$excelSheet->setCellValue('B2', "CI");
	// 	$excelSheet->setCellValue('C2', "IMPORTE");
	// 	$excelSheet->setCellValue('D2', "TARJETA");
	// 	$excelSheet->setCellValue('E2', "NUMERO");
	// 	$excelSheet->setCellValue('F2', "LOCALIDAD");

	// 	$excelSheet->getStyle('D')->getNumberFormat()->setFormatCode('@');
		
	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['numContrato']);
	// 		// $excelSheet->setCellValue(('F'.$indexRow), $handleDateTimeClass->setFormatMMAAFromBD($client['vencimientoTarjeta']));
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['departamento']);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('G1', 'Monto total');
	// 	$excelSheet->setCellValue('H1', $monto_total);

	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	// $excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setWidth(9);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:F1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A2:F2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . CREDITEL_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . CREDITEL_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . CREDITEL_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosOCA($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();
	// 	// - ciSocio
	// 	// - nombreSocio
	// 	// - ci 
	// 	// - nombre
	// 	// - importe
	// 	// - numContrato
	// 	// - tarjeta
	// 	// - direccion
	// 	// - telefono


	// 	if($idDebito == OCA){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/oca"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/oca", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE"); // - nombreSocio
	// 	$excelSheet->setCellValue('B2', "CI"); // - ciSocio
	// 	$excelSheet->setCellValue('C2', "DIRECCION"); // - direccion
	// 	$excelSheet->setCellValue('D2', "TELEFONO"); // - telefono


	// 	$excelSheet->setCellValue('E1', "CUENTA ASOCIADA")->getStyle('E1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('E2', "NOMBRE"); // - nombre
	// 	$excelSheet->setCellValue('F2', "CI"); // - ci 
	// 	$excelSheet->setCellValue('G2', "IMPORTE"); // - importe
	// 	$excelSheet->setCellValue('H2', "NUMERO"); // - numContrato
	// 	$excelSheet->setCellValue('I2', "TARJETA"); // - tarjeta
		
	// 	$excelSheet->getStyle('I')->getNumberFormat()->setFormatCode('@');

	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['direccion']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['telefono']);
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['nombre']);
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['ci']);
	// 		$excelSheet->setCellValue(('G'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('H'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('I'.$indexRow), $client['tarjeta'] . " " );
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('J1', 'Monto total');
	// 	$excelSheet->setCellValue('K1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('K')->setAutoSize(true);

	// 	$excelSheet->getColumnDimension('L')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('M')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('N')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('O')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:D1');
	// 	$excelSheet->mergeCells('E1:I1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	$excelSheet->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	// $excelSheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A2:I2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . OCA_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . OCA_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . OCA_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosCREDITOSDIRECTOS($nombreArchivo, $clients, $empresa, $idDebito, $fechaProduccion){ 
	// 	$handleDateTimeClass = new handleDateTime();
	// 	// - ciSocio
	// 	// - nombreSocio
	// 	// - ci 
	// 	// - nombre
	// 	// - importe
	// 	// - numContrato
	// 	// - tarjeta
	// 	// - direccion
	// 	// - telefono
	// 	// exportExcelCobrosCREDITOSDIRECTOS($nombreArchivo, $responseGetClientList->listResult, $responseInfoEmpresa->empresa, $idDebito, $fechaProduccion)

	// 	if($idDebito == CREDITOSDIRECTOS){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/creditos"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/creditos", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE"); // - nombreSocio
	// 	$excelSheet->setCellValue('B2', "CI"); // - ciSocio
	// 	$excelSheet->setCellValue('C2', "NUMERO CONTRATO"); // - numContrato
	// 	$excelSheet->setCellValue('D2', "INTEGRANTES"); // - integrantes
	// 	$excelSheet->setCellValue('E2', "IMPORTE"); // - importe
	// 	$excelSheet->setCellValue('F2', "DEPARTAMENTO"); // - departamento

	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['integrantes']);
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['departamento']);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('G1', 'Monto total');
	// 	$excelSheet->setCellValue('H1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:F1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	$excelSheet->getStyle('A2:F2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . CREDITOSDIRECTOS_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . CREDITOSDIRECTOS_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . CREDITOSDIRECTOS_PATH . $nombreArchivo;
	// 	return $response;
	// }

	// public function exportExcelCobrosPASSCARD($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();
	// 	// - ciSocio
	// 	// - nombreSocio
	// 	// - importe
	// 	// - numContrato = Numero Socio
	// 	// - tarjeta
	// 	// - tarjeta vencimiento
	// 	// - IVA
	// 	// - departamento

	// 	if($idDebito == PASSCARD){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/pass"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/pass", 0777, true);
	// 	}
	// 	$response = new \stdClass();
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($nombreArchivo);
		
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "INFORMACION SOCIO")->getStyle('A1')->getFont()->setBold(true);
	// 	$excelSheet->setCellValue('A2', "NOMBRE"); // - nombreSocio
	// 	$excelSheet->setCellValue('B2', "CI"); // - ciSocio
	// 	$excelSheet->setCellValue('C2', "NUMERO SOCIO"); // - numContrato
	// 	$excelSheet->setCellValue('D2', "TARJETA"); // - tarjeta
	// 	$excelSheet->setCellValue('E2', "VTO TARJETA"); // - vencimientoTarjeta
	// 	$excelSheet->setCellValue('F2', "IMPORTE"); // - importe
	// 	$excelSheet->setCellValue('G2', "DEPARTAMENTO"); // - departamento
	// 	$excelSheet->setCellValue('H2', "Imp. IVA Básico"); // - IVA

	// 	$excelSheet->getStyle('D')->getNumberFormat()->setFormatCode('@');

	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
	// 		$IVA = intval($client['importe']) / (1.22) * (0.22);
	// 		$IVA = number_format($IVA, 2, '.', ''); // Para corregir numeros despues de la coma | solo quiero 2
	// 		// $IVA = intval($IVA * 100);
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['nombreSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['numContrato']);
	// 		$excelSheet->setCellValue(('D'.$indexRow), $client['tarjeta'] . " " );
	// 		$excelSheet->setCellValue(('E'.$indexRow), $handleDateTimeClass->setFormatBarDate($client['vencimientoTarjeta']));
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['importe']);
	// 		$excelSheet->setCellValue(('G'.$indexRow), $client['departamento']);
	// 		$excelSheet->setCellValue(('H'.$indexRow), $IVA);
	// 		$indexRow++;
	// 	}
		
	// 	$excelSheet->setCellValue('I1', 'Monto total');
	// 	$excelSheet->setCellValue('J1', $monto_total);
		
	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);

	// 	$excelSheet->mergeCells('A1:H1');

	// 	// Center the text horizontally in the merged cell
	// 	// $excelSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);		
	// 	$excelSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	// 	$excelSheet->getStyle('A2:H2')->getFont()->setBold(true);
		
	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . PASSCARD_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . PASSCARD_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;


	// 	$response->finalName = $nombreArchivo;
	// 	// echo $empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS';
	// 	// exit;

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . PASSCARD_PATH . $nombreArchivo;
	// 	return $response;
	// }
	
	// public function exportExcelDetails($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();
	// 	if($idDebito == BPS){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/bps"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/bps", 0777, true);
	// 	}
		
	// 	$response = new \stdClass();
		
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS');
		
		
	// 	$numberFormat = new NumberFormat();
	// 	$rut_format = $numberFormat::FORMAT_NUMBER;
	// 	$price_format = $numberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
		
		
	// 	$anio_mes = $handleDateTimeClass->getCurrentDateAAAAMM();
	// 	// $anio_mes = '202309';
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A1', "DOCUMENTO");
	// 	$excelSheet->setCellValue('B1', "NOMBRE");
	// 	$excelSheet->setCellValue('C1', 'CONTRATO');
	// 	$excelSheet->setCellValue('D1', 'DEPARTAMENTO');
	// 	$excelSheet->setCellValue('E1', 'IMPORTE');

	// 	// $excelSheet->setCellValue('F1', 'Cantidad de clientes');
	// 	// $excelSheet->setCellValue('G1', count($clients));

		
		
	// 	$indexRow = 2;
	// 	$blink = true;
	// 	foreach ($clients as $client) {
	// 		$excelSheet->setCellValue(('A'.$indexRow), $client['ciSocio']);
	// 		$excelSheet->setCellValue(('B'.$indexRow), $client['nombreSocio']);
	// 		if($blink)
	// 			$excelSheet->getStyle('A'.$indexRow.':E'.$indexRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
	// 		else
	// 			$excelSheet->getStyle('A'.$indexRow.':E'.$indexRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');

	// 		$importe_parts = explode(',', $client['contrato_importe']);
	// 		$departamento_parts = explode(',', $client['contrato_departamento']);
	// 		$contrato_parts = explode(',', $client['contrato_numContrato']);
			
	// 		$indexRow++;
	// 		$count = count($importe_parts);
	// 		for ($i = 0; $i < $count; $i++) {
	// 			$monto_total += floatval($importe_parts[$i]);
	// 			$excelSheet->setCellValue(('C'.$indexRow), $contrato_parts[$i]);
	// 			$excelSheet->setCellValue(('D'.$indexRow), $departamento_parts[$i]);
	// 			$excelSheet->setCellValue(('E'.$indexRow), $importe_parts[$i]);
	// 			if($blink)
	// 				$excelSheet->getStyle('C'.$indexRow.':E'.$indexRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
	// 			else
	// 				$excelSheet->getStyle('C'.$indexRow.':E'.$indexRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');

	// 			$indexRow++;
	// 		}
	// 		$blink = !$blink;
	// 	}
		
	// 	$excelSheet->setCellValue(('D'. $indexRow), "TOTAL: ");
	// 	$excelSheet->setCellValue(('E'. $indexRow), $monto_total);
		
	// 	$excelSheet->getStyle('A:F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

	// 	$excelSheet->getColumnDimension('A')->setWidth(13);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getStyle('A1:H1')->getFont()->setBold(true);

	// 	$writer = new Xlsx($spreadsheet);
	// 	// $writer = new Xls($spreadsheet);
	// 	$writer->save("exports/" . $empresa->id . BPS_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . BPS_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;

	// 	$response->finalName = $empresa->convenio . '-' . $empresa->descripcion . ' DEBITOS BPS.xlsx';

	// 	$response->format = 'xlsx';
	// 	$response->path = URL_EXPORTS . $empresa->id . BPS_PATH . $nombreArchivo;
	// 	return $response;

	// }

	// public function exportExcel($nombreArchivo, $clients, $empresa, $idDebito){ 
	// 	$handleDateTimeClass = new handleDateTime();
	// 	if($idDebito == BPS){
	// 		if(!is_dir(URL_EXPORTS . $empresa->id . "/bps"))
    //             mkdir(URL_EXPORTS . $empresa->id . "/bps", 0777, true);
	// 	}
		
	// 	$response = new \stdClass();
		
	// 	$spreadsheet = new Spreadsheet();
	// 	$excelSheet = $spreadsheet->getActiveSheet();
	// 	$excelSheet->setTitle($empresa->convenio . '-' . $empresa->descripcion . 'DEBITOS BPS');
		
		
	// 	$numberFormat = new NumberFormat();
	// 	$rut_format = $numberFormat::FORMAT_NUMBER;
	// 	$price_format = $numberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
		
		
	// 	$anio_mes = $handleDateTimeClass->getCurrentDateAAAAMM();
	// 	// $anio_mes = '202309';
	// 	$monto_total = 0;
	// 	$excelSheet->setCellValue('A2', "Pais");
	// 	$excelSheet->setCellValue('B2', "Tipo Doc.");
	// 	$excelSheet->setCellValue('C2', 'Documento');
	// 	$excelSheet->setCellValue('D2', 'Correlativo');
	// 	$excelSheet->setCellValue('E2', 'Importe');
	// 	$excelSheet->setCellValue('F2', 'Referencia externa');

	// 	$excelSheet->setCellValue('C1', "Emision");
	// 	$excelSheet->setCellValue('D1', $anio_mes);


	// 	$excelSheet->setCellValue('G1', "Tipo Persona:");
	// 	$excelSheet->setCellValue('H1', 'PAS');
		
	// 	$excelSheet->setCellValue('I1', 'Cantidad de Lineas');
	// 	$excelSheet->setCellValue('J1', count($clients));

		
		
	// 	$indexRow = 3;
	// 	foreach ($clients as $client) {
	// 		$monto_total += floatval($client['importe']);
			
	// 		$excelSheet->setCellValue(('A'.$indexRow), 1);
	// 		// $excelSheet->getStyle('A'.$indexRow)->getNumberFormat()->setFormatCode($rut_format);
			
	// 		$excelSheet->setCellValue(('B'.$indexRow), 'DO');
			
	// 		$excelSheet->setCellValue(('C'.$indexRow), $client['ciSocio']);
	// 		// $excelSheet->getStyle('C'.$indexRow)->getNumberFormat()->setFormatCode($price_format);
			
	// 		$excelSheet->setCellValue(('D'.$indexRow), ltrim($empresa->convenio, "0")); // para BPS el convenio es el correlativo
	// 		// $excelSheet->getStyle('D'.$indexRow)->getNumberFormat()->setFormatCode($price_format);
			
	// 		$excelSheet->setCellValue(('E'.$indexRow), $client['importe']);
			
	// 		$excelSheet->setCellValue(('F'.$indexRow), $client['nombreSocio']);
			
	// 		$indexRow++;
			
	// 	}
		
	// 	$excelSheet->setCellValue('K1', 'Monto total');
	// 	$excelSheet->setCellValue('L1', $monto_total);

	// 	$excelSheet->getColumnDimension('A')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('B')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('C')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('D')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('E')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('F')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('G')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('H')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('I')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('J')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('K')->setAutoSize(true);
	// 	$excelSheet->getColumnDimension('L')->setAutoSize(true);

	// 	$writer = null;
	// 	// if (str_ends_with($nombreArchivo, '.xlsx'))
	// 	if (strpos($nombreArchivo, '.xlsx') !== false)
	// 		$writer = new Xlsx($spreadsheet);
	// 	else
	// 		$writer = new Xls($spreadsheet);
		
	// 	$writer->save("exports/" . $empresa->id . BPS_PATH . $nombreArchivo);
		
	// 	$file_name = $empresa->id . BPS_PATH . $nombreArchivo;
	// 	// $file_name = substr($file_name, 0, -5);
	// 	$response->result = 2;
	// 	$response->name = $file_name;

	// 	if (strpos($nombreArchivo, '.xlsx') !== false) {
	// 		$response->finalName = $empresa->convenio . '-' . $empresa->descripcion . ' DEBITOS BPS.xlsx';
	// 		$response->format = 'xlsx';
	// 	} else {
	// 		$response->finalName = $empresa->convenio . '-' . $empresa->descripcion . ' DEBITOS BPS.xls';
	// 		$response->format = 'xls';
	// 	}
		
	// 	$response->path = URL_EXPORTS . $empresa->id . BPS_PATH . $nombreArchivo;
	// 	return $response;

	// }

	// public function readExcelBPS($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
	// 	$validator = new CiValidator();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$promo = -1;
	// 	$numContrato = -1;
	// 	$departamento = -1;
	// 	$fechaAlta = -1;
	// 	$importe = -1;
	// 	$ci = -1;
	// 	$nombre = -1;
	// 	$apellido = -1;

	// 	// get column names first
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // Asi como esta le falta recorrer la ultima columna pero no tiene nada que importe en este caso
			
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'NOMBRE')
	// 			$nombre = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'APELLIDO')
	// 			$apellido = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'IMPORTE')
	// 			$importe = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'CI')
	// 			$ci = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'DEPARTAMENTO')
	// 			$departamento = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'NUM CONT.' || $excelSheet->getCell($col . 2)->getValue() == 'Nº CONTRARTO')
	// 			$numContrato = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'MES')
	// 			$fechaAlta = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'SI HAY PROMO')
	// 			$promo = $col;
	// 	}
	// 	// if ($promo == -1 || $numContrato == -1 || $departamento == -1 || $fechaAlta == -1 || $importe == -1 || $ci == -1 || $nombre == -1 || $nombre == -1 || $apellido == -1) {
	// 	if ($numContrato == -1 || $departamento == -1 || $fechaAlta == -1 || $importe == -1 || $ci == -1 || $nombre == -1 || $apellido == -1) {
	// 		$response->result = 1;
	// 		$response->message = "Error al leer el archivo. Columna no encontrada ";
	// 		return $response;
	// 	}
		
	// 	// loop through the rows and columns of the sheet | start from row 3
	// 	for ($row = 3; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		// if(is_null($excelSheet->getCell($ci . $row)->getValue()) || is_null($excelSheet->getCell($nombre . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue())){
	// 		// if(is_null($excelSheet->getCell($ci . $row)->getValue()) || is_null($excelSheet->getCell($numContrato . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue())){
	// 		if(is_null($excelSheet->getCell($ci . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue())){
	// 			// echo "fallo :" . $row . "\n";

	// 			// $response->resume .= 'Linea ' . $row . ' Salteada' . "\n";

	// 			// $response->clients = $clients;
	// 			// $response->result = 1;
	// 			// $response->message = "Carga correcta hasta la fila " . $row;
	// 			// return $response;
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
	// 		$client->nombreSocio = "";
	// 		$client->apellidoSocio = "";
	// 		try {
	// 			//code...
	// 			// $client->nombre = $excelSheet->getCell($nombre . $row)->getValue();
	// 			// $client->ci = $excelSheet->getCell($ci . $row)->getValue();
	// 			$client->nombreSocio = $excelSheet->getCell($nombre . $row)->getValue();
	// 			$client->ciSocio = $excelSheet->getCell($ci . $row)->getValue();
	// 			// $client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->importe = $excelSheet->getCell($importe . $row)->getCalculatedValue();
	// 		} catch (\Throwable $th) {
	// 			//throw $th;
	// 			// echo 'ERROR: ' . $th;
	// 			$response->result = 1;
	// 			$response->message = "Error al leer el archivo. Linea " . $row;
	// 			return $response;
	// 		}
	// 		$client->apellidoSocio = $excelSheet->getCell($apellido . $row)->getValue();
	// 		// $client->fechaAlta = $excelSheet->getCell($fechaAlta . $row)->getValue();
			
	// 		// CAPTURANDO LA FECHA DE ALTA ---------- TODO ES POR LOS DISTINTOS FORMATOS ENCONTRADOS

	// 		// Define a mapping of month abbreviations to month numbers
	// 		$monthAbbreviations = [
	// 			'jan' => 1,
	// 			'feb' => 2,
	// 			'mar' => 3,
	// 			'apr' => 4,
	// 			'May' => 5,
	// 			'jun' => 6,
	// 			'jul' => 7,
	// 			'aug' => 8,
	// 			'sep' => 9,
	// 			'oct' => 10,
	// 			'nov' => 11,
	// 			'dec' => 12,

	// 			'abri' => 4,
	// 			'abr' => 4,

	// 			'ene' => 1,
	// 			'feb' => 2,
	// 			'mar' => 3,
	// 			'abr' => 4,
	// 			'may' => 5,
	// 			'jun' => 6,
	// 			'jul' => 7,
	// 			'ago' => 8,
	// 			'sep' => 9,
	// 			'oct' => 10,
	// 			'nov' => 11,
	// 			'dic' => 12,
	// 		];

	// 		$monthNames = [
	// 			'enero' => 1,        // Enero
	// 			'febrero' => 2,      // Febrero
	// 			'marzo' => 3,        // Marzo
	// 			'abril' => 4,        // Abril
	// 			'mayo' => 5,         // Mayo
	// 			'junio' => 6,        // Junio
	// 			'julio' => 7,        // Julio
	// 			'agosto' => 8,       // Agosto
	// 			'septiembre' => 9,   // Septiembre
	// 			'setiembre' => 9,   // Septiembre
	// 			'octubre' => 10,     // Octubre
	// 			'noviembre' => 11,   // Noviembre
	// 			'diciembre' => 12,   // Diciembre
	// 		];


	// 		// Retrieve the cell's value as a PHP serialized date value
	// 		$excelSerializedDate = $excelSheet->getCell($fechaAlta . $row)->getValue();
	// 		// Check if the cell's value is a valid date-like string
	// 		$dateParts = explode('-', $excelSerializedDate); // porque hay strings como sigue: 'Aug-21'
	// 		if (count($dateParts) === 2) {
	// 			$monthAbbreviation = strtolower($dateParts[0]);
	// 			$year = 20 . intval($dateParts[1]);
				
	// 			if (array_key_exists($monthAbbreviation, $monthAbbreviations)) {
	// 				$month = $monthAbbreviations[$monthAbbreviation];
					
	// 				// Create a DateTime object for the date-like string
	// 				$date = new DateTime();
	// 				$date->setDate($year, $month, 1);
					
	// 				// Format the date as desired (e.g., to 'Y-m-d' format)
	// 				$formattedDate = $date->format('Y-m-d');
	// 				$client->fechaAlta = $formattedDate;
	// 			} else {
	// 				$client->fechaAlta = null;
	// 				// echo 'Invalid month abbreviation 1. |' . $excelSerializedDate . "|";
	// 				// Handle invalid month abbreviation
	// 			}
	// 		} else {
	// 			$dateParts = explode(' ', $excelSerializedDate); // porque tambien hay errores como 'Sep 22'
	// 			if (count($dateParts) === 2) {
	// 				$monthAbbreviation = strtolower($dateParts[0]);
	// 				$year = 20 . intval($dateParts[1]);
					
	// 				if (array_key_exists($monthAbbreviation, $monthAbbreviations)) {
	// 					$month = $monthAbbreviations[$monthAbbreviation];
						
	// 					// Create a DateTime object for the date-like string
	// 					$date = new DateTime();
	// 					$date->setDate($year, $month, 1);
						
	// 					// Format the date as desired (e.g., to 'Y-m-d' format)
	// 					$formattedDate = $date->format('Y-m-d');
	// 					$client->fechaAlta = $formattedDate;
	// 				} else {
	// 					$client->fechaAlta = null;
	// 					// echo 'Invalid month abbreviation 2. |' . $excelSerializedDate . "|";
	// 					// Handle invalid month abbreviation
	// 				}
	// 			} else {
	// 				if(is_null($excelSerializedDate) || $excelSerializedDate == "") {
	// 					// echo 'Invalid month abbreviation 3. |' . $excelSerializedDate . "|";
	// 					// var_dump($excelSerializedDate);
	// 					$client->fechaAlta = null;
	// 				} else {
	// 					$cellValueLower = strtolower($excelSerializedDate); // Convert to lowercase for case insensitivity
	// 					if (array_key_exists($cellValueLower, $monthNames)) {
	// 						// echo 'entro a la nuevo |' . $cellValueLower . "|    " . "\n";
	// 						$month = $monthNames[$cellValueLower];

	// 						// Create a DateTime object for the full month name
	// 						$date = new DateTime();
	// 						$date->setDate('2017', $month, 1); // Use the current year and set day to 1

	// 						// Format the date as desired (e.g., to 'Y-m-d' format)
	// 						$formattedDate = $date->format('Y-m-d');
	// 						$client->fechaAlta = $formattedDate;
	// 					} else {
	// 						// If not a date-like string, proceed with existing code for Excel serialized dates
	// 						$unixTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($excelSerializedDate);
	// 						$date = new DateTime();
	// 						$date->setTimestamp($unixTimestamp);
							
	// 						// Check if the year is 1900 and adjust it if needed
	// 						$year = $date->format('Y');
	// 						if ($year == 1900) {
	// 							$year = 2017; // Replace with the desired year
	// 							$date->setDate($year, $date->format('m'), $date->format('d'));
	// 						}
							
	// 						// Format the date as desired (e.g., to 'Y-m-d' format)
	// 						$formattedDate = $date->format('Y-m-d');
	// 						$client->fechaAlta = $formattedDate;
	// 					}
	// 				}
	// 			}
	// 		}

	// 		// FIN DE CAPTURANDO LA FECHA DE ALTA ---------------------------------------------
	// 		// if(strlen($client->ciSocio) < 8){
	// 			// if(!$validator->validate_ci( $client->ciSocio)){ // Si la cedula no es valida
	// 			// $client->ciSocio = $client->ciSocio . $validator->validation_digit( $client->ciSocio ); // Le concateno el digito verificador
	// 			// }
	// 		// }
	// 		if($client->nombreSocio == ""){
	// 			$nameTemp = $restController->queryNameFromCi($client->ciSocio);
	// 			if($nameTemp != "ERROR")
	// 				$client->nombreSocio = $nameTemp;
	// 			else 
	// 				$client->nombreSocio = "SIN NOMBRE";
	// 		}

	// 		$client->departamento = $excelSheet->getCell($departamento . $row)->getValue();
	// 		// $client->promo = $excelSheet->getCell($promo . $row)->getValue();
	// 		// $client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue();
	// 		$client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue() != "" ? $excelSheet->getCell($numContrato . $row)->getValue() : $client->ciSocio ; // ADAPTADO
	// 		$clients[] = $client;
	// 	}
	// 	error_log("CANTIDAD DE CLIENTES:" . count($clients));
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
		
	// }

	// public function readExcelMASTER($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$tarjeta = -1;
	// 	$nombre = 'SIN NOMBRE';
	// 	$importe = -1;
	// 	$socio = -1;
	// 	$sucursal = -1;

	// 	// get column names first
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // SI encuentra todos en fila A
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'SUCURSAL')
	// 			$sucursal = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'Tarjeta')
	// 			$tarjeta = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'Socio')
	// 			$socio = $col;
	// 		if($excelSheet->getCell($col . 2)->getValue() == 'Importe')
	// 			$importe = $col;
	// 	}
	// 	if ($tarjeta == -1 || $socio == -1 || $sucursal == -1 || $importe == -1) {
			
	// 		for ($col = 'B'; $col != $highestColumn; $col++) { // Si lata alguno que busque en fila B
	// 			if($excelSheet->getCell($col . 1)->getValue() == 'SUCURSAL')
	// 				$sucursal = $col;
	// 			if($excelSheet->getCell($col . 1)->getValue() == 'Tarjeta')
	// 				$tarjeta = $col;
	// 			if($excelSheet->getCell($col . 1)->getValue() == 'Socio')
	// 				$socio = $col;
	// 			if($excelSheet->getCell($col . 1)->getValue() == 'Importe')
	// 				$importe = $col;
	// 		}
			
	// 		// if ($tarjeta == -1 || $socio == -1 || $sucursal == -1 || $importe == -1) {
	// 		if ($tarjeta == -1 || $socio == -1 || $importe == -1) {
	// 			$response->result = 1;
	// 			// $response->message = "Error al leer el archivo. Columna no encontrada . tarjeta: " . $tarjeta . ". socio: " . $socio . ". sucursal: " . $sucursal . ". importe: " . $importe;
	// 			$response->message = "Error al leer el archivo. Columna no encontrada";
	// 			return $response;
	// 		}
	// 	}
	// 	$validator = new CiValidator();
	// 	// loop through the rows and columns of the sheet | start from row 3
	// 	for ($row = 3; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		if(is_null($excelSheet->getCell($socio . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue()) || is_null($excelSheet->getCell($tarjeta . $row)->getValue())){
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
	// 		try {
	// 			//code...
	// 			$client->ciSocio = $excelSheet->getCell($socio . $row)->getValue();

	// 			if(!$validator->validate_ci( $client->ciSocio)){ // Si la cedula no es valida
	// 				$client->ciSocio = $client->ciSocio . $validator->validation_digit( $client->ciSocio ); // Le concateno el digito verificador
	// 			}

	// 			$client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			// $client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->tarjeta = $excelSheet->getCell($tarjeta . $row)->getValue();
	// 		} catch (\Throwable $th) {
	// 			// echo 'ERROR: ' . $th;
	// 			$response->result = 1;
	// 			$response->message = "Error al leer el archivo. Linea " . $row;
	// 			return $response;
	// 		}
	// 		if($sucursal != -1)
	// 			$client->sucursal = $excelSheet->getCell($sucursal . $row)->getValue();
	// 		else
	// 			$client->sucursal = "";
	// 		$nameTemp = $restController->queryNameFromCi($client->ciSocio);
	// 		if($nameTemp != "ERROR")
	// 			$client->nombre = $nameTemp;
	// 		else 
	// 			$client->nombre = $nombre;

	// 		$client->nombreSocio = $client->nombre;
	// 		$client->ci = $client->ciSocio;
	// 		$client->numContrato = $client->ciSocio;
	// 		// $client->nombre = $nombre;
	// 		$clients[] = $client;
	// 	}
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
		
	// }

	// // readExcelCREDITEL($responseInfoEmpresa->empresa, $idDebito, $uploadedFilePath)
	// public function readExcelCREDITEL($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$nombreSocio = 'SIN NOMBRE';
	// 	$ciSocio = -1;
	// 	$numContrato = -1;
	// 	$tarjeta = -1;
	// 	$importe = -1;
	// 	$sucursal = -1;

	// 	// get column names first
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // SI encuentra todos en fila A
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Nro Socio')
	// 			$numContrato = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Cédula')
	// 			$ciSocio = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Nro Tarjeta')
	// 			$tarjeta = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'LOCALIDAD')
	// 			$sucursal = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Importe')
	// 			$importe = $col;
	// 	}
	// 	if ($tarjeta == -1 || $ciSocio == -1 || $numContrato == -1 || $importe == -1) {
	// 		$response->result = 1;
	// 		// $response->message = "Error al leer el archivo. Columna no encontrada . tarjeta: " . $tarjeta . ". socio: " . $socio . ". sucursal: " . $sucursal . ". importe: " . $importe;
	// 		$response->message = "Error al leer el archivo. Columna no encontrada";
	// 		return $response;
	// 	}
	// 	$validator = new CiValidator();
	// 	// loop through the rows and columns of the sheet | start from row 2
	// 	for ($row = 2; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		if(is_null($excelSheet->getCell($ciSocio . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue()) || is_null($excelSheet->getCell($tarjeta . $row)->getValue()) || is_null($excelSheet->getCell($numContrato . $row)->getValue())){
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
	// 		try {
	// 			//code...
	// 			$client->ciSocio = $excelSheet->getCell($ciSocio . $row)->getValue();

	// 			if(!$validator->validate_ci( $client->ciSocio)){ // Si la cedula no es valida
	// 				$client->ciSocio = $client->ciSocio . $validator->validation_digit( $client->ciSocio ); // Le concateno el digito verificador
	// 			}

	// 			$client->importe =( $excelSheet->getCell($importe. $row)->getValue() / 100);
	// 			// $client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->tarjeta = $excelSheet->getCell($tarjeta . $row)->getValue();
	// 		} catch (\Throwable $th) {
	// 			// echo 'ERROR: ' . $th;
	// 			$response->result = 1;
	// 			$response->message = "Error al leer el archivo. Linea " . $row;
	// 			return $response;
	// 		}
	// 		$client->sucursal = $excelSheet->getCell($sucursal . $row)->getValue();
			
	// 		$nameTemp = $restController->queryNameFromCi($client->ciSocio);
	// 		if($nameTemp != "ERROR")
	// 			$client->nombreSocio = $nameTemp;
	// 		else 
	// 			$client->nombreSocio = $nombre;

	// 		$client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue();
	// 		$clients[] = $client;
	// 	}
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
	// }

	// // readExcelCABAL($responseInfoEmpresa->empresa, $idDebito, $uploadedFilePath)
	// public function readExcelCABAL($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$nombreSocio = 'SIN NOMBRE';
	// 	$ciSocio = -1;
	// 	$nombre = 'SIN NOMBRE';
	// 	$ci = -1;
	// 	$numContrato = -1;
	// 	$importe = -1;
	// 	$tarjeta = -1;

	// 	// get column names first
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // SI encuentra todos en fila A
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Nro. Cupon')
	// 			$numContrato = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Socio')
	// 			$nombreSocio = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Doc Cliente')
	// 			$ciSocio = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Doc Cuenta')
	// 			$ci = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Cuenta ó Tarjeta')
	// 			$tarjeta = $col;
	// 		if($excelSheet->getCell($col . 1)->getValue() == 'Importe')
	// 			$importe = $col;
	// 	}
	// 	if ($tarjeta == -1 || $ciSocio == -1 || $ci == -1 || $numContrato == -1 || $importe == -1) {
	// 		$response->result = 1;
	// 		// $response->message = "Error al leer el archivo. Columna no encontrada . tarjeta: " . $tarjeta . ". socio: " . $socio . ". sucursal: " . $sucursal . ". importe: " . $importe;
	// 		$response->message = "Error al leer el archivo. Columna no encontrada";
	// 		return $response;
	// 	}
	// 	$validator = new CiValidator();
	// 	// loop through the rows and columns of the sheet | start from row 2
	// 	for ($row = 2; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		if(is_null($excelSheet->getCell($ciSocio . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue()) || is_null($excelSheet->getCell($tarjeta . $row)->getValue()) || is_null($excelSheet->getCell($numContrato . $row)->getValue()) || is_null($excelSheet->getCell($ci . $row)->getValue())){
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
			
	// 		$client->ciSocio = $excelSheet->getCell($ciSocio . $row)->getValue();
	// 		$client->importe =$excelSheet->getCell($importe. $row)->getValue();
	// 		$client->tarjeta = $excelSheet->getCell($tarjeta . $row)->getValue();

	// 		$client->ci = $excelSheet->getCell($ci . $row)->getValue();
	// 		$client->nombreSocio = $excelSheet->getCell($nombreSocio . $row)->getValue();
			
	// 		$nameTemp = $restController->queryNameFromCi($client->ci);
	// 		if($nameTemp != "ERROR")
	// 			$client->nombre = $nameTemp;
	// 		else
	// 			$client->nombre = $nombre;

	// 		$client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue();
	// 		$clients[] = $client;
	// 	}
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
	// }

	// public function readExcelCREDITOSDIRECTOS($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$ci = -1;
	// 	$nombre = 'SIN NOMBRE';
	// 	$importe = -1;
	// 	// $departamento = -1;
	// 	$numContrato = -1;
	// 	$integrantes = -1;

	// 	// get column names first
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // SI encuentra todos en fila A
	// 		// ############################### OLD VERSION ###############################
	// 		// if($excelSheet->getCell($col . 1)->getValue() == 'CI')
	// 		// 	$ci = $col;
	// 		// if($excelSheet->getCell($col . 1)->getValue() == 'Importe')
	// 		// 	$importe = $col;
	// 		// if($excelSheet->getCell($col . 1)->getValue() == 'contrato')
	// 		// 	$departamento = $col;
	// 		// if($excelSheet->getCell($col . 1)->getValue() == 'INTEG')
	// 		// 	$integrantes = $col;
	// 		// ############################### OLD VERSION ###############################

	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'CI TARJETA')
	// 			$ci = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'IMPORTE')
	// 			$importe = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'CONTRATO')
	// 			$numContrato = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'INTEG')
	// 			$integrantes = $col;
	// 	}
	// 	if($numContrato == -1)
	// 		$numContrato = $ci;
	// 	if ($ci == -1 || $importe == -1 || $numContrato == -1) {
	// 		$response->result = 1;
	// 		// $response->message = "Error al leer el archivo. Columna no encontrada . tarjeta: " . $tarjeta . ". socio: " . $socio . ". sucursal: " . $sucursal . ". importe: " . $importe;
	// 		$response->message = "Error al leer el archivo. Columna no encontrada";
	// 		return $response;
	// 	}
	// 	$validator = new CiValidator();
	// 	// loop through the rows and columns of the sheet | start from row 3
	// 	for ($row = 2; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		if(is_null($excelSheet->getCell($ci . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue()) || is_null($excelSheet->getCell($numContrato . $row)->getValue())){
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
	// 		try {
	// 			//code...
	// 			$client->ci = $excelSheet->getCell($ci . $row)->getValue();
	// 			if(!$validator->validate_ci( $client->ci)){ // Si la cedula no es valida
	// 				$client->ci = $client->ci . $validator->validation_digit( $client->ci ); // Le concateno el digito verificador
	// 			}
	// 			// echo "Validation for " . $client->ci . ": ".($validator->validate_ci( $client->ci ) ? 'true' : 'false').PHP_EOL;
	// 			$client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			// $client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue();
	// 			$client->integrantes = $excelSheet->getCell($integrantes . $row)->getValue();
	// 		} catch (\Throwable $th) {
	// 			// echo 'ERROR: ' . $th;
	// 			$response->result = 1;
	// 			$response->message = "Error al leer el archivo. Linea " . $row;
	// 			return $response;
	// 		}
	// 		$client->departamento = $excelSheet->getCell('B' . $row)->getValue();
	// 		$nameTemp = $restController->queryNameFromCi($client->ci);
	// 		if($nameTemp != "ERROR")
	// 			$client->nombre = $nameTemp;
	// 		else 
	// 			$client->nombre = $nombre;
	// 		if($client->integrantes == "") $client->integrantes = 1;
			
	// 		$client->nombreSocio = $client->nombre;
	// 		$client->ciSocio = $client->ci;

	// 		$clients[] = $client;
	// 	}
	// 	// var_dump($clients);
	// 	// exit;
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
	// }

	// public function readExcelPASSCARD($empresa, $idDebito, $uploadedFilePath){
	// 	$clients = array();
	// 	$response = new \stdClass();
	// 	$restController = new ctr_rest();
		
	// 	$response->resume = "";

	// 	$spreadsheet = IOFactory::load($uploadedFilePath);

	// 	$excelSheet = $spreadsheet->getActiveSheet();

	// 	$highestRow = $excelSheet->getHighestRow();
	// 	$highestColumn = $excelSheet->getHighestColumn();

	// 	$ci = -1;
	// 	$nombre = 'SIN NOMBRE';
	// 	$importe = -1;
	// 	$numContrato = -1;
	// 	$departamento = 'B';
	// 	$tarjeta = -1;
	// 	$vencimientoTarjeta = -1;

	// 	// - ci (ciSocio) 
	// 	// - nombre (nombreSocio) NULL
	// 	// - importe (importe).
	// 	// - numContrato (numContrato) Numero Socio.
	// 	// - departamento (departamento)
	// 	// - tarjeta (tarjeta)
	// 	// - vencimientoTarjeta(vencimientoTarjeta)

	// 	// get column names first
	// 	// if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'AGENCIA')
	// 	for ($col = 'A'; $col != $highestColumn; $col++) { // SI encuentra todos en fila A
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'CéDULA')
	// 			$ci = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'IMPORTE')
	// 			$importe = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'NRO SOCIO')
	// 			$numContrato = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'VTO TARJETA (AAAAMMDD)')
	// 			$vencimientoTarjeta = $col;
	// 		if(strtoupper($excelSheet->getCell($col . 1)->getValue()) == 'TARJETA')
	// 			$tarjeta = $col;
	// 	}
	// 	if ($ci == -1 || $importe == -1 || $numContrato == -1 || $vencimientoTarjeta == -1 || $tarjeta == -1) {
	// 		$response->result = 1;
	// 		// $response->message = "Error al leer el archivo. Columna no encontrada . tarjeta: " . $tarjeta . ". socio: " . $socio . ". sucursal: " . $sucursal . ". importe: " . $importe;
	// 		$response->message = "Error al leer el archivo. Columna no encontrada";
	// 		return $response;
	// 	}
	// 	$validator = new CiValidator();
	// 	// loop through the rows and columns of the sheet | start from row 3
	// 	for ($row = 2; $row <= $highestRow; $row++) {
	// 		// Si alguno de estos campos es NULL lo salteo
	// 		if(is_null($excelSheet->getCell($ci . $row)->getValue()) || is_null($excelSheet->getCell($importe . $row)->getValue()) || is_null($excelSheet->getCell($numContrato . $row)->getValue()) || is_null($excelSheet->getCell($vencimientoTarjeta . $row)->getValue()) || is_null($excelSheet->getCell($tarjeta . $row)->getValue())){
	// 			continue;
	// 		}
	// 		$client = new \stdClass();
	// 		try {
	// 			//code...
	// 			$client->ci = $excelSheet->getCell($ci . $row)->getValue();
	// 			if(!$validator->validate_ci( $client->ci)){ // Si la cedula no es valida
	// 				$client->ci = $client->ci . $validator->validation_digit( $client->ci ); // Le concateno el digito verificador
	// 			}
	// 			// echo "Validation for " . $client->ci . ": ".($validator->validate_ci( $client->ci ) ? 'true' : 'false').PHP_EOL;
	// 			$client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->importe = sprintf("%.2f", $client->importe / 100);
				
	// 			// $client->importe = $excelSheet->getCell($importe . $row)->getValue();
	// 			$client->numContrato = $excelSheet->getCell($numContrato . $row)->getValue();
	// 			$client->departamento = $excelSheet->getCell($departamento . $row)->getValue();
	// 			$client->tarjeta = $excelSheet->getCell($tarjeta . $row)->getValue();
	// 			$client->vencimientoTarjeta = $excelSheet->getCell($vencimientoTarjeta . $row)->getValue();
	// 		} catch (\Throwable $th) {
	// 			// echo 'ERROR: ' . $th;
	// 			$response->result = 1;
	// 			$response->message = "Error al leer el archivo. Linea " . $row;
	// 			return $response;
	// 		}
			

	// 		$nameTemp = $restController->queryNameFromCi($client->ci);
	// 		if($nameTemp != "ERROR")
	// 			$client->nombre = $nameTemp;
	// 		else 
	// 			$client->nombre = $nombre;

	// 		$clients[] = $client;
	// 	}
	// 	// var_dump($clients);
	// 	// exit;
	// 	$response->clients = $clients;
	// 	$response->result = 2;
	// 	return $response;
	// }

}