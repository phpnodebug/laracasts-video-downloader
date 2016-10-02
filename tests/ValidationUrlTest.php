<?php

namespace Oaattia\Downloader;

use Oaattia\Downloader\Validator\UrlValidation;

class ValidationUrlTest extends \PHPUnit_Framework_TestCase
{
    use UrlValidation;

    /**
     * @test
     * @expectedException \Oaattia\Downloader\Exceptions\NotLaraCastsUrlExceptions
     */
    public function test_if_it_throw_exception_for_the_wrong_url()
    {
        $urls = [
            "https://laracasts.com/series",
            "https://laracasts.com/",
            "https://laracasts.com",
            "http://asdasd.com/",
        ];

        foreach ($urls as $url) {
            $this->validateUrl($url);
        }
    }

    /** @test */
    public function test_if_it_has_the_right_url()
    {
        $urls = [
            "https://laracasts.com/series/testing-series/",
        ];

        foreach ($urls as $url) {
            $this->assertTrue($this->validateUrl($url));
        }
    }
}
