<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Transaksi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-gradient-to-b from-[#121B2B] to-[#1F3A5A] min-h-screen flex justify-center items-center px-4">
<form class="flex flex-col gap-4 text-sm w-full max-w-xs" id="formEditTransaksi" method="POST" action="save_edit_transaksi.php">
<div class="flex flex-col gap-3">
      <div class="flex flex-col gap-1">
        <label for="invoice" class="text-[13px] font-semibold">Nomor Invoice</label>
        <input id="invoice" name="invoice" type="text" required 
               class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" 
               placeholder="Masukkan nomor invoice" />
      </div>
      
      <div class="flex flex-col gap-1">
        <label for="payment-status" class="text-[13px] font-semibold">Status Pembayaran</label>
        <select id="payment-status" name="payment-status" required 
                class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none">
          <option value="" disabled selected>Pilih Status</option>
          <option value="dibayar">Dibayar</option>
          <option value="belum_dibayar">Belum Dibayar</option>
        </select>
      </div>
      
      <div class="flex flex-col gap-1">
        <label for="order-status" class="text-[13px] font-semibold">Status Pesanan</label>
        <select id="order-status" name="order-status" required 
                class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none">
          <option value="" disabled selected>Pilih Status</option>
          <option value="baru">Baru</option>
          <option value="proses">Proses</option>
          <option value="selesai">Selesai</option>
          <option value="diambil">Diambil</option>
        </select>
      </div>
    </div>

      <button type="submit" class="bg-green-200 hover:bg-green-300 border border-green-700 rounded-md w-20 h-10 text-sm font-normal text-black transition-colors">
         Simpan
    </button>
    </form>
  </section>
</body>
</html>