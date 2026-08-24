import prisma from '@/lib/prisma';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { toggleCategoryStatus, createCategory } from '@/app/actions/admin/tickets';
import { ArrowLeft, Plus, Power } from 'lucide-react';

export default async function AdminEventDetailPage({ params }: { params: Promise<{ eventId: string }> }) {
  const { eventId } = await params;
  
  const event = await prisma.ticketEvent.findUnique({
    where: { id: eventId },
    include: { categories: { orderBy: { price: 'desc' } } }
  });

  if (!event) notFound();

  return (
    <div>
      <div className="mb-8">
        <Link href="/admin/tickets" className="text-muted hover:text-primary inline-flex items-center gap-2 mb-4 text-sm font-medium transition-colors">
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Daftar Event
        </Link>
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-ink mb-1">{event.name}</h1>
            <p className="text-muted text-sm">{event.venue}</p>
          </div>
          
          <details className="group relative">
            <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none">
              <Plus className="w-4 h-4" /> Tambah Kategori
            </summary>
            <div className="absolute right-0 top-full mt-2 w-80 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10">
              <h3 className="font-bold text-ink mb-3">Kategori Tiket Baru</h3>
              <form action={createCategory} className="space-y-3">
                <input type="hidden" name="event_id" value={event.id} />
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Nama Kategori (Contoh: VIP, CAT 1)</label>
                  <input type="text" name="name" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                   <label className="block text-xs font-medium text-ink mb-1">Harga (Rp)</label>
                   <input type="number" name="price" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                   <label className="block text-xs font-medium text-ink mb-1">Total Kuota</label>
                   <input type="number" name="quota" defaultValue="10" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active">Simpan Kategori</button>
              </form>
            </div>
          </details>
        </div>
      </div>

      <div className="bg-surface border border-hairline rounded-xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-surface-soft border-b border-hairline">
              <tr>
                <th className="px-6 py-4 font-semibold text-ink">Kategori</th>
                <th className="px-6 py-4 font-semibold text-ink">Harga</th>
                <th className="px-6 py-4 font-semibold text-ink">Terjual / Kuota</th>
                <th className="px-6 py-4 font-semibold text-ink">Status</th>
                <th className="px-6 py-4 font-semibold text-ink text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-hairline">
              {event.categories.map(cat => (
                <tr key={cat.id} className={`hover:bg-sky-50/50 transition-colors ${!cat.is_active && 'opacity-60 bg-gray-50'}`}>
                  <td className="px-6 py-4 font-bold text-ink">{cat.name}</td>
                  <td className="px-6 py-4 font-medium text-ink">Rp {cat.price.toLocaleString('id-ID')}</td>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-2">
                       <div className="w-24 bg-canvas rounded-full h-2 border border-hairline overflow-hidden">
                         <div className="bg-primary h-full" style={{ width: `${((cat.quota - cat.available_quota) / cat.quota) * 100}%` }}></div>
                       </div>
                       <span className="text-xs font-medium text-muted">{cat.quota - cat.available_quota} / {cat.quota}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                     <span className={`text-xs px-2 py-1 rounded-md font-medium ${cat.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                       {cat.is_active ? 'Aktif' : 'Nonaktif'}
                     </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="flex justify-end items-center gap-2">
                       <form action={toggleCategoryStatus.bind(null, cat.id, cat.is_active, event.id)}>
                         <button type="submit" className={`p-2 rounded border flex items-center justify-center transition-colors ${cat.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={cat.is_active ? 'Nonaktifkan' : 'Aktifkan'}>
                           <Power className="w-4 h-4" />
                         </button>
                       </form>
                    </div>
                  </td>
                </tr>
              ))}
              
              {event.categories.length === 0 && (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-muted">
                    Belum ada kategori tiket di event ini.
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
