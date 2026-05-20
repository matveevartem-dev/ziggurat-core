# infrastructure/front/Dockerfile
FROM node:22-alpine

WORKDIR /app

COPY package*.json ./

RUN npm cache clean --force && npm install

COPY . .

ENV HOST=0.0.0.0
ENV PORT=3000

CMD ["npm", "run", "dev"]
