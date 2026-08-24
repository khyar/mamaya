import { getPrisma } from '@/lib/prisma';
import { getAdminSession } from '@/lib/auth';
import DashboardCharts from './DashboardCharts';
import RecentActivity from './RecentActivity';

export default async function AdminDashboardPage() {
  const session = await getAdminSession();

  const prisma = await getPrisma();

  // Basic stats
  const pendingOrders = await prisma.order.count({ where: { status: 'awaiting_payment' } });
  const processingOrders = await prisma.order.count({ where: { status: 'processing' } });
  const completedOrders = await prisma.order.count({ where: { status: 'completed' } });

  const completedStats = await prisma.order.aggregate({
    where: { status: 'completed' },
    _sum: { grand_total: true, subtotal: true }
  });
  
  const totalIncome = completedStats._sum.grand_total || completedStats._sum.subtotal || 0;

  // Recent 5 Orders
  const recentOrders = await prisma.order.findMany({
    orderBy: { createdAt: 'desc' },
    take: 5
  });

  // Calculate Weekly Data (Last 7 Days)
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 6);
  sevenDaysAgo.setHours(0, 0, 0, 0);

  const recentCompletedOrders = await prisma.order.findMany({
    where: {
      status: 'completed',
      createdAt: { gte: sevenDaysAgo }
    }
  });

  const weeklyData = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const dateStr = d.toLocaleDateString('id-ID', { weekday: 'short' });
    
    const dayTotal = recentCompletedOrders
      .filter(o => new Date(o.createdAt).toDateString() === d.toDateString())
      .reduce((sum, o) => sum + (o.grand_total || o.subtotal || 0), 0);
      
    weeklyData.push({ date: dateStr, total: dayTotal });
  }

  // Calculate Order Composition Data (All Time Completed)
  const allCompletedOrders = await prisma.order.findMany({
    where: { status: 'completed' }
  });

  const foodTotal = allCompletedOrders.filter(o => o.order_type === 'food').reduce((sum, o) => sum + (o.grand_total || o.subtotal || 0), 0);
  const ticketTotal = allCompletedOrders.filter(o => o.order_type === 'ticket').reduce((sum, o) => sum + (o.grand_total || o.subtotal || 0), 0);
  const jastipTotal = allCompletedOrders.filter(o => o.order_type === 'jastip').reduce((sum, o) => sum + (o.grand_total || o.subtotal || 0), 0);
  const mixedTotal = allCompletedOrders.filter(o => o.order_type === 'mixed').reduce((sum, o) => sum + (o.grand_total || o.subtotal || 0), 0);

  const compositionData = [
    { name: 'Food', value: foodTotal },
    { name: 'Ticket', value: ticketTotal },
    { name: 'Jastip', value: jastipTotal },
    { name: 'Mixed', value: mixedTotal }
  ].filter(item => item.value > 0);

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

      {/* Advanced Dashboard Components */}
      <DashboardCharts weeklyData={weeklyData} compositionData={compositionData} />
      
      <RecentActivity recentOrders={recentOrders} />
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
