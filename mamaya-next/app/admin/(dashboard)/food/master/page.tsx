import { getPrisma } from '@/lib/prisma';
import Link from 'next/link';
import { createMasterProduct, toggleMasterProductStatus } from '@/app/actions/admin/master_food';
import { ArrowLeft, Plus, Power } from 'lucide-react';

export default async function AdminMasterFoodPage() {
  const masterProducts = await (await getPrisma()).masterProduct.findMany({
    orderBy: { createdAt: 'desc' }
  });

  return (
    <div>
      <div className="mb-8">
        <Link href="/admin/food" className="text-muted hover:text-primary inline-flex items-center gap-2 mb-4 text-sm font-medium transition-colors">
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Jadwal PO
        </Link>
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-ink mb-1">Katalog Master Makanan</h1>
            <p className="text-muted text-sm">Daftar semua resep/menu utama yang bisa disalin ke Batch PO.</p>
          </div>
          
          <details className="group relative">
            <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none">
              <Plus className="w-4 h-4" /> Tambah Master Menu
            </summary>
            <div className="absolute right-0 top-full mt-2 w-80 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10">
              <h3 className="font-bold text-ink mb-3">Master Menu Baru</h3>
              <form action={createMasterProduct} className="space-y-3">
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Nama Menu</label>
                  <input type="text" name="name" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Deskripsi Resep</label>
                  <textarea name="description" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" rows={2}></textarea>
                </div>
                <div>
                  <label className="block text-xs font-medium text-ink mb-1">Harga Dasar (Rp)</label>
                  <input type="number" name="base_price" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                </div>
                <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active mt-2">Simpan Master</button>
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
                <th className="px-6 py-4 font-semibold text-ink">Menu Master</th>
                <th className="px-6 py-4 font-semibold text-ink">Harga Dasar</th>
                <th className="px-6 py-4 font-semibold text-ink">Status</th>
                <th className="px-6 py-4 font-semibold text-ink text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-hairline">
              {masterProducts.map(product => (
                <tr key={product.id} className={`hover:bg-sky-50/50 transition-colors ${!product.is_active && 'opacity-60 bg-gray-50'}`}>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                       <img src={product.image || '/images/placeholder-food.jpg'} alt={product.name} className="w-10 h-10 rounded-md object-cover" />
                       <div>
                         <p className="font-bold text-ink">{product.name}</p>
                         <p className="text-xs text-muted max-w-[200px] truncate">{product.description}</p>
                       </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 font-medium text-ink">Rp {product.base_price.toLocaleString('id-ID')}</td>
                  <td className="px-6 py-4">
                     <span className={`text-xs px-2 py-1 rounded-md font-medium ${product.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                       {product.is_active ? 'Tersedia' : 'Diarsipkan'}
                     </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="flex justify-end items-center gap-2">
                       <form action={toggleMasterProductStatus.bind(null, product.id, product.is_active)}>
                         <button type="submit" className={`p-2 rounded border flex items-center justify-center transition-colors ${product.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={product.is_active ? 'Nonaktifkan' : 'Aktifkan'}>
                           <Power className="w-4 h-4" />
                         </button>
                       </form>
                    </div>
                  </td>
                </tr>
              ))}
              
              {masterProducts.length === 0 && (
                <tr>
                  <td colSpan={4} className="px-6 py-12 text-center text-muted">
                    Belum ada Master Menu yang dibuat.
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
