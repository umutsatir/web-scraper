<?php
    class Scraper {
        public $title_xpath;
        public $article_xpath;

        public function get_links($url) {
            $xml = file_get_contents($url);
            preg_match_all('/<loc.*?>.*?<\/loc.*?>/i', $xml, $matches);
            $all_matches = array_map(function($match) {
                return strip_tags($match);
            }, $matches[0]);
            return $all_matches;
        }

        public function filter_links($links, $filters) {
            $filtered_links = [];
            foreach ($links as $link) {
                $filtered = true;
                foreach ($filters as $filter) {
                    if (strpos($link, $filter) !== false) {
                        $filtered = false;
                        break;
                    }
                }
                if ($filtered) {
                    array_push($filtered_links, $link);
                }
            }
            return $filtered_links;
        }

        public function get_page($url) {
            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.5',
                    ]
                ]
            ];
            $context = stream_context_create($options);
            $page = file_get_contents($url, false, $context);
            if ($page === FALSE) {
                return "Error: " . $http_response_header[0];
            }
            return $page;
        }

        public function get_text($page) {
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($page);
            $xpath = new DOMXPath($doc);
            $title = $xpath->query($this->title_xpath);
            $article = $xpath->query($this->article_xpath);
            $plain_text = '';
            foreach($article as $node) {
                if ($node->nodeValue[0] != '.')
                    $plain_text .= strip_tags($node->nodeValue) . ' ';
            }
            $plain_text = preg_replace("/\r\n|\r|\n/", "", $plain_text);
            $plain_text = preg_replace("/\r\t|\r|\t/", "", $plain_text);
            $plain_text = preg_replace('/[\'"]/', '', $plain_text); // remove quotes

            if ($title->length == 0 || $plain_text == "")
                throw new Exception("Title or text not found");

            return [$title[0]->nodeValue, $plain_text];
        }
    }
?>