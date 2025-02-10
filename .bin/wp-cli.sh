#!/bin/bash

set -euxo pipefail

# Ensure at least two arguments are provided
if [[ $# -lt 2 ]]; then
  echo "Error: Missing required arguments."
  echo "Usage: $0 <environment> <command> [additional parameters]"
  exit 1
fi

SITE_NAME=$1
COMMAND=$2

# Remove the first two arguments.
shift 2 

# Collect additional params.
ADDITIONAL_PARAMS=("$@")

# Function to validate arguments against malicious input
validate_args() {
    local safe_regex='^[a-zA-Z0-9/_=.-]+$' # Only allow safe characters
    local blocked_words='(rm|eval|exec|system|passthru|shell_exec|php_exec|python_exec|wget|curl|>|<|`|\$\(.*\))' # Block dangerous commands
    echo "Validating arguments: $@"

    for arg in "$@"; do
        if [[ ! "$arg" =~ $safe_regex ]]; then
            echo "Error: Argument '$arg' contains unsafe characters!"
            exit 1
        fi
        
        if [[ "$arg" =~ $blocked_words ]]; then
            echo "Error: Argument '$arg' contains blocked commands!"
            exit 1
        fi
    done
}

# Validate additional parameters
validate_args "${ADDITIONAL_PARAMS[@]}"

# Site to environment map.
declare -A site_to_env_map=(
    ["qa"]="qa"
    ["staging"]="dev"
    ["live"]="live"
)

# Validate the site name
if [[ -z "${site_to_env_map[$SITE_NAME]:-}" ]]; then
  echo "Invalid site name: $SITE_NAME"
  exit 1
fi

# Command map.
declare -A command_to_wp_cli_map=(
    ["cache_get"]="cache get"
    ["cache_delete"]="cache delete"
    ["cache_flush"]="cache flush"
    ["cache_flush_group"]="cache flush-group"
    ["cache_edge_warmup"]="quark-cache edge flush-and-warm"
    ["cron_event_list"]="cron event list"
    ["cron_event_run"]="cron event run"
    ["softrip_sync_all"]="quark-softrip sync all"
    ["softrip_sync_items"]="quark-softrip sync items"
    ["ingestor_push_all"]="quark-ingestor push all"
    ["ingestor_push_items"]="quark-ingestor push items"
    ["ingestor_push_urgent"]="quark-ingestor push urgent"
    ["solr_index"]="solr index"
    ["solr_info"]="solr info"
    ["solr_stats"]="solr stats"
    ["meta_get"]="post meta get"
    ["meta_list"]="post meta list"
)

# Validate the command
if [[ -z "${command_to_wp_cli_map[$COMMAND]:-}" ]]; then
  echo "Invalid command: $COMMAND"
  exit 1
fi

# Add SSH Key
eval $(ssh-agent -s)
rm -rf /tmp/ssh_agent.sock
ssh-agent -a /tmp/ssh_agent.sock > /dev/null
echo "$PANTHEON_SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
mkdir -p ~/.ssh && touch ~/.ssh/config
grep -qxF 'StrictHostKeyChecking no' ~/.ssh/config || echo "StrictHostKeyChecking no" >> ~/.ssh/config

# Determine the target environment
TARGET_ENVIRONMENT=${site_to_env_map[$SITE_NAME]}
WP_CLI_PREFIX=${command_to_wp_cli_map[$COMMAND]}

# Construct the WP-CLI command
WP_CLI_COMMAND="terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- $WP_CLI_PREFIX ${ADDITIONAL_PARAMS[@]}"

# Debugging output
echo "Executing: $WP_CLI_COMMAND"

# Execute the WP-CLI command
eval "$WP_CLI_COMMAND"

set +x
