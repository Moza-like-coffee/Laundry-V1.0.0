function initEditOutletForm() {
    document.getElementById('nama-outlet').addEventListener('change', function() {
      const outletId = this.value;
      if (outletId) {
        fetch('fetch_outlet_data.php?id=' + outletId)
          .then(response => response.json())
          .then(data => {
            document.getElementById('lokasi-outlet').value = data.alamat || '';
            document.getElementById('no-telp').value = data.tlp || '';
          })
          .catch(error => {
            console.error('Error fetching outlet data:', error);
          });
      } else {
        document.getElementById('lokasi-outlet').value = '';
        document.getElementById('no-telp').value = '';
      }
    });
  }
  