<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_GET['term'])) {
    $term = strtolower($_GET['term']);
    $reader = new Xlsx();
    $spreadsheet = $reader->load('PAARC1.xlsx');
    $sheet = $spreadsheet->getSheet(0);

    $results = [];
    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $rowData = [];
        foreach ($cellIterator as $cell) {
            $rowData[] = $cell->getValue();
        }
        if (strpos(strtolower($rowData[1]), $term) !== false) { // Match Name
            $results[] = [
                "id" => $rowData[0],
                "label" => $rowData[1],
                "department" => $rowData[2],
                "email" => $rowData[3],
                "phone" => $rowData[4]
            ];
        }
    }
    echo json_encode($results);
}
?>
