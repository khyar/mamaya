import { PrismaClient } from '@prisma/client'
import { PrismaLibSql } from '@prisma/adapter-libsql'
import { createClient } from '@libsql/client/web'

const prismaClientSingleton = () => {
  const libsql = createClient({
    url: process.env.TURSO_DATABASE_URL || process.env.DATABASE_URL || '',
    authToken: process.env.TURSO_AUTH_TOKEN || ''
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
