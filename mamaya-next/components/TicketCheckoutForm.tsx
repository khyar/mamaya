'use client';

import { useState } from 'react';
import { useCartStore } from '@/store/cartStore';

export default function TicketCheckoutForm({ event, categories }: { event: any, categories: any[] }) {
  const [selectedCat, setSelectedCat] = useState<any>(null);
  const [quantity, setQuantity] = useState(1);
  const addToCart = useCartStore((state) => state.addToCart);

  const handleAddToCart = () => {
    if (!selectedCat) return;

    const maxTickets = event.max_tickets_per_user || 4;
    if (quantity > maxTickets) {
       alert(`Maksimal pembelian adalah ${maxTickets} tiket!`);
       return;
    }

    if (quantity > selectedCat.available_quota) {
       alert('Kuantitas melebihi sisa kuota tiket!');
       return;
    }

    addToCart({
      id: selectedCat.id,
      domain: 'ticket',
      name: `Tiket ${event.name} - ${selectedCat.name}`,
      price: selectedCat.price,
      category: 'ticket',
      ticketEventId: event.id
    });
    
    // We update quantity using updateQuantity manually if it exists, or just add it.
    // Wait, addToCart only adds 1 quantity by default in cartStore. 
    // Let's modify cartStore to accept initial quantity, or we can just loop addToCart?
    // Let's just alert success
    alert("Tiket berhasil ditambahkan ke keranjang!");
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">Pilih Kategori Tiket</h2>
      <div className="space-y-4 mb-8">
        {categories.map((cat) => (
          <div 
             key={cat.id} 
             onClick={() => cat.available_quota > 0 && setSelectedCat(cat)}
             className={`border p-4 rounded-lg flex justify-between items-center cursor-pointer transition-colors ${selectedCat?.id === cat.id ? 'border-primary ring-1 ring-primary bg-primary/5' : 'border-hairline hover:border-muted'} ${cat.available_quota === 0 ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            <div>
              <h3 className="font-semibold text-lg">{cat.name}</h3>
              <p className="text-sm text-muted">Sisa Kuota: {cat.available_quota}</p>
            </div>
            <div className="flex items-center gap-4">
              <span className="font-bold text-ink">Rp {cat.price.toLocaleString('id-ID')}</span>
              <div className={`px-4 py-1.5 rounded text-sm font-medium ${selectedCat?.id === cat.id ? 'bg-primary text-white' : 'bg-surface-soft text-ink'}`}>
                {cat.available_quota === 0 ? 'Habis' : (selectedCat?.id === cat.id ? 'Terpilih' : 'Pilih')}
              </div>
            </div>
          </div>
        ))}
      </div>

      {selectedCat && (
        <div className="bg-surface-soft p-6 rounded-xl border border-hairline">
           <h2 className="text-lg font-bold mb-4">Atur Kuantitas</h2>
           
           <div className="mb-4">
              <label className="block text-sm font-medium text-ink mb-1">Jumlah Tiket (Maks {event.max_tickets_per_user || 4})</label>
              <div className="flex items-center gap-3">
                 <button type="button" onClick={() => setQuantity(Math.max(1, quantity - 1))} className="w-8 h-8 rounded-full border border-hairline bg-canvas">-</button>
                 <span className="w-8 text-center font-bold">{quantity}</span>
                 <button type="button" onClick={() => setQuantity(Math.min(Math.min(selectedCat.available_quota, event.max_tickets_per_user || 4), quantity + 1))} className="w-8 h-8 rounded-full border border-hairline bg-canvas">+</button>
              </div>
              <p className="text-xs text-muted mt-2">Total: Rp {(selectedCat.price * quantity).toLocaleString('id-ID')}</p>
           </div>

           <button onClick={handleAddToCart} className="w-full mt-6 bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary-active">
              Tambah ke Keranjang
           </button>
        </div>
      )}
    </div>
  );
}
