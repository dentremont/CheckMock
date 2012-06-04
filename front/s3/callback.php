<?php
# Copyright 2009 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); # you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.
    
/**
 * After a successful upload, Amazon S3 will redirect to the success_action_redirect field 
 * of the HTML POST form and send the uploaded object's bucket, key, and etag values in the 
 * query string. This file is used to take those variables and echo a JSON string in the 
 * BODY element of the YUI generated IFRAME so the client can become aware of the successful 
 * upload.
 *
 * The source of the IFRAME must be hosted on the same domain as the parent window, or you might 
 * run into cross-domain security restrictions.	 	 
 */

$bucket = html_entity_decode($_GET['bucket']);
$key = html_entity_decode($_GET['key']);
$etag = str_replace('"', '', html_entity_decode($_GET['etag']));	
$callbackUrl = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

// Echo a JSON string into the YUI generated IFRAME so that the client browser is aware of the upload

echo '{"bucket": "' . $bucket . '", "key": "' . $key . '", "etag": "' . $etag . '", "callbackUrl": "' . $callbackUrl . '"}';