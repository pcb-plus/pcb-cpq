pcb-cpq
================================

## 安装

```sh
composer require pcb-plus/pcb-cpq
```

## 导入计价因子

```sh
php artisan vendor:publish --tag=cpq
php artisan import:cpq-factors {version_number} {product_code} database/imports/{filename}
```
