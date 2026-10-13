import { z } from 'zod'
import { requiredString } from './shared.schema'

/**
 * Zod validation schema for user login request bodies.
 *
 * Validates:
 * - username: A required trimmed string converted to lowercase.
 * - password: A required trimmed string with a maximum length of 64 characters.
 */
export const loginBodySchema = z.object({
  username: requiredString('Username').toLowerCase(),
  password: requiredString('Password').max(64, {
    error: 'Password must not exceed 64 characters',
  }),
})
