function get_arg {
    local value=${1}
    local default=${2}

    if [ ${value} ]; then
        echo ${value}
    else
        echo ${default}
    fi
}

function command_exists {
    type "$1" &> /dev/null ;
}

function download_source {
    local url=${1}
    local branch=${2}
    local dir=${3}
    local path=${4}

    if [ -d ${dir} ]; then
        rm -Rf ${dir};
    fi
    git clone --quiet -b ${branch} --depth=1 ${url} ${dir}${path}
}

function get_commit_id {
    local url=${1}
    local branch=${2}

    git ls-remote ${url}  | grep refs/heads/${branch} | awk '{ print $1}'
}

function pack_source {
    local filename=${1}
    local src_path=${2}

    zip ${filename} -r ${src_path} -x \*.git\* -9 -q
}

function composer_install {
    composer install --quiet --no-ansi --no-dev --no-interaction --optimize-autoloader
}

function write_version {
    local composer_file=${1}
    local version=${2}

    sedi 's/"version": [0-9a-zA-Z -_]*/"version": "'"${version}"'",/' ${composer_file}
}

function sedi {
  if [ "$(uname)" == "Linux" ]; then
    sed -i "$@"
  else
    sed -i "" "$@"
  fi
}
