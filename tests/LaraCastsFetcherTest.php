<?php
namespace Oaattia\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Mockery as m;

class LaraCastsFetcherTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @test
     */
    public function test_it_get_page_title_and_lessons_and_urls()
    {
        //        $url         = 'https://laracasts.com/series/test-series';
//        $client      = m::mock(Client::class);
//        $resolver    = m::mock(Resolver::class);
//        $laraFetcher = m::mock(LaraCastsFetcher::class)->makePartial();
//        $laraFetcher->shouldReceive('validateUrl')->once()->with($url)->andReturn($laraFetcher);
////        $laraFetcher->shouldReceive('login')->once()->andReturn(true);
//
//        $client->shouldReceive('get')->once()->with($url, ['cookies' => m::mock(CookieJar::class)])->andReturn('bar');
//
//        $fetcher = new LaraCastsFetcher($client, $resolver);
//        $fetcher->getPageTitleAndLessonsAndUrls($url);
    }
}
