<?php declare(strict_types=1);

namespace App\Tests\unit\InputFilter;

use App\InputFilter\AuthorizationInputFilter;

class AuthorizationInputFilterTest extends \Codeception\Test\Unit
{
    public function testValidToken(): void
    {
        $header = [
           'token' => 'faskfjksdf933oref04934jfkfkwkfj39403jeffksdfmkdf93404frf',
           'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0',
        ];

        $validator = new AuthorizationInputFilter();
        $validator->setData($header);

        $this->assertTrue($validator->isValid());
        $this->assertEquals(
            'faskfjksdf933oref04934jfkfkwkfj39403jeffksdfmkdf93404frf',
            $validator->getValue(AuthorizationInputFilter::KEY_AUTHORIZATION)
        );
    }

    public function testEmptyToken(): void
    {
        $header = [
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0',
        ];

        $validator = new AuthorizationInputFilter();
        $validator->setData($header);

        $this->assertFalse($validator->isValid());
        $this->assertEquals([
            'token' => [ 'isEmpty' => "Value is required and can't be empty" ],
        ], $validator->getMessages());
    }

    public function testTokenIsShorterThanExpected(): void
    {
        $header = [
            'token' => 'abcdefghijklmnoqrstuv',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0',
        ];

        $validator = new AuthorizationInputFilter();
        $validator->setData($header);

        $this->assertFalse($validator->isValid());
        $this->assertEquals([
            'token' => [ 'stringLengthTooShort' => 'The input is less than 56 characters long' ],
        ], $validator->getMessages());
    }

    public function testTokenIsLongerThanExpected(): void
    {
        $header = [
            'token' => 'abcdefghijklmnoqrstuvfaskfjksdf933oref04934jfkfkwkfj39403jeffksdf',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0',
        ];

        $validator = new AuthorizationInputFilter();
        $validator->setData($header);

        $this->assertFalse($validator->isValid());
        $this->assertEquals([
            'token' => [ 'stringLengthTooLong' => 'The input is more than 56 characters long' ],
        ], $validator->getMessages());
    }
}
