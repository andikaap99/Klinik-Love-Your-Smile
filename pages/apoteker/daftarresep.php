<?php
require __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Pemeriksaan</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .antrian-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #f0f0f0;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 16px;
    }

    .antrian-info {
      flex: 1;
    }

    .selesai-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .selesai-btn:hover {
      background-color: #45a049;
    }
  </style>
</head>

<body>
<nav class="navbar">
    <div class="navbar-left">
        <h2>Apoteker</h2>
    </div>
    <div class="navbar-right">
        <button onclick="window.location.href='../../functions/logout.php'">Logout</button>
    </div>
  </nav>
  <div style="display: flex;">
    <div class="sidebar">
      <h3>Klinik Gigi<br><small>Love Your Smile</small></h3>
      <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
      <button onclick="window.location.href='daftarresep.php'">Daftar Resep</button>
    </div>
    <div class="main" style="padding: 32px; flex:1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
      <h2 class="judul" style="margin-bottom: 20px;">Daftar Resep Obat</h2>
      <div id="antrianContainer" style="width: 100%; max-width: 800px;"></div>

      <div id="obatModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Detail Obat</h3>
        <label>Kode Resep</label><br>
        <input type="text" id="kode_resep" class="input-grey" readonly><br>
        <label>Obat</label><br>
        <input type="text" id="obat" class="input-grey" readonly><br>
        <label>Harga</label><br>
        <input type="text" id="harga" class="input-grey"><br>

        <button class="button-add" onclick="konfirmasiObat()">Simpan</button>
        <button class="button-hapus" onclick="closeModal()">Batal</button>
      </div>
    </div>
  </div>

  <script>
    function loadAntrian() {
      fetch('../../functions/get_resep.php?action=read')
        .then(response => response.json())
        .then(data => {
          const container = document.getElementById('antrianContainer');
          container.innerHTML = '';

          if (data.length === 0) {
            container.innerHTML = '<p>Tidak ada antrian saat ini.</p>';
            return;
          }

          data.forEach((item, index) => {
            console.log(item);
            const card = document.createElement('div');
            card.className = 'antrian-card';

            const infoDiv = document.createElement('div');
            infoDiv.className = 'antrian-info';
            infoDiv.innerHTML = `
              <div class="nama">Dokter: ${item.nama_dokter}</div>
              <div class="kode_resep">Kode Resep: #${item.kode_resep}</div>
              <div class="resep">Obat: ${item.resep}</div>
              <div class="pasien">Atas Nama: ${item.nama_pasien}</div>
              <div class="antrian">Status: ${item.status}</div>
            `;

            const btn = document.createElement('button');
            btn.className = 'selesai-btn';
            btn.innerText = 'Detail';
            btn.onclick = function() {
              openModal(item.id);
            };

            card.appendChild(infoDiv);
            card.appendChild(btn);

            container.appendChild(card);
          });
        });
    }

    let currentResepId = null;

    function openModal(id) {
      fetch(`../../functions/get_resep.php?action=detail&id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('kode_resep').value = data.resep.kode_resep;
            document.getElementById('obat').value = data.resep.resep;
            document.getElementById('harga').value = '';
            currentResepId = id;
            document.getElementById('obatModal').style.display = 'block';
          } else {
            alert('Resep tidak ditemukan');
          }
        });
    }

    function closeModal() {
      document.getElementById('obatModal').style.display = 'none';
    }

    function konfirmasiObat() {
      const harga = document.getElementById('harga').value.trim();

      if (!harga) {
        alert('Harga tidak boleh kosong!');
        return;
      }

      if (!confirm('Apakah Anda yakin ingin menyelesaikan resep ini?')) {
        return;
      }

      fetch('../../functions/get_resep.php?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(currentResepId) + '&harga=' + encodeURIComponent(harga)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeModal();
          loadAntrian();
        } else {
          alert('Gagal menyelesaikan resep.');
        }
      });
    }

    window.onload = loadAntrian;
  </script>
</body>

</html>