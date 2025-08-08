<?php
require __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran - Klinik Gigi</title>
  <link rel="stylesheet" href="../style.css">
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
      <h3>Klinik Gigi<br><small>Love Your Smile</small></h3>
      <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
      <button onclick="window.location.href='dashboard_resepsionis.php'">Daftar Pasien</button>
      <button onclick="window.location.href='antrian.php'">Pendaftaran Antrian</button>
      <button onclick="window.location.href='perawatan.php'">Perawatan</button>
      <button class="active">Pembayaran</button>
    </div>
    <div class="main" style="flex:1; padding:40px 32px; display:flex; flex-direction:column; align-items:center; justify-content:flex-start;">
      <h2>Riwayat Pembayaran</h2>
      <table style="width:100%; border-collapse:collapse; margin-bottom:25px;">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pasien</th>
            <th>Nama Resepsionis</th>
            <th>No Invoice</th>
            <th>Tanggal</th>
            <th>Pelayanan</th>
            <th>Obat</th>
            <th>Harga</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="pembayaranTableBody">
        </tbody>
      </table>
      <button class="add-btn" onclick="showPembayaranModal()" style="margin-top:16px;">Tambah Pembayaran</button>
      <div id="pembayaranModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Tambah Pembayaran</h3>
        <label>ID Rekam Medis</label><br>
        <select id="rekamMedisSelect" class="input-grey" onchange="loadDetailRekamMedis()">
          <option value="">-- Pilih Rekam Medis --</option>
        </select><br>
        <label>No Invoice</label><br>
        <input type="text" id="noInvoice" class="input-grey"><br>
        <label>Tanggal</label><br>
        <input type="date" id="tanggal" class="input-grey"><br>
        <select id="resepsionisSelect" class="input-grey">
          <option value="">-- Pilih Resepsionis --</option>
        </select><br>
        <button class="button-add" onclick="tambahPembayaran()">Simpan</button>
        <button class="button-hapus" onclick="closePembayaranModal()">Batal</button>
      </div>
    </div>
  </div>

<script>
  function showPembayaranModal() {
    // Load Rekam Medis
    fetch('../../functions/get_pembayaran.php?action=rekam_medis_list')
      .then(response => response.json())
      .then(data => {
        const select = document.getElementById('rekamMedisSelect');
        select.innerHTML = '<option value="">-- Pilih Rekam Medis --</option>';
        data.forEach(rm => {
          select.innerHTML += `<option value="${rm.id_rekam_medis}">RM${rm.id_rekam_medis} - ${rm.nama_pasien}</option>`;
        });
      });

    // Load Resepsionis
    fetch('../../functions/get_pembayaran.php?action=resepsionis_list')
      .then(response => response.json())
      .then(data => {
        const select = document.getElementById('resepsionisSelect');
        select.innerHTML = '<option value="">-- Pilih Resepsionis --</option>';
        data.forEach(r => {
          select.innerHTML += `<option value="${r.id}">${r.nama}</option>`;
        });
      });

    document.getElementById('pembayaranModal').style.display = 'block';
  }


  function closePembayaranModal() {
    document.getElementById('pembayaranModal').style.display = 'none';
  }

  function loadDetailRekamMedis() {
    const id_rekam_medis = document.getElementById('rekamMedisSelect').value;
    if (!id_rekam_medis) return;

    fetch(`../../functions/get_pembayaran.php?action=rekam_medis_full_detail&id_rekam_medis=${id_rekam_medis}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log(data)
          // Simpan data ini di JS untuk nanti disubmit saat "Simpan"
          window.currentRekamMedisDetail = data;
        }
      });
  }



function loadPembayaran() {
  fetch('../../functions/get_pembayaran.php?action=read')
  .then(response => response.json())
  .then(data => {
    const tbody = document.getElementById('pembayaranTableBody');
    tbody.innerHTML = '';
    data.forEach((pembayaran, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${pembayaran.nama_pasien}</td>
        <td>${pembayaran.nama_resepsionis}</td>
        <td>${pembayaran.no_invoice}</td>
        <td>${pembayaran.tanggal}</td>
        <td>${pembayaran.nama_pelayanan}</td>
        <td>${pembayaran.nama_obat}</td>
        <td>Rp ${parseInt(pembayaran.harga_total).toLocaleString('id-ID')}</td>
        <td><button class="button-hapus" onclick="hapusPembayaran(${pembayaran.id})">Hapus</button></td>
      `;
      tbody.appendChild(row);
    });
  });
}

function tambahPembayaran() {
    const id_rekam_medis = document.getElementById('rekamMedisSelect').value;
    const id_resepsionis = document.getElementById('resepsionisSelect').value;
    const noInvoice = document.getElementById('noInvoice').value;
    const tanggal = document.getElementById('tanggal').value;

    if (!id_rekam_medis || !id_resepsionis || !noInvoice || !tanggal) {
        alert('Semua data harus diisi!');
        return;
    }

    // Ambil detail Rekam Medis (id_pasien)
    fetch(`../../functions/get_pembayaran.php?action=rekam_medis_full_detail&id_rekam_medis=${id_rekam_medis}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const { id_pasien, id_pelayanan, kode_resep, harga_total } = data;

            // Lanjut insert ke transaksi

            fetch('../../functions/get_pembayaran.php?action=create', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id_pasien=${id_pasien}&id_resepsionis=${id_resepsionis}&id_rekam_medis=${id_rekam_medis}&id_pelayanan=${id_pelayanan}&kode_resep=${kode_resep}&no_invoice=${encodeURIComponent(noInvoice)}&tanggal=${tanggal}`
            })
            .then(response => response.text())  // <-- Ubah dari .json() ke .text()
            .then(result => {
                console.log(result);  // Tampilkan apapun yang dikembalikan (baik JSON atau error HTML)
                try {
                    const data = JSON.parse(result);
                    if (data.success) {
                        alert('Pembayaran berhasil ditambahkan');
                        closePembayaranModal();
                        loadPembayaran();
                    } else {
                        alert('Gagal menambahkan pembayaran: ' + data.message);
                    }
                } catch (e) {
                    alert('Response bukan JSON:\n' + result);
                }
            });
        } else {
            alert('Gagal mendapatkan detail rekam medis');
        }
    });
}


function hapusPembayaran(id) {
  if (confirm('Yakin ingin menghapus data ini?')) {
    fetch('../../functions/get_pembayaran.php?action=delete', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Pembayaran berhasil dihapus');
        loadPembayaran();
      } else {
        alert('Gagal menghapus data: ' + (data.message || 'Unknown error'));
        console.error(data);
      }
    })
    .catch(err => {
      alert('Terjadi kesalahan saat menghapus data.');
      console.error(err);
    });
  }
}


window.onload = loadPembayaran;
</script>
</body>
</html>
