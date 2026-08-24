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

export async function toggleBatchStatus(batchId: string, currentStatus: boolean) {
  await checkAdmin();
  await prisma.batch.update({
    where: { id: batchId },
    data: { is_active: !currentStatus }
  });
  revalidatePath('/admin/food');
  revalidatePath(`/admin/food/${batchId}`);
}

export async function createBatch(data: FormData) {
  await checkAdmin();
  
  const name = data.get('name') as string;
  const description = data.get('description') as string;
  const open_date = new Date(data.get('open_date') as string);
  const close_date = new Date(data.get('close_date') as string);
  const delivery_date = new Date(data.get('delivery_date') as string);

  await prisma.batch.create({
    data: {
      name,
      description,
      open_date,
      close_date,
      delivery_date,
      is_active: true,
      status: 'open'
    }
  });

  revalidatePath('/admin/food');
}

export async function toggleProductStatus(productId: string, currentStatus: boolean, batchId: string) {
  await checkAdmin();
  await prisma.product.update({
    where: { id: productId },
    data: { is_active: !currentStatus }
  });
  revalidatePath(`/admin/food/${batchId}`);
}

export async function createProduct(data: FormData) {
  await checkAdmin();
  
  const batchId = data.get('batchId') as string;
  const name = data.get('name') as string;
  const description = data.get('description') as string;
  const price = parseFloat(data.get('price') as string);
  const stock = parseInt(data.get('stock') as string, 10);
  
  // Image handling in a real app would upload to S3/Cloudflare R2
  // For now, we just use a placeholder or string
  const image = data.get('image') as string || '/images/placeholder-food.jpg';

  await prisma.product.create({
    data: {
      batchId,
      name,
      description,
      price,
      stock,
      image,
      is_active: true
    }
  });

  revalidatePath(`/admin/food/${batchId}`);
}
