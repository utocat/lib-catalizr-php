<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'TestMain.php';

/**
 * Description of investorTest
 *
 * @author codati
 * @group fundraisings
 */
class D_FundraisingsTest extends TestMain {
    /**
     *
     * @var \Catalizr\Entity\Fundraisings
     */
    static $fundraisingHaveIid;
    /**
     *
     * @var array()
     */
    static $fundraisings;
    
    public function testCreateErrorApi() {
        $fundraisingData = array(
            'name'=> 'myFundraising',
            'part_amount' => 100,
            'minimum_investment'=> 1000,
            'fee' => 1,
            'start_date' => date('c',time() +50000),
            'end_date' => date('c',time() +70000),
            'amount_total' => 10000,
            'description'=> 'test lib php',
            'bic_swift'=> 'AGRIFRPP867',
            'iban'=> 'FR1420041010050500013M02606'
        );
                
        $fundraising1 = new \Catalizr\Entity\Fundraisings($fundraisingData);
        try{
            
            $this->api->companies->createFundraisingsByCompanyId('edfedfedfedfedfedfedfedf', $fundraising1);

        } catch (\Catalizr\Lib\HttpException $ex) {
          $this->assertSame(404, $ex->getCode(),'http code');
          $this->assertSame('Company not found', $ex->getMessage());
        }
        $fundraising2 = new \Catalizr\Entity\Fundraisings();

        try{ 
            $this->api->companies->createFundraisingsByCompany(C_CompaniesTest::$companie, $fundraising2);

        } catch (\Catalizr\Lib\HttpException $ex) {
          $this->assertSame(400, $ex->getCode(),'http code');
          $this->assertSame('"name" is required', $ex->getMessage());
        }
    }
    
    public function testCreate() {
        $fundraisingData = array(
            'name'=> 'myFundraising',
            'part_amount' => 100,
            'minimum_investment'=> 1000,
            'fee' => 1,
            'start_date' => date('c',time() +50000),
            'end_date' => date('c',time() +70000),
            'amount_total' => 10000,
            'description'=> 'test lib php',
            'bic_swift'=> 'AGRIFRPP867',
            'iban'=> 'FR1420041010050500013M02606'
        );
        
        self::$fundraisings[] = new \Catalizr\Entity\Fundraisings($fundraisingData);
        self::$fundraisings[] = new \Catalizr\Entity\Fundraisings($fundraisingData);
        self::$fundraisings[] = new \Catalizr\Entity\Fundraisings($fundraisingData);
        $this->api->companies->createFundraisingsByCompany(C_CompaniesTest::$companie, self::$fundraisings[0]);

        $this->api->companies->createFundraisingsByExternalCompanyId(C_CompaniesTest::$companie->iid, self::$fundraisings[1]);
        $this->api->companies->createFundraisingsByCompanyId(C_CompaniesTest::$companie->id, self::$fundraisings[2]);
    }
    
    public function testCreateFull() {
        $fundraisingData = array(
            'name'=> 'myFundraising',
            'part_amount' => 100.5,
            'minimum_investment'=> 1000,
            'fee' => 1,
            'start_date' => date('c',time() +50000),
            'end_date' => date('c',time() +70000),
            'amount_total' => 10000,
            'description'=> 'test lib php',
            'bic_swift'=> 'AGRIFRPP867',
            'iban'=> 'FR1420041010050500013M02606',
            'funds_type' =>'CREATE',
            'iid'=> time()
        );
        $fundraising = new \Catalizr\Entity\Fundraisings($fundraisingData);

        $this->api->companies->createFundraisingsByExternalCompanyId(C_CompaniesTest::$companie->iid, $fundraising);
        return $fundraising;
    }
    
    public function testGetError() {
        try {
            $this->api->fundraisings->getById('rrrrrrrrrrrrrrrrrrrrrrrr');

        } catch (\Catalizr\Lib\HttpException $ex) {
            $this->assertSame(400, $ex->getCode(),'http code');
            $this->assertSame('"fundraising_id" must only contain hexadecimal characters', $ex->getMessage());
        }

        try {
            $this->api->fundraisings->getById('edfedfedfedfedfedfedfedf');

        } catch (\Catalizr\Lib\HttpException $ex) {
            $this->assertSame(404, $ex->getCode(),'http code');
            $this->assertSame('Fundraising not found', $ex->getMessage());
        }
    }
    
    /**
     * 
     * @depends testCreateFull
     */
    public function testGet(\Catalizr\Entity\Fundraisings $fundraising) {
        // get by id
        self::$fundraisingHaveIid = $this->api->fundraisings->getById($fundraising->id);
        // get by iid
        $fundraisingGetIid = $this->api->fundraisings->getByExternalId($fundraising->iid);
        $this->assertEquals(self::$fundraisingHaveIid,$fundraisingGetIid);

        $this->assertSame(self::$fundraisingHaveIid->name, 'myFundraising');
        $this->assertSame(self::$fundraisingHaveIid->part_amount, 100.5);
        $this->assertSame(self::$fundraisingHaveIid->minimum_investment, 1000);
        $this->assertSame(self::$fundraisingHaveIid->fee, 1);
        $this->assertSame(self::$fundraisingHaveIid->amount_total, 10000);
        $this->assertSame(self::$fundraisingHaveIid->description, 'test lib php');
        $this->assertSame(self::$fundraisingHaveIid->bic_swift, 'AGRIFRPP867');
        $this->assertSame(self::$fundraisingHaveIid->iban, 'FR1420041010050500013M02606');
        $this->assertSame(self::$fundraisingHaveIid->funds_type, 'CREATE');

        $this->assertInternalType('string',self::$fundraisingHaveIid->start_date);
        $this->assertInternalType('string',self::$fundraisingHaveIid->end_date);
        $this->assertInternalType('string',self::$fundraisingHaveIid->createdAt);
        $this->assertInternalType('string',self::$fundraisingHaveIid->updatedAt);

        
        $this->assertSame(self::$fundraisingHaveIid->id, $fundraising->id);
        $this->assertSame(self::$fundraisingHaveIid->iid, $fundraising->iid);

        // get all ids
        $ids1 = $this->api->companies->getFundraisingsIdByCompanyId(C_CompaniesTest::$companie->id);
        $ids2 = $this->api->companies->getFundraisingsIdByCompnay(C_CompaniesTest::$companie);
        $ids3 = $this->api->companies->getFundraisingsIdByExternalCompanyId(C_CompaniesTest::$companie->iid);

        $this->assertEquals($ids1,$ids2);
        $this->assertEquals($ids2,$ids3);

        $this->assertContainsOnly('string', $ids1);

        // get id by iid

        $id = $this->api->fundraisings->getIdByExternalIid($fundraising->iid);
        
        $this->assertSame($id, $fundraising->id);
    
    }
    
}