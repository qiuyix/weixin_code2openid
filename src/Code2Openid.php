<?php
namespace tingyu\WXCodeOpenid;

use tingyu\HttpRequest\Method\RequestGet;

class Code2Openid
{
    private $appid;
    private $secert;
    private $grandType;
    private $requestUrl = 'https://api.weixin.qq.com/sns/jscode2session';

    protected $responseData;

    public function __construct($appid, $secert, $grandType = 'authorization_code')
    {
        $this->appid = $appid;
        $this->secert = $secert;
        $this->grandType = $grandType;
        $this->responseData = null;
    }

    /**
     * js_code 换 openid
     * @param $jsCode
     * @return mixed
     * @throws \Exception
     */
    public function jsCode2Session($jsCode)
    {
        
        if (!is_string($jsCode)) {
            throw new \Exception("jscode类型不是字符串");
        }

        $jsCode = trim($jsCode);
        if (empty($jsCode)) {
            throw new \Exception("jscode内容为空");
        }

        $query = [
            'appid' => $this->appid,
            'secret' => $this->secert,
            'js_code' => $jsCode,
            'grant_type' => $this->grandType,
        ];

        try {
            $client = new RequestGet();
            $responseBody = $client->request($this->requestUrl, $query);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $this->responseData = $responseBody;

        $data = json_decode($responseBody, true);
        $jsonErr = json_last_error();
        if ($jsonErr !== 0) {
            $jsonErrMsg = json_last_error_msg();
            throw new \Exception("返回值解析错误{$jsonErrMsg}");
        }

        if (!array_key_exists('errcode', $data) && array_key_exists('openid', $data) && !empty($data['openid'])) {
            return $data['openid'];
        } elseif (array_key_exists('errmsg', $data)) {
            throw new \Exception($data['errmsg']);
        } else {
            throw new \Exception('响应值中没有获取到openid');
        }
    }
}