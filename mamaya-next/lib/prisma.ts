import { PrismaClient } from '@prisma/client'
import { PrismaLibSql } from '@prisma/adapter-libsql'
import { createClient } from '@libsql/client/web'
import { getCloudflareContext } from '@opennextjs/cloudflare'

const getEnv = (key: string) => {
  try {
    const { env } = getCloudflareContext();
    if (env && env[key]) return env[key] as string;
  } catch (e) {}

  if (typeof process !== 'undefined') {
    const env = process['env'];
    if (env) return env[key] || '';
  }
  if (globalThis.process?.env?.[key]) {
    return globalThis.process.env[key] || '';
  }
  return '';
};

const prismaClientSingleton = () => {
  const libsql = createClient({
    url: getEnv('TURSO_DATABASE_URL') || getEnv('DATABASE_URL'),
    authToken: getEnv('TURSO_AUTH_TOKEN')
  })
  const adapter = new PrismaLibSql(libsql as any)
  return new PrismaClient({ adapter })
}

declare global {
  var prismaGlobal: undefined | ReturnType<typeof prismaClientSingleton>
}

const prisma = new Proxy({} as ReturnType<typeof prismaClientSingleton>, {
  get(target, prop) {
    if (!globalThis.prismaGlobal) {
      globalThis.prismaGlobal = prismaClientSingleton()
    }
    return (globalThis.prismaGlobal as any)[prop]
  }
})

export default prisma

if (process.env.NODE_ENV !== 'production') {
  // Global assignment is handled within the proxy for dev as well
}
