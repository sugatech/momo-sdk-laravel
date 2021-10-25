#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# includes
source ${ROOT_DIR}/helper.sh

# args
root_branch=$(get_arg $1 develop)

# branches
target_branch=$(git branch --show-current)
review_branch=review

# reset to root branch
git checkout ${review_branch}
git reset --hard ${root_branch}

# merge target to review branch
git merge --no-ff -m "Reviewing" ${target_branch}

# back to target branch
git checkout ${target_branch}
