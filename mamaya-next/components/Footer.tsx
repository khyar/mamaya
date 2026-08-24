import Link from 'next/link';

export default function Footer() {
  return (
    <footer className="bg-surface-soft border-t border-hairline mt-auto">
      <div className="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px]">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
          <div>
            <h3 className="text-ink font-medium text-[16px] mb-4">Layanan Mamaya</h3>
            <ul className="space-y-3 text-[14px]">
              <li><Link href="/food" className="text-muted hover:text-ink transition-colors">Food & Catering</Link></li>
              <li><Link href="/tickets" className="text-muted hover:text-ink transition-colors">Event Tickets</Link></li>
              <li><Link href="/jastip" className="text-muted hover:text-ink transition-colors">Jasa Titip (Jastip)</Link></li>
            </ul>
          </div>
          <div>
            <h3 className="text-ink font-medium text-[16px] mb-4">Dukungan</h3>
            <ul className="space-y-3 text-[14px]">
              <li><Link href="/track" className="text-muted hover:text-ink transition-colors">Lacak Pesanan</Link></li>
              <li><Link href="#" className="text-muted hover:text-ink transition-colors">Cara Pemesanan</Link></li>
              <li><Link href="#" className="text-muted hover:text-ink transition-colors">Pusat Bantuan</Link></li>
            </ul>
          </div>
          <div>
            <h3 className="text-ink font-medium text-[16px] mb-4">Hubungi Kami</h3>
            <ul className="space-y-3 text-[14px]">
              <li><span className="text-muted">WhatsApp: 6281234567890</span></li>
              <li><span className="text-muted">Instagram: @dapurmamaya</span></li>
              <li><span className="text-muted">Email: hello@mamaya.id</span></li>
            </ul>
          </div>
        </div>
      </div>
      
      <div className="border-t border-hairline">
        <div className="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="text-[14px] text-muted font-medium">
            &copy; {new Date().getFullYear()} Dapur Mamaya. Hak Cipta Dilindungi.
          </div>
          <div className="flex gap-4 text-[14px] text-muted font-medium">
            <Link href="#" className="hover:text-ink">Privasi</Link>
            <span>·</span>
            <Link href="#" className="hover:text-ink">Syarat & Ketentuan</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
