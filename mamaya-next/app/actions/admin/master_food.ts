'use server';

import { getPrisma } from '@/lib/prisma';
import { getAdminSession } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

async function checkAdmin() {
  const session = await getAdminSession();
  if (!session || session.role !== 'admin') {
    throw new Error('Unauthorized');
  }
}

export async function createMasterProduct(data: FormData) {
  await checkAdmin();
  
  const name = data.get('name') as string;
  const description = data.get('description') as string;
  const base_price = parseFloat(data.get('base_price') as string);
  const image = data.get('image') as string || '/images/placeholder-food.jpg';

  await (await getPrisma()).masterProduct.create({
    data: {
      name,
      description,
      base_price,
      image,
      is_active: true
    }
  });

  revalidatePath('/admin/food/master');
}

export async function toggleMasterProductStatus(id: string, currentStatus: boolean) {
  await checkAdmin();
  await (await getPrisma()).masterProduct.update({
    where: { id },
    data: { is_active: !currentStatus }
  });
  revalidatePath('/admin/food/master');
}
