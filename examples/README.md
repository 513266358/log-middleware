# 日志中间件使用示例

本目录包含了各种使用场景的示例代码，帮助您快速上手日志中间件的使用。

## 配置说明

1. 配置文件位置：`config/log-middleware.php`
2. 主要配置项：
   - `service`: 服务名称（从 `APP_NAME` 环境变量获取）
   - `env`: 环境名称（从 `APP_ENV` 环境变量获取）
   - `region`: 区域设置
   - `kubernetes`: Kubernetes 相关配置
   - `storage`: 日志存储配置
   - `alerts`: 告警配置

3. 环境变量配置示例：
```env
APP_NAME=my-service
APP_ENV=production
APP_REGION=us-east1
KUBERNETES_ENABLED=true
KUBERNETES_POD_NAME=my-service-7d58f4b9c-zw6bk
KUBERNETES_NAMESPACE=prod
```

## 示例列表

1. **基础使用 (basic.php)**
   - 基本配置
   - 不同日志级别
   - 简单上下文数据

2. **HTTP 请求日志 (http.php)**
   - 请求参数记录
   - 响应状态记录
   - 错误处理
   - 敏感数据脱敏

3. **Kubernetes 环境 (kubernetes.php)**
   - K8s 环境配置
   - 服务启动日志
   - 资源监控
   - 健康检查

4. **错误处理 (error.php)**
   - 数据库错误
   - 业务逻辑错误
   - 系统错误
   - 异常堆栈记录

## 运行示例

1. 首先安装依赖：
```bash
composer install
```

2. 配置环境变量：
```bash
# 复制示例环境文件
cp .env.example .env

# 编辑环境变量
vim .env
```

3. 配置 Laravel 队列：
```bash
# 在 .env 文件中配置队列连接
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

4. 启动队列处理进程：
```bash
php artisan queue:work
```

5. 运行示例：
```bash
php examples/basic.php
php examples/http.php
php examples/kubernetes.php
php examples/error.php
```

## 日志输出

所有示例的日志将通过队列处理，最终输出到 `storage/logs` 目录，按以下结构组织：

```
storage/logs/
  ├── prod/
  │   ├── my-service/
  │   └── other-service/
  └── test/
```

## 日志级别与存储周期

| 级别    | 存储周期 | 告警方式          |
|---------|----------|-------------------|
| INFO    | 7天      | 不告警            |
| WARNING | 30天     | 企业微信通知      |
| ERROR   | 永久     | PagerDuty + 短信  |

## 注意事项

1. 确保队列系统正常运行
2. 确保存储目录具有写入权限
3. 生产环境中请根据实际情况修改配置
4. 敏感信息会自动脱敏
5. 建议在开发环境中先测试日志格式
6. 确保环境变量正确配置 