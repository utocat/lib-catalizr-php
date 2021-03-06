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
 * @group investor
 */
class B_InvestorTest extends TestMain{
    /**
     *
     * @var \Catalizr\Entity\Investors
     */
    static $investor;


    public function testCreateErrorApi() {

        $investor = new Catalizr\Entity\Investors();
        try{
            $this->api->investors->create($investor);

        } catch (\Catalizr\Lib\HttpException $ex) {
            $this->assertSame(400, $ex->getCode(),'http code');
            $this->assertSame('"name" is required', $ex->getMessage());
        }
        return $investor;
    }

    public function testCreateFull() {
        $investor = new Catalizr\Entity\Investors();

        $investor->name = 'Test';
        $investor->surname = 'PHP';
        $investor->birth_date = '03/08/1990';
        $investor->birth_city = 'Hirson';
        $investor->address = 'Doge B, 4 Avenue des Saules';
        $investor->zip = '59000';
        $investor->city = 'Lille';
        $investor->country= 'France';
        $investor->title= 'Mr';
        $investor->phone= '+33674736274';
        $investor->email= 'support'.time().rand().'@catalizr.eu';
        $investor->iid= time().rand();
        $this->api->investors->create($investor);

        $this->assertInternalType('string', $investor->id);
        $this->assertInternalType('string', $investor->iid);
        self::$investor = $investor;

        return $investor;
    }

    /**
     *
     * @depends testCreateErrorApi
     */
    public function testCreate() {
        $investor = new Catalizr\Entity\Investors();

        $investor->name = 'Test';
        $investor->surname = 'PHP';
        $investor->birth_date = '03/08/1990';
        $investor->birth_city = 'Hirson';
        $investor->address = 'Doge B, 4 Avenue des Saules';
        $investor->zip = '59000';
        $investor->city = 'Lille';
        $investor->country= 'France';
        $investor->title= 'Mr';
        $investor->email= 'support'.time().rand().'@catalizr.eu';
        $this->api->investors->create($investor);

        $this->assertInternalType('string', $investor->id);
        return $investor;
    }
    public function testGetError() {
        try {
            $this->api->investors->getById('rrrrrrrrrrrrrrrrrrrrrrrr');

        } catch (\Catalizr\Lib\HttpException $ex) {
            $this->assertSame(400, $ex->getCode(),'http code');
            $this->assertSame('"investor_id" must only contain hexadecimal characters', $ex->getMessage());
        }

        try {
            $this->api->investors->getById('edfedfedfedfedfedfedfedf');

        } catch (\Catalizr\Lib\HttpException $ex) {
            $this->assertSame(404, $ex->getCode(),'http code');
            $this->assertSame('Investor edfedfedfedfedfedfedfedf not found', $ex->getMessage());
        }
    }
    /**
     *
     * @depends testCreateFull
     */
    public function testGet(\Catalizr\Entity\Investors $investor) {
        // get by id
        self::$investor = $this->api->investors->getById($investor->id);
        // get by iid
        $investorGetIid = $this->api->investors->getByExternalId($investor->iid);
        $this->assertEquals(self::$investor,$investorGetIid);

        $this->assertSame(self::$investor->name, 'Test');
        $this->assertSame(self::$investor->surname, 'PHP');
        $this->assertSame(self::$investor->birth_date, '1990-03-08T00:00:00.000Z');
        $this->assertSame(self::$investor->birth_city, 'Hirson');
        $this->assertSame(self::$investor->address, 'Doge B, 4 Avenue des Saules');
        $this->assertSame(self::$investor->zip, '59000');
        $this->assertSame(self::$investor->city, 'Lille');
        $this->assertSame(self::$investor->country, 'France');
        $this->assertSame(self::$investor->title, 'Mr');
        $this->assertSame(self::$investor->email, $investor->email);

        $this->assertInternalType('string',self::$investor->createdAt);
        $this->assertInternalType('string',self::$investor->updatedAt);

        $this->assertSame(self::$investor->id, $investor->id);
        $this->assertSame(self::$investor->iid, $investor->iid);

        // get all ids
        $ids = $this->api->investors->getAllid();

        $this->assertContainsOnly('string', $ids->items);

        // get id by iid

        $id = $this->api->investors->getIdByExternalIid($investor->iid);

        $this->assertSame($id, $investor->id);

        return self::$investor;
    }

    /**
     * @test
     * @depends testGet
     * @param \Catalizr\Entity\Investors $investors
     * @throws \Catalizr\Lib\HttpException
     */
    public function getByEmail(\Catalizr\Entity\Investors $investors)
    {
        $investorByEmail = $this->api->investors->getByEmail($investors->email);
        $this->assertEquals($investors, $investorByEmail);
    }

    /**
     * @test
     * @depends testCreateFull
     * @throws \Catalizr\Lib\HttpException
     */
    public function update()
    {
        $investor = clone self::$investor;
        unset($investor->email);

        $investor->name = 'Test UPDATED';
        $this->api->investors->update($investor);

        $investor->name = 'Test';
        $this->api->investors->update($investor);
    }
}
