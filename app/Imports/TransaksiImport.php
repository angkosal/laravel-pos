<?php

namespace App\Imports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransaksiImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Pastikan nama kolom dari CSV sesuai:
        // tanggal_transaksi | nama_pegawai | nama_produk | jumlah | harga_satuan

        $total_harga = isset($data['jumlah'], $data['harga_satuan'])
            ? $data['jumlah'] * $data['harga_satuan']
            : 0;

        Transaksi::create([
            'Tanggal_Transaksi' => $data['tanggal_transaksi'] ?? null,
            'Nama_Pegawai'      => $data['nama_pegawai'] ?? null, // ⬅️ PENTING (harus sama dgn database)
            'Nama_Produk'       => $data['nama_produk'] ?? null,
            'Total_Pesanan'     => $data['jumlah'] ?? 0,
            'Harga_Satuan'      => $data['harga_satuan'] ?? 0,
            'Total_Harga'       => $total_harga,
        ]);
    }
}
