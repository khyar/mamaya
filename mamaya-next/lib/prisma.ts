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
    console.log("=== CLOUDFLARE CONTEXT ===");
    console.log("Keys in env:", ctx?.env ? Object.keys(ctx.env) : "no env object");
    url = ctx.env.TURSO_DATABASE_URL || ctx.env.DATABASE_URL || '';
    authToken = (ctx.env.TURSO_AUTH_TOKEN || '') as string;
    console.log("URL from ctx.env:", url ? "FOUND" : "NOT FOUND", "Value:", url, "Type:", typeof url);
  } catch (e) {
    console.error("Cloudflare context failed, fallback to global", e);
  }

  if (!url && typeof process !== 'undefined') {
    const processEnv = process['env'];
    console.log("Keys in process.env:", processEnv ? Object.keys(processEnv) : "no process.env");
    if (processEnv) {
      url = processEnv['TURSO_DATABASE_URL'] || processEnv['DATABASE_URL'] || '';
      authToken = processEnv['TURSO_AUTH_TOKEN'] || '';
    }
  }

  if (!url && globalThis.process?.env) {
    url = globalThis.process.env['TURSO_DATABASE_URL'] || globalThis.process.env['DATABASE_URL'] || '';
    authToken = globalThis.process.env['TURSO_AUTH_TOKEN'] || '';
  }

  console.log("createClient called with URL:", url, "Type:", typeof url);
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
