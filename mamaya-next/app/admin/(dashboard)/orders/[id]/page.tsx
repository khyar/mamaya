import { getPrisma } from '@/lib/prisma';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import StatusSelect from './StatusSelect';
import { ArrowLeft, User, MapPin, Receipt, Phone, FileText } from 'lucide-react';

export default async function OrderDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  
  const order = await (await getPrisma()).order.findUnique({
    where: { id },
    include: { items: true }
  });

  if (!order) {
    notFound();
  }

  return (
    <div>
      <div className="mb-8">
        <Link href="/admin/orders" className="text-muted hover:text-primary inline-flex items-center gap-2 mb-4 text-sm font-medium transition-colors">
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Daftar Pesanan
        </Link>
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-ink mb-1">Pesanan {order.id}</h1>
            <p className="text-muted text-sm">
              Dibuat pada {new Date(order.createdAt).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
            </p>
          </div>
          <div className="bg-surface border border-hairline p-2 rounded-xl">
             <StatusSelect orderId={order.id} currentStatus={order.status} />
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
             <h2 className="text-lg font-bold text-ink mb-4 flex items-center gap-2 border-b border-hairline pb-3">
               <Receipt className="w-5 h-5 text-muted" /> Rincian Barang
             </h2>
             
             <div className="space-y-4">
               {order.items.map(item => (
                 <div key={item.id} className="flex justify-between items-center bg-canvas p-4 rounded-lg border border-hairline">
                   <div>
                     <p className="font-semibold text-ink">{item.product_name}</p>
                     <p className="text-sm text-muted">{item.quantity} x Rp {item.price.toLocaleString('id-ID')}</p>
                   </div>
                   <div className="font-bold text-ink">
                     Rp {(item.quantity * item.price).toLocaleString('id-ID')}
                   </div>
                 </div>
               ))}
             </div>

             <div className="mt-6 flex justify-end">
                <div className="text-right">
                  <p className="text-sm text-muted mb-1">Total Penagihan</p>
                  <p className="text-2xl font-bold text-primary">Rp {(order.grand_total || order.subtotal).toLocaleString('id-ID')}</p>
                </div>
             </div>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
             <h2 className="text-lg font-bold text-ink mb-4 flex items-center gap-2 border-b border-hairline pb-3">
               <User className="w-5 h-5 text-muted" /> Info Pelanggan
             </h2>
             <div className="space-y-3 text-sm">
               <div>
                 <p className="text-muted mb-0.5">Nama Lengkap</p>
                 <p className="font-medium text-ink">{order.customer_name}</p>
               </div>
               <div>
                 <p className="text-muted mb-0.5 flex items-center gap-1"><Phone className="w-3 h-3" /> Nomor Telepon / WA</p>
                 <p className="font-medium text-ink">
                   <a href={`https://wa.me/${order.customer_phone}`} target="_blank" rel="noreferrer" className="text-primary hover:underline">
                     {order.customer_phone}
                   </a>
                 </p>
               </div>
               {order.customer_ktp && (
                 <div>
                   <p className="text-muted mb-0.5">NIK (KTP)</p>
                   <p className="font-medium text-ink">{order.customer_ktp}</p>
                 </div>
               )}
             </div>
          </div>

          <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
             <h2 className="text-lg font-bold text-ink mb-4 flex items-center gap-2 border-b border-hairline pb-3">
               <MapPin className="w-5 h-5 text-muted" /> Pengiriman
             </h2>
             <div className="space-y-3 text-sm">
               <div>
                 <p className="text-muted mb-0.5">Metode</p>
                 <p className="font-medium text-ink capitalize">{order.shipping_method || 'Tidak Ada'}</p>
               </div>
               <div>
                 <p className="text-muted mb-0.5">Alamat Lengkap</p>
                 <p className="font-medium text-ink">{order.customer_address || '-'}</p>
               </div>
             </div>
          </div>

          {order.notes && (
             <div className="bg-surface border border-hairline rounded-xl p-6 shadow-sm">
               <h2 className="text-lg font-bold text-ink mb-4 flex items-center gap-2 border-b border-hairline pb-3">
                 <FileText className="w-5 h-5 text-muted" /> Catatan Pesanan
               </h2>
               <div className="text-sm bg-yellow-50 p-3 rounded-lg border border-yellow-200 text-yellow-800 whitespace-pre-wrap">
                 {order.notes}
               </div>
             </div>
          )}
        </div>
      </div>
    </div>
  );
}
