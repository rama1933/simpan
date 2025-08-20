<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterSKPD;

class MasterSKPDSeeder extends Seeder
{
    public function run(): void
    {
        $skpds = [
            ['kode_skpd' => 'DISDIK', 'nama_skpd' => 'Dinas Pendidikan Dan Kebudayaan', 'is_active' => true],
            ['kode_skpd' => 'DINKES', 'nama_skpd' => 'Dinas Kesehatan', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.PADANGBATUNG', 'nama_skpd' => 'Puskesmas Padang Batung', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.KALIRING', 'nama_skpd' => 'Puskesmas Kaliring', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.MALINAU', 'nama_skpd' => 'Puskesmas Malinau', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.LOKSADO', 'nama_skpd' => 'Puskesmas Loksado', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.TELAGALANGSAT', 'nama_skpd' => 'Puskesmas Telaga Langsat', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.ANGKINANG', 'nama_skpd' => 'Puskesmas Angkinang', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.BAMBAN', 'nama_skpd' => 'Puskesmas Bamban', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.KANDANGAN', 'nama_skpd' => 'Puskesmas Kandangan', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.GAMBAH', 'nama_skpd' => 'Puskesmas Gambah', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.JAMBUHILIR', 'nama_skpd' => 'Puskesmas Jambu Hilir', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.SUNGAIRAYA', 'nama_skpd' => 'Puskesmas Sungai Raya', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.BATANGKULUR', 'nama_skpd' => 'Puskesmas Batang Kulur', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.SIMPUR', 'nama_skpd' => 'Puskesmas Simpur', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.WASAH', 'nama_skpd' => 'Puskesmas Wasah', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.KALUMPANG', 'nama_skpd' => 'Puskesmas Kalumpang', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.BAYANAN', 'nama_skpd' => 'Puskesmas Bayanan', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.SUNGAIPINANG', 'nama_skpd' => 'Puskesmas Sungai Pinang', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.BARUHJAYA', 'nama_skpd' => 'Puskesmas Baruh Jaya', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.BAJAYAU', 'nama_skpd' => 'Puskesmas Bajayau', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.NEGARA', 'nama_skpd' => 'Puskesmas Negara', 'is_active' => true],
            ['kode_skpd' => 'PUSKESMAS.PASUNGKAN', 'nama_skpd' => 'Puskesmas Pasungkan', 'is_active' => true],
            ['kode_skpd' => 'RSUD.DAHASEJAHTERA', 'nama_skpd' => 'RSUD Daha Sejahtera', 'is_active' => true],
            ['kode_skpd' => 'RSUD.HASANBASRY', 'nama_skpd' => 'RSUD Brigjend H. Hasan Basry Kandangan', 'is_active' => true],
            ['kode_skpd' => 'PUTR', 'nama_skpd' => 'Dinas Pekerjaan Umum Dan Tata Ruang', 'is_active' => true],
            ['kode_skpd' => 'DISPERA', 'nama_skpd' => 'Dinas Perumahan Rakyat, Kawasan Pemukiman dan Lingkungan Hidup', 'is_active' => true],
            ['kode_skpd' => 'SATPOLPP', 'nama_skpd' => 'Satuan Polisi Pamong Praja dan Pemadam Kebakaran', 'is_active' => true],
            ['kode_skpd' => 'BPPD', 'nama_skpd' => 'Badan Penanggulangan Bencana Daerah', 'is_active' => true],
            ['kode_skpd' => 'DINSOS', 'nama_skpd' => 'Dinas Sosial', 'is_active' => true],
            ['kode_skpd' => 'DKP', 'nama_skpd' => 'Dinas Ketahanan Pangan', 'is_active' => true],
            ['kode_skpd' => 'DISDUKCAPIL', 'nama_skpd' => 'Dinas Kependudukan Dan Pencatatan Sipil', 'is_active' => true],
            ['kode_skpd' => 'DISPMD', 'nama_skpd' => 'Dinas Pemberdayaan Masyarakat dan Desa', 'is_active' => true],
            ['kode_skpd' => 'DPPKBPPP', 'nama_skpd' => 'Dinas Pengendalian Penduduk, Keluarga Berencana, Pemberdayaan Perempuan dan  Perlindungan Anak', 'is_active' => true],
            ['kode_skpd' => 'DISHUB', 'nama_skpd' => 'Dinas Perhubungan', 'is_active' => true],
            ['kode_skpd' => 'DISKOMINFO', 'nama_skpd' => 'Dinas Komunikasi dan Informatika', 'is_active' => true],
            ['kode_skpd' => 'DISNAKERKOP', 'nama_skpd' => 'Dinas Tenaga Kerja, Koperasi Usaha Kecil dan Perindustrian', 'is_active' => true],
            ['kode_skpd' => 'DPMPTSP', 'nama_skpd' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'is_active' => true],
            ['kode_skpd' => 'DISPORAPAR', 'nama_skpd' => 'Dinas Pemuda Olahraga dan Pariwisata', 'is_active' => true],
            ['kode_skpd' => 'DPK', 'nama_skpd' => 'Dinas Perpustakaan dan Kearsipan', 'is_active' => true],
            ['kode_skpd' => 'DISKAN', 'nama_skpd' => 'Dinas Perikanan', 'is_active' => true],
            ['kode_skpd' => 'DISPERTAN', 'nama_skpd' => 'Dinas Pertanian', 'is_active' => true],
            ['kode_skpd' => 'DISPERDAG', 'nama_skpd' => 'Dinas Perdagangan', 'is_active' => true],
            ['kode_skpd' => 'BAG.HUKUM', 'nama_skpd' => 'Bagian Hukum', 'is_active' => true],
            ['kode_skpd' => 'BAG.PEMERINTAHAN', 'nama_skpd' => 'Bagian Pemerintahan', 'is_active' => true],
            ['kode_skpd' => 'BAG.PROTOKOL', 'nama_skpd' => 'Bagian Protokol dan Komunikasi Pimpinan', 'is_active' => true],
            ['kode_skpd' => 'BAG.KESEJAHTERAANRAKYAT', 'nama_skpd' => 'Bagian Kesejahteraan Rakyat', 'is_active' => true],
            ['kode_skpd' => 'BAG.ORGANISASI', 'nama_skpd' => 'Bagian Organisasi', 'is_active' => true],
            ['kode_skpd' => 'BAG.UMUM', 'nama_skpd' => 'Bagian Umum', 'is_active' => true],
            ['kode_skpd' => 'BAG.PENGADAANBARANGJASA', 'nama_skpd' => 'Bagian Pengadaan Barang dan Jasa', 'is_active' => true],
            ['kode_skpd' => 'BAG.PEREKONOMIANPEMBANGUNAN', 'nama_skpd' => 'Bagian Perekonomian dan Pembangunan', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.SUNGAIRAYA', 'nama_skpd' => 'Kecamatan Sungai Raya', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.PADANGBATUNG', 'nama_skpd' => 'Kecamatan Padang Batung', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.TELAGALANGSAT', 'nama_skpd' => 'Kecamatan Telaga Langsat', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.ANGKINANG', 'nama_skpd' => 'Kecamatan Angkinang', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.KANDANGAN', 'nama_skpd' => 'Kecamatan Kandangan', 'is_active' => true],
            ['kode_skpd' => 'KELURAHAN.KANDANGANKOTA', 'nama_skpd' => 'Kelurahan Kandangan Kota', 'is_active' => true],
            ['kode_skpd' => 'KELURAHAN.KANDANGANBARAT', 'nama_skpd' => 'Kelurahan Kandangan Barat', 'is_active' => true],
            ['kode_skpd' => 'KELURAHAN.KANDANGANUTARA', 'nama_skpd' => 'Kelurahan Kandangan Utara', 'is_active' => true],
            ['kode_skpd' => 'KELURAHAN.JAMBUHILIR', 'nama_skpd' => 'Kelurahan Jambu Hilir', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.SIMPUR', 'nama_skpd' => 'Kecamatan Simpur', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.DAHASELATAN', 'nama_skpd' => 'Kecamatan Daha Selatan', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.DAHAUTARA', 'nama_skpd' => 'Kecamatan Daha Utara', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.KALUMPANG', 'nama_skpd' => 'Kecamatan Kalumpang', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.LOKSADO', 'nama_skpd' => 'Kecamatan Loksado', 'is_active' => true],
            ['kode_skpd' => 'KECAMATAN.DAHABARAT', 'nama_skpd' => 'Kecamatan Daha Barat', 'is_active' => true],
            ['kode_skpd' => 'INSPEKTORAT', 'nama_skpd' => 'Inspektorat Daerah', 'is_active' => true],
            ['kode_skpd' => 'BAPPELITBANGDA', 'nama_skpd' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah', 'is_active' => true],
            ['kode_skpd' => 'BAKEUDA', 'nama_skpd' => 'Badan Pengelolaan Keuangan dan Pendapatan Daerah', 'is_active' => true],
            ['kode_skpd' => 'BKPSDM', 'nama_skpd' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia', 'is_active' => true],
            ['kode_skpd' => 'SEKRETARIATDPRD', 'nama_skpd' => 'Sekretariat DPRD', 'is_active' => true],
            ['kode_skpd' => 'BKBP', 'nama_skpd' => 'Badan Kesatuan Bangsa dan Politik', 'is_active' => true]
        ];

        foreach ($skpds as $skpd) {
            MasterSKPD::firstOrCreate(
                ['kode_skpd' => $skpd['kode_skpd']],
                $skpd
            );
        }
    }
}
