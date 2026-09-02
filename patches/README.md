# Patches locais

Este diretório guarda customizações necessárias em dependências externas que não
podem ser publicadas diretamente nos repositórios oficiais.

## MultipleLocalAuth

O arquivo `MultipleLocalAuth-uberlandia.patch` adiciona ao plugin oficial:

- validação de CPF e CNPJ conforme o tipo selecionado no cadastro;
- suporte ao CNPJ numérico e alfanumérico com dígitos verificadores por módulo 11;
- normalização e verificação de documentos já cadastrados;
- correção da consulta que tratava linhas SQL como entidades e gerava warnings ao
  acessar `owner` e `status`.

O submódulo `plugins/MultipleLocalAuth` deve continuar apontando para um commit
oficial sem commits locais. O `update.sh` remove uma aplicação anterior do patch,
atualiza os submódulos e reaplica a customização antes de construir os containers.

Se uma atualização oficial alterar os mesmos trechos, o deploy será interrompido
antes da construção. Nesse caso, revise o novo `Provider.php`, atualize o patch e
teste sua aplicação e reversão antes de executar o deploy novamente.

Não inclua alterações internas do submódulo diretamente no commit do projeto
principal. O arquivo versionado é o patch deste diretório.
