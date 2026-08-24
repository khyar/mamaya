import Link from 'next/link';
import { getPrisma } from '@/lib/prisma';

export default async function TicketsPage() {
  const events = await (await getPrisma()).ticketEvent.findMany({
    where: { is_active: true }
  });

  return (
    <>
      <div className="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-indigo-200/80 via-purple-100/40 to-transparent pointer-events-none -z-10"></div>
      
      <section className="pt-[96px] pb-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto text-center flex flex-col items-center">
        <div className="max-w-[800px]">
          <h1 className="text-[40px] md:text-[56px] font-bold text-ink tracking-tight mb-[16px] leading-[1.1]">
            Tiket Konser Impian, <br />Kini Lebih Mudah.
          </h1>
          <p className="text-[18px] md:text-[20px] text-muted mb-[32px] font-normal">
            Jasa war tiket konser terpercaya. Dapatkan akses ke acara favorit Anda tanpa stres.
          </p>
          <div className="flex gap-4 justify-center">
            <Link href="#catalog" className="btn-primary">Lihat Event Tersedia</Link>
          </div>
        </div>
      </section>

      <section id="catalog" className="py-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
        <h2 className="text-[24px] font-bold text-ink mb-[24px]">Event Tersedia</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
          {events.map((event) => (
            <div key={event.id} className="bg-canvas border border-hairline rounded-lg overflow-hidden card-shadow-hover flex flex-col md:flex-row">
              <img src={event.banner_image || '/images/portal/tickets.jpg'} alt={event.name} className="w-full md:w-48 h-48 object-cover" />
              <div className="p-5 flex flex-col flex-1">
                <h3 className="font-bold text-ink text-lg mb-2">{event.name}</h3>
                <p className="text-muted text-sm mb-4 line-clamp-2 flex-1">{event.description}</p>
                <div className="flex items-center justify-between mt-auto">
                  <span className="font-semibold text-ink">
                    {event.event_date ? new Date(event.event_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : 'TBA'}
                  </span>
                  <Link href={`/tickets/${event.slug}`} className="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-active transition-colors text-sm">
                    Pesan Tiket
                  </Link>
                </div>
              </div>
            </div>
          ))}
          
          {events.length === 0 && (
            <div className="col-span-full text-center py-12 text-muted">Belum ada event tiket yang tersedia saat ini.</div>
          )}
        </div>
      </section>
    </>
  );
}
