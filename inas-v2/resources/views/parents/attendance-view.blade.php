<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Anak: {{ $data['nama'] ?? 'Siswa' }}</title>
    <style>
        /* BASE STYLES */
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            background-color: #f0f4f9; 
            display: flex; 
            justify-content: center; 
            align-items: flex-start;
            min-height: 100vh; 
            padding: 30px 20px;
        }
        
        /* CONTAINER UTAMA (Wrapper untuk Card) */
        .main-wrapper {
            width: 90%; 
            max-width: 900px; 
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* CARD STYLING */
        .card { 
            background: white; 
            padding: 30px;
            /* Mempertahankan sudut melengkung pada card individu */
            border-radius: 12px; 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); 
        }
        
        /* Hapus shadow dari card individu karena sudah di main-wrapper */
        .photo-card { 
            padding: 20px; 
            border-bottom: 5px solid #d0ff00; 
        }

        /* HEADER & LOGO */
        .header { 
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 20px; 
        }
        .logo {
            width: 70px;
            height: auto; 
            margin-bottom: 15px; 
        }
        h1 { 
            font-size: 32px; 
            color: #1c2e4a; 
            margin: 0 0 5px 0; 
            font-weight: 800;
        }
        .subtitle { 
            color: #6c757d; 
            font-size: 16px; 
            margin: 0; 
        }

        /* PHOTO BOX */
        .photo-box-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }
        .photo-box { 
            width: 90%; 
            max-width: 350px; 
            height: auto;
            background-color: #e9ecef; 
            border-radius: 8px; /* Sudut melengkung untuk kotak foto */
            overflow: hidden;
            border: 2px solid #ddd;
        }
        .photo-box img { 
            width: 100%; 
            height: auto; 
            display: block;
        }
        .photo-item-label { 
            font-size: 15px; 
            font-weight: 600;
            color: #343a40;
            margin-top: 10px;
            text-align: center;
        }
        
        /* DATA CARD STYLES */
        .data-card { padding: 30px; }
        .data-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding-top: 10px;
        }
        .data-item { 
            padding: 15px 0;
            border-bottom: 1px dashed #e9ecef;
        }
        .data-item:last-child { border-bottom: none; }
        .data-label { 
            font-weight: 500; 
            color: #0c0c0c; 
            display: block; 
            margin-bottom: 5px; 
            font-size: 14px; 
        }
        .data-value { 
            font-size: 20px; 
            color: #1c2e4a; 
            font-weight: 700; 
        }
        
        /* BADGE STATUS */
        .status-badge { 
            padding: 8px 18px; 
            border-radius: 25px; 
            font-size: 16px; 
            font-weight: bold; 
            color: white; 
            display: inline-block; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .status-Masuk { background-color: #28a745; } 
        .status-Pulang { background-color: #dc3545; } 
        .status-Belum-Absen { background-color: #ffc107; color: #343a40; } 

        /* Responsif untuk Mobile */
        @media (max-width: 768px) {
            body { padding: 10px; } /* Tambah padding sedikit di body */
            .main-wrapper { 
                width: 100%; 
                gap: 15px; 
                /* Hapus border-radius dari main-wrapper di sini jika Anda ingin card menempel di tepi */
                border-radius: 0; 
                box-shadow: none;
            }
            .card { 
                padding: 20px 15px; 
                /* KUNCI: Pertahankan sudut melengkung pada CARD individual di mobile */
                border-radius: 12px; 
            }
            
            .photo-card { border-bottom-width: 3px; }
            h1 { font-size: 26px; }
            .subtitle { font-size: 14px; }
            
            /* Data Grid tetap menyamping 2 kolom */
            .data-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 15px; 
                padding: 0;
                margin: 0;
            }
            .data-item {
                padding: 10px 0;
                border-bottom: none; 
            }

            .data-value { font-size: 16px; }
            .status-badge { font-size: 14px; }
            .photo-box { height: 200px; }
        }

    </style>
</head>
<body>
    <div class="main-wrapper">
        
        @if(isset($error))
            <div class="card" style="background-color: #f8d7da; border: 1px solid #dc3545;">
                <h1 style="color: #dc3545;">Akses Ditolak</h1>
                <p style="color: #dc3545;">{{ $error }}</p>
            </div>
        @else
            <!-- CARD 1: PHOTO & HEADER CARD (ATAS) -->
            <div class="card photo-card">
                <div class="header">
                    <!-- LOGO DARI PUBLIC FOLDER: Posisi di atas judul -->
                    <img class="logo" 
                         src="{{ url('images/logo.png') }}?v={{ time() }}" 
                         alt="Logo Sekolah/Instansi" 
                         onerror="this.style.display='none'; console.error('Logo Gagal Dimuat. URL yang Dicoba: ' + this.src);">
                    
                    <h1>LAPORAN KEHADIRAN HARIAN SISWA/SISWI SMP DAN SMK ISLAM BIHBUL</h1>
                    <p class="subtitle">Data Status Absensi Real-time ({{ \Carbon\Carbon::now()->format('d M Y') }})</p>
                </div>
                
                <div class="photo-box-wrapper">
                    <!-- FOTO GABUNGAN (Masuk atau Pulang) -->
                    <div class="photo-item">
                        <div class="photo-box">
                            @if($data['foto_url'])
                                <img src="{{ $data['foto_url'] }}" alt="Foto Absensi">
                            @else
                                <p style="color: #6c757d; font-weight: normal;">[Belum Ada Bukti Foto]</p>
                            @endif
                        </div>
                        <p class="photo-item-label">
                            Bukti Foto Saat 
                            @if($data['status_kehadiran'] === 'Pulang') Pulang @else Masuk @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 2: DATA CARD (BAWAH) -->
            <div class="card data-card">
                <h2 style="font-size: 24px; color: #414a1c; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px;">Detail Kehadiran</h2>
                
                <div class="data-grid">
                    
                    <!-- DATA SISWA -->
                    <div class="data-item">
                        <span class="data-label">Nama Siswa</span>
                        <span class="data-value">{{ $data['nama'] ?? '-' }}</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Kelas</span>
                        <span class="data-value">{{ $data['kelas'] ?? '-' }}</span>
                    </div>

                    <!-- DATA ABSENSI -->
                    <div class="data-item">
                        <span class="data-label">Status Terakhir</span>
                        <span id="status-badge" class="status-badge status-{{ str_replace(' ', '-', $data['status_kehadiran'] ?? 'Belum-Absen') }}">
                            {{ $data['status_kehadiran'] ?? 'Belum Absen' }}
                        </span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Jam Masuk</span>
                        <span class="data-value">{{ $data['jam_masuk'] ?? 'Belum Absen' }}</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Jam Pulang</span>
                        <span class="data-value">{{ $data['jam_pulang'] ?? 'Belum Pulang' }}</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Tanggal Laporan</span>
                        <span class="data-value">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
                    </div>

                </div>
            </div>
        @endif
    </div>
</body>
</html>