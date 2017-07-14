#!/bin/bash

BOOTSTRAP_VERSION="3.3.7"
JQUERY_VERSION="3.2.1"
JQUERY_TYPE=".slim"

THISDIR=$(realpath `dirname $0`)
ASSETS_PATH="$THISDIR/assets"
BOOTSTRAP_ROOT_DIR="$ASSETS_PATH/bootstrap"


JS_URL="https://maxcdn.bootstrapcdn.com/bootstrap/${BOOTSTRAP_VERSION}/js/bootstrap.min.js"
CSS2_URL="https://maxcdn.bootstrapcdn.com/bootstrap/${BOOTSTRAP_VERSION}/js/bootstrap.min.css"
CSS1_URL="https://maxcdn.bootstrapcdn.com/bootstrap/${BOOTSTRAP_VERSION}/css/bootstrap-theme.min.css"
JQUERY_URL="https://code.jquery.com/jquery-${JQUERY_VERSION}.${JQUERY_TYPE}.min.js"

if [ ! -d "$BOOTSTRAP_ROOT_DIR/$BOOTSTRAP_VERSION" ]; then
  mkdir -p "$BOOTSTRAP_ROOT_DIR/$BOOTSTRAP_VERSION"
fi

wget "$JS_URL" -O "$BOOTSTRAP_ROOT_DIR/${BOOTSTRAP_VERSION}/bootstrap.min.js"
wget "$CSS1_URL" -O "$BOOTSTRAP_ROOT_DIR/${BOOTSTRAP_VERSION}/bootstrap.min.css"
wget "$CSS2_URL" -O "$BOOTSTRAP_ROOT_DIR/${BOOTSTRAP_VERSION}/bootstrap-theme.min.css"
wget "$JQUERY_URL" -O "$ASSETS_PATH/jquery-${JQUERY_VERSION}${JQUERY_TYPE}.min.css"
