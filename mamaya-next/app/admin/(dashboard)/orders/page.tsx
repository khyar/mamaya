import { getPrisma } from '@/lib/prisma';
import Link from 'next/link';

export default async function AdminOrdersPage() {
  const orders = await (await getPrisma()).order.findMany({
    orderBy: { createdAt: 'desc' },
    include: {
      items: true
    }
  });

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending': return <span className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Pending</span>;
      case 'awaiting_payment': return <span className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Menunggu Bayar</span>;
      case 'processing': return <span className="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-medium">Diproses</span>;
      case 'completed': return <span className="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Selesai</span>;
      case 'cancelled': return <span className="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Batal</span>;
      default: return <span className="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">{status}</span>;
    }
  };

  return (
    <div>
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h1 className="text-3xl font-bold text-ink mb-1">Kelola Pesanan</h1>
          <p className="text-muted text-sm">Daftar semua pesanan pelanggan dari Makanan, Tiket, dan Jastip.</p>
        </div>
      </div>

      <div className="bg-surface border border-hairline rounded-xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-surface-soft border-b border-hairline">
              <tr>
                <th className="px-6 py-4 font-semibold text-ink">ID Pesanan</th>
                <th className="px-6 py-4 font-semibold text-ink">Pelanggan</th>
                <th className="px-6 py-4 font-semibold text-ink">Tanggal</th>
                <th className="px-6 py-4 font-semibold text-ink">Tipe</th>
                <th className="px-6 py-4 font-semibold text-ink">Total</th>
                <th className="px-6 py-4 font-semibold text-ink">Status</th>
                <th className="px-6 py-4 font-semibold text-ink">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-hairline">
              {orders.map((order) => (
                <tr key={order.id} className="hover:bg-sky-50/50 transition-colors">
                  <td className="px-6 py-4 font-mono font-medium text-primary">{order.id}</td>
                  <td className="px-6 py-4">
                    <p className="font-medium text-ink">{order.customer_name}</p>
                    <p className="text-xs text-muted">{order.customer_phone}</p>
                  </td>
                  <td className="px-6 py-4 text-muted">
                    {new Date(order.createdAt).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  </td>
                  <td className="px-6 py-4 capitalize text-muted">{order.order_type}</td>
                  <td className="px-6 py-4 font-semibold text-ink">
                    Rp {(order.grand_total || order.subtotal).toLocaleString('id-ID')}
                  </td>
                  <td className="px-6 py-4">
                    {getStatusBadge(order.status)}
                  </td>
                  <td className="px-6 py-4">
                    <Link href={`/admin/orders/${order.id}`} className="text-primary hover:underline font-medium">
                      Detail
                    </Link>
                  </td>
                </tr>
              ))}
              
              {orders.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center text-muted">
                    Belum ada pesanan yang masuk.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
