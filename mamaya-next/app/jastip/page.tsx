import Link from 'next/link';
import prisma from '@/lib/prisma';

export default async function JastipPage() {
  const trips = await prisma.jastipTrip.findMany({
    where: { is_active: true }
  });

  return (
    <>
      <div className="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-sky-200/80 via-teal-100/40 to-transparent pointer-events-none -z-10"></div>

      <section className="pt-[96px] pb-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto text-center flex flex-col items-center">
        <div className="max-w-[800px]">
          <h1 className="text-[40px] md:text-[56px] font-bold text-ink tracking-tight mb-[16px] leading-[1.1]">
            Barang Impianmu, <br />Hadir di Depan Pintu.
          </h1>
          <p className="text-[18px] md:text-[20px] text-muted mb-[32px] font-normal">
            Jastip barang branded, skincare, dan barang langka dari US, UK, dan Jepang.
          </p>
          <div className="flex gap-4 justify-center">
            <Link href="#trips" className="btn-primary">Lihat Jadwal Trip</Link>
          </div>
        </div>
      </section>

      <section id="trips" className="py-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto">
        <h2 className="text-[24px] font-bold text-ink mb-[24px]">Jadwal Trip Tersedia</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
          {trips.map((trip) => (
            <div key={trip.id} className="bg-canvas border border-hairline rounded-lg overflow-hidden card-shadow-hover flex flex-col md:flex-row">
              <div className="w-full md:w-48 h-48 bg-sky-50 flex items-center justify-center flex-col p-4 border-r border-hairline">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" className="text-sky-500 mb-2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.2-1.1.7L2.5 9l8.1 4.5L7 17l-3.5-1.5L2 17l4 4 1.5-1.5L6 16l3.5-3.6 4.5 8.1c.3.5.7.9 1.2.7l2.1-1.2c.5-.2.8-.7.7-1.2z" /></svg>
                <span className="font-bold text-ink text-center text-sm">{trip.destination}</span>
              </div>
              <div className="p-5 flex flex-col flex-1">
                <h3 className="font-bold text-ink text-lg mb-2">{trip.destination}</h3>
                <p className="text-muted text-sm mb-4 line-clamp-2 flex-1">{trip.description}</p>
                <div className="flex items-center justify-between mt-auto">
                  <div className="flex flex-col">
                    <span className="text-xs text-muted">Keberangkatan:</span>
                    <span className="font-semibold text-ink text-sm">
                      {trip.departure_date ? new Date(trip.departure_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '-'}
                    </span>
                  </div>
                  <Link href={`/jastip/${trip.slug}`} className="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-active transition-colors text-sm">
                    Ikut Jastip
                  </Link>
                </div>
              </div>
            </div>
          ))}

          {trips.length === 0 && (
            <div className="col-span-full text-center py-12 text-muted">Belum ada jadwal trip yang tersedia saat ini.</div>
          )}
        </div>
      </section>
    </>
  );
}
