import { PrismaClient } from '@prisma/client'
import { PrismaLibSql } from '@prisma/adapter-libsql'
import { v4 as uuidv4 } from 'uuid'

const adapter = new PrismaLibSql({
  url: process.env.DATABASE_URL || 'file:./dev.db'
})

const prisma = new PrismaClient({ adapter })

async function main() {
  console.log('Clearing old data...')
  await prisma.orderItem.deleteMany()
  await prisma.order.deleteMany()
  await prisma.product.deleteMany()
  await prisma.batch.deleteMany()
  await prisma.ticketCategory.deleteMany()
  await prisma.ticketEvent.deleteMany()
  await prisma.jastipCatalog.deleteMany()
  await prisma.jastipTrip.deleteMany()
  await prisma.user.deleteMany()

  console.log('Seeding Users...')
  const admin = await prisma.user.create({
    data: {
      name: 'Admin Mamaya',
      email: 'admin@mamaya.id',
      password: 'password',
      role: 'admin'
    }
  })

  // 1. FOOD DOMAIN
  console.log('Seeding Food (Batches & Products)...')
  const batch1 = await prisma.batch.create({
    data: {
      name: 'Batch Agustus Minggu Ke-3',
      description: 'Menu spesial kemerdekaan dengan sambal matah ekstra pedas.',
      open_date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // Opened 2 days ago
      close_date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // Closes in 2 days
      delivery_date: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // Delivery in 5 days
      status: 'open',
      is_active: true
    }
  });

  const batch2 = await prisma.batch.create({
    data: {
      name: 'Batch Agustus Minggu Ke-4',
      description: 'Pre-order akhir bulan.',
      open_date: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000), // Opens in 3 days
      close_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000), // Closes in 7 days
      delivery_date: new Date(Date.now() + 10 * 24 * 60 * 60 * 1000), // Delivery in 10 days
      status: 'open',
      is_active: true
    }
  });

  const food1 = await prisma.product.create({
    data: {
      name: 'Ayam Woku Manado',
      description: 'Pedas, gurih, bumbu melimpah. Khas resep keluarga Mamaya.',
      price: 45000,
      stock: 50,
      batchId: batch1.id,
      image: '/images/placeholder-food.jpg'
    }
  })

  const food2 = await prisma.product.create({
    data: {
      name: 'Asinan Buah Segar',
      description: 'Asam manis pedas, dijamin bikin melek.',
      price: 25000,
      stock: 100,
      batchId: batch1.id,
      image: '/images/placeholder-food.jpg'
    }
  })

  // 2. TICKETS DOMAIN
  console.log('Seeding Tickets (Events & Categories)...')
  const event1 = await prisma.ticketEvent.create({
    data: {
      name: 'Coldplay: Music of the Spheres',
      slug: 'coldplay-jakarta',
      description: 'Konser Coldplay di Gelora Bung Karno Jakarta',
      venue: 'Gelora Bung Karno',
      war_start_time: new Date(new Date().setDate(new Date().getDate() - 1)), // War started yesterday
      war_end_time: new Date(new Date().setDate(new Date().getDate() + 2)), // Ends in 2 days
      event_date: new Date('2026-11-15T19:00:00Z'),
      banner_image: '/images/portal/tickets.jpg',
      terms: 'Maksimal pembelian 4 tiket per akun.',
      is_active: true
    }
  })

  await prisma.ticketCategory.create({
    data: {
      event_id: event1.id,
      name: 'CAT 1',
      price: 5000000,
      quota: 10,
      available_quota: 10
    }
  })

  await prisma.ticketCategory.create({
    data: {
      event_id: event1.id,
      name: 'Festival',
      price: 3500000,
      quota: 20,
      available_quota: 5
    }
  })

  // 3. JASTIP DOMAIN
  console.log('Seeding Jastip (Trips & Catalogs)...')
  const trip1 = await prisma.jastipTrip.create({
    data: {
      destination: 'Jepang (Tokyo & Osaka)',
      slug: 'trip-jepang-nov-2026',
      departure_date: new Date('2026-11-01T00:00:00Z'),
      return_date: new Date('2026-11-10T00:00:00Z'),
      po_close_date: new Date('2026-10-25T00:00:00Z'),
      baggage_quota_kg: 30.5,
      description: 'Jastip skincare, snack, dan merchandise anime asli Jepang.',
      is_active: true
    }
  })

  await prisma.jastipCatalog.create({
    data: {
      trip_id: trip1.id,
      name: 'Tokyo Banana (Isi 8)',
      estimated_price: 150000,
      image: '/images/portal/jastip.jpg'
    }
  })

  await prisma.jastipCatalog.create({
    data: {
      trip_id: trip1.id,
      name: 'Hada Labo Gokujyun Premium Lotion',
      estimated_price: 200000,
      image: '/images/portal/jastip.jpg'
    }
  })

  console.log('Seed completed successfully!')
}

main()
  .catch((e) => {
    console.error(e)
    process.exit(1)
  })
  .finally(async () => {
    await prisma.$disconnect()
  });
