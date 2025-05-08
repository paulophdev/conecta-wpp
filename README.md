# Conecta-wpp

## 📋 Requisitos do Sistema

### Versões das Ferramentas
- PHP: 8.4.4
- Node.js: 22.11.0
- Laravel: 12.1.1

## 🚀 Início Rápido

### 1. Clone o Projeto
```bash
git clone https://github.com/paulophdev/conecta-wpp
```

### 2. Instalação das Dependências
```bash
# Dependências PHP
composer install

# Dependências JavaScript
npm install
```

### 3. Iniciar a Aplicação
```bash
# Iniciar servidor Laravel
composer run dev

# Iniciar servidor Reverb (comunicação em tempo real)
php artisan reverb:start --debug
```

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
php artisan config:clear
php artisan cache:clear
php artisan reverb:restart
```

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

Para ativar o monitoramento de erros com o Bugsnag, basta adicionar a chave de API no seu arquivo `.env`:

```
VITE_BUGSNAG_API_KEY=SEU_API_KEY_DO_BUGSNAG
```

Se a variável não estiver definida, o Bugsnag não será inicializado. O log de inicialização do Bugsnag só aparece no console em ambiente de desenvolvimento.