The Main purpose of this tool provide monitoring to web servers and services

- pinger.php is the main process that will ping the services and monitor and notify according to configuration in config.php
- config.php hold all configuration requird to run the program
- you specify some kind of receipe, like you want find some texts in some pages or return result or status code or xpath for html in the page, if those conditions failed the services start notifing
- you can sepecifiy some peoples with interest in some service to notified first
- you can shutdown the notification service in time period
- logs of monitoring is stored in sqlite database file
- report.php should give you weekly summary about the service health 