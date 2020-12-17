<?php
namespace chainup\waas\client;

interface WaasApi
{
    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_createUser.html
     * 注册手机号用户
     * @param $country 国编码
     * @param $mobile 手机号
     * @return 注册后的用户UID
     */
    public function CreateMobileUser($country, $mobile) ;

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_registerEmail.html
     * 注册邮箱用户
     * @param $email
     * @return int 注册后的用户UID
     */
    public function CreateEmailUser($email);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_info.html
     *  获取注册的用户信息
     * @param $country
     * @param $mobile
     * @return UserInfo
     */
    public function GetUserInfoByMobile($country, $mobile);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_info.html
     * 获取注册的用户信息
     * @param $email
     * @return UserInfo
     */
    public function GetUserInfoByEmail($email);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_getCoinList.html
     *  获取支持的币种列表
     * @return Coin
     */
    public function GetCoinList();

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getByUidAndSymbol.html
     *  获取用户币种余额
     * @param $uid
     * @param $symbol
     * @return Balance
     */
    public function GetBalanceByUidAndSymbol($uid, $symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getCompanyBySymbol.html
     *  获取商户归集后的余额
     * @param $symbol
     * @return collect balnce
     */
    public function GetCollectBalanceBySymbol($symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getDepositAddress.html
     *  获取币种充值地址
     * @param $uid
     * @param $symbol
     * @return deposit address
     */
    public function GetDepositAddress($uid, $symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_withdraw.html
     *  发起提现
     * @param $requestId 商户订单唯一标识，用来区分重复提现
     * @param $fromWaasUid  CreateMobileUser 接口返回的uid
     * @param $withdrawAddress 提现地址
     * @param $withdrawAmount 提现金额
     * @param $withdrawSymbol 提现币种
     * @return waas 平台提现id
     */
    public function Withdraw($requestId, $fromWaasUid, $withdrawAddress, $withdrawAmount, $withdrawSymbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_syncWithdrawList.html
     * 同步提现记录
     * @param int $lastWaasId  waas平台提现id, 返回比last waas id 更大的100笔
     * @return withdraw list
     */
    public function SyncWithdrawList($lastWaasId = 0);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_withdrawList.html
     *  批量获取提现记录
     * @param array $requestIdList
     * @return mixed
     */
    public function WithdrawBatchList($requestIdList = array());

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_syncDepositList.html
     * 同步充值记录
     * @param int $lastWaasId
     * @return mixed
     */
    public function SyncDepositList($lastWaasId = 0);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_depositList.html
     *  批量同步充值记录
     * @param array $requestIdList
     * @return mixed
     */
    public function DepositBatchList($requestIdList = array());
}