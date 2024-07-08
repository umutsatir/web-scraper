<?php
    class GPTDetector {
        public $url = 'https://api.zerogpt.com/api/detect/detectText';

        public function get_percentage($paragraph) {
            if (empty($paragraph)) {
                throw new Exception("Empty paragraph");
            }
            $data = ['input_text' => $paragraph];
            $data = json_encode($data);
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => [
                        'Content-Type: application/json',
                        'Accept: application/json, text/plain, */*',
                        'Origin: https://www.zerogpt.com',
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                        'Accept-Language: en-US,en;q=0.5',
                    ],
                    'content' => $data
                ]
            ];
            $context = stream_context_create($options);
            $result = @file_get_contents($this->url, false, $context);
            if ($result === false) {
                throw new Exception("Error: Could not connect to the API");
            }
            $result = json_decode($result, true);
            if ($result['success'] == false) {
                throw new Exception($result['message']);
            }
            if ($result['data']['additional_feedback'] == '') {
                return $result['data']['fakePercentage'];
            } else {
                throw new Exception($result['data']['additional_feedback'] . " - " . $result['data']['textWords'] . " words");
            }
        }
    }
?>