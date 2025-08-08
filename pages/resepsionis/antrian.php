<?php
require __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Resepsionis - Klinik Gigi</title>
  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <nav class="navbar">
    <div class="navbar-left">
        <h2>Resepsionis</h2>
    </div>
    <div class="navbar-right">
        <button onclick="window.location.href='../../functions/logout.php'">Logout</button>
    </div>
  </nav>

  <div style="display: flex;">
    <div class="sidebar">
      <h3>Klinik Gigi<br /><small>Love Your Smile</small></h3>
      <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
      <button onclick="window.location.href='dashboard_resepsionis.php'">Daftar Pasien</button>
      <button class="active">Pendaftaran Antrian</button>
      <button onclick="window.location.href='perawatan.php'">Perawatan</button>
      <button onclick="window.location.href='pembayaran.php'">Pembayaran</button>
    </div>

    <div class="main" style="flex: 1; padding: 40px 32px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
      <h2>Pendaftaran Antrian</h2>

      <div style="width: 100%; max-width: 500px; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <label for="pasienSelect">Nama Pasien</label><br>
        <select id="pasienSelect" class="input-grey" style="width: 100%; margin-bottom: 16px;">
          <option value="">-- Pilih Pasien --</option>
          <!-- Data pasien akan dimuat dari database -->
        </select>

        <label for="diagnosaInput">Keluhan</label><br>
        <input type="text" id="diagnosaInput" class="input-grey" style="width: 100%; margin-bottom: 16px;"><br>

        <label for="noAntrianInput">No Antrian</label><br>
        <input type="text" id="noAntrianInput" class="input-grey" style="width: 100%; margin-bottom: 16px;"><br>

        <button class="add-btn" onclick="submitAntrian()">Simpan</button>
      </div>
    </div>
  </div>

  <script>
    function loadPasienDropdown() {
      fetch('../../functions/get_antrian.php?action=get_pasien')
        .then(response => response.json())
        .then(data => {
            const pasienSelect = document.getElementById('pasienSelect');
            pasienSelect.innerHTML = '';
            data.forEach(pasien => {
                const option = document.createElement('option');
                option.value = pasien.id;
                option.textContent = pasien.nama;
                pasienSelect.appendChild(option);
            });
        });
    }

    function submitAntrian() {
      const pasienId = document.getElementById('pasienSelect').value;
      const diagnosa = document.getElementById('diagnosaInput').value.trim();
      const noAntrian = document.getElementById('noAntrianInput').value.trim();

      if (!pasienId || !diagnosa || !noAntrian) {
        alert('Semua data harus diisi!');
        return;
      }

      fetch('../../functions/get_antrian.php?action=create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_pasien=${encodeURIComponent(pasienId)}&keluhan=${encodeURIComponent(diagnosa)}&no_antrian=${encodeURIComponent(noAntrian)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Antrian berhasil ditambahkan');
          // Optional: Reset input setelah submit
          document.getElementById('pasienSelect').value = '';
          document.getElementById('diagnosaInput').value = '';
          document.getElementById('noAntrianInput').value = '';
        } else {
          alert('Gagal menambahkan antrian: ' + data.message);
        }
      })
      .catch(error => {
        alert('Terjadi kesalahan saat mengirim data');
        console.error(error);
      });
    }

    window.onload = function() {
      loadPasienDropdown();
    };
  </script>
</body>

</html>
