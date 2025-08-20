<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Knowledge;
use App\Models\Category;
use App\Models\MasterSKPD;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HSSKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding HSS Knowledge data...');

        // Get categories and SKPDs
        $categories = Category::all()->keyBy('name');
        $skpds = MasterSKPD::all()->keyBy('kode_skpd');
        $users = User::all();
        
        // Delete existing HSS knowledge data first
        $hssSkpdCodes = collect($this->getHSSKnowledgeData())->pluck('skpd_code')->unique();
        $hssSkpdIds = $skpds->whereIn('kode_skpd', $hssSkpdCodes)->pluck('id');
        
        Knowledge::whereIn('skpd_id', $hssSkpdIds)->delete();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $knowledgeData = $this->getHSSKnowledgeData();
        
        foreach ($knowledgeData as $data) {
            // Find category
            $categoryId = $categories->get($data['category'])?->id ?? $categories->first()?->id;
            
            // Find SKPD
            $skpdId = $skpds->get($data['skpd_code'])?->id;
            
            // Find user from the same SKPD
            $skpdUser = $users->where('skpd_id', $skpdId)->first();
            $authorId = $skpdUser ? $skpdUser->id : $users->random()->id;
            
            // Random date between 2023-01-01 and 2025-07-31
            $startDate = strtotime('2023-01-01');
            $endDate = strtotime('2025-07-31');
            $randomTimestamp = rand($startDate, $endDate);
            $publishedAt = date('Y-m-d H:i:s', $randomTimestamp);
            
            Knowledge::create(
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'description' => $data['description'],
                    'category_id' => $categoryId,
                    'skpd_id' => $skpdId,
                    'author_id' => $authorId,
                    'status' => $data['status'],
                    'published_at' => $publishedAt,
                    'verification_status' => 'approved',
                    'verified_by' => $users->random()->id,
                    'verified_at' => $publishedAt,
                    'created_at' => $publishedAt,
                    'updated_at' => $publishedAt,
                ]
            );
        }
        
        $this->command->info('HSS Knowledge data seeded successfully!');
    }

    private function getHSSKnowledgeData(): array
    {
        return [
            // DINAS PENDIDIKAN
            [
                'title' => 'Panduan Implementasi Kurikulum Merdeka di HSS',
                'description' => 'Panduan implementasi Kurikulum Merdeka untuk sekolah di Kabupaten Hulu Sungai Selatan',
                'content' => 'Panduan lengkap implementasi Kurikulum Merdeka di seluruh sekolah Kabupaten Hulu Sungai Selatan. Mencakup strategi pembelajaran, penilaian, dan pengembangan karakter siswa sesuai dengan kondisi lokal HSS.',
                'category' => 'Panduan',
                'skpd_code' => 'DISDIK',
                'skpd_name' => 'Dinas Pendidikan',
                'status' => 'published'
            ],
            [
                'title' => 'Program Bantuan Operasional Sekolah HSS 2024',
                'description' => 'Program BOS untuk mendukung operasional sekolah di HSS tahun 2024',
                'content' => 'Program BOS untuk sekolah-sekolah di HSS tahun 2024, termasuk alokasi dana, mekanisme penyaluran, dan pelaporan penggunaan dana.',
                'category' => 'Program',
                'skpd_code' => 'DISDIK',
                'skpd_name' => 'Dinas Pendidikan',
                'status' => 'published'
            ],
            [
                'title' => 'Standar Pelayanan Minimal Pendidikan HSS',
                'description' => 'Standar pelayanan minimal yang harus dipenuhi satuan pendidikan di HSS',
                'content' => 'Standar pelayanan minimal pendidikan di HSS yang harus dipenuhi oleh setiap satuan pendidikan untuk menjamin kualitas pendidikan.',
                'category' => 'Standar',
                'skpd_code' => 'DISDIK',
                'skpd_name' => 'Dinas Pendidikan',
                'status' => 'published'
            ],
            [
                'title' => 'Sistem Informasi Manajemen Pendidikan HSS',
                'description' => 'Sistem informasi untuk mengelola data pendidikan di HSS',
                'content' => 'Sistem informasi untuk mengelola data pendidikan di HSS, termasuk data siswa, guru, sekolah, dan sarana prasarana.',
                'category' => 'Sistem',
                'skpd_code' => 'DISDIK',
                'skpd_name' => 'Dinas Pendidikan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
                'verification_status' => 'approved',
                'verified_by' => 1
            ],
            [
                'title' => 'Program Beasiswa Siswa Berprestasi HSS',
                'description' => 'Program beasiswa untuk siswa berprestasi di HSS',
                'content' => 'Program beasiswa untuk siswa berprestasi di HSS, baik prestasi akademik maupun non-akademik, untuk mendukung kelanjutan pendidikan.',
                'category' => 'Program',
                'skpd_code' => 'DISDIK',
                'skpd_name' => 'Dinas Pendidikan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
                'verification_status' => 'approved',
                'verified_by' => 1
            ],
            
            // DINAS KESEHATAN
            [
                'title' => 'Program Imunisasi Dasar Lengkap HSS',
                'description' => 'Program imunisasi dasar lengkap untuk bayi dan balita di HSS',
                'content' => 'Program imunisasi dasar lengkap untuk bayi dan balita di HSS guna mencegah penyakit yang dapat dicegah dengan imunisasi.',
                'category' => 'Program',
                'skpd_code' => 'DINKES',
                'skpd_name' => 'Dinas Kesehatan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
                'verification_status' => 'approved',
                'verified_by' => 1
            ],
            [
                'title' => 'Panduan Penanganan COVID-19 di HSS',
                'description' => 'Panduan Penanganan COVID-19 di HSS',
                'content' => 'Panduan lengkap penanganan COVID-19 di HSS, termasuk protokol kesehatan, isolasi mandiri, dan rujukan ke fasilitas kesehatan.',
                'category' => 'Panduan',
                'skpd_code' => 'DINKES',
                'skpd_name' => 'Dinas Kesehatan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            [
                'title' => 'Sistem Informasi Kesehatan Daerah HSS',
                'description' => 'Sistem Informasi Kesehatan Daerah HSS',
                'content' => 'Sistem informasi kesehatan daerah untuk monitoring dan evaluasi program kesehatan di HSS secara real-time.',
                'category' => 'Sistem',
                'skpd_code' => 'DINKES',
                'skpd_name' => 'Dinas Kesehatan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            [
                'title' => 'Program Pencegahan Stunting HSS',
                'description' => 'Program Pencegahan Stunting HSS',
                'content' => 'Program pencegahan stunting di HSS melalui intervensi gizi spesifik dan sensitif untuk 1000 hari pertama kehidupan.',
                'category' => 'Program',
                'skpd_code' => 'DINKES',
                'skpd_name' => 'Dinas Kesehatan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            [
                'title' => 'Standar Pelayanan Puskesmas HSS',
                'description' => 'Standar Pelayanan Puskesmas HSS',
                'content' => 'Standar pelayanan kesehatan di Puskesmas HSS untuk menjamin kualitas pelayanan kesehatan dasar bagi masyarakat.',
                'category' => 'Standar',
                'skpd_code' => 'DINKES',
                'skpd_name' => 'Dinas Kesehatan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            
            // SATUAN POLISI PAMONG PRAJA
            [
                'title' => 'Protokol Penegakan Perda HSS',
                'description' => 'Protokol Penegakan Perda HSS',
                'content' => 'Protokol penegakan Peraturan Daerah di HSS, termasuk prosedur penindakan, sanksi, dan koordinasi dengan instansi terkait.',
                'category' => 'Protokol',
                'skpd_code' => 'SATPOLPP',
                'skpd_name' => 'Satpol PP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            [
                'title' => 'Sistem Penanggulangan Kebakaran HSS',
                'description' => 'Sistem Penanggulangan Kebakaran HSS',
                'content' => 'Sistem penanggulangan kebakaran di HSS, termasuk pos pemadam, peralatan, prosedur tanggap darurat, dan edukasi masyarakat.',
                'category' => 'Sistem',
                'skpd_code' => 'SATPOLPP',
                'skpd_name' => 'Satpol PP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,

            ],
            
            // BADAN PENANGGULANGAN BENCANA DAERAH
            [
                'title' => 'Rencana Kontinjensi Banjir HSS',
                'description' => 'Rencana Kontinjensi Banjir HSS',
                'content' => 'Rencana kontinjensi penanggulangan bencana banjir di HSS, termasuk early warning system, evakuasi, dan pemulihan.',
                'category' => 'Perencanaan',
                'skpd_code' => 'BPBD',
                'skpd_name' => 'BPBD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Kesiapsiagaan Bencana untuk Masyarakat HSS',
                'description' => 'Panduan Kesiapsiagaan Bencana untuk Masyarakat HSS',
                'content' => 'Panduan kesiapsiagaan bencana untuk masyarakat HSS, termasuk identifikasi risiko, tas siaga bencana, dan jalur evakuasi.',
                'category' => 'Panduan',
                'skpd_code' => 'BPBD',
                'skpd_name' => 'BPBD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS SOSIAL
            [
                'title' => 'Program Bantuan Sosial HSS 2024',
                'description' => 'Program Bantuan Sosial HSS 2024',
                'content' => 'Program bantuan sosial untuk masyarakat kurang mampu di HSS, termasuk PKH, BPNT, dan bantuan khusus lainnya.',
                'category' => 'Program',
                'skpd_code' => 'DINSOS',
                'skpd_name' => 'Dinas Sosial',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Pelayanan Panti Sosial HSS',
                'description' => 'Panduan Pelayanan Panti Sosial HSS',
                'content' => 'Panduan pelayanan di panti sosial HSS untuk lansia, anak terlantar, dan penyandang disabilitas.',
                'category' => 'Panduan',
                'skpd_code' => 'DINSOS',
                'skpd_name' => 'Dinas Sosial',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS KETAHANAN PANGAN
            [
                'title' => 'Program Diversifikasi Pangan Lokal HSS',
                'description' => 'Program Diversifikasi Pangan Lokal HSS',
                'content' => 'Program diversifikasi pangan berbasis sumber daya lokal HSS, termasuk pengembangan pangan alternatif dan edukasi gizi.',
                'category' => 'Program',
                'skpd_code' => 'DKP',
                'skpd_name' => 'Dinas Ketahanan Pangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Pemantauan Keamanan Pangan HSS',
                'description' => 'Sistem Pemantauan Keamanan Pangan HSS',
                'content' => 'Sistem pemantauan keamanan pangan di HSS, termasuk inspeksi pasar, restoran, dan industri rumah tangga pangan.',
                'category' => 'Sistem',
                'skpd_code' => 'DKP',
                'skpd_name' => 'Dinas Ketahanan Pangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL
            [
                'title' => 'Panduan Pelayanan Administrasi Kependudukan HSS',
                'description' => 'Panduan Pelayanan Administrasi Kependudukan HSS',
                'content' => 'Panduan lengkap pelayanan administrasi kependudukan di HSS, termasuk KTP, KK, akta kelahiran, dan dokumen lainnya.',
                'category' => 'Panduan',
                'skpd_code' => 'DISDUKCAPIL',
                'skpd_name' => 'Disdukcapil',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Informasi Administrasi Kependudukan HSS',
                'description' => 'Sistem Informasi Administrasi Kependudukan HSS',
                'content' => 'Sistem informasi administrasi kependudukan HSS berbasis digital, termasuk fitur, akses, dan prosedur penggunaan.',
                'category' => 'Sistem',
                'skpd_code' => 'DISDUKCAPIL',
                'skpd_name' => 'Disdukcapil',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PEMBERDAYAAN MASYARAKAT DAN DESA
            [
                'title' => 'Program Pemberdayaan Ekonomi Desa HSS',
                'description' => 'Program Pemberdayaan Ekonomi Desa HSS',
                'content' => 'Program pemberdayaan ekonomi masyarakat desa di HSS melalui BUMDes, koperasi, dan UMKM.',
                'category' => 'Program',
                'skpd_code' => 'DISPMD',
                'skpd_name' => 'Dinas PMD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Pengelolaan Dana Desa HSS',
                'description' => 'Panduan Pengelolaan Dana Desa HSS',
                'content' => 'Panduan pengelolaan dana desa di HSS, termasuk perencanaan, pelaksanaan, pelaporan, dan pertanggungjawaban.',
                'category' => 'Panduan',
                'skpd_code' => 'DISPMD',
                'skpd_name' => 'Dinas PMD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PENGENDALIAN PENDUDUK, KB, PP DAN PA
            [
                'title' => 'Program Keluarga Berencana HSS 2024',
                'description' => 'Program Keluarga Berencana HSS 2024',
                'content' => 'Program KB di HSS untuk mengendalikan pertumbuhan penduduk dan meningkatkan kesejahteraan keluarga.',
                'category' => 'Program',
                'skpd_code' => 'DPPKBPPP',
                'skpd_name' => 'Dinas PPKB PPP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Perlindungan Anak HSS',
                'description' => 'Panduan Perlindungan Anak HSS',
                'content' => 'Panduan perlindungan anak di HSS dari kekerasan, eksploitasi, dan penelantaran.',
                'category' => 'Panduan',
                'skpd_code' => 'DPPKBPPP',
                'skpd_name' => 'Dinas PPKB PPP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PERHUBUNGAN
            [
                'title' => 'Sistem Transportasi Publik HSS',
                'description' => 'Sistem Transportasi Publik HSS',
                'content' => 'Sistem transportasi publik di HSS, termasuk rute angkutan, tarif, dan standar pelayanan.',
                'category' => 'Sistem',
                'skpd_code' => 'DISHUB',
                'skpd_name' => 'Dinas Perhubungan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Protokol Keselamatan Transportasi HSS',
                'description' => 'Protokol Keselamatan Transportasi HSS',
                'content' => 'Protokol keselamatan transportasi di HSS, termasuk inspeksi kendaraan, pengawasan lalu lintas, dan edukasi safety riding.',
                'category' => 'Protokol',
                'skpd_code' => 'DISHUB',
                'skpd_name' => 'Dinas Perhubungan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS KOMUNIKASI DAN INFORMATIKA
            [
                'title' => 'Strategi Smart City HSS 2024-2029',
                'description' => 'Strategi Smart City HSS 2024-2029',
                'content' => 'Strategi pengembangan smart city di HSS, termasuk infrastruktur TIK, aplikasi pelayanan publik, dan transformasi digital.',
                'category' => 'Strategi',
                'skpd_code' => 'DISKOMINFO',
                'skpd_name' => 'Diskominfo',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Keamanan Siber Pemerintah HSS',
                'description' => 'Panduan Keamanan Siber Pemerintah HSS',
                'content' => 'Panduan keamanan siber untuk instansi pemerintah HSS, termasuk proteksi data, backup system, dan incident response.',
                'category' => 'Panduan',
                'skpd_code' => 'DISKOMINFO',
                'skpd_name' => 'Diskominfo',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS TENAGA KERJA, KOPERASI DAN PERINDUSTRIAN
            [
                'title' => 'Program Pelatihan Kerja HSS 2024',
                'description' => 'Program Pelatihan Kerja HSS 2024',
                'content' => 'Program pelatihan kerja untuk meningkatkan kompetensi tenaga kerja HSS sesuai kebutuhan industri.',
                'category' => 'Program',
                'skpd_code' => 'DISNAKERKOP',
                'skpd_name' => 'Disnakerkop',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Pengembangan UMKM HSS',
                'description' => 'Panduan Pengembangan UMKM HSS',
                'content' => 'Panduan pengembangan UMKM di HSS, termasuk akses permodalan, pemasaran, dan pembinaan manajemen.',
                'category' => 'Panduan',
                'skpd_code' => 'DISNAKERKOP',
                'skpd_name' => 'Disnakerkop',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PENANAMAN MODAL DAN PTSP
            [
                'title' => 'Panduan Investasi di HSS',
                'description' => 'Panduan Investasi di HSS',
                'content' => 'Panduan investasi di HSS, termasuk potensi sektor, insentif, prosedur perizinan, dan fasilitas pendukung.',
                'category' => 'Panduan',
                'skpd_code' => 'DPMPTSP',
                'skpd_name' => 'DPMPTSP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Pelayanan Terpadu Satu Pintu HSS',
                'description' => 'Sistem Pelayanan Terpadu Satu Pintu HSS',
                'content' => 'Sistem PTSP HSS untuk mempermudah pelayanan perizinan dan non-perizinan secara terintegrasi.',
                'category' => 'Sistem',
                'skpd_code' => 'DPMPTSP',
                'skpd_name' => 'DPMPTSP',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PEMUDA OLAHRAGA DAN PARIWISATA
            [
                'title' => 'Pengembangan Destinasi Wisata Loksado HSS',
                'description' => 'Pengembangan Destinasi Wisata Loksado HSS',
                'content' => 'Strategi pengembangan destinasi wisata Loksado sebagai ikon pariwisata HSS, termasuk infrastruktur, promosi, dan pelayanan.',
                'category' => 'Strategi',
                'skpd_code' => 'DISPORAPAR',
                'skpd_name' => 'Disporapar',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Pembinaan Atlet HSS',
                'description' => 'Program Pembinaan Atlet HSS',
                'content' => 'Program pembinaan atlet HSS untuk prestasi di tingkat provinsi dan nasional, termasuk pelatihan, kompetisi, dan beasiswa.',
                'category' => 'Program',
                'skpd_code' => 'DISPORAPAR',
                'skpd_name' => 'Disporapar',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PERPUSTAKAAN DAN KEARSIPAN
            [
                'title' => 'Program Literasi Masyarakat HSS',
                'description' => 'Program Literasi Masyarakat HSS',
                'content' => 'Program peningkatan literasi masyarakat HSS melalui perpustakaan keliling, taman baca, dan festival literasi.',
                'category' => 'Program',
                'skpd_code' => 'DPK',
                'skpd_name' => 'Dinas Perpustakaan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Pengelolaan Arsip Daerah HSS',
                'description' => 'Sistem Pengelolaan Arsip Daerah HSS',
                'content' => 'Sistem pengelolaan arsip daerah HSS secara digital untuk preservasi dan akses informasi publik.',
                'category' => 'Sistem',
                'skpd_code' => 'DPK',
                'skpd_name' => 'Dinas Perpustakaan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PERIKANAN
            [
                'title' => 'Program Budidaya Ikan Air Tawar HSS',
                'description' => 'Program Budidaya Ikan Air Tawar HSS',
                'content' => 'Program pengembangan budidaya ikan air tawar di HSS, termasuk teknologi, bantuan bibit, dan pemasaran.',
                'category' => 'Program',
                'skpd_code' => 'DISKAN',
                'skpd_name' => 'Dinas Perikanan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Konservasi Sumber Daya Perikanan HSS',
                'description' => 'Panduan Konservasi Sumber Daya Perikanan HSS',
                'content' => 'Panduan konservasi sumber daya perikanan di sungai dan danau HSS untuk keberlanjutan ekosistem.',
                'category' => 'Panduan',
                'skpd_code' => 'DISKAN',
                'skpd_name' => 'Dinas Perikanan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PERTANIAN
            [
                'title' => 'Program Peningkatan Produktivitas Padi HSS',
                'description' => 'Program Peningkatan Produktivitas Padi HSS',
                'content' => 'Program peningkatan produktivitas padi di HSS melalui teknologi, benih unggul, dan penyuluhan pertanian.',
                'category' => 'Program',
                'skpd_code' => 'DISPERTAN',
                'skpd_name' => 'Dinas Pertanian',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Pengendalian Hama dan Penyakit Tanaman HSS',
                'description' => 'Panduan Pengendalian Hama dan Penyakit Tanaman HSS',
                'content' => 'Panduan pengendalian hama dan penyakit tanaman di HSS secara terpadu dan ramah lingkungan.',
                'category' => 'Panduan',
                'skpd_code' => 'DISPERTAN',
                'skpd_name' => 'Dinas Pertanian',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PERDAGANGAN
            [
                'title' => 'Program Pengembangan Pasar Tradisional HSS',
                'description' => 'Program Pengembangan Pasar Tradisional HSS',
                'content' => 'Program revitalisasi dan pengembangan pasar tradisional di HSS untuk meningkatkan daya saing dan kenyamanan.',
                'category' => 'Program',
                'skpd_code' => 'DISPERDAG',
                'skpd_name' => 'Dinas Perdagangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Pengawasan Perdagangan HSS',
                'description' => 'Sistem Pengawasan Perdagangan HSS',
                'content' => 'Sistem pengawasan perdagangan di HSS untuk melindungi konsumen dan memastikan persaingan usaha yang sehat.',
                'category' => 'Sistem',
                'skpd_code' => 'DISPERDAG',
                'skpd_name' => 'Dinas Perdagangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // DINAS PEKERJAAN UMUM DAN PERUMAHAN RAKYAT
            [
                'title' => 'Master Plan Infrastruktur Jalan HSS 2024-2029',
                'description' => 'Master Plan Infrastruktur Jalan HSS 2024-2029',
                'content' => 'Rencana induk pembangunan infrastruktur jalan di HSS untuk periode 2024-2029, termasuk prioritas pembangunan dan pemeliharaan.',
                'category' => 'Perencanaan',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Standar Konstruksi Jalan HSS',
                'description' => 'Standar Konstruksi Jalan HSS',
                'content' => 'Standar konstruksi jalan di HSS sesuai dengan kondisi geografis dan iklim setempat, termasuk spesifikasi material dan metode pelaksanaan.',
                'category' => 'Standar',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Pembangunan Rumah Layak Huni HSS',
                'description' => 'Program Pembangunan Rumah Layak Huni HSS',
                'content' => 'Program pembangunan dan rehabilitasi rumah tidak layak huni (RTLH) di HSS untuk meningkatkan kualitas hidup masyarakat.',
                'category' => 'Program',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Informasi Manajemen Aset Daerah HSS',
                'description' => 'Sistem Informasi Manajemen Aset Daerah HSS',
                'content' => 'Sistem informasi untuk mengelola aset daerah HSS, termasuk inventarisasi, pemeliharaan, dan pemanfaatan aset.',
                'category' => 'Sistem',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Teknis Pembangunan Drainase HSS',
                'description' => 'Panduan Teknis Pembangunan Drainase HSS',
                'content' => 'Panduan teknis pembangunan sistem drainase di HSS untuk mengatasi masalah banjir dan genangan air.',
                'category' => 'Panduan',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Penyediaan Air Bersih Desa HSS',
                'description' => 'Program Penyediaan Air Bersih Desa HSS',
                'content' => 'Program penyediaan air bersih untuk desa-desa di HSS melalui pembangunan sumur bor, PAMSIMAS, dan sistem perpipaan.',
                'category' => 'Program',
                'skpd_code' => 'DPUPR',
                'skpd_name' => 'Dinas PUPR',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BAGIAN HUKUM
            [
                'title' => 'Panduan Penyusunan Peraturan Daerah HSS',
                'description' => 'Panduan Penyusunan Peraturan Daerah HSS',
                'content' => 'Panduan teknis penyusunan Peraturan Daerah di HSS, termasuk tahapan, format, dan prosedur harmonisasi.',
                'category' => 'Panduan',
                'skpd_code' => 'BAGHUKUM',
                'skpd_name' => 'Bagian Hukum',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Bantuan Hukum Masyarakat HSS',
                'description' => 'Sistem Bantuan Hukum Masyarakat HSS',
                'content' => 'Sistem pemberian bantuan hukum untuk masyarakat kurang mampu di HSS, termasuk prosedur dan kriteria penerima.',
                'category' => 'Sistem',
                'skpd_code' => 'BAGHUKUM',
                'skpd_name' => 'Bagian Hukum',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BAGIAN PEMERINTAHAN
            [
                'title' => 'Protokol Penyelenggaraan Pemerintahan HSS',
                'description' => 'Protokol Penyelenggaraan Pemerintahan HSS',
                'content' => 'Protokol penyelenggaraan pemerintahan daerah HSS, termasuk tata cara rapat, upacara, dan kegiatan kenegaraan.',
                'category' => 'Protokol',
                'skpd_code' => 'BAGPEM',
                'skpd_name' => 'Bagian Pemerintahan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Koordinasi Antar SKPD HSS',
                'description' => 'Panduan Koordinasi Antar SKPD HSS',
                'content' => 'Panduan koordinasi antar SKPD di HSS untuk meningkatkan sinergi dan efektivitas pelayanan publik.',
                'category' => 'Panduan',
                'skpd_code' => 'BAGPEM',
                'skpd_name' => 'Bagian Pemerintahan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // KECAMATAN KANDANGAN
            [
                'title' => 'Profil Kecamatan Kandangan HSS',
                'description' => 'Profil Kecamatan Kandangan HSS',
                'content' => 'Profil lengkap Kecamatan Kandangan sebagai ibukota HSS, termasuk demografi, potensi ekonomi, dan fasilitas publik.',
                'category' => 'Profil',
                'skpd_code' => 'KECKANDANGAN',
                'skpd_name' => 'Kecamatan Kandangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Pemberdayaan Masyarakat Kandangan',
                'description' => 'Program Pemberdayaan Masyarakat Kandangan',
                'content' => 'Program pemberdayaan masyarakat di Kecamatan Kandangan melalui pelatihan keterampilan dan pengembangan UMKM.',
                'category' => 'Program',
                'skpd_code' => 'KECKANDANGAN',
                'skpd_name' => 'Kecamatan Kandangan',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // KECAMATAN ANGKINANG
            [
                'title' => 'Potensi Wisata Kecamatan Angkinang',
                'description' => 'Potensi Wisata Kecamatan Angkinang',
                'content' => 'Potensi wisata alam dan budaya di Kecamatan Angkinang, termasuk air terjun, situs sejarah, dan tradisi lokal.',
                'category' => 'Potensi',
                'skpd_code' => 'KECANGKINANG',
                'skpd_name' => 'Kecamatan Angkinang',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Pertanian Organik Angkinang',
                'description' => 'Program Pertanian Organik Angkinang',
                'content' => 'Program pengembangan pertanian organik di Kecamatan Angkinang untuk meningkatkan nilai tambah produk pertanian.',
                'category' => 'Program',
                'skpd_code' => 'KECANGKINANG',
                'skpd_name' => 'Kecamatan Angkinang',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // KELURAHAN KANDANGAN KOTA
            [
                'title' => 'Profil Kelurahan Kandangan Kota',
                'description' => 'Profil Kelurahan Kandangan Kota',
                'content' => 'Profil Kelurahan Kandangan Kota sebagai pusat pemerintahan HSS, termasuk fasilitas perkantoran dan perdagangan.',
                'category' => 'Profil',
                'skpd_code' => 'KELKANDANGANKOTA',
                'skpd_name' => 'Kelurahan Kandangan Kota',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Smart Kelurahan Kandangan Kota',
                'description' => 'Program Smart Kelurahan Kandangan Kota',
                'content' => 'Program smart kelurahan di Kandangan Kota dengan pemanfaatan teknologi untuk pelayanan administrasi dan informasi publik.',
                'category' => 'Program',
                'skpd_code' => 'KELKANDANGANKOTA',
                'skpd_name' => 'Kelurahan Kandangan Kota',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // INSPEKTORAT
            [
                'title' => 'Panduan Audit Internal Pemerintah HSS',
                'description' => 'Panduan Audit Internal Pemerintah HSS',
                'content' => 'Panduan pelaksanaan audit internal di lingkungan Pemerintah HSS, termasuk metodologi, standar, dan pelaporan.',
                'category' => 'Panduan',
                'skpd_code' => 'INSPEKTORAT',
                'skpd_name' => 'Inspektorat',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Pengawasan Kinerja SKPD HSS',
                'description' => 'Sistem Pengawasan Kinerja SKPD HSS',
                'content' => 'Sistem pengawasan kinerja SKPD di HSS untuk memastikan pencapaian target dan efektivitas program.',
                'category' => 'Sistem',
                'skpd_code' => 'INSPEKTORAT',
                'skpd_name' => 'Inspektorat',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BAPPELITBANGDA
            [
                'title' => 'RPJMD HSS 2021-2026',
                'description' => 'RPJMD HSS 2021-2026',
                'content' => 'Rencana Pembangunan Jangka Menengah Daerah HSS 2021-2026, termasuk visi, misi, strategi, dan program prioritas.',
                'category' => 'Perencanaan',
                'skpd_code' => 'BAPPELITBANGDA',
                'skpd_name' => 'Bappelitbangda',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Monitoring dan Evaluasi Pembangunan HSS',
                'description' => 'Sistem Monitoring dan Evaluasi Pembangunan HSS',
                'content' => 'Sistem monitoring dan evaluasi pelaksanaan pembangunan di HSS untuk mengukur capaian kinerja dan dampak program.',
                'category' => 'Sistem',
                'skpd_code' => 'BAPPELITBANGDA',
                'skpd_name' => 'Bappelitbangda',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BAKEUDA
            [
                'title' => 'Strategi Peningkatan PAD HSS 2024',
                'description' => 'Strategi Peningkatan PAD HSS 2024',
                'content' => 'Strategi peningkatan Pendapatan Asli Daerah HSS melalui optimalisasi pajak, retribusi, dan pengelolaan aset daerah.',
                'category' => 'Strategi',
                'skpd_code' => 'BAKEUDA',
                'skpd_name' => 'Bakeuda',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Panduan Pengelolaan Keuangan Daerah HSS',
                'description' => 'Panduan Pengelolaan Keuangan Daerah HSS',
                'content' => 'Panduan pengelolaan keuangan daerah HSS sesuai dengan peraturan perundang-undangan dan prinsip good governance.',
                'category' => 'Panduan',
                'skpd_code' => 'BAKEUDA',
                'skpd_name' => 'Bakeuda',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BKPSDM
            [
                'title' => 'Program Pengembangan Kompetensi ASN HSS',
                'description' => 'Program Pengembangan Kompetensi ASN HSS',
                'content' => 'Program pengembangan kompetensi ASN di HSS melalui diklat, bimbingan teknis, dan sertifikasi profesi.',
                'category' => 'Program',
                'skpd_code' => 'BKPSDM',
                'skpd_name' => 'BKPSDM',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Sistem Manajemen Kinerja ASN HSS',
                'description' => 'Sistem Manajemen Kinerja ASN HSS',
                'content' => 'Sistem manajemen kinerja ASN di HSS untuk meningkatkan produktivitas dan akuntabilitas pelayanan publik.',
                'category' => 'Sistem',
                'skpd_code' => 'BKPSDM',
                'skpd_name' => 'BKPSDM',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // SEKRETARIAT DPRD
            [
                'title' => 'Panduan Tata Tertib DPRD HSS',
                'description' => 'Panduan Tata Tertib DPRD HSS',
                'content' => 'Panduan tata tertib dan prosedur persidangan DPRD HSS, termasuk mekanisme pengambilan keputusan dan etika anggota.',
                'category' => 'Panduan',
                'skpd_code' => 'SETDPRD',
                'skpd_name' => 'Sekretariat DPRD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Peningkatan Kapasitas Anggota DPRD HSS',
                'description' => 'Program Peningkatan Kapasitas Anggota DPRD HSS',
                'content' => 'Program peningkatan kapasitas anggota DPRD HSS dalam menjalankan fungsi legislasi, anggaran, dan pengawasan.',
                'category' => 'Program',
                'skpd_code' => 'SETDPRD',
                'skpd_name' => 'Sekretariat DPRD',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            
            // BAKESBANGPOL
            [
                'title' => 'Strategi Ketahanan Nasional HSS',
                'description' => 'Strategi Ketahanan Nasional HSS',
                'content' => 'Strategi penguatan ketahanan nasional di HSS melalui pembinaan ideologi, politik, ekonomi, sosial budaya, dan pertahanan keamanan.',
                'category' => 'Strategi',
                'skpd_code' => 'BAKESBANGPOL',
                'skpd_name' => 'Bakesbangpol',
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
            ],
            [
                'title' => 'Program Pembinaan Wawasan Kebangsaan HSS',
                'description' => 'Program Pembinaan Wawasan Kebangsaan HSS',
                'content' => 'Program pembinaan wawasan kebangsaan untuk masyarakat HSS guna memperkuat persatuan dan kesatuan bangsa.',
                 'category' => 'Program',
                 'skpd_code' => 'BAKESBANGPOL',
                 'skpd_name' => 'Bakesbangpol',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // DINAS LINGKUNGAN HIDUP
             [
                 'title' => 'Program Pengelolaan Sampah Terpadu HSS',
                'description' => 'Program Pengelolaan Sampah Terpadu HSS',
                'content' => 'Program pengelolaan sampah terpadu di HSS dari hulu ke hilir, termasuk reduce, reuse, recycle, dan pengolahan akhir.',
                 'category' => 'Program',
                 'skpd_code' => 'DLH',
                 'skpd_name' => 'Dinas Lingkungan Hidup',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pengendalian Pencemaran Air HSS',
                'description' => 'Panduan Pengendalian Pencemaran Air HSS',
                'content' => 'Panduan pengendalian pencemaran air di HSS, termasuk monitoring kualitas air sungai dan danau.',
                 'category' => 'Panduan',
                 'skpd_code' => 'DLH',
                 'skpd_name' => 'Dinas Lingkungan Hidup',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // DINAS ESDM
             [
                 'title' => 'Potensi Energi Terbarukan HSS',
                'description' => 'Potensi Energi Terbarukan HSS',
                'content' => 'Potensi energi terbarukan di HSS, termasuk mikrohidro, solar panel, dan biomassa untuk kemandirian energi.',
                 'category' => 'Potensi',
                 'skpd_code' => 'DESDM',
                 'skpd_name' => 'Dinas ESDM',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Hemat Energi HSS',
                'description' => 'Program Hemat Energi HSS',
                'content' => 'Program hemat energi untuk rumah tangga dan industri di HSS guna mengurangi konsumsi dan biaya energi.',
                 'category' => 'Program',
                 'skpd_code' => 'DESDM',
                 'skpd_name' => 'Dinas ESDM',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // DINAS KEHUTANAN
             [
                 'title' => 'Program Rehabilitasi Hutan HSS',
                'description' => 'Program Rehabilitasi Hutan HSS',
                'content' => 'Program rehabilitasi hutan dan lahan kritis di HSS untuk mencegah erosi dan menjaga keseimbangan ekosistem.',
                 'category' => 'Program',
                 'skpd_code' => 'DISHUT',
                 'skpd_name' => 'Dinas Kehutanan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pengelolaan Hutan Lestari HSS',
                'description' => 'Panduan Pengelolaan Hutan Lestari HSS',
                'content' => 'Panduan pengelolaan hutan lestari di HSS dengan prinsip kelestarian fungsi produksi, konservasi, dan lindung.',
                 'category' => 'Panduan',
                 'skpd_code' => 'DISHUT',
                 'skpd_name' => 'Dinas Kehutanan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // DINAS PETERNAKAN
             [
                 'title' => 'Program Pengembangan Ternak Sapi HSS',
                'description' => 'Program Pengembangan Ternak Sapi HSS',
                'content' => 'Program pengembangan ternak sapi di HSS untuk meningkatkan populasi dan produktivitas daging serta susu.',
                 'category' => 'Program',
                 'skpd_code' => 'DISNAK',
                 'skpd_name' => 'Dinas Peternakan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pencegahan Penyakit Hewan HSS',
                'description' => 'Panduan Pencegahan Penyakit Hewan HSS',
                'content' => 'Panduan pencegahan dan pengendalian penyakit hewan di HSS untuk menjaga kesehatan ternak dan keamanan pangan.',
                 'category' => 'Panduan',
                 'skpd_code' => 'DISNAK',
                 'skpd_name' => 'Dinas Peternakan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // BPS HSS
             [
                 'title' => 'Data Statistik Pembangunan HSS 2023',
                'description' => 'Data Statistik Pembangunan HSS 2023',
                'content' => 'Data statistik pembangunan HSS tahun 2023, termasuk indikator ekonomi, sosial, dan demografi.',
                 'category' => 'Data',
                 'skpd_code' => 'BPS',
                 'skpd_name' => 'BPS HSS',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Metodologi Survei dan Sensus HSS',
                'description' => 'Metodologi Survei dan Sensus HSS',
                'content' => 'Metodologi pelaksanaan survei dan sensus di HSS untuk menghasilkan data yang akurat dan representatif.',
                 'category' => 'Metodologi',
                 'skpd_code' => 'BPS',
                 'skpd_name' => 'BPS HSS',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // KECAMATAN LOKSADO
             [
                 'title' => 'Potensi Wisata Alam Loksado',
                'description' => 'Potensi Wisata Alam Loksado',
                'content' => 'Potensi wisata alam di Kecamatan Loksado, termasuk air terjun, arung jeram, dan trekking pegunungan Meratus.',
                 'category' => 'Potensi',
                 'skpd_code' => 'KECLOKSADO',
                 'skpd_name' => 'Kecamatan Loksado',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Pemberdayaan Masyarakat Dayak Loksado',
                'description' => 'Program Pemberdayaan Masyarakat Dayak Loksado',
                'content' => 'Program pemberdayaan masyarakat Dayak di Loksado melalui pelestarian budaya dan pengembangan ekonomi kreatif.',
                 'category' => 'Program',
                 'skpd_code' => 'KECLOKSADO',
                 'skpd_name' => 'Kecamatan Loksado',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // KECAMATAN PADANG BATUNG
             [
                 'title' => 'Program Pengembangan Pertanian Padang Batung',
                'description' => 'Program Pengembangan Pertanian Padang Batung',
                'content' => 'Program pengembangan sektor pertanian di Kecamatan Padang Batung, termasuk irigasi dan teknologi pertanian.',
                 'category' => 'Program',
                 'skpd_code' => 'KECPADANGBATUNG',
                 'skpd_name' => 'Kecamatan Padang Batung',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Profil Demografis Padang Batung',
                'description' => 'Profil Demografis Padang Batung',
                'content' => 'Profil demografis Kecamatan Padang Batung, termasuk jumlah penduduk, mata pencaharian, dan tingkat pendidikan.',
                 'category' => 'Profil',
                 'skpd_code' => 'KECPADANGBATUNG',
                 'skpd_name' => 'Kecamatan Padang Batung',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // PUSKESMAS KANDANGAN
             [
                 'title' => 'Program Imunisasi Puskesmas Kandangan',
                'description' => 'Program Imunisasi Puskesmas Kandangan',
                'content' => 'Program imunisasi lengkap di Puskesmas Kandangan untuk mencegah penyakit menular pada anak dan dewasa.',
                 'category' => 'Program',
                 'skpd_code' => 'PKMKANDANGAN',
                 'skpd_name' => 'Puskesmas Kandangan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pelayanan Kesehatan Ibu dan Anak',
                'description' => 'Panduan Pelayanan Kesehatan Ibu dan Anak',
                'content' => 'Panduan pelayanan kesehatan ibu dan anak di Puskesmas Kandangan, termasuk ANC, persalinan, dan posyandu.',
                 'category' => 'Panduan',
                 'skpd_code' => 'PKMKANDANGAN',
                 'skpd_name' => 'Puskesmas Kandangan',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // PUSKESMAS LOKSADO
             [
                 'title' => 'Program Kesehatan Masyarakat Terpencil Loksado',
                'description' => 'Program Kesehatan Masyarakat Terpencil Loksado',
                'content' => 'Program pelayanan kesehatan untuk masyarakat terpencil di wilayah Puskesmas Loksado dengan pendekatan mobile clinic.',
                 'category' => 'Program',
                 'skpd_code' => 'PKMLOKSADO',
                 'skpd_name' => 'Puskesmas Loksado',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pengobatan Tradisional Terintegrasi',
                'description' => 'Panduan Pengobatan Tradisional Terintegrasi',
                'content' => 'Panduan integrasi pengobatan tradisional dengan pelayanan kesehatan modern di Puskesmas Loksado.',
                 'category' => 'Panduan',
                 'skpd_code' => 'PKMLOKSADO',
                 'skpd_name' => 'Puskesmas Loksado',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             
             // DATA TAMBAHAN UNTUK MENCAPAI 256
             [
                 'title' => 'Rencana Tata Ruang Wilayah HSS 2021-2041',
                'description' => 'Rencana Tata Ruang Wilayah HSS 2021-2041',
                'content' => 'Rencana Tata Ruang Wilayah HSS 2021-2041 sebagai acuan pengembangan wilayah yang berkelanjutan.',
                 'category' => 'Perencanaan',
                 'skpd_code' => 'BAPPELITBANGDA',
                 'skpd_name' => 'Bappelitbangda',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Digitalisasi Pelayanan Publik HSS',
                'description' => 'Program Digitalisasi Pelayanan Publik HSS',
                'content' => 'Program digitalisasi pelayanan publik di HSS untuk meningkatkan efisiensi dan transparansi.',
                 'category' => 'Program',
                 'skpd_code' => 'DISKOMINFO',
                 'skpd_name' => 'Diskominfo',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Strategi Pengembangan Ekonomi Kreatif HSS',
                'description' => 'Strategi Pengembangan Ekonomi Kreatif HSS',
                'content' => 'Strategi pengembangan ekonomi kreatif di HSS berbasis potensi lokal dan kearifan budaya.',
                 'category' => 'Strategi',
                 'skpd_code' => 'DISNAKERKOP',
                 'skpd_name' => 'Disnakerkop',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Peningkatan Kualitas Jalan Desa HSS',
                'description' => 'Program Peningkatan Kualitas Jalan Desa HSS',
                'content' => 'Program peningkatan kualitas jalan desa di HSS untuk mendukung konektivitas dan aksesibilitas masyarakat.',
                 'category' => 'Program',
                 'skpd_code' => 'DPUPR',
                 'skpd_name' => 'Dinas PU',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Panduan Pengelolaan Keuangan Desa HSS',
                'description' => 'Panduan Pengelolaan Keuangan Desa HSS',
                'content' => 'Panduan pengelolaan keuangan desa di HSS sesuai dengan peraturan dan prinsip akuntabilitas.',
                 'category' => 'Panduan',
                 'skpd_code' => 'DISPMD',
                 'skpd_name' => 'Dinas PMD',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Bantuan Hukum Gratis HSS',
                'description' => 'Program Bantuan Hukum Gratis HSS',
                'content' => 'Program bantuan hukum gratis untuk masyarakat kurang mampu di HSS melalui kerjasama dengan organisasi advokat.',
                 'category' => 'Program',
                 'skpd_code' => 'BAGHUKUM',
                 'skpd_name' => 'Bagian Hukum',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Sistem Informasi Geografis HSS',
                'description' => 'Sistem Informasi Geografis HSS',
                'content' => 'Sistem Informasi Geografis HSS untuk mendukung perencanaan pembangunan dan pengelolaan sumber daya.',
                 'category' => 'Sistem',
                 'skpd_code' => 'BAPPELITBANGDA',
                 'skpd_name' => 'Bappelitbangda',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ],
             [
                 'title' => 'Program Peningkatan Kapasitas Aparatur HSS',
                'description' => 'Program Peningkatan Kapasitas Aparatur HSS',
                'content' => 'Program peningkatan kapasitas aparatur pemerintah HSS melalui pendidikan dan pelatihan berkelanjutan.',
                 'category' => 'Program',
                 'skpd_code' => 'BKPSDM',
                 'skpd_name' => 'BKPSDM',
                 'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => 1,
             ]
         ];
    }
}