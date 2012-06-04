<?php
# Copyright 2007 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); # you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.

/**
 * sample-attachments.php demonstrates how to asynchronously upload attachments to a form
 * using the Yahoo! User Interface (YUI)
 */

// Access identifiers are found in config.php
require 'config.php';
require 'PostPolicy.php';

/*
    YUI! creates a hidden IFRAME to POST your file asynchronously.  Because the body of the IFRAME is
    from the amazonaws.com domain, it will not readable by the parent window of the client due to cross-domain
    restrictions.  A work-around for this is to include a success_action_redirect variable in your HTML form
    that points to a file on your server.  Response data is passed to this file using Query string variables. Your
    application can handle these parameters in any way that you need, but one of the goals of this file should be
    to notify the client browser that the upload was successful.  To accomplish this, echo a JSON string
    with the returned variables.  See callback.php for more information.
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

            // Create an object to contain all of the functions
            var s3Post = {

                // Return a Content-Type header based on the provided file extension
                getContentType : function(extension) {
                    var contentType = "";
                    switch (extension) {
                        case ".bmp": contentType = "image/bmp"; break;
                        case ".gif": contentType = "image/gif"; break;
                        case ".jpg": case ".jpeg": contentType = "image/jpeg"; break;
                        case ".png": contentType = "image/png"; break;
                        case ".avi": contentType = "video/x-msvideo"; break;
                        case ".doc": case ".docx": contentType = "application/vnd.ms-word"; break;
                        case ".html": case ".html": contentType = "text/html"; break;
                        case ".ico": contentType = "image/x-icon"; break;
                        case ".mov": contentType = "video/quicktime"; break;
                        case ".mp3": contentType = "audio/mpeg"; break;
                        case ".pdf": contentType = "application/pdf"; break;
                        case ".ppt": case ".pptx": contentType = "application/vnd.ms-powerpoint"; break;
                        case ".rtf": contentType = "application/rtf"; break;
                        case ".txt": contentType = "plain/text"; break;
                        case ".wav": contentType = "audio/x-wav"; break;
                        case ".zip": contentType = "application/zip"; break;
                    }
                    return contentType;
                },

                // Add a new attachment form, fired when the "+ Add more" button is clicked.
                addAttachment : function() {
                    // Get the element that we need to clone
                    var cloneEle = YAHOO.util.Dom.get("duplicate").cloneNode(true);
                    cloneEle.setAttribute("id", "");
                    var fileEle = YAHOO.util.Dom.getElementsBy(function(ele) { return (ele.getAttribute("name") == "file") ? true : false; }, "input", cloneEle)[0];
                    fileEle.value = "";
                    YAHOO.util.Dom.setStyle(fileEle, "display", "inline");
                    var spanEle = cloneEle.getElementsByTagName("span")[0];
                    spanEle.innerHTML = "";
                    // Append the new attachment form (cloneEle) to the attachmentContainer
                    YAHOO.util.Dom.get("attachmentContainer").appendChild(cloneEle);
                },

                // Start the asynchronous upload of a form
                uploadAttachment : function(s3formEle) {
                    // Get the span element of this upload form
                    var spanEle = s3formEle.getElementsByTagName("span")[0];
                    // Get the file element of this upload form
                    var fileEle = YAHOO.util.Dom.getElementsBy(function(ele) { return (ele.getAttribute("name") == "file") ? true : false; }, "input", s3formEle)[0];
                    var fileName = fileEle.value;
                    // Get the Content-Type element of this upload form
                    var contentTypeEle = YAHOO.util.Dom.getElementsBy(function(ele) { return (ele.getAttribute("name") == "Content-Type") ? true : false; }, "input", s3formEle)[0];
                    var s3url = s3formEle.getAttribute("action");
                    if (!fileName) {
                        alert("* Please select a file to upload.");
                        return;
                    }

                    // Add the appropriate Content-Type header to the upload
                    var extension = fileName.substring(fileName.lastIndexOf("."), fileName.length).toLowerCase();
                    contentType = s3Post.getContentType(extension);
                    contentTypeEle.value = contentType;

                    // Attach s3form to the YUI Connect Manager
                    YAHOO.util.Connect.setForm(s3formEle, true);
                    
                    // Make the Connect request
                    var transaction = YAHOO.util.Connect.asyncRequest("POST", s3url, {
                        upload: function(o) {
                            if (!o.responseText) {
                                // An error occurred while POSTing the file
                                alert("The upload failed.");
                            } else {
                                // The upload was successfull, now parse the JSON data.  Let's use the YUI JSON
                                // utility so that we can make sure the response if valid a JSON string
                                try {
                                    var response = YAHOO.lang.JSON.parse(o.responseText);
                                } catch (e) {
                                    alert("Invalid JSON string returned from the success_action_redirect URL:\n" + o.responseText);
                                }
                                var fileSource = s3url + response.key;
                                var inputEle = document.createElement("input");
                                inputEle.setAttribute("value", fileSource);
                                inputEle.setAttribute("name", "attachments[]");
                                inputEle.setAttribute("type", "hidden");
                                YAHOO.util.Dom.get("mainForm").appendChild(inputEle);
                                YAHOO.util.Dom.setStyle(fileEle, "display", "none");
                                spanEle.innerHTML = '<a href="' + fileSource + '" target="_blank">' + fileSource + '</a>';
                            }
                        },
                        // The attempt to upload failed
                        failure: function(o) {
                            alert("Failed to send POST: " + o.statusText);
                        }
                    });
                },

                // Submit the main form
                submitForm : function() {
                    // You should do a check to make sure all of the uploads have completed before submitting the main form.
                    // For the purpose of this sample, we'll just assume all of the uploads have completed.
                    var allDone = true;
                    if (allDone) document.mainForm.submit();                    
                }
            };

        </script>
    </head>
    <body>
        <p><a href="readme.html">View readme.html</a> | <a href="sample.php">View sample.php</a> | <a href="sample-attachments.php" class="selected">View sample-attachments.php</a></p>
        <h1>Asynchronous Uploads to Amazon S3</h1>
        <p><em>sample-attachments.php</em></p>
        
        <?php if ((AWS_ACCESS_KEY_ID == 'YOUR_AWS_ACCESS_KEY_ID' || AWS_SECRET_ACCESS_KEY == 'YOUR_AWS_SECRET_ACCESS_KEY') || (!$bucket || $bucket == 'YOUR_BUCKET')) :?>
            <div class="error">* Edit config.php and add in your Access Key ID, Secret Access Key, and Bucket.</div>
            <?php exit; ?>
        <?php endif; ?>
        
        <p>This sample demonstrates how to asynchronously upload multiple POST forms to Amazon S3 that
        are to be used in conjunction with another form.  This use-case demonstrates how to add attachments to a
        forum-like message. The Yahoo! User Interface (YUI) is used to facilitate the asynchronous
        upload of the form.  YUI will create an off-screen IFRAME to upload the file and receive the
        success_action_redirect from Amazon S3.</p>

        <div id="attachmentForm">
            <h2>Send a Message</h2>
            <fieldset>
                <legend>Message</legend>
                <form name="mainForm" id="mainForm" action="submit.php" method="post">
                    <p>
                        <label for="subject">Subject:</label><br />
                        <input type="text" id="subject" name="subject" size="30" />
                    </p>
                    <p>
                        <label for="message">Message:</label><br />
                        <textarea rows="4" cols="35" id="message" name="message"></textarea>
                    </p>
                </form>
            </fieldset>
            <fieldset>
                <legend>Attach Files</legend>
                <div id="attachmentContainer">
                    <div id="duplicate">
                        <form action="<?php echo 'http://' . $bucket . '.s3.amazonaws.com/'; ?>" enctype="multipart/form-data" method="post">
                            <input type="hidden" name="AWSAccessKeyId" value="<?php echo $s3policy->getAwsAccessKeyId(); ?>" />
                            <input type="hidden" name="acl" value="<?php echo $s3policy->getCondition('acl'); ?>" />
                            <input type="hidden" name="key" value="${filename}" />
                            <input type="hidden" name="policy" value="<?php echo $s3policy->getPolicy(true); ?>" />
                            <input type="hidden" name="Content-Type" value="" />
                            <input type="hidden" name="signature" value="<?php echo $s3policy->getSignedPolicy(); ?>" />
                            <input type="hidden" name="success_action_redirect" value="<?php echo $s3policy->getCondition('success_action_redirect'); ?>" />
                            <input type="file" name="file" onchange="s3Post.uploadAttachment(this.parentNode)" />
                            <span></span>
                        </form>
                    </div>
                </div>
                <p><a href="#" id="addMoreAttachments" onclick="s3Post.addAttachment(); return false;">Attach another file</a></p>
            </fieldset>
            <div id="submitContainer">
                <input type="button" value="Send Message" onclick="s3Post.submitForm()" id="submitButton" />
            </div>
        </div>
    </body>
</html>