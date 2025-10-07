<?php
include "koneksi.php";   // koneksi database

session_start();

// Default role mahasiswa (kalau belum login admin)
$role = $_SESSION['role'] ?? 'mahasiswa';
?>




<?php
// Proses CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari     = $_POST["hari"];
    $tanggal  = $_POST["tanggal"] ?? date('Y-m-d');
    $ruang    = $_POST["ruang"];
    $jam      = $_POST["jam"];
    $matkul   = $_POST["matkul"];
    $prodi    = $_POST["prodi"];
    $aksi     = $_POST["aksi"];

    if ($aksi == "simpan" || $aksi == "edit") {
    // Cek apakah data untuk hari, tanggal, ruang, dan jam sudah ada
    $cek = $koneksi->query("SELECT * FROM jadwal 
                            WHERE hari='$hari' 
                            AND tanggal='$tanggal' 
                            AND ruang='$ruang' 
                            AND jam='$jam'");

    if ($cek->num_rows > 0) {
        // Kalau ada, update data tersebut
        $koneksi->query("UPDATE jadwal 
                         SET matkul='$matkul', prodi='$prodi' 
                         WHERE hari='$hari' 
                         AND tanggal='$tanggal' 
                         AND ruang='$ruang' 
                         AND jam='$jam'");
    } else {
        // Kalau belum ada, tambahkan data baru
        $koneksi->query("INSERT INTO jadwal (hari, tanggal, ruang, jam, matkul, prodi) 
                         VALUES ('$hari', '$tanggal', '$ruang', '$jam', '$matkul', '$prodi')");
    }

} elseif ($aksi == "hapus") {
    // Hapus hanya data di tanggal yang sama, jangan semua tanggal
    $koneksi->query("DELETE FROM jadwal 
                     WHERE hari='$hari' 
                     AND tanggal='$tanggal' 
                     AND ruang='$ruang' 
                     AND jam='$jam'");

} elseif ($aksi == "hapus_hari") {
    // Hapus semua data dalam hari & tanggal itu saja (bukan semua tanggal)
    $koneksi->query("DELETE FROM jadwal 
                     WHERE hari='$hari' 
                     AND tanggal='$tanggal'");
}

exit;

}


// === Ambil data jadwal untuk tampilan ===
$tanggalFilter = $_GET['tanggal'] ?? date('Y-m-d');
$data = [];
$res = $koneksi->query("SELECT * FROM jadwal WHERE tanggal='$tanggalFilter'");
while ($row = $res->fetch_assoc()) {
    $key = strtolower($row["hari"]) . $row["ruang"] . $row["jam"];
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
$jamList = ["08:00-10:30","10:45-13:15","13:30-16:00","16:00-18:30"];
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

      .kapasitas p {  
  color: #0d6efd;  
}  

/* Judul hari */
h3 {
  text-align: center;
  margin-top: 30px;
  color: #0d6efd; /* biru */
  padding-left: 10px;
  font-weight: 600;
}

.filter-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 25px;
  background: #f9f9f9;       /* abu muda, netral */
  border: 1px solid #ddd;    /* batas lembut */
  border-radius: 8px;
  margin: 15px auto;
  width: 90%;
  max-width: 1000px;
  color: #333;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}



/* Dropdown Hari */
.filter-left select,
.filter-right input {
  padding: 10px 15px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #fff;
  font-size: 14px;
  color: #333;
}

.filter-right input::placeholder {
  color: #666;
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
  table-layout: fixed;   /* 🔑 penting: fix ukuran kolom */
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

th, td {
  border: 1px solid #ddd;
  text-align: center;
  padding: 4px;
  min-width: 40px;
  cursor: pointer;
  white-space: nowrap;
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
  padding: 6px 4px;
  font-weight: 500;
  font-size: 10px;
  word-wrap: break-word;       /* ✅ pecah kata panjang */
  white-space: normal;         /* ✅ teks turun ke baris baru */
  overflow-wrap: break-word;   /* ✅ dukungan tambahan */
  text-align: center;          /* agar tetap rapi di tengah */
  box-sizing: border-box;
}


.info-box {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid #00ffcc;
  padding: 10px;
  margin: 10px auto;
  border-radius: 8px;
  color: #fff;
  width: 90%;  /* lebih kecil dari full */
  max-width: 1000px; /* biar gak full screen */
}

.info-grid {
  text-align: center;
  margin-top: 20px;
}


/* === Keterangan Prodi (datar ke samping) === */
.prodi-legend {
  display: flex;
  flex-wrap: nowrap;     /* 🔥 jangan melipat ke bawah */
  gap: 8px;              /* jarak antar kotak warna */
  justify-content: center;  /* posisikan di tengah */
  margin-top: 8px;
  overflow-x: auto;       /* jika terlalu panjang, bisa discroll ke samping */
  padding-bottom: 5px;
}

.prodi-legend span {
  display: inline-block;
  padding: 6px 10px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 600;
  color: #333;
  text-align: center;
  min-width: 120px;       /* biar kotak sejajar */
}


.kapasitas p {
  margin: 3px 0;
  font-size: 13px;
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

.date-picker {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 5px 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: 0.3s ease;
}

.date-picker:hover {
  border-color: #007bff;
  box-shadow: 0 3px 6px rgba(0, 123, 255, 0.2);
}

.date-picker label {
  font-weight: 500;
  color: #333;
}

.date-picker input[type="date"] {
  border: none;
  background: transparent;
  outline: none;
  font-size: 15px;
  color: #333;
  cursor: pointer;
}

.date-picker input[type="date"]::-webkit-calendar-picker-indicator {
  background-color: #007bff;
  padding: 5px;
  border-radius: 5px;
  cursor: pointer;
  filter: invert(1);
  transition: 0.3s;
}

.date-picker input[type="date"]::-webkit-calendar-picker-indicator:hover {
  background-color: #0056b3;
}

.modal input[type="date"] {
  width: 100%;
  box-sizing: border-box;
  background-color: #2c2c2c; /* sesuaikan dengan warna dark tema kamu */
  border: 1px solid #444;
  border-radius: 6px;
  color: #fff;
  padding: 10px;
  font-size: 14px;
  appearance: none;         /* hilangkan style default browser */
  -webkit-appearance: none; /* khusus Safari/Chrome */
}

.modal input[type="date"]::-webkit-datetime-edit {
  color: #fff;              /* ubah warna teks agar konsisten */
}

.modal input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1);        /* ubah warna icon kalender agar terlihat di tema gelap */
  cursor: pointer;
}

h3 {
  text-align: center;     /* 🔥 pusatkan teks */
  margin-top: 15px;
  margin-bottom: 10px;
  font-size: 18px;
  font-weight: bold;
}

.search-wrapper {
  position: relative;
  display: inline-block;
}

.search-wrapper .search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #ccc;
  font-size: 18px;
  pointer-events: none; /* biar tidak menghalangi klik input */
}

.search-wrapper input[type="search"] {
  padding-left: 35px; /* ruang untuk ikon */
  background-color: #111; /* sesuaikan dengan tema kamu */
  color: white;
  border: 1px solid #333;
  border-radius: 6px;
  height: 40px;
}


</style>
</head>
<body>







<?php if ($role === 'admin'): ?>
<div style="text-align:right; padding:10px;">
  <a href="logout.php" style="color:#fff; background:#dc3545; padding:8px 12px; border-radius:6px; text-decoration:none;">
    Logout
  </a>
</div>
<?php endif; ?>





<script>
function applyFilter(event) {
  // Cegah reload otomatis (jika input ada di dalam <form>)
  if (event) event.preventDefault();

  const pilihHari = document.getElementById("filterHari").value.toLowerCase();
  const keyword   = document.getElementById("searchBox").value.toLowerCase();
  const tanggal   = document.getElementById("filterTanggal").value;

  // Simpan tanggal yang dipilih ke localStorage agar tidak hilang setelah reload manual
  if (tanggal) localStorage.setItem("filterTanggal", tanggal);

  document.querySelectorAll(".jadwal-hari").forEach(container => {
    const hari = container.dataset.hari; 
    const header = document.querySelector(
      `.hari-header .judulHari[data-hari='${hari.charAt(0).toUpperCase() + hari.slice(1)}']`
    )?.closest(".hari-header");

    if (pilihHari !== "all" && hari !== pilihHari) {
      container.style.display = "none";
      if (header) header.style.display = "none";
      return;
    }

    let adaCocok = false;
    container.querySelectorAll("table tr").forEach((row, idx) => {
      if (idx === 0) return; // lewati header tabel
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

// Saat halaman dimuat, kembalikan tanggal terakhir yang dipilih
window.addEventListener("DOMContentLoaded", () => {
  const savedDate = localStorage.getItem("filterTanggal");
  if (savedDate) document.getElementById("filterTanggal").value = savedDate;
});
</script>


  <!-- Content -->
  <div class="content">
<h2>
<img src="Unglam.png" alt="Logo Universitas" class="logo-univ">
  Jadwal Pemakaian Ruangan Fisip ULM
</h2>

<!-- Info Box -->
    <!-- Kolom 1: Keterangan Prodi -->
    <div class="prodi-legend">
      <span class="ap">PS. Adm. Publik</span>
      <span class="ab">PS. Adm. Bisnis</span>
      <span class="ip">PS. Ilmu Pemerintahan</span>
      <span class="ik">PS. Ilmu Komunikasi</span>
      <span class="so">PS. Sosiologi</span>
      <span class="ge">PS. Geografi</span>
    </div>
    
    <div class="filter-container">
  <div class="filter-left">
    <div class="date-picker">
      <label for="filterTanggal">Pilih Tanggal:</label>
<input 
  type="date" 
  id="filterTanggal" 
  value="<?= htmlspecialchars($tanggalFilter) ?>" 
  onchange="applyFilter()">
    </div>

    <label for="filterHari" style="margin-left:10px;">Pilih Hari:</label>
    <select id="filterHari" onchange="applyFilter()">
      <option value="all">Semua</option>
      <?php foreach ($hariList as $hari): ?>
        <option value="<?= $hari ?>"><?= $hari ?></option>
      <?php endforeach; ?>
    </select>
  </div>

<div class="search-wrapper">
  <span class="search-icon">🔍</span>
<input 
  type="search" 
  id="searchBox" 
  placeholder="Cari Matkul / Prodi" 
  onkeyup="applyFilter()">
</div>

   
</div>

  </div>  
</div>  


  </div>




    <!-- Modal -->
   <?php if ($role === 'admin'): ?>
<div id="inputModal" class="modal">
  <div class="modal-content">
    <h3 id="modalTitle">Input Jadwal</h3>
    <input type="hidden" id="hari">
    <input type="hidden" id="ruang">
    <input type="hidden" id="jam">
    <label>Tanggal Pemakaian:</label>
<input type="date" id="tanggal" value="<?= date('Y-m-d') ?>">

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
<?php endif; ?>


    <!-- Generate tabel -->
    <?php foreach ($hariList as $hari): ?>
<div class="hari-header">
  <h3 class="judulHari" data-hari="<?= $hari ?>"><?= $hari ?></h3>
 <?php if ($role === 'admin'): ?>
  <button class="btn-clear" onclick="hapusSemua('<?= $hari ?>')">
    <i class="fas fa-wrench"></i>
  </button>
<?php endif; ?>
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

<?php if ($role === 'admin'): ?>
document.querySelectorAll(".jadwal-hari table td").forEach(cell => {
  if (cell.cellIndex === 0) return; // skip kolom ruang

  cell.addEventListener("click", function() {
    const table = this.closest("table");
    const container = this.closest(".jadwal-hari");
    const hari = container.dataset.hari; // langsung ambil dari data-hari

    const ruang = this.closest("tr").children[0].innerText;
    const jam   = table.querySelector("tr th:nth-child("+(this.cellIndex+1)+")").innerText;

    document.getElementById("hari").value  = hari.charAt(0).toUpperCase() + hari.slice(1);
    document.getElementById("ruang").value = ruang;
    document.getElementById("jam").value   = jam;

    const div = this.querySelector("div");
    if (div) {
      document.getElementById("matkul").value = div.textContent;
      document.getElementById("prodi").value  = div.className;
      document.getElementById("btnSave").style.display   = "none";
      document.getElementById("btnEdit").style.display   = "inline-block";
      document.getElementById("btnDelete").style.display = "inline-block";
    } else {
      document.getElementById("matkul").value = "";
      document.getElementById("prodi").value  = "ap";
      document.getElementById("btnSave").style.display   = "inline-block";
      document.getElementById("btnEdit").style.display   = "none";
      document.getElementById("btnDelete").style.display = "none";
    }

    document.getElementById("inputModal").style.display = "block";
  });
});
<?php endif; ?>



// Simpan filter sebelum submit
// Simpan filter sebelum submit
function submitJadwal(aksi) {
  const hari    = document.getElementById("hari").value;
  const tanggal = document.getElementById("tanggal").value;
  const ruang   = document.getElementById("ruang").value;
  const jam     = document.getElementById("jam").value;
  const matkul  = document.getElementById("matkul").value;
  const prodi   = document.getElementById("prodi").value;

  // Simpan filter hari saat ini agar tidak reset
  const filterHariVal = document.getElementById("filterHari").value;
  localStorage.setItem("filterHari", filterHariVal);

  const filterTanggalVal = document.getElementById("filterTanggal").value;
localStorage.setItem("filterTanggal", filterTanggalVal);


  // ✅ pastikan ini lengkap dan benar
  const formData = new FormData();
  formData.append("hari", hari);
  formData.append("tanggal", tanggal);
  formData.append("ruang", ruang);
  formData.append("jam", jam);
  formData.append("matkul", matkul);
  formData.append("prodi", prodi);
  formData.append("aksi", aksi);

  fetch("", { method: "POST", body: formData })
    .then(() => {
      updateCell(hari, tanggal, ruang, jam, matkul, prodi);
      tutupModal();
       location.reload();
    });
}


function updateCell(hari, tanggal, ruang, jam, matkul, prodi) {
  const container = document.querySelector(`.jadwal-hari[data-hari="${hari.toLowerCase()}"]`);
  if (!container) return;
  const table = container.querySelector("table");
  const rows = table.querySelectorAll("tr");

  for (let i = 1; i < rows.length; i++) {
    const r = rows[i].children[0].innerText;
    if (r === ruang) {
      const headers = table.querySelectorAll("th");
      for (let j = 1; j < headers.length; j++) {
        if (headers[j].innerText === jam) {
          const cell = rows[i].children[j];
          cell.innerHTML = matkul ? `<div class='${prodi}'>${matkul}</div>` : "";
          return;
        }
      }
    }
  }
}

document.getElementById("filterTanggal").addEventListener("change", () => {
  const tanggal = document.getElementById("filterTanggal").value;
  window.location = "?tanggal=" + tanggal; // reload dengan parameter tanggal
});


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