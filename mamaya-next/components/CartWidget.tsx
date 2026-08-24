'use client';

import { useCartStore } from '@/store/cartStore';
import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { globalCheckoutAction } from '@/app/actions/checkout';

export default function CartWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const [isMounted, setIsMounted] = useState(false);
  
  const { items, removeFromCart, increaseQuantity, decreaseQuantity, getTotalPrice, getTotalItems, clearCart } = useCartStore();
  
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  // Dynamic fields
  const [shippingMethod, setShippingMethod] = useState<'pickup' | 'delivery'>('pickup');
  const [address, setAddress] = useState('');
  const [notes, setNotes] = useState('');
  const [email, setEmail] = useState('');
  const [ktp, setKtp] = useState('');
  
  const router = useRouter();

  useEffect(() => {
    setIsMounted(true);
    const savedName = localStorage.getItem('mamaya_name');
    const savedPhone = localStorage.getItem('mamaya_phone');
    const savedAddress = localStorage.getItem('mamaya_address');
    const savedEmail = localStorage.getItem('mamaya_email');
    const savedKtp = localStorage.getItem('mamaya_ktp');
    const savedJastipNotes = localStorage.getItem('mamaya_jastip_notes');
    
    if (savedName) setName(savedName);
    if (savedPhone) setPhone(savedPhone);
    if (savedAddress) setAddress(savedAddress);
    if (savedEmail) setEmail(savedEmail);
    if (savedKtp) setKtp(savedKtp);
    if (savedJastipNotes && !notes) setNotes(savedJastipNotes);
  }, []);

  if (!isMounted) return null;

  const totalItems = getTotalItems();
  const totalPrice = getTotalPrice();

  const hasFood = items.some(i => i.domain === 'food');
  const hasTicket = items.some(i => i.domain === 'ticket');
  const hasJastip = items.some(i => i.domain === 'jastip');
  
  const needsAddress = hasFood || hasJastip;

  const handleCheckout = async (e: React.FormEvent) => {
    e.preventDefault();
    if (items.length === 0) return;

    // Validation
    if (hasTicket && ktp.length !== 16) {
       alert("Nomor NIK KTP wajib persis 16 digit untuk pemesanan Tiket!");
       return;
    }
    
    // Validate Food batchId
    const foodItems = items.filter(i => i.domain === 'food');
    if (foodItems.length > 0) {
       const missingBatch = foodItems.find(i => !i.batchId);
       if (missingBatch) {
          alert(`Error: Makanan "${missingBatch.name}" tidak memiliki ID Batch valid. Silakan hapus dan tambahkan ulang.`);
          return;
       }
    }

    localStorage.setItem('mamaya_name', name);
    localStorage.setItem('mamaya_phone', phone);
    if (address) localStorage.setItem('mamaya_address', address);
    if (email) localStorage.setItem('mamaya_email', email);
    if (ktp) localStorage.setItem('mamaya_ktp', ktp);

    setIsSubmitting(true);
    try {
      const orderIds = await globalCheckoutAction({
        name,
        phone,
        email,
        ktp,
        shippingMethod,
        shippingAddress: address,
        notes,
        items
      });
      
      clearCart();
      localStorage.removeItem('mamaya_jastip_notes'); // Clear local notes
      setIsOpen(false);
      
      // If multiple orders, just show tracking for the first one for now or a success page.
      if (orderIds && orderIds.length > 0) {
         alert(`Pesanan berhasil dibuat! ID: ${orderIds.join(', ')}`);
         router.push(`/track?order=${orderIds[0]}`);
      }
    } catch (error: any) {
      console.error("Checkout action failed:", error);
      alert(error.message || "Gagal membuat pesanan. Silakan coba lagi.");
      setIsSubmitting(false);
    }
  };

  return (
    <>
      <button 
        onClick={() => setIsOpen(true)}
        className="fixed bottom-6 right-6 z-50 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-primary-active transition-transform hover:scale-105 group"
      >
        <div className="relative">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          {totalItems > 0 && (
            <span className="absolute -top-3 -right-3 bg-red-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white">
              {totalItems}
            </span>
          )}
        </div>
      </button>

      {isOpen && (
        <div className="fixed inset-0 z-[100] flex justify-end">
          <div className="absolute inset-0 bg-ink/50 backdrop-blur-sm" onClick={() => setIsOpen(false)}></div>
          
          <div className="relative w-full max-w-md bg-canvas h-full shadow-2xl flex flex-col animate-slide-in-right">
            <div className="p-6 border-b border-hairline flex justify-between items-center bg-surface-soft">
              <h2 className="text-xl font-bold text-ink">Keranjang Belanja</h2>
              <button onClick={() => setIsOpen(false)} className="text-muted hover:text-ink p-2 rounded-full hover:bg-canvas transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
              </button>
            </div>

            <div className="flex-1 overflow-y-auto p-6 bg-canvas">
              {items.length === 0 ? (
                <div className="h-full flex flex-col items-center justify-center text-muted">
                  <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" className="mb-4 text-hairline"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                  <p>Keranjang Anda masih kosong.</p>
                  <button onClick={() => setIsOpen(false)} className="mt-4 text-primary font-medium hover:underline">Mulai Belanja</button>
                </div>
              ) : (
                <div className="space-y-6">
                  {/* Daftar Item */}
                  <div className="space-y-4 border-b border-hairline pb-6">
                    {items.map((item) => (
                      <div key={item.id} className="flex gap-4 items-start bg-surface-soft p-3 rounded-lg border border-hairline">
                        {item.image ? (
                          <img src={item.image} alt={item.name} className="w-16 h-16 object-cover rounded-md flex-shrink-0" />
                        ) : (
                          <div className="w-16 h-16 bg-canvas rounded-md flex items-center justify-center text-xs text-muted flex-shrink-0 border border-hairline">
                            <span className="uppercase font-bold text-ink/40">{item.domain}</span>
                          </div>
                        )}
                        <div className="flex-1">
                          <h4 className="font-semibold text-ink text-sm leading-tight mb-1">{item.name}</h4>
                          <p className="text-primary font-bold text-sm mb-2">Rp {item.price.toLocaleString('id-ID')}</p>
                          <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2 bg-canvas border border-hairline rounded-full px-2 py-1">
                              <button type="button" onClick={() => decreaseQuantity(item.id)} className="text-ink font-bold px-2 hover:text-primary">-</button>
                              <span className="text-sm font-semibold min-w-[1rem] text-center">{item.quantity}</span>
                              <button type="button" onClick={() => increaseQuantity(item.id)} className="text-ink font-bold px-2 hover:text-primary">+</button>
                            </div>
                            <button type="button" onClick={() => removeFromCart(item.id)} className="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>

                  {/* Form Global Checkout */}
                  <form id="checkout-form" onSubmit={handleCheckout} className="space-y-4">
                    <h3 className="font-bold text-ink mb-2">Informasi Pemesan</h3>
                    
                    <div>
                      <label className="block text-sm font-medium text-ink mb-1">Nama Lengkap</label>
                      <input required type="text" value={name} onChange={e => setName(e.target.value)} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-ink mb-1">Nomor WhatsApp</label>
                      <input required type="tel" value={phone} onChange={e => setPhone(e.target.value)} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" />
                    </div>

                    {hasTicket && (
                      <div className="bg-primary/5 p-4 rounded-lg border border-primary/20 space-y-4 mt-4">
                        <h4 className="font-bold text-sm text-primary flex items-center gap-2">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg>
                          Data Khusus Tiket E-Voucher
                        </h4>
                        <div>
                          <label className="block text-sm font-medium text-ink mb-1">Email Aktif</label>
                          <input required type="email" value={email} onChange={e => setEmail(e.target.value)} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" />
                        </div>
                        <div>
                          <label className="block text-sm font-medium text-ink mb-1">NIK KTP (16 Digit)</label>
                          <input required type="text" minLength={16} maxLength={16} pattern="[0-9]{16}" value={ktp} onChange={e => setKtp(e.target.value.replace(/\D/g, ''))} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" placeholder="16 digit angka" />
                        </div>
                      </div>
                    )}

                    {needsAddress && (
                      <div className="space-y-4 mt-4">
                         {hasFood && !hasJastip && (
                           <div>
                             <label className="block text-sm font-medium text-ink mb-2">Metode Pengiriman (Food)</label>
                             <div className="grid grid-cols-2 gap-2">
                               <button type="button" onClick={() => setShippingMethod('pickup')} className={`py-2 px-3 border rounded text-sm font-medium transition-colors ${shippingMethod === 'pickup' ? 'bg-primary border-primary text-white' : 'border-hairline text-muted hover:border-ink'}`}>Ambil di Toko</button>
                               <button type="button" onClick={() => setShippingMethod('delivery')} className={`py-2 px-3 border rounded text-sm font-medium transition-colors ${shippingMethod === 'delivery' ? 'bg-primary border-primary text-white' : 'border-hairline text-muted hover:border-ink'}`}>Kirim Alamat</button>
                             </div>
                           </div>
                         )}

                         {(shippingMethod === 'delivery' || hasJastip) && (
                           <div>
                             <label className="block text-sm font-medium text-ink mb-1">Alamat Lengkap</label>
                             <textarea required value={address} onChange={e => setAddress(e.target.value)} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" rows={3} placeholder="Alamat lengkap (Kecamatan, Kota, Kode Pos)"></textarea>
                           </div>
                         )}
                         
                         <div>
                           <label className="block text-sm font-medium text-ink mb-1">Catatan Pesanan / Request Jastip</label>
                           <textarea value={notes} onChange={e => setNotes(e.target.value)} className="w-full border border-hairline rounded p-2 focus:outline-none focus:border-primary bg-surface-soft" rows={2} placeholder="Catatan opsional, instruksi khusus, atau link referensi Jastip."></textarea>
                         </div>
                      </div>
                    )}
                  </form>
                </div>
              )}
            </div>

            {items.length > 0 && (
              <div className="p-6 border-t border-hairline bg-surface-soft">
                <div className="flex justify-between items-end mb-4">
                  <div>
                    <p className="text-sm text-muted">Total Pembayaran</p>
                    <p className="text-xs text-muted">Belum termasuk ongkir</p>
                  </div>
                  <p className="text-2xl font-bold text-ink">Rp {totalPrice.toLocaleString('id-ID')}</p>
                </div>
                <button 
                  disabled={isSubmitting}
                  type="submit" 
                  form="checkout-form"
                  className="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary-active transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                >
                  {isSubmitting ? 'Memproses Pesanan...' : 'Checkout Semua Pesanan'}
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
}
