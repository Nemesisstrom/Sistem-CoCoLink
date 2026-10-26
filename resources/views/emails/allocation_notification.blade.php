<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background-color: #047857; color: white; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; }
        .content { padding: 20px; }
        .badge { background-color: #ecfdf5; color: #047857; padding: 8px 12px; font-weight: bold; border-radius: 4px; display: inline-block; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pemberitahuan Alokasi Pasokan Limbah</h2>
        </div>
        <div class="content">
            <p>Yth. Pengurus <strong>{{ $allocation->koperasiAnak->name }}</strong>,</p>
            <p>Koperasi Induk CoCoLink telah mengalokasikan target pesanan pasokan baru untuk unit Anda dengan rincian sebagai berikut:</p>

            <ul>
                <li><strong>No. Order Induk:</strong> {{ $allocation->order->order_number }}</li>
                <li><strong>Jenis Limbah:</strong> {{ $allocation->order->wasteType->name }}</li>
                <li><strong>Target Kuota:</strong> {{ $allocation->allocated_quantity }} {{ $allocation->order->wasteType->unit }}</li>
                <li><strong>Pembeli Industri (PT Tujuan):</strong> {{ $allocation->order->pengepul->name }}</li>
                <li><strong>Alamat Pengiriman Target:</strong> {{ $allocation->order->delivery_address }}</li>
            </ul>

            <p>Mohon segera masuk ke Dashboard CoCoLink Anda untuk memperbarui status kesiapan pasokan di gudang.</p>

            <p class="badge">Status Alokasi: Assigned (Menunggu Penyiapan Barang)</p>
        </div>
        <div class="footer">
            <p>Email ini dikirimkan secara otomatis oleh Sistem Central Ops CoCoLink.</p>
        </div>
    </div>
</body>
</html>
