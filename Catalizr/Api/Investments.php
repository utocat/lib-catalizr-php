<?php

namespace Catalizr\Api;

/**
 * Description of ApiInvestment
 *
 * @author codati
 */
class Investments extends \Catalizr\Lib\Api{
    /**
     *
     * @var string
     */
    static $classEntity =  "\Catalizr\Entity\Investments";
    /**
     *
     * @var string
     */
    static $prefixTag =  'investments';

    /**
     * 
     * @param string $id id of catalizr
     * @return \Catalizr\Entity\Investments Investment get
     */
    public function getById($id) {
        $investment = parent::getById( self::$prefixTag, self::$classEntity, $id);
        $investment->fundraising_id = $investment->fundraising;
        $investment->investor_id = $investment->investor;
        $investment->fundraising = null;
        $investment->investor = null;
        return $investment;
    }
//    
//   /**
//    * 
//    * @return string[]
//    */
//    public function getAllid() {
//        return parent::getAll(self::$prefixTag, self::$classEntity);
//    }
    
    /**
     * 
     * @param string|int|double $iid external id
     * @return \Catalizr\Entity\Investments Investment get
     */
    public function getByExternalId($iid) {
        return parent::getByExternalId($iid);
    }
    /**
     * 
     * @param string $prefixTag
     * @param string|int|double $iid external iid
     * @return string
     */
    public function getIdByExternalIid($iid) {

        return parent::getIdByExternalIid( self::$prefixTag, $iid);
    }
    
    /**
     * 
     * @param \Catalizr\Entity\Investments $investment investment for save
     * @return void
     */
    public function create(\Catalizr\Entity\Investments &$investment) {
        if(!isset($investment->fundraising_id))
        {
            if(isset($investment->fundraising))
            {
                $investment->fundraising_id = $investment->fundraising->id;
            }else if(isset($investment->fundraising_external_id))
            {
                $investment->fundraising_id = $this->api->fundraisings->getIdByExternalIid($investment->fundraising_external_id);
            }else{
                throw new \Exception('fundraising or fundraising_id or fundraising_external_id is not set in investment');
            }
        }
        
        if(!isset($investment->investor_id))
        {
            if(isset($investment->investor))
            {
                $investment->investor_id = $investment->investor->id;
            }else if(isset($investment->investor_external_id))
            {
                $investment->investor_id = $this->api->investors->getIdByExternalIid($investment->investor_external_id);
            }else{
                throw new \Exception('investor or investor_id or investor_external_id is not set in investment');
            }
        }
       return parent::create(self::$prefixTag, $investment);
    }
    
        /**
     * 
     * @param string $id
     * @param \Catalizr\Entity\Documents $document
     */
    public function createDocumentByInvestmentId($id, \Catalizr\Entity\Documents &$document){
        $this->createDocumentById(self::$prefixTag,$id, $document);
    }
    /**
     * 
     * @param \Catalizr\Entity\Companies $Investment
     * @param \Catalizr\Entity\Documents $document
     */
    public function createDocumentByInvestment(\Catalizr\Entity\Investments $Investment ,\Catalizr\Entity\Documents &$document){
        $this->createDocumentById(self::$prefixTag,$Investment->id, $document);

    }
    /**
     * 
     * @param string|int|double $iid
     * @param \Catalizr\Entity\Documents $document
     */
    public function createDocumentByExternalInvestmentId($iid ,\Catalizr\Entity\Documents &$document){
        $id=$this->getIdByExternalIid( $iid);
        
        $this->createDocumentById(self::$prefixTag,$id,$document);
    }
    
        /**
     * 
     * @param string $id id of catalizr
     * @return void
     */
    public function finishByIdInvestment($id) {
        return $this->api->helperRequest->executeReq(self::$prefixTag.'_finish', null,[$id]);
    }
    /**
     * 
     * @param \Catalizr\Entity\Investments
     * @return void
     */
    public function finishByInvestment($investment) {
        return $this->finishByIdInvestment($investment->id);
    }
    /**
     * 
     * @param string|int|double $iid external id
     * @return void
     */
    public function finishByExternalInvestmentId($iid) {
        $id = $this->getIdByExternalIid($iid);
        return $this->finishByIdInvestment($id);

    }
    
}
