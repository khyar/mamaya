import prisma from './lib/prisma';

async function main() {
  const user = await prisma.user.upsert({
    where: { email: 'admin@mamaya.id' },
    update: {
      password: 'password',
      role: 'admin',
    },
    create: {
      name: 'Administrator',
      email: 'admin@mamaya.id',
      password: 'password',
      role: 'admin',
    }
  });
  console.log('Admin user updated:', user);
}

main().catch(console.error);
