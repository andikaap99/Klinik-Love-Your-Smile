<?php
require __DIR__ . '/../../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dokter</title>
  <link rel="stylesheet" href="../style.css">
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
  <div style="display: flex; min-height: 100vh;">
    <div class="sidebar">
      <h3>Klinik Gigi<br><small>Love Your Smile</small></h3>
      <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
      <button onclick="window.location.href='pemeriksaan.php'"> Antrian Pemeriksaan</button>
      <button class="active">Rekam Medis</button>
      <button onclick="window.location.href='resepobat.php'">Resep Obat</button>
    </div>
    <div class="main" style="padding: 32px; flex:1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
      <h2 class="judul" style="margin-bottom: 20px;">Daftar Rekam Medis</h2>
      <div class="table-container">
        <table id="pasienTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Id Pemeriksaan</th>
              <th>Nama</th>
              <th>Nama Dokter</th>
              <th>Diagnosa</th>
              <th>Pelayanan</th>
              <th>Resep Obat</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <button class="add-btn" onclick="showAddModal()">Add Rekam</button>
      <div id="addModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Tambah Pemeriksaan</h3>
        <label>Id Pemeriksaan</label><br>
        <input type="text" id="idPemeriksaanInput" class="input-grey" readonly><br>
        <label>Id Pasien</label><br>
        <input type="text" id="namaPasienInput" class="input-grey" readonly><br>
        <label>Nama Dokter</label><br>
        <select id="dokterSelect" class="input-grey">
          <option value="">-- Pilih Dokter --</option>
        </select><br>
        <label>Diagnosa</label><br>
        <input type="text" id="diagnosa" class="input-grey"><br>

        <button onclick="nextToModal2()">Next</button>
        <button onclick="closeAddModal()">Batal</button>
      </div>

      <div id="addModal2" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Tambah Pemeriksaan</h3>
        <label>Nama Pelayanan</label><br>
        <select id="pelayananSelect2" class="input-grey">
          <option value="">-- Pilih Pelayanan --</option>
        </select><br>
        <label>Resep Obat</label><br>
        <select id="resepSelect" class="input-grey">
          <option value="">-- Pilih Resep --</option>
        </select><br>

        <button onclick="submitRekamMedis()">Simpan</button>
        <button onclick="closeAddModal2()">Batal</button>
      </div>

      <div id="editRekamModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Edit Rekam Medis</h3>
        <input type="hidden" id="editIdRekam">

        <label>Nama Dokter</label><br>
        <select id="editDokterSelect" class="input-grey">
          <option value="">-- Pilih Dokter --</option>
        </select><br>

        <label>Diagnosa</label><br>
        <input type="text" id="editDiagnosa" class="input-grey"><br><br>

        <button onclick="editRekamMedis()">Simpan</button>
        <button onclick="closeEditModal()">Batal</button>
      </div>

    </div>
  </div>
  <script>
    let tempDataModal1 = {}; // Temporary storage

    function nextToModal2() {
      const id_pemeriksaan = document.getElementById('idPemeriksaanInput').value;
      const id_dokter = document.getElementById('dokterSelect').value;
      const diagnosa = document.getElementById('diagnosa').value;

      if (!id_pemeriksaan || !id_dokter || !diagnosa) {
        alert('Semua data harus diisi!');
        return;
      }

      // Simpan data sementara
      tempDataModal1 = {
        id_pemeriksaan,
        id_dokter,
        diagnosa
      };

      // Tutup Modal 1, Buka Modal 2
      closeAddModal();
      showAddModal2();
    }

    function showAddModal2() {
      loadPelayananDropdown2();
      loadResepDropdown();
      document.getElementById('addModal2').style.display = 'block';
    }


    function loadRekamMedis() {
      fetch('../../functions/get_rekam_medis.php?action=read')
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#pasienTable tbody');
          tbody.innerHTML = '';

          if (data.length === 0) {
            const emptyRow = document.createElement('tr');
            const emptyCell = document.createElement('td');
            emptyCell.setAttribute('colspan', '6');
            emptyCell.style.textAlign = 'center';
            emptyCell.textContent = 'Tidak ada data rekam medis.';
            emptyRow.appendChild(emptyCell);
            tbody.appendChild(emptyRow);
            return;
          }

          data.forEach((item, index) => {
            const row = document.createElement('tr');

            // Kolom No
            const tdNo = document.createElement('td');
            tdNo.textContent = index + 1;
            row.appendChild(tdNo);

            // Kolom Id Pemeriksaan
            const tdIdPemeriksaan = document.createElement('td');
            tdIdPemeriksaan.textContent = item.id_pemeriksaan;
            row.appendChild(tdIdPemeriksaan);

            // Kolom Nama Pasien
            const tdNamaPasien = document.createElement('td');
            tdNamaPasien.textContent = item.nama_pasien;
            row.appendChild(tdNamaPasien);

            // Kolom Nama Dokter
            const tdNamaDokter = document.createElement('td');
            tdNamaDokter.textContent = item.nama_dokter;
            row.appendChild(tdNamaDokter);

            // Kolom Diagnosa
            const tdDiagnosa = document.createElement('td');
            tdDiagnosa.textContent = item.diagnosa;
            row.appendChild(tdDiagnosa);

            // Kolom Pelyanan
            const tdPelayanan = document.createElement('td');
            tdPelayanan.textContent = item.nama_pelayanan;
            row.appendChild(tdPelayanan);

            // Kolom Obat
            const tdObat = document.createElement('td');
            tdObat.textContent = item.resep;
            row.appendChild(tdObat);

            // Kolom Action (Edit & Hapus)
            const tdAction = document.createElement('td');

            const btnHapus = document.createElement('button');
            btnHapus.textContent = 'Hapus';
            btnHapus.onclick = function() {
              hapusRekamMedis(item.id);
            };

            btnHapus.classList.add('button-hapus');

            tdAction.appendChild(btnHapus);
            row.appendChild(tdAction);

            tbody.appendChild(row);
          });
        })
        .catch(error => {
          console.error('Gagal memuat data:', error);
        });
    }

    function loadDokterDropdown() {
      fetch('../../functions/get_rekam_medis.php?action=get_dokter')
        .then(response => response.json())
        .then(data => {
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

    function loadResepDropdown() {
      fetch('../../functions/get_resep.php?action=get_resep')
        .then(response => response.json())
        .then(data => {
          const resepSelect = document.getElementById('resepSelect');
          resepSelect.innerHTML = '';
          data.forEach(resep => {
            const option = document.createElement('option');
            option.value = resep.kode_resep;
            option.textContent = resep.kode_resep;
            resepSelect.appendChild(option);
          });
        });
    }


    function loadPelayananDropdown() {
      fetch('../../functions/get_rekam_medis.php?action=get_pelayanan')
        .then(response => response.json())
        .then(data => {
          pelayananSelect2.innerHTML = '';
          data.forEach(pelayanan => {
            const option = document.createElement('option');
            option.value = pelayanan.id;
            option.textContent = pelayanan.nama_pelayanan;
            pelayananSelect2.appendChild(option);
          });
        });
    }

    function submitRekamMedis() {
      const id_pelayanan = document.getElementById('pelayananSelect2').value;
      const kode_resep = document.getElementById('resepSelect').value;
      alert(kode_resep);
      if (!id_pelayanan || !kode_resep) {
        alert('Pilih pelayanan dan obat!');
        return;
      }

      const payload = {
        ...tempDataModal1, // Data dari Modal 1
        id_pelayanan,
        kode_resep
      };
      console.log(payload);
      // Kirim ke backend (gabungan data modal 1 dan 2)
      fetch('../../functions/get_rekam_medis.php?action=add_full', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `id_pemeriksaan=${payload.id_pemeriksaan}&id_dokter=${payload.id_dokter}&diagnosa=${encodeURIComponent(payload.diagnosa)}&id_pelayanan=${payload.id_pelayanan}&kode_resep=${payload.kode_resep}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Data berhasil disimpan');
            closeAddModal2();
            loadRekamMedis();
          } else {
            alert(data.message);
          }
        });
    }

    function loadPelayananDropdown2() {
      fetch('../../functions/get_rekam_medis.php?action=get_pelayanan')
        .then(response => response.json())
        .then(data => {
          const pelayananSelect2 = document.getElementById('pelayananSelect2');
          pelayananSelect2.innerHTML = '';
          data.forEach(pelayanan => {
            const option = document.createElement('option');
            option.value = pelayanan.id;
            option.textContent = pelayanan.nama_pelayanan;
            pelayananSelect2.appendChild(option);
          });
        });
    }

    function closeAddModal2() {
      document.getElementById('addModal2').style.display = 'none';
    }


    function addRekamMedis() {
      const id_pemeriksaan = document.getElementById('idPemeriksaanInput').value;
      const id_dokter = document.getElementById('dokterSelect').value;
      const id_pelayanan = document.getElementById('pelayananSelect').value;
      const id_obat = document.getElementById('obatSelect').value;
      const diagnosa = document.getElementById('diagnosa').value;

      console.log({
        id_pemeriksaan,
        id_dokter,
        id_pelayanan,
        diagnosa
      });

      fetch('../../functions/get_rekam_medis.php?action=add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `id_pemeriksaan=${id_pemeriksaan}&id_dokter=${id_dokter}&id_pelayanan=${id_pelayanan}&id_obat=${id_obat}&diagnosa=${encodeURIComponent(diagnosa)}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Data berhasil ditambahkan');
            loadRekamMedis();
            closeAddModal();
          } else {
            alert('Gagal menambahkan data');
          }
        });
    }

    function addRekamMedis2() {
      const id_pelayanan = document.getElementById('pelayananSelect2').value;
      const id_obat = document.getElementById('obatSelect').value;

      if (!id_pelayanan || !id_obat) {
        alert('Pilih pelayanan dan obat!');
        return;
      }

      fetch('../../functions/get_rekam_medis.php?action=add2', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `id_pelayanan=${id_pelayanan}&id_obat=${id_obat}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Data berhasil ditambahkan');
            loadRekamMedis();
            closeAddModal2();
          } else {
            alert('Gagal menambahkan data');
          }
        });
    }


    function hapusRekamMedis(id) {
      if (confirm('Yakin ingin menghapus data ini?')) {
        fetch('../../functions/get_rekam_medis.php?action=delete', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Data berhasil dihapus');
              loadRekamMedis();
            } else {
              alert('Gagal menghapus data');
            }
          });
      }
    }


    function showAddModal() {
      fetch('../../functions/get_rekam_medis.php?action=get_pemeriksaan')
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            const pemeriksaan = data[0]; // Karena hanya 1 yang statusnya "Sedang Diperiksa"
            document.getElementById('idPemeriksaanInput').value = pemeriksaan.id;
            document.getElementById('namaPasienInput').value = pemeriksaan.nama;
            document.getElementById('addModal').style.display = 'block';
          } else {
            alert('Tidak ada pemeriksaan yang sedang diperiksa.');
          }
        });
    }

    function closeAddModal() {
      document.getElementById('addModal').style.display = 'none';
    }
   
    window.onload = function() {
      loadRekamMedis();
      loadDokterDropdown();
      loadPelayananDropdown();
      loadResepDropdown();
    };
  </script>
</body>

</html>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pemeriksaan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbar">Pemeriksaan</div>
  <div style="display: flex; min-height: 100vh;">
    <div class="sidebar">
      <h3>Klinik Gigi<br><small>Love Your Smile</small></h3>
      <div class="avatar">X</div>
      <button onclick="window.location.href='dokter.html'">Resep Obat</button>
      <button class="active">Rekam Medis</button>
      <button onclick="window.location.href='resep.html'">Resep Obat</button>
    </div>
    <div class="main">
      <div class="judul">Antrian Saat Ini</div>
      <div class="antrian-card">
        <div class="nama">Nama Pasien</div>
        <div class="keluhan">Keluhan : Lorem ipsum dolores dolor kolor ???</div>
        <div class="antrian">Dalam Antrian<br>No. Antrian #</div>
      </div>
      <button class="selesai-btn" onclick="selesaiPemeriksaan()">Pemeriksaan Selesai</button>
    </div>
  </div>
  <script>
    function selesaiPemeriksaan() {
      alert("Pemeriksaan selesai!");
    }
  </script>
</body>
</html> -->