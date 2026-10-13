import { z } from 'zod'
import { requiredString } from './shared.schema'

export const createSubCategorySchema = z.object({
  category_id: z
    .number({ error: 'Category is required' })
    .int()
    .positive({ message: 'Category is required' }),

  name: requiredString('Name').max(100, { message: 'Name must not exceed 100 characters' }),

  price_limit: z
    .number({ error: 'Price limit is required' })
    .min(0, { message: 'Price limit cannot be negative' })
    .max(9999999999999.99, { message: 'Price limit is too large' }),
})

export const updateSubCategorySchema = z.object({
  category_id: z
    .number({ error: 'Category is required' })
    .int()
    .positive({ message: 'Category is required' }),

  name: requiredString('Name').max(100, { message: 'Name must not exceed 100 characters' }),

  price_limit: z
    .number({ error: 'Price limit is required' })
    .min(0, { message: 'Price limit cannot be negative' })
    .max(9999999999999.99, { message: 'Price limit is too large' }),
})
