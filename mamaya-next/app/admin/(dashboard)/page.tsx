import prisma from '@/lib/prisma';
import { getAdminSession } from '@/lib/auth';

export default async function AdminDashboardPage() {
  const session = await getAdminSession();

  // Basic stats
  const totalOrders = await prisma.order.count();
  const pendingOrders = await prisma.order.count({ where: { status: 'awaiting_payment' } });
  const processingOrders = await prisma.order.count({ where: { status: 'processing' } });
  const completedOrders = await prisma.order.count({ where: { status: 'completed' } });

  // Income sum (from completed orders)
  const completedStats = await prisma.order.aggregate({
    where: { status: 'completed' },
    _sum: { grand_total: true, subtotal: true }
  });
  
  const totalIncome = completedStats._sum.grand_total || completedStats._sum.subtotal || 0;

  return (
    <div>
      <h1 className="text-3xl font-bold text-ink mb-2">Halo, Admin 👋</h1>
      <p className="text-muted mb-8">Ringkasan operasional Mamaya hari ini.</p>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <StatCard title="Menunggu Pembayaran" value={pendingOrders} type="warning" />
        <StatCard title="Sedang Diproses" value={processingOrders} type="info" />
        <StatCard title="Selesai" value={completedOrders} type="success" />
        <StatCard title="Total Pendapatan" value={`Rp ${totalIncome.toLocaleString('id-ID')}`} type="primary" />
      </div>

      <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
         <h2 className="text-xl font-bold text-ink mb-4">Aktivitas Terakhir</h2>
         <p className="text-muted text-sm">Dashboard ini masih dalam pengembangan awal. Buka menu <b>Pesanan</b> untuk melihat daftar pesanan secara lengkap.</p>
      </div>
    </div>
  );
}

function StatCard({ title, value, type }: { title: string, value: string | number, type: 'warning' | 'info' | 'success' | 'primary' }) {
  const colorMap = {
    warning: 'bg-yellow-50 text-yellow-600 border-yellow-200',
    info: 'bg-indigo-50 text-indigo-600 border-indigo-200',
    success: 'bg-green-50 text-green-600 border-green-200',
    primary: 'bg-sky-50 text-sky-600 border-sky-200',
  };

  return (
    <div className={`border rounded-xl p-6 ${colorMap[type]}`}>
      <h3 className="text-sm font-medium opacity-80 mb-2">{title}</h3>
      <p className="text-3xl font-bold">{value}</p>
    </div>
  );
}
