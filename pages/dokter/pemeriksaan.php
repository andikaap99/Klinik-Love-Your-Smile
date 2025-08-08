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
      <button class="active">Antrian Pemeriksaan</button>
      <button onclick="window.location.href='rekammedis.php'">Rekam Medis</button>
      <button onclick="window.location.href='resepobat.php'">Resep Obat</button>
    </div>
    <div class="main" style="padding: 32px; flex:1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
      <h2 class="judul" style="margin-bottom: 20px;">Antrian Saat Ini</h2>
      <div id="antrianContainer" style="width: 100%; max-width: 800px;"></div>
    </div>
  </div>

  <script>
    function loadAntrian() {
      fetch('../../functions/get_pemeriksaan.php?action=read')
        .then(response => response.json())
        .then(data => {
          const container = document.getElementById('antrianContainer');
          container.innerHTML = '';

          if (data.length === 0) {
            container.innerHTML = '<p>Tidak ada antrian saat ini.</p>';
            return;
          }

          data.forEach((item, index) => {
            const card = document.createElement('div');
            card.className = 'antrian-card';

            const infoDiv = document.createElement('div');
            infoDiv.className = 'antrian-info';
            infoDiv.innerHTML = `
              <div class="nama">${item.nama}</div>
              <div class="keluhan">Keluhan: ${item.keluhan}</div>
              <div class="antrian">${item.status}<br>No. Antrian #${item.no_antrian}</div>
            `;

            const btn = document.createElement('button');
            btn.className = 'selesai-btn';
            btn.innerText = 'Masuk Pemeriksaan';
            btn.onclick = function() {
              selesaiPemeriksaan(item.id);
            };

            card.appendChild(infoDiv);
            card.appendChild(btn);

            container.appendChild(card);
          });
        });
    }

    function selesaiPemeriksaan(id) {
      fetch('../../functions/get_pemeriksaan.php?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Dipanggil oleh Dokter. Pemeriksaan dimulai!');
          loadAntrian(); 
        } else {
          alert('Gagal menyelesaikan pemeriksaan.');
        }
      });
    }

    window.onload = loadAntrian;
  </script>
</body>
</html>
