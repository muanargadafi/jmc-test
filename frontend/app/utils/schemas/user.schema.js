import { z } from 'zod'
import { requiredString } from './shared.schema'

const strongPassword = (label) =>
  z
    .string()
    .min(8, { message: `${label} must be at least 8 characters` })
    .regex(/[A-Z]/, { message: `${label} must contain at least one uppercase letter` })
    .regex(/[a-z]/, { message: `${label} must contain at least one lowercase letter` })
    .regex(/[0-9]/, { message: `${label} must contain at least one number` })
    .regex(/[^A-Za-z0-9]/, { message: `${label} must contain at least one special character` })

export const createUserSchema = z.object({
  role_id: z.number({ error: 'Role is required' }).int().positive({ message: 'Role is required' }),

  username: requiredString('Username').max(100, {
    message: 'Username must not exceed 100 characters',
  }),

  password: strongPassword('Password'),

  nama: requiredString('Name')
    .min(3, { message: 'Name must be at least 3 characters' })
    .max(100, { message: 'Name must not exceed 100 characters' }),

  email: requiredString('Email')
    .email({ message: 'Invalid email format' })
    .min(3, { message: 'Email must be at least 3 characters' })
    .max(255, { message: 'Email must not exceed 255 characters' }),
})

export const updateUserSchema = z.object({
  role_id: z.number({ error: 'Role is required' }).int().positive({ message: 'Role is required' }),

  username: requiredString('Username').max(100, {
    message: 'Username must not exceed 100 characters',
  }),

  // nullable — empty = unchanged
  password: z
    .union([z.literal(''), strongPassword('Password')])
    .optional()
    .nullable(),

  nama: requiredString('Name')
    .min(3, { message: 'Name must be at least 3 characters' })
    .max(100, { message: 'Name must not exceed 100 characters' }),

  email: requiredString('Email')
    .email({ message: 'Invalid email format' })
    .min(3, { message: 'Email must be at least 3 characters' })
    .max(255, { message: 'Email must not exceed 255 characters' }),
})
