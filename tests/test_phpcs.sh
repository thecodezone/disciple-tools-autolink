#!/bin/bash

set -e

cd "$(dirname "${BASH_SOURCE[0]}")/../"

vendor/bin/phpcs disciple-tools-autolink.php