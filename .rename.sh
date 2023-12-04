NAME="DT Plugin"
NAMESPACE="DT\Plugin"
FILENAME="dt-plugin.php"
SNAKE_CASE="dt_plugin"
UPPER_CAMEL_CASE="DT_Plugin"
KEBAB_CASE="dt-plugin"

find ./ -type f -print0 | xargs -0 perl -pi -e "s/DT Plugin/$NAME/g";
find ./ -type f -print0 | xargs -0 perl -pi -e "s/DT\Plugin/$NAMESPACE/g";
find ./ -type f -print0 | xargs -0 perl -pi -e "s/DT_Plugin/$UPPER_CAMEL_CASE/g";
find ./ -type f -print0 | xargs -0 perl -pi -e "s/dt_plugin/$SNAKE_CASE/g";
find ./ -type f -print0 | xargs -0 perl -pi -e "s/dt-plugin/$KEBAB_CASE/g";
mv dt-plugin.php $FILENAME
rm .rename.sh