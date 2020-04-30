#!/usr/bin/env bash

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
source ${DIR}/../../settings.sh

# REGISTRY_HOST=registry-host(optional) IMAGE=php-7.0 VERSION=v4 ./build.sh

: ${REGISTRY_HOST:="srv-docker-15.usgroup.loc:5000"}

if [[ ! -v IMAGE ]]; then
    echo "IMAGE must be defined."
    exit 1
fi

TAG="${REGISTRY_HOST}/ecentria/${PROJECT_NAMESPACE}/${IMAGE}:${VERSION:=latest}"

docker build --tag ${TAG} ${IMAGE}
docker push ${TAG}
