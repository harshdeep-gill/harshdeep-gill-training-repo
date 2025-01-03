#!/bin/bash

set -euxo pipefail

TARGET_ENVIRONMENT=${1:-'qa'}

# Add SSH Key
eval $(ssh-agent -s)
rm -rf /tmp/ssh_agent.sock
ssh-agent -a /tmp/ssh_agent.sock > /dev/null
echo "$PANTHEON_SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
mkdir -p ~/.ssh && touch ~/.ssh/config
grep -qxF 'StrictHostKeyChecking no' ~/.ssh/config || echo "StrictHostKeyChecking no" >> ~/.ssh/config

# Clone Database from Source to Target Environment
terminus env:clone-content quark-expeditions.live $TARGET_ENVIRONMENT --db-only --yes

# Set Domain Name for Search and Replace
DOMAIN_NAME=$TARGET_ENVIRONMENT

# Set Staging Domain Name for Search and Replace
if [ $TARGET_ENVIRONMENT == 'dev' ]; then
 DOMAIN_NAME='staging'
fi

# Search and Replace environment domain
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- search-replace www.quarkexpeditions.com $DOMAIN_NAME.quarkexpeditions.com --all-tables

# Flush cache
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- cache flush

# Import Departures
set +e
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- quark-softrip sync all
set -e

# Flush cache
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- cache flush

# Repost solr schema
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- solr repost-schema

# Index solr
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- solr delete --all
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- solr index

# Flush rewrite rules
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- rewrite flush

# Final cache flush
terminus wp quark-expeditions.$TARGET_ENVIRONMENT -- cache flush

set +x
