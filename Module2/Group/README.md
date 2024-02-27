# CSE330
Name: Fiona & Rohan


# Link
### Rohan
http://18.191.24.174/~RohanSong/module2/login.php
### Fiona
http://ec2-3-82-231-44.compute-1.amazonaws.com/~Fiona/module2-group-module2-510576-505908/login.php

##### We support the user name as follows: Rohan, Fiona, Test, Song.
![Image text](./static/Login.png)


# Requirements
### File Sharing Site
##### File Management (25 Points):
- [x] Users should not be able to see any files until they enter a username and log in (4 points)
- [x] Users can see a list of all files they have uploaded (4 points)
- [x] Users can open files they have previously uploaded (5 points)
- [x] Users can upload files (4 points)
- [x] Users can delete files. If a file is "deleted", it should actually be removed from the filesystem (4 points)
- [x] The directory structure is hidden. Users should not be able to access or view files by manipulating a URL. (2 points)
- [x] Users can log out (2 points)

##### Best Practices (10 Points):
- [x] Code is well formatted and easy to read, with proper commenting (4 points)
- [x] The site follows the FIEO philosophy (3 points)
- [x] All pages pass the W3C validator (3 points)

##### Usability (5 Points):
- [x] Site is intuitive to use and navigate (4 points)
- [x] Site is visually appealing (1 point)

### Creative Portion
- [x] Support transferring files to other users</br>
When inputting the username, then we can not only support uploading to the local file but also support uploading to the specified user.
- [x] Display other information about the file</br>
When you get to your homepage, you can see the file list there. Also, you will see the size and type of the file. 
- [x] Verify the filenames whether conform to the naming standards</br>
We will verify the filenames in order to make sure it follows the naming standards. For example, once you upload the file named "Lecture 1", this will not be allowed in our file management system.
- [x] Zip the file</br>
We support the user to zip the file in our file system.
- [x] Folder space monitor and upload file size limit check</br>
We set a folder limit to 20Mb per user, if the uploaded file exceed 20Mb, we will deny the upload request. In addition, PHP will restrict us to a maximum 2 MB upload file size.
