<?php include "koneksi.php"; ?> 

<?php
// Proses CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari   = $_POST["hari"];
    $ruang  = $_POST["ruang"];
    $jam    = $_POST["jam"];
    $matkul = $_POST["matkul"];
    $prodi  = $_POST["prodi"];
    $aksi   = $_POST["aksi"];

    if ($aksi == "simpan" || $aksi == "edit") {
        $cek = $koneksi->query("SELECT * FROM jadwal WHERE hari='$hari' AND ruang='$ruang' AND jam='$jam'");
        if ($cek->num_rows > 0) {
            $koneksi->query("UPDATE jadwal SET matkul='$matkul', prodi='$prodi' 
                             WHERE hari='$hari' AND ruang='$ruang' AND jam='$jam'");
        } else {
            $koneksi->query("INSERT INTO jadwal(hari, ruang, jam, matkul, prodi) 
                 VALUES('$hari','$ruang','$jam','$matkul','$prodi')");
        }
    } elseif ($aksi == "hapus") {
        $koneksi->query("DELETE FROM jadwal WHERE hari='$hari' AND ruang='$ruang' AND jam='$jam'");
    } elseif ($aksi == "hapus_hari") {
    $koneksi->query("DELETE FROM jadwal WHERE hari='$hari'");
}
    exit;
}

// Ambil semua data jadwal
$data = [];
$res = $koneksi->query("SELECT * FROM jadwal");
while ($row = $res->fetch_assoc()) {
    $key = strtolower($row["hari"]) . "" . $row["ruang"] . "" . $row["jam"];
    $data[$key] = $row;
}

// List hari, ruang, jam
$hariList = ["Senin","Selasa","Rabu","Kamis","Jumat"];
$ruangList = [
  "R I","R II","R III","R IV","R V","R VI",
  "X Kom","R VII","R VIII","RK.1",
  "A","B","D","E","G",
  "AULA",
  "GB1.02","GB1.03","GB1.15"
];
$jamList = ["08:00-10:30","10:45-13:15","13:30-16:00","16:00-18:30","18:30-21:00"];
?>

<!DOCTYPE html> 
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Matriks Jadwal Mingguan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
/* === Override Global === */
body {
  font-family: 'Inter', sans-serif !important;
  margin: 0;
  background: #ffffff !important; /* putih polos */
  color: #333;
}

/* Header utama */
h2 {
  text-align: center;
  margin: 10px 0 10px;
  color: #0d6efd !important; /* biru */
  font-weight: 700;
}


/* Judul hari */
h3 {
  text-align: center;
  margin-top: 30px;
  color: #0d6efd; /* biru */
  padding-left: 10px;
  font-weight: 600;
}


/* === Filter Bar === */
.filter-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #0d6efd; /* biru solid */
  color: white;
 

}

/* Dropdown Hari */
.filter-left select {
  padding: 10px 15px;
  border-radius: 25px;
  border: none;
  font-size: 14px;
  color: #333;
  background: #fff;
  outline: none;
  cursor: pointer;
}

/* Search Bar */
.filter-right {
  position: relative;
}

.filter-right input {
  padding: 10px 40px 10px 15px;
  border-radius: 25px;
  border: none;
  font-size: 14px;
  width: 230px;
  background: #fff;
  color: #333;
  outline: none;
}

.filter-right input::placeholder {
  color: #666;
}

/* Ikon kaca pembesar */
.filter-right::after {
  content: "\f002";
  font-family: "Font Awesome 5 Free";
  font-weight: 900;
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-size: 14px;
  pointer-events: none;
}

