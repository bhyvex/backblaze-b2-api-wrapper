<?php

    /**
     * Backblaze B2 API Wrapper.
     *
     * @author Dan Rovito
     * @copyright DanRovito.com
     *
     * @version dev-master
     */
    class b2_api
    {
        //Account Authorization
        public function b2_authorize_account($acct_id, $app_key)
        {
            $this->account_id = $acct_id;
            $application_key = $app_key;
            $credentials = base64_encode($this->account_id.':'.$application_key);
            $url = 'https://api.backblaze.com/b2api/v1/b2_authorize_account';

            $session = curl_init($url);

            // Add headers
            $headers = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Basic '.$credentials;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  // Add headers

            curl_setopt($session, CURLOPT_HTTPGET, true);  // HTTP GET
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true); // Receive server response

            $http_result = curl_exec($session); //results
            $error = curl_error($session); //Error return
            $http_code = curl_getinfo($session, CURLINFO_HTTP_CODE); //Result type: 200, 404, 500, etc.

            curl_close($session);

            $json = json_decode($http_result);
            $this->apiUrl = $json->apiUrl;
            $this->authToken = $json->authorizationToken;
            $this->downloadUrl = $json->downloadUrl;

            //Print result code if it doesn't equal 200
            if ($http_code != 200) {
                return print $http_code;
            } else {
                //Return results
                return $http_result;
            }
        }

        //Create Bucket
        public function b2_create_bucket($api_bucket_name, $bucket_type)
        {
            $account_id = $this->account_id; // Obtained from your B2 account page
            $api_url = $this->apiUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $bucket_name = $api_bucket_name; // 6 char min, 50 char max: letters, digits, - and _
            $bucket_type = $bucket_type; // Either allPublic or allPrivate

            $session = curl_init($api_url.'/b2api/v1/b2_create_bucket');

            // Add post fields
            $data = ['accountId' => $account_id, 'bucketName' => $bucket_name, 'bucketType' => $bucket_type];
            $post_fields = json_encode($data);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response

            $http_result = curl_exec($session); //results

            curl_close($session); // Clean up

            return json_encode($http_result); // show response
        }

        //Delete Bucket
        public function b2_delete_bucket($api_bucket_id)
        {
            $account_id = $this->account_id; // Obtained from your B2 account page
            $api_url = $this->apiUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $bucket_id = $api_bucket_id;  // The ID of the bucket you want to delete

            $session = curl_init($api_url.'/b2api/v1/b2_delete_bucket');

            // Add post fields
            $data = ['accountId' => $account_id, 'bucketId' => $bucket_id];
            $post_fields = json_encode($data);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response

            $http_result = curl_exec($session); //results

            curl_close($session); // Clean up

            return json_encode($http_result); // show response
        }

        //Delete file version
        public function b2_delete_file_version($api_file_id, $api_file_name)
        {
            $api_url = $this->apiUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $file_id = $api_file_id;  // The ID of the file you want to delete
            $file_name = $api_file_name; // The file name of the file you want to delete

            $session = curl_init($api_url.'/b2api/v1/b2_delete_file_version');

            // Add post fields
            $data = ['fileId' => $file_id, 'fileName' => $file_name];
            $post_fields = json_encode($data);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response

            $http_result = curl_exec($session); //results

            curl_close($session); // Clean up

            return json_encode($http_result); // show response
        }

        //Download file by ID
        public function b2_download_file_by_id($fileID)
        {
            $download_url = $this->downloadUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $file_id = $fileID; // The ID of the file you want to download
            $uri = $download_url.'/b2api/v1/b2_download_file_by_id?fileId='.$file_id;

            $session = curl_init($uri);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_HTTPGET, true); // HTTP GET
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
            $http_result = curl_exec($session); // results
            curl_close($session); // Clean up

            return $http_result; // show response
        }

        //Download file by Name
        public function b2_download_file_by_name($bucketName, $fileName)
        {
            $auth_token = $this->authToken; // From b2_authorize_account call
            $uri = $this->downloadUrl.'/file/'.$bucketName.'/'.$fileName;

            $session = curl_init($uri);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_HTTPGET, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
            $http_result = curl_exec($session); // results
            curl_close($session); // Clean up
            return $http_result; // show response
        }

        //Get File Info
        public function b2_get_file_info($api_file_id)
        {
            $api_url = $this->apiUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $file_id = $api_file_id; // The id of the file
            $session = curl_init($api_url.'/b2api/v1/b2_get_file_info');

            // Add post fields
            $data = ['fileId' => $file_id];
            $post_fields = json_encode($data);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response

            $http_result = curl_exec($session); //results

            curl_close($session); // Clean up

            return json_encode($http_result); // return response
        }

        //Get upload URL
        public function b2_get_upload_url($bucketID)
        {
            $api_url = $this->apiUrl; // From b2_authorize_account call
            $auth_token = $this->authToken; // From b2_authorize_account call
            $bucket_id = $bucketID;  // The ID of the bucket you want to upload to

            $session = curl_init($api_url.'/b2api/v1/b2_get_upload_url');

            // Add post fields
            $data = ['bucketId' => $bucket_id];
            $post_fields = json_encode($data);
            curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

            // Add headers
            $headers = [];
            $headers[] = 'Authorization: '.$auth_token;
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true); // HTTP POST
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
            $server_output = curl_exec($session); // results
            curl_close($session); // Clean up
            return $http_result; // return response
        }

        //Hide File
        public function b2_hide_file()
        {
        }

        //List buckets
        public function b2_list_buckets()
        {
        }

        //List file names
        public function b2_list_file_names()
        {
        }

        //List file versions
        public function b2_list_file_versions()
        {
        }

        //List parts
        public function b2_list_parts()
        {
        }

        //List unfinished large files
        public function b2_list_unfinished_large_files()
        {
        }

        //Start large file
        public function b2_start_large_file()
        {
        }

        //Finish Large file
        public function b2_finish_large_file()
        {
        }

        //Get Upload Part URL
        public function b2_get_upload_part_url()
        {
        }

        //update bucket
        public function b2_update_bucket()
        {
        }

        //upload file
        public function b2_upload_file()
        {
        }

        //Upload part
        public function b2_upload_part()
        {
        }
    }
