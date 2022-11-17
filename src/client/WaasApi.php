<?php
namespace chainup\waas\client;

interface WaasApi
{
    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_createUser.html
     * register mobile number
     * @param $country country code
     * @param $mobile Phone number
     * @return User UID after registration
     */
    public function CreateMobileUser($country, $mobile) ;

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_registerEmail.html
     * Register account by  Email
     * @param $email
     * @return int User UID after registration
     */
    public function CreateEmailUser($email);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_info.html
     *  Get registered user information
     * @param $country
     * @param $mobile
     * @return UserInfo
     */
    public function GetUserInfoByMobile($country, $mobile);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_info.html
     * Get registered user information
     * @param $email
     * @return UserInfo
     */
    public function GetUserInfoByEmail($email);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/user_getCoinList.html
     *  obtain list of supported coins
     * @return Coin
     */
    public function GetCoinList();

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getByUidAndSymbol.html
     *  obtain user account balance by coins
     * @param $uid
     * @param $symbol
     * @return Balance
     */
    public function GetBalanceByUidAndSymbol($uid, $symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getCompanyBySymbol.html
     *  obtain merchants account balance after assets consolidation
     * @param $symbol
     * @return collect balnce
     */
    public function GetCollectBalanceBySymbol($symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/account_getDepositAddress.html
     *  obtain coin deposit address
     * @param $uid
     * @param $symbol
     * @return deposit address
     */
    public function GetDepositAddress($uid, $symbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_withdraw.html
     *  Initiate a withdrawal
     * @param $requestId Merchant order unique identifier, used to distinguish repeated withdrawals
     * @param $fromWaasUid  CreateMobileUser The uid returned by the interface
     * @param $withdrawAddress Withdrawal address
     * @param $withdrawAmount Withdrawal Amount
     * @param $withdrawSymbol Withdrawal coin
     * @return waas Platform withdrawal id
     */
    public function Withdraw($requestId, $fromWaasUid, $withdrawAddress, $withdrawAmount, $withdrawSymbol);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_syncWithdrawList.html
     * Sync withdrawal record
     * @param int $lastWaasId  waas Platform withdrawal id,Returns 100 more than last waas id
     * @return withdraw list
     */
    public function SyncWithdrawList($lastWaasId = 0);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_withdrawList.html
     *  Batch query withdrawal records
     * @param array $requestIdList
     * @return mixed
     */
    public function WithdrawBatchList($requestIdList = array());

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_syncDepositList.html
     * Sync deposit record
     * @param int $lastWaasId
     * @return mixed
     */
    public function SyncDepositList($lastWaasId = 0);

    /**
     * http://docs.hicoin.vip/zh/latest/API-WaaS-V2/api/billing_depositList.html
     *  Batch query deposit records
     * @param array $requestIdList
     * @return mixed
     */
    public function DepositBatchList($requestIdList = array());
}