#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# includes
source ${ROOT_DIR}/helper.sh

# vars
current_branch=$(git branch --show-current)

# args
version=$1
git_branch=$(get_arg $2 $current_branch)

# checkout
git checkout ${git_branch}

# write version
composer_file=./composer.json
write_version ${composer_file} ${version}

# push to server
git add ${composer_file}
git commit -m "Version ${version}"
git push origin

# tag version
git tag "v${version}"
git push origin "v${version}"
