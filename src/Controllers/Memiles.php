<?php

namespace Memiles\Comission\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use DB;

class Memiles
{
    
    const DROPSHIPPER = 25;
    const CUST = 1;
    const SHM = 0.5;
    const DSGETDS = 0.5;
    const STATUS_SELESAI_ID = 5;
    const REFERENCE_TABLE = 'meshop_pos_orders';
    const WALLET_DROPSHIPPER = 'mebuy_dropshipper';
    const WALLET_GOLD = 'mebuy_gold';
    const WALLET_SHM = 'mebuy_shm';
    const WALLET_DS_ROYALTY = 'mebuy_dropshipper_royalty';

    const CUSTOMER_VALUE = 'customer';
    const DROPSHIPPER_VALUE = 'dropshipper';
    const SHM_VALUE = 'shm';
    const DS_GET_DS_VALUE = 'ds_get_ds';

    protected $com_dropshipper, $com_cust, $com_shm, $com_dsgetds, $customer_id, $order_id, $shm_id, $dropshipper_id, $upline_id;

    public function __construct($dropshipper = self::DROPSHIPPER, $cust = self::CUST, $shm = self::SHM, $dsgetds = 0.5)
    {
        $this->com_dropshipper = $dropshipper;
        $this->com_cust = $cust;
        $this->com_shm = $shm;
        $this->com_dsgetds = $dsgetds;
    }

    /**
     * Amount Comission Dropshipper
     * 
     * @param Value $value ValueComissino
     * 
     * return void
     */
    public function setPercentageDropshipper($value)
    {
        $this->com_dropshipper = $value;
    }

    /**
     * Amount Comission SHM
     * 
     * @param Value $value ValueComissino
     * 
     * return void
     */
    public function setPercentageShm($value)
    {
        $this->com_shm = $value;
    }

    /**
     * Amount Comission Cust
     * 
     * @param Value $value ValueComissino
     * 
     * return void
     */
    public function setPercentageCust($value)
    {
        $this->com_cust = $value;
    }

    /**
     * Amount Comission
     * 
     * @param Value $value ValueComissino
     * 
     * return void
     */
    public function setPercentageDsGetDs($value)
    {
        $this->com_dsgetds = $value;
    }

    private function getPercentageDropshipper()
    {
        return $this->com_dropshipper/100;
    }

    private function getPercentageCust()
    {
        return $this->com_cust/100;
    }

    private function getPercentageShm()
    {
        return $this->com_shm/100;
    }

    private function getPercentageDsGetDs()
    {
        return $this->com_dsgetds/100;
    }

    private function setOrderId($value)
    {
        $this->order_id = $value;
    }

    private function setCustomerId($value)
    {
        $this->customer_id = $value;
    }

    private function setDropshipperId($value)
    {
        $this->dropshipper_id = $value;
    }

    private function setShmId($value)
    {
        $this->shm_id = $value;
    }

    private function setUplineId($value)
    {
        $this->upline_id = $value;
    }

    private function getOrderId()
    {
        return $this->order_id;
    }

    private function getCustomerId()
    {
        return $this->customer_id;
    }

    private function getDropshipperId()
    {
        return $this->dropshipper_id;
    }

    private function getShmId()
    {
        return $this->shm_id;
    }

    private function getUplineId()
    {
        return $this->upline_id;
    }

    private function getOrder()
    {
        return  DB::table('meshop_pos_orders')
            ->join('meshop_pos_order_status_logs', 'meshop_pos_orders.id', '=', 'meshop_pos_order_status_logs.meshop_pos_order_id')
            ->where('meshop_pos_orders.id', $this->getOrderId())
            ->where('meshop_pos_orders.user_id', $this->getCustomerId())
            ->where('meshop_pos_order_status_logs.order_status_id', self::STATUS_SELESAI_ID)
            ->select('meshop_pos_orders.*')
            ->first();
    }

    private function getUserIdByType($type)
    {
        if ($type == self::CUSTOMER_VALUE) {
            $user_id = $this->getCustomerId();
        } else if ($type == self::DROPSHIPPER_VALUE) {
            $user_id = $this->getDropshipperId();
        } else if ($type == self::SHM_VALUE) {
            $user_id = $this->getShmId();
        } else if ($type == self::DS_GET_DS_VALUE) {
            $user_id = $this->getUplineId();
        }
        return $user_id;
    }

    private function getWalletSlugByType($type)
    {
        if ($type == self::CUSTOMER_VALUE) {
            $wallet_id = self::WALLET_GOLD;
        } else if ($type == self::DROPSHIPPER_VALUE) {
            $wallet_id = self::WALLET_DROPSHIPPER;
        } else if ($type == self::SHM_VALUE) {
            $wallet_id = self::WALLET_SHM;
        } else if ($type == self::DS_GET_DS_VALUE) {
            $wallet_id = self::WALLET_DS_ROYALTY;
        }
        return $wallet_id;
    }

    private function getComissionByType($type)
    {
        $data = 0;
        if ($type == self::CUSTOMER_VALUE) {
            $data = $this->getPercentageCust();
        } else if ($type == self::DROPSHIPPER_VALUE) {
            $data = $this->getPercentageDropshipper();
        } else if ($type == self::SHM_VALUE) {
            $data = $this->getPercentageShm();
        } else if ($type == self::DS_GET_DS_VALUE) {
            $data = $this->getPercentageDsGetDs();
        }
        return $data;
    }

