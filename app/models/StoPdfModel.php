<?php
class StoPdfModel {

    private $db;

    public function __construct() {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function insert($data) {

        $sql = "REPLACE INTO sto_pdf 
        (nomor_sto, tanggal_dokumen, tanggal_cetak, jenis_kegiatan, asal_barang, tujuan_barang, moda, referensi, pemilik_barang, nama_barang, 
        qty, satuan, tarif, biaya, total, halaman, pdf_file)
        VALUES 
        (:nomor_sto, :tanggal_dokumen, :tanggal_cetak, :jenis_kegiatan, :asal_barang, :tujuan_barang, :moda, :referensi, :pemilik_barang, :nama_barang, 
        :qty, :satuan, :tarif, :biaya, :total, :halaman, :pdf_file)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':nomor_sto'        => $data['nomor_sto'],
            ':tanggal_dokumen'  => $data['tanggal_dokumen'],
            ':tanggal_cetak'    => $data['tanggal_cetak'],
            ':jenis_kegiatan'   => $data['jenis_kegiatan'],
            ':asal_barang'      => $data['asal_barang'],
            ':tujuan_barang'    => $data['tujuan_barang'],
            ':moda'             => $data['moda'],
            ':referensi'        => $data['referensi'],
            ':pemilik_barang'   => $data['pemilik_barang'],
            ':nama_barang'      => $data['nama_barang'],
            ':qty'              => $data['qty'],
            ':satuan'           => $data['satuan'],
            ':tarif'            => $data['tarif'],
            ':biaya'            => $data['biaya'],
            ':total'            => $data['total'],
            ':halaman'          => $data['halaman'],
            ':pdf_file'         => $data['pdf_file']
        ]);
    }

    public function getAll()
{
    $sql = "SELECT * FROM sto_pdf ORDER BY tanggal_dokumen DESC";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


}


