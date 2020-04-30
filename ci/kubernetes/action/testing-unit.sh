#!/usr/bin/env bash

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
source ${DIR}/../../settings.sh

set -o nounset
set -o errexit
set -o pipefail

TESTING_TYPE="unit-testing"

TESTING_COMMAND='echo \">>> Running unit tests...\"'
TESTING_COMMAND+=" && ./vendor/bin/phpunit -c ./ --log-junit /${TESTING_TYPE}/results/phpunit-results.xml"

# Validate needed values
declare -a ENV_VARS=(
    "ROOT_DIR"
    "BUILD_KEY"
    "BUILD_COMMIT"
    "KUBERNETES_CLUSTER_USER"
    "KUBERNETES_CLUSTER_HOST"
    "TESTING_TYPE"
    "TESTING_COMMAND"
    "REGISTRY_HOST"
)

for VAR in "${ENV_VARS[@]}"; do
    if [[ ! -v ${VAR} ]]; then
        echo "${VAR} must be defined."
        exit 1
    fi
done

POD_LABEL="${PROJECT_NAMESPACE}-${TESTING_TYPE}-${BUILD_KEY,,}"
POD_FILENAME="testing-unit.yml"

COMMAND="cd /var/www/app"
COMMAND+=" && git clone ${PROJECT_REPOSITORY} project"
COMMAND+=" && cd project"
COMMAND+=" && git fetch origin"
COMMAND+=" && git reset --hard ${BUILD_COMMIT}"
COMMAND+=" && cp ./ci/environments/staging/.env ./.env"
COMMAND+=" && composer install --no-progress --no-suggest --no-interaction"
COMMAND+=" && chmod -R 777 var"
COMMAND+=" && cd /var/www/app/project"
COMMAND+=" && ${TESTING_COMMAND}"

ENV_VARIABLES="POD_LABEL=\"${POD_LABEL}\""
ENV_VARIABLES+=" KUBERNETES_HOST_PATH=\"/tmp/${BUILD_KEY}\""
ENV_VARIABLES+=" TESTING_TYPE=\"${TESTING_TYPE}\""
ENV_VARIABLES+=" REGISTRY_HOST=\"${REGISTRY_HOST}\""
ENV_VARIABLES+=" COMMAND=\"${COMMAND}\""

echo ">>> Preparing Kubernetes environment..."
ssh ${KUBERNETES_CLUSTER_USER}@${KUBERNETES_CLUSTER_HOST} "cd /tmp && mkdir -p ${BUILD_KEY}" \
    && scp ${ROOT_DIR}/ci/kubernetes/pod/${POD_FILENAME} ${KUBERNETES_CLUSTER_USER}@${KUBERNETES_CLUSTER_HOST}:/tmp/${BUILD_KEY}/${POD_FILENAME} \
    && ssh ${KUBERNETES_CLUSTER_USER}@${KUBERNETES_CLUSTER_HOST} "${ENV_VARIABLES} envsubst < /tmp/${BUILD_KEY}/${POD_FILENAME} | kubectl --namespace=${PROJECT_NAMESPACE} create -f -"

# Get build host (waiting for 10 min (120 tries * 5 sec))
echo "Waiting for Pod to start..."

for ((i=0; i<120; i++)); do
    BUILD_HOST=$(kubectl --namespace=${PROJECT_NAMESPACE} get pods -l app=${POD_LABEL} -o=jsonpath='{.items[0].spec.nodeName}') && rc=$? || rc=$?
    if [ $rc -eq 0 ] && [ ${BUILD_HOST} ]; then
        echo "Pod created on: ${BUILD_HOST}"
        break
    fi
    echo "Still waiting..."
    sleep 5
done

if [ -z "${BUILD_HOST}" ]; then
    echo "Failed to determine build host for Pod \"${POD_LABEL}\"."
    exit 1
fi

# Get app container output (waiting for 5 min (60 tries * 5 sec))
OUTPUT_COMMAND="n=0; until [ \$n -ge 60 ]; do kubectl --namespace=${PROJECT_NAMESPACE} logs -f pod/${POD_LABEL} -c app 2>&1 && exit 0; let n++; sleep 5; done; exit 1"

echo ">>> Outputting Pod logs..."
ssh ${KUBERNETES_CLUSTER_USER}@${KUBERNETES_CLUSTER_HOST} "${OUTPUT_COMMAND}"

if [ $? -ne 0 ]; then
    echo "Failed to get logs of Pod \"${POD_LABEL}\"."
    exit 1
fi

# Download artifacts
echo ">>> Downloading artifacts..."
rm -rf ${ROOT_DIR}/test-reports \
    && mkdir -p ${ROOT_DIR}/test-reports/${TESTING_TYPE} \
    && scp -r \
    ${KUBERNETES_CLUSTER_USER}@${BUILD_HOST}.usgroup.loc:/tmp/${BUILD_KEY}/${TESTING_TYPE}/results/. \
    ${ROOT_DIR}/test-reports/${TESTING_TYPE}

echo -e "\n>>> Checking Pod exit code..\n"
for ((i=0; i<10; i++)); do
    pod_exit_code=$(kubectl get pods ${POD_LABEL} -n ${PROJECT_NAMESPACE} -o jsonpath="{.status.containerStatuses[?(@.name==\"app\")].state.terminated.exitCode}" --allow-missing-template-keys=false) && rc=$? || rc=$?
    if [[ $rc -eq 0 ]]; then break; fi
    sleep 1
done

[[ $rc -eq 0 ]] && [[ $pod_exit_code = "0" ]] || \
    { echo -e "TESTING FAILED\nPod \"${POD_LABEL}\" exited with non-zero code:\n $pod_exit_code\nGet Pod exit code command returned:\n $rc\n" >&2; exit 1; }

exit 0