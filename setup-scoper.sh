if ! command -v php-scoper > /dev/null 2>&1; then
  echo "PHP Scoper is not installed. Installing..."
  composer global config --no-plugins allow-plugins.wpify/scoper true
  composer global require wpify/scoper
fi

if ! command -v php-scoper > /dev/null 2>&1; then
  echo "The php-scoper command can not be found. Please add the composer global bin directory to your PATH."
  echo "You can do this by adding the following line to your ~/.bashrc or ~/.bash_profile:"
  echo "export PATH=\$(composer global config bin-dir --absolute --quiet):\$PATH"
  exit 1
else
  echo "PHP Scoper is installed."
fi