##安装
1. cp .env.example .env
2. 创建数据库,修改.env文件
3. 执行`composer install`
3. 执行`php artisan key:generate`
4. 执行`php artisan migrate`
5. 执行`php artisan db:seed`
6. 执行`php artisan jwt:secret`
7. 执行`php artisan storage:link`
