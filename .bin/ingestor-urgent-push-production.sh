#!/bin/bash
set -x
set -euo pipefail

# Add SSH Key
eval $(ssh-agent -s)
ssh-agent -a /tmp/ssh_agent.sock > /dev/null
echo "$PANTHEON_SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
mkdir -p ~/.ssh && echo "StrictHostKeyChecking no" >> ~/.ssh/config

# Run Command
terminus wp quark-expeditions-ms.live -- quark-ingestor push urgent --url=www.quarkexpeditions.com
