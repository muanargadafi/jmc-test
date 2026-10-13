import { z } from 'zod'
import { requiredString } from './shared.schema'

export const createCategorySchema = z.object({
  code: requiredString('Code').max(10, { message: 'Code must not exceed 10 characters' }),
  name: requiredString('Name').max(100, { message: 'Name must not exceed 100 characters' }),
})

export const updateCategorySchema = z.object({
  code: requiredString('Code').max(10, { message: 'Code must not exceed 10 characters' }),
  name: requiredString('Name').max(100, { message: 'Name must not exceed 100 characters' }),
})
