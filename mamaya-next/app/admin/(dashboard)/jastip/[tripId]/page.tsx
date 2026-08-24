import { getPrisma } from '@/lib/prisma';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { toggleCatalogStatus, createCatalog } from '@/app/actions/admin/jastip';
import { ArrowLeft, Plus, Power, Link as LinkIcon } from 'lucide-react';

export default async function AdminTripDetailPage({ params }: { params: Promise<{ tripId: string }> }) {
  const { tripId } = await params;
  
  const trip = await (await getPrisma()).jastipTrip.findUnique({
    where: { id: tripId },
    include: { catalogs: { orderBy: { createdAt: 'desc' } } }
  });

  if (!trip) notFound();

  return (
    <div>
      <div className="mb-8">
        <Link href="/admin/jastip" className="text-muted hover:text-primary inline-flex items-center gap-2 mb-4 text-sm font-medium transition-colors">
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Daftar Trip
        </Link>
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-ink mb-1">Katalog {trip.destination}</h1>
            <p className="text-muted text-sm">Bagasi Maks: {trip.baggage_quota_kg} KG</p>
          </div>
          
          <details className="group relative">
            <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none">
              <Plus className="w-4 h-4" /> Tambah Barang
            </summary>
            <div className="absolute right-0 top-full mt-2 w-80 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10">
              <h3 className="font-bold text-ink mb-3">Barang Baru</h3>
              <form action={createCatalog} className="space-y-3">
                <input type="hidden" name="trip_id" value={trip.id} />
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Nama Barang</label>
                  <input type="text" name="name" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                   <label className="block text-xs font-medium text-ink mb-1">Estimasi Harga (Rp)</label>
                   <input type="number" name="estimated_price" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                   <label className="block text-xs font-medium text-ink mb-1">Link Referensi (Opsional)</label>
                   <input type="url" name="reference_url" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="https://" />
                </div>
                <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active mt-2">Simpan Barang</button>
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
                <th className="px-6 py-4 font-semibold text-ink">Barang</th>
                <th className="px-6 py-4 font-semibold text-ink">Estimasi Harga</th>
                <th className="px-6 py-4 font-semibold text-ink">Referensi</th>
                <th className="px-6 py-4 font-semibold text-ink">Status</th>
                <th className="px-6 py-4 font-semibold text-ink text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-hairline">
              {trip.catalogs.map(catalog => (
                <tr key={catalog.id} className={`hover:bg-sky-50/50 transition-colors ${!catalog.is_active && 'opacity-60 bg-gray-50'}`}>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                       <img src={catalog.image || '/images/portal/jastip.jpg'} alt={catalog.name} className="w-10 h-10 rounded-md object-cover" />
                       <span className="font-bold text-ink">{catalog.name}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4 font-medium text-ink">Rp {(catalog.estimated_price || 0).toLocaleString('id-ID')}</td>
                  <td className="px-6 py-4">
                     {catalog.reference_url ? (
                       <a href={catalog.reference_url} target="_blank" rel="noreferrer" className="text-primary hover:underline flex items-center gap-1">
                         <LinkIcon className="w-3 h-3" /> Buka Link
                       </a>
                     ) : (
                       <span className="text-muted text-xs">-</span>
                     )}
                  </td>
                  <td className="px-6 py-4">
                     <span className={`text-xs px-2 py-1 rounded-md font-medium ${catalog.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                       {catalog.is_active ? 'Aktif' : 'Nonaktif'}
                     </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="flex justify-end items-center gap-2">
                       <form action={toggleCatalogStatus.bind(null, catalog.id, catalog.is_active, trip.id)}>
                         <button type="submit" className={`p-2 rounded border flex items-center justify-center transition-colors ${catalog.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={catalog.is_active ? 'Nonaktifkan' : 'Aktifkan'}>
                           <Power className="w-4 h-4" />
                         </button>
                       </form>
                    </div>
                  </td>
                </tr>
              ))}
              
              {trip.catalogs.length === 0 && (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-muted">
                    Belum ada barang di katalog trip ini.
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