/* === Table === */
table {
  border-collapse: collapse;
  width: 100%;
  font-size: 11px; /* jangan diubah */
  margin-bottom: 15px;
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
  table-layout: fixed;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

th, td {
  border: 1px solid #ddd;
  text-align: center;
  padding: 4px;
  min-width: 60px;
  cursor: pointer;
  white-space: nowrap;
}

td div {
  padding: 2px 0;
  font-weight: 500;
  font-size: 10px;
}

th {
  background: #0d6efd;
  color: #fff;
  font-weight: 600;
}

tr:nth-child(even) td {
  background: #fafafa;
}

td:hover {
  background: #eef6ff;
}

tr:hover td {
  background: #f0f8ff !important;
}

/* Modal */
.modal {
  display: none; position: fixed; z-index: 1;
  left: 0; top: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
}
.modal-content {
  background: #fff; margin: 8% auto; padding: 20px;
  border-radius: 10px; width: 350px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.modal-content h3 {
  text-align: center;
  color: #198754;
  margin-bottom: 15px;
}
.modal-content input, .modal-content select {
  width: 100%; padding: 10px; margin: 8px 0;
  border: 1px solid #ccc; border-radius: 6px;
}
.modal-content button {
  width: 100%; padding: 10px; margin-top: 10px;
  border: none; border-radius: 6px; cursor: pointer;
  font-weight: bold;
}
.btn-save { background: #198754; color: white; }
.btn-edit { background: #198754; color: white; }
.btn-delete { background: #c0392b; color: white; }
.btn-cancel { background: #7f8c8d; color: white; }

/* Warna prodi sesuai screenshot */
.ap { background-color: #ffff7c; }  /* PS. Adm. Publik - Kuning Muda */
.ab { background-color: #74fffa; }  /* PS. Adm. Bisnis - Biru Cyan */
.ip { background-color: #ff564d; }  /* PS. Ilmu Pemerintahan - Merah Muda */
.ik { background-color: #80ff7e; }  /* PS. Ilmu Komunikasi - Hijau Muda */
.so { background-color: #ffdd92; }  /* PS. Sosiologi - Warna Kulit */
.ge { background-color: #fc91fc; }  /* PS. Geografi - Ungu Muda */




td div {
  width: 100%;
  height: 100%;
  display: block;
  padding: 6px 0;
  font-weight: 500;
}

/* Info Box Footer Full Width */
.info-box {
    position: fixed;        /* fixed agar selalu di bawah */
    bottom: 0;              /* menempel di bawah */
    left: 0;                /* mulai dari kiri */
    width: 100%;            /* membentang penuh */
    background: #0a0a0a;    /* tetap hitam agar neon jelas */
    border-radius: 0;        /* hilangkan rounding agar full width */
    padding: 10px 0;        /* tinggi box */
    font-size: 12px;
    box-shadow: 0px -2px 6px rgba(0,0,0,0.3);
    color: #fff;
    overflow: hidden;
    z-index: 1000;          /* pastikan di atas konten */
    text-align: center;     /* konten center */
}

/* Neon border animasi */
.info-box::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 0;       /* full width */
    padding: 2px;
    background: linear-gradient(
        90deg,
        #ff00ff,
        #00ffff,
        #00ff00,
        #ffff00,
        #ff0000,
        #ff00ff
    );
    background-size: 300% 300%;
    animation: neon-walk 6s linear infinite;
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
            mask-composite: exclude;
    filter: blur(2px);
    z-index: -1;
}

.info-box h3, .info-box p {
    margin: 0;
    font-size: 12px;
    display: inline-block;  /* agar horizontal */
    padding: 0 15px;
    vertical-align: middle;
}

.form-group {
  display: flex;
  align-items: center;
  gap: 10px; /* jarak antara label dan input */
}

#matkul, #prodi {
  flex: 1; /* lebar otomatis sama */
  padding: 5px;
  box-sizing: border-box;
}

/* Container filter */
.filter-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #0d6efd;
  color: white;
}

/* Filter kiri (Hari) */
.filter-left select {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  font-size: 14px;
  margin-left: 10px;
}

/* Filter kanan (Search) */
.filter-right input {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  font-size: 14px;
  width: 200px;
}

.logo-univ {
  height: 50px;              /* atur sesuai kebutuhan */
  width: 50px;               /* pastikan sama supaya bulat */
  vertical-align: middle;
  margin-right: 8px;
  border-radius: 50%;        /* bikin bulat */
  object-fit: cover;         /* crop pas lingkaran */
  background: transparent;   /* pastikan tanpa latar */
}

.hari-header {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 20px 0 5px;
  position: relative; /* biar bisa posisikan obeng di kiri */
}

.btn-clear {
  position: absolute;
  left: 0;                 /* tempel di paling kiri */
  background: transparent; /* invisible */
  border: none;
  cursor: pointer;
  font-size: 18px;
  color: #fff;
  transition: transform 0.2s, color 0.2s;
}

.btn-clear:hover {
  transform: scale(1.2);
  color: #ffc107; /* hover kuning */
}

.judulHari {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #0d6efd; /* biru */
}


</style>
</head>
<body>

  <!-- Filter + Search -->
<div class="filter-container">
  <div class="filter-left">
    <label for="filterHari">Pilih Hari:</label>
    <select id="filterHari" onchange="filterHari()">
      <option value="all">Semua</option>
      <?php foreach ($hariList as $hari): ?>
        <option value="<?= $hari ?>"><?= $hari ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="filter-right">
    <input type="text" id="searchBox" placeholder="Cari Matkul / Prodi" onkeyup="filterSearch()">
  </div>
</div>


<script>
function applyFilter() {
  const pilihHari = document.getElementById("filterHari").value.toLowerCase();
  const keyword   = document.getElementById("searchBox").value.toLowerCase();

  document.querySelectorAll(".jadwal-hari").forEach(container => {
    const hari = container.dataset.hari; 
    const header = document.querySelector(`.hari-header .judulHari[data-hari='${hari.charAt(0).toUpperCase()+hari.slice(1)}']`)?.closest(".hari-header");

    if (pilihHari !== "all" && hari !== pilihHari) {
      container.style.display = "none";
      if (header) header.style.display = "none";
      return;
    }

    let adaCocok = false;

    container.querySelectorAll("table tr").forEach((row, idx) => {
      if (idx === 0) return;
      let cocok = false;

      row.querySelectorAll("td div").forEach(cell => {
        if (cell && cell.textContent.toLowerCase().includes(keyword)) {
          cocok = true;
        }
      });

      row.style.display = (cocok || keyword === "") ? "" : "none";
      if (cocok) adaCocok = true;
    });

    if (adaCocok || keyword === "") {
      container.style.display = "block";
      if (header) header.style.display = "flex";
    } else {
      container.style.display = "none";
      if (header) header.style.display = "none";
    }
  });
}


// Event listener
document.getElementById("filterHari").addEventListener("change", applyFilter);
document.getElementById("searchBox").addEventListener("keyup", applyFilter);
</script>

  <!-- Content -->
  <div class="content">
<h2>
<img src="Unglam.png" alt="Logo Universitas" class="logo-univ">
  Jadwal Perkuliahan FISIP ULM 2025-2026
</h2>

    <!-- Info Box -->
    <div class="info-box">
        <h3>Kapasitas Ruangan</h3>
        <p>AULA → 200 Orang</p>
        <p>I, II, VII, VIII, XKom → 125 Orang</p>
        <p>RK.1, A, B, D, E, F, G → 80 Orang</p>
        <p>GB2, GB3, GB15 → 50 Orang</p>
        <p>III, IV, V, VI → 40 Orang</p>
    </div>

    <!-- Modal -->
    <div id="inputModal" class="modal">
      <div class="modal-content">
        <h3 id="modalTitle">Input Jadwal</h3>
        <input type="hidden" id="hari">
        <input type="hidden" id="ruang">
        <input type="hidden" id="jam">

        <label>Mata Kuliah:</label>
        <input type="text" id="matkul" placeholder="Nama Mata Kuliah">
        <label>Program Studi:</label>
        <select id="prodi">
          <option value="ap">Administrasi Publik</option>
          <option value="ab">Administrasi Bisnis</option>
          <option value="ip">Ilmu Pemerintahan</option>
          <option value="ik">Ilmu Komunikasi</option>
          <option value="so">Sosiologi</option>
          <option value="ge">Geografi</option>
        </select>
        <button class="btn-save" id="btnSave" onclick="submitJadwal('simpan')">Simpan</button>
        <button class="btn-edit" id="btnEdit" onclick="submitJadwal('edit')">Update</button>
        <button class="btn-delete" id="btnDelete" onclick="submitJadwal('hapus')">Hapus</button>
        <button class="btn-cancel" onclick="tutupModal()">Batal</button>
      </div>
    </div>

    <!-- Generate tabel -->
    <?php foreach ($hariList as $hari): ?>
<div class="hari-header">
  <h3 class="judulHari" data-hari="<?= $hari ?>"><?= $hari ?></h3>
  <button class="btn-clear" onclick="hapusSemua('<?= $hari ?>')">
    <i class="fas fa-wrench"></i>
  </button>
</div>

    <div class="jadwal-hari" data-hari="<?= strtolower($hari) ?>">
      <table>
        <tr>
          <th>Ruang</th>
          <?php foreach ($jamList as $jam): ?>
            <th><?= $jam ?></th>
          <?php endforeach; ?>
        </tr>
        <?php foreach ($ruangList as $ruang): ?>
          <tr>
            <td style="font-weight:600;background:#f4f6f7;"><?= $ruang ?></td>
            <?php foreach ($jamList as $jam): ?>
              <?php
                $key = strtolower($hari) . "" . $ruang . "" . $jam;
                if (isset($data[$key])) {
                  $m = $data[$key]["matkul"];
                  $p = $data[$key]["prodi"];
                  echo "<td><div class='$p'>$m</div></td>";
                } else {
                  echo "<td></td>";
                }
              ?>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <?php endforeach; ?>
  </div>

<script>

  function hapusSemua(hari) {
  if (!confirm("Yakin hapus semua jadwal untuk hari " + hari + " ?")) return;

  const formData = new FormData();
  formData.append("hari", hari);
  formData.append("aksi", "hapus_hari");

  fetch("", { method:"POST", body: formData })
    .then(() => location.reload());
}

let selectedCell = null;

document.querySelectorAll("td").forEach(cell => {
  if (cell.cellIndex === 0) return;
  cell.addEventListener("click", function() {
    selectedCell = this;
    const hari  = this.closest(".jadwal-hari").dataset.hari;
    const ruang = this.parentNode.cells[0].innerText;
    const jam   = this.closest("table").rows[0].cells[this.cellIndex].innerText;

    document.getElementById("hari").value = hari;
    document.getElementById("ruang").value = ruang;
    document.getElementById("jam").value = jam;

    document.getElementById("matkul").value = "";
    document.getElementById("prodi").value = "ap";

    if (this.innerHTML.trim() === "") {
      document.getElementById("modalTitle").innerText = "Input Jadwal";
      document.getElementById("btnSave").style.display = "block";
      document.getElementById("btnEdit").style.display = "none";
      document.getElementById("btnDelete").style.display = "none";
    } else {
      document.getElementById("modalTitle").innerText = "Edit Jadwal";
      document.getElementById("btnSave").style.display = "none";
      document.getElementById("btnEdit").style.display = "block";
      document.getElementById("btnDelete").style.display = "block";
      const content = this.querySelector("div");
      if (content) {
        document.getElementById("matkul").value = content.textContent;
        document.getElementById("prodi").value = content.className;
      }
    }
    document.getElementById("inputModal").style.display = "block";
  });
});

// Simpan filter sebelum submit
function submitJadwal(aksi) {
  const hari   = document.getElementById("hari").value;
  const ruang  = document.getElementById("ruang").value;
  const jam    = document.getElementById("jam").value;
  const matkul = document.getElementById("matkul").value;
  const prodi  = document.getElementById("prodi").value;

  // Simpan filter hari saat ini
  const filterHariVal = document.getElementById("filterHari").value;
  localStorage.setItem("filterHari", filterHariVal);

  const formData = new FormData();
  formData.append("hari", hari);
  formData.append("ruang", ruang);
  formData.append("jam", jam);
  formData.append("matkul", matkul);
  formData.append("prodi", prodi);
  formData.append("aksi", aksi);

  fetch("", { method:"POST", body: formData })
  .then(() => location.reload());
}

// Setelah halaman reload, kembalikan filter sebelumnya
window.addEventListener("DOMContentLoaded", () => {
  const savedFilter = localStorage.getItem("filterHari");
  if(savedFilter) {
    document.getElementById("filterHari").value = savedFilter;
    filterHari(); // terapkan filter
  }
});


function tutupModal() { document.getElementById("inputModal").style.display = "none"; }
window.onclick = function(event) {
  if (event.target == document.getElementById("inputModal")) tutupModal();
}

// Filter hari
function filterHari() {
  const pilih = document.getElementById("filterHari").value.toLowerCase();

  document.querySelectorAll(".hari-header").forEach(header => {
    const hari = header.querySelector(".judulHari").dataset.hari.toLowerCase();
    const container = document.querySelector(`.jadwal-hari[data-hari="${hari}"]`);

    if (pilih === "all" || hari === pilih) {
      header.style.display = "flex";   // tampilkan header + obeng
      if (container) container.style.display = "block";
    } else {
      header.style.display = "none";   // sembunyikan header + obeng
      if (container) container.style.display = "none";
    }
  });
}


</script>
</body>
</html>