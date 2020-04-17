<?php

 /*  require ('../vendor/autoload.php');
  require ('subastas.php');

  require_once __DIR__ . '\..\vendor\phpoffice\phpspreadsheet\src\Bootstrap.php'; */
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Helper\Sample;
 use PhpOffice\PhpSpreadsheet\IOFactory;
 use PhpOffice\PhpSpreadsheet\Style\Alignment;
 use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
 use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
 use PhpOffice\PhpSpreadsheet\Style\Fill;
 use PhpOffice\PhpSpreadsheet\Shared\Date;
 use PhpOffice\PhpSpreadsheet\Style\Color;
 use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
 use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excel
{
    public function __construct()
    {
    }

    public static function post($peticion)
    {
        if ($peticion[0] == 'generarPDF') {
            return self::generaPDF($_POST);
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }

    private static function generaPDF()
    {

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        /* CONSULTA A LA BASE DE DATOS */
        /* $idSubasta = $_POST['idsubasta'];
         */

        $headerStyle = [
    'font'=>[
        'color'=>[
            'rgb'=>'D9D9D9'
        ],
        'size'=>28,
        'bold'=>true
    ],
    'fill'=>[
        'fillType'=>Fill::FILL_SOLID,
        'startColor'=>[
            'rgb'=>'203764'
        ]
    ]
];

        $headerDate = [
    'font'=>[
        'color'=>[
            'rgb'=>'A6A6A6'
        ],
        'size'=>11,
        'bold'=> false
    ]
];

        $cabezerasStyle =[
    'font'=>[
        'color'=>[
            'rgb'=>'000000'
        ],
        'size'=>11,
        'bold'=>true
    ],
    'fill'=>[
        'fillType'=>Fill::FILL_SOLID,
        'startColor'=>[
            'rgb'=>'B4C6E7'
        ]
    ]
];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->createSheet();
        $nombreDelDocumento = "ReporteSubasta.xlsx";

        /* Estilos por default */
        $helper->log('Set default font');
        $spreadsheet->getDefaultStyle()
    ->getFont()
    ->setName('Raleway')
    ->setSize(10);
        /* $sheet = $spreadsheet->getActiveSheet(); */

        //Se establece la pesaña activa y el titulo
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle("Ganadores");


        /* Propiedades del documento */
        $spreadsheet
    ->getProperties()
    ->setCreator("Escuderia AGO")
    ->setLastModifiedBy("EAGO") // última vez modificado por
    ->setTitle('Reporte de Subasta')
    ->setDescription('Reporte de pujas totales durante la subasta');


        /* Formato de hoja de reporte Ganadores */
        /* HEADER */

        /* Tamaño de celdas */
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('B')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('D')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('F')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('H')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('J')
   ->setAutoSize(true);

        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension(2)->setRowHeight(15);
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getRowDimension(4)->setRowHeight(15);

        /* Agrega la imagen del logo */
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('logo');
        $drawing->setPath("modelos/LOGO-AGO.png"); // put your path and image here
        $drawing->setCoordinates('A2');
        $drawing->setOffsetX(0);
        $drawing->setRotation(0);
        $drawing->setHeight(50);
        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());


        $spreadsheet->getActiveSheet()
    ->setCellValue('D2', "REPORTE DE SUBASTA");

        /* Unir celdas y estilo de font*/
        $spreadsheet->getActiveSheet()->mergeCells('D2:H3');
        $spreadsheet->getActiveSheet()
    ->getStyle('D2')
    ->getFont()
    ->setSize(28);
        $spreadsheet->getActiveSheet()
    ->getStyle('D2')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()
    ->getStyle('A1:L4')->applyFromArray($headerStyle);


        /* Agrega el dia que fue generado */
        /* No genera bien la fecha */
        $dateTimeNow = time();
        $sheet
    ->setCellValue('K2', "Generado el dia")
    ->setCellValue('K3', Date::PHPToExcel($dateTimeNow));
        $sheet->getStyle('K2:K3')->applyFromArray($headerDate);


        /* Escribe las cabeceras de la tabla */
        $sheet
    ->setCellValue('B9', "Auto")
    ->setCellValue('D9', "Precio de salida (MXN)")
    ->setCellValue('F9', "Ganador")
    ->setCellValue('H9', "Puja (MXN)")
    ->setCellValue('J9', "Hora del registro");

        $sheet->getStyle('B9:J9')->applyFromArray($cabezerasStyle);

        /* ------------------------------------------------------------------- */
        /* Hace la consulta a los ganadores */

        $oresuladoOfertas = new subastas(0, 0);
        $resuladoOfertas = $oresuladoOfertas::revisarresultados($_POST);
        $array = json_decode(json_encode($resuladoOfertas), true);
        $row=11;

        /* loop through the data */
        foreach ($array as $ganadores) {
            $sheet
        ->setCellValue('B'.$row, $ganadores['marca']." ".$ganadores['modelo'])
        ->setCellValue('D'.$row, "$".$ganadores['precio'])
        ->setCellValue('F'.$row, $ganadores['usuario'])
        ->setCellValue('H'.$row, "$".$ganadores['oferta'])
        ->setCellValue('J'.$row, $ganadores['hora_puja']);
            $row++;
        }
        $row=11;

        /* ------------------------------------------------------------------- */
        /* Reporte de pujas */

        //Se establece la pesaña activa y el titulo
        
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle("Ofertas");

        /* Formato de hoja de reporte Pujas */
        /* HEADER */

        /* Tamaño de celdas */
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('B')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('C')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('D')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('E')
   ->setAutoSize(true);
        $spreadsheet->getActiveSheet()
   ->getColumnDimension('F')
   ->setAutoSize(true);
   $spreadsheet->getActiveSheet()
   ->getColumnDimension('G')
   ->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(15);
        $spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(15);
        $spreadsheet->getActiveSheet()->getRowDimension(3)->setRowHeight(15);
        $spreadsheet->getActiveSheet()->getRowDimension(4)->setRowHeight(15);

        /* Agrega la imagen del logo */
        /* $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('logo');
        $drawing->setPath("modelos/LOGO-AGO.png"); // put your path and image here
        $drawing->setCoordinates('A2');
        $drawing->setOffsetX(0);
        $drawing->setRotation(0);
        $drawing->setHeight(50);
        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);
        $drawing->setWorksheet($spreadsheet->getActiveSheet()); */


        $spreadsheet->getActiveSheet()
    ->setCellValue('D2', "REPORTE DE SUBASTA");
    $spreadsheet->getActiveSheet()->setCellValue('D4', "Historial de pujas");

        /* Unir celdas y estilo de font*/
        $spreadsheet->getActiveSheet()->mergeCells('D2:H3');
        $spreadsheet->getActiveSheet()
    ->getStyle('D2')
    ->getFont()
    ->setSize(28);
        $spreadsheet->getActiveSheet()
    ->getStyle('D2')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()
    ->getStyle('A1:L4')->applyFromArray($headerStyle);
    $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($headerDate);


        /* Agrega el dia que fue generado */
        /* No genera bien la fecha */
        $dateTimeNow = time();
        $spreadsheet->getActiveSheet()
    ->setCellValue('K2', "Generado el dia")
    ->setCellValue('K3', Date::PHPToExcel($dateTimeNow));
    $spreadsheet->getActiveSheet()->getStyle('K2:K3')->applyFromArray($headerDate);


        /* Escribe las cabeceras de la tabla */
        $spreadsheet->getActiveSheet()
    ->setCellValue('B9', "Auto")
    ->setCellValue('C9', "Usuario")
    ->setCellValue('D9', "Oferta (MXN)")
    ->setCellValue('E9', "Estatus")
    ->setCellValue('F9', "Motivo")
    ->setCellValue('G9', "Hora del registro");

    $spreadsheet->getActiveSheet()->getStyle('B9:G9')->applyFromArray($cabezerasStyle);


        /* --------------------------------------------------------------- */
        /* Hace consulta a las pujas de la subasta */

        /* loop through the data */
        foreach ($array as $pujas) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('B'.$row, $pujas['marca']." ".$pujas['modelo']);
            $row++;
        
            foreach($pujas["ofertas"] as $oferta){
                $spreadsheet->getActiveSheet()
                ->setCellValue('C'.$row, $oferta['nombre_usuario'])
                ->setCellValue('D'.$row, "$".$oferta['oferta'])
                ->setCellValue('E'.$row, $oferta['estatus'])
                ->setCellValue('F'.$row, $oferta['motivo'])
                ->setCellValue('G'.$row, $oferta['hora_puja']);
                $row++;
            }
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);



        /**
         * Los siguientes encabezados son necesarios para que
         * el navegador entienda que no le estamos mandando
         * simple HTML
         * Por cierto: no hagas ningún echo ni cosas de esas; es decir, no imprimas nada
         */
 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
        header('Cache-Control: max-age=0');
 
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_end_clean();
        $writer->save('php://output');
        exit();
    }
}
?>