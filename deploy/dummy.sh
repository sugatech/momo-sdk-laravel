#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# create empty dummy commit to squash Merge Request
git commit --allow-empty -m "Dummy commit to squash Merge Request"
git push origin
