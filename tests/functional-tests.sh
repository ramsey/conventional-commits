#!/usr/bin/env bash

stty cols 120

__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# Create arguments based on the expect filenames, so that we can have
# a nicer display of test names printed to the screen.
args=""
for f in "${__DIR__}/expect/"*.exp; do
    args+="$(basename -s .exp $f)"
    args+=$'\n'
done

# Remove the trailing newline character.
args=$(echo "${args}" | perl -pe 'chomp if eof')

# Store the arguments to a temporary data provider file for bash-test to use.
data_provider_for_test_expect="$(mktemp)"
echo "${args}" >> "${data_provider_for_test_expect}"

cd "${__DIR__}/expect" || exit 1

_test_expect() {
    "./${1}.exp" >/dev/null 2>&1
    test $? -eq 0
}
