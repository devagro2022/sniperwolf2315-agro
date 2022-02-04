<?php
include('../conectarbase.php');
include_once '../usercon_odoo.php';
Conexion::abrirConexion();
$Conn = Conexion::obtenerConexion();

$ini=trim($_GET['i']);
$fin=trim($_GET['f']);
//$usuario=strtoupper($bus_usu);

$diaA = date('d');
$mesA = date('m');
//$mesA=$mesA-1;
setlocale(LC_TIME, 'es_ES');
$fecha = DateTime::createFromFormat('!m', $mesA);
$mesN = strftime("%B", $fecha->getTimestamp());
$anioA = date('Y');
$fechaActual=$diaA." - ".$mesN." - ".$anioA;

$query1="select sd.name as despacho,sp.origin as orden,sd.date as fecha,sp.number_of_packages as total_cajas,sfl.invoice_number as num_factura,sd.type
from stock_dispatches sd
left join stock_dispatches_line sfl on sd.id=sfl.dispatche_id
left join stock_picking sp on sfl.picking_id=sp.id
where sd.type='conveyor' and sd.state='dispatched' and sp.number_of_packages is not null
and sd.date between '$ini' and '$fin';";

$resultado1= $Conn->prepare($query1);
$resultado1->execute();
$datos1=$resultado1->fetchAll();

$r=$r."<p style=\"text-align: center;\" class=\"z-depth-1\">Informe de despachos generado a corte: ".$fechaActual."</p>";
    $i=1;
        $r=$r."<table style=\"border: 1px solid #000; width:100%;\" class=\"#009688 teal\" >";
        $r=$r."<tr style=\"border-bottom: 1pt solid black; font-size: 0.8em;\">";
        $r=$r."<td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">No.</td>
        <td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">Despacho</td>
        <td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">No. Orden</td>
        <td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">Fecha</td>
        <td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">Total Cajas</td>
        <td style=\"font-weight: bold;text-align: left;\" class=\"z-depth-1 white-text text-darken-2\">Numero Factura</td>
        <td><a href='Informexls/Informe_Despachos.xlsx' class=\"z-depth-1 white-text text-darken-2\">Descargar</a><Strong></td>";
        $r=$r . "</tr>";
    $fd=3;
    // $r="Informexls/Informe_Inventario008.xlsx"
    $miruta='../Informexls/';
    $nombre_fichero = 'Informe_Despachos';
    $mipath=$miruta.$nombre_fichero.'.xlsx';
    if(file_exists($miruta)) {
        include('../Classes/PHPExcel.php');
        include('../Classes/PHPExcel/Reader/Excel2007.php');
        //Crear el objeto Excel: 
        $objPHPExcel = new PHPExcel();
        $mipath2=$miruta.$nombre_fichero.'.xlsx';
        if(file_exists($mipath2)) {
            $archivo = $mipath2;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);                
        } else {              
            $objPHPExcel->getProperties()->setCreator("Autor: Agrocampo");
            $objPHPExcel->getProperties()->setLastModifiedBy("Agrocampo");
            $objPHPExcel->getProperties()->setTitle("Informe Agrocampo");
            $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Informe Empresarial");
            $objPHPExcel->getProperties()->setDescription("Informe en Office 2007 XLSX");
                $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
                $objPHPExcel->getProperties()->setCategory("Resultado de Informe"); 
                // Add new sheet
                $objWorkSheet = $objPHPExcel->createSheet(0);
                $objWorkSheet->setCellValue('A2', 'No.')
                             ->setCellValue('B2', 'Despacho')
                             ->setCellValue('C2', 'No. Orden')
                             ->setCellValue('D2', 'Fecha')
                             ->setCellValue('E2', 'Total Cajas')
                             ->setCellValue('F2', 'Numero Factura');
                //colocar titulos a las hojas de excel
                //$objWorkSheet->setTitle("$i");
                $objWorkSheet->setTitle('Informe Despachos');                    
                }
                $objPHPExcel->getProperties()->setCreator("Autor: Agrocampo");
                $objPHPExcel->getProperties()->setLastModifiedBy("Agrocampo");
                $objPHPExcel->getProperties()->setTitle("Informe Agrocampo");
                $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Informe Empresarial");
                $objPHPExcel->getProperties()->setDescription("Informe en Office 2007 XLSX");
                $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
                $objPHPExcel->getProperties()->setCategory("Resultado de Informe"); 
                    ///////// revisar 
                //BORRAR DTOS
                $fil=3;
                    $objPHPExcel->setActiveSheetIndex(0);
                    $totalreg = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                    $totalreg=$totalreg+1;
                    while ($fil <= $totalreg) {
                        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fil, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$fil, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$fil, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$fil, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$fil, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$fil, '');
                        $fil++;
                }
                //ANCHOS
                $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                
                    $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('C1', "Informe de despachos generado a corte: ".$fechaActual);
                    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
                    
    //DATOS        
    foreach($datos1 as $dato1){
        if(($i%2)==0){
            $color="#AED6F1";
        }else{
            $color="#E8F6F3";
        }
        $d2=$dato1['despacho'];
        $d3=$dato1['orden'];
        $d4=$dato1['fecha'];
        $d5=$dato1['total_cajas'];
        $d6=$dato1['num_factura'];

        //$r=$r."<p>Categorizaci&oacute;n Credito y Cartera.</p>";
        $r=$r."<tr style='background-color: $color;  border: 1px solid rgb(120,120,120); font-size: 1.2em;'>";
        $r=$r."<td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$i."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$d2."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$d3."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$d4."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$d5."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'>".$d6."</td>
               <td style='width: 10%; font-size: 0.5em; border: 1px solid rgb(220,220,220);height: 10px;padding: 5px;'></td>";
        $r=$r."</tr>";
        //EXCEL
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$fd, $i)
        ->setCellValueExplicitByColumnAndRow(1, $fd, $d2, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicitByColumnAndRow(2, $fd, $d3, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicitByColumnAndRow(3, $fd, $d4, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicitByColumnAndRow(4, $fd, $d5, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValue('F'.$fd, $d6);
        $i++;
        $fd++;  
    }
}
$r=$r . "</table>";
//CREA ARCHIVO************************************************************
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    //Guardar el achivo: 
    $objWriter->save($mipath2);
//CERRRAR CONEXION BASE
mssql_close();

echo $r;
?>