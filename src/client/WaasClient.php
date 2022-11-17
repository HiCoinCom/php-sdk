<?php
namespace chainup\waas\client;


use chainup\waas\client\model\Result;
use chainup\waas\crypto\RsaUtils;
use GuzzleHttp;


defined("HTTP_POST") or define("HTTP_POST", "POST");
defined("HTTP_GET") or define("HTTP_GET", "GET");

class WaasClient implements WaasApi
{
    private  $config;

    public function __construct(Config $config){
        $config->setWaasPublicKey( RsaUtils::FormatterPublicKey($config->getWaasPublicKey()) );
        $config->setUserPrivateKey( RsaUtils::FormatterPrivateKey($config->getUserPrivateKey()) );
        $this->config = $config;
    }

    private function doRequest($path, $method, $params = array()){
        $params["time"] = time();
        $params["charset"] = $this->config->getCharset();
        $params["version"] = $this->config->getVersion();

        $data = RsaUtils::Encrypt(json_encode($params, JSON_UNESCAPED_UNICODE), $this->config->getUserPrivateKey());
        $form = array(
            "app_id"=>$this->config->getAppid(),
            "data"=>$data
        );

        $request = array();
        if(HTTP_POST == $method){
            $request["form_params"] = $form;
        }else {
            $request["query"] = $form;
        }

        $request["headers"] = array(
            "Content-Type"=>"application/x-www-form-urlencoded",
            'User-Agent' => 'rest php client/1.0',
            'Accept'     => 'application/json',
            'timeout'  => 60,
        );
        $url = $this->config->getDomain(). $path;

        $client = new GuzzleHttp\Client();
        $response = $client->request($method, $url, $request);

        $code = $response->getStatusCode(); // 200
        $reason = $response->getReasonPhrase(); // OK
        $body = $response->getBody()->getContents();

        $result = new Result();
        if($code != 200){
            $result->setCode(-1);
            $result->setMsg("http status {$code}, {$reason}");
            $result->setData($body);
            return $result;
        }

        if(!$body){
            $result->setCode(-2);
            $result->setMsg("json decode waas response false, raw body:{$body}");
            return $result;
        }

        $data = json_decode($body, true);
        if(!$data || !$data["data"]){
            $result->setCode(-3);
            $result->setMsg("json decode waas response false, raw body:{$body}");
            return $result;
        }

        $decrypt = RsaUtils::Decrypt($data["data"], $this->config->getWaasPublicKey());
        if(!$decrypt){
            $result->setCode(-4);
            $result->setMsg("rsa decode waas response false, raw body:{$data['data']}");
            return $result;
        }

        $decryptJson = json_decode($decrypt, true);
        $result->setCode($decryptJson["code"]);
        if(isset($decryptJson['msg'])){
            $result->setMsg($decryptJson['msg']);
        }

        if(isset($decryptJson['data'])) {
            $result->setData($decryptJson['data']);
        }
        return $result;
    }

    /**
     * register mobile number
     * @param $country country code
     * @param $mobile Phone number
     * @return User UID after registration
     */
    public function CreateMobileUser($country, $mobile) {
        $params = array(
            'country'=>$country,
            'mobile'=>$mobile,
        );
        return $this->doRequest(Config::URL_REGISTER_MOBILE_USER, HTTP_POST, $params);
    }

    /**
     * Register account by  Email
     * @param $email
     * @return int User UID after registration
     */
    public function CreateEmailUser($email){
        $params = array(
            'email'=>$email,
        );
        return $this->doRequest(Config::URL_REGISTER_EMAIL_USER, HTTP_POST, $params);
    }

    /**
     *  Get registered user information
     * @param $country
     * @param $mobile
     * @return UserInfo
     */
    public function GetUserInfoByMobile($country, $mobile){
        $params = array(
            'country'=>$country,
            'mobile'=>$mobile,
        );
        return $this->doRequest(Config::URL_USER_INFO, HTTP_GET, $params);
    }

    /**
     * Get registered user information
     * @param $email
     * @return UserInfo
     */
    public function GetUserInfoByEmail($email){
        $params = array(
            'email'=>$email,
        );
        return $this->doRequest(Config::URL_USER_INFO, HTTP_GET, $params);
    }

