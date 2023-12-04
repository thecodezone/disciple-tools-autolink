find ./ -type f -print0 | xargs -0 perl -pi -e 's/DT_Plugin/DT_Plugin/g';
find ./ -type f -print0 | xargs -0 perl -pi -e 's/dt_plugin/dt_plugin/g';
find ./ -type f -print0 | xargs -0 perl -pi -e 's/dt-plugin/dt-plugin/g';
find ./ -type f -print0 | xargs -0 perl -pi -e 's/starter_post_type/starter_post_type/g';
find ./ -type f -print0 | xargs -0 perl -pi -e 's/DT Plugin/DT Plugin/g';
mv dt-plugin.php dt-plugin.php
rm .rename.sh