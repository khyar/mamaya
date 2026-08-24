import prisma from '@/lib/prisma';
import { notFound } from 'next/navigation';
import JastipRequestForm from '@/components/JastipRequestForm';

export default async function JastipTripPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  
  const trip = await prisma.jastipTrip.findUnique({
    where: { slug },
    include: { 
      catalogs: {
        where: { is_active: true }
      } 
    }
  });

  if (!trip) return notFound();

  return (
    <div className="max-w-[800px] mx-auto px-4 py-12">
      <h1 className="text-3xl font-bold mb-4">{trip.destination}</h1>
      <p className="text-muted mb-8">{trip.description}</p>
      
      <h2 className="text-xl font-bold mb-4">Pilih Barang atau Request Khusus</h2>

      <JastipRequestForm trip={trip} />
    </div>
  );
}
