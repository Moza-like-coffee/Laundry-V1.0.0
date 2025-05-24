<?php
session_start();
include '../database/connect.php';

// Set timezone ke Indonesia
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk generate kode invoice
function generateInvoiceCode($mysqli) {
    $prefix = "KILAT" . date("Ymd") . "-";
    
    // Cari nomor urut terakhir hari ini
    $sql = "SELECT MAX(kode_invoice) as last_code FROM tb_transaksi WHERE kode_invoice LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $likePattern = $prefix . "%";
    $stmt->bind_param("s", $likePattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $lastNumber = 0;
    if ($row['last_code']) {
        $parts = explode("-", $row['last_code']);
        $lastNumber = intval(end($parts));
    }
    
    $newNumber = str_pad($lastNumber + 1, 3, "0", STR_PAD_LEFT);
    return $prefix . $newNumber;
}

try {
    // Validasi data yang diperlukan
    $requiredFields = ['nama-lengkap', 'tanggal', 'estimasi-laundry', 'status-pembayaran', 'status-pesanan', 'pajak'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    // Validasi detail transaksi
    if (!isset($_POST['details']) || !is_array($_POST['details']) || count($_POST['details']) === 0) {
        throw new Exception("Detail transaksi tidak boleh kosong");
    }

    // Generate kode invoice
    $kode_invoice = generateInvoiceCode($mysqli);

    // Hitung total sebelum diskon dan pajak
    $total = 0;
    foreach ($_POST['details'] as $detail) {
        // Validasi detail
        if (empty($detail['id_paket']) || empty($detail['qty']) || $detail['qty'] <= 0) {
            throw new Exception("Detail transaksi tidak valid");
        }

        // Ambil harga paket dari database
        $stmt = $mysqli->prepare("SELECT harga FROM tb_paket WHERE id = ?");
        $stmt->bind_param("i", $detail['id_paket']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Paket tidak ditemukan");
        }
        $paket = $result->fetch_assoc();
        $total += $paket['harga'] * $detail['qty'];
    }

    // Hitung diskon (dalam persen)
    $diskon = isset($_POST['diskon']) ? floatval($_POST['diskon']) : 0;
    $diskonAmount = $total * ($diskon / 100);

    // Hitung pajak (dalam persen)
    $pajak = isset($_POST['pajak']) ? floatval($_POST['pajak']) : 11;
    $pajakAmount = $total * ($pajak / 100);

    // Hitung biaya tambahan
    $biaya_tambahan = isset($_POST['biaya-tambahan']) ? floatval($_POST['biaya-tambahan']) : 0;

    // Hitung total akhir
    $total_akhir = $total - $diskonAmount + $pajakAmount + $biaya_tambahan;

    // Format tanggal dengan waktu (Indonesia timezone)
    $tgl = date('Y-m-d H:i:s', strtotime($_POST['tanggal'] . ' ' . date('H:i:s')));
    $batas_waktu = date('Y-m-d H:i:s', strtotime($_POST['estimasi-laundry'] . ' ' . date('H:i:s')));
    
    // Format tgl_bayar jika status pembayaran adalah 'dibayar'
    $tgl_bayar = null;
    if ($_POST['status-pembayaran'] === 'dibayar') {
        $paymentDate = $_POST['tanggal-pembayaran'] ? $_POST['tanggal-pembayaran'] : date('Y-m-d');
        $tgl_bayar = date('Y-m-d H:i:s', strtotime($paymentDate . ' ' . date('H:i:s')));
    }

    // Mulai transaksi database
    $mysqli->begin_transaction();

    try {
        // Insert ke tabel tb_transaksi
        $stmt = $mysqli->prepare("INSERT INTO tb_transaksi (
            id_outlet, kode_invoice, id_member, tgl, batas_waktu, tgl_bayar, 
            biaya_tambahan, diskon, pajak, status, dibayar, id_user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Gunakan id_outlet dari session atau null jika admin
        $id_outlet = ($_SESSION['role'] === 'admin') ? null : ($_SESSION['id_outlet'] ?? null);
        
        $stmt->bind_param(
            "isissssddssi",
            $id_outlet,
            $kode_invoice,
            $_POST['nama-lengkap'],
            $tgl,
            $batas_waktu,
            $tgl_bayar,
            $biaya_tambahan,
            $diskon,
            $pajak,
            $_POST['status-pesanan'],
            $_POST['status-pembayaran'],
            $_SESSION['user_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan transaksi: " . $stmt->error);
        }

        $id_transaksi = $mysqli->insert_id;

        // Insert detail transaksi
        foreach ($_POST['details'] as $detail) {
            $stmt = $mysqli->prepare("INSERT INTO tb_detail_transaksi (
                id_transaksi, id_paket, qty, keterangan
            ) VALUES (?, ?, ?, ?)");

            $keterangan = isset($detail['keterangan']) ? $detail['keterangan'] : '';
            
            $stmt->bind_param(
                "iids",
                $id_transaksi,
                $detail['id_paket'],
                $detail['qty'],
                $keterangan
            );

            if (!$stmt->execute()) {
                throw new Exception("Gagal menyimpan detail transaksi: " . $stmt->error);
            }
        }

        // Commit transaksi
        $mysqli->commit();

        // Set session success message
        $_SESSION['success_message'] = "Transaksi berhasil disimpan dengan kode invoice: $kode_invoice";
        echo json_encode([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'invoice_url' => 'invoice.php?id=' . $id_transaksi
        ]);
        exit();

    } catch (Exception $e) {
        // Rollback jika terjadi error
        $mysqli->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Tangani error dan redirect dengan pesan error
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: menu_transaksi.php?error=1");
    exit();
}