'use client';

import { useState } from 'react';
import { createProduct } from '@/app/actions/admin/food';
import { Plus } from 'lucide-react';

export default function AddProductForm({ batchId, masterProducts }: { batchId: string, masterProducts: any[] }) {
  const [selectedMaster, setSelectedMaster] = useState('');
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [image, setImage] = useState('');

  const handleMasterChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const val = e.target.value;
    setSelectedMaster(val);
    
    if (val) {
      const master = masterProducts.find(m => m.id === val);
      if (master) {
        setName(master.name);
        setDescription(master.description || '');
        setPrice(master.base_price.toString());
        setImage(master.image || '');
      }
    } else {
      setName('');
      setDescription('');
      setPrice('');
      setImage('');
    }
  };

  return (
    <details className="group relative">
      <summary className="btn-primary flex items-center gap-2 cursor-pointer list-none px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-active">
        <Plus className="w-4 h-4" /> Tambah Menu
      </summary>
      <div className="absolute right-0 top-full mt-2 w-96 bg-surface border border-hairline rounded-xl shadow-lg p-5 z-10">
        <h3 className="font-bold text-ink mb-3">Menu Baru dari Master</h3>
        
        <div className="mb-4">
          <label className="block text-xs font-medium text-ink mb-1 text-primary">Pilih dari Master Katalog (Opsional)</label>
          <select value={selectedMaster} onChange={handleMasterChange} className="w-full border rounded-lg p-2 text-sm bg-sky-50 border-sky-200 text-sky-800 focus:border-primary focus:outline-none">
            <option value="">-- Ketik Manual --</option>
            {masterProducts.map(m => (
              <option key={m.id} value={m.id}>{m.name} - Rp {m.base_price.toLocaleString('id-ID')}</option>
            ))}
          </select>
        </div>
        
        <form action={createProduct} className="space-y-3 border-t border-hairline pt-3">
          <input type="hidden" name="batchId" value={batchId} />
          <input type="hidden" name="image" value={image} />
          
          <div>
            <label className="block text-xs font-medium text-ink mb-1">Nama Menu</label>
            <input type="text" name="name" required value={name} onChange={e => setName(e.target.value)} className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
          </div>
          <div>
            <label className="block text-xs font-medium text-ink mb-1">Deskripsi</label>
            <textarea name="description" value={description} onChange={e => setDescription(e.target.value)} className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" rows={2}></textarea>
          </div>
          <div className="flex gap-3">
             <div className="flex-1">
               <label className="block text-xs font-medium text-ink mb-1">Harga (Rp)</label>
               <input type="number" name="price" required value={price} onChange={e => setPrice(e.target.value)} className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
             </div>
             <div className="w-24">
               <label className="block text-xs font-medium text-ink mb-1">Stok</label>
               <input type="number" name="stock" defaultValue="100" required className="w-full border border-hairline rounded-lg px-3 py-2.5 text-sm bg-surface-soft focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none shadow-sm" />
             </div>
          </div>
          <button type="submit" className="w-full bg-primary text-white p-2 rounded-lg text-sm font-medium hover:bg-primary-active">Simpan Menu ke Batch</button>
        </form>
      </div>
    </details>
  );
}
