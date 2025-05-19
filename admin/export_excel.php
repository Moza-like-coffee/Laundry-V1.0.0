<?php
require '../vendor/autoload.php'; 
include '../database/connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil parameter filter yang sama dengan halaman laporan
$tanggalAwal = isset($_GET['tanggal-awal']) ? $_GET['tanggal-awal'] : '';
$tanggalAkhir = isset($_GET['tanggal-akhir']) ? $_GET['tanggal-akhir'] : '';
$outlet = isset($_GET['outlet']) ? $_GET['outlet'] : '';
$kasir = isset($_GET['petugas']) ? $_GET['petugas'] : '';
$statusPesanan = isset($_GET['status-pesanan']) ? $_GET['status-pesanan'] : '';
$statusPembayaran = isset($_GET['status-pembayaran']) ? $_GET['status-pembayaran'] : '';

$sqlLaporan = "SELECT 
    t.id, 
    t.kode_invoice, 
    t.tgl, 
    t.tgl_bayar, 
    t.status, 
    t.dibayar, 
    o.nama as outlet, 
    u.nama as kasir,
    COALESCE(SUM(td.qty * p.harga), 0) as total
  FROM tb_transaksi t
  JOIN tb_outlet o ON t.id_outlet = o.id
  JOIN tb_user u ON t.id_user = u.id
  LEFT JOIN tb_detail_transaksi td ON t.id = td.id_transaksi
  LEFT JOIN tb_paket p ON td.id_paket = p.id
  WHERE 1=1";
  
if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
    $sqlLaporan .= " AND t.tgl BETWEEN '$tanggalAwal' AND '$tanggalAkhir'";
} elseif (!empty($tanggalAwal)) {
    $sqlLaporan .= " AND t.tgl >= '$tanggalAwal'";
} elseif (!empty($tanggalAkhir)) {
    $sqlLaporan .= " AND t.tgl <= '$tanggalAkhir'";
}

if (!empty($outlet)) {
    $sqlLaporan .= " AND t.id_outlet = '$outlet'";
}
if (!empty($kasir)) {
    $sqlLaporan .= " AND t.id_user = '$kasir'";
}
if (!empty($statusPesanan)) {
    $sqlLaporan .= " AND t.status = '$statusPesanan'";
}
if (!empty($statusPembayaran)) {
    $sqlLaporan .= " AND t.dibayar = '$statusPembayaran'";
}

$sqlLaporan .= " GROUP BY t.id";
$resultLaporan = mysqli_query($mysqli, $sqlLaporan);

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header laporan
$sheet->setCellValue('A1', 'LAPORAN TRANSAKSI');
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Informasi filter
$sheet->setCellValue('A2', 'Periode:');
$sheet->setCellValue('B2', date('d M Y', strtotime($tanggalAwal)) . ' - ' . date('d M Y', strtotime($tanggalAkhir)));
$sheet->setCellValue('A3', 'Outlet:');
$sheet->setCellValue('B3', !empty($outlet) ? $outletName : 'Semua Outlet');

// Header tabel
$sheet->setCellValue('A5', 'No. Invoice');
$sheet->setCellValue('B5', 'Tanggal');
$sheet->setCellValue('C5', 'Outlet');
$sheet->setCellValue('D5', 'Kasir');
$sheet->setCellValue('E5', 'Status Pesanan');
$sheet->setCellValue('F5', 'Status Pembayaran');
$sheet->setCellValue('G5', 'Total');

// Style untuk header tabel
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => 'center'],
    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9D9D9']]
];
$sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

// Isi data
$row = 6;
$totalPendapatan = 0;

while ($transaksi = mysqli_fetch_assoc($resultLaporan)) {
    $sheet->setCellValue('A' . $row, $transaksi['kode_invoice']);
    $sheet->setCellValue('B' . $row, date('d M Y', strtotime($transaksi['tgl'])));
    $sheet->setCellValue('C' . $row, $transaksi['outlet']);
    $sheet->setCellValue('D' . $row, $transaksi['kasir']);
    
    $statusPesanan = '';
    switch ($transaksi['status']) {
        case 'baru': $statusPesanan = 'Baru'; break;
        case 'proses': $statusPesanan = 'Proses'; break;
        case 'selesai': $statusPesanan = 'Selesai'; break;
        case 'diambil': $statusPesanan = 'Diambil'; break;
        default: $statusPesanan = $transaksi['status'];
    }
    $sheet->setCellValue('E' . $row, $statusPesanan);
    
    $statusBayar = ($transaksi['dibayar'] == 'dibayar') ? 'Dibayar' : 'Belum Dibayar';
    if ($transaksi['dibayar'] == 'dibayar' && !empty($transaksi['tgl_bayar'])) {
        $statusBayar .= "\n(" . date('d M Y', strtotime($transaksi['tgl_bayar'])) . ")";
    }
    $sheet->setCellValue('F' . $row, $statusBayar);
    $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
    
    $sheet->setCellValue('G' . $row, $transaksi['total']);
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    $totalPendapatan += $transaksi['total'];
    $row++;
}

// Total pendapatan
$sheet->setCellValue('F' . $row, 'TOTAL PENDAPATAN:');
$sheet->setCellValue('G' . $row, $totalPendapatan);
$sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
$sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');

// Auto size kolom
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Border untuk data
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => 'thin'
        ]
    ]
];
$sheet->getStyle('A5:G' . ($row-1))->applyFromArray($dataStyle);

// Set nama file
$filename = 'Laporan_Transaksi_' . date('Ymd_His') . '.xlsx';

// Redirect output to a client's web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;