<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class GitHub_Plugin_Updater
{
    private $plugin_slug;
    private $plugin_file;
    private $github_user;
    private $github_repo;
    private $access_token;

    public function __construct($plugin_file, $github_user, $github_repo, $access_token = '')
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->github_user = $github_user;
        $this->github_repo = $github_repo;
        $this->access_token = $access_token;

        add_filter("pre_set_site_transient_update_plugins", [$this, "check_update"]);
        add_filter("plugins_api", [$this, "plugin_info"], 10, 3);
        add_filter("http_request_args", [$this, "add_github_auth_to_zip_download"], 10, 2);
    }

    public function add_github_auth_to_zip_download($args, $url)
    {
        if (
            strpos($url, 'github.com') !== false &&
            strpos($url, $this->github_user) !== false &&
            strpos($url, $this->github_repo) !== false
        ) {
            if (!isset($args['headers'])) {
                $args['headers'] = [];
            }

            $args['headers']['Authorization'] = 'token ' . $this->access_token;
            $args['headers']['User-Agent'] = 'WordPress/' . get_bloginfo('version');
            $args['headers']['Accept'] = 'application/vnd.github+json';
        }

        return $args;
    }

    public function get_repo_api_url($endpoint = '')
    {
        return "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/$endpoint";
    }

    public function check_update($transient)
    {
        if (empty($transient->checked))
            return $transient;

        $plugin_data = get_plugin_data($this->plugin_file);
        $current_version = $plugin_data['Version'];

        $response = wp_remote_get($this->get_repo_api_url('releases/latest'), [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version'),
                'Authorization' => 'token ' . $this->access_token,
                'Accept' => 'application/vnd.github+json',
            ]
        ]);

        if (is_wp_error($response))
            return $transient;

        $release = json_decode(wp_remote_retrieve_body($response));

        if (
            !empty($release->tag_name)
            && version_compare($current_version, $release->tag_name, '<')
            && !empty($release->assets[0]->browser_download_url)
        ) {
            $transient->response[$this->plugin_slug] = (object) [
                'slug' => $this->plugin_slug,
                'plugin' => $this->plugin_slug,
                'new_version' => $release->tag_name,
                'url' => $release->html_url,
                'package' => !empty($release->assets[0]->browser_download_url)
                    ? $release->assets[0]->browser_download_url
                    : '',
            ];
        }

        return $transient;
    }

    public function plugin_info($false, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== dirname($this->plugin_slug)) {
            return false;
        }

        $release_response = wp_remote_get($this->get_repo_api_url('releases/latest'), [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version'),
                'Authorization' => 'token ' . $this->access_token,
                'Accept' => 'application/vnd.github+json',
            ]
        ]);

        if (is_wp_error($release_response))
            return false;

        $release = json_decode(wp_remote_retrieve_body($release_response));
        $version = $release->tag_name ?? '0.0.0';
        $download_link = $release->assets[0]->browser_download_url ?? '';

        $repo_response = wp_remote_get($this->get_repo_api_url(), [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version'),
                'Authorization' => 'token ' . $this->access_token,
                'Accept' => 'application/vnd.github+json',
            ]
        ]);

        if (is_wp_error($repo_response))
            return false;

        $repo = json_decode(wp_remote_retrieve_body($repo_response));

        return (object) [
            'name' => $repo->name,
            'slug' => $this->plugin_slug,
            'version' => $version,
            'author' => '<a href="' . esc_url($repo->owner->html_url) . '">' . esc_html($repo->owner->login) . '</a>',
            'homepage' => $repo->homepage ?? $repo->html_url,
            'short_description' => $repo->description ?? '',
            'sections' => [
                'description' => $repo->description ?? '',
            ],
            'download_link' => $download_link,
            'requires' => '5.0',
            'tested' => '6.5',
        ];
    }
}