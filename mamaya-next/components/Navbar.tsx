"use client";

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState } from 'react';
import { Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

export default function Navbar() {
  const pathname = usePathname();
  const [mobileMenu, setMobileMenu] = useState(false);
  const cartCount = 0; // TODO: Implement real cart logic

  const isActive = (path: string) => pathname?.startsWith(path);

  // If we're on the portal page (root), we don't show the navbar. The portal page has its own minimal header.
  if (pathname === '/') {
    return null;
  }

  return (
    <nav className="bg-canvas sticky top-0 z-50 border-b border-hairline">
      <div className="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-[80px]">
          {/* Logo */}
          <Link href="/" className="text-[24px] font-bold tracking-tight text-ink">Mamaya.</Link>

          {/* Desktop Nav */}
          <div className="hidden md:flex items-center h-full">
            <Link href="/food" className={`flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 ${isActive('/food') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline'}`}>
              Food
            </Link>
            <Link href="/tickets" className={`flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 ${isActive('/tickets') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline'}`}>
              Tickets
            </Link>
            <Link href="/jastip" className={`flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 ${isActive('/jastip') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline'}`}>
              Jastip
            </Link>
          </div>

          {/* Utilities */}
          <div className="flex items-center gap-4">
            <Link href="/track" className="text-[14px] font-medium text-ink hover:text-muted transition-colors hidden sm:block">Lacak Pesanan</Link>
            
            <button onClick={() => setMobileMenu(!mobileMenu)} className="md:hidden p-2 rounded-full border border-hairline hover:shadow-md transition-shadow">
              {mobileMenu ? <X className="w-5 h-5 text-ink" /> : <Menu className="w-5 h-5 text-ink" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Menu */}
      <AnimatePresence>
        {mobileMenu && (
          <motion.div 
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: 'auto', opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            className="md:hidden border-t border-hairline bg-canvas overflow-hidden"
          >
            <div className="px-4 py-4 space-y-2">
              <Link href="/food" onClick={() => setMobileMenu(false)} className="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Food</Link>
              <Link href="/tickets" onClick={() => setMobileMenu(false)} className="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Tickets</Link>
              <Link href="/jastip" onClick={() => setMobileMenu(false)} className="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Jastip</Link>
              <hr className="border-hairline my-2" />
              <Link href="/track" onClick={() => setMobileMenu(false)} className="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Lacak Pesanan</Link>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
}
