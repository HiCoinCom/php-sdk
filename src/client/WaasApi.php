<?php
namespace chainup\waas\client;

interface WaasApi
{
    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/get-address/create-user
     * register mobile number
     * @param $country country code
     * @param $mobile Phone number
     * @return User UID after registration
     */
    public function CreateMobileUser($country, $mobile) ;

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/get-address/register-email
     * Register account by  Email
     * @param $email
     * @return int User UID after registration
     */
    public function CreateEmailUser($email);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/account-assets/user-info
     *  Get registered user information
     * @param $country
     * @param $mobile
     * @return UserInfo
     */
    public function GetUserInfoByMobile($country, $mobile);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/account-assets/user-info
     * Get registered user information
     * @param $email
     * @return UserInfo
     */
    public function GetUserInfoByEmail($email);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/account-assets/coin-list
     *  obtain list of supported coins
     * @return Coin
     */
    public function GetCoinList();

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/account-assets/get-balance
     *  obtain user account balance by coins
     * @param $uid
     * @param $symbol
     * @return Balance
     */
    public function GetBalanceByUidAndSymbol($uid, $symbol);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/account-assets/get-account
     *  obtain merchants account balance after assets consolidation
     * @param $symbol
     * @return collect balnce
     */
    public function GetCollectBalanceBySymbol($symbol);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/get-address/get-deposit-address
     *  obtain coin deposit address
     * @param $uid
     * @param $symbol
     * @return deposit address
     */
    public function GetDepositAddress($uid, $symbol);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/user-withdraw/withdraw
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
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/user-withdraw/withdraw-sync-list
     * Sync withdrawal record
     * @param int $lastWaasId  waas Platform withdrawal id,Returns 100 more than last waas id
     * @return withdraw list
     */
    public function SyncWithdrawList($lastWaasId = 0);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/user-withdraw/withdraw-list
     *  Batch query withdrawal records
     * @param array $requestIdList
     * @return mixed
     */
    public function WithdrawBatchList($requestIdList = array());

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/user-deposit/deposit-sync-list
     * Sync deposit record
     * @param int $lastWaasId
     * @return mixed
     */
    public function SyncDepositList($lastWaasId = 0);

    /**
     * https://custodydocs-en.chainup.com/api-references/custody-apis/apis/user-deposit/deposit-list
     *  Batch query deposit records
     * @param array $requestIdList
     * @return mixed
     */
    public function DepositBatchList($requestIdList = array());
}