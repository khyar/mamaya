import prisma from '@/lib/prisma';
import Link from 'next/link';

export default async function TrackPage({
  searchParams,
}: {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}) {
  const params = await searchParams;
  const orderId = typeof params.order === 'string' ? params.order : null;

  let order = null;
  let error = null;

  if (orderId) {
    try {
      order = await prisma.order.findUnique({
        where: { id: orderId.toUpperCase() },
        include: {
          items: {
            include: {
              product: true
            }
          }
        }
      });

      if (!order) {
        error = "Pesanan tidak ditemukan. Pastikan kode pesanan sudah benar.";
      }
    } catch (err) {
      error = "Terjadi kesalahan saat mencari pesanan.";
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending': return <span className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">Menunggu Pembayaran</span>;
      case 'processing': return <span className="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">Sedang Diproses</span>;
      case 'completed': return <span className="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Selesai</span>;
      case 'cancelled': return <span className="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Dibatalkan</span>;
      default: return <span className="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">{status}</span>;
    }
  };

  return (
    <div className="max-w-[800px] mx-auto px-4 sm:px-6 py-12 min-h-[70vh]">
      <div className="text-center mb-10">
        <h1 className="text-3xl font-bold text-ink mb-2">Lacak Pesanan</h1>
        <p className="text-muted">Masukkan kode pesanan Anda (contoh: MMY-ABCD12)</p>
      </div>

      <div className="bg-canvas border border-hairline rounded-xl p-6 shadow-sm mb-8">
        <form className="flex flex-col sm:flex-row gap-4">
          <input
            type="text"
            name="order"
            defaultValue={orderId || ''}
            placeholder="Kode Pesanan (MMY-...)"
            className="flex-1 border border-hairline rounded-lg p-3 bg-canvas focus:outline-none focus:border-primary text-ink"
            required
          />
          <button type="submit" className="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-active transition-colors">
            Cari Pesanan
          </button>
        </form>
      </div>

      {error && (
        <div className="bg-red-50 text-red-600 p-4 rounded-lg text-center mb-8">
          {error}
        </div>
      )}

      {order && (
        <div className="bg-canvas border border-hairline rounded-xl overflow-hidden shadow-sm">
          <div className="p-6 border-b border-hairline bg-surface-soft flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <p className="text-sm text-muted mb-1">Kode Pesanan</p>
              <h2 className="text-xl font-mono font-bold text-ink">{order.id}</h2>
            </div>
            <div>
              {getStatusBadge(order.status)}
            </div>
          </div>
          
          <div className="p-6">
            <h3 className="font-semibold text-ink mb-4 border-b border-hairline pb-2">Detail Pesanan</h3>
            <div className="space-y-4 mb-6">
              {order.items.map((item: any) => (
                <div key={item.id} className="flex justify-between items-center">
                  <div className="flex gap-4 items-center">
                    {item.product?.image ? (
                      <img src={item.product.image} alt={item.product_name} className="w-12 h-12 object-cover rounded" />
                    ) : (
                      <div className="w-12 h-12 bg-surface-soft rounded flex items-center justify-center text-muted text-xs">Foto</div>
                    )}
                    <div>
                      <p className="font-medium text-ink">{item.product_name}</p>
                      <p className="text-sm text-muted">{item.quantity} x Rp {item.price.toLocaleString('id-ID')}</p>
                    </div>
                  </div>
                  <div className="font-semibold text-ink">
                    Rp {(item.quantity * item.price).toLocaleString('id-ID')}
                  </div>
                </div>
              ))}
            </div>

            <div className="border-t border-hairline pt-4 flex flex-col items-end">
              <p className="text-muted mb-1">Total Belanja</p>
              <p className="text-2xl font-bold text-ink">Rp {(order.grand_total || order.subtotal).toLocaleString('id-ID')}</p>
            </div>
          </div>
        </div>
      )}

      {!order && !error && (
        <div className="text-center py-12 border border-dashed border-hairline rounded-xl">
          <div className="w-16 h-16 bg-surface-soft rounded-full flex items-center justify-center mx-auto mb-4 text-muted">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </div>
          <p className="text-muted">Gunakan form di atas untuk melacak pesanan Anda.</p>
        </div>
      )}
      
      <div className="mt-8 text-center">
         <Link href="/" className="text-primary hover:text-primary-active font-medium inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Beranda
         </Link>
      </div>
    </div>
  );
}
