import { NextResponse } from 'next/server';
import { getPrisma } from '@/lib/prisma';
import { getAdminSession } from '@/lib/auth';

export const dynamic = 'force-dynamic';

export async function GET() {
  const session = await getAdminSession();
  if (!session || session.role !== 'admin') {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  try {
    const orders = await (await getPrisma()).order.findMany({
      orderBy: { createdAt: 'desc' },
      include: {
        items: true,
      }
    });

    // Create CSV header
    const headers = [
      'ID Pesanan',
      'Tanggal Transaksi',
      'Status',
      'Tipe Pesanan',
      'Nama Pelanggan',
      'No. WhatsApp',
      'Metode Pengiriman',
      'Alamat',
      'ID Item',
      'Nama Produk',
      'Harga Satuan',
      'Jumlah',
      'Subtotal Item',
      'Catatan Khusus'
    ];

    let csvContent = headers.join(',') + '\n';

    // Helper to escape CSV strings
    const escapeCsv = (str: string | null | undefined) => {
      if (!str) return '""';
      const cleanStr = String(str).replace(/"/g, '""');
      return `"${cleanStr}"`;
    };

    // Flatten items into CSV rows
    orders.forEach(order => {
      const orderBaseData = [
        escapeCsv(order.id),
        escapeCsv(new Date(order.createdAt).toISOString()),
        escapeCsv(order.status),
        escapeCsv(order.order_type),
        escapeCsv(order.customer_name),
        escapeCsv(order.customer_phone),
        escapeCsv(order.shipping_method || '-'),
        escapeCsv(order.customer_address || '-')
      ];

      if (order.items.length === 0) {
        // Fallback if no items
        const row = [
          ...orderBaseData,
          escapeCsv('-'),
          escapeCsv('-'),
          escapeCsv('0'),
          escapeCsv('0'),
          escapeCsv('0'),
          escapeCsv(order.notes || '-')
        ];
        csvContent += row.join(',') + '\n';
      } else {
        order.items.forEach(item => {
          const row = [
            ...orderBaseData,
            escapeCsv(item.id),
            escapeCsv(item.product_name),
            escapeCsv(item.price.toString()),
            escapeCsv(item.quantity.toString()),
            escapeCsv((item.price * item.quantity).toString()),
            escapeCsv(order.notes || '-')
          ];
          csvContent += row.join(',') + '\n';
        });
      }
    });

    // Return as downloadable file
    const dateStr = new Date().toISOString().split('T')[0];
    return new NextResponse(csvContent, {
      headers: {
        'Content-Type': 'text/csv; charset=utf-8',
        'Content-Disposition': `attachment; filename="Laporan_Penjualan_Mamaya_${dateStr}.csv"`
      }
    });
  } catch (error) {
    console.error('Export error:', error);
    return NextResponse.json({ error: 'Gagal mengekspor data' }, { status: 500 });
  }
}
