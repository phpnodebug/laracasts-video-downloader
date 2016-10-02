<?php

namespace Oaattia\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use League\Flysystem\Filesystem;
use Oaattia\Downloader\Exceptions\FailedLoginExceptions;
use Oaattia\Downloader\Exceptions\InvalidUrlExceptions;
use Oaattia\Downloader\Exceptions\NotLaraCastsUrlExceptions;
use Oaattia\Downloader\Validator\UrlValidation;
use Symfony\Component\Console\Helper\ProgressBar;

class LaraCastsFetcher
{
    use UrlValidation;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var CookieJar
     */
    protected $cookie;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;
    
    /**
     * LaraCastsFetcher constructor.
     *
     * @param Client $client
     * @param Resolver $resolver
     * @param Filesystem $filesystem
     */
    public function __construct(Client $client, Resolver $resolver, Filesystem $filesystem)
    {
        $this->client     = $client;
        $this->resolver   = $resolver;
        $this->filesystem = $filesystem;
        $this->cookie     = new CookieJar();
    }
    
    /**
     * @return bool
     * @throws FailedLoginExceptions
     */
    public function login()
    {
        // get the content of the login page
        $response = $this->client->get('https://laracasts.com/login', [
            'cookies' => $this->cookie,
            'verify'  => false,
        ]);

        $token = $this->resolver->getToken($response->getBody()->getContents());

        $response = $this->client->post('https://laracasts.com/sessions', [
            'cookies'     => $this->cookie,
            'form_params' => [
                'email'    => getenv('EMAIL'),
                'password' => getenv('PASSWORD'),
                '_token'   => $token,
                'remember' => 1,
            ],
            'verify'      => false,
        ]);

        if (strpos($response->getBody()->getContents(), 'flash(\'Welcome back! You are now logged in.\');') !== false) {
            return true;
        }
        
        throw new FailedLoginExceptions("Failed to login");
    }
    
    /**
     * Get the current page lessons
     *
     * @param $url
     *
     * @return array
     * @throws FailedLoginExceptions
     * @throws InvalidUrlExceptions
     * @throws NotLaraCastsUrlExceptions
     */
    public function getPageTitleAndLessonsAndUrls($url)
    {
        $this->validateUrl($url);
        $this->login();

        $response = $this->client->get($url, ['cookies' => $this->cookie]);
        $html     = $response->getBody()->getContents();
        
        $courseTitle  = $this->resolver->getCourseTitle($html);
        $lessonTitles = $this->resolver->getLessonsTitle($html);
        $lessonLinks  = $this->resolver->getLessonsLinks($html);

        return [$courseTitle, $lessonTitles, $lessonLinks];
    }

    /**
     * @param $getArgument
     *
     * @return array
     */
    public function getPageLinks($getArgument)
    {
        list($courseHeader, $lessonTitle, $lessonUrls) = $this->getPageTitleAndLessonsAndUrls($getArgument);

        foreach ($lessonUrls as $uri) {
            $urls[] = "https://laracasts.com" . $uri;
        }

        return $urls;
    }

    /**
     * Get page content html
     *
     * @param $url
     *
     * @return string
     */
    public function getPageContent($url)
    {
        // get the content of the login page
        $response = $this->client->get($url, [
            'cookies' => $this->cookie,
            'verify'  => false,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $html
     *
     * @return string
     * @throws NoDownloadLinkException
     */
    public function getDownloadLinkFromPage($html)
    {
        # https://github.com/iamfreee/laracasts-downloader/blob/master/App/Html/Parser.php#L81
        preg_match('(\'\/downloads\/.+\')', $html, $matches);

        if (isset($matches[0]) === false) {
            throw new NoDownloadLinkException();
        }

        return 'https://laracasts.com' . substr($matches[0], 1, -1);
    }
    
    /**
     * @param $fromUrl
     * @param $output
     *
     * @return bool
     */
    public function downloadLink($fromUrl, $output, $lessonTitle)
    {
        $finalUrl = $this->getRedirectUrl($this->getRedirectUrl($fromUrl));
        $saveTo = getenv("DOWNLOAD") . DIRECTORY_SEPARATOR . $lessonTitle . '.mp4';
        $this->saveRemoteFile($output, $finalUrl, $saveTo);
    }

    /**
     * @param $fromUrl
     *
     * @return string
     */
    private function getRedirectUrl($fromUrl)
    {
        $response = $this->client->get($fromUrl, [
            'cookies'         => $this->cookie,
            'allow_redirects' => false,
            'verify'          => false,
        ]);

        $url = $response->getHeader('Location');

        return implode('', $url);
    }

    /**
     * @param $output
     * @param $finalUrl
     * @param $saveTo
     */
    private function saveRemoteFile($output, $finalUrl, $saveTo)
    {
        $this->client->get($finalUrl, [
            'sink'     => $saveTo,
            'verify'   => false,
            'progress' => function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($output) {
                $progress = new ProgressBar($output, $downloadTotal);
                $progress->start();
                $i = 0;
                while ($i+$downloadedBytes < $downloadTotal) {
                    $progress->advance();
                }
                $progress->finish();
            },
        ]);
    }
}
