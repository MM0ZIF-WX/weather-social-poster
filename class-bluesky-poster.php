<?php
if (!defined('ABSPATH')) {
    exit;
}

class BlueskyPoster {
    private $username;
    private $password;
    private $api_base = 'https://bsky.social/xrpc';

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function postContent($text, $image = '', $link = '') {
        $session = $this->createSession();
        if (!$session || !isset($session['accessJwt'])) {
            throw new Exception('Failed to create session');
        }

        $post = [
            '$type' => 'app.bsky.feed.post',
            'text' => $text,
            'createdAt' => gmdate('c'),
        ];

        if (!empty($link)) {
            $post['embed'] = [
                '$type' => 'app.bsky.embed.external',
                'external' => [
                    'uri' => $link,
                    'title' => 'Live Weather Data',
                    'description' => 'Latest weather updates from MM0ZIF_WX'
                ]
            ];
        }

        return $this->apiRequest('com.atproto.repo.createRecord', [
            'repo' => $session['did'],
            'collection' => 'app.bsky.feed.post',
            'record' => $post
        ], $session['accessJwt']);
    }

    public function getPostInteractions($post_uri, $limit) {
        $session = $this->createSession();
        if (!$session || !isset($session['accessJwt'])) {
            throw new Exception('Failed to create session for interactions');
        }

        $response = $this->apiRequest('app.bsky.feed.getPostThread', [
            'uri' => $post_uri,
            'depth' => 1
        ], $session['accessJwt']);

        $interactions = [];
        if (isset($response['thread']['replies']) && is_array($response['thread']['replies'])) {
            foreach ($response['thread']['replies'] as $reply) {
                if (isset($reply['post'])) {
                    $interactions[] = [
                        'author' => $reply['post']['author']['displayName'] ?? $reply['post']['author']['handle'],
                        'text' => $reply['post']['record']['text'],
                        'time' => $reply['post']['record']['createdAt'],
                        'likes' => $reply['post']['likeCount'] ?? 0,
                        'reposts' => $reply['post']['repostCount'] ?? 0
                    ];
                }
            }
        }

        return array_slice($interactions, 0, $limit);
    }

    private function createSession() {
        $response = wp_remote_post($this->api_base . '/com.atproto.server.createSession', [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'identifier' => $this->username,
                'password' => $this->password
            ]),
            'timeout' => 30,
            'sslverify' => true
        ]);

        if (is_wp_error($response)) {
            throw new Exception('HTTP Error: ' . $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($response_code !== 200) {
            throw new Exception('Session creation failed: ' . ($body['error'] ?? "HTTP $response_code"));
        }

        return $body;
    }

    private function apiRequest($method, $data, $jwt) {
        $response = wp_remote_post($this->api_base . '/' . $method, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt
            ],
            'body' => json_encode($data),
            'timeout' => 30,
            'sslverify' => true
        ]);

        if (is_wp_error($response)) {
            throw new Exception('HTTP Error: ' . $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($response_code !== 200) {
            throw new Exception('API Error: ' . ($body['error'] ?? "HTTP $response_code"));
        }

        return $body;
    }
}
?>