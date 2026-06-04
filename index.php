<?php

class User
{
    public $nama;
    public $noHP;

    public function __construct($nama, $noHP)
    {

        if (empty($nama)) {
            exit("<H3>PERINGATAN!</H3><br>" . "Nama tidak boleh kosong<br>");
        }

        if (strlen($noHP) < 10) {
            exit("<H3>PERINGATAN!</H3><br>" . "Nomor HP minimal 10 digit<br>");
        }

        $this->nama = $nama;
        $this->noHP = $noHP;
    }

    public function getNama()
    {
        return ucwords($this->nama);
    }

    public function getStatus()
    {
        return "member";
    }
}

class Pelanggan extends User
{
    public $poin;

    public function __construct($nama, $noHP, $poin)
    {
        parent::__construct($nama, $noHP);
        $this->poin = $poin;
    }

    public function getStatus()
    {
        return "Member";
    }

    public function tambahPoin($point)
    {
        $poinNew = floor($point / 10000);
        $this->poin += $poinNew;
    }

    public function getPoin()
    {
        return $this->poin;
    }
}

class Layanan
{
    public $layanan;
    public $tarif;

    public function __construct($layanan)
    {
        $this->layanan = $layanan;
        if ($layanan == "GoRide Reguler") {
            $this->tarif = 2500;
        } elseif ($layanan == "GoRide Prioritas") {
            $this->tarif = 3000;
        } elseif ($layanan == "goCar") {
            $this->tarif = 4500;
        } elseif ($layanan == "goCar XL") {
            $this->tarif = 6000;
        } elseif ($layanan == "goFood") {
            $this->tarif = 2000;
        }
    }

    public function getTarif()
    {
        return $this->tarif;
    }

    public function getJenisLayanan()
    {
        return $this->layanan;
    }
}

class Voucher
{
    public $kodeVoucher;
    public $diskonPersen;

    public function __construct($kodeVoucher)
    {
        $this->kodeVoucher = $kodeVoucher;
        if ($kodeVoucher == "HEMAT10") {
            $this->diskonPersen = 0.10;
        } elseif ($kodeVoucher == "HEMAT20") {
            $this->diskonPersen = 0.20;
        } elseif ($kodeVoucher == "HEMAT30") {
            $this->diskonPersen = 0.30;
        } else {
            echo "Voucher tidak sesuai daftar voucher <br>";
            $this->diskonPersen = 0;
        }
    }

    public function hitungDiskon($subtotal)
    {
        return $subtotal * $this->diskonPersen;
    }
}

class Transaksi
{
    public $pelanggan;
    public $layanan;
    private $pembayaran;
    private $voucher;
    private $jarakTempuh;

    private static $totalTransaksi = 0;

    public function __construct($pelanggan, $layanan, $pembayaran, $voucher, $jarakTempuh)
    {
        if ($jarakTempuh <= 0) {
            exit("<H3>PERINGATAN!</H3><br>" . "Jarak harus lebih dari 0");
        }

        $this->pelanggan = $pelanggan;
        $this->layanan = $layanan;
        $this->pembayaran = $pembayaran;
        $this->voucher = $voucher;
        $this->jarakTempuh = $jarakTempuh;
        self::$totalTransaksi++;
    }

    public function hitungSubTotal()
    {
        return $this->jarakTempuh * $this->layanan->getTarif();
    }

    public function hitungDiskon()
    {
        $subTotal = $this->hitungSubTotal();
        return ($subTotal > 50000) ? $subTotal * 0.05 : 0;
    }

    public function hitungDiskonVoucher()
    {
        if ($this->voucher != null) {
            return $this->voucher->hitungDiskon(
                $this->hitungSubTotal()
            );
        }

        return 0;
    }

    public function hitungBiayaAdmin()
    {
        return $this->pembayaran->biayaAdmin();
    }

    public function hitungTotal()
    {

        return $this->hitungSubTotal() - $this->hitungDiskon() - $this->hitungDiskonVoucher() + $this->hitungBiayaAdmin();
    }

    public static function getTotalTransaksi()
    {
        return self::$totalTransaksi;
    }
}

abstract class Pembayaran
{
    abstract public function getMetode();
    abstract public function biayaAdmin();
}

class Ewallet extends Pembayaran
{

    public function getMetode()
    {
        return "E-Wallet";
    }

    public function biayaAdmin()
    {
        return 1000;
    }
}

class TransferBank extends Pembayaran
{
    public function getMetode()
    {
        return "Transfer Bank";
    }


