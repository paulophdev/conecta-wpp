# Conecta-wpp

## 📋 Requisitos do Sistema

### Importante ter instalado
- Docker
- Docker Compose

## 🚀 Início Rápido com Docker

### 1. Clone o Projeto
```bash
git clone https://github.com/paulophdev/conecta-wpp
cd conecta-wpp
```

### 2. Crie o arquivo de ambiente
```bash
cp .env.example .env
```

### 3. Suba os containers
```bash
docker-compose up -d --build
```

### 4. Instale as dependências e gere os assets (dentro do container)
```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 5. Gere o build do frontend e rode as migrations
```bash
docker-compose exec app rm -rf public/build
docker-compose exec app npm run build
docker-compose exec app php artisan migrate
```

### 6. Acesse a aplicação
Abra o navegador em [http://localhost:8000](http://localhost:8000)

---

## 🔧 Configuração do WPPConnect Server

1. Clone o projeto WPPConnect Server:
```bash
git clone https://github.com/wppconnect-team/wppconnect-server
```

2. Siga as instruções de instalação do README do WPPConnect Server

3. Configure o WPPConnect Server:
   - Acesse `src/config.ts`
   - Altere o `secretKey`
   - Adicione o valor no campo `WPP_CONNECT_FIXED_KEY` do arquivo `.env` deste projeto
   - Atualize a rota do webhook para: `http://localhost:8000/api/webhook/session-status`

4. Limpe os caches:
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan reverb:restart
```

---

## 📚 Documentação da API

Para gerar a documentação da API:
1. Atualize o arquivo `openapi.yaml`
2. Execute o comando:
```bash
npx @redocly/cli build-docs openapi.yaml
```

## 🛠️ Recursos Utilizados

### Componentes
- [Radix Vue](https://www.radix-vue.com) - Biblioteca de componentes

### Ícones
- [Lucide Icons](https://lucide.dev/icons) - Biblioteca de ícones

### Inspiração UI
- [Uiverse](https://uiverse.io/elements) - Elementos com Tailwind CSS

## 📦 Produção

Para configuração do WPPConnect Server em ambiente de produção, consulte a [documentação oficial](https://wppconnect.io/pt-BR/docs/projects/wppserver/configuration).

# Configuração do Bugsnag

Para ativar o monitoramento de erros com o Bugsnag, utilize duas chaves diferentes:

## Frontend (Vue.js)
Adicione a chave de API do tipo **Browser** no seu arquivo `.env`:
```
VITE_BUGSNAG_FRONTEND_KEY=CHAVE_BROWSER_DO_BUGSNAG
```

## Backend (Laravel)
Adicione a chave de API do tipo **Server** no seu arquivo `.env`:
```
BUGSNAG_BACKEND_KEY=CHAVE_SERVER_DO_BUGSNAG
```

Se as variáveis não estiverem definidas, o Bugsnag não será inicializado. O log de inicialização do Bugsnag só aparece no console em ambiente de desenvolvimento.