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
terminus env:clone-content quark-expeditions-ms.live $TARGET_ENVIRONMENT --db-only --yes

# Set Domain Name for Search and Replace
DOMAIN_NAME=$TARGET_ENVIRONMENT

# Set Staging Domain Name for Search and Replace
if [ $TARGET_ENVIRONMENT == 'dev' ]; then
 DOMAIN_NAME='staging'
fi

# Search and Replace environment domain
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- search-replace www.quarkexpeditions.com $DOMAIN_NAME.quarkexpeditions.com --all-tables
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- search-replace wp.quarkexpeditions.cn $DOMAIN_NAME.quarkexpeditions.cn --all-tables

# Flush cache
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- cache flush --url=$DOMAIN_NAME.quarkexpeditions.com
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- cache flush --url=$DOMAIN_NAME.quarkexpeditions.cn

# Import Departures
set +e
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- quark-softrip sync all --url=$DOMAIN_NAME.quarkexpeditions.com
set -e

# Flush cache
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- cache flush --url=$DOMAIN_NAME.quarkexpeditions.com

# Repost solr schema
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- solr repost-schema --url=$DOMAIN_NAME.quarkexpeditions.com

# Index solr
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- solr delete --all --url=$DOMAIN_NAME.quarkexpeditions.com
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- solr index --url=$DOMAIN_NAME.quarkexpeditions.com

# Flush rewrite rules
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- rewrite flush --url=$DOMAIN_NAME.quarkexpeditions.com

# Final cache flush
terminus wp quark-expeditions-ms.$TARGET_ENVIRONMENT -- cache flush --url=$DOMAIN_NAME.quarkexpeditions.com

set +x
