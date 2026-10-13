import { z } from 'zod'

/**
 * Creates a required trimmed string Zod schema with custom error messages.
 *
 * @param {string} label - The field label used in validation error messages.
 * @returns {z.ZodString} The configured Zod string schema.
 */
export const requiredString = (label) =>
  z
    .string({
      error: (issue) => (issue.input === undefined ? `${label} is required` : `Invalid ${label}`),
    })
    .trim()
    .min(1, { error: `${label} is required` })

/**
 * Creates an optional/nullable trimmed string Zod schema with custom length constraints.
 *
 * @param {Object} options - Configuration options for the schema.
 * @param {string} options.label - The field label used in validation error messages.
 * @param {number} [options.min] - The minimum allowed string length.
 * @param {number} [options.max] - The maximum allowed string length.
 * @returns {z.ZodNullable<z.ZodEffects<z.ZodString, string | null, string>>} The configured nullable Zod schema.
 */
export const nullableString = (options) =>
  z
    .string()
    .trim()
    .transform((val) => (val === '' ? null : val))
    .nullable()
    .refine((val) => val === null || options.min === undefined || val.length >= options.min, {
      error: `${options.label} must be at least ${options.min} characters`,
    })
    .refine((val) => val === null || options.max === undefined || val.length <= options.max, {
      error: `${options.label} must not exceed ${options.max} characters`,
    })
