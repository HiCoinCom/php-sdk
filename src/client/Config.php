<?php


namespace chainup\waas\client;


class Config
{

    private $appid = "";
    private $domain = "https://openapi.chainup.com/api/v2";
    private $charset = "utf-8";
    private $version = "v2";
    //User-created private key
    private $userPrivateKey = "" ;
    //chainup waas provided public key
    private $waasPublicKey = "" ;

    const URL_REGISTER_MOBILE_USER            = "/user/createUser";
    const URL_REGISTER_EMAIL_USER               = "/user/registerEmail";
    const URL_USER_INFO                                     = "/user/info";
    const URL_GET_COIN_LIST                              = "/user/getCoinList";
    const URL_GET_USER_BALANCE                    = "/account/getByUidAndSymbol";
    const URL_GET_COMPANY_BALANCE          = "/account/getCompanyBySymbol";
    const URL_GET_DEPOSIT_ADDRESS              = "/account/getDepositAddress";
    const URL_WITHDRAW                                   = "/billing/withdraw";
    const URL_SYNC_WITHDRAW_LIST              = "/billing/syncWithdrawList";
    const URL_WITHDRAW_LIST                          = "/billing/withdrawList";
    const URL_SYNC_DEPOSI_TLIST                     = "/billing/syncDepositList";
    const URL_DEPOSI_TLIST                                 = "/billing/depositList";

    /**
     * @return string
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * @param string $appid
     */
    public function setAppid($appid)
    {
        $this->appid = $appid;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getUserPrivateKey()
    {
        return $this->userPrivateKey;
    }

    /**
     * @param string $userPrivateKey
     */
    public function setUserPrivateKey($userPrivateKey)
    {
        $this->userPrivateKey = $userPrivateKey;
    }

    /**
     * @return string
     */
    public function getWaasPublicKey()
    {
        return $this->waasPublicKey;
    }

    /**
     * @param string $waasPublicKey
     */
    public function setWaasPublicKey($waasPublicKey)
    {
        $this->waasPublicKey = $waasPublicKey;
    }


}
