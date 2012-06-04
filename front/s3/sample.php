<?php
# Copyright 2007 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); # you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.

/**
 * sample.php demonstrates how to upload a single file asynchronously to Amazon S3
 * using the Yahoo! User Interface (YUI).
 */
 
// Access identifiers are found in config.php
require 'config.php';
require 'PostPolicy.php';

/*
    YUI! creates a hidden IFRAME to POST your file asynchronously.  Because the 
    body of the IFRAME is from the amazonaws.com domain, it will not readable 
    by the parent window of the client due to cross-domain restrictions.  A 
    work-around for this is to include a success_action_redirect variable in 
    your HTML form that points to a file on your server.  Response data is 
    passed to this file using Query string variables. Your application can 
    handle these parameters in any way that you need, but one of the goals of 
    this file should be to notify the client browser that the upload was 
    successful.  To accomplish this, echo a JSON string with the returned 
    variables.  See callback.php for more information.
*/
$success_action_redirect = 'http://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER["REQUEST_URI"]) . '/callback.php';

// Create a new POST policy document
$s3policy = new Aws_S3_PostPolicy(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $bucket, 86400);
$s3policy->addCondition('', 'acl', 'public-read')
         ->addCondition('', 'bucket', $bucket)
         ->addCondition('starts-with', '$key', '')
         ->addCondition('starts-with', '$Content-Type', '')
         ->addCondition('', 'success_action_redirect', $success_action_redirect);
         
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Language" content="en-us" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Asynchronous File Uploads to Amazon S3</title>
        <meta name="title" content="Asynchronous File Uploads to Amazon S3" />
        <meta name="description" content="Amazon Simple Storage Service Aysnchronous Ajax File Upload Using the Yahoo User Interface" />
        <link href="styles.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/yahoo-dom-event/yahoo-dom-event.js"></script>
        <script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/connection/connection-min.js"></script>
        <script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/json/json-min.js"></script>
        <script type="text/javascript">

            // When the DOM is ready to use, launch this function
            YAHOO.util.Event.onDOMReady(function(e) {                
                YAHOO.util.Event.on('submitForm', 'click', function(e) {
                                        
                    if (!YAHOO.util.Dom.get('file').value) {
                        alert('* Please select a file to upload.');
                        return;
                    }
                    
                    YAHOO.util.Dom.get('responseImg').setAttribute('src', 'ajax-loader.gif');                    
                    YAHOO.util.Dom.setStyle('responseContainer', 'display', 'block');
                    
                    var s3formEle = YAHOO.util.Dom.get('s3form');
                    var s3url = s3formEle.getAttribute('action');

                    // Add the appropriate Content-Type header to the Upload
                    var showImage = false, contentType = "binary/octet-stream", fileToUpload = YAHOO.util.Dom.get('file').value;
                    extension = fileToUpload.substring(fileToUpload.lastIndexOf("."), fileToUpload.length).toLowerCase();
                    if (extension) {
                        switch (extension) {
                            case '.bmp': contentType = "image/bmp"; showImage = true; break;
                            case '.gif': contentType = "image/gif"; showImage = true; break;
                            case '.jpg': case '.jpeg': contentType = "image/jpeg"; showImage = true; break;
                            case '.png': contentType = "image/png"; showImage = true; break;
                            case '.avi': contentType = "video/x-msvideo"; break;
                            case '.doc': case '.docx': contentType = "application/vnd.ms-word"; break;
                            case '.html': case '.html': contentType = "text/html"; break;
                            case '.ico': contentType = "image/x-icon"; break;
                            case '.mov': contentType = "video/quicktime"; break;
                            case '.mp3': contentType = "audio/mpeg"; break;
                            case '.pdf': contentType = "application/pdf"; break;
                            case '.ppt': case '.pptx': contentType = "application/vnd.ms-powerpoint"; break;
                            case '.rtf': contentType = "application/rtf"; break;
                            case '.txt': contentType = "plain/text"; break;
                            case '.wav': contentType = "audio/x-wav"; break;
                            case '.zip': contentType = "application/zip"; break;
                        }
                        YAHOO.util.Dom.get('contentType').value = contentType;
                    }

                    // Attach s3form to the Connect Manager
                    YAHOO.util.Connect.setForm(s3formEle, true);

                    // Make the Connect request
                    var transaction = YAHOO.util.Connect.asyncRequest('POST', s3url, {
                        
                        upload: function(o) {
                            if (!o.responseText) {                                
                                alert("The upload failed.");                                
                                YAHOO.util.Dom.setStyle('responseContainer', 'display', 'none');
                                return;
                            }
                            
                            // The upload was successfull, now parse the JSON data.  Let's use the YUI JSON
                            // utility so that we can make sure the response if valid a JSON string
                            try {
                                var response = YAHOO.lang.JSON.parse(o.responseText);
                            } catch (e) {
                                alert("Invalid JSON string returned from the success_action_redirect URL:\n" + o.responseText);
                            }
                            
                            // Fill in our response elements with the JSON response variables
                            var fileSource = s3url + response.key;
                            // Link to the object url
                            YAHOO.util.Dom.get('urlEle').setAttribute('href', fileSource);

                            if (showImage) {
                                // Show the uploaded image
                                YAHOO.util.Dom.setStyle('responseImg', 'display', 'block');                                
                                YAHOO.util.Dom.get('objectUrl').innerHTML = "";                                
                                YAHOO.util.Dom.get('responseImg').src = fileSource;
                            } else {
                                // We aren't uploading an image, so hide the responseImage
                                YAHOO.util.Dom.setStyle('responseImg', 'display', 'none');
                                YAHOO.util.Dom.get('objectUrl').innerHTML = fileSource;
                            }
                            YAHOO.util.Dom.get('callbackEle').innerHTML = response.callbackUrl;
                            YAHOO.util.Dom.get('bucketEle').innerHTML = response.bucket;
                            YAHOO.util.Dom.get('keyEle').innerHTML = response.key;
                            YAHOO.util.Dom.get('etagEle').innerHTML = response.etag;
                        },

                        // The attempt to upload failed
                        failure: function(o) {
                            alert('Failed to send POST: ' + o.statusText);
                        }
                    });
                });
            });
        </script>
    </head>
    <body>
        <p><a href="readme.html">View readme.html</a> | <a href="sample.php" class="selected">View sample.php</a> | <a href="sample-attachments.php">View sample-attachments.php</a></p>
        <h1>Asynchronous Uploads to Amazon S3</h1>
        <p><em>sample.php</em></p>
        <?php if ((AWS_ACCESS_KEY_ID == 'YOUR_AWS_ACCESS_KEY_ID' || AWS_SECRET_ACCESS_KEY == 'YOUR_AWS_SECRET_ACCESS_KEY') || (!$bucket || $bucket == 'YOUR_BUCKET')) :?>
            <div class="error">* Edit config.php and add in your Access Key ID, Secret Access Key, and Bucket.</div>
            <?php exit; ?>
        <?php endif; ?>
        <p>This sample demonstrates how to asynchronously upload a POST form to Amazon S3.  The success_action_redirect form field
        has been set so that upon completion of the Amazon S3 POST upload, Amazon S3 will redirect back to callback.php with
        meta-data about the uploaded object in the query-string. The Yahoo! User Interface (YUI) is used to facilitate the asynchronous
        upload of the form.  YUI will create an off-screen IFRAME to upload the file, and the redirection will take place within the IFRAME.</p>
        <h2>Upload a File to Amazon S3</h2>
        <fieldset class="formContainer" id="uploadContainer">
            <legend>File to Upload</legend>
            <div class="bd">
                <form action="<?php echo 'http://' . $bucket . '.s3.amazonaws.com/'; ?>" id="s3form" enctype="multipart/form-data" method="post">
                    <p>
                        <input type="hidden" name="AWSAccessKeyId" id="AWSAccessKeyId" value="<?php echo $s3policy->getAwsAccessKeyId(); ?>" />
                        <input type="hidden" name="acl" id="acl" value="<?php echo $s3policy->getCondition('acl'); ?>" />
                        <input type="hidden" name="key" id="key" value="${filename}" />
                        <input type="hidden" name="policy" value="<?php echo $s3policy->getPolicy(true); ?>" />
                        <input type="hidden" name="Content-Type" id="contentType" value="" />
                        <input type="hidden" name="signature" id="signature" value="<?php echo $s3policy->getSignedPolicy(); ?>" />
                        <input type="hidden" name="success_action_redirect" value="<?php echo $s3policy->getCondition('success_action_redirect'); ?>" />
                        <label for="file">File</label>&nbsp;
                        <input type="file" name="file" id="file" />
                        <input type="button" id="submitForm" value="Submit"/>
                    </p>
                </form>
            </div>
            <div class="ft">
                <h3>Policy</h3>
                <pre><?php echo $s3policy->getPolicy(); ?></pre>
            </div>
        </fieldset>
        <fieldset class="formContainer" id="responseContainer">
            <legend>Your Uploaded Object</legend>
            <div class="bd">
                <table cellpadding="5" cellspacing="3">
                    <thead>
                        <tr>
                            <td>Bucket</td>
                            <td>Key</td>
                            <td>Etag</td>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <strong>Callback url</strong><br />
                                <span id="callbackEle">Loading...</span>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr>
                            <td id="bucketEle">Loading...</td>
                            <td id="keyEle">Loading...</td>
                            <td id="etagEle">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="ft">
                <a href="#" id="urlEle" target="_blank">
                    <img id="responseImg" alt="Uploaded image" src="ajax-loader.gif" />
                    <span id="objectUrl"></span>
                </a>
            </div>
        </fieldset>
    </body>
</html>