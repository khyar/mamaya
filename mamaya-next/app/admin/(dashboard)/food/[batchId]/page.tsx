import prisma from '@/lib/prisma';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { toggleProductStatus } from '@/app/actions/admin/food';
import { ArrowLeft, Power } from 'lucide-react';
import AddProductForm from './AddProductForm';

export default async function AdminBatchDetailPage({ params }: { params: Promise<{ batchId: string }> }) {
  const { batchId } = await params;
  
  const batch = await prisma.batch.findUnique({
    where: { id: batchId },
    include: { products: { orderBy: { createdAt: 'desc' } } }
  });

  const masterProducts = await prisma.masterProduct.findMany({
    where: { is_active: true },
    orderBy: { name: 'asc' }
  });

  if (!batch) notFound();

  return (
    <div>
      <div className="mb-8">
        <Link href="/admin/food" className="text-muted hover:text-primary inline-flex items-center gap-2 mb-4 text-sm font-medium transition-colors">
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Daftar Batch
        </Link>
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-ink mb-1">{batch.name}</h1>
            <p className="text-muted text-sm">{batch.description || 'Tidak ada deskripsi'}</p>
          </div>
          
          <AddProductForm batchId={batch.id} masterProducts={masterProducts} />
        </div>
      </div>

      <div className="bg-surface border border-hairline rounded-xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-surface-soft border-b border-hairline">
              <tr>
                <th className="px-6 py-4 font-semibold text-ink">Nama Menu</th>
                <th className="px-6 py-4 font-semibold text-ink">Harga</th>
                <th className="px-6 py-4 font-semibold text-ink">Stok</th>
                <th className="px-6 py-4 font-semibold text-ink">Status</th>
                <th className="px-6 py-4 font-semibold text-ink text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-hairline">
              {batch.products.map(product => (
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
                  <td className="px-6 py-4 font-medium text-ink">Rp {product.price.toLocaleString('id-ID')}</td>
                  <td className="px-6 py-4 font-medium text-ink">{product.stock}</td>
                  <td className="px-6 py-4">
                     <span className={`text-xs px-2 py-1 rounded-md font-medium ${product.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                       {product.is_active ? 'Aktif' : 'Nonaktif'}
                     </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="flex justify-end items-center gap-2">
                       <form action={toggleProductStatus.bind(null, product.id, product.is_active, batch.id)}>
                         <button type="submit" className={`p-2 rounded border flex items-center justify-center transition-colors ${product.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={product.is_active ? 'Nonaktifkan' : 'Aktifkan'}>
                           <Power className="w-4 h-4" />
                         </button>
                       </form>
                    </div>
                  </td>
                </tr>
              ))}
              
              {batch.products.length === 0 && (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-muted">
                    Belum ada menu di batch ini.
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
