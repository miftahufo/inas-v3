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
            align-items: center; 
            min-height: 100vh; 
            padding: 20px;
        }
        
        /* CARD CONTAINER */
        .main-container { 
            width: 90%; 
            max-width: 400px; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); 
            overflow: hidden;
        }
        
        /* HEADER DAN JUDUL */
        .header { 
            padding: 30px; 
            background-color: #ffffff; 
            border-bottom: 5px solid #007bff;
            display: flex;
            align-items: center;
        }
        .logo {
            width: 40px; 
            height: auto; 
            margin-right: 15px;
            flex-shrink: 0; 
        }
        .header-content { margin-left: 0; }
        h1 { 
            font-size: 28px; 
            color: #1c2e4a; 
            margin: 0 0 5px 0; 
            font-weight: 700;
        }
        .subtitle { 
            color: #6c757d; 
            font-size: 16px; 
            margin: 0; 
        }

        /* BODY CONTENT (2 Kolom) */
        .content { 
            display: flex; 
            flex-wrap: wrap; 
        }
        
        /* KOLOM KIRI (DATA TEXT) */
        .data-panel { 
            flex: 2; 
            min-width: 320px; 
            padding: 30px; 
            border-right: 1px dashed #e9ecef;
        }
        .data-group { margin-bottom: 25px; }
        .data-item { margin-bottom: 15px; }
        .data-label { 
            font-weight: 600; 
            color: #007bff; 
            display: block; 
            margin-bottom: 2px; 
            font-size: 14px; 
        }
        .data-value { 
            font-size: 18px; 
            color: #343a40; 
            font-weight: 700; 
        }
        
        /* BADGE STATUS */
        .status-badge { 
            padding: 8px 16px; 
            border-radius: 25px; 
            font-size: 15px; 
            font-weight: bold; 
            color: white; 
            display: inline-block; 
            margin-top: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .status-Masuk { background-color: #28a745; } 
        .status-Pulang { background-color: #dc3545; } 
        .status-Belum-Absen { background-color: #ffc107; color: #343a40; } 

        /* KOLOM KANAN (FOTO) */
        .photo-panel { 
            flex: 1; 
            min-width: 300px; 
            background-color: #f8f9fa; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            padding: 30px;
        }
        .photo-box { 
            width: 100%; 
            max-width: 250px; /* Dikecilkan agar pas di mobile */
            height: 200px; 
            background-color: #e9ecef; 
            border-radius: 8px; 
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .photo-box img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }
        .photo-panel p { color: #6c757d; font-size: 14px; margin-top: 15px; text-align: center; }

        /* Responsif untuk Mobile */
        @media (max-width: 768px) {
            .content { flex-direction: column; }
            .main-container { width: 100%; border-radius: 0; }
            .header { padding: 20px 15px; }
            h1 { font-size: 24px; }
            .subtitle { font-size: 14px; }
            .data-panel { border-right: none; border-bottom: 1px dashed #e9ecef; padding: 20px 15px; }
            .photo-panel { padding: 20px 5px; }
            .photo-box { height: 200px; }
            .data-value { font-size: 16px; }
            .status-badge { font-size: 14px; }
        }

    </style>
</head>
<body>
    <div class="main-container">
        
        @if(isset($error))
            <div class="header" style="background-color: #f8d7da; border-color: #dc3545;">
                <h1 style="color: #dc3545;">Akses Ditolak</h1>
                <p style="color: #dc3545;">{{ $error }}</p>
            </div>
        @else
            <div class="header">
                <!-- LOGO DARI PUBLIC FOLDER -->
                <img class="logo"  
                     src="{{ url('images/logo.png') }}?v={{ time() }}" 
                     alt="Logo Sekolah/Instansi" 
                     onerror="this.style.display='none'; console.error('Logo Gagal Dimuat. URL yang Dicoba: ' + this.src);">
                
                <div class="header-content">
                    <h1>LAPORAN KEHADIRAN HARIAN SISWA 
                        SMP DAN SMK ISLAM BIHBUL</h1>
                    <p class="subtitle">Data Status Absensi Real-time ({{ \Carbon\Carbon::now()->format('d M Y') }})</p>
                </div>
            </div>

            <div class="content">
                <div class="data-panel">
                    
                    <div class="data-group">
                        <div class="data-item">
                            <span class="data-label">Nama Siswa</span>
                            <span class="data-value">{{ $data['nama'] ?? '-' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Kelas</span>
                            <span class="data-value">{{ $data['kelas'] ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="data-group">
                        <div class="data-item">
                            <span class="data-label">Status Terakhir</span>
                            <span id="status-badge" class="status-badge status-{{ str_replace(' ', '-', $data['status_kehadiran'] ?? 'Belum-Absen') }}">
                                {{ $data['status_kehadiran'] ?? 'Belum Absen' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="data-group">
                        <div class="data-item">
                            <span class="data-label">Jam Masuk</span>
                            <span class="data-value">{{ $data['jam_masuk'] ?? 'Belum Absen' }}</span>
                        </div>

                        <div class="data-item">
                            <span class="data-label">Jam Pulang</span>
                            <span class="data-value">{{ $data['jam_pulang'] ?? 'Belum Pulang' }}</span>
                        </div>
                    </div>

                </div>

                <div class="photo-panel">
                    <div class="photo-box">
                        @if($data['foto_url'])
                            <img src="{{ $data['foto_url'] }}" alt="Foto Absensi">
                        @else
                            <p style="color: #6c757d;">[Foto Belum Tersedia]</p>
                        @endif
                    </div>
                    <p>Bukti Foto Saat Absen Terakhir</p>
                </div>
            </div>
        @endif
    </div>
</body>
</html>