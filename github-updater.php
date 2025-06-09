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
    }

    public function get_repo_api_url($endpoint = '')
    {
        $url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/$endpoint";
        return $url;
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
                'Authorization' => 'token ' . $this->access_token
            ]
        ]);

        if (is_wp_error($response))
            return $transient;

        $release = json_decode(wp_remote_retrieve_body($response));

        if (!empty($release->tag_name) && is_string($release->tag_name)) {
            if (version_compare($current_version, $release->tag_name, '<')) {
                $transient->response[$this->plugin_slug] = (object) [
                    'slug' => $this->plugin_slug,
                    'plugin' => $this->plugin_slug,
                    'new_version' => $release->tag_name,
                    'url' => $release->html_url,
                    'package' => $release->zipball_url,
                ];
            }
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
            ]
        ]);

        if (is_wp_error($release_response)) {
            return false;
        }

        $release = json_decode(wp_remote_retrieve_body($release_response));
        $version = $release->tag_name ?? '0.0.0';

        $repo_response = wp_remote_get($this->get_repo_api_url(), [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version'),
                'Authorization' => 'token ' . $this->access_token,
            ]
        ]);

        if (is_wp_error($repo_response)) {
            return false;
        }

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
            'download_link' => $release->zipball_url ?? '',
            'requires' => '5.0',
            'tested' => '6.5',
        ];
    }
}