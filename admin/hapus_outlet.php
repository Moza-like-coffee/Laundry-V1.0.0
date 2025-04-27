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
      <form class="flex flex-col gap-6 text-sm" id="formHapus">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <div class="flex flex-col gap-4">
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
        </div>

        <div class="flex justify-start">
        <button type="submit" class="bg-[#d81e2a] border border-black rounded-md px-6 py-2 text-sm font-normal">
         Hapus
        </button>
        </div>
      </form>
    </section>
  </div>
</body>
</html>
