import { PrismaClient } from '@prisma/client'
import { PrismaLibSql } from '@prisma/adapter-libsql'
import { getCloudflareContext } from '@opennextjs/cloudflare'

export const getPrisma = async () => {
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
  
  // Dalam Cloudflare Workers, instance klien HTTP (seperti fetch yang digunakan libsql) 
  // terikat pada Request Context. Menyimpan PrismaClient di cache global akan menyebabkan 
  // error "Cannot perform I/O on behalf of a different request".
  // Oleh karena itu, kita harus meng-instansiasi PrismaClient baru di setiap pemanggilan.
  const adapter = new PrismaLibSql({
    url: url as string,
    authToken: authToken as string
  });
  
  return new PrismaClient({ adapter });
};

