<?php
declare(strict_types=1);

namespace Loki\Logging\Response\Config;

/**
 * 业务code
 *
 * Class ApiCode
 * @package App\Mapping\Response
 */
class ResponseCode
{
    ##请求成功
    CONST SUCCESS           = 600002;
    CONST SUCCESS_WAIT      = 600001;

    ##请求错误
    CONST ERROR             = 500000;

    ##请求参数不正确
    CONST ARGUMENT_MISSING  = 400001;
    CONST NOT_FOUND         = 400004;

    ##请求不通过需要跳转
    CONST MUST_LOGIN         = 300001;

    CONST HTTP_MESSAGE = [
        self::SUCCESS               => '请求成功',
        self::SUCCESS_WAIT          => '请求失败,请稍后',
        self::ERROR                 => '服务异常',
    ];

    /**
     * 根据code获取对应的message
     *
     * @param int $httpCode
     * @return string
     * @author kert
     */
    public static function getMessage(int $httpCode): string
    {
        return self::HTTP_MESSAGE[$httpCode];
    }
}
