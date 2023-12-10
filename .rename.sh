NAME="DT Plugin"
NAMESPACE="DT\\Plugin"
PACKAGE="dt\/plugin"
NAMESPACE_ESCAPED="DT\\\\Plugin"
FILENAME="dt-plugin.php"
SNAKE_CASE="dt_plugin"
UPPER_CAMEL_CASE="DT_Plugin"
KEBAB_CASE="dt-plugin"

# Exclude specified directories
EXCLUDE_DIRS="-path ./vendor -o -path ./node_modules -o -path ./.git -o -path ./.idea"

# Replace strings in files excluding specified directories
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT Plugin/$NAME/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt\/plugin/$PACKAGE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT\\Plugin/$NAMESPACE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT\\\\Plugin/$NAMESPACE_ESCAPED/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT_Plugin/$UPPER_CAMEL_CASE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt_plugin/$SNAKE_CASE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt-plugin/$KEBAB_CASE/g"

mv dt-plugin.php $FILENAME
rm .rename.sh
