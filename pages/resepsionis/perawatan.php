<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Daftar Perawatan - Klinik Gigi</title>
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
      <button onclick="window.location.href='dashboard_resepsionis.php'">
        Daftar Pasien
      </button>
      <button onclick="window.location.href='antrian.php'">Pendaftaran Antrian</button>
      <button class="active">Perawatan</button>
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
      <h2>Daftar Jenis Perawatan</h2>
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Perawatan</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="perawatanTableBody">
        </tbody>
      </table>
      <button class="add-btn" onclick="showPerawatanModal()" style="margin-top: 16px">Tambah Perawatan</button>
      <div id="perawatanModal" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:#fff; padding:24px; border:1px solid #ccc; z-index:100; border-radius:8px; min-width:320px;">
        <h3>Tambah Perawatan</h3>
        <label>Nama Perawatan</label><br>
        <input type="text" id="namaPerawatan" class="input-grey"><br>
        <label>Harga</label><br>
        <input type="text" id="harga" class="input-grey"><br>
        <label>Deskripsi</label><br>
        <input type="text" id="desc" class="input-grey"><br><br>

        <button class="button-add" onclick="tambahPerawatan()">Simpan</button>
        <button class="button-hapus" onclick="closePerawatanModal()">Batal</button>
      </div>
    </div>
  </div>
  <script>
    function loadPerawatan() {
      var xhr = new XMLHttpRequest();
      xhr.open("GET", "../../functions/get_perawatan.php", true);
      xhr.onload = function() {
        if (this.status === 200) {
          var data = JSON.parse(this.responseText);
          var tbody = document.getElementById("perawatanTableBody");
          tbody.innerHTML = '';
          data.forEach(function(perawatan, index) {
            var row = document.createElement("tr");

            // Kolom No (index + 1)
            var tdNo = document.createElement("td");
            tdNo.textContent = index + 1;
            row.appendChild(tdNo);

            // Kolom Nama
            var tdNama = document.createElement("td");
            tdNama.textContent = perawatan.nama_pelayanan;
            row.appendChild(tdNama);

            // Kolom Harga
            var tdHarga = document.createElement("td");
            // Format Harga
            var hargaFormat = new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              minimumFractionDigits: 0
            }).format(perawatan.harga);
            tdHarga.textContent = hargaFormat;
            row.appendChild(tdHarga);

            // Kolom Deskripsi
            var tdDeskripsi = document.createElement("td");
            tdDeskripsi.textContent = perawatan.deskripsi;
            row.appendChild(tdDeskripsi);

            // Kolom Aksi
            var tdAksi = document.createElement("td");
            var tombolHapus = document.createElement("button");

            tombolHapus.textContent = "Hapus";
            tombolHapus.classList.add("btn-hapus");
            tombolHapus.onclick = function() {
              hapusPerawatan(perawatan.id);
            };

            tdAksi.appendChild(tombolHapus);
            row.appendChild(tdAksi);

            // Tambahkan row ke tbody
            tbody.appendChild(row);
          });
        } else {
          console.error("Gagal memuat data perawatan.");
        }
      };
      xhr.send();
    }

    function showPerawatanModal() {
      document.getElementById('perawatanModal').style.display = 'block';
    }

    function closePerawatanModal() {
      document.getElementById('perawatanModal').style.display = 'none';
    }

    function tambahPerawatan() {
      const nama = document.getElementById('namaPerawatan').value;
      const harga = document.getElementById('harga').value;
      const desc = document.getElementById('desc').value;

      if (nama && harga && desc) {
        fetch('../../functions/get_perawatan.php?action=create', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'nama_pelayanan=' + encodeURIComponent(nama) + '&harga=' + encodeURIComponent(harga) + '&deskripsi=' + encodeURIComponent(desc)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Data berhasil ditambahkan');
            closePerawatanModal();
            loadPerawatan();
            document.getElementById('namaPerawatan').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('desc').value = '';
          } else {
            alert('Gagal menambahkan data');
          }
        });
      } else {
        alert('Semua data harus diisi!');
      }
    }

    function hapusPerawatan(id) {
      if (confirm("Yakin ingin menghapus data ini?")) {
        fetch('../../functions/get_perawatan.php?action=delete', {
            method: 'post',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Data berhasil dihapus');
              loadPerawatan(); // refresh tabel
            } else {
              alert('Gagal menghapus data');
            }
          });
      }
    }

    window.onload = loadPerawatan();
  </script>
</body>

</html>