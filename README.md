# Monitoring Web System

This website originaly deployed on https://niprod.net/etat is used to monitor all service used by niprod.

They display a condensed status on one page. 

An administration interface (by user/pass, access by clic on copyright on bottom page) can active/unactive maintenance mode.

Maintenance activation AND Global status is available on ./ram directory. (can connect physical trafficLight system) 

## Installation

System need : 

- Ping authorisation for php (see `socket_create(AF_INET, SOCK_RAW, 1)` php documentation)
- Php fsockopen
- Php ini
- Cron configuration
- Php7
- Apache 2

Just deploy git repository on web root folder and restart apache.

## Crontab configuration

open crontab : 

```shell
crontab -e 
```

add this line : 

```shell
*/10 * * * * php /var/www/html/refresh.php >/dev/null
```

## Website configuration

All website configuration is located on .conf.ini file 

### [SITES] 

Used to add website to monitor, as the following format : 

```ini
[sitename]@[link_click]:[link_check]
```

[sitename] is displayed on website

[link_click] is the href redirected when you click on link

[link_check] is the link checked by php

=> use different link_click/link_check when link_click caused redirection (302) , not 300 status.

### [SERVICES]

Used to add service to check, as the following format : 

```ini
[name]:[ip/hostname],[service][,service][...]
```

 [name] is displayed on website

[ip/hostname] where services is located

[service] list of service checked by php

#### Services 

- rdp
- dns
- ssh
- https
- doors
- http
- bitbucket
- jenkins
- sonarqube
- testlink
- jira
- hawkbit
- cubeOverPi
- ping

#### Php usage

Service checked function (implemented with php) is located on includes/main.php

For each service we need function with following prototype :

```php
function test_[service]($ip) { return false ; // return bool }
```

###  [EQPTS]

Used to add equipment to monitor, as following format :

```ini
[host]:[ip/hostname]
```

 [host] is displayed by website

[ip/hostname] used to perform ping and displayed time to ping.

### [TEMPS]

Used to add equipment to monitor, as following format :

```ini
[host]:[cmd]
```

[host] is displayed by website, shall be the same as one of [EQPTS] section !

[cmd] performed by php, and displayed as temperature on website.