'use server';

import { getPrisma } from '@/lib/prisma';
import { v4 as uuidv4 } from 'uuid';

export async function checkoutAction(data: {
  name: string;
  phone: string;
  shippingMethod: 'pickup' | 'delivery';
  address?: string;
  notes?: string;
  batchId: string;
  items: { productId: string; name: string; quantity: number; price: number }[];
}) {
  try {
    const { name, phone, shippingMethod, address, notes, batchId, items } = data;
    
    // Hitung total dari server untuk mencegah manipulasi client
    const subtotal = items.reduce((acc, item) => acc + item.price * item.quantity, 0);

    // Guest user creation
    let user = await (await getPrisma()).user.findFirst({
      where: { name, email: `${phone}@guest.mamaya.id` }
    });

    if (!user) {
      user = await (await getPrisma()).user.create({
        data: {
          name,
          email: `${phone}@guest.mamaya.id`,
          password: 'GUEST_PASSWORD', 
          role: 'guest',
        }
      });
    }

    // Generate Order ID format FOD-YYYYMMDD-XXXX
    const dateStr = new Date().toISOString().slice(0,10).replace(/-/g,''); // YYYYMMDD
    let orderId = '';
    let isUnique = false;

    // Pastikan order_id unik
    while (!isUnique) {
      const randomPart = Math.random().toString(36).substring(2, 6).toUpperCase();
      orderId = `FOD-${dateStr}-${randomPart}`;
      
      const existing = await (await getPrisma()).order.findUnique({
        where: { id: orderId }
      });
      
      if (!existing) {
        isUnique = true;
      }
    }

    // Create Order with Items
    const order = await (await getPrisma()).order.create({
      data: {
        id: orderId,
        userId: user.id,
        batchId: batchId,
        order_type: 'food',
        status: shippingMethod === 'delivery' ? 'awaiting_shipping_cost' : 'awaiting_payment',
        customer_name: name,
        customer_phone: phone,
        customer_address: shippingMethod === 'delivery' ? address : null,
        shipping_method: shippingMethod,
        notes: notes,
        subtotal: subtotal,
        items: {
          create: items.map(item => ({
            productId: item.productId,
            product_name: item.name,
            quantity: item.quantity,
            price: item.price
          }))
        }
      }
    });

    console.log(`[Order Created] ${orderId} - ${name} (${phone}) - Rp ${subtotal}`);

    return order.id;
  } catch (error) {
    console.error("Food checkout action failed:", error);
    throw new Error("Gagal membuat pesanan makanan");
  }
}

export async function checkoutTicketAction(data: {
  name: string;
  phone: string;
  email: string;
  ktp: string;
  categoryId: string;
  categoryName: string;
  price: number;
  quantity: number;
  eventId: string;
}) {
  try {
    const { name, phone, email, ktp, categoryId, categoryName, price, quantity, eventId } = data;
    
    if (quantity > 4) {
      throw new Error("Maksimal pembelian adalah 4 tiket.");
    }
    
    const event = await (await getPrisma()).ticketEvent.findUnique({ where: { id: eventId } });
    if (!event) throw new Error("Event tidak ditemukan");
    
    const now = new Date();
    if (event.war_start_time && now < event.war_start_time) {
      throw new Error("Penjualan tiket belum dimulai.");
    }
    if (event.war_end_time && now > event.war_end_time) {
      throw new Error("Penjualan tiket sudah ditutup.");
    }

    const subtotal = price * quantity;

    let user = await (await getPrisma()).user.findFirst({
      where: { name, email: `${phone}@guest.mamaya.id` }
    });

    if (!user) {
      user = await (await getPrisma()).user.create({
        data: {
          name,
          email: `${phone}@guest.mamaya.id`,
          password: 'GUEST_PASSWORD',
          role: 'guest',
        }
      });
    }

    const dateStr = new Date().toISOString().slice(0,10).replace(/-/g,'');
    let orderId = '';
    let isUnique = false;

    while (!isUnique) {
      const randomPart = Math.random().toString(36).substring(2, 6).toUpperCase();
      orderId = `TIX-${dateStr}-${randomPart}`;
      
      const existing = await (await getPrisma()).order.findUnique({
        where: { id: orderId }
      });
      if (!existing) isUnique = true;
    }

    const order = await (await getPrisma()).order.create({
      data: {
        id: orderId,
        userId: user.id,
        order_type: 'ticket',
        status: 'awaiting_payment',
        customer_name: name,
        customer_phone: phone,
        customer_ktp: ktp,
        notes: `Email E-Ticket: ${email}`,
        subtotal: subtotal,
        ticketEventId: eventId,
        items: {
          create: [{
            ticketCategoryId: categoryId,
            product_name: `Tiket ${categoryName}`,
            quantity: quantity,
            price: price
          }]
        }
      }
    });

    // Kurangi kuota
    await (await getPrisma()).ticketCategory.update({
      where: { id: categoryId },
      data: {
        available_quota: {
          decrement: quantity
        }
      }
    });

    console.log(`[Ticket Order Created] ${orderId}`);
    return order.id;
  } catch (error) {
    console.error("Ticket checkout failed:", error);
    throw new Error("Gagal memproses tiket");
  }
}

