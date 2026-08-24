import Link from 'next/link';
import { redirect } from 'next/navigation';
import { getAdminSession } from '@/lib/auth';
import { LayoutDashboard, ShoppingBag, Utensils, Ticket, Plane, LogOut } from 'lucide-react';
import { logoutAction } from '@/app/actions/auth';

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getAdminSession();
  
  if (!session) {
    redirect('/admin/login');
  }

  const menuItems = [
    { name: 'Dasbor', href: '/admin', icon: LayoutDashboard },
    { name: 'Pesanan', href: '/admin/orders', icon: ShoppingBag },
    { name: 'Makanan', href: '/admin/food', icon: Utensils },
    { name: 'Tiket', href: '/admin/tickets', icon: Ticket },
    { name: 'Jastip', href: '/admin/jastip', icon: Plane },
  ];

  return (
    <div className="min-h-screen bg-canvas flex flex-col md:flex-row">
      {/* Sidebar */}
      <aside className="w-full md:w-64 bg-surface border-r border-hairline flex flex-col md:h-screen sticky top-0">
        <div className="p-6 border-b border-hairline flex items-center justify-between">
          <Link href="/admin" className="text-xl font-bold text-primary">
            Mamaya Admin
          </Link>
        </div>
        
        <nav className="flex-1 p-4 space-y-1 overflow-y-auto hidden md:block">
          {menuItems.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              className="flex items-center gap-3 px-4 py-3 text-ink hover:bg-sky-50 hover:text-primary rounded-xl font-medium transition-colors"
            >
              <item.icon className="w-5 h-5" />
              {item.name}
            </Link>
          ))}
        </nav>
        
        {/* Mobile Nav */}
        <nav className="md:hidden flex overflow-x-auto p-4 gap-2 border-b border-hairline">
           {menuItems.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              className="flex items-center gap-2 px-4 py-2 bg-surface-soft text-ink hover:bg-sky-50 rounded-full text-sm font-medium whitespace-nowrap"
            >
              <item.icon className="w-4 h-4" />
              {item.name}
            </Link>
          ))}
        </nav>

        <div className="p-4 border-t border-hairline hidden md:block">
          <div className="mb-4 px-4 text-sm">
             <p className="text-muted truncate">{session.email}</p>
             <p className="font-medium text-ink capitalize">{session.role}</p>
          </div>
          <form action={logoutAction}>
            <button type="submit" className="flex items-center gap-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl font-medium transition-colors">
              <LogOut className="w-5 h-5" />
              Keluar
            </button>
          </form>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto">
        <div className="p-6 lg:p-10 max-w-7xl mx-auto">
          {children}
        </div>
      </main>
    </div>
  );
}
