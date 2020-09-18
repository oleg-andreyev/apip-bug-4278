#!/usr/bin/env bash

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
source ${DIR}/../../settings.sh

set -o nounset
set -o errexit
set -o pipefail

TESTING_TYPE="unit-testing"

TESTING_COMMAND='echo \" Running unit tests...\"'
TESTING_COMMAND+=" && ./vendor/bin/phpunit -c ./ --log-junit /${TESTING_TYPE}/results/phpunit-results.xml"
TESTING_COMMAND+=" && ls -la /${TESTING_TYPE}/results/ && minio_cli cp -r /${TESTING_TYPE}/results/ \"build-artifacts/build-artifacts/${BUILD_KEY}/${TESTING_TYPE}/results/\""
# Validate needed values
declare -a ENV_VARS=(
    "ROOT_DIR"
    "BUILD_KEY"
    "BUILD_COMMIT"
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

POD_LABEL="${TESTING_TYPE}-${BUILD_KEY,,}"
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

echo ">>> Preparing Kubernetes environment..."
export POD_LABEL
export REGISTRY_HOST
export PROJECT_NAME
export MINIO_PATH_TO_BINARY
export MINIO_PATH_CONFIG_DIR
export COMMAND
envsubst < "${ROOT_DIR}/ci/kubernetes/pod/${POD_FILENAME}" > manifest.yml && cat manifest.yml && kubectl create --namespace="${PROJECT_NAMESPACE}" -f manifest.yml

# Get build host (waiting for 10 min (120 tries * 5 sec))
echo "Waiting for Pod to start..."
kubectl wait --namespace="${PROJECT_NAMESPACE}" --timeout=10m --for=condition=Ready pod/"${POD_LABEL}"

echo ">>> Outputting Pod logs..."
# Get app container output (waiting for 5 min (60 tries * 5 sec))
function print_logs () {
  n=0;
  until [ $n -ge 60 ]; do
    kubectl --namespace="${PROJECT_NAMESPACE}" logs -f pod/"${POD_LABEL}" -c app 2>&1 && return;
    (( n++ ))
    sleep 5;
  done;

  echo "Failed to get logs of Pod \"${POD_LABEL}\"."
  exit 1
}

print_logs;

# Download artifacts
echo ">>> Downloading artifacts..."
rm -rf "${ROOT_DIR}/test-reports" \
    && mkdir -p "${ROOT_DIR}/test-reports/${TESTING_TYPE}" \
    && minio_cli cp -r "build-artifacts/build-artifacts/${BUILD_KEY}/${TESTING_TYPE}/results/" "${ROOT_DIR}/test-reports/${TESTING_TYPE}/" \
    && ls -la "${ROOT_DIR}/test-reports/${TESTING_TYPE}/"
# TODO: Remove this line after configuration is finished
echo ">>> Debug: Test results downloaded to ${ROOT_DIR}/test-reports/${TESTING_TYPE}"

echo -e "\n>>> Checking Pod exit code..\n"
for ((i=0; i<10; i++)); do
    pod_exit_code=$(kubectl get pods "${POD_LABEL}" -n "${PROJECT_NAMESPACE}" -o jsonpath="{.status.containerStatuses[?(@.name==\"app\")].state.terminated.exitCode}" --allow-missing-template-keys=false) && rc=$? || rc=$?
    if [[ $rc -eq 0 ]]; then break; fi
    sleep 1
done

[[ $rc -eq 0 ]] && [[ $pod_exit_code = "0" ]] || \
    { echo -e "TESTING FAILED\nPod \"${POD_LABEL}\" exited with non-zero code:\n $pod_exit_code\nGet Pod exit code command returned:\n $rc\n" >&2; exit 1; }
exit 0
