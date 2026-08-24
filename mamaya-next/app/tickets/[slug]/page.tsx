import { getPrisma } from '@/lib/prisma';
import { notFound } from 'next/navigation';
import TicketCheckoutForm from '@/components/TicketCheckoutForm';

export default async function TicketEventPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  
  const event = await (await getPrisma()).ticketEvent.findUnique({
    where: { slug },
    include: { 
      categories: {
        where: { is_active: true }
      } 
    }
  });

  if (!event) return notFound();

  return (
    <div className="max-w-[800px] mx-auto px-4 py-12">
      <h1 className="text-3xl font-bold mb-4">{event.name}</h1>
      <p className="text-muted mb-8">{event.description}</p>
      
      {event.seating_plan_image && (
        <div className="mb-10 p-4 bg-surface border border-hairline rounded-xl shadow-sm">
          <h2 className="text-xl font-bold text-ink mb-4 text-center">Denah Kursi / Seating Plan</h2>
          <img src={event.seating_plan_image} alt={`Seating plan for ${event.name}`} className="w-full h-auto rounded-lg object-contain bg-canvas max-h-[600px]" />
        </div>
      )}
      
      <TicketCheckoutForm event={event} categories={event.categories} />
    </div>
  );
}
