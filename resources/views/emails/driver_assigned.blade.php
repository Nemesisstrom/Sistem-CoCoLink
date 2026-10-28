<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penugasan Pengiriman Baru - CoCoLink</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">

        <!-- Header Email -->
        <div style="background-color: #047857; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 20px; font-weight: bold; font-family: Arial, sans-serif;">🌴 CoCoLink Logistics</h1>
            <p style="margin: 4px 0 0 0; font-size: 13px; opacity: 0.9;">Notifikasi Penugasan Pengiriman Limbah Kelapa</p>
        </div>

        <!-- Konten Utama -->
        <div style="padding: 24px; color: #374151; font-size: 14px; line-height: 1.6;">
            <p style="margin-top: 0;">Halo <strong>{{ $shipment->driver->name }}</strong>,</p>
            <p>Anda telah ditugaskan untuk melakukan pengangkutan dan pengiriman limbah kelapa dengan rincian jadwal sebagai berikut:</p>

            <!-- Card Rincian Tugas -->
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280; width: 40%;">ID Pengiriman</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #111827;">#SHIP-{{ $shipment->id }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">No. Order Utama</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #047857;">{{ $shipment->allocation->order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Tanggal Pengiriman</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #111827;">{{ \Carbon\Carbon::parse($shipment->delivery_date)->translatedFormat('l, d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Lokasi Penjemputan</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #111827;">{{ $shipment->allocation->koperasiAnak->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Tujuan Pengiriman</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #111827;">
                            {{ $shipment->allocation->order->pengepul->name }}<br>
                            <span style="font-size: 11px; color: #6b7280; font-weight: normal;">
                                {{ $shipment->allocation->order->delivery_address }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Komoditas & Volume</td>
                        <td style="padding: 6px 0; font-weight: bold; color: #047857;">
                            {{ $shipment->allocation->order->wasteType->name }} - {{ $shipment->allocation->allocated_quantity }} {{ $shipment->allocation->order->wasteType->unit }}
                        </td>
                    </tr>
                </table>
            </div>

            <p style="margin-bottom: 24px;">Silakan klik tombol di bawah ini untuk masuk ke Dashboard Driver dan memperbarui status perjalanan Anda:</p>

            <!-- Tombol Aksi -->
            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ route('driver.dashboard') }}" style="background-color: #047857; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">
                    🚚 Buka Dashboard Driver
                </a>
            </div>

            <p style="font-size: 12px; color: #9ca3af; margin-bottom: 0;">* Harap pastikan kendaraan dalam kondisi siap jalan dan periksa kembali volume muatan sebelum melakukan perjalanan.</p>
        </div>

        <!-- Footer Email -->
        <div style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px; text-align: center; font-size: 11px; color: #9ca3af;">
            <p style="margin: 0;">Email otomatis ini dikirim oleh Sistem Sinergi Limbah Kelapa CoCoLink.</p>
            <p style="margin: 4px 0 0 0;">&copy; {{ date('Y') }} CoCoLink Platform. All rights reserved.</p>
        </div>

    </div>
</body>
</html>
