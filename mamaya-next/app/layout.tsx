import type { Metadata } from "next";
import { Inter } from 'next/font/google';
import "./globals.css";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import CartWidget from "@/components/CartWidget";

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "Dapur Mamaya - Super App",
  description: "Dapur Mamaya - Pre-Order makanan rumahan, Tiket, dan Jastip.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id">
      <body className={`${inter.variable} bg-canvas text-body font-sans antialiased min-h-screen flex flex-col`}>
        <Navbar />
        <main className="flex-1 relative">
          {children}
        </main>
        <Footer />
        <CartWidget />
      </body>
    </html>
  );
}
