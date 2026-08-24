'use server';

import prisma from '@/lib/prisma';
import { getAdminSession } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

async function checkAdmin() {
  const session = await getAdminSession();
  if (!session || session.role !== 'admin') {
    throw new Error('Unauthorized');
  }
}

export async function toggleTripStatus(tripId: string, currentStatus: boolean) {
  await checkAdmin();
  await prisma.jastipTrip.update({
    where: { id: tripId },
    data: { is_active: !currentStatus }
  });
  revalidatePath('/admin/jastip');
  revalidatePath(`/admin/jastip/${tripId}`);
}

export async function createTrip(data: FormData) {
  await checkAdmin();
  
  const destination = data.get('destination') as string;
  const slug = data.get('slug') as string;
  const description = data.get('description') as string;
  
  const departure_date = new Date(data.get('departure_date') as string);
  const return_date = new Date(data.get('return_date') as string);
  const po_close_date = new Date(data.get('po_close_date') as string);
  
  const baggage_quota_kg = parseFloat(data.get('baggage_quota_kg') as string);

  await prisma.jastipTrip.create({
    data: {
      destination,
      slug,
      description,
      departure_date,
      return_date,
      po_close_date,
      baggage_quota_kg,
      is_active: true,
    }
  });

  revalidatePath('/admin/jastip');
}

export async function toggleCatalogStatus(catalogId: string, currentStatus: boolean, tripId: string) {
  await checkAdmin();
  await prisma.jastipCatalog.update({
    where: { id: catalogId },
    data: { is_active: !currentStatus }
  });
  revalidatePath(`/admin/jastip/${tripId}`);
}

export async function createCatalog(data: FormData) {
  await checkAdmin();
  
  const trip_id = data.get('trip_id') as string;
  const name = data.get('name') as string;
  const estimated_price = parseFloat(data.get('estimated_price') as string);
  const reference_url = data.get('reference_url') as string;
  const image = data.get('image') as string || '/images/portal/jastip.jpg';

  await prisma.jastipCatalog.create({
    data: {
      trip_id,
      name,
      estimated_price,
      reference_url,
      image,
      is_active: true
    }
  });

  revalidatePath(`/admin/jastip/${trip_id}`);
}
