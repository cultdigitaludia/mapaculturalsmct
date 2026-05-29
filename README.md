# Mapa Cultural de Uberlândia — SMCT
 
Plataforma oficial de mapeamento cultural da **Secretaria Municipal de Cultura e Turismo de Uberlândia (SMCT)**, baseada no [Mapas Culturais](https://github.com/mapasculturais/mapasculturais).
 
🔗 **Acesso:** [mapas.melancia.tech](https://mapas.melancia.tech)
 
---
 
## Sobre o projeto
 
Este repositório é um fork do [mapasculturais-base-project](https://github.com/mapasculturais/mapasculturais-base-project) com todas as customizações feitas para Uberlândia. A plataforma roda com Docker e toda a configuração necessária já está aqui — basta clonar, configurar o `.env` e subir os containers.
 
Para continuar o desenvolvimento, leia as seções abaixo para entender o que já foi implementado e como o projeto está estruturado.
 
---
 
## O que foi implementado
 
### Tema — `UberlandiaCultural`
 
Tema filho baseado no `BaseV2` do Mapas Culturais, localizado em `themes/UberlandiaCultural/`.
 
- Identidade visual alinhada à Prefeitura de Uberlândia (fontes e cores)
- Página inicial redesenhada com botões de acesso rápido (inspirada no [Cultura Viva](https://culturaviva.cultura.gov.br))
- **Página de Turismo** (`/turismo`) — página dedicada com mapa e filtros, exibindo apenas os espaços dos tipos turísticos (IDs 1000–1019). A rota é registrada no `Theme.php` e a view fica em `views/turismo/index.php`
- Tipos de espaços turísticos customizados adicionados ao banco (IDs 1000–1019), com taxonomia `status_turismo` (Espaços Oficiais / Espaços Cadastrados)
- **Chatbot Assistente Cultural** — widget flutuante em todas as páginas, com integração à API de eventos para exibir a agenda dos próximos 30 dias e guia de cadastro
Para ativar o tema, edite `docker/common/0.main.php`:
 
```php
'themes.active' => 'UberlandiaCultural',
```
 
### Plugin — `MultipleLocalAuth`
 
Permite login com e-mail e senha, sem necessidade de conta gov.br. Localizado em `plugins/MultipleLocalAuth/` como submódulo.
 
### Páginas padrão
 
Criadas como views do tema e integradas ao menu:
 
- **Termos de Uso**
- **Política de Privacidade** (conforme LGPD)
- **Autorização de Uso de Imagem e Voz**
### Geocodificação — CEP Aberto
 
Preenchimento automático de endereços de Uberlândia ao digitar o CEP. Configure em `docker/common/config.d/`:
 
```php
'app.cepAbertoToken' => 'seu_token_aqui',
```
 
Crie um token gratuito em [cepaberto.com](http://www.cepaberto.com).
 
### Scripts de Backup
 
Localizados em `scripts/`. Fazem backup do banco de dados e dos arquivos persistentes da aplicação.
 
| Script | O que faz |
|---|---|
| `postgres-dump.sh` | Dump diário compactado (`.sql.gz`) do banco PostgreSQL rodando no Docker |
| `backup-day.sh` | Copia o dump com o nome do dia (`DD.sql.gz`), mantendo histórico diário |
| `backup-mon.sh` | Copia o dump com o nome do mês (`YYYY-MM.sql.gz`), mantendo histórico mensal |
| `backup-files.sh` | Backup dos diretórios `private-files`, `public-files`, `logs` e do arquivo `.env` |
 
**Exemplo de crontab** (ajuste os caminhos conforme seu ambiente):
 
```cron
00 00 * * * bash /dados/mapasculturais/scripts/postgres-dump.sh /dados/backups/
00 01 * * * bash /dados/mapasculturais/scripts/backup-day.sh /dados/backups/
00 02 1 * * bash /dados/mapasculturais/scripts/backup-mon.sh /dados/backups/
00 03 * * * bash /dados/mapasculturais/scripts/backup-files.sh /dados/mapasculturais /dados/backups/
```
 
---
 
## Estrutura de arquivos
 
```
.
├── dev/                         # Ambiente de desenvolvimento local
│   ├── start.sh                 # Sobe o ambiente de dev
│   ├── bash.sh / shell.sh       # Acessa o container
│   ├── psql.sh                  # Acessa o banco de dados
│   ├── watch.sh                 # Compila assets do tema
│   └── docker-compose.local.yml
├── docker/
│   ├── common/config.d/         # Configurações compartilhadas (tema ativo, plugins, CEP Aberto etc.)
│   ├── db/                      # Dump SQL inicial
│   └── production/              # Configs exclusivas de produção (nginx, Dockerfile)
├── plugins/
│   └── MultipleLocalAuth/       # Login por e-mail e senha (submódulo Git)
├── scripts/                     # Scripts de backup
├── themes/
│   └── UberlandiaCultural/      # Tema customizado da SMCT (submódulo Git)
├── .env_sample                  # Modelo para o arquivo .env
├── docker-compose.yml           # Produção / homologação
├── docker-compose.certbot.yml   # Geração de certificado SSL
├── start.sh / stop.sh / restart.sh / update.sh
├── logs.sh                      # Exibe logs do docker-compose
├── bash.sh                      # Terminal do container Mapas
└── psql.sh                      # Console psql do banco
```
 
---
 
## Início rápido — Desenvolvimento local
 
**Pré-requisitos:** `git`, `docker`, `docker-compose`, Linux ou macOS.
 
```bash
git clone https://github.com/cultdigitaludia/mapaculturalsmct.git --recursive
cd mapaculturalsmct
cd dev/
sudo ./start.sh
# Acesse: http://localhost/
```
 
**Usuário admin padrão:**
 
| Campo | Valor |
|---|---|
| E-mail | `Admin@local` |
| Senha | `mapas123` |
 
---
 
## Deploy em produção
 
```bash
# 1. Clonar no servidor
git clone https://github.com/cultdigitaludia/mapaculturalsmct.git --recursive
cd mapaculturalsmct
 
# 2. Configurar variáveis de ambiente
cp .env_sample .env
nano .env   # preencha DB, URL, chaves etc. — nunca commite este arquivo
 
# 3. Gerar certificado SSL (edite domain e email no script antes)
sudo ./init-letsencrypt.sh
 
# 4. Subir
sudo ./start.sh
 
# 5. Para atualizar o core do Mapas Culturais
sudo ./update.sh
```
 
---
 
## Versionamento
 
O projeto segue o [Versionamento Semântico](https://semver.org/lang/pt-BR/) com [Git Flow](https://danielkummer.github.io/git-flow-cheatsheet/index.pt_BR.html):
 
- `develop` — novas funcionalidades e testes locais
- `main` — homologação
- **tags** (`1.0.0`, `1.1.0` etc.) — produção
---
 
## Licença
 
[AGPL-3.0](./LICENCE)
 
---
 
**Secretaria Municipal de Cultura e Turismo de Uberlândia — SMCT**