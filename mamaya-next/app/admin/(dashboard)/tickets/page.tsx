import prisma from '@/lib/prisma';
import Link from 'next/link';
import { toggleEventStatus, createEvent } from '@/app/actions/admin/tickets';
import { Plus, Power } from 'lucide-react';

export default async function AdminTicketsPage() {
  const events = await prisma.ticketEvent.findMany({
    orderBy: { createdAt: 'desc' },
    include: { _count: { select: { categories: true } } }
  });

  return (
    <div>
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h1 className="text-3xl font-bold text-ink mb-1">Kelola Tiket</h1>
          <p className="text-muted text-sm">Manajemen Event Konser dan Kategori Tiket.</p>
        </div>
        
        <details className="group relative">
          <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none">
            <Plus className="w-4 h-4" /> Buat Event Baru
          </summary>
          <div className="absolute right-0 top-full mt-2 w-96 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10 h-[80vh] overflow-y-auto">
            <h3 className="font-bold text-ink mb-3">Buat Event Konser</h3>
            <form action={createEvent} className="space-y-3">
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Nama Event</label>
                <input type="text" name="name" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Slug URL</label>
                <input type="text" name="slug" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="contoh-event-2026" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Venue (Lokasi)</label>
                <input type="text" name="venue" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Deskripsi Singkat</label>
                <textarea name="description" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" rows={2}></textarea>
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Tanggal & Waktu Event</label>
                <input type="datetime-local" name="event_date" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
              </div>
              <div className="flex gap-2">
                 <div className="flex-1">
                   <label className="block text-xs font-medium text-ink mb-1">War Start</label>
                   <input type="datetime-local" name="war_start_time" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                 </div>
                 <div className="flex-1">
                   <label className="block text-xs font-medium text-ink mb-1">War End</label>
                   <input type="datetime-local" name="war_end_time" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
                 </div>
              </div>
              <div>
                 <label className="block text-xs font-medium text-ink mb-1">Max Tiket per Akun</label>
                 <input type="number" name="max_tickets_per_user" defaultValue="4" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
              </div>
              <div>
                 <label className="block text-xs font-medium text-ink mb-1">Link Gambar Denah Kursi (Opsional)</label>
                 <input type="text" name="seating_plan_image" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" placeholder="/images/seating.jpg" />
              </div>
              <div>
                <label className="block text-xs font-medium text-ink mb-1">Syarat & Ketentuan</label>
                <textarea name="terms" className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" rows={2}></textarea>
              </div>
              <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active mt-2">Simpan Event</button>
            </form>
          </div>
        </details>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {events.map(event => (
          <div key={event.id} className={`bg-surface border rounded-xl overflow-hidden shadow-sm flex flex-col ${event.is_active ? 'border-hairline' : 'border-red-200 opacity-80'}`}>
            <div className="h-32 bg-canvas overflow-hidden">
               <img src={event.banner_image || ''} alt={event.name} className="w-full h-full object-cover opacity-80" />
            </div>
            <div className="p-5 flex-1 -mt-6">
              <div className="bg-surface inline-block px-3 py-1 rounded-lg border border-hairline shadow-sm mb-3 text-xs font-medium text-muted">
                 {event.venue}
              </div>
              <div className="flex justify-between items-start mb-2">
                 <h2 className="text-lg font-bold text-ink leading-tight">{event.name}</h2>
                 <span className={`text-xs px-2 py-1 rounded-md font-medium shrink-0 ml-2 ${event.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                   {event.is_active ? 'Aktif' : 'Nonaktif'}
                 </span>
              </div>
              
              <div className="space-y-1.5 text-xs text-muted mb-4 mt-4">
                <div className="flex justify-between"><span>Tgl Event:</span> <span className="font-medium text-ink">{event.event_date ? new Date(event.event_date).toLocaleDateString('id-ID') : '-'}</span></div>
                <div className="flex justify-between"><span>Mulai War:</span> <span className="font-medium text-ink">{event.war_start_time ? new Date(event.war_start_time).toLocaleString('id-ID') : '-'}</span></div>
              </div>
              
              <div className="text-sm font-medium text-primary bg-sky-50 p-2 rounded-lg text-center mt-auto">
                {event._count.categories} Kategori Tiket
              </div>
            </div>
            
            <div className="border-t border-hairline p-3 flex items-center justify-between bg-canvas gap-3">
              <Link href={`/admin/tickets/${event.id}`} className="flex-1 bg-surface-soft hover:bg-sky-50 text-ink hover:text-primary font-medium text-sm text-center py-2 rounded-lg transition-colors border border-hairline">
                Kelola Tiket
              </Link>
              <form action={toggleEventStatus.bind(null, event.id, event.is_active)}>
                <button type="submit" className={`p-2 rounded-lg border flex items-center justify-center transition-colors ${event.is_active ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-500 hover:bg-green-50'}`} title={event.is_active ? 'Nonaktifkan Event' : 'Aktifkan Event'}>
                  <Power className="w-4 h-4" />
                </button>
              </form>
            </div>
          </div>
        ))}

        {events.length === 0 && (
          <div className="col-span-full text-center p-12 bg-surface border border-dashed border-hairline rounded-xl text-muted">
            Belum ada event tiket yang dibuat.
          </div>
        )}
      </div>
    </div>
  );
}
