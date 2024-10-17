<?php

use Axeptio\Plugin\Models\Settings;
use Faker\Factory as Faker;

// Mock WordPress functions
function esc_js($text) { return $text; }
function esc_attr($text) { return $text; }
function esc_html($text) { return $text; }
function esc_html__($text, $domain) { return $text; }
function esc_html_e($text, $domain) { echo $text; }
    // Mock the get_sdk_settings function
    function get_sdk_settings() {
        return [
            'triggerGTMEvents' => 'true',
        ];
    }


beforeEach(function () {
    $this->faker = Faker::create();
});

it('returns the correct GTM events setting', function ($expected_value) {
    // Mock the Settings class
    $settings_mock = \Mockery::mock('alias:Axeptio\Plugin\Models\Settings');
    $settings_mock->shouldReceive('get_option')
        ->with('gtm_events', 'true')
        ->andReturn($expected_value);

    $actual_value = Settings::get_option('gtm_events', 'true');

    expect($actual_value)->toBe($expected_value);
})->with(['true', 'false', 'update_only']);


it('correctly renders GTM events select field', function ($expected_value) {
    // Mock the Settings class
    $settings_mock = \Mockery::mock('alias:Axeptio\Plugin\Models\Settings');
    $settings_mock->shouldReceive('get_option')
        ->with('gtm_events', 'true')
        ->andReturn($expected_value);

    // Simulate $data variable
    $data = (object) [
        'value' => $expected_value,
        'id' => 'xpwp_gtm_events',
        'label' => 'GTM Events',
        'description' => 'Configure GTM events',
        'group' => 'axeptio_settings',
        'name' => 'gtm_events'
    ];

    // Capture the output of the template
    ob_start();
    include __DIR__ . '/../templates/admin/common/fields/gtm-events.php';
    $output = ob_get_clean();

    expect($output)
        ->toContain('id="xpwp_gtm_events"')
        ->toContain('name="axeptio_settings[gtm_events]"')
        ->toContain('value="' . $expected_value . '"')
        ->toContain('Send all events to dataLayer')
        ->toContain('Do not send any events to dataLayer')
        ->toContain('Send only axeptio_update event to dataLayer');
})->with(['true', 'false', 'update_only']);
