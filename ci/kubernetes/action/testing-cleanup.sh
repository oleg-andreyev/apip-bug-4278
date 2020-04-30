#!/usr/bin/env bash

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
source ${DIR}/../../settings.sh

# Need to call this actions to guarantee that Kubernetes cluster will stay clean
# All pods should be destroyed after step is done.
#
# Usually this action is called on Bamboo as "Final tasks"
# which are called after the main tasks have been finished despite the
# result similar to collecting artifacts.

set -o nounset
set -o errexit

echo ">>> Cleaning up Kubernetes cluster..."

# Validate needed values
declare -a ENV_VARS=(
    "BUILD_KEY"
    "KUBERNETES_CLUSTER_USER"
    "KUBERNETES_CLUSTER_HOST"
    "TESTING_TYPE"
)

for VAR in "${ENV_VARS[@]}"; do
    if [[ ! -v ${VAR} ]]; then
        echo "${VAR} must be defined."
        exit 1
    fi
done

POD_LABEL="${PROJECT_NAMESPACE}-${TESTING_TYPE}-${BUILD_KEY,,}"

# Cleanup
ssh ${KUBERNETES_CLUSTER_USER}@${KUBERNETES_CLUSTER_HOST} "kubectl --namespace=${PROJECT_NAMESPACE} delete pod ${POD_LABEL} --force --grace-period=0 2>&1 || true"

exit $?
