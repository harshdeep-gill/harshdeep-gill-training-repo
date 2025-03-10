#!/bin/bash

set -euxo pipefail

# Add SSH Key
eval $(ssh-agent -s)
rm -rf /tmp/ssh_agent.sock
ssh-agent -a /tmp/ssh_agent.sock > /dev/null
echo "$PANTHEON_SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
mkdir -p ~/.ssh && touch ~/.ssh/config
grep -qxF 'StrictHostKeyChecking no' ~/.ssh/config || echo "StrictHostKeyChecking no" >> ~/.ssh/config

# Deploy to test, backup live and deploy to live -- note:  because -e is set, if any of these commands fail, the script will exit, which we ideally expect.
terminus env:deploy --no-interaction -- quark-expeditions-ms.test
terminus backup:create --element all --no-interaction -- quark-expeditions-ms.live
terminus env:deploy --no-interaction -- quark-expeditions-ms.live

set +x
