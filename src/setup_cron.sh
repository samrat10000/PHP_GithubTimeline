(crontab -l 2>/dev/null; echo "*/5 * * * * php $(pwd)/cron.php") | crontab -
echo "Cron job added to run cron.php every 5 minutes."
