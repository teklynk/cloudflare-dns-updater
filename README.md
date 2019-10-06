# Cloudflare-DNS-Updater

# Requirements
A server running PHP.

# Install Instructions
**Clone this project to a directory on your server.**  
git clone https://github.com/teklynk/cloudflare-dns-updater.git

**Set the files to a user:group that has read/write/execute permissions.**

chown user:group cloudflare_dns_updater.php cloudflare_dns_updater.log current_ip.txt

**Set cloudflare_dns_updater.php to executable.**

chmod +x cloudflare_php_updater.php

**Set cloudflare_dns_updater.log and current_ip.txt to read/write.**  
(this may be redundant)

chmod +rw cloudflare_dns_updater.log current_ip.txt

**Create a cron job to run the script hourly, weekly, monthly...**  
This will execute cloudflare_dns_updater.php and output/write to the cloudflare_dns_updater.log  

crontab -e

@hourly /scripts/cloudflare_dns_updater/cloudflare_dns_updater.php >> /scripts/cloudflare_dns_updater/cloudflare_dns_updater.log

Sample log file output: 

```
No update needed. | 2019-10-06 12:00:01

IP updated to 555.555.555.555 for domain domain1.com | 2019-10-06 01:00:01

{"result":{"id":"5a105e8b9d40e1329780d62easample","type":"A","name":"domain1.com ","content":"555.555.555.555","proxiable":true,"proxied":true,"ttl":1,"locked":false,"zone_id":"8ad8757baa8564dc136c1e07507sample","zone_name":"domain1.com ","modified_on":"2019-10-06T01:00:01.429896Z","created_on":"2019-10-06T01:00:01.429896Z","meta":{"auto_added":false,"managed_by_apps":false,"managed_by_argo_tunnel":false}},"success":true,"errors":[],"messages":[]} | 2019-10-06 01:00:01

Email notification sent to recipient@example.com | 2019-10-06 01:00:01

```