export async function requestJastipAction(data: {
  name: string;
  phone: string;
  shippingAddress: string;
  requestNote?: string;
  tripId: string;
  catalogs?: { catalogId: string; name: string; quantity: number; estimatedPrice: number }[];
}) {
  try {
    const { name, phone, shippingAddress, requestNote, tripId, catalogs } = data;
    
    const trip = await (await getPrisma()).jastipTrip.findUnique({ where: { id: tripId } });
    if (!trip || !trip.is_active || (trip.po_close_date && new Date() > trip.po_close_date)) {
      throw new Error("PO Jastip untuk trip ini sudah ditutup.");
    }

    let user = await (await getPrisma()).user.findFirst({
      where: { name, email: `${phone}@guest.mamaya.id` }
    });

    if (!user) {
      user = await (await getPrisma()).user.create({
        data: {
          name,
          email: `${phone}@guest.mamaya.id`,
          password: 'GUEST_PASSWORD',
          role: 'guest',
        }
      });
    }

    const dateStr = new Date().toISOString().slice(0,10).replace(/-/g,'');
    let orderId = '';
    let isUnique = false;

    while (!isUnique) {
      const randomPart = Math.random().toString(36).substring(2, 6).toUpperCase();
      orderId = `JST-${dateStr}-${randomPart}`;
      
      const existing = await (await getPrisma()).order.findUnique({
        where: { id: orderId }
      });
      if (!existing) isUnique = true;
    }

    const order = await (await getPrisma()).order.create({
      data: {
        id: orderId,
        userId: user.id,
        order_type: 'jastip',
        status: 'pending', // Pending admin review & quotation
        customer_name: name,
        customer_phone: phone,
        customer_address: shippingAddress,
        subtotal: 0, // Quotation will be provided later
        jastipTripId: tripId,
        notes: requestNote || 'Predefined catalog item',
        items: catalogs && catalogs.length > 0 ? {
          create: catalogs.map(cat => ({
            jastipCatalogId: cat.catalogId,
            product_name: cat.name,
            quantity: cat.quantity,
            price: cat.estimatedPrice,
          }))
        } : {
          create: [{
            product_name: "Jastip Request (Menunggu Harga)",
            quantity: 1,
            price: 0
          }]
        }
      }
    });

    console.log(`[Jastip Request Created] ${orderId}`);
    return order.id;
  } catch (error) {
    console.error("Jastip checkout action failed:", error);
    throw new Error(error instanceof Error ? error.message : "Gagal mengirim request jastip");
  }
}

