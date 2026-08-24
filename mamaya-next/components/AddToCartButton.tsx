'use client';

import { useCartStore } from '@/store/cartStore';

interface AddToCartButtonProps {
  product: {
    id: string;
    name: string;
    price: number;
    category?: string;
    image?: string | null;
    batchId?: string | null;
  };
}

export default function AddToCartButton({ product }: AddToCartButtonProps) {
  const addToCart = useCartStore((state) => state.addToCart);

  return (
    <button 
      onClick={() => addToCart({
        id: product.id,
        domain: 'food',
        name: product.name,
        price: product.price,
        category: product.category || 'food',
        image: product.image || undefined,
        batchId: product.batchId || undefined
      })}
      className="bg-primary text-white px-4 py-2 rounded font-medium text-sm hover:bg-primary-active transition-colors"
    >
      + Keranjang
    </button>
  );
}
