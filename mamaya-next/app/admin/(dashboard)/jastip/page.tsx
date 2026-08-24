import prisma from '@/lib/prisma';
import Link from 'next/link';
import { toggleTripStatus, createTrip } from '@/app/actions/admin/jastip';
import { Plus, Power } from 'lucide-react';

export default async function AdminJastipPage() {
  const trips = await prisma.jastipTrip.findMany({
    orderBy: { createdAt: 'desc' },
    include: { _count: { select: { catalogs: true } } }
  });

  return (
    <div>
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h1 className="text-3xl font-bold text-ink mb-1">Kelola Jastip</h1>
          <p className="text-muted text-sm">Manajemen Jadwal Trip dan Katalog Barang.</p>
        </div>
        
        <details className="group relative">
          <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none">
            <Plus className="w-4 h-4" /> Buka PO Trip Baru
          </summary>
          <div className="absolute right-0 top-full mt-2 w-96 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10 h-[70vh] overflow-y-auto">
            <h3 className="font-bold text-ink mb-3">Buat Trip Jastip</h3>
            <form action={createTrip} className="space-y-3">
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Destinasi (Tujuan)</label>
                <input type="text" name="destination" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="Contoh: Jepang (Tokyo)" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Slug URL</label>
                <input type="text" name="slug" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="trip-jepang-2026" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Batas Bagasi (KG)</label>
                <input type="number" step="0.1" name="baggage_quota_kg" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" defaultValue="30" />
              </div>
              <div className="flex gap-2">
                 <div className="flex-1">
                   <label className="block text-xs font-medium text-ink mb-1">Tgl Berangkat</label>
                   <input type="date" name="departure_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                 </div>
                 <div className="flex-1">
                   <label className="block text-xs font-medium text-ink mb-1">Tgl Pulang</label>
                   <input type="date" name="return_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                 </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Tanggal Tutup PO</label>
                <input type="datetime-local" name="po_close_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Deskripsi & Syarat</label>
                <textarea name="description" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" rows={2}></textarea>
              </div>
              <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active mt-2">Simpan Trip</button>
            </form>
          </div>
        </details>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {trips.map(trip => (
          <div key={trip.id} className={`bg-surface border rounded-xl overflow-hidden shadow-sm flex flex-col ${trip.is_active ? 'border-hairline' : 'border-red-200 opacity-80'}`}>
            <div className="p-5 flex-1">
              <div className="flex justify-between items-start mb-2">
                 <h2 className="text-lg font-bold text-ink leading-tight">{trip.destination}</h2>
                 <span className={`text-xs px-2 py-1 rounded-md font-medium shrink-0 ml-2 ${trip.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                   {trip.is_active ? 'Aktif' : 'Nonaktif'}
                 </span>
              </div>
              <p className="text-sm text-muted mb-4 line-clamp-2">{trip.description}</p>
              
              <div className="space-y-1.5 text-xs text-muted mb-4 mt-4">
                <div className="flex justify-between"><span>Berangkat:</span> <span className="font-medium text-ink">{trip.departure_date ? new Date(trip.departure_date).toLocaleDateString('id-ID') : '-'}</span></div>
                <div className="flex justify-between"><span>Tutup PO:</span> <span className="font-medium text-ink">{trip.po_close_date ? new Date(trip.po_close_date).toLocaleString('id-ID') : '-'}</span></div>
                <div className="flex justify-between"><span>Kuota Bagasi:</span> <span className="font-medium text-ink">{trip.baggage_quota_kg} KG</span></div>
              </div>
              
              <div className="text-sm font-medium text-primary bg-sky-50 p-2 rounded-lg text-center mt-auto">
                {trip._count.catalogs} Barang Katalog
              </div>
            </div>
            
            <div className="border-t border-hairline p-3 flex items-center justify-between bg-canvas gap-3">
              <Link href={`/admin/jastip/${trip.id}`} className="flex-1 bg-surface-soft hover:bg-sky-50 text-ink hover:text-primary font-medium text-sm text-center py-2 rounded-lg transition-colors border border-hairline">
                Kelola Katalog
              </Link>
              <form action={toggleTripStatus.bind(null, trip.id, trip.is_active)}>
                <button type="submit" className={`p-2 rounded-lg border flex items-center justify-center transition-colors ${trip.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={trip.is_active ? 'Nonaktifkan Trip' : 'Aktifkan Trip'}>
                  <Power className="w-4 h-4" />
                </button>
              </form>
            </div>
          </div>
        ))}

        {trips.length === 0 && (
          <div className="col-span-full text-center p-12 bg-surface border border-dashed border-hairline rounded-xl text-muted">
            Belum ada trip jastip yang dibuat.
          </div>
        )}
      </div>
    </div>
  );
}
