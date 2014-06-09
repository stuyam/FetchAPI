<?php

class AuthTest extends TestCase {

    protected $prefix = 'v1/auth/';

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testAuthSetNumber()
    {
        $this->call('POST', $this->prefix.'set-number', ['phone'=>'+15083142814', 'country_code'=>'+1']);

        $this->assertResponseStatus(204);
    }

    /**
     * Test that the method fails with missing parameters
     *
     * @return void
     */
    public function testAuthSetNumberFails()
    {
        $this->call('POST', $this->prefix.'set-number', ['phone'=>'+15083142814', 'country_cod'=>'+1']);

        $this->assertJsonMissingParameter();
    }

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testAuthVerfiyNumber()
    {
        VerifyPhone::create(['phone'=>'+15083142814', 'country_code'=>'+1', 'verify'=>'2255', 'expire'=>time()+600, 'tries'=>0]);

        $this->call('POST', $this->prefix.'set-number', ['phone'=>'+15083142814', 'pin'=>'1234']);

        $this->assertResponseStatus(204);
    }
}
