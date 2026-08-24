'use server';

import prisma from '@/lib/prisma';
import { createAdminSession, clearAdminSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export async function loginAction(formData: FormData) {
  const email = formData.get('email') as string;
  const password = formData.get('password') as string;

  if (!email || !password) {
    return { error: 'Email dan password wajib diisi.' };
  }

  // Admin seed fallback: if no admin exists, create one with default password
  let user = await prisma.user.findFirst({
    where: { email }
  });

  if (!user && email === 'admin@mamaya.id' && password === 'password') {
    user = await prisma.user.create({
      data: {
        name: 'Administrator',
        email: 'admin@mamaya.id',
        password: 'password', // In real app, this should be hashed
        role: 'admin'
      }
    });
  }

  if (!user || user.role !== 'admin' || user.password !== password) {
    return { error: 'Email atau password salah, atau Anda bukan admin.' };
  }

  await createAdminSession(user.email);
  return { success: true };
}

export async function logoutAction() {
  await clearAdminSession();
  redirect('/admin/login');
}
