amazon-s3-bonfire
=================

Bonfire module for uploading images to Amazon S3. Works on Bonfire v0.6.


### Features

 * Users can upload images to S3.
 * Users can view/delete their own uploads from S3.
 * Information about uploaded images stored in db table and retrieved from db table to minimize S3 requests.
 * File handling using Codeigniter's File Uploading Class.


### Installation


 * Add all files to their appropriate folders in your Bonfire application.

 * Open the config.inc.php file located in `bonfire/codeigniter/libraries/awsphp/` and enter your Amazon S3 credentials.

 * Add a context named `aws` to the contexts array in the `bonfire/application/config/application.php` file. Note: the context will appear when you're logged in as a user.

 * Log into your Bonfire application as developer. Go to Developer > Database migrations > Modules. Select 002_Install_s3.php for Amazonupload and click migrate module. This will install the db table for storing upload data.

 * Test the module by logging in as a user and going to yoursite/index.php/admin/aws/amazonupload. The module is made for users only.


### That's all! 

Suggestions or improvements are welcomed!

There are a few things that need to be added/improved:

* showing upload errors in the template rather then echoing them

* temporarily storing uploaded files on the server by deleting them after upload to S3. Uploading files to your server's tmp folder would be ideal because it stores files temporarily and automatically deletes them when your function finishes executing.





