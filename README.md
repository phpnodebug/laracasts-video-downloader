## Install

Via Composer

``` bash
$ composer require oaattia/laracasts-downloader
```

## Usage

⋅⋅⋅ Got the idea from this great [package](https://github.com/iamfreee/laracasts-downloader)

- I created this package so it can help me to download videos from Laracasts website, there is no option to download the full lesson for specific series in the website, so i want some kind of tool to help me to download it from the website

- Create `.env` file by copying `.env.example` and editing the file, you will need to add the laracasts username and password so you can login to the website

### To download the lessons for the series use the following command

``` bash
php console laracasts:download https://laracasts.com/series/series-name
```

### To view the lessons in the current page ( series page ), use the following command

``` bash
php console laracasts:view https://laracasts.com/series/series-name
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

If you have PR you want to submit, please do :)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
