import Link from 'next/link';
import { getPrisma } from '@/lib/prisma';
import AddToCartButton from '@/components/AddToCartButton';

export default async function FoodPage() {
  const activeBatches = await (await getPrisma()).batch.findMany({
    where: { 
       is_active: true,
       open_date: { lte: new Date() },
       close_date: { gte: new Date() }
    },
    include: { 
      products: {
        where: { is_active: true }
      } 
    },
    orderBy: { delivery_date: 'asc' }
  });

  return (
    <>
      <div className="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-orange-200/80 via-amber-100/40 to-transparent pointer-events-none -z-10"></div>
      
      <section className="pt-[96px] pb-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto text-center flex flex-col items-center">
        <div className="max-w-[800px]">
          <h1 className="text-[40px] md:text-[56px] font-bold text-ink tracking-tight mb-[16px] leading-[1.1]">
            Masakan Rumahan, <br />Diantar Hangat ke Meja Anda.
          </h1>
          <p className="text-[18px] md:text-[20px] text-muted mb-[32px] font-normal">
            Pre-order asinan segar, lauk pauk otentik, dan dessert spesial Mamaya.
          </p>
          <div className="flex gap-4 justify-center">
            <Link href="#menu" className="btn-primary">Lihat Menu PO Aktif</Link>
          </div>
        </div>
      </section>
      
      <section id="menu" className="py-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
        {activeBatches.length === 0 ? (
           <div className="text-center py-12">
             <h2 className="text-[24px] font-bold text-ink mb-[16px]">Belum Ada PO Aktif</h2>
             <p className="text-muted">Mohon maaf, saat ini tidak ada jadwal Pre-Order makanan yang sedang buka. Silakan cek kembali nanti!</p>
           </div>
        ) : (
           activeBatches.map(batch => (
              <div key={batch.id} className="mb-16">
                 <div className="mb-6 pb-4 border-b border-hairline">
                    <h2 className="text-[28px] font-bold text-ink mb-2">{batch.name}</h2>
                    <p className="text-muted mb-4">{batch.description}</p>
                    <div className="flex flex-wrap gap-4">
                       <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                         PO Ditutup: {new Date(batch.close_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}
                       </span>
                       <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="M7 15h0M2 18h20M12 15h0M17 15h0"/></svg>
                         Jadwal Pengiriman: {new Date(batch.delivery_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}
                       </span>
                    </div>
                 </div>

                 <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                   {batch.products.length === 0 ? (
                      <p className="text-muted italic">Belum ada menu di dalam batch ini.</p>
                   ) : (
                     batch.products.map((product) => (
                       <div key={product.id} className="bg-canvas border border-hairline rounded-lg overflow-hidden card-shadow-hover flex flex-col">
                         <img src={product.image || '/images/placeholder-food.jpg'} alt={product.name} className="w-full h-48 object-cover" />
                         <div className="p-5 flex flex-col flex-1">
                           <h3 className="font-bold text-ink text-lg mb-2">{product.name}</h3>
                           <p className="text-muted text-sm mb-4 line-clamp-2 flex-1">{product.description}</p>
                           <div className="flex items-center justify-between mt-auto">
                             <span className="font-semibold text-ink">Rp {product.price.toLocaleString('id-ID')}</span>
                             <AddToCartButton product={product} />
                           </div>
                         </div>
                       </div>
                     ))
                   )}
                 </div>
              </div>
           ))
        )}
      </section>
    </>
  );
}
