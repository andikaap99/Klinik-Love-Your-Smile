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

  <div style="display: flex; height: 100vh">
    <div class="sidebar">
      <h3>Klinik Gigi<br /><small>Love Your Smile</small></h3>
      <img src="../image/happy.png" title="smile icons" style= "width:124px"></img>
      <button class="active">Daftar Pasien</button>
      <button onclick="window.location.href='antrian.php'">Pendaftaran Antrian</button>
      <button onclick="window.location.href='perawatan.php'">
        Perawatan
      </button>
      <button onclick="window.location.href='pembayaran.php'">
        Pembayaran
      </button>
    </div>

    <div class="main" style="
          flex: 1;
          padding: 40px 32px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: flex-start;
        ">
      <h2>Daftar Pasien</h2>
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>No. Telp</th>
            <th>Alamat</th>
          </tr>
        </thead>
        <tbody id="pasienTableBody"></tbody>
      </table>
      <button class="add-btn" onclick="showPasienModal()" style="margin-top: 16px">Tambah Pasien</button>
      <div id="pasienModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Tambah Pasien</h3>
        <label>Nama Pasien</label><br>
        <input type="text" id="namaPasien" class="input-grey"><br>
        <label>No. Telp</label><br>
        <input type="text" id="noTelp" class="input-grey"><br>
        <label>Alamat</label><br>
        <input type="text" id="alamatPasien" class="input-grey"><br><br>

        <button onclick="tambahPasien()">Simpan</button>
        <button onclick="closePasienModal()">Batal</button>
      </div>
    </div>
  </div>

  <script>
    // Load data pasien dengan XMLHttpRequest (AJAX Native)
    function loadPasien() {
      var xhr = new XMLHttpRequest();
      xhr.open("GET", "../../functions/get_pasien.php", true);
      xhr.onload = function() {
        if (this.status === 200) {
          var data = JSON.parse(this.responseText);
          var tbody = document.getElementById("pasienTableBody");
          tbody.innerHTML = ""; // Kosongkan isi tbody dulu

          data.forEach(function(pasien, index) {
            var row = document.createElement("tr");

            // Kolom No (index + 1)
            var tdNo = document.createElement("td");
            tdNo.textContent = index + 1;
            row.appendChild(tdNo);

            // Kolom Nama
            var tdNama = document.createElement("td");
            tdNama.textContent = pasien.nama;
            row.appendChild(tdNama);

            // Kolom No. Telp
            var tdTelp = document.createElement("td");
            tdTelp.textContent = pasien.no_telp;
            row.appendChild(tdTelp);

            // Kolom Alamat
            var tdAlamat = document.createElement("td");
            tdAlamat.textContent = pasien.alamat;
            row.appendChild(tdAlamat);

            // Tambahkan row ke tbody
            tbody.appendChild(row);
          });
        } else {
          console.error("Gagal memuat data pasien.");
        }
      };
      xhr.send();
    }

    function showPasienModal() {
      document.getElementById('pasienModal').style.display = 'block';
    }

    function closePasienModal() {
      document.getElementById('pasienModal').style.display = 'none';
    }

    function tambahPasien() {
      const nama = document.getElementById('namaPasien').value;
      const noTelp = document.getElementById('noTelp').value;
      const alamatPasien = document.getElementById('alamatPasien').value;

      if (nama && noTelp && alamatPasien) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../../functions/get_pasien.php?action=create", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
          if (this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
              alert("Berhasil menambahkan Pasien");
              closePasienModal();
              loadPasien(); // Refresh data tabel pasien dari database
              // Reset input
              document.getElementById('namaPasien').value = '';
              document.getElementById('noTelp').value = '';
              document.getElementById('alamatPasien').value = '';
            } else {
              alert("Gagal menambahkan pasien: " + response.message);
            }
          } else {
            alert("Gagal menghubungi server.");
          }
        };

        xhr.send("nama=" + encodeURIComponent(nama) + "&no_telp=" + encodeURIComponent(noTelp) + "&alamat=" + encodeURIComponent(alamatPasien));
      } else {
        alert('Semua data harus diisi!');
      }
    }

    // Load data saat halaman selesai dimuat
    window.onload = loadPasien;
  </script>
</body>

</html>