export async function globalCheckoutAction(data: {
  name: string;
  phone: string;
  email: string;
  ktp: string;
  shippingMethod: 'pickup' | 'delivery';
  shippingAddress: string;
  notes: string;
  items: any[];
}) {
  const { name, phone, shippingMethod, shippingAddress, notes, items } = data;
  
  const subtotal = items.reduce((acc, item) => acc + item.price * item.quantity, 0);

  // 1. Guest User
  let user = await (await getPrisma()).user.findFirst({
    where: { name, email: `${phone}@guest.mamaya.id` }
  });

  if (!user) {
    user = await (await getPrisma()).user.create({
      data: {
        name,
        email: `${phone}@guest.mamaya.id`,
        password: 'GUEST_PASSWORD',
        role: 'guest',
      }
    });
  }

  // 2. Generate short ID: MMY-XXXXXX
  const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase();
  const orderId = `MMY-${randomPart}`;

  // 3. Extract IDs for relations
  const batchId = items.find(i => i.domain === 'food')?.batchId;
  const ticketEventId = items.find(i => i.domain === 'ticket')?.ticketEventId;
  const jastipTripId = items.find(i => i.domain === 'jastip')?.jastipTripId;

  // 4. Build Order Items
  const orderItemsData = items.map(item => {
    let payload: any = {
      product_name: item.name,
      quantity: item.quantity,
      price: item.price
    };
    if (item.domain === 'food') {
      payload.productId = item.id;
    } else if (item.domain === 'ticket') {
      payload.ticketCategoryId = item.id;
    } else if (item.domain === 'jastip') {
      if (!item.isCustomRequest) {
        payload.jastipCatalogId = item.id;
      }
    }
    return payload;
  });

  // 5. Append ticket info to notes if needed
  let finalNotes = notes;
  if (data.email || data.ktp) {
    finalNotes = `Data Tiket | Email: ${data.email || '-'} | KTP: ${data.ktp || '-'}\n` + (finalNotes || '');
  }

  // 6. Check ticket quotas and dates
  const ticketItems = items.filter(i => i.domain === 'ticket');
  if (ticketItems.length > 0) {
     for (const ticket of ticketItems) {
       const cat = await (await getPrisma()).ticketCategory.findUnique({ where: { id: ticket.id }, include: { event: true } });
       if (!cat) throw new Error(`Kategori tiket ${ticket.name} tidak ditemukan.`);
       if (cat.available_quota < ticket.quantity) throw new Error(`Kuota tiket ${cat.name} tidak mencukupi.`);
       const now = new Date();
       if (cat.event.war_start_time && now < cat.event.war_start_time) throw new Error("Penjualan tiket belum dimulai.");
       if (cat.event.war_end_time && now > cat.event.war_end_time) throw new Error("Penjualan tiket sudah ditutup.");
       
       await (await getPrisma()).ticketCategory.update({
         where: { id: ticket.id },
         data: { available_quota: { decrement: ticket.quantity } }
       });
     }
  }

  // 7. Check jastip trip
  const jastipItems = items.filter(i => i.domain === 'jastip');
  if (jastipItems.length > 0 && jastipTripId) {
     const trip = await (await getPrisma()).jastipTrip.findUnique({ where: { id: jastipTripId } });
     if (!trip || !trip.is_active || (trip.po_close_date && new Date() > trip.po_close_date)) {
       throw new Error("PO Jastip sudah ditutup.");
     }
  }

  // 8. Create Order
  const order = await (await getPrisma()).order.create({
    data: {
      id: orderId,
      userId: user.id,
      order_type: 'mixed',
      status: 'awaiting_payment',
      customer_name: name,
      customer_phone: phone,
      customer_ktp: data.ktp || null,
      customer_address: shippingAddress || null,
      shipping_method: shippingMethod,
      notes: finalNotes,
      subtotal: subtotal,
      batchId: batchId || null,
      ticketEventId: ticketEventId || null,
      jastipTripId: jastipTripId || null,
      items: {
        create: orderItemsData
      }
    }
  });

  return [order.id];
}
