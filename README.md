# Jamf Pro Upload Page

Josh Harvey | Nov 2018

josh@macjeezy.com

Table of Contents
=================
- [Overview](#overview)
- [Features](#features)
	- [Packages](#packages)
	- [Profiles](#profiles)
	- [Scripts](#scripts)
- [Install/Setup](#setup)
- [Troubleshooting](#troubleshooting)

## Overview

This page was created to allow jamf admins the access they need to upload packages and scripts to the JSS, without having to give them full access to Jamf Admin.  

Besides being able to upload new scripts, packages and profiles, your admins will also have the ability to download any script that is being used in your JSS *(With the option to add a filter if needed)*, make any edits to that script, then reupload it to the JSS. This allows for any changes to be made on the most up to date version.

**[Jamf Pro Upload Page Example](http://macjeezy.com/JamfProUploads/)**


## Features
*(and things I am really proud of)*
* **Up to date Site Lists** *(Site Selection Dropdowns)*
    - Updated each time the page is reloaded. Uses an API call to create an xml file with all the sites *(getsites.php)*. Then the html page uses jquery to parse the xml *(SiteList.xml)* and loads them as options in the dropdown menu.
* **Up to date Script List** *(Script Selection Dropdown - Edit Existing Script Option)*
    - Updated each time the page is reloaded. Uses an API call to create an xml file with all of the scripts in the JSS *(getscripts.php)*. Then the html page uses jquery to parse the xml *(ScriptList.xml)* and loads them as options in the dropdown menu. You also have the ability to filter the list of scripts based off its naming prefix. This can keep your "Full Jamf Pro" scripts from being downloaded and edited, and only allow sites to edit their own scripts.
* **Configuration Profile Uploads** *(Unsigned Profiles Only currently)
    - This section is a workaround for a open PI with jamf and allows for your admins to upload *unsigned* configuration profiles to their site.


![Jamf Pro Upload Form Home Page](/images/example.png)
    
**Example of the main page**



### Packages
**Options** | **Info**
------------ | -------------
**File Size Limit** | 6GB
**Allowed File Types** | .pkg, .mpkg, .dmg

**Uploading a Package**

Select the site you manage via the dropdown menu. Once a site is selected, add the package you want to upload then select the "Upload" button. Once the package has been uploaded successfully, an API call will be made to update the JSS and a javascript message will appear informing you of the successful upload and that it is ready to use. 

Your new package will appear in the JSS under the Uploads category and use the following naming convention
        
        sitename - packagename.pkg

### Profiles
**Options** | **Info**
------------ | -------------
**File Size Limit** | N/A
**Allowed File Types** | .mobileconfig

**Uploading a Profile**

Select the site you manage via the dropdown menu. Once a site is selected, add the profile you want to upload then select the "Upload" button. Once the profile has been uploaded successfully, an API call will be made to update the JSS and you will be provided with two links. One to open the profile in a new tab and the other to go back to the Upload Form Home. 

Your new profile will appear in the JSS under the Uploads category and use the following naming convention
        
        sitename - name.of.profile

### Scripts
**Options** | **Info**
------------ | ------------- 
**File Size Limit** | 2MB
**Allowed File Types** | .sh, .py

**Creating a new script**

   Select "Create New Script" from the Scripts Dropdown. Once the page has loaded, select the site you manage from the dropdown menu and add the script you are uploading to the JSS. When ready, select the "Upload" button and the script will be uploaded to the DP and an API call will then be ran to create the script in the database. Once the script has been successfully created, a javascript message will appear informing you that the script is ready for use.

   Your new script will appear in the JSS under the Uploads category and use the following naming convention
   
    sitename - scriptname
    
**Editing an exiting script**

   Select "Edit Exitising Script" from the Script Dropdown. Once the page has loaded, you will see a dropdown menu of all of the scripts that currently exist in the JSS. Select the script you want to edit/replace. Once you have selected the script, you have the option to download it. This allows for you to make changes on the most current and updated version of that script. After you have completed editing your script, double check the dropdown menu still has the name of the script you are editing selected. If so, use the "Choose File" button to add your script and select the "Upload" button. Once the script has been successfully uploaded, a javascript message will appear informing you that the script is ready for use.

Your edited script will appear in the JSS under the Uploads category and use the previous name



### Setup

* **IIS Only**
    * For packages larger then 30MB, you will need to change the Request Limits inside of IIS to a value higher then the largest package you think will be used. The default setting is 30MB and the value is read as Bytes *(eg: 3000000000 Bytes = 3 Gigabytes)* [Request Limits Documentation](https://docs.microsoft.com/en-us/iis/configuration/system.webserver/security/requestfiltering/requestlimits/)

* **Requirements**
    * Webserver with PHP installed
    	* [Install PHP on IIS](https://docs.microsoft.com/en-us/iis/application-frameworks/scenario-build-a-php-website-on-iis/configuring-step-1-install-iis-and-php)
		* [Install PHP on CentOS](https://www.tecmint.com/install-php-7-in-centos-7/)
    * API Access to your Jamf Pro Instance
    
    
**Setup Steps**
1. Copy the files to your webserver into the same directory. If you choose to use different directories *(eg: /PHP/getscripts.php, /images/tuxlogo.png, etc..)* be sure to update the source paths for each of the files that have been moved.
    * **PHP Pages** */JamfProUploadPage/PHP_Pages/*
        * createscript.php
        * getscript.php
        * getsites.php
        * pkgupload.php
	* profileupload.php
        * scriptdownload.php
        * scriptupload.php
     * **HTML Pages** */JamfProUploadPage/HTML_Pages/*
        * index.html
        * home.html
        * newscript.html
        * pkgupload.html
	* profileupload.html
        * scriptupload.html
     * **Images** */JamfProUploadPage/images/*
        * tuxlogo.png *(You can use any icon, just be sure to change the reference in the index.html)*
     * **CSS** */JamfProUploadPage/*
        * uploadstylesheet.css
2. Create the following directories on your webserver *(Note: Be sure to change the Packages path in pkguploads.php to the path of the DP you use with Jamf Pro)*
    * Packages *(Used for testing the upload process)*
    * Scripts
    * Profiles
3. Modify the following files and information to match your Jamf Pro setup *(Note: You may also want to change the text of the error messages and contact email that gets displayed also. The fields mentioned below are only the required changes needed to make the scripts function)*
    * **createscript.php**
    
      `$uploadDirectory = "/Scripts/";`
      
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('POST', 'https://jamfurl/JSSResource/scripts/id/0', $updateXML);`
    * **profileupload.php**
    
      `$uploadDirectory = "/Profile/";`
      
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('POST', 'https://jamfurl/JSSResource/osxconfigurationprofiles/id/0', $updateXML);`
      
      `echo "<a href='https://your.jamf.pro/OSXConfigurationProfiles.html?o=r&id=" . $matches[1] . "' target='_blank'>Click Here To Edit The Profile in a new tab</a>";`
    * **getscript.php**
    
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('GET', 'https://jamfurl/JSSResource/scripts', false);`
    * **getsites.php**
    
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('GET', 'https://jamfurl/JSSResource/sites', false);`
    * **pkgupload.php**
    
      `$uploadDirectory = "/Path/To/Packages/";`
      
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('POST', 'https://jamfurl/JSSResource/packages/id/0', $updateXML);`
    * **scriptdownload.php**
    
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('GET', 'https://jamfurl/JSSResource/scripts/name/' . $siteURL , false);`
    * **scriptupload.php**
    
      `$uploadDirectory = "/Scripts/";`
      
      `curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");`
      
      `$get_data = callAPI('PUT', 'https://jamfurl/JSSResource/scripts/name/' . $siteURL , $updateXML);`    
    * **index.html**
       * Customize the title, header and footer to display your information
    * **scriptupload.html**
       * Lines 25 - 28
       *Uncomment these lines and change the 'keyword' to match a naming prefix you use with scripts to filter and "restrict" them from being editing/replaced.*
4. Modify the php.ini file to allow file uploads by changing the following values to fit your environment
    * **post_max_size** [PHP Manual](http://php.net/post-max-size)
    * **upload_max_filesize** [PHP Manual](http://php.net/upload-max-filesize)
    * **memory_limit** [PHP Manual](http://php.net/memory-limit)
5. Once the above files have been copied over and modified to fit your environment, your upload page should be up and running. Just use the FQDN of your webserver and add the directory you saved the files in (eg: https://yourwebserver.com/JamfUploads/) 

### Troubleshooting
* **Dropdown menus stuck at "loading"**
	* This is the default text used for the dropdown menus. If you see this, verify the SiteList.xml or ScriptList.xml exist in the same directory as the page is in. If missing, verify the settings in the php page creating the xml file are correct.

* **404 Error Page with larger uploads**
	* This error usually occurs when the Request Limit header is set to a smaller value then the package you are trying to upload. To verify this is the cause of the issue, look in your webserver logs and find the substatus of the error. If it is 404.13, changing the Request Limit header will resolve this issue.
	
* **Configuration Profile gets created without a payload**
	* This will occur if the Configuration Profile was uploaded as a SIGNED profile. Verify the Configuration Profile is UNSIGNED and try the upload again. This will also occur if you are uploading a PPPC profile to a jamf instance older then 10.9.
