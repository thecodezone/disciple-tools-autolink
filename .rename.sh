find ./ -type f -exec sed -i -e 's|Disciple_Tools_Autolink|Disciple_Tools_Autolink|g' {} \;
find ./ -type f -exec sed -i -e 's|disciple_tools_autolink|disciple_tools_autolink|g' {} \;
find ./ -type f -exec sed -i -e 's|disciple-tools-autolink|disciple-tools-autolink|g' {} \;
find ./ -type f -exec sed -i -e 's|starter_post_type|starter_post_type|g' {} \;
find ./ -type f -exec sed -i -e 's|Autolink|Autolink|g' {} \;
mv disciple-tools-autolink.php disciple-tools-autolink.php
rm .rename.sh
