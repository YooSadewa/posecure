// whatsapp-bot.js
const { Client, LocalAuth } = require("whatsapp-web.js");
const qrcode = require("qrcode-terminal");
const mysql = require("mysql2/promise");

// Konfigurasi Database
const dbConfig = {
  host: "localhost",
  user: "root",
  password: "",
  database: "db_posecure",
};

// Inisialisasi WhatsApp Client
const client = new Client({
  authStrategy: new LocalAuth({
    dataPath: "./whatsapp-session",
  }),
  puppeteer: {
    headless: true,
    args: ["--no-sandbox", "--disable-setuid-sandbox"],
  },
});

// Event ketika QR Code siap di-scan
client.on("qr", (qr) => {
  console.log("Scan QR Code di bawah ini:");
  qrcode.generate(qr, { small: true });
});

// Event ketika WhatsApp sudah siap
client.on("ready", () => {
  console.log("WhatsApp Bot sudah siap!");
  console.log("Bot akan mengecek absensi setiap hari...");

  // Jalankan pengecekan pertama kali
  cekAbsensiRonda();

  // Set interval untuk cek setiap hari (24 jam)
  setInterval(cekAbsensiRonda, 24 * 60 * 60 * 1000);
});

// Event ketika ada error
client.on("auth_failure", (msg) => {
  console.error("Autentikasi gagal:", msg);
});

// Event ketika disconnected
client.on("disconnected", (reason) => {
  console.log("WhatsApp terputus:", reason);
});

// Fungsi untuk mengecek absensi ronda
async function cekAbsensiRonda() {
  let connection;

  try {
    console.log("Memulai pengecekan absensi ronda...");

    // Koneksi ke database
    connection = await mysql.createConnection(dbConfig);

    // Query untuk mencari warga yang tidak absen 3 minggu berturut-turut
    // DAN belum pernah dikirim notifikasi dalam 7 hari terakhir
    const query = `
            SELECT 
                u.id_user,
                u.nama,
                u.no_telp,
                w.blok_rumah,
                w.hari_ronda,
                COUNT(a.id_absensi) as total_absensi,
                MAX(a.tanggal) as absensi_terakhir,
                MAX(ln.tanggal_kirim) as notif_terakhir
            FROM user u
            INNER JOIN warga w ON u.id_user = w.id_user
            LEFT JOIN absensi a ON u.id_user = a.id_user 
                AND a.tanggal >= DATE_SUB(CURDATE(), INTERVAL 21 DAY)
            LEFT JOIN log_notifikasi ln ON u.id_user = ln.id_user
                AND ln.status = 'success'
                AND ln.tanggal_kirim >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            WHERE u.role = 'warga'
            GROUP BY u.id_user, u.nama, u.no_telp, w.blok_rumah, w.hari_ronda
            HAVING total_absensi = 0 AND notif_terakhir IS NULL
        `;

    const [users] = await connection.execute(query);

    console.log(
      `Ditemukan ${users.length} warga yang tidak absen 3 minggu berturut-turut`
    );

    // Kirim pesan ke setiap user
    for (const user of users) {
      await kirimPesanWhatsApp(user);
      // Delay 2 detik antar pengiriman untuk menghindari spam
      await delay(2000);
    }

    console.log("Selesai mengecek dan mengirim pesan");
  } catch (error) {
    console.error("Error saat mengecek absensi:", error);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

// Fungsi untuk mengirim pesan WhatsApp
async function kirimPesanWhatsApp(user) {
  try {
    // Format nomor WhatsApp (hapus karakter non-digit)
    let nomorWA = user.no_telp.replace(/\D/g, "");

    // Tambahkan kode negara jika belum ada (contoh: Indonesia 62)
    if (!nomorWA.startsWith("62")) {
      if (nomorWA.startsWith("0")) {
        nomorWA = "62" + nomorWA.substring(1);
      } else {
        nomorWA = "62" + nomorWA;
      }
    }

    // Format nomor untuk WhatsApp
    const chatId = nomorWA + "@c.us";

    // Pesan yang akan dikirim
    const pesan = `Halo *${user.nama}*,

Kami ingin mengingatkan bahwa Anda belum melakukan absensi ronda selama *3 minggu berturut-turut*.

📍 Blok Rumah: ${user.blok_rumah}
📅 Jadwal Ronda: ${user.hari_ronda}

Mohon untuk segera melakukan absensi ronda sesuai jadwal yang telah ditentukan.

Terima kasih atas perhatian dan kerjasamanya.

_Pesan ini dikirim otomatis oleh sistem._`;

    // Kirim pesan
    await client.sendMessage(chatId, pesan);

    console.log(`✓ Pesan berhasil dikirim ke ${user.nama} (${nomorWA})`);

    // Log ke database (PENTING: untuk mencegah notif berulang!)
    await logPengirimanPesan(user.id_user, nomorWA, "success");
  } catch (error) {
    console.error(`✗ Gagal mengirim pesan ke ${user.nama}:`, error.message);
    await logPengirimanPesan(
      user.id_user,
      user.no_telp,
      "failed",
      error.message
    );
  }
}

// Fungsi untuk log pengiriman pesan (opsional)
async function logPengirimanPesan(idUser, nomorWA, status, errorMsg = null) {
  let connection;
  try {
    connection = await mysql.createConnection(dbConfig);
    const query = `
            INSERT INTO log_notifikasi 
            (id_user, no_telp, status, pesan_error, tanggal_kirim) 
            VALUES (?, ?, ?, ?, NOW())
        `;
    await connection.execute(query, [idUser, nomorWA, status, errorMsg]);
  } catch (error) {
    console.error("Error saat log pengiriman:", error);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

// Fungsi delay
function delay(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

// Inisialisasi WhatsApp Client
client.initialize();

// Handle graceful shutdown
process.on("SIGINT", async () => {
  console.log("\nMenghentikan bot...");
  await client.destroy();
  process.exit(0);
});
