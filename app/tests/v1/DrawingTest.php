<?php namespace tests\v1;

use \Fetch\v1\Models\User, \Fetch\v1\Models\VerifyPhone;

class DrawingTest extends \TestCase {

    protected $prefix = 'v1/drawing/';

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testDrawingCreate()
    {
        $this->call('POST', $this->prefix.'create', ['userid'=>'1', 'to_phone_hash'=>sha1('foo'), 'drawing'=> 'blaaaalbaaalbaaaa']);

        $this->assertResponseStatus(200);
    }

    /**
     * Test that the method returns with a failure
     *
     * @return void
     */
    public function testDrawingCreateMissingParameters()
    {
        $this->call('POST', $this->prefix.'create', ['userid'=>'1']);

        $this->assertResponseStatus(460);
    }

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testDrawingMissingPhones()
    {
        User::create([
            'username'=>'stuart',
            'name'=>'Stuart Yamartino',
            'phone'=>'+15083142814',
            'country_code'=>'+1',
            'phone_hash'=>sha1('+15083142814'),
            'token'=>sha1('test')
        ]);

        $this->call('POST', $this->prefix.'missing-phones', ['userid'=>'1', 'missing_phones'=> '[{"name": "stuart", "phone":+15083142814"},{"name": "christian", "phone":+15082127555"}']);

        $this->assertResponseStatus(204);
    }

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testDrawingLinkable()
    {

        $response = $this->call('POST', $this->prefix.'create-linkable', ['userid'=>'1', 'drawing'=> 'blaaalblaaatest']);

        $this->assertResponseStatus(200);

        $this->assertTrue($this->isJson($response->getContent()));
    }

}
