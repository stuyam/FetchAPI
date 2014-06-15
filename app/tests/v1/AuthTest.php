<?php namespace tests\v1;

use \Fetch\v1\Models\User, \Fetch\v1\Models\VerifyPhone;

class AuthTest extends \TestCase {

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

        $this->call('POST', $this->prefix.'verify-number', ['phone'=>'+15083142814', 'pin'=>'2255']);

        $this->assertResponseStatus(200);
    }

    /**
     * Test that the method returns with a success
     *
     * @return void
     */
    public function testAuthVerfiyNumberExistingUser()
    {
        User::create([
            'username'=>'stuart',
            'name'=>'Stuart Yamartino',
            'phone'=>'+15083142814',
            'country_code'=>'+1',
            'phone_hash'=>sha1('+15083142814'),
            'token'=>sha1('test')
        ]);
        VerifyPhone::create([
            'phone'=>'+15083142814',
            'country_code'=>'+1',
            'verify'=>'2255',
            'expire'=>time()+600,
            'tries'=>0
        ]);

        $this->call('POST', $this->prefix.'verify-number', ['phone'=>'+15083142814', 'pin'=>'2255']);

        $this->assertResponseStatus(200);
    }
}
