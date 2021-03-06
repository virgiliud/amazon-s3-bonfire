amazon-s3-bonfire
=================

Bonfire module for uploading images to Amazon S3. Works on Bonfire v0.6.


### Module Features

 * Users can upload images to Amazon S3.
 * Users can view/delete their images.
 * Uploads to S3 are recorded in a database table to minimize API requests.
 * File handling uses Codeigniter's File Uploading Class.


### Installation


 * Add all files to their appropriate folders in your Bonfire application

 * Open the `config.inc.php` file located in `bonfire/codeigniter/libraries/awsphp/` and enter your Amazon S3 credentials

 * Open the `aws.php` file located in `bonfire/modules/amazonupload/controllers/` and enter the name of your S3 bucket in the `$bucket` variables

 * Add a context named `aws` to the contexts array in the `bonfire/application/config/application.php` file. Note: the context will appear when you're logged in as a user

 * Log into your Bonfire application as Developer. In Database Migrations > Modules, select `002_Install_s3.php` for the `Amazonupload` module and click migrate module. This will install the db table for storing data about images uploaded to S3

 * Test the module by logging in as a user and going to `yoursite/index.php/admin/aws/amazonupload`. The module is made to be used by users only


### That's all! 

Contributers are welcomed!

There are a few things that need to be added/improved:

* showing upload errors in the template rather then echoing them

* creating thumbnails for each uploaded image 

* temporarily storing uploaded files on the server by deleting them after upload to S3. Uploading files to your server's tmp folder would be ideal because it stores files temporarily and automatically deletes them when your function finishes executing.


### License 

Copyright (c) 2013 Virgiliu D.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


