<?php

namespace Oaattia\Downloader;

use Symfony\Component\DomCrawler\Crawler;

class Resolver
{
    
    /**
     * Fetch the token from the html
     *
     * @param $html
     *
     * @return null|string
     */
    public function getToken($html)
    {
        return $this->crawler($html)->filter('input[name=_token]')->attr('value');
    }

    /**
     * Get the lesson title from the page
     *
     * @param $html
     *
     * @return Crawler
     */
    public function getLessonsTitle($html)
    {
        $titles = $this->crawler($html)->filter('span.Lesson-List__title')->each(function (Crawler $node) use (&$array) {
            return trim($node->children()->text());
        });

        return $titles;
    }

    /**
     * Get the lessons links
     *
     * @param $html
     * @return array
     */
    public function getLessonsLinks($html)
    {
        $links = $this->crawler($html)->filter('span.Lesson-List__title')->each(function (Crawler $node) use (&$array) {
            return $node->children()->attr('href');
        });

        return $links;
    }


    
    /**
     * Get course title
     *
     * @param $html
     *
     * @return mixed
     */
    public function getCourseTitle($html)
    {
        $title = $this->crawler($html)->filter('.Banner__heading')->text();

        return trim($title);
    }


    public function getPageUrls($urls)
    {
        $urls = $this->crawler($html)->filter('Lesson-List__title a')->each(function (Crawler $node) use (&$array) {
            return trim($node->children()->href());
        });

        return $urls;
    }

    private function getPageContent()
    {
        $urls = $this->crawler($html)->filter('Lesson-List__title a')->each(function (Crawler $node) use (&$array) {
            return trim($node->children()->href());
        });

        return $urls;
    }
    /**
     *
     * Get the Crawler instance
     *
     * @param $html
     *
     * @return Crawler
     */
    private function crawler($html)
    {
        return new Crawler($html);
    }
}
