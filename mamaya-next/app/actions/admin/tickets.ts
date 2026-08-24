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

export async function toggleEventStatus(eventId: string, currentStatus: boolean) {
  await checkAdmin();
  await prisma.ticketEvent.update({
    where: { id: eventId },
    data: { is_active: !currentStatus }
  });
  revalidatePath('/admin/tickets');
  revalidatePath(`/admin/tickets/${eventId}`);
}

export async function createEvent(data: FormData) {
  await checkAdmin();
  
  const name = data.get('name') as string;
  const slug = data.get('slug') as string;
  const venue = data.get('venue') as string;
  const description = data.get('description') as string;
  const terms = data.get('terms') as string;
  
  const event_date = new Date(data.get('event_date') as string);
  const war_start_time = new Date(data.get('war_start_time') as string);
  const war_end_time = new Date(data.get('war_end_time') as string);
  
  const banner_image = data.get('banner_image') as string || '/images/portal/tickets.jpg';
  const seating_plan_image = data.get('seating_plan_image') as string || '';
  const max_tickets_per_user = parseInt(data.get('max_tickets_per_user') as string, 10) || 4;

  await prisma.ticketEvent.create({
    data: {
      name,
      slug,
      venue,
      description,
      terms,
      event_date,
      war_start_time,
      war_end_time,
      banner_image,
      seating_plan_image,
      max_tickets_per_user,
      is_active: true,
    }
  });

  revalidatePath('/admin/tickets');
}

export async function toggleCategoryStatus(categoryId: string, currentStatus: boolean, eventId: string) {
  await checkAdmin();
  await prisma.ticketCategory.update({
    where: { id: categoryId },
    data: { is_active: !currentStatus }
  });
  revalidatePath(`/admin/tickets/${eventId}`);
}

export async function createCategory(data: FormData) {
  await checkAdmin();
  
  const event_id = data.get('event_id') as string;
  const name = data.get('name') as string;
  const price = parseFloat(data.get('price') as string);
  const quota = parseInt(data.get('quota') as string, 10);

  await prisma.ticketCategory.create({
    data: {
      event_id,
      name,
      price,
      quota,
      available_quota: quota,
      is_active: true
    }
  });

  revalidatePath(`/admin/tickets/${event_id}`);
}
