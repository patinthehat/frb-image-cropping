#!/bin/bash

# *
# * define asset versions to use
# *
BOOTSTRAP_VERSION="3.3.7"
JQUERY_VERSION="3.2.1"
JQUERY_TYPE="min"

#======================================================================================================

BOOTSTRAP_JS_TARGET="js/bootstrap.min.js"
BOOTSTRAP_CSS_TARGET="css/bootstrap.min.css"
BOOTSTRAP_THEME_CSS_TARGET="css/bootstrap-theme.min.css"
JQUERY_TARGET="jquery-$JQUERY_VERSION.$JQUERY_TYPE.js"

THISDIR=$(realpath `dirname $0`)
ASSETS_PATH="$THISDIR/assets"
BOOTSTRAP_ROOT_DIR="$ASSETS_PATH/bootstrap"
BOOTSTRAP_PATH="$BOOTSTRAP_ROOT_DIR/$BOOTSTRAP_VERSION"
JQUERY_PATH="$ASSETS_PATH"


function status_msg() {
    local exec_result=$1
    local msg="$2"    
    if [ $exec_result -eq 0 ]; then
        echo -e "\t$msg"
    else
        echo "[error] Previous command did not finish successfully."
    fi
}

function get_url() {
    local TARGET_FILE=$1
    local TARGET_URL=$2
    local TARGET_FILE_EXT=$(echo "$TARGET_FILE" | egrep -o '\.((min|slim)\.(js|css)|(js|css))')
    local ASSET_TYPE=$(echo $TARGET_FILE_EXT | egrep -o '(js|css)$')
    local PACKAGE_NAME=$(basename "$TARGET_FILE" "$TARGET_FILE_EXT")
    PACKAGE_NAME=$(php -r "\$s = \"$PACKAGE_NAME\"; echo preg_replace(\"/-([a-zA-Z0-9\.]+)\\\$/\",\" (\$1)\",\$s).PHP_EOL;")
    local CURL_OPTS="--silent --compressed "
    
    curl $CURL_OPTS --output "$TARGET_FILE" "$TARGET_URL"
    status_msg $? "* downloaded $ASSET_TYPE asset: $PACKAGE_NAME"
}

BOOTSTRAP_THEME_CSS_URL="https://maxcdn.bootstrapcdn.com/bootstrap/$BOOTSTRAP_VERSION/$BOOTSTRAP_THEME_CSS_TARGET"
BOOTSTRAP_CSS_URL="https://maxcdn.bootstrapcdn.com/bootstrap/$BOOTSTRAP_VERSION/$BOOTSTRAP_CSS_TARGET"
BOOTSTRAP_JS_URL="https://maxcdn.bootstrapcdn.com/bootstrap/$BOOTSTRAP_VERSION/$BOOTSTRAP_JS_TARGET"
JQUERY_URL="https://code.jquery.com/$JQUERY_TARGET"

if [ ! -d "$ASSETS_PATH" ]; then
    mkdir -p "$ASSETS_PATH"
fi

if [ ! -d "$BOOTSTRAP_PATH" ] || [ ! -d "$BOOTSTRAP_PATH/js" ] || [ ! -d "$BOOTSTRAP_PATH/css" ]; then
    mkdir -p "$BOOTSTRAP_PATH" 2>&1 >/dev/null
    mkdir -p "$BOOTSTRAP_PATH/js" 2>&1 >/dev/null
    mkdir -p "$BOOTSTRAP_PATH/css" 2>&1 >/dev/null
fi

# -H \"pragma: no-cache\" -H \"dnt: 1\" -H \"accept-encoding: gzip, deflate, br\" -H \"accept-language: en-US,en;q=0.8\" -H \"upgrade-insecure-requests: 1\" 
# -H \"user-agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36\" 
# -H \"accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8\" -H \"cache-control: no-cache\" --compressed
# -H \"pragma: no-cache\" -H \"cache-control: no-cache\" -H \"dnt: 1\"'
# -H "authority: maxcdn.bootstrapcdn.com"

echo "* Retrieving project assets..."
get_url "$BOOTSTRAP_PATH/$BOOTSTRAP_THEME_CSS_TARGET" "$BOOTSTRAP_THEME_CSS_URL"
get_url "$BOOTSTRAP_PATH/$BOOTSTRAP_CSS_TARGET" "$BOOTSTRAP_CSS_URL $CURL_OPTIONS"
get_url "$BOOTSTRAP_PATH/$BOOTSTRAP_JS_TARGET" "$BOOTSTRAP_JS_URL"
get_url "$JQUERY_PATH/$JQUERY_TARGET" "$JQUERY_URL"
echo "* Finished."
