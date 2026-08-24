'use client';

import { 
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
  PieChart, Pie, Cell, Legend
} from 'recharts';

interface WeeklyData {
  date: string;
  total: number;
}

interface CompositionData {
  name: string;
  value: number;
}

interface DashboardChartsProps {
  weeklyData: WeeklyData[];
  compositionData: CompositionData[];
}

const COLORS = ['#ef4444', '#3b82f6', '#10b981', '#f59e0b']; // Red, Blue, Green, Yellow

export default function DashboardCharts({ weeklyData, compositionData }: DashboardChartsProps) {
  
  const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  };

  const CustomTooltip = ({ active, payload, label }: any) => {
    if (active && payload && payload.length) {
      return (
        <div className="bg-canvas border border-hairline p-3 rounded-lg shadow-lg">
          <p className="font-bold text-ink mb-1">{label}</p>
          <p className="text-primary font-medium">
            Pendapatan: {formatRupiah(payload[0].value)}
          </p>
        </div>
      );
    }
    return null;
  };

  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
      {/* Weekly Revenue Bar Chart */}
      <div className="lg:col-span-2 bg-surface border border-hairline rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-bold text-ink mb-6">Pendapatan 7 Hari Terakhir</h2>
        <div className="h-[300px] w-full">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={weeklyData} margin={{ top: 5, right: 20, left: 20, bottom: 5 }}>
              <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e5e7eb" />
              <XAxis dataKey="date" tickLine={false} axisLine={false} tick={{ fontSize: 12, fill: '#6b7280' }} dy={10} />
              <YAxis 
                tickFormatter={(val) => `Rp ${val / 1000}K`} 
                tickLine={false} 
                axisLine={false} 
                tick={{ fontSize: 12, fill: '#6b7280' }}
                dx={-10}
              />
              <Tooltip content={<CustomTooltip />} cursor={{ fill: '#f3f4f6' }} />
              <Bar dataKey="total" fill="#ef4444" radius={[4, 4, 0, 0]} maxBarSize={50} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Order Composition Pie Chart */}
      <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm flex flex-col">
        <h2 className="text-lg font-bold text-ink mb-6">Komposisi Penjualan</h2>
        <div className="flex-1 min-h-[300px]">
          {compositionData.length === 0 || compositionData.every(d => d.value === 0) ? (
            <div className="h-full flex items-center justify-center text-muted text-sm">
              Belum ada data penjualan.
            </div>
          ) : (
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={compositionData}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={80}
                  paddingAngle={5}
                  dataKey="value"
                  stroke="none"
                >
                  {compositionData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip formatter={(value: number) => formatRupiah(value)} />
                <Legend verticalAlign="bottom" height={36} iconType="circle" />
              </PieChart>
            </ResponsiveContainer>
          )}
        </div>
      </div>
    </div>
  );
}
