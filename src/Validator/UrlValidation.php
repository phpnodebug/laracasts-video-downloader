<?php

namespace Oaattia\Downloader\Validator;

use Oaattia\Downloader\Exceptions\NotLaraCastsUrlExceptions;

trait UrlValidation
{
    /**
     * Check the current url if it's valid laracasts series page url,
     * so we can download the lessons
     *
     * @param $url
     *
     * @return $this
     * @throws InvalidUrlExceptions
     * @throws NotLaraCastsUrlExceptions
     */
    public function validateUrl($url)
    {
        // check if the passed url is string
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlExceptions("Invalid url!!");
        }

        // check if it's laracasts website
        if (! preg_match_all('/https:\/\/laracasts\.com\/series\/(.*)\/$/', $url)
        ) {
            throw new NotLaraCastsUrlExceptions("Not valid series page url!!");
        }
        
        return true;
    }
}
