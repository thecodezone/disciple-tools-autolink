source $HOME/.bashrc

if ! command -v php-scoper > /dev/null 2>&1; then
  echo "PHP Scoper is not installed. Installing..."
  composer global config --no-plugins allow-plugins.wpify/scoper true
  composer global require wpify/scoper --ignore-platform-reqs
fi