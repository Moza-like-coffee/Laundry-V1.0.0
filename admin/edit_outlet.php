<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Outlet Edit Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
  <div class="flex">
    <section class="bg-white rounded-md p-6 w-full max-w-5xl">
      <form class="flex flex-col gap-6 text-sm" id="editForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <!-- Data Lama -->
          <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Lama</h2>
            <div class="flex flex-col gap-3">
              <label for="nama-outlet" class="text-[13px] font-semibold">Nama Outlet</label>
              <select id="nama-outlet" name="nama-outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required>
                <option value="" disabled selected>Pilih Outlet</option>
              </select>
            </div>
            <div class="flex flex-col gap-3">
              <label for="lokasi-outlet" class="text-[13px] font-semibold">Lokasi Outlet</label>
              <input type="text" id="lokasi-outlet" name="lokasi-outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required />
            </div>
            <div class="flex flex-col gap-3">
              <label for="no-telp" class="text-[13px] font-semibold">No. Telp</label>
              <input type="text" id="no-telp" name="no-telp" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required />
            </div>
          </div>

          <!-- Data Baru -->
          <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Baru</h2>
            <div class="flex flex-col gap-3">
              <label for="nama-outlet-baru" class="text-[13px] font-semibold">Nama Outlet Baru</label>
              <input type="text" id="nama-outlet-baru" name="nama-outlet-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required />
            </div>
            <div class="flex flex-col gap-3">
              <label for="lokasi-outlet-baru" class="text-[13px] font-semibold">Lokasi Outlet Baru</label>
              <input type="text" id="lokasi-outlet-baru" name="lokasi-outlet-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required />
            </div>
            <div class="flex flex-col gap-3">
              <label for="no-telp-baru" class="text-[13px] font-semibold">No. Telp Baru</label>
              <input type="text" id="no-telp-baru" name="no-telp-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required />
            </div>
          </div>
        </div>

        <button type="submit" class="self-start bg-[#D1E7CD] border border-black rounded-md px-6 py-2 text-sm font-normal mt-4 hover:bg-green-300">
          Edit
        </button>
      </form>
    </section>
  </div>

</body>
</html>
