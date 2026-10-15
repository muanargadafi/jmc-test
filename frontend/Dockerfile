FROM node:24-alpine AS builder

WORKDIR /app

RUN npm install -g pnpm@12.11.2

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml .npmrc ./

RUN pnpm install --frozen-lockfile

COPY . .
RUN pnpm run build

FROM node:24-alpine

WORKDIR /app

COPY --from=builder /app/.output ./.output

EXPOSE 3000

ENV NODE_ENV=production
ENV HOST=0.0.0.0
ENV PORT=3000

CMD ["node", ".output/server/index.mjs"]
