import prisma from '@/lib/prisma';
import Link from 'next/link';
import { toggleBatchStatus, createBatch } from '@/app/actions/admin/food';
import { Plus, Power } from 'lucide-react';

export default async function AdminFoodPage() {
  const batches = await prisma.batch.findMany({
    orderBy: { createdAt: 'desc' },
    include: { _count: { select: { products: true } } }
  });

  return (
    <div>
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h1 className="text-3xl font-bold text-ink mb-1">Kelola Makanan (PO)</h1>
          <p className="text-muted text-sm">Manajemen Jadwal Batch (Pre-Order) dan Menu Makanan.</p>
        </div>
        
        <div className="flex items-center gap-3">
          <Link href="/admin/food/master" className="btn-secondary flex items-center gap-2 border border-hairline px-4 py-2 rounded-lg text-sm font-medium hover:bg-surface-soft transition-colors">
            Kelola Master Menu
          </Link>
          
          <details className="group relative">
            <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-active">
              <Plus className="w-4 h-4" /> Buat Batch Baru
            </summary>
            
            <div className="absolute right-0 top-full mt-2 w-80 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10">
              <h3 className="font-bold text-ink mb-3">Buat Batch PO</h3>
              <form action={createBatch} className="space-y-3">
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Nama Batch</label>
                  <input type="text" name="name" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="Contoh: Batch Agustus" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Deskripsi</label>
                  <input type="text" name="description" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Tanggal Buka PO</label>
                  <input type="datetime-local" name="open_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Tanggal Tutup PO</label>
                  <input type="datetime-local" name="close_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Tanggal Pengiriman</label>
                  <input type="datetime-local" name="delivery_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active">
                  Simpan Batch
                </button>
              </form>
            </div>
          </details>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {batches.map(batch => (
          <div key={batch.id} className={`bg-surface border rounded-xl overflow-hidden shadow-sm flex flex-col ${batch.is_active ? 'border-hairline' : 'border-red-200 opacity-80'}`}>
            <div className="p-5 flex-1">
              <div className="flex justify-between items-start mb-2">
                 <h2 className="text-lg font-bold text-ink">{batch.name}</h2>
                 <span className={`text-xs px-2 py-1 rounded-md font-medium ${batch.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                   {batch.is_active ? 'Aktif' : 'Nonaktif'}
                 </span>
              </div>
              <p className="text-sm text-muted mb-4 line-clamp-2">{batch.description}</p>
              
              <div className="space-y-1.5 text-xs text-muted mb-4">
                <div className="flex justify-between">
                  <span>Buka:</span> 
                  <span className="font-medium text-ink">{new Date(batch.open_date).toLocaleDateString('id-ID')}</span>
                </div>
                <div className="flex justify-between">
                  <span>Tutup:</span> 
                  <span className="font-medium text-ink">{new Date(batch.close_date).toLocaleDateString('id-ID')}</span>
                </div>
                <div className="flex justify-between">
                  <span>Kirim:</span> 
                  <span className="font-medium text-ink">{new Date(batch.delivery_date).toLocaleDateString('id-ID')}</span>
                </div>
              </div>
              
              <div className="text-sm font-medium text-primary bg-sky-50 p-2 rounded-lg text-center">
                {batch._count.products} Menu Tersedia
              </div>
            </div>
            
            <div className="border-t border-hairline p-3 flex items-center justify-between bg-canvas gap-3">
              <Link href={`/admin/food/${batch.id}`} className="flex-1 bg-surface-soft hover:bg-sky-50 text-ink hover:text-primary font-medium text-sm text-center py-2 rounded-lg transition-colors border border-hairline">
                Kelola Menu
              </Link>
              <form action={toggleBatchStatus.bind(null, batch.id, batch.is_active)}>
                <button type="submit" className={`p-2 rounded-lg border flex items-center justify-center transition-colors ${batch.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={batch.is_active ? 'Nonaktifkan Batch' : 'Aktifkan Batch'}>
                  <Power className="w-4 h-4" />
                </button>
              </form>
            </div>
          </div>
        ))}

        {batches.length === 0 && (
          <div className="col-span-full text-center p-12 bg-surface border border-dashed border-hairline rounded-xl text-muted">
            Belum ada batch PO yang dibuat.
          </div>
        )}
      </div>
    </div>
  );
}
