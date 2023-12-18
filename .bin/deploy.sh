#!/bin/bash
set -x
set -euo pipefail

# Add SSH Key
eval $(ssh-agent -s)
ssh-agent -a /tmp/ssh_agent.sock > /dev/null
echo "$PANTHEON_SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
mkdir -p ~/.ssh && echo "StrictHostKeyChecking no" >> ~/.ssh/config

# Git Config
git config --global user.email "tools@enchantingtravels.com"
git config --global user.name "GitHub Deploy"

# Clone Pantheon Repo
git clone --single-branch --branch $PANTHEON_GIT_BRANCH $PANTHEON_GIT_REPOSITORY $HOME/pantheon-deploy --depth 1
cd $HOME/pantheon-deploy

# Copy MU Plugins
mv wp-content/mu-plugins wp-content/mu-plugins-temp
cp -r $GITHUB_WORKSPACE/wp-content/mu-plugins wp-content
mv wp-content/mu-plugins-temp/loader.php wp-content/mu-plugins
mv wp-content/mu-plugins-temp/pantheon-mu-plugin wp-content/mu-plugins
rm -rf wp-content/mu-plugins-temp

# Copy Plugins
rm -rf wp-content/plugins
cp -r $GITHUB_WORKSPACE/wp-content/plugins wp-content

# Copy Themes
rm -rf wp-content/themes
mv $GITHUB_WORKSPACE/wp-content/themes wp-content

# Copy Misc.
if test -f "wp-content/object-cache.php"; then
  rm wp-content/object-cache.php
fi
mv $GITHUB_WORKSPACE/wp-content/object-cache.php wp-content

# Blade Config
if test -f "blade.config.php"; then
  rm blade.config.php
fi
mv $GITHUB_WORKSPACE/blade.config.php .

# Copy Composer `vendor` Directory
rm -rf vendor
cp -r $GITHUB_WORKSPACE/vendor .

# Update assets version
if [ -z "${RELEASE_VERSION:-}" ]; then
    RELEASE_VERSION=$(date +%Y%m%d%H%M%S)
fi
echo "<?php return [ 'version' => '$RELEASE_VERSION' ];" > wp-content/themes/quark/dist/site-assets.php

# Check if we have changes
if [[ -z $(git status -s) ]]; then
	# No changes
	echo "No changes to push"
else
	# Push changes to Pantheon
	git add .
	git status
	git commit -m "Deploy from GitHub $GITHUB_SHA"
	git push origin $PANTHEON_GIT_BRANCH
fi

set +x