    private function getWallet($type)
    {
        
        $wallet_id = $this->getWalletSlugByType($type);
        $user_id = $this->getUserIdByType($type);
       
        $wallet = DB::table('wallets')
            ->where('slug', $wallet_id)
            ->first();
        
        $user_wallet = DB::table('user_wallets')
            ->where('wallet_id', $wallet->id)
            ->where('user_id', $user_id)
            ->first();

        if (!$user_wallet) {
            $user_wallet = DB::table('user_wallet')
                ->insertGetId(
                    [
                        'wallet_id' => $wallet->id,
                        'user_id' => $user_id,
                        'amount' => 0
                    ]
                );
            $user_wallet = DB::table('user_wallets')
                ->where('id', $user_wallet->id)
                ->first();
        }

        return $user_wallet;
    }

    private function checkLogWallet($type)
    {
        try {
            $user_wallet = $this->getWallet($type);
            $user_id = $this->getUserIdByType($type);
            $data = DB::table('user_wallet_logs')
                ->where('user_id', $user_id)
                ->where('reference_id', $this->getOrder())
                ->where('reference_table', self::REFERENCE_TABLE)
                ->where('user_wallet_id', $user_wallet->id)
                ->first();

            return $data;

        } catch (Exception $e) {
            // throw new Exception($e->getMessage());
            return null;
        }
    }

    private function isComissionInserted($type)
    {
        return $this->checkLogWallet($type) ? true : false;
    }

    private function insertComission($type, $amount)
    {
        $user_wallet = $this->getWallet($type);
        $user_id = $this->getUserIdByType($type);
        $comission = $this->getComissionByType($type);
        try {

            DB::table('user_wallet_logs')
                ->insert(
                    [
                        'user_id' => $user_id,
                        'user_wallet_id' => $user_wallet->id,
                        'amount' => $amount,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'start_date' => Carbon::now(),
                        'end_date' => Carbon::now(),
                        'type' => 'debit',
                        'before' => $user_wallet->amount,
                        'after' => $user_wallet->amount + $amount,
                        'reference_id' => $this->getOrderId(),
                        'reference_table' => self::REFERENCE_TABLE,
                        'comission' => $comission,
                        'remarks' => 'Anda mendapatkan komisi sebesar : ' . number_format($amount, 0)
                    ]
                );

            $user_wallet = DB::table('user_wallets')
                ->where('id', $user_wallet->id)
                ->update(
                    [
                        'amount' => $user_wallet->amount + $amount
                    ]
                );

            return true;

        } catch (Exception $e) {
            // throw new Exception($e->getMessage())
            return null;
        }
    }

    private function getAffiliator()
    {
        return DB::table('mebuy_affiliator')
            ->where('user_id', $this->getDropshipperId())
            ->first();

    }

    private function isEnterpreneurIsNotSHM($affiliator)
    {
        return $affiliator->user_executive_marketing_id != $affiliator->user_enterpreneur_id;
    }

    /**
     * Share Comission
     * 
     * @param MeshopPosOrderId $meshop_pos_order_id MeshopPosOrderId
     * @param UserId           $user_id             UserID
     * 
     * return void
     */
    public function shareComission($meshop_pos_order_id, $user_id)
    {
        try {
            
            $this->setOrderId($meshop_pos_order_id);
            $this->setCustomerId($user_id);

            $order = $this->getOrder();
            
            if (!$order) {
                throw new Exception('Transaksi tidak ditemukan, atau transaksi belum selesai');
            }

            $vendor = DB::table('vendor_mebuy')
                ->where('id', $order->vendor_id)
                ->first();

            if ($vendor) {
                $this->setDropshipperId($vendor->user_id);
            }

            $comission_dropshipper = $this->getPercentageDropshipper() * $order->grand_total;
            $comission_cust = $this->getPercentageCust() * $order->grand_total;
            $comission_shm = $this->getPercentageShm() * $order->grand_total;
            $comission_ds_get_ds = $this->getPercentageDsGetDs() * $order->grand_total;


            /**
             * Komisi Customer
             */
            if ($this->isComissionInserted(self::CUSTOMER_VALUE)) {
                $this->insertComission(self::CUSTOMER_VALUE, $comission_cust);
            }

            if ($this->getDropshipperId()) {
                /**
                 * Komisi Dropshipper
                 */
                if ($this->isComissionInserted(self::DROPSHIPPER_VALUE)) {
                    $this->insertComission(self::DROPSHIPPER_VALUE, $comission_dropshipper);
                }

                if ($affiliator = $this->getAffiliator()) {
                    $this->setShmId($affiliator->user_executive_marketing_id);
                    /**
                     * Komisi SHM
                     */
                    if ($this->isComissionInserted(self::SHM_VALUE)) {
                        $this->insertComission(self::SHM_VALUE, $comission_shm);
                    }

                    if ($this->isEnterpreneurIsNotSHM($affiliator)) {
                        /**
                         * Komisi Upline
                         */
                        $this->setUplineId($affiliator->user_enterpreneur_id);
                        if ($this->isComissionInserted(self::DS_GET_DS_VALUE)) {
                            $this->insertComission(self::DS_GET_DS_VALUE, $comission_ds_get_ds);
                        }
                    }
                }

            }
            

        } catch (Exception $e) {
            // throw new Exception($e->getMessage());
        }
    }

}
