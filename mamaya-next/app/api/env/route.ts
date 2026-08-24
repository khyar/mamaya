import { NextResponse } from 'next/server';
import { getCloudflareContext } from '@opennextjs/cloudflare';

export async function GET() {
  let envKeys = [];
  let errorMsg = null;
  
  try {
    const ctx = await getCloudflareContext({ async: true });
    if (ctx && ctx.env) {
      envKeys = Object.keys(ctx.env);
    }
  } catch (error: any) {
    errorMsg = error.message || String(error);
  }

  return NextResponse.json({
    message: "Environment Variables Check",
    envKeys,
    hasTursoDatabaseUrl: envKeys.includes('TURSO_DATABASE_URL'),
    hasDatabaseUrl: envKeys.includes('DATABASE_URL'),
    error: errorMsg
  });
}
