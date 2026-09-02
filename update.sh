#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
AUTH_SUBMODULE="plugins/MultipleLocalAuth"
AUTH_PATCH="${PROJECT_DIR}/patches/MultipleLocalAuth-uberlandia.patch"

cd "${PROJECT_DIR}"

if [[ ! -f "${AUTH_PATCH}" ]]; then
    echo "Erro: patch de autenticação não encontrado: ${AUTH_PATCH}" >&2
    exit 1
fi

# O deploy anterior deixa o patch aplicado no submódulo. Remove-o antes de
# atualizar para que o Git consiga trocar o commit oficial com segurança.
if git -C "${AUTH_SUBMODULE}" apply --reverse --check "${AUTH_PATCH}" >/dev/null 2>&1; then
    echo "Removendo customizações locais do MultipleLocalAuth..."
    git -C "${AUTH_SUBMODULE}" apply --reverse "${AUTH_PATCH}"
elif ! git -C "${AUTH_SUBMODULE}" apply --check "${AUTH_PATCH}" >/dev/null 2>&1; then
    echo "Erro: o MultipleLocalAuth contém alterações inesperadas." >&2
    echo "O deploy foi interrompido antes da atualização." >&2
    exit 1
fi

if [[ -n "$(git -C "${AUTH_SUBMODULE}" status --porcelain)" ]]; then
    echo "Erro: o MultipleLocalAuth não ficou limpo após remover o patch." >&2
    echo "O deploy foi interrompido para preservar as alterações existentes." >&2
    exit 1
fi

git pull --recurse-submodules
git submodule update --init --recursive

if git -C "${AUTH_SUBMODULE}" apply --check "${AUTH_PATCH}"; then
    echo "Aplicando customizações de autenticação de Uberlândia..."
    git -C "${AUTH_SUBMODULE}" apply "${AUTH_PATCH}"
elif git -C "${AUTH_SUBMODULE}" apply --reverse --check "${AUTH_PATCH}" >/dev/null 2>&1; then
    echo "Customizações de autenticação já estão aplicadas."
else
    echo "Erro: o patch de autenticação é incompatível com a versão atual do MultipleLocalAuth." >&2
    echo "O deploy foi interrompido antes da construção dos containers." >&2
    exit 1
fi

docker compose build --no-cache --pull

./stop.sh
./start.sh

# Em produção, temas e plugins são incorporados à imagem pelo Dockerfile.
# Nenhum processo do container deve reescrever os arquivos versionados do host.
if ! git diff --quiet --ignore-submodules=all; then
    echo "Erro: o deploy alterou arquivos versionados no servidor." >&2
    echo "Confira o resultado de: git status --short" >&2
    exit 1
fi

echo "Deploy concluído sem alterações nos arquivos versionados do host."
