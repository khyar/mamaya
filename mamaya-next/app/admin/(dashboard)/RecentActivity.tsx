import Link from 'next/link';

interface Order {
  id: string;
  customer_name: string;
  order_type: string;
  status: string;
  createdAt: Date;
  subtotal: number;
  grand_total: number | null;
}

export default function RecentActivity({ recentOrders }: { recentOrders: Order[] }) {
  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending': return <span className="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Pending</span>;
      case 'awaiting_payment': return <span className="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Menunggu Bayar</span>;
      case 'processing': return <span className="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Diproses</span>;
      case 'completed': return <span className="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Selesai</span>;
      case 'cancelled': return <span className="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Batal</span>;
      default: return <span className="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">{status}</span>;
    }
  };

  const getOrderTypeColor = (type: string) => {
    switch (type) {
      case 'food': return 'text-red-600 bg-red-50 border-red-200';
      case 'ticket': return 'text-blue-600 bg-blue-50 border-blue-200';
      case 'jastip': return 'text-green-600 bg-green-50 border-green-200';
      case 'mixed': return 'text-purple-600 bg-purple-50 border-purple-200';
      default: return 'text-gray-600 bg-gray-50 border-gray-200';
    }
  };

  if (recentOrders.length === 0) {
    return (
      <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
        <h2 className="text-xl font-bold text-ink mb-4">Aktivitas Terakhir</h2>
        <div className="text-center py-8 text-muted">Belum ada pesanan yang masuk.</div>
      </div>
    );
  }

  return (
    <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-bold text-ink">Aktivitas Terakhir</h2>
        <Link href="/admin/orders" className="text-sm font-bold text-primary hover:underline">
          Lihat Semua
        </Link>
      </div>

      <div className="space-y-4">
        {recentOrders.map(order => (
          <div key={order.id} className="flex items-center justify-between border-b border-hairline pb-4 last:border-0 last:pb-0">
            <div className="flex items-center gap-4">
              <div className={`w-12 h-12 rounded-lg border flex items-center justify-center font-bold text-xs uppercase ${getOrderTypeColor(order.order_type)}`}>
                {order.order_type === 'mixed' ? 'MIX' : order.order_type.substring(0, 3)}
              </div>
              <div>
                <p className="font-bold text-ink text-sm">
                  {order.customer_name} <span className="text-muted font-normal">membeli</span> {order.order_type}
                </p>
                <div className="flex items-center gap-2 mt-1">
                  <p className="text-xs text-muted">
                    {new Date(order.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB
                  </p>
                  <span>•</span>
                  {getStatusBadge(order.status)}
                </div>
              </div>
            </div>
            <div className="text-right hidden sm:block">
              <p className="font-bold text-ink text-sm">Rp {(order.grand_total || order.subtotal).toLocaleString('id-ID')}</p>
              <Link href={`/admin/orders/${order.id}`} className="text-xs text-primary font-semibold hover:underline">Detail</Link>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