    /**
     *  obtain list of supported coins
     * @return Coin
     */
    public function GetCoinList(){
        return $this->doRequest(Config::URL_GET_COIN_LIST, HTTP_GET);
    }

    /**
     *  obtain user account balance by coins
     * @param $uid
     * @param $symbol
     * @return Balance
     */
    public function GetBalanceByUidAndSymbol($uid, $symbol){
        $params = array(
            'uid'=>$uid,
            'symbol'=>$symbol
        );
        return $this->doRequest(Config::URL_GET_USER_BALANCE, HTTP_GET, $params);
    }

    /**
     *  obtain merchants account balance after assets consolidation
     * @param $symbol
     * @return collect balnce
     */
    public function GetCollectBalanceBySymbol($symbol){
        $params = array(
            'symbol'=>$symbol
        );
        return $this->doRequest(Config::URL_GET_COMPANY_BALANCE, HTTP_GET, $params);
    }

    /**
     *  obtain coin deposit address
     * @param $uid
     * @param $symbol
     * @return deposit address
     */
    public function GetDepositAddress($uid, $symbol){
        $params = array(
            'symbol'=>$symbol,
            'uid'=>$uid,
        );
        return $this->doRequest(Config::URL_GET_DEPOSIT_ADDRESS, HTTP_POST, $params);
    }

    /**
     *  Initiate a withdrawal
     * @param $requestId Merchant order unique identifier, used to distinguish repeated withdrawals
     * @param $fromWaasUid  CreateMobileUser The uid returned by the interface
     * @param $withdrawAddress Withdrawal address
     * @param $withdrawAmount Withdrawal Amount
     * @param $withdrawSymbol Withdrawal coin
     * @return waas Platform withdrawal id
     */
    public function Withdraw($requestId, $fromWaasUid, $withdrawAddress, $withdrawAmount, $withdrawSymbol){
        $params = array(
            'symbol'=>$withdrawSymbol,
            'request_id'=>$requestId,
            'from_uid'=>$fromWaasUid,
            'to_address'=>$withdrawAddress,
            'amount'=>$withdrawAmount,
        );
        return $this->doRequest(Config::URL_WITHDRAW, HTTP_POST, $params);
    }

    /** Sync withdrawal record
     * @param int $lastWaasId  waas Platform withdrawal id, return 100 larger than last waas id
     * @return withdraw list
     */
    public function SyncWithdrawList($lastWaasId = 0){
        $params = array(
            'max_id'=>$lastWaasId,
        );
        return $this->doRequest(Config::URL_SYNC_WITHDRAW_LIST, HTTP_GET, $params);
    }

    /**
     *  Batch query withdrawal records
     * @param array $requestIdList
     * @return mixed
     */
    public function WithdrawBatchList($requestIdList = array()){
        if(empty($requestIdList)){
            $result = new Result();
            $result->setCode("-2");
            $result->setMsg("params can not be empty, stop send request");
            return $result;
        }

        $params = array(
            'ids'=>join(",", $requestIdList),
        );
        return $this->doRequest(Config::URL_WITHDRAW_LIST, HTTP_GET, $params);
    }

    /**
     * Sync deposit record
     * @param int $lastWaasId
     * @return mixed
     */
    public function SyncDepositList($lastWaasId = 0){
        $params = array(
            'max_id'=>$lastWaasId,
        );
        return $this->doRequest(Config::URL_SYNC_DEPOSI_TLIST, HTTP_GET, $params);
    }

    /**
     *  Batch query deposit recordsrecords
     * @param array $requestIdList
     * @return mixed
     */
    public function DepositBatchList($requestIdList = array()){
        if(empty($requestIdList)){
            $result = new Result();
            $result->setCode("-2");
            $result->setMsg("params can not be empty, stop send request");
            return $result;
        }

        $params = array(
            'ids'=>join(",", $requestIdList),
        );
        return $this->doRequest(Config::URL_DEPOSI_TLIST, HTTP_GET, $params);
    }
}