    public function biayaAdmin()
    {
        return 2500;
    }
}

class Cash extends Pembayaran
{

    public function getMetode()
    {
        return "Cash";
    }

    public function biayaAdmin()
    {
        return 0;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        form {
            display: inline-block;
            text-align: left;
        }

        input,
        select {
            width: 300px;
            padding: 5px;
        }

        button {
            width: 100%;
            background-color: #00ffee;
            padding: 8px;
        }

        button:hover {
            background-color: #188495;
        }

        hr {
            margin: 20px 0;
        }

        h3 {
            margin-top: 20px;
        }

        .struk {
            width: 350px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: left;
            background-color: #f9f9f9;
        }

        .struk b {
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <h2>Sistem Ojek Online</h2>

    <form method="POST">

        <label>Nama</label><br>
        <input type="text" name="nama">
        <br><br>

        <label>No HP</label><br>
        <input type="text" name="nomorHP">
        <br><br>

        <label>Jarak Tempuh (KM)</label><br>
        <input type="number" name="jarak">
        <br><br>

        <label>Jenis Layanan</label><br>
        <select name="layanan">
            <option value="GoRide Reguler">GoRide Reguler</option>
            <option value="GoRide Prioritas">GoRide Prioritas</option>
            <option value="goCar">goCar</option>
            <option value="goCar XL">goCar XL</option>
            <option value="goFood">goFood</option>
        </select>
        <br><br>

        <label>Metode Pembayaran</label><br>
        <select name="pembayaran">
            <option value="ewallet">E-Wallet</option>
            <option value="transfer">Transfer Bank</option>
            <option value="cash">Cash</option>
        </select>
        <br><br>

        <label>Voucher</label><br>
        <select name="voucher">
            <option value="">Tidak pakai vocuher</option>
            <option value="HEMAT10">HEMAT10</option>
            <option value="HEMAT20">HEMAT20</option>
            <option value="HEMAT30">HEMAT30</option>
        </select>
        <br><br>

        <button type="submit" name="kirim">Kirim</button>

    </form>

    <hr>

    <?php

    if (isset($_POST['kirim'])) {

        $nama = $_POST['nama'];
        $noHP = $_POST['nomorHP'];
        $jarak = $_POST['jarak'];
        $layanan = $_POST['layanan'];
        $voucher = $_POST['voucher'];
        $metodeBayar = $_POST['pembayaran'];

        $poin = 0;

        $pelanggan = new Pelanggan($nama, $noHP, $poin);
        $layanan1 = new Layanan($layanan);
        $voucher1 = null;

        if (!empty($voucher)) {
            $voucher1 = new Voucher($voucher);
        }

        if ($metodeBayar == "ewallet") {
            $pembayaran = new Ewallet();
        } elseif ($metodeBayar == "transfer") {
            $pembayaran = new TransferBank();
        } else {
            $pembayaran = new Cash();
        }

        $transaksi = new Transaksi($pelanggan, $layanan1, $pembayaran, $voucher1, $jarak);

        $pelanggan->tambahPoin($transaksi->hitungTotal());

        echo "<div class='struk'>";
        echo "<h3>STRUK</h3>";
        echo "Nama: " . $pelanggan->getNama() . "<br>";
        echo "No. HP: " . $pelanggan->noHP . "<br>";
        echo "Status: " . $pelanggan->getStatus() . "<br>";
        echo "Layanan: " . $layanan1->getJenisLayanan() . "<br>";
        echo "Jarak :" . $jarak . " KM<br>";
        echo "Metode Pembayaran: " . $pembayaran->getMetode() . "<br>";
        echo "Subtotal: Rp " . number_format($transaksi->hitungSubTotal(), 0, ',', '.') . "<br>";
        echo "Diskon Member: Rp " . number_format($transaksi->hitungDiskon(), 0, ',', '.') . "<br>";
        echo "Diskon Voucher: Rp " . number_format($transaksi->hitungDiskonVoucher(), 0, ',', '.') . "<br>";
        echo "Biaya Admin: Rp " . number_format($transaksi->hitungBiayaAdmin(), 0, ',', '.') . "<br>";
        echo "<b>Total Bayar: Rp " . number_format($transaksi->hitungTotal(), 0, ',', '.') . "</b><br>";
        echo "Poin Sekarang: " . $pelanggan->getPoin() . "<br>";
        echo "Total Transaksi: " . Transaksi::getTotalTransaksi();
        echo "</div>";
    }
    ?>
    </div>
</body>

</html>