<?php
require __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Resep Obat</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body>
  <div style="display: flex;flex-direction:column;">
  <nav class="navbar">
    <div class="navbar-left">
        <h2>Dokter</h2>
    </div>
    <div class="navbar-right">
        <button onclick="window.location.href='../../functions/logout.php'">Logout</button>
    </div>
  </nav>
      <div style="display: flex;">
        <div class="sidebar">
          <h3>Klinik Gigi<br><small>Love Your Smile</small></h3>
          <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
          <button onclick="window.location.href='pemeriksaan.php'">Antrian Pemeriksaan</button>
          <button onclick="window.location.href='rekammedis.php'">Rekam Medis</button>
          <button onclick="window.location.href='resepobat.php'">Resep Obat</button>
        </div>
      <div class="resep-layout" style="padding: 32px; flex:1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
        <h2 class="judul" style="margin-bottom: 20px;">Daftar Resep Obat</h2>
        <div class="resep-main">
          <form class="resep-form-panel" method="POST" action="../../functions/get_resep.php?action=simpan">
            <label for="dokterSelect" class="resep-label">Nama Dokter</label>
            <select id="dokterSelect" name="id_dokter" class="input-white">
              <option value="">-- Pilih Dokter --</option>
            </select><br>
            <label for="resep" class="resep-label">Resep Obat</label>
            <textarea id="resep" name="resep" class="resep-textarea" required></textarea>
            <button type="submit" class="resep-submit-btn">Tambahkan</button>
          </form>
        </div>
      </div>
  </div>
  <script>
    // Modify your existing tambahResep function
    function tambahResep(e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);

      fetch(form.action, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Resep berhasil ditambahkan!');
            window.location.reload(); // Reload the page
          } else {
            alert('Gagal menambahkan resep: ' + (data.message || ''));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengirim data');
        });
    }

    // Update your form to use the onsubmit handler
    document.querySelector('.resep-form-panel').addEventListener('submit', tambahResep);

    function loadDokterDropdown() {
      fetch('../../functions/get_rekam_medis.php?action=get_dokter')
        .then(response => response.json())
        .then(data => {
          console.log(data);
          const dokterSelect = document.getElementById('dokterSelect');
          dokterSelect.innerHTML = '';
          data.forEach(dokter => {
            const option = document.createElement('option');
            option.value = dokter.id;
            option.textContent = dokter.nama;
            dokterSelect.appendChild(option);
          });
        });
    }

    window.onload = function() {
      loadDokterDropdown();
    }
  </script>
</body>

</html>