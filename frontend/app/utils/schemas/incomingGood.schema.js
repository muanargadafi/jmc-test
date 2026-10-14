import { z } from 'zod'
import { requiredString, nullableString } from './shared.schema'

const itemSchema = z.object({
  name: requiredString('Item name').max(200, { message: 'Item name cannot exceed 200 characters' }),

  price: z.number({ error: 'Price is required' }).min(0, { message: 'Price cannot be negative' }),

  quantity: z
    .number({ error: 'Quantity is required' })
    .min(0.01, { message: 'Minimum quantity is 0.01' }),

  unit: requiredString('Unit').max(40, { message: 'Unit cannot exceed 40 characters' }),

  expired_date: z.string().nullable().optional(),
})

export const createIncomingGoodSchema = z.object({
  operator_id: z
    .number({ error: 'Operator is required' })
    .int()
    .positive({ message: 'Operator is required' }),

  sub_category_id: z
    .number({ error: 'Sub category is required' })
    .int()
    .positive({ message: 'Sub category is required' }),

  received_date: requiredString('Received date'),

  origin: requiredString('Origin').max(200, { message: 'Origin cannot exceed 200 characters' }),

  letter_number: nullableString({ label: 'Letter number', max: 100 }),

  attachment: z.any().optional().nullable(),

  items: z.array(itemSchema).min(1, { message: 'At least 1 item is required' }),
})

export const updateIncomingGoodSchema = z.object({
  operator_id: z
    .number({ error: 'Operator is required' })
    .int()
    .positive({ message: 'Operator is required' }),

  sub_category_id: z
    .number({ error: 'Sub category is required' })
    .int()
    .positive({ message: 'Sub category is required' }),

  received_date: requiredString('Received date'),

  origin: requiredString('Origin').max(200, { message: 'Origin cannot exceed 200 characters' }),

  letter_number: nullableString({ label: 'Letter number', max: 100 }),

  attachment: z.any().optional().nullable(),

  remove_attachment: z.boolean().optional(),

  items: z.array(itemSchema).min(1, { message: 'At least 1 item is required' }),
})
