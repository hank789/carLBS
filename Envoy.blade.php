@servers(['web-pro1' => 'root@47.92.88.204','web-pro2' => 'root@39.98.222.54'])

@task('pro',['on' => ['web-pro1','web-pro2']])
su - web
cd /home/web/carLBS
git pull origin master
php artisan config:cache
php artisan route:cache
php artisan opcache:clear
php artisan queue:restart
@endtask

@task('pro-m',['on' => ['web-pro1','web-pro2']])
su - web
cd /home/web/carLBS
git pull origin master
php artisan opcache:clear
@endtask