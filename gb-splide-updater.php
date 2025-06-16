<?php

if (!defined('ABSPATH')) {
    exit;
}

class GBSplide_Updater
{
    private $plugin_slug;
    private $plugin_file;
    private $update_json_url;

    public function __construct($plugin_file, $update_json_url)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->update_json_url = $update_json_url;

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    public function check_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $plugin_data = get_plugin_data($this->plugin_file);
        $current_version = $plugin_data['Version'];

        $response = wp_remote_get($this->update_json_url);
        if (is_wp_error($response)) {
            return $transient;
        }

        $data = json_decode(wp_remote_retrieve_body($response));
        if (
            !empty($data->version) &&
            version_compare($current_version, $data->version, '<')
        ) {
            $transient->response[$this->plugin_slug] = (object)[
                'slug' => $this->plugin_slug,
                'plugin' => $this->plugin_slug,
                'new_version' => $data->version,
                'url' => $data->homepage ?? '',
                'package' => $data->download_url ?? '',
            ];
        }

        return $transient;
    }

    public function plugin_info($false, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== dirname($this->plugin_slug)) {
            return false;
        }

        $response = wp_remote_get($this->update_json_url);
        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response));

        return (object)[
            'name' => $data->name ?? 'Custom Plugin',
            'slug' => $this->plugin_slug,
            'version' => $data->version ?? '',
            'author' => $data->author ?? '',
            'homepage' => $data->homepage ?? '',
            'short_description' => $data->description ?? '',
            'sections' => [
                'description' => $data->description ?? '',
            ],
            'download_link' => $data->download_url ?? '',
            'requires' => $data->requires ?? '5.0',
            'tested' => $data->tested ?? '6.8',
        ];
    }
}
