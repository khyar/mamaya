'use client';

import { useState } from 'react';
import { useCartStore } from '@/store/cartStore';

export default function JastipRequestForm({ trip }: { trip: any }) {
  const [requestNote, setRequestNote] = useState('');
  
  // Selected Catalogs state: id -> quantity
  const [selectedCatalogs, setSelectedCatalogs] = useState<Record<string, number>>({});
  const addToCart = useCartStore((state) => state.addToCart);

  const toggleCatalog = (id: string) => {
    setSelectedCatalogs(prev => {
      const next = { ...prev };
      if (next[id]) delete next[id];
      else next[id] = 1;
      return next;
    });
  };

  const updateQuantity = (id: string, qty: number) => {
    if (qty < 1) return;
    setSelectedCatalogs(prev => ({ ...prev, [id]: qty }));
  };

  const handleAddToCart = () => {
    const hasCatalogs = Object.keys(selectedCatalogs).length > 0;
    
    if (!hasCatalogs && !requestNote) {
       alert("Harap pilih barang dari katalog atau tuliskan request khusus.");
       return;
    }
    
    // Add Catalogs to Cart
    Object.entries(selectedCatalogs).forEach(([catalogId, quantity]) => {
       const cat = trip.catalogs.find((c: any) => c.id === catalogId);
       if (cat) {
          addToCart({
             id: cat.id,
             domain: 'jastip',
             name: cat.name,
             price: cat.estimated_price || 0,
             image: cat.image || undefined,
             category: 'jastip',
             jastipTripId: trip.id,
             quantity: quantity
          });
       }
    });

    // Add Custom Request to Cart
    if (requestNote) {
       addToCart({
          id: `custom-${Date.now()}`,
          domain: 'jastip',
          name: "Jastip Request (Menunggu Harga)",
          price: 0,
          category: 'jastip',
          jastipTripId: trip.id,
          isCustomRequest: true,
          quantity: 1
       });
       // Save request note in localStorage so it can be retrieved by CartWidget
       const existingNotes = localStorage.getItem('mamaya_jastip_notes') || '';
       localStorage.setItem('mamaya_jastip_notes', existingNotes + (existingNotes ? '\n\n' : '') + requestNote);
    }

    alert("Item Jastip berhasil ditambahkan ke keranjang!");
    setSelectedCatalogs({});
    setRequestNote('');
  };

  return (
    <div className="bg-surface-soft p-6 rounded-xl border border-hairline">
       {trip.catalogs && trip.catalogs.length > 0 && (
         <div className="mb-8">
           <h3 className="font-bold text-ink mb-4 text-xl">Katalog Barang (Referensi)</h3>
           <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
             {trip.catalogs.map((cat: any) => {
                const isSelected = !!selectedCatalogs[cat.id];
                return (
                  <div key={cat.id} className={`group bg-surface rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border cursor-pointer flex flex-col ${isSelected ? 'border-primary ring-2 ring-primary/20 bg-sky-50/30' : 'border-hairline hover:border-primary/50'}`} onClick={() => !isSelected && toggleCatalog(cat.id)}>
                    {cat.image ? (
                       <div className="w-full h-[220px] bg-canvas relative overflow-hidden">
                         <img src={cat.image} alt={cat.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                       </div>
                    ) : (
                       <div className="w-full h-[220px] bg-canvas flex items-center justify-center text-muted">
                         <span className="text-sm">Tidak ada gambar</span>
                       </div>
                    )}
                    
                    <div className="p-5 flex flex-col flex-1">
                      <h4 className="font-bold text-lg text-ink mb-1">{cat.name}</h4>
                      <p className="text-sm font-semibold text-primary mb-4">Rp {cat.estimated_price?.toLocaleString('id-ID')}</p>
                      
                      {isSelected ? (
                        <div className="mt-auto flex items-center justify-between border-t border-hairline pt-4">
                          <div className="flex items-center gap-3 bg-canvas border border-hairline rounded-lg px-2 py-1 shadow-inner" onClick={e => e.stopPropagation()}>
                            <button type="button" onClick={() => updateQuantity(cat.id, selectedCatalogs[cat.id] - 1)} className="text-ink font-bold w-6 h-6 flex items-center justify-center hover:text-primary hover:bg-surface rounded">-</button>
                            <span className="text-sm font-bold min-w-[1.5rem] text-center">{selectedCatalogs[cat.id]}</span>
                            <button type="button" onClick={() => updateQuantity(cat.id, selectedCatalogs[cat.id] + 1)} className="text-ink font-bold w-6 h-6 flex items-center justify-center hover:text-primary hover:bg-surface rounded">+</button>
                          </div>
                          <button type="button" onClick={(e) => { e.stopPropagation(); toggleCatalog(cat.id); }} className="text-red-500 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors">Batal</button>
                        </div>
                      ) : (
                        <div className="mt-auto pt-4 flex">
                          <div className="w-full text-center bg-primary/10 text-primary py-2.5 rounded-xl font-semibold text-sm group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                            + Tambah Titipan
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                );
             })}
           </div>
         </div>
       )}

       <h3 className="text-lg font-bold mb-4 border-t border-hairline pt-6">Barang Request Khusus</h3>
       <p className="text-sm text-muted mb-4">Jika barang yang dicari tidak ada di katalog, tuliskan detail barang (warna, ukuran) atau sertakan link referensi barang di bawah ini.</p>
       
       <div className="space-y-4">
         <div>
            <textarea 
               value={requestNote}
               onChange={e => setRequestNote(e.target.value)}
               className="w-full border border-hairline rounded p-3 bg-canvas focus:outline-none focus:border-primary" 
               rows={4} 
               placeholder="Contoh: Titip Sepatu Nike Air Max seri 90 ukuran 42 warna putih. Link: https://..."
            ></textarea>
         </div>
       </div>

       <button onClick={handleAddToCart} className="mt-6 bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-active w-full">
          Tambah ke Keranjang
       </button>
    </div>
  );
}
