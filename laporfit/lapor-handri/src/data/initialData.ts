import { Report, ReportCategory, ReportStatus, UrgencyLevel, ReporterRole } from "../types";

export const INITIAL_REPORTS: Report[] = [
  {
    id: "LH-20260615-0012",
    title: "Koneksi Wifi FTI-UII Putus-Putus di Gedung KH Mas Mansur Lantai 3",
    description: "Koneksi Wifi FTI-UII (terutama akses eduroam dan FTI-Hotspot) sering sekali putus-putus saat perkuliahan di lantai 3. Hal ini sangat mengganggu saat kami harus mengakses praktikum online atau mengunduh modul di tengah kelas. Mohon dilakukan pengecekan access point di lorong kelas lantai 3.",
    category: ReportCategory.IT,
    status: ReportStatus.SELESAI,
    urgency: UrgencyLevel.SEDANG,
    reporterName: "Muhammad Farhan",
    reporterRole: ReporterRole.MAHASISWA,
    reporterEmail: "21523001@students.uii.ac.id",
    reporterWhatsapp: "08123456789",
    isPublic: true,
    createdAt: "2026-06-15T09:15:00.000Z",
    updatedAt: "2026-06-17T14:30:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Laporan masuk dan sedang diverifikasi oleh admin Lapor Handri.",
        timestamp: "2026-06-15T09:15:00.000Z"
      },
      {
        status: ReportStatus.DIPROSES,
        note: "Laporan diteruskan ke Divisi IT & Sistem Informasi FTI UII untuk pengecekan router/access point.",
        timestamp: "2026-06-16T10:00:00.000Z"
      },
      {
        status: ReportStatus.SELESAI,
        note: "Tim IT FTI telah mengganti access point yang bermasalah di koridor lantai 3 Gedung KH Mas Mansur. Sinyal sekarang sudah stabil dan bandwidth ditingkatkan.",
        timestamp: "2026-06-17T14:30:00.000Z"
      }
    ],
    comments: [
      {
        id: "c1",
        senderName: "Admin IT FTI",
        senderRole: "Admin",
        content: "Terima kasih atas laporannya Mas Farhan. Kemarin sore tim IT sudah meluncur ke lokasi dan menemukan bahwa Access Point AP-Mansur-3B mengalami overheat. Unit sudah kami ganti dengan yang baru. Silakan dicoba kembali ya.",
        createdAt: "2026-06-17T14:25:00.000Z"
      },
      {
        id: "c2",
        senderName: "Muhammad Farhan",
        senderRole: "Mahasiswa",
        content: "Alhamdulillah, sudah saya coba hari ini di kelas Algoritma, koneksinya lancar jaya tanpa putus. Terima kasih banyak atas respons cepatnya!",
        createdAt: "2026-06-18T08:00:00.000Z"
      }
    ]
  },
  {
    id: "LH-20260620-0045",
    title: "AC di Ruang Kelas 2.05 Gedung KH Mas Mansur Mengeluarkan Bunyi Bising dan Tidak Dingin",
    description: "Saat kuliah Analisis Sistem Kerja di Ruang Kelas 2.05, AC bagian belakang berbunyi sangat berisik (berderit kencang) dan udara tidak dingin sama sekali, melainkan hanya keluar angin biasa. Mahasiswa di barisan belakang kesulitan mendengar penjelasan dosen karena suaranya cukup bising.",
    category: ReportCategory.SARPRAS,
    status: ReportStatus.DIPROSES,
    urgency: UrgencyLevel.SEDANG,
    reporterName: "Roro Anindya",
    reporterEmail: "22522104@students.uii.ac.id",
    reporterWhatsapp: "08574321098",
    isPublic: true,
    createdAt: "2026-06-20T11:20:00.000Z",
    updatedAt: "2026-06-21T09:00:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Laporan diterima oleh Layanan Aspirasi FTI.",
        timestamp: "2026-06-20T11:20:00.000Z"
      },
      {
        status: ReportStatus.DIPROSES,
        note: "Laporan diverifikasi. Surat penugasan perbaikan AC telah dikirimkan ke Divisi Sarana & Prasarana (Sarpras) FTI UII.",
        timestamp: "2026-06-21T09:00:00.000Z"
      }
    ],
    comments: [
      {
        id: "c3",
        senderName: "Sarpras FTI UII",
        senderRole: "Admin",
        content: "Baik Mba Anindya, laporan sudah kami catat. Teknisi eksternal AC dijadwalkan datang untuk melakukan servis berkala dan perbaikan fan motor AC kelas tersebut pada hari Sabtu ini agar tidak mengganggu jalannya perkuliahan. Terima kasih infonya.",
        createdAt: "2026-06-21T09:05:00.000Z"
      }
    ]
  },
  {
    id: "LH-20260625-0089",
    title: "Dispensasi Absen Kegiatan Lomba Nasional Belum Terinput di Portal Akademik",
    description: "Saya telah mewakili FTI UII dalam perlombaan debat nasional pada tanggal 10-14 Juni kemarin dan sudah mengumpulkan surat tugas serta bukti sertifikat ke bagian kemahasiswaan. Namun, di portal akademik saya, absen pada tanggal tersebut masih dianggap Alpha. Mohon bantuannya untuk update dispensasi.",
    category: ReportCategory.AKADEMIK,
    status: ReportStatus.MENUNGGU,
    urgency: UrgencyLevel.RENDAH,
    reporterName: "Siti Rahmawati",
    reporterEmail: "20521092@students.uii.ac.id",
    isPublic: true,
    createdAt: "2026-06-25T14:40:00.000Z",
    updatedAt: "2026-06-25T14:40:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Laporan berhasil dikirimkan dan menunggu verifikasi berkas oleh admin Kemahasiswaan FTI.",
        timestamp: "2026-06-25T14:40:00.000Z"
      }
    ],
    comments: []
  },
  {
    id: "LH-20260627-0102",
    title: "Saran Penambahan Tempat Sampah Pemilah Sampah Organik & Anorganik di Dekat Kantin FTI",
    description: "Halo Mas Handri dan jajaran dekanat FTI. Saya ingin memberi masukan positif. Di area sekitar kantin FTI, tempat sampah yang tersedia masih digabung tanpa pemisahan jenis sampah. Alangkah baiknya jika disediakan tempat sampah pemilah (organik, anorganik, kertas) untuk mendukung program FTI Green Campus. Terima kasih banyak.",
    category: ReportCategory.SARPRAS,
    status: ReportStatus.SELESAI,
    urgency: UrgencyLevel.RENDAH,
    reporterName: "Anonim",
    reporterEmail: "23525008@students.uii.ac.id",
    isPublic: true,
    createdAt: "2026-06-27T08:30:00.000Z",
    updatedAt: "2026-06-28T16:10:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Laporan diterima.",
        timestamp: "2026-06-27T08:30:00.000Z"
      },
      {
        status: ReportStatus.DIPROSES,
        note: "Saran diapresiasi oleh Dekanat FTI dan dikoordinasikan dengan petugas kebersihan/umum.",
        timestamp: "2026-06-28T09:00:00.000Z"
      },
      {
        status: ReportStatus.SELESAI,
        note: "Tempat sampah pilah 3 warna (Merah, Kuning, Hijau) sudah diletakkan di 4 titik strategis sekitar kantin FTI.",
        timestamp: "2026-06-28T16:10:00.000Z"
      }
    ],
    comments: [
      {
        id: "c4",
        senderName: "Mas Handri (FTI)",
        senderRole: "Admin",
        content: "Halo mahasiswa FTI yang hebat. Kami sangat mengapresiasi usulan ini! FTI sangat berkomitmen terhadap kampanye kelestarian lingkungan. Saat ini tempat sampah pilah baru sudah terpasang di sekitar kantin. Mari bersama-sama kita jaga kebersihan kampus tercinta kita. Terima kasih masukannya!",
        createdAt: "2026-06-28T16:05:00.000Z"
      }
    ]
  },
  {
    id: "LH-20260628-0115",
    title: "Masalah Kurang Responsifnya Pelayanan Loket Akademik Saat Jam Istirahat Siang",
    description: "Kami memahami bahwa staf administrasi memiliki hak istirahat makan siang. Namun, sering terjadi antrean mengular panjang tepat jam 13.00 karena loket hanya dijaga oleh 1 orang petugas, sementara petugas lain belum kembali. Apakah memungkinkan ada sistem piket bergiliran agar pelayanan administrasi mahasiswa tetap berjalan optimal tanpa jeda kosong?",
    category: ReportCategory.PELAYANAN,
    status: ReportStatus.MENUNGGU,
    urgency: UrgencyLevel.SEDANG,
    reporterName: "Aditya Pratama",
    reporterEmail: "22524021@students.uii.ac.id",
    isPublic: true,
    createdAt: "2026-06-28T13:45:00.000Z",
    updatedAt: "2026-06-28T13:45:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Laporan masuk dan sedang ditinjau oleh Kepala Urusan Akademik FTI.",
        timestamp: "2026-06-28T13:45:00.000Z"
      }
    ],
    comments: []
  },
  {
    id: "LH-20260629-0204",
    title: "Permohonan Keringanan & Skema Cicilan Pembayaran SPP Angsuran ke-2 Semester Antara",
    description: "Dikarenakan kondisi keuangan keluarga kami yang sedang mengalami penurunan karena musibah usaha, saya ingin mengajukan permohonan dispensasi skema cicilan SPP angsuran kedua yang jatuh tempo bulan depan. Apakah ada perpanjangan masa pembayaran atau skema khusus pembagian angsuran di FTI?",
    category: ReportCategory.KEUANGAN,
    status: ReportStatus.MENUNGGU,
    urgency: UrgencyLevel.TINGGI,
    reporterName: "Anonim",
    reporterRole: ReporterRole.MAHASISWA,
    reporterEmail: "21523999@students.uii.ac.id",
    isPublic: true,
    createdAt: "2026-06-29T10:15:00.000Z",
    updatedAt: "2026-06-29T10:15:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Permohonan keringanan keuangan berhasil diajukan dan sedang ditelaah oleh divisi keuangan FTI.",
        timestamp: "2026-06-29T10:15:00.000Z"
      }
    ],
    comments: []
  },
  {
    id: "LH-20260701-0210",
    title: "Permohonan Perbaikan Proyektor & Tata Suara di Ruang Auditorium FTI",
    description: "Proyektor utama di Ruang Auditorium FTI berkedip-kedip saat digunakan untuk Seminar Nasional, dan микрофон wireless sering mengalami noise/interferensi. Mohon bantuan pengecekan fasilitas sebelum agenda ujian terbuka dan kuliah umum minggu depan.",
    category: ReportCategory.SARPRAS,
    status: ReportStatus.DIPROSES,
    urgency: UrgencyLevel.TINGGI,
    reporterName: "Dr. Ir. Hari Purnomo, M.T.",
    reporterRole: ReporterRole.DOSEN,
    reporterEmail: "hari.purnomo@uii.ac.id",
    reporterWhatsapp: "081122334455",
    isPublic: true,
    createdAt: "2026-07-01T08:00:00.000Z",
    updatedAt: "2026-07-01T10:30:00.000Z",
    timeline: [
      {
        status: ReportStatus.MENUNGGU,
        note: "Aduan dari Dosen diterima.",
        timestamp: "2026-07-01T08:00:00.000Z"
      },
      {
        status: ReportStatus.DIPROSES,
        note: "Disetujui Dekanat. Tim Multimedia & Sarpras sedang mengganti kabel HDMI dan memperbarui sistem transmisi mic wireless.",
        timestamp: "2026-07-01T10:30:00.000Z"
      }
    ],
    comments: []
  }
];
