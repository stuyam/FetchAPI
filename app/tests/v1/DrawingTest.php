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

}
