import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { jwtVerify } from 'jose';

const secretKey = process.env.SESSION_SECRET || 'fallback-secret-mamaya-2026';
const key = new TextEncoder().encode(secretKey);

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;
  
  if (pathname.startsWith('/admin')) {
    const session = request.cookies.get('admin_session')?.value;
    
    // If trying to access protected route without session
    if (!pathname.startsWith('/admin/login') && !session) {
      return NextResponse.redirect(new URL('/admin/login', request.url));
    }
    
    // If session exists, verify it
    if (session) {
      try {
        await jwtVerify(session, key, { algorithms: ['HS256'] });
        // If visiting login page but already logged in, redirect to dashboard
        if (pathname.startsWith('/admin/login')) {
          return NextResponse.redirect(new URL('/admin', request.url));
        }
        return NextResponse.next();
      } catch (error) {
        // Invalid session, let them proceed if they are on login page, otherwise redirect
        if (!pathname.startsWith('/admin/login')) {
          return NextResponse.redirect(new URL('/admin/login', request.url));
        }
      }
    }
  }
  
  return NextResponse.next();
}

export const config = {
  matcher: ['/admin/:path*'],
};
