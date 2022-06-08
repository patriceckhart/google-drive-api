# Google Drive Api

Google Drive Api client

## Installation

The Google Drive Api client is listed on packagist (https://packagist.org/packages/patriceckhart/google-drive-api) - therefore you don't have to include the package in your "repositories" entry any more.

Just run:

```
composer require patriceckhart/google-drive-api
```

## Usage
```php
<?php

use PatricEckhart\GoogleDriveClient;

class GoogleDrive
{

    /**
     * List files
     *
     * @return array
     */
    public function listFiles():array
    {
        $client = new GoogleDriveClient(
            '/data/neos/Data/Security/google/client_secret_768913446618-aax0u2k92nh350lij57ts58a0igm1a82.apps.googleusercontent.com.json', // clientIdPathAndFileName
            'https://redirecturi.com', // redirectUri
            '2//13pMQO5SH5KXaCgYIARAAAERSNwF-L9IrXHD7F1SLRD0r2xJ8PicU!2dCtoZxmdb6dGCzl5UQVCFArqstTaIoUuTW7MjnEAzyPIg', // refreshToken
            'https://www.googleapis.com/auth/drive', // scope
            '1K__pKH52TK3ZxeWSR8O-XOgYad31_xtt', // startingPoint (Folder ID)
            20 // pageSize
        );
        return $client->list();
    }

}
```
### Response

The response returns the full folder and file structure starting from the starting point.

```
string "id" => string
string "name" => string
string "mimeType" => string
string "kind" => string
string "parent" => string
string "webViewLink" => string
string "webContentLink" => string
string "publicFile"  => string
```

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
