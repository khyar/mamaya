'use client';

import { useState } from 'react';
import { updateOrderStatus } from '@/app/actions/admin';
import { Check, Loader2 } from 'lucide-react';

export default function StatusSelect({ orderId, currentStatus }: { orderId: string, currentStatus: string }) {
  const [status, setStatus] = useState(currentStatus);
  const [loading, setLoading] = useState(false);
  const [saved, setSaved] = useState(false);

  const statuses = [
    { value: 'pending', label: 'Pending (Belum Lengkap)' },
    { value: 'awaiting_payment', label: 'Menunggu Pembayaran' },
    { value: 'processing', label: 'Sedang Diproses' },
    { value: 'completed', label: 'Selesai' },
    { value: 'cancelled', label: 'Dibatalkan' }
  ];

  const handleChange = async (e: React.ChangeEvent<HTMLSelectElement>) => {
    const newStatus = e.target.value;
    setStatus(newStatus);
    setLoading(true);
    setSaved(false);

    try {
      await updateOrderStatus(orderId, newStatus);
      setSaved(true);
      setTimeout(() => setSaved(false), 3000);
    } catch (err) {
      alert("Gagal mengupdate status.");
      setStatus(currentStatus);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex items-center gap-3">
      <select
        value={status}
        onChange={handleChange}
        disabled={loading}
        className="border border-hairline rounded-lg p-2 bg-canvas text-ink focus:outline-none focus:border-primary text-sm font-medium disabled:opacity-50"
      >
        {statuses.map(s => (
          <option key={s.value} value={s.value}>{s.label}</option>
        ))}
      </select>

      {loading && <Loader2 className="w-4 h-4 text-primary animate-spin" />}
      {saved && <Check className="w-4 h-4 text-green-500" />}
    </div>
  );
}
