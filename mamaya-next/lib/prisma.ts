import { PrismaClient } from '@prisma/client'
import { PrismaLibSql } from '@prisma/adapter-libsql'
import { getCloudflareContext } from '@opennextjs/cloudflare'

let cachedPrisma: any = null;

export const getPrisma = async () => {
  if (cachedPrisma) return cachedPrisma;

  let url = '';
  let authToken = '';

  try {
    const ctx = await getCloudflareContext({ async: true });
    url = ctx.env.TURSO_DATABASE_URL || ctx.env.DATABASE_URL || '';
    authToken = (ctx.env.TURSO_AUTH_TOKEN || '') as string;
  } catch (e) {
    // Cloudflare context failed, fallback to global
  }

  if (!url && typeof process !== 'undefined') {
    const processEnv = process['env'];
    if (processEnv) {
      url = processEnv['TURSO_DATABASE_URL'] || processEnv['DATABASE_URL'] || '';
      authToken = processEnv['TURSO_AUTH_TOKEN'] || '';
    }
  }

  if (!url && globalThis.process?.env) {
    url = globalThis.process.env['TURSO_DATABASE_URL'] || globalThis.process.env['DATABASE_URL'] || '';
    authToken = globalThis.process.env['TURSO_AUTH_TOKEN'] || '';
  }

  if (!url) {
    throw new Error("Database URL is missing. Please ensure environment variables are configured.");
  }
  
  if (typeof process !== 'undefined' && process.env) {
    process.env['DATABASE_URL'] = url;
    process.env['TURSO_DATABASE_URL'] = url;
    process.env['TURSO_AUTH_TOKEN'] = authToken;
  }
  
  const adapter = new PrismaLibSql({
    url: url as string,
    authToken: authToken as string
  });
  
  cachedPrisma = new PrismaClient({ adapter });
  return cachedPrisma;
};

if (process.env.NODE_ENV !== 'production') {
  globalThis.prismaGlobal = cachedPrisma;
}
