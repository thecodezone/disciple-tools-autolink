cd "$(dirname "${BASH_SOURCE[0]}")/../"

NAME="DT Autolink"
NAMESPACE="DT\\Autolink"
NAMESPACE_ESCAPED="DT\\\\Autolink"
PACKAGE="dt\/autolink"
FILENAME="dt-autolink.php"
SNAKE_CASE="dt_autolink"
UPPER_CAMEL_CASE="DT_Autolink"
KEBAB_CASE="dt-autolink"

# Exclude specified directories
EXCLUDE_DIRS="-path ./vendor -o -path ./vendor-scoped -o -path ./node_modules -o -path ./.git -o -path ./.idea"

PLACEHOLDER="dt_plugins"

# Copy ../.env.example as .env unless it exists
if [ -f ".env.example" ] && [ ! -f ".env" ]; then
  cp .env.example .env
fi

# Replace strings in files excluding specified directories
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT Autolink/$NAME/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt\/plugin/$PACKAGE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT\\Plugin/$NAMESPACE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT\\\\Plugin/$NAMESPACE_ESCAPED/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/DT_Autolink/$UPPER_CAMEL_CASE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt_plugins/$PLACEHOLDER/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt_autolink/$SNAKE_CASE/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/$PLACEHOLDER/dt_plugins/g"
find ./ \( $EXCLUDE_DIRS \) -prune -o -type f -print0 | xargs -0 perl -pi -e "s/dt-autolink/$KEBAB_CASE/g"

#mv dt-autolink.php $FILENAME
#rm .github/local.yml
#rm Writerside
#rm bin/setup.sh


