<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 5px;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background-color: #fff;
            padding: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .store-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .store-info {
            font-size: 8px;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .meta-info table {
            width: 100%;
            font-size: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-top: 5px;
        }
        .totals td {
            padding: 1px 0;
            font-size: 9px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">TOKO TANI</div>
        <div class="store-info">Sistem Kasir Pertanian Digital</div>
        <div class="store-info">Telp: 081234567890</div>
    </div>

    <div class="separator"></div>

    <div class="meta-info">
        <table>
            <tr>
                <td>No: {{ $sale->invoice_number }}</td>
                <td class="text-right">Kasir: {{ $sale->creator->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td>Tgl: {{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                <td class="text-right">Pelanggan: {{ $sale->customer->name ?? 'Walk-in (Umum)' }}</td>
            </tr>
        </table>
    </div>

    <div class="separator"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Barang</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">{{ $item->quantity }} {{ $item->product->sellUnit->symbol }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table class="totals">
        <tr>
            <td>TOTAL BELANJA</td>
            <td class="text-right font-bold">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>METODE BAYAR</td>
            <td class="text-right uppercase">{{ $sale->payment_method }}</td>
        </tr>
        <tr>
            <td>JUMLAH DIBAYAR</td>
            <td class="text-right">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
        </tr>
        @if($sale->due_amount > 0)
        <tr>
            <td style="color:red;">SISA PIUTANG (HUTANG)</td>
            <td class="text-right" style="color:red; font-weight:bold;">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p>Terima Kasih Atas Kunjungan Anda</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>
</body>
</